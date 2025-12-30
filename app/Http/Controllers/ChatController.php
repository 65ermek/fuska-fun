<?php

namespace App\Http\Controllers;

use App\Mail\JobMessageToOwner;
use App\Models\ChatRoom;
use App\Models\Customer;
use App\Models\Job;
use App\Models\Message;
use App\Services\ChatRoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ChatController extends Controller
{
    // === ОСНОВНОЙ МЕТОД ОТПРАВКИ СООБЩЕНИЯ ===
    public function send(Request $request)
    {
        $validated = $request->validate([
            'job_id' => 'required|exists:jobs,id',
            'email' => 'required|email|max:100',
            'texto' => 'required|string|max:2000',
            'name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'author_email' => 'nullable|email|max:100',
            'author_name' => 'nullable|string|max:100',
            'create_chat_only' => 'nullable|boolean',
        ]);

        $job = Job::findOrFail($validated['job_id']);

        $authorEmail = $validated['author_email'] ?? $job->email;
        $authorName  = $validated['author_name'] ?? $job->contact_name ?? null;

        // --- Customer создание/обновление ---
        $customer = Customer::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'source' => 'candidat',
                'persistent_token' => hash('sha256', Str::random(40)),
            ]
        );

        $customer->update([
            'last_seen_at' => now(),
            'name' => $validated['name'] ?? $customer->name,
            'phone' => $validated['phone'] ?? $customer->phone,
            'source' => 'candidat'
        ]);

        // --- ChatRoom через сервис ---
        $chatRoom = ChatRoomService::findOrCreate(
            $job,
            $validated['email'],
            $validated['name'] ?? null,
            $authorEmail,
            $authorName
        );

        // 🔥 СОХРАНЯЕМ candidate_token В СЕССИЮ
        session([
            'customer_id' => $customer->id,
            'customer_email' => $customer->email,
            'customer_name' => $customer->name ?? 'Kandidát',
            'customer_source' => 'candidat',
            'candidate_token' => $chatRoom->candidate_token, // 🔥 ДОБАВЛЕНО
            'user_email' => $customer->email, // 🔥 ДЛЯ СОВМЕСТИМОСТИ
            'user_name' => $customer->name ?? 'Kandidát', // 🔥 ДЛЯ СОВМЕСТИМОСТИ
        ]);

        // --- Создаём сообщение (если не create_chat_only) ---
        $messageId = null;
        if (!($validated['create_chat_only'] ?? false) && !empty($validated['texto'])) {
            $message = Message::create([
                'chat_room_id' => $chatRoom->id,
                'sender_email' => $validated['email'],
                'sender_name' => $validated['name'] ?? 'Kandidát',
                'message' => $validated['texto'],
                'is_read' => false
            ]);
            $messageId = $message->id;

            // --- Отправка письма ---
            $chatLink = route('chat.author.show', [
                'token' => $job->edit_token,
                'job_id' => $job->id
            ]);

            Mail::to($job->email)->send(
                new JobMessageToOwner(
                    $validated['email'],
                    $validated['texto'],
                    $job,
                    $chatLink,
                    $validated['name'] ?? 'Kandidát',
                    $validated['phone'] ?? null
                )
            );
        }

        // 🔥 ПОДДЕРЖКА ОБОИХ ФОРМАТОВ ОТВЕТА
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'ok',
                'chat_room_id' => $chatRoom->id,
                'message_id' => $messageId,
                'redirect_url' => route('chat.room', $chatRoom->id) // 🔥 ДЛЯ AJAX РЕДИРЕКТА
            ])->cookie(cookie(
                'fuska_customer_token',
                $customer->persistent_token,
                60 * 24 * 365
            ));
        } else {
            // 🔥 WEB РЕДИРЕКТ (как в startChat)
            return redirect()->route('chat.room', $chatRoom->id)
                ->with('success', 'Чат успешно создан!')
                ->cookie(cookie(
                    'fuska_customer_token',
                    $customer->persistent_token,
                    60 * 24 * 365
                ));
        }
    }
    // === WEB МЕТОДЫ ===

    /**
     * Чат для автора объявления - автологин по ссылке из письма
     */
    public function authorChat($token, $job_id)
    {
        $job = Job::where('id', $job_id)
            ->where('edit_token', $token)
            ->first();

        if (!$job) {
            return redirect('/')->with('error', 'Odkaz je neplatný nebo vypršel.');
        }

        // Очищаем сессию и устанавливаем данные автора
        session()->invalidate();
        session()->regenerate();

        session([
            'author_token' => $token,
            'user_email' => $job->email,
            'user_name' => $job->contact_name,
            'author_job_id' => $job_id,
            'author_logged_in' => true,
            // Чистим customer данные
            'customer_id' => null,
            'customer_email' => null,
            'customer_name' => null,
            'customer_phone' => null,
            'customer_source' => null
        ]);

        // Поиск последнего чата автора
        $latestChat = ChatRoom::where('job_id', $job_id)
            ->where('author_token', $token)
            ->latest()
            ->first();

        return redirect()->route('jobs.show', $job->slug)->with([
            'author_chat_token' => $token,
            'author_job_id' => $job_id,
            'author_email' => $job->email,
            'author_name' => $job->contact_name,
            'auto_open_chat' => true,
            'auto_open_chat_id' => $latestChat ? $latestChat->id : null,
            'success' => 'Byli jste úspěšně přihlášeni jako autor inzerátu.'
        ]);
    }

    public function getConversations(Request $request)
    {
        $email = $request->input('email');

        if (!$email) {
            return response()->json(['error' => 'Email is required'], 400);
        }

        // Загружаем чаты
        $chats = ChatRoom::where('author_email', $email)
            ->orWhere('candidate_email', $email)
            ->orderByDesc('updated_at')
            ->get();

        $conversations = $chats->map(function ($chat) use ($email) {

            // Определяем кем является пользователь: автор или кандидат
            $isAuthor = $chat->author_email === $email;

            $contactName  = $isAuthor ? $chat->candidate_name  : $chat->author_name;
            $contactEmail = $isAuthor ? $chat->candidate_email : $chat->author_email;

            // Загружаем последнее сообщение
            $lastMessageObj = $chat->messages()->latest()->first();

            // -----------------------------------------
            // 🟢 БЛОК ONLINE-СТАТУСОВ
            // -----------------------------------------
            $status = \App\Models\ChatOnlineStatus::where('email', $contactEmail)->first();

            $isOnline = false;
            $lastSeen = null;

            if ($status) {
                $lastSeen = $status->last_seen;

                // Онлайн — если был активен последние 120 сек
                if ($status->is_online && now()->diffInSeconds($status->last_seen) < 120) {
                    $isOnline = true;
                }
            }

            return [
                'chat_room_id'    => $chat->id,
                'contactName'     => $contactName,
                'contactEmail'    => $contactEmail,
                'jobTitle'        => $chat->job_title,
                'lastMessage'     => $lastMessageObj->message ?? null,
                'lastMessageTime' => $lastMessageObj->created_at ?? null,
                'is_author_chat'  => $isAuthor,

                // 🆕 Добавлено:
                'online'          => $isOnline,
                'last_seen'       => $lastSeen,
            ];
        });

        return response()->json($conversations);
    }
    /**
     * 🔥 ПОЛУЧЕНИЕ ИМЕНИ АВТОРА ИЗ CUSTOMERS
     */
    public function getMessages(Request $request)
    {
        try {
            $chatRoomId = $request->get('chat_room_id');
            $userEmail = $request->get('email');

            Log::info("Getting messages for chat room", [
                'chat_room_id' => $chatRoomId,
                'user_email' => $userEmail
            ]);

            $messages = Message::where('chat_room_id', $chatRoomId)
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function($message) use ($userEmail) {
                    return [
                        'id' => $message->id,
                        'message' => $message->message,
                        'sender_name' => $message->sender_name,
                        'sender_email' => $message->sender_email,
                        'is_author' => $message->sender_email === $userEmail,
                        'created_at' => $message->created_at,
                        'is_read' => $message->is_read
                    ];
                });

            // 🔥 ВАЖНО: Возвращаем ПРОСТО МАССИВ (как ожидает JS в первом условии)
            return response()->json($messages); // Без обертки!

        } catch (\Exception $e) {
            Log::error('Error in getMessages: ' . $e->getMessage());
            return response()->json([]); // Пустой массив при ошибке
        }
    }

    public function sendMessage(Request $request)
    {
        try {
            $data = $request->validate([
                'chat_room_id' => 'required|exists:chat_rooms,id',
                'sender_email' => 'required|email',
                'message' => 'required|string|max:2000'
            ]);

            $senderName = $this->getSenderNameFromCustomers($data['sender_email']);

            $message = Message::create([
                'chat_room_id' => $data['chat_room_id'],
                'sender_email' => $data['sender_email'],
                'sender_name' => $senderName,
                'message' => trim($data['message']),
                'is_read' => false
            ]);

            return response()->json([
                'success' => true,
                'message_id' => $message->id
            ]);

        } catch (\Exception $e) {
            \Log::error('Error sending message: ' . $e->getMessage());
            return response()->json(['error' => 'Send failed'], 500);
        }
    }
    /**
     * 🔥 ПОЛУЧЕНИЕ ИМЕНИ ИЗ ТАБЛИЦЫ CUSTOMERS
     */
    private function getSenderNameFromCustomers($email)
    {
        $customer = \App\Models\Customer::where('email', $email)->first();

        if ($customer && $customer->name) {
            $name = $customer->name;

            // 🔥 ЧИСТИМ ИМЯ ОТ ДУБЛИРОВАНИЯ EMAIL
            if (strpos($name, $email) !== false) {
                $name = str_replace($email, '', $name);
            }

            // 🔥 ЧИСТИМ ОТ ЛЮБЫХ EMAIL
            $emailRegex = '/\s*[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}\s*/';
            $name = preg_replace($emailRegex, '', $name);

            $name = trim($name);

            if (!empty($name)) {
                return $name;
            }
        }

        // 🔥 FALLBACK: если customer не найден или имя пустое
        return $this->getFallbackName($email);
    }
    // Авторские операции
    private function getFallbackName($email)
    {
        // 1. Проверяем автора через chat_rooms
        $chatRoom = \App\Models\ChatRoom::where('author_email', $email)
            ->with(['job'])
            ->first();

        if ($chatRoom && $chatRoom->job && $chatRoom->job->contact_name) {
            return $chatRoom->job->contact_name;
        }

        // 2. Берем часть email до @
        $nameFromEmail = explode('@', $email)[0];
        return ucfirst($nameFromEmail);
    }

    public function checkNewMessages(Request $request)
    {
        try {
            $chatRoomId = $request->query('chat_room_id');
            $userEmail = $request->query('email');
            $lastId = $request->query('last_id', 0);

            $newMessages = Message::where('chat_room_id', $chatRoomId)
                ->where('id', '>', $lastId)
                ->where('sender_email', '!=', $userEmail)
                ->orderBy('created_at', 'asc')
                ->get();

            // 🔥 ОСТАВЛЯЕМ ЛОГ ТОЛЬКО ЕСЛИ ЕСТЬ НОВЫЕ СООБЩЕНИЯ
            if ($newMessages->count() > 0) {
                Log::info("🆕 {$newMessages->count()} new messages in chat {$chatRoomId} for {$userEmail}");

                Message::where('chat_room_id', $chatRoomId)
                    ->where('sender_email', '!=', $userEmail)
                    ->where('is_read', false)
                    ->update(['is_read' => true]);
            }

            return response()->json($newMessages);

        } catch (\Exception $e) {
            Log::error('Error checking new messages: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function getMyConversations(Request $request)
    {
        try {
            Log::info('getMyConversations called', [
                'user_email' => $request->get('user_email'),
                'session_email' => session('customer_email')
            ]);

            $userEmail = $request->get('user_email') ?: session('customer_email');

            if (!$userEmail) {
                Log::warning('No user email provided');
                return response()->json([]);
            }

            $testConversations = [
                [
                    'chat_room_id' => 'test_room_1',
                    'partner_name' => 'Test User',
                    'job_title' => 'Test Job',
                    'last_message' => 'Test message',
                    'last_message_time' => now()->toISOString(),
                    'unread_count' => 1
                ]
            ];

            Log::info('Returning test conversations', [
                'count' => count($testConversations),
                'user_email' => $userEmail
            ]);

            return response()->json($testConversations);

        } catch (\Exception $e) {
            Log::error('Error in getMyConversations: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    // === АВТОРСКИЕ МЕТОДЫ ===

    public function checkAuthorAuth(Request $request)
    {
        try {
            $authorToken = session('author_token');
            $userEmail = session('user_email');
            $authorJobId = session('author_job_id');
            $isAuthorLoggedIn = session('author_logged_in', false);

            // Проверяем наличие сессии автора
            if (!$authorToken || !$userEmail || !$isAuthorLoggedIn) {
                return response()->json([
                    'authorized' => false,
                    'message' => 'No author session found'
                ]);
            }

            // Проверяем что email и токен соответствуют job
            $job = Job::where('email', $userEmail)
                ->where('edit_token', $authorToken)
                ->when($authorJobId, function($query) use ($authorJobId) {
                    return $query->where('id', $authorJobId);
                })
                ->first();

            $authorized = !!$job;

            return response()->json([
                'authorized' => $authorized,
                'author' => $job ? [
                    'email' => $job->email,
                    'name' => $job->contact_name,
                    'job_id' => $job->id,
                    'job_title' => $job->title,
                ] : null
            ]);

        } catch (\Exception $e) {
            Log::error('Error in checkAuthorAuth: ' . $e->getMessage());
            return response()->json(['authorized' => false], 500);
        }
    }

    public function getAuthorConversations(Request $request)
    {
        try {
            $authorEmail = $request->query('author_email');
            $authorToken = $request->query('author_token');

            if (!$authorEmail || !$authorToken) {
                return response()->json(['error' => 'Author credentials required'], 400);
            }

            // Проверяем авторизацию автора
            $jobs = Job::where('email', $authorEmail)
                ->where('edit_token', $authorToken)
                ->get();

            if ($jobs->isEmpty()) {
                return response()->json(['error' => 'Author access denied'], 403);
            }

            // Получаем чаты автора
            $chatRooms = ChatRoom::whereIn('job_id', $jobs->pluck('id'))
                ->where('author_token', $authorToken)
                ->with(['job', 'lastMessage'])
                ->get()
                ->map(function ($room) use ($authorEmail) {
                    return [
                        'chat_room_id' => $room->id,
                        'partner_name' => $room->candidate_name,
                        'partner_email' => $room->candidate_email,
                        'job_title' => $room->job->title,
                        'job_id' => $room->job_id,
                        'last_message' => $room->lastMessage->message ?? 'Začněte konverzaci...',
                        'last_message_time' => $room->lastMessage->created_at ?? $room->created_at,
                        'unread_count' => $room->messages()
                            ->where('sender_email', '!=', $authorEmail)
                            ->where('is_read', false)
                            ->count()
                    ];
                });

            return response()->json($chatRooms);

        } catch (\Exception $e) {
            Log::error('Error getting author conversations: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function createAuthorChat(Request $request)
    {
        try {
            $validated = $request->validate([
                'job_id' => 'required|integer',
                'author_email' => 'required|email',
                'author_name' => 'required|string',
                'candidate_email' => 'required|email',
                'candidate_name' => 'required|string',
                'author_token' => 'required|string'
            ]);

            Log::info('Creating author chat', $validated);

            // 🔥 ПРОВЕРЯЕМ ЧЕРЕЗ Job таблицу
            $job = Job::where('id', $validated['job_id'])
                ->where('email', $validated['author_email'])
                ->where('edit_token', $validated['author_token'])
                ->first();

            if (!$job) {
                Log::warning('Author access denied for job', [
                    'job_id' => $validated['job_id'],
                    'author_email' => $validated['author_email']
                ]);
                return response()->json([
                    'success' => false,
                    'error' => 'Author access denied'
                ], 403);
            }

            // 🔥 СОЗДАЕМ ЧАТ С author_token
            $chatRoom = ChatRoom::firstOrCreate([
                'job_id' => $validated['job_id'],
                'candidate_email' => $validated['candidate_email']
            ], [
                'candidate_name' => $validated['candidate_name'],
                'author_token' => $validated['author_token'], // 🔥 СОХРАНЯЕМ author_token
                'status' => 'active'
            ]);

            Log::info('Author chat room processed', [
                'chat_room_id' => $chatRoom->id,
                'was_created' => $chatRoom->wasRecentlyCreated
            ]);

            return response()->json([
                'success' => true,
                'chat_room_id' => $chatRoom->id, // 🔥 ЧИСЛОВОЙ ID
                'chat_room' => $chatRoom,
                'is_new' => $chatRoom->wasRecentlyCreated
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating author chat: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createChat(Request $request)
    {
        $validated = $request->validate([
            'job_id' => 'required|integer',
            'user_email' => 'required|email',
            'user_name' => 'required|string',
            'initial_message' => 'required|string'
        ]);

        $job = Job::findOrFail($validated['job_id']);

        $chatRoom = ChatRoomService::findOrCreate(
            $job,
            $validated['user_email'],
            $validated['user_name']
        );

        $message = Message::create([
            'chat_room_id' => $chatRoom->id,
            'sender_email' => $validated['user_email'],
            'sender_name' => $validated['user_name'],
            'message' => $validated['initial_message'],
            'is_read' => false
        ]);

        return response()->json([
            'success' => true,
            'chat_room_id' => $chatRoom->id
        ]);
    }
    private function getInterlocutorInfo(ChatRoom $chatRoom, $currentUserEmail)
    {
        if ($chatRoom->candidate_email === $currentUserEmail) {
            return [
                'email' => $chatRoom->job->email,
                'name' => $chatRoom->job->contact_name,
                'type' => 'author'
            ];
        } else {
            return [
                'email' => $chatRoom->candidate_email,
                'name' => $chatRoom->candidate_name,
                'type' => 'candidate'
            ];
        }
    }
    public function getChatInfoApi($chatRoomId, Request $request)
    {
        try {
            $chatRoom = ChatRoom::with(['job'])->findOrFail($chatRoomId);

            if (!$this->verifyAccess($chatRoom, $request->user_email, $request->token)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Доступ запрещен'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'chat' => $chatRoom,
                'interlocutor' => $this->getInterlocutorInfo($chatRoom, $request->user_email)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Чат не найден'
            ], 404);
        }
    }

    private function verifyAccess(ChatRoom $chatRoom, $userEmail, $token): bool
    {
        if ($chatRoom->job->email === $userEmail && $chatRoom->author_token === $token) {
            return true;
        }

        if ($chatRoom->candidate_email === $userEmail && $chatRoom->candidate_token === $token) {
            return true;
        }

        return false;
    }

    public function closeChatApi($chatRoom, Request $request)
    {
        // реализация закрытия чата
    }

    public function getUnreadCountApi(Request $request)
    {
        // реализация получения количества непрочитанных сообщений
    }
}

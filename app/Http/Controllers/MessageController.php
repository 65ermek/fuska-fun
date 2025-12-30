<?php
// app/Http/Controllers/MessageController.php

namespace App\Http\Controllers;

use App\Mail\JobMessageToOwner;
use App\Models\ChatRoom;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class MessageController extends Controller
{
    // === API МЕТОДЫ ===
    /**
     * 🔥 ОТПРАВКА СООБЩЕНИЯ В ВИРТУАЛЬНУЮ КОМНАТУ
     */
    public function sendVirtualMessage(Request $request)
    {
        try {
            $validated = $request->validate([
                'chat_room_id' => 'required|string',
                'job_id' => 'required|exists:jobs,id',
                'sender_email' => 'required|email',
                'sender_name' => 'nullable|string',
                'message' => 'required|string|max:2000',
            ]);

            // 🔥 СОХРАНЯЕМ СООБЩЕНИЕ В ТАБЛИЦУ MESSAGES
            $message = Message::create([
                'chat_room_id' => $validated['chat_room_id'],
                'sender_email' => $validated['sender_email'],
                'sender_name' => $validated['sender_name'],
                'message' => $validated['message'],
                'is_read' => false
            ]);

            // 🔥 ОПРЕДЕЛЯЕМ ПОЛУЧАТЕЛЯ И ОТПРАВЛЯЕМ УВЕДОМЛЕНИЕ
            $parts = explode('_', $validated['chat_room_id']);
            $receiverEmail = ($parts[2] === $validated['sender_email']) ? $parts[3] : $parts[2];

            // Можно добавить отправку email уведомления
            // Mail::to($receiverEmail)->send(new NewMessageNotification($message));

            return response()->json([
                'status' => 'success',
                'message_id' => $message->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending virtual message: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * 🔥 ПОЛУЧЕНИЕ СООБЩЕНИЙ ВИРТУАЛЬНОЙ КОМНАТЫ
     */
    public function getVirtualMessages(Request $request)
    {
        try {
            $chatRoomId = $request->get('chat_room_id');

            if (!$chatRoomId) {
                return response()->json([]);
            }

            // 🔥 ПОЛУЧАЕМ СООБЩЕНИЯ ПО CHAT_ROOM_ID
            $messages = Message::where('chat_room_id', $chatRoomId)
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function($message) {
                    return [
                        'id' => $message->id,
                        'sender_email' => $message->sender_email,
                        'sender_name' => $message->sender_name,
                        'message' => $message->message,
                        'created_at' => $message->created_at,
                        'is_read' => $message->is_read
                    ];
                });

            return response()->json($messages);

        } catch (\Exception $e) {
            Log::error('Error getting virtual messages: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    // API: Отправить сообщение
    public function sendMessageApi(Request $request, $chatRoomId)
    {
        \Log::info('=== SEND MESSAGE API CALLED ===', [
            'chat_room_id' => $chatRoomId,
            'all_request_data' => $request->all(),
            'session_data' => [
                'user_email' => session('user_email'),
                'author_token' => session('author_token'),
                'candidate_token' => session('candidate_token')
            ]
        ]);
        $validator = Validator::make($request->all(), [
            'sender_email' => 'required|email',
            'sender_name' => 'required|string|max:100',
            'message' => 'required|string|max:2000',
            'token' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $chatRoom = ChatRoom::with(['job'])->findOrFail($chatRoomId);

            if (!$this->verifyAccess($chatRoom, $request->sender_email, $request->token)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Доступ запрещен'
                ], 403);
            }

            if ($chatRoom->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Чат закрыт'
                ], 400);
            }

            // Создаем сообщение
            $message = Message::create([
                'chat_room_id' => $chatRoomId,
                'sender_email' => $request->sender_email,
                'sender_name' => $request->sender_name,
                'message' => $request->message
            ]);

            // Обновляем время последней активности чата
            $chatRoom->touch();

            // Отправляем уведомление автору, если сообщение от кандидата
            if ($request->sender_email === $chatRoom->candidate_email) {
                $this->sendNotificationToAuthor($message, $chatRoom);
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при отправке сообщения: ' . $e->getMessage()
            ], 500);
        }
    }

// Отправка уведомления автору используя ваш Mailable
    private function sendNotificationToAuthor(Message $message, ChatRoom $chatRoom)
    {
        try {
            $authorEmail = $chatRoom->job->email;

            if (filter_var($authorEmail, FILTER_VALIDATE_EMAIL)) {
                // Генерируем ссылку для автора
                $chatLink = url('/chat/room/' . $chatRoom->id . '?token=' . $chatRoom->author_token . '&email=' . $chatRoom->job->email);

                // Используем ваш существующий Mailable
                Mail::to($authorEmail)->send(new JobMessageToOwner(
                    $message->sender_email,        // fromEmail
                    $message->message,             // text
                    $chatRoom->job,                // job
                    $chatLink                      // chatLink
                ));

                \Log::info('Chat notification sent to author', [
                    'author_email' => $authorEmail,
                    'chat_link' => $chatLink,
                    'message_id' => $message->id
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error sending chat notification email: ' . $e->getMessage(), [
                'author_email' => $authorEmail ?? 'unknown',
                'chat_room_id' => $chatRoom->id
            ]);
        }
    }

    // API: Получить сообщения
// В MessageController - метод getMessagesApi
    public function getMessagesApi($chatRoomId, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_email' => 'required|email',
            'token' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $chatRoom = ChatRoom::findOrFail($chatRoomId);

            if (!$this->verifyAccess($chatRoom, $request->user_email, $request->token)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Доступ запрещен'
                ], 403);
            }

            $messages = Message::where('chat_room_id', $chatRoomId)
                ->orderBy('created_at', 'asc')
                ->get();

            // Помечаем сообщения собеседника как прочитанные
            if ($messages->count() > 0) {
                Message::where('chat_room_id', $chatRoomId)
                    ->where('sender_email', '!=', $request->user_email)
                    ->where('is_read', false)
                    ->update(['is_read' => true]);
            }
            if ($validator->fails()) {
                \Log::error('MOBILE - Validation failed details:', [
                    'errors' => $validator->errors()->toArray(),
                    'input_data' => $request->all(),
                    'chat_room_id' => $chatRoomId
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return response()->json([
                'success' => true,
                'messages' => $messages
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении сообщений'
            ], 500);
        }
    }

    // API: Получить количество непрочитанных
    public function getUnreadCountApi(Request $request)
    {
        // ... существующий код getUnreadCount...
    }

    // === WEB МЕТОДЫ ===

    // Web: Отправить сообщение (через форму)
    public function sendMessageWeb(Request $request, $chatRoomId)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:2000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $chatRoom = ChatRoom::findOrFail($chatRoomId);

            // Определяем отправителя из сессии
            if (session('candidate_token')) {
                $senderEmail = session('user_email');
                $senderName = $chatRoom->candidate_name;
                $token = session('candidate_token');
            } else {
                $senderEmail = $chatRoom->job->contact_email ?? 'author';
                $senderName = $chatRoom->job->contact_name;
                $token = $chatRoom->author_token;
            }

            if (!$this->verifyAccess($chatRoom, $senderEmail, $token)) {
                abort(403, 'Доступ запрещен');
            }

            Message::create([
                'chat_room_id' => $chatRoomId,
                'sender_email' => $senderEmail,
                'sender_name' => $senderName,
                'message' => $request->message
            ]);

            $chatRoom->touch();

            return redirect()->back()->with('success', 'Сообщение отправлено!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ошибка при отправке сообщения');
        }
    }
// В MessageController - метод verifyAccess
    private function verifyAccess(ChatRoom $chatRoom, $userEmail, $token): bool
    {
        // Проверка автора (по email и токену объявления)
        if ($chatRoom->job->email === $userEmail && $chatRoom->author_token === $token) {
            return true;
        }

        // Проверка кандидата (по email и токену кандидата)
        if ($chatRoom->candidate_email === $userEmail && $chatRoom->candidate_token === $token) {
            return true;
        }
                return false;
    }
    // ... остальные методы без изменений
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatOnlineStatus;
use App\Models\ChatRoom;

class ChatOnlineStatusController extends Controller
{
    /**
     * Пометить пользователя онлайн (старый метод)
     */
    public static function markOnline($email, $name = null, $userType = null)
    {
        \Log::info('ChatOnlineStatus: markOnline()', [
            'email' => $email,
            'userType' => $userType,
            'timestamp' => now()->toDateTimeString()
        ]);

        return static::updateOrCreate(
            ['email' => $email],
            [
                'last_seen' => now(),
                'is_online' => true,
                'user_type' => $userType
            ]
        );
    }

    /**
     * Пометить пользователя оффлайн
     */
    public static function markOffline($email)
    {
        \Log::info('ChatOnlineStatus: markOffline()', [
            'email' => $email,
            'timestamp' => now()->toDateTimeString()
        ]);

        return static::where('email', $email)->update([
            'is_online' => false
        ]);
    }
    private function formatLastSeen($timestamp)
    {
        if (!$timestamp) {
            return null;
        }

        $time = \Carbon\Carbon::parse($timestamp);

        // если сегодня → показываем время
        if ($time->isToday()) {
            return $time->format('H:i'); // 18:30
        }

        // если не сегодня → день.месяц.год
        return $time->format('d.m.Y'); // 07.11.2025
    }

    /**
     * Получить онлайн-статус пользователя
     */
    public function getOnlineStatus(Request $request)
    {
        $data = $request->validate(['email' => 'required|email']);

        $status = ChatOnlineStatus::where('email', $data['email'])->first();

        if (!$status) {
            return response()->json([
                'email' => $data['email'],
                'is_online' => false,
                'last_seen' => null,
            ]);
        }

        return response()->json([
            'email'       => $data['email'],
            'is_online'   => ChatOnlineStatus::isOnline($data['email']), // ← ВАЖНО
            'last_seen'   => $this->formatLastSeen($status->last_seen),
        ]);
    }
    /**
     * Новый вариант обновления онлайн-статуса
     */
    public function updateOnlineStatus(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'name' => 'nullable|string',
            'user_type' => 'nullable|string',
        ]);

        $status = ChatOnlineStatus::markOnline(
            $data['email'],
            $data['name'] ?? null,
            $data['user_type'] ?? 'candidate'
        );

        return response()->json(['success' => true, 'status' => $status]);
    }

    /**
     * Новый GET метод проверки статуса
     */
    public function getOnlineStatusApi(Request $request)
    {
        $data = $request->validate(['email' => 'required|email']);

        return response()->json([
            'email' => $data['email'],
            'is_online' => ChatOnlineStatus::isOnline($data['email']),
        ]);
    }

    /**
     * Получить ВСЕ статусы
     */
    public function getAllStatuses()
    {
        return response()->json(ChatOnlineStatus::all());
    }

    /**
     * Получить статусы для участников конкретной чат-комнаты
     */
    public function getChatStatuses($chatRoomId)
    {
        $chatRoom = ChatRoom::findOrFail($chatRoomId);

        $statuses = ChatOnlineStatus::getChatStatuses($chatRoom);

        return response()->json($statuses);
    }
}

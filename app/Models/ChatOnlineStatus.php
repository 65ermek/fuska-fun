<?php
// app/Models/ChatOnlineStatus.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatOnlineStatus extends Model
{
    use HasFactory;

    protected $table = 'chat_online_statuses';

    protected $fillable = ['email', 'name', 'last_seen', 'is_online', 'user_type'];

    protected $casts = [
        'last_seen' => 'datetime',
        'is_online' => 'boolean'
    ];

    // Пометить пользователя как онлайн
    public static function markOnline($email, $name = null, $userType = 'candidate')
    {
        return static::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'last_seen' => now(),
                'is_online' => true,
                'user_type' => $userType
            ]
        );
    }

    // Пометить пользователя как оффлайн
    public static function markOffline($email)
    {
        return static::where('email', $email)->update([
            'is_online' => false
        ]);
    }

    // Проверить онлайн статус (онлайн если был активен последние 2 минуты)
    public static function isOnline($email)
    {
        $status = static::where('email', $email)->first();

        if (!$status) {
            return false;
        }

        return $status->is_online && $status->last_seen->gt(now()->subMinutes(2));
    }

    // Получить статусы для всех участников чата
    public static function getChatStatuses($chatRoom)
    {
        $emails = [
            $chatRoom->candidate_email,
            $chatRoom->job->email
        ];

        $statuses = static::whereIn('email', $emails)->get()->keyBy('email');

        return [
            'candidate' => [
                'email' => $chatRoom->candidate_email,
                'name' => $chatRoom->candidate_name,
                'online' => $statuses[$chatRoom->candidate_email]->is_online ?? false,
                'last_seen' => $statuses[$chatRoom->candidate_email]->last_seen ?? null
            ],
            'author' => [
                'email' => $chatRoom->job->email,
                'name' => $chatRoom->job->contact_name,
                'online' => $statuses[$chatRoom->job->email]->is_online ?? false,
                'last_seen' => $statuses[$chatRoom->job->email]->last_seen ?? null
            ]
        ];
    }
}

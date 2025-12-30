<?php
// app/Services/ChatRoomService.php

namespace App\Services;

use App\Models\ChatRoom;
use App\Models\Job;
use Illuminate\Support\Str;

class ChatRoomService
{
    public static function findOrCreate(Job $job, $candidateEmail, $candidateName = null, $authorEmail = null, $authorName = null)
    {
        // Существующая логика...
        $chatRoom = ChatRoom::firstOrCreate(
            [
                'job_id' => $job->id,
                'candidate_email' => $candidateEmail
            ],
            [
                'candidate_name' => $candidateName ?? $candidateEmail,
                'author_email' => $authorEmail ?? $job->email,
                'author_name' => $authorName ?? $job->contact_name,
                'author_token' => $job->edit_token,
                'candidate_token' => Str::random(40), // 🔥 ГЕНЕРИРУЕМ ТОКЕН
                'status' => 'active'
            ]
        );

        // 🔥 ОБНОВЛЯЕМ candidate_token если его нет
        if (empty($chatRoom->candidate_token)) {
            $chatRoom->update([
                'candidate_token' => Str::random(40)
            ]);
            $chatRoom->refresh();
        }

        return $chatRoom;
    }
}

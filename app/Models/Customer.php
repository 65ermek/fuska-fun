<?php
// app/Models/Customer.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'email',
        'phone',
        'name',
        'company',
        'persistent_token',
        'last_seen_at',
        'source',
        'notes'
    ];

    protected $casts = [
        'last_seen_at' => 'datetime'
    ];

    // Создать или найти customer
    public static function findOrCreate($email, $name = null, $source = 'chat')
    {
        return static::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name ?? self::generateNameFromEmail($email),
                'persistent_token' => bin2hex(random_bytes(32)),
                'source' => $source
            ]
        );
    }

    // Обновить активность
    public function markAsActive()
    {
        $this->update(['last_seen_at' => now()]);
    }

    // Сгенерировать имя
    private static function generateNameFromEmail($email)
    {
        $name = explode('@', $email)[0];
        return ucfirst(str_replace(['.', '_', '-'], ' ', $name));
    }

    // SCOPE: Только чат-пользователи
    public function scopeChatUsers($query)
    {
        return $query->where('source', 'chat');
    }

    // SCOPE: Контактная форма
    public function scopeContactForm($query)
    {
        return $query->where('source', 'contact_form');
    }

    // Отношения
    public function authoredChats()
    {
        return $this->hasMany(ChatRoom::class, 'job_email', 'email');
    }

    public function candidateChats()
    {
        return $this->hasMany(ChatRoom::class, 'candidate_email', 'email');
    }

    // Проверить, является ли чат-пользователем
    public function isChatUser()
    {
        return in_array($this->source, ['chat', 'auto_detected']);
    }

    // Получить все чаты (если является чат-пользователем)
    public function chatRooms()
    {
        if (!$this->isChatUser()) {
            return collect();
        }

        return ChatRoom::where(function($query) {
            $query->where('job_email', $this->email)
                ->orWhere('candidate_email', $this->email);
        })->get();
    }
    public function updateRole($newRole)
    {
        $currentSource = $this->source ?? 'visitor';

        // Если текущая роль уже совпадает
        if ($currentSource === $newRole) {
            return $currentSource;
        }

        // Если пользователь был visitor - назначаем новую роль
        if ($currentSource === 'visitor') {
            return $newRole;
        }

        // 🔥 ОБЪЕДИНЯЕМ РОЛИ В "both"
        $roles = [$currentSource, $newRole];

        if (in_array('author', $roles) && in_array('candidat', $roles)) {
            return 'both';
        }

        // На всякий случай возвращаем новую роль
        return $newRole;
    }
}

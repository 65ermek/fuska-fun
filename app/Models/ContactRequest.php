<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'name',
        'email',
        'phone',
        'message',
        'ip_address',
        'user_agent',
        'status',
        'is_read',
    ];
// Добавляем scope для непрочитанных
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

// Метод для пометки как прочитанное
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Отношение к объявлению
     */
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Scope для фильтрации по статусу
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope для поиска
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('email', 'like', "%{$search}%")
                ->orWhere('message', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhereHas('job', function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                });
        });
    }

    /**
     * Scope для фильтрации по дате
     */
    public function scopeDateRange($query, $from, $to)
    {
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }
        return $query;
    }

    /**
     * Получить статус в читаемом формате
     */
    public function getStatusTextAttribute()
    {
        return [
            'pending' => 'Čeká',
            'sent' => 'Odesláno',
            'failed' => 'Chyba'
        ][$this->status] ?? $this->status;
    }

    /**
     * Получить цвет статуса
     */
    public function getStatusColorAttribute()
    {
        return [
            'pending' => 'warning',
            'sent' => 'success',
            'failed' => 'danger'
        ][$this->status] ?? 'secondary';
    }
}

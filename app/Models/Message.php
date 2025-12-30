<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_room_id',
        'sender_email',
        'sender_name',
        'message',
        'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function chatRoom(): BelongsTo
    {
        return $this->belongsTo(ChatRoom::class);
    }

    public function senderCustomer()
    {
        return $this->belongsTo(Customer::class, 'sender_email', 'email');
    }

    public function markAsRead()
    {
        $this->timestamps = false;
        $this->update(['is_read' => true]);
    }
}

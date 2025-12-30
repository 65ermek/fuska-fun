<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'candidate_email',
        'candidate_name',
        'author_email',
        'author_name',
        'author_token',
        'candidate_token',
        'status'
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function unreadMessages()
    {
        return $this->hasMany(Message::class)->where('is_read', false);
    }

    public function authorCustomer()
    {
        return $this->belongsTo(Customer::class, 'author_email', 'email');
    }

    public function candidateCustomer()
    {
        return $this->belongsTo(Customer::class, 'candidate_email', 'email');
    }
}

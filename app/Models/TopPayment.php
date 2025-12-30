<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopPayment extends Model
{
    use HasFactory;

    protected $table = 'top_payments';

    protected $fillable = [
        'job_id',
        'count',
        'amount',
        'status',
        'paid_at',
        'vs_number',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    /**
     * Связь с объявлением (Job)
     */
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Проверка, оплачено ли топование
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}

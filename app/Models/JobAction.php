<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobAction extends Model
{
    use HasFactory;
    protected $fillable = ['email', 'job_id', 'action', 'meta', 'ip', 'note'
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}

<?php

namespace App\Models; use Illuminate\Database\Eloquent\Model;
class JobPhoto extends Model
{
    protected $fillable=['job_id','path','sort'];
    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}

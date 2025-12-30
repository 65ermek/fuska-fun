<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'job_category_id', 'city', 'district', 'title', 'description',
        'pay_type', 'price', 'price_negotiable', 'contact_name', 'phone','email',
        'telegram', 'whatsapp', 'status', 'edit_token', 'lang', 'ip', 'ua',
        'path',
        'sort'
    ];

    public function category()
    {
        return $this->belongsTo(JobCategory::class, 'job_category_id');
    }

    public function photos()
    {
        return $this->hasMany(JobPhoto::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getPriceLabelAttribute(): string
    {
        if (is_null($this->price)) {
            return __('messages.price_negotiable');
        }

        return number_format($this->price, 0, ',', ' ') . ' Kč' . ($this->pay_type === 'per_hour' ? '/h' : '');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
    // Добавляем отношения с чатами
    public function chatRooms()
    {
        return $this->hasMany(ChatRoom::class);
    }

    // Получить email автора (контактное лицо)
    public function getAuthorEmail()
    {
        return $this->email;
    }

    // Alias для совместимости
    public function getContactEmail()
    {
        return $this->email;
    }
}

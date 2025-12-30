<?php
// app/Models/User.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'role',
        'lang',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Accessor для аватарки
    // В модели User
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar && file_exists(public_path('images/avatars/' . $this->avatar))) {
            return asset('images/avatars/' . $this->avatar);
        }

        return asset('images/avatars/default-avatar.png');
    }

    // Проверки ролей
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    public function isManager()
    {
        return $this->role === 'manager';
    }
    public function isModerator(): bool
    {
        return $this->role === 'moderator';
    }

    // Проверка может ли управлять контентом
    public function canManageContent(): bool
    {
        return $this->isAdmin() || $this->isModerator();
    }
    // Получение доступных ролей (для админки)
    public static function getAvailableRoles()
    {
        return [
            'admin' => 'Administrator (Full Access)',
            'moderator' => 'Moderator (Content Management)',
        ];
    }

    // Получение доступных языков
    public static function getAvailableLanguages()
    {
        return [
            'cs' => 'Čeština',
            'ru' => 'Русский',
            'uk' => 'Українська',
            'uz' => 'Oʻzbekcha',
            'ro' => 'Română',
            'en' => 'English',
        ];
    }

    // Scope для активных пользователей
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope для администраторов
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    // Scope для модераторов
    public function scopeModerators($query)
    {
        return $query->where('role', 'moderator');
    }
}

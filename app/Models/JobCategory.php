<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class JobCategory extends Model
{
    protected $fillable=['name','slug','image','sort'];
    /**
     * Автоматическое создание slug при сохранении
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Scope для сортировки
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort')->orderBy('name');
    }
    // В модели JobCategory
    public function getImageUrlAttribute()
    {
        return asset('images/' . $this->image);
    }
    /**
     * Scope для активных (можно добавить позже если нужно)
     */
    public function scopeActive($query)
    {
        return $query; // Пока все категории активны
    }
    public function jobs(){ return $this->hasMany(Job::class); } }


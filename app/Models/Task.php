<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'instructions',
        'task_type',
        'task_meta',
        'reward',
        'estimated_minutes',
        'level_id',
        'status',
        'total_slots',
        'remaining_slots',
        'category',
        'is_featured',
    ];

    protected $casts = [
        'reward' => 'decimal:2',
        'task_meta' => 'array',
        'is_featured' => 'boolean',
    ];

    public static function categories(): array
    {
        return [
            'general' => 'General',
            'daily' => 'Daily',
            'premium' => 'Premium',
        ];
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::categories()[$this->category] ?? ucfirst($this->category);
    }

    public static function taskTypes(): array
    {
        return [
            'youtube' => 'Watch YouTube Video',
        ];
    }

    public function getTaskTypeLabelAttribute(): string
    {
        return self::taskTypes()[$this->task_type] ?? ucfirst($this->task_type);
    }

    /**
     * Get the task meta for a specific type with defaults.
     */
    public function getMeta(string $key, mixed $default = null): mixed
    {
        return $this->task_meta[$key] ?? $default;
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function submissions()
    {
        return $this->hasMany(TaskSubmission::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAvailable($query)
    {
        return $query->where(function ($q) {
            $q->where('total_slots', 0)
              ->orWhere('remaining_slots', '>', 0);
        });
    }

    public function scopeDaily($query)
    {
        return $query->where('category', 'daily');
    }

    public function scopePremium($query)
    {
        return $query->where('category', 'premium');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeGeneral($query)
    {
        return $query->where('category', 'general');
    }

    /**
     * Check if a user has completed this task.
     */
    public function isCompletedBy(User $user): bool
    {
        return $this->submissions()->where('user_id', $user->id)->exists();
    }

    /**
     * Get the user's submission for this task.
     */
    public function userSubmission(User $user)
    {
        return $this->submissions()->where('user_id', $user->id)->first();
    }
}

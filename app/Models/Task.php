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
    ];

    protected $casts = [
        'reward' => 'decimal:2',
        'task_meta' => 'array',
    ];

    public static function taskTypes(): array
    {
        return [
            'text' => 'Text Submission',
            'url' => 'URL / Link',
            'youtube' => 'Watch YouTube Video',
            'video' => 'Watch Video (Other)',
            'image' => 'Image Upload',
            'file' => 'File Upload',
            'social_share' => 'Social Media Share',
            'quiz' => 'Quiz / Questions',
            'code' => 'Code Submission',
            'custom' => 'Custom Task',
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
}

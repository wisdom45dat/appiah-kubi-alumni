<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forum extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function topics()
    {
        return $this->hasMany(ForumTopic::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }

    // Methods
    public function getLatestTopicAttribute()
    {
        return $this->topics()->latest()->first();
    }

    public function updateCounts()
    {
        $this->update([
            'topics_count' => $this->topics()->count(),
            'posts_count' => ForumPost::whereIn('topic_id', $this->topics()->pluck('id'))->count(),
        ]);
    }
}

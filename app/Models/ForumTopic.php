<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumTopic extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'forum_id',
        'user_id',
        'title',
        'content',
        'is_pinned',
        'is_locked',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'last_reply_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function forum()
    {
        return $this->belongsTo(Forum::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function posts()
    {
        return $this->hasMany(ForumPost::class);
    }

    public function lastReplyBy()
    {
        return $this->belongsTo(User::class, 'last_reply_by');
    }

    // Scopes
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeUnlocked($query)
    {
        return $query->where('is_locked', false);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('last_reply_at', 'desc');
    }

    // Methods
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function updateLastReply(ForumPost $post)
    {
        $this->update([
            'last_reply_by' => $post->user_id,
            'last_reply_at' => $post->created_at,
            'replies_count' => $this->posts()->count() - 1, // Subtract the original post
        ]);

        $this->forum->updateCounts();
    }

    public function canUserPost(User $user)
    {
        return !$this->is_locked || $user->hasRole('admin');
    }
}

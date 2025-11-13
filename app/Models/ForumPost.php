<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'topic_id',
        'user_id',
        'parent_id',
        'content',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function topic()
    {
        return $this->belongsTo(ForumTopic::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(ForumPost::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(ForumPost::class, 'parent_id');
    }

    public function likes()
    {
        return $this->hasMany(ForumPostLike::class);
    }

    // Scopes
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    // Methods
    public function isOriginalPost()
    {
        return is_null($this->parent_id);
    }

    public function hasReplies()
    {
        return $this->replies()->count() > 0;
    }

    public function getDepthAttribute()
    {
        $depth = 0;
        $post = $this;
        
        while ($post->parent_id) {
            $depth++;
            $post = $post->parent;
        }
        
        return $depth;
    }
}

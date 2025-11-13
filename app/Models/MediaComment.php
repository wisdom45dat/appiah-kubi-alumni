<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'media_id',
        'user_id',
        'parent_id',
        'comment',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function media()
    {
        return $this->belongsTo(Media::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(MediaComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(MediaComment::class, 'parent_id');
    }

    public function likes()
    {
        return $this->hasMany(MediaCommentLike::class);
    }

    // Scopes
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    // Methods
    public function hasReplies()
    {
        return $this->replies()->count() > 0;
    }
}

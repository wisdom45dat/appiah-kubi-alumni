<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'album_id',
        'uploaded_by',
        'file_path',
        'file_name',
        'file_type',
        'mime_type',
        'file_size',
        'caption',
        'tags',
        'metadata',
        'privacy',
    ];

    protected $casts = [
        'tags' => 'array',
        'metadata' => 'array',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function album()
    {
        return $this->belongsTo(Album::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function likes()
    {
        return $this->hasMany(MediaLike::class);
    }

    public function comments()
    {
        return $this->hasMany(MediaComment::class);
    }

    // Scopes
    public function scopeImages($query)
    {
        return $query->where('file_type', 'image');
    }

    public function scopeVideos($query)
    {
        return $query->where('file_type', 'video');
    }

    public function scopePopular($query)
    {
        return $query->orderBy('views_count', 'desc');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // Methods
    public function getFileUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }

    public function getThumbnailUrlAttribute()
    {
        if ($this->file_type === 'image') {
            $path = pathinfo($this->file_path);
            $thumbnailPath = $path['dirname'] . '/thumbs/' . $path['filename'] . '.jpg';
            return file_exists(storage_path('app/public/' . $thumbnailPath)) 
                ? asset('storage/' . $thumbnailPath)
                : $this->file_url;
        }
        return asset('images/video-thumbnail.jpg');
    }

    public function isLikedBy(User $user)
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function isImage()
    {
        return $this->file_type === 'image';
    }

    public function isVideo()
    {
        return $this->file_type === 'video';
    }
}

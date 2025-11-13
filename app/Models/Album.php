<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Album extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'cover_image',
        'privacy',
        'created_by',
        'category',
        'album_year',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function media()
    {
        return $this->hasMany(Media::class);
    }

    public function photos()
    {
        return $this->hasMany(Media::class)->where('file_type', 'image');
    }

    public function videos()
    {
        return $this->hasMany(Media::class)->where('file_type', 'video');
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('privacy', 'public');
    }

    public function scopeAlumniOnly($query)
    {
        return $query->where('privacy', 'alumni');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('album_year', $year);
    }

    public function scopeFeatured($query)
    {
        return $query->whereHas('media', function($q) {
            $q->where('is_featured', true);
        });
    }

    // Methods
    public function getCoverImageUrlAttribute()
    {
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }

        $firstMedia = $this->media()->first();
        return $firstMedia ? $firstMedia->file_url : asset('images/default-album-cover.jpg');
    }

    public function canView(User $user = null)
    {
        if ($this->privacy === 'public') {
            return true;
        }

        if (!$user) {
            return false;
        }

        if ($this->privacy === 'alumni') {
            return $user->hasRole('alumni');
        }

        if ($this->privacy === 'private') {
            return $user->id === $this->created_by;
        }

        return false;
    }

    public function updateCounts()
    {
        $this->update([
            'photo_count' => $this->photos()->count(),
            'video_count' => $this->videos()->count(),
        ]);
    }
}

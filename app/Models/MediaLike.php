<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'media_id',
        'user_id',
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

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}

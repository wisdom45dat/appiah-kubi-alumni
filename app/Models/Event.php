<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'featured_image',
        'type',
        'start_date',
        'end_date',
        'venue_name',
        'venue_address',
        'latitude',
        'longitude',
        'registration_fee',
        'capacity',
        'is_published',
        'is_featured',
        'registration_fields',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'registration_fields' => 'array',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function photos()
    {
        return $this->hasMany(EventPhoto::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    public function scopePast($query)
    {
        return $query->where('end_date', '<', now());
    }

    public function scopeCurrent($query)
    {
        return $query->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    // Methods
    public function getFeaturedImageUrlAttribute()
    {
        return $this->featured_image ? asset('storage/' . $this->featured_image) : asset('images/default-event.jpg');
    }

    public function isUpcoming()
    {
        return $this->start_date > now();
    }

    public function isPast()
    {
        return $this->end_date < now();
    }

    public function isCurrent()
    {
        return $this->start_date <= now() && $this->end_date >= now();
    }

    public function getRegistrationCountAttribute()
    {
        return $this->registrations()->where('status', 'confirmed')->count();
    }

    public function isFull()
    {
        return $this->capacity && $this->registration_count >= $this->capacity;
    }

    public function hasUserRegistered(User $user)
    {
        return $this->registrations()->where('user_id', $user->id)->exists();
    }
}

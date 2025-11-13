<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    use  HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar',
        'graduation_year',
        'house',
        'bio',
        'current_profession',
        'current_company',
        'current_city',
        'current_country',
        'social_links',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'social_links' => 'array',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function albums()
    {
        return $this->hasMany(Album::class, 'created_by');
    }

    public function media()
    {
        return $this->hasMany(Media::class, 'uploaded_by');
    }

    public function eventRegistrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function jobListings()
    {
        return $this->hasMany(JobListing::class, 'posted_by');
    }

    public function forumTopics()
    {
        return $this->hasMany(ForumTopic::class);
    }

    public function forumPosts()
    {
        return $this->hasMany(ForumPost::class);
    }

    public function mentorshipAsMentor()
    {
        return $this->hasMany(MentorshipMatch::class, 'mentor_id');
    }

    public function mentorshipAsMentee()
    {
        return $this->hasMany(MentorshipMatch::class, 'mentee_id');
    }

    // Scopes
    public function scopeByGraduationYear($query, $year)
    {
        return $query->where('graduation_year', $year);
    }

    public function scopeByHouse($query, $house)
    {
        return $query->where('house', $house);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    // Methods
    public function getBatchmatesAttribute()
    {
        return self::where('graduation_year', $this->graduation_year)
            ->where('id', '!=', $this->id)
            ->get();
    }

    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : asset('images/default-avatar.png');
    }

    public function getProfileCompletionAttribute()
    {
        $totalFields = 8; // name, email, graduation_year, profession, company, city, country, bio
        $completedFields = 2; // name and email are always filled

        if ($this->graduation_year) $completedFields++;
        if ($this->current_profession) $completedFields++;
        if ($this->current_company) $completedFields++;
        if ($this->current_city) $completedFields++;
        if ($this->current_country) $completedFields++;
        if ($this->bio) $completedFields++;
        if ($this->avatar) $completedFields++;

        return round(($completedFields / $totalFields) * 100);
    }

    public function isBatchmateWith(User $user)
    {
        return $this->graduation_year === $user->graduation_year;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MentorshipProgram extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'category',
        'skills_covered',
        'duration',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'skills_covered' => 'array',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function matches()
    {
        return $this->hasMany(MentorshipMatch::class, 'program_id');
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

    // Methods
    public function getActiveMatchesCountAttribute()
    {
        return $this->matches()->where('status', 'active')->count();
    }

    public function getCompletedMatchesCountAttribute()
    {
        return $this->matches()->where('status', 'completed')->count();
    }
}

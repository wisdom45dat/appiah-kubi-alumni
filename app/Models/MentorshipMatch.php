<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentorshipMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'mentor_id',
        'mentee_id',
        'status',
        'start_date',
        'end_date',
        'goals',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relationships
    public function program()
    {
        return $this->belongsTo(MentorshipProgram::class, 'program_id');
    }

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function mentee()
    {
        return $this->belongsTo(User::class, 'mentee_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'end_date' => now(),
        ]);
    }

    public function getDurationInDaysAttribute()
    {
        if (!$this->start_date) return 0;
        $endDate = $this->end_date ?? now();
        return $this->start_date->diffInDays($endDate);
    }
}

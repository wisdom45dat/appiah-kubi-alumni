<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'status',
        'amount_paid',
        'registration_data',
        'guests_count',
        'special_requirements',
        'checked_in_at',
    ];

    protected $casts = [
        'registration_data' => 'array',
        'checked_in_at' => 'datetime',
        'amount_paid' => 'decimal:2',
    ];

    // Relationships
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAttended($query)
    {
        return $query->whereNotNull('checked_in_at');
    }

    // Methods
    public function isConfirmed()
    {
        return $this->status === 'confirmed';
    }

    public function isCheckedIn()
    {
        return !is_null($this->checked_in_at);
    }

    public function checkIn()
    {
        $this->update([
            'checked_in_at' => now(),
            'status' => 'attended',
        ]);
    }

    public function getTotalAmountAttribute()
    {
        return $this->amount_paid + ($this->event->registration_fee * $this->guests_count);
    }
}

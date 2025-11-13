<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'user_id',
        'donor_name',
        'donor_email',
        'donor_phone',
        'amount',
        'currency',
        'payment_method',
        'transaction_id',
        'status',
        'is_anonymous',
        'message',
        'payment_details',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_details' => 'array',
        'is_anonymous' => 'boolean',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function donor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeAnonymous($query)
    {
        return $query->where('is_anonymous', true);
    }

    // Methods
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function getDisplayNameAttribute()
    {
        if ($this->is_anonymous) {
            return 'Anonymous Donor';
        }
        return $this->donor_name;
    }

    public function markAsCompleted()
    {
        $this->update(['status' => 'completed']);
        $this->campaign->updateCurrentAmount();
    }

    public function getFormattedAmountAttribute()
    {
        return $this->currency . ' ' . number_format($this->amount, 2);
    }
}

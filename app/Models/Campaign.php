<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'featured_image',
        'type',
        'target_amount',
        'current_amount',
        'start_date',
        'end_date',
        'is_active',
        'is_featured',
        'allowed_payment_methods',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'allowed_payment_methods' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOngoing($query)
    {
        return $query->where('start_date', '<=', now())
                    ->where(function($q) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', now());
                    });
    }

    // Methods
    public function getFeaturedImageUrlAttribute()
    {
        return $this->featured_image ? asset('storage/' . $this->featured_image) : asset('images/default-campaign.jpg');
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->target_amount == 0) return 0;
        return min(100, round(($this->current_amount / $this->target_amount) * 100, 2));
    }

    public function getDaysRemainingAttribute()
    {
        if (!$this->end_date) return null;
        return max(0, now()->diffInDays($this->end_date, false));
    }

    public function isActive()
    {
        return $this->is_active && 
               $this->start_date <= now() && 
               (!$this->end_date || $this->end_date >= now());
    }

    public function updateCurrentAmount()
    {
        $this->update([
            'current_amount' => $this->donations()->where('status', 'completed')->sum('amount')
        ]);
    }
}

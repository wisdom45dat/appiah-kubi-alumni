<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobListing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'company_name',
        'employment_type',
        'location',
        'is_remote',
        'salary_min',
        'salary_max',
        'salary_currency',
        'application_url',
        'contact_email',
        'application_deadline',
        'is_active',
        'is_featured',
        'posted_by',
    ];

    protected $casts = [
        'is_remote' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'application_deadline' => 'date',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function poster()
    {
        return $this->belongsTo(User::class, 'posted_by');
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

    public function scopeRemote($query)
    {
        return $query->where('is_remote', true);
    }

    public function scopeRecent($query)
    {
        return $query->where('created_at', '>=', now()->subDays(30));
    }

    public function scopeExpired($query)
    {
        return $query->where('application_deadline', '<', now());
    }

    // Methods
    public function isActive()
    {
        return $this->is_active && 
               (!$this->application_deadline || $this->application_deadline >= now());
    }

    public function getSalaryRangeAttribute()
    {
        if ($this->salary_min && $this->salary_max) {
            return $this->salary_currency . ' ' . 
                   number_format($this->salary_min) . ' - ' . 
                   number_format($this->salary_max);
        } elseif ($this->salary_min) {
            return $this->salary_currency . ' ' . number_format($this->salary_min) . '+';
        } elseif ($this->salary_max) {
            return 'Up to ' . $this->salary_currency . ' ' . number_format($this->salary_max);
        }
        return 'Negotiable';
    }

    public function getIsExpiredAttribute()
    {
        return $this->application_deadline && $this->application_deadline < now();
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }
}

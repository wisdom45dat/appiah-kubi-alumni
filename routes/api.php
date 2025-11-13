<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::middleware(['auth:sanctum'])->group(function () {
    // Alumni search API
    Route::get('/alumni/search', [DashboardController::class, 'searchAlumni']);
    
    // Profile completion API
    Route::get('/profile/completion', function (Request $request) {
        return response()->json([
            'completion_percentage' => $request->user()->profile_completion,
            'missing_fields' => [] // You can implement this to show which fields are missing
        ]);
    });
});

// Public APIs
Route::get('/stats', function () {
    return response()->json([
        'total_alumni' => \App\Models\User::role('alumni')->count(),
        'total_events' => \App\Models\Event::published()->count(),
        'total_donations' => \App\Models\Donation::completed()->sum('amount'),
        'active_campaigns' => \App\Models\Campaign::active()->count(),
    ]);
});

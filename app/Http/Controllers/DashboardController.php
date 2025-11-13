<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Album;
use App\Models\Event;
use App\Models\NewsArticle;
use App\Models\ForumTopic;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Basic stats
        $stats = [
            'total_alumni' => User::role('alumni')->count(),
            'recent_albums' => Album::with('creator')->latest()->limit(5)->get(),
            'upcoming_events' => Event::published()->upcoming()->limit(3)->get(),
            'latest_news' => NewsArticle::published()->latest()->limit(3)->get(),
            'active_campaigns' => Campaign::active()->ongoing()->limit(3)->get(),
            'forum_activity' => ForumTopic::with(['user', 'forum'])->latest()->limit(5)->get(),
        ];

        // Batchmates for the current user
        $batchmates = $user->batchmates->take(6);

        // Recent photos from public albums
        $recentPhotos = \App\Models\Media::with(['album', 'uploader'])
            ->whereHas('album', function($query) {
                $query->where('privacy', 'public')
                      ->orWhere('privacy', 'alumni');
            })
            ->where('file_type', 'image')
            ->latest()
            ->limit(12)
            ->get();

        return view('dashboard.index', compact('stats', 'batchmates', 'recentPhotos', 'user'));
    }

    public function alumniDirectory(Request $request)
    {
        $query = User::role('alumni')->with('roles');

        // Apply filters
        if ($request->has('graduation_year') && $request->graduation_year) {
            $query->where('graduation_year', $request->graduation_year);
        }

        if ($request->has('house') && $request->house) {
            $query->where('house', $request->house);
        }

        if ($request->has('profession') && $request->profession) {
            $query->where('current_profession', 'like', '%' . $request->profession . '%');
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('current_company', 'like', '%' . $search . '%')
                  ->orWhere('current_profession', 'like', '%' . $search . '%');
            });
        }

        $alumni = $query->latest()->paginate(24);

        $graduationYears = User::role('alumni')->distinct()->pluck('graduation_year')->sort();
        $houses = User::role('alumni')->distinct()->pluck('house')->filter();

        return view('dashboard.directory', compact('alumni', 'graduationYears', 'houses'));
    }

    public function searchAlumni(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $results = User::role('alumni')
            ->where(function($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                  ->orWhere('email', 'like', '%' . $query . '%')
                  ->orWhere('current_profession', 'like', '%' . $query . '%')
                  ->orWhere('current_company', 'like', '%' . $query . '%')
                  ->orWhere('graduation_year', 'like', '%' . $query . '%');
            })
            ->limit(10)
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'graduation_year' => $user->graduation_year,
                    'profession' => $user->current_profession,
                    'company' => $user->current_company,
                    'avatar' => $user->avatar_url,
                    'profile_url' => route('profile.show', $user),
                ];
            });

        return response()->json($results);
    }
}

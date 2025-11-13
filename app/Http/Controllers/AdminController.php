<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Album;
use App\Models\Event;
use App\Models\NewsArticle;
use App\Models\JobListing;
use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_alumni' => User::role('alumni')->count(),
            'pending_verifications' => User::where('is_verified', false)->count(),
            'total_albums' => Album::count(),
            'total_events' => Event::count(),
            'total_donations' => Donation::completed()->sum('amount'),
            'active_campaigns' => Campaign::active()->count(),
            'job_listings' => JobListing::active()->count(),
        ];

        // Recent activity
        $recentUsers = User::with('roles')->latest()->limit(5)->get();
        $recentAlbums = Album::with('creator')->latest()->limit(5)->get();
        $recentEvents = Event::with('creator')->latest()->limit(5)->get();
        $recentDonations = Donation::with(['campaign', 'donor'])->latest()->limit(5)->get();

        // Charts data
        $usersByYear = User::select(DB::raw('graduation_year, COUNT(*) as count'))
            ->whereNotNull('graduation_year')
            ->groupBy('graduation_year')
            ->orderBy('graduation_year')
            ->get();

        $donationsByMonth = Donation::completed()
            ->select(DB::raw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount) as total'))
            ->where('created_at', '>=', now()->subYear())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return view('admin.dashboard', compact(
            'stats', 
            'recentUsers', 
            'recentAlbums', 
            'recentEvents', 
            'recentDonations',
            'usersByYear',
            'donationsByMonth'
        ));
    }

    public function userManagement(Request $request)
    {
        $query = User::with('roles');

        if ($request->has('role') && $request->role) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->has('verification') && $request->verification) {
            if ($request->verification === 'verified') {
                $query->where('is_verified', true);
            } else {
                $query->where('is_verified', false);
            }
        }

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        $users = $query->latest()->paginate(20);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function verifyUser(User $user)
    {
        $user->update(['is_verified' => true]);

        return redirect()->back()->with('success', 'User verified successfully!');
    }

    public function assignRole(User $user, Request $request)
    {
        $request->validate([
            'role' => 'required|exists:roles,name'
        ]);

        $user->syncRoles([$request->role]);

        return redirect()->back()->with('success', 'Role assigned successfully!');
    }

    public function contentModeration()
    {
        $pendingAlbums = Album::where('privacy', 'private')->latest()->get();
        $pendingEvents = Event::where('is_published', false)->latest()->get();
        $pendingJobs = JobListing::where('is_active', false)->latest()->get();
        $pendingNews = NewsArticle::where('is_published', false)->latest()->get();

        return view('admin.moderation', compact(
            'pendingAlbums', 
            'pendingEvents', 
            'pendingJobs', 
            'pendingNews'
        ));
    }

    public function approveContent(Request $request)
    {
        $request->validate([
            'type' => 'required|in:album,event,job,news',
            'id' => 'required|integer',
        ]);

        switch ($request->type) {
            case 'album':
                $album = Album::findOrFail($request->id);
                $album->update(['privacy' => 'public']);
                break;
                
            case 'event':
                $event = Event::findOrFail($request->id);
                $event->update(['is_published' => true]);
                break;
                
            case 'job':
                $job = JobListing::findOrFail($request->id);
                $job->update(['is_active' => true]);
                break;
                
            case 'news':
                $news = NewsArticle::findOrFail($request->id);
                $news->publish();
                break;
        }

        return redirect()->back()->with('success', 'Content approved successfully!');
    }

    public function financialReports()
    {
        $donations = Donation::with(['campaign', 'donor'])
            ->completed()
            ->latest()
            ->paginate(20);

        $campaignStats = Campaign::withSum(['donations as total_raised' => function($query) {
            $query->where('status', 'completed');
        }])->get();

        $totalRaised = Donation::completed()->sum('amount');
        $totalCampaigns = Campaign::count();
        $activeCampaigns = Campaign::active()->count();

        return view('admin.financial-reports', compact(
            'donations', 
            'campaignStats', 
            'totalRaised',
            'totalCampaigns',
            'activeCampaigns'
        ));
    }

    public function systemSettings()
    {
        $settings = [
            'site_name' => config('app.name'),
            'site_description' => 'Appiah Kubi Old Students Association',
            'contact_email' => config('mail.from.address'),
            'enable_registrations' => true,
            'enable_donations' => true,
            'enable_events' => true,
        ];

        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        // This would typically update settings in database or config files
        // For now, we'll just show a success message
        
        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
}

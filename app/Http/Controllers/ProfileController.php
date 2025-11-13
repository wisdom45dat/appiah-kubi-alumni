<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;

class ProfileController extends Controller
{
    public function show(User $user = null)
    {
        $user = $user ?? auth()->user();
        $batchmates = $user->batchmates->take(8);
        $recentMedia = $user->media()->with('album')->latest()->limit(6)->get();
        $userEvents = $user->eventRegistrations()->with('event')->latest()->limit(5)->get();

        return view('profile.show', compact('user', 'batchmates', 'recentMedia', 'userEvents'));
    }

    public function edit()
    {
        $user = auth()->user();
        $houses = ['Perseverance', 'Integrity', 'Excellence', 'Unity', 'Discipline'];
        $currentYear = date('Y');
        $graduationYears = range(1970, $currentYear);
        rsort($graduationYears);

        return view('profile.edit', compact('user', 'houses', 'graduationYears'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'graduation_year' => 'required|digits:4',
            'house' => 'nullable|string|max:50',
            'bio' => 'nullable|string|max:1000',
            'current_profession' => 'nullable|string|max:255',
            'current_company' => 'nullable|string|max:255',
            'current_city' => 'nullable|string|max:100',
            'current_country' => 'nullable|string|max:100',
            'social_links.linkedin' => 'nullable|url',
            'social_links.facebook' => 'nullable|url',
            'social_links.twitter' => 'nullable|url',
            'social_links.instagram' => 'nullable|url',
            'avatar' => 'nullable|image|max:2048',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            
            // Create thumbnail
            $this->createAvatarThumbnail($avatarPath);
            
            $validated['avatar'] = $avatarPath;
        }

        // Handle social links
        $socialLinks = [];
        foreach (['linkedin', 'facebook', 'twitter', 'instagram'] as $platform) {
            if ($request->filled("social_links.{$platform}")) {
                $socialLinks[$platform] = $request->input("social_links.{$platform}");
            }
        }
        $validated['social_links'] = $socialLinks;

        $user->update($validated);

        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|min:8|confirmed',
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'Password updated successfully.');
    }

    public function albums(User $user = null)
    {
        $user = $user ?? auth()->user();
        $albums = $user->albums()->withCount(['photos', 'videos'])->latest()->paginate(12);

        return view('profile.albums', compact('user', 'albums'));
    }

    public function activity(User $user = null)
    {
        $user = $user ?? auth()->user();
        
        $activities = [
            'media_uploads' => $user->media()->with('album')->latest()->limit(10)->get(),
            'event_registrations' => $user->eventRegistrations()->with('event')->latest()->limit(10)->get(),
            'forum_posts' => $user->forumPosts()->with('topic')->latest()->limit(10)->get(),
        ];

        return view('profile.activity', compact('user', 'activities'));
    }

    private function createAvatarThumbnail($path)
    {
        try {
            $image = Image::make(storage_path('app/public/' . $path));
            
            // Create thumbnails directory if it doesn't exist
            $thumbDir = 'avatars/thumbs';
            if (!Storage::disk('public')->exists($thumbDir)) {
                Storage::disk('public')->makeDirectory($thumbDir);
            }

            // Create various sizes
            $sizes = [
                'small' => 64,
                'medium' => 128,
                'large' => 256,
            ];

            foreach ($sizes as $size => $dimension) {
                $thumbnailPath = $thumbDir . '/' . pathinfo($path, PATHINFO_FILENAME) . "_{$size}.jpg";
                $image->fit($dimension, $dimension)
                      ->encode('jpg', 80)
                      ->save(storage_path('app/public/' . $thumbnailPath));
            }
        } catch (\Exception $e) {
            \Log::error('Avatar thumbnail creation failed: ' . $e->getMessage());
        }
    }
}

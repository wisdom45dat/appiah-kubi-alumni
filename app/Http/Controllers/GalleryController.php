<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Media;
use App\Models\MediaLike;
use App\Models\MediaComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $query = Album::with(['creator', 'media']);

        // Privacy filter
        $query->where(function($q) {
            $q->where('privacy', 'public')
              ->orWhere(function($q2) {
                  $q2->where('privacy', 'alumni')
                     ->whereHas('creator', function($q3) {
                         $q3->where('is_verified', true);
                     });
              });
        });

        // Category filter
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Year filter
        if ($request->has('year') && $request->year) {
            $query->where('album_year', $request->year);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        $albums = $query->latest()->paginate(12);

        $categories = Album::distinct()->pluck('category');
        $years = Album::whereNotNull('album_year')->distinct()->pluck('album_year');

        return view('gallery.index', compact('albums', 'categories', 'years'));
    }

    public function showAlbum(Album $album)
    {
        if (!$album->canView(auth()->user())) {
            abort(403, 'You do not have permission to view this album.');
        }

        $media = $album->media()
            ->with(['uploader', 'likes', 'comments.user'])
            ->latest()
            ->paginate(24);

        return view('gallery.album', compact('album', 'media'));
    }

    public function createAlbum()
    {
        $categories = [
            'school_days' => 'School Days',
            'reunions' => 'Reunions',
            'events' => 'Events',
            'sports' => 'Sports',
            'academic' => 'Academic',
            'general' => 'General'
        ];

        $currentYear = date('Y');
        $years = range(1970, $currentYear);
        rsort($years);

        return view('gallery.create-album', compact('categories', 'years'));
    }

    public function storeAlbum(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'privacy' => 'required|in:public,alumni,private',
            'category' => 'required|string',
            'album_year' => 'nullable|digits:4',
        ]);

        $album = Album::create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('gallery.album.upload', $album)
            ->with('success', 'Album created successfully! You can now add photos and videos.');
    }

    public function showUploadForm(Album $album)
    {
        if ($album->created_by !== auth()->id()) {
            abort(403, 'You can only upload to your own albums.');
        }

        return view('gallery.upload', compact('album'));
    }

    public function uploadMedia(Album $album, Request $request)
    {
        if ($album->created_by !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'files.*' => 'required|file|mimes:jpeg,png,jpg,gif,mp4,avi,mov,wmv|max:51200', // 50MB
            'captions' => 'nullable|array',
            'captions.*' => 'nullable|string|max:500',
        ]);

        $uploadedFiles = [];

        foreach ($request->file('files') as $index => $file) {
            $isVideo = Str::startsWith($file->getMimeType(), 'video/');
            $fileType = $isVideo ? 'video' : 'image';

            // Store file
            $path = $file->store("albums/{$album->id}", 'public');

            // Process image if it's an image
            if (!$isVideo) {
                $this->createThumbnail($path);
            }

            // Get file metadata
            $metadata = $isVideo ? null : $this->getImageMetadata($file);

            $media = Media::create([
                'album_id' => $album->id,
                'uploaded_by' => auth()->id(),
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $fileType,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'caption' => $request->captions[$index] ?? null,
                'metadata' => $metadata,
                'privacy' => $album->privacy,
            ]);

            $uploadedFiles[] = $media;
        }

        // Update album counts
        $album->updateCounts();

        return response()->json([
            'success' => true,
            'message' => count($uploadedFiles) . ' files uploaded successfully',
            'media' => $uploadedFiles,
        ]);
    }

    public function showMedia(Media $media)
    {
        if (!$media->album->canView(auth()->user())) {
            abort(403, 'You do not have permission to view this media.');
        }

        $media->incrementViews();
        $media->load(['album', 'uploader', 'likes.user', 'comments.user']);

        // Get previous and next media in album
        $previousMedia = Media::where('album_id', $media->album_id)
            ->where('id', '<', $media->id)
            ->latest('id')
            ->first();

        $nextMedia = Media::where('album_id', $media->album_id)
            ->where('id', '>', $media->id)
            ->oldest('id')
            ->first();

        return view('gallery.media', compact('media', 'previousMedia', 'nextMedia'));
    }

    public function likeMedia(Media $media)
    {
        if (!$media->album->canView(auth()->user())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $like = $media->likes()->firstOrCreate([
            'user_id' => auth()->id(),
        ]);

        if ($like->wasRecentlyCreated) {
            $media->increment('likes_count');
            return response()->json(['liked' => true, 'likes_count' => $media->likes_count]);
        }

        return response()->json(['liked' => false, 'likes_count' => $media->likes_count]);
    }

    public function unlikeMedia(Media $media)
    {
        $deleted = $media->likes()->where('user_id', auth()->id())->delete();

        if ($deleted) {
            $media->decrement('likes_count');
            return response()->json(['liked' => false, 'likes_count' => $media->likes_count]);
        }

        return response()->json(['liked' => true, 'likes_count' => $media->likes_count]);
    }

    public function addComment(Media $media, Request $request)
    {
        if (!$media->album->canView(auth()->user())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'comment' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:media_comments,id',
        ]);

        $comment = MediaComment::create([
            'media_id' => $media->id,
            'user_id' => auth()->id(),
            'parent_id' => $request->parent_id,
            'comment' => $request->comment,
        ]);

        $comment->load('user');

        return response()->json([
            'success' => true,
            'comment' => $comment,
            'html' => view('gallery.partials.comment', compact('comment'))->render(),
        ]);
    }

    private function createThumbnail($path)
    {
        try {
            $image = Image::make(storage_path('app/public/' . $path));
            
            // Create thumbnail directory if it doesn't exist
            $thumbDir = dirname($path) . '/thumbs';
            if (!Storage::disk('public')->exists($thumbDir)) {
                Storage::disk('public')->makeDirectory($thumbDir);
            }

            // Create thumbnail
            $thumbnailPath = $thumbDir . '/' . pathinfo($path, PATHINFO_FILENAME) . '.jpg';
            $image->fit(300, 300)
                  ->encode('jpg', 80)
                  ->save(storage_path('app/public/' . $thumbnailPath));
        } catch (\Exception $e) {
            \Log::error('Thumbnail creation failed: ' . $e->getMessage());
        }
    }

    private function getImageMetadata($file)
    {
        try {
            $image = Image::make($file);
            return [
                'width' => $image->width(),
                'height' => $image->height(),
                'exif' => $image->exif(),
            ];
        } catch (\Exception $e) {
            return null;
        }
    }
}

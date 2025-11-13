<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\EventPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with(['creator', 'registrations']);

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('status')) {
            switch ($request->status) {
                case 'upcoming':
                    $query->upcoming();
                    break;
                case 'past':
                    $query->past();
                    break;
                case 'current':
                    $query->current();
                    break;
            }
        }

        $events = $query->published()->latest()->paginate(12);

        $eventTypes = ['reunion', 'fundraising', 'networking', 'workshop', 'sports', 'cultural'];

        return view('events.index', compact('events', 'eventTypes'));
    }

    public function show(Event $event)
    {
        if (!$event->is_published && !auth()->user()?->hasRole('admin')) {
            abort(404);
        }

        $event->load(['creator', 'registrations.user', 'photos.media']);

        $userRegistration = null;
        if (auth()->check()) {
            $userRegistration = $event->registrations()
                ->where('user_id', auth()->id())
                ->first();
        }

        $registeredUsers = $event->registrations()
            ->with('user')
            ->confirmed()
            ->latest()
            ->get();

        return view('events.show', compact('event', 'userRegistration', 'registeredUsers'));
    }

    public function create()
    {
        $eventTypes = [
            'reunion' => 'Class Reunion',
            'fundraising' => 'Fundraising Event',
            'networking' => 'Networking Event',
            'workshop' => 'Workshop/Seminar',
            'sports' => 'Sports Event',
            'cultural' => 'Cultural Event',
        ];

        return view('events.create', compact('eventTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'venue_name' => 'required|string|max:255',
            'venue_address' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'registration_fee' => 'nullable|numeric|min:0',
            'capacity' => 'nullable|integer|min:1',
            'featured_image' => 'nullable|image|max:2048',
            'registration_fields' => 'nullable|array',
        ]);

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('events', 'public');
        }

        $event = Event::create([
            ...$validated,
            'created_by' => auth()->id(),
            'is_published' => $request->has('publish'),
        ]);

        return redirect()->route('events.show', $event)
            ->with('success', 'Event created successfully!');
    }

    public function register(Event $event, Request $request)
    {
        if ($event->hasUserRegistered(auth()->user())) {
            return redirect()->back()->with('error', 'You are already registered for this event.');
        }

        if ($event->isFull()) {
            return redirect()->back()->with('error', 'This event is full.');
        }

        $validated = $request->validate([
            'guests_count' => 'nullable|integer|min:0|max:5',
            'special_requirements' => 'nullable|string|max:500',
        ]);

        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => auth()->id(),
            'guests_count' => $validated['guests_count'] ?? 0,
            'special_requirements' => $validated['special_requirements'] ?? null,
            'status' => $event->registration_fee > 0 ? 'pending' : 'confirmed',
        ]);

        if ($event->registration_fee > 0) {
            return redirect()->route('events.payment', $event)
                ->with('info', 'Please complete your payment to confirm registration.');
        }

        return redirect()->route('events.show', $event)
            ->with('success', 'Successfully registered for the event!');
    }

    public function cancelRegistration(Event $event)
    {
        $registration = $event->registrations()
            ->where('user_id', auth()->id())
            ->first();

        if (!$registration) {
            return redirect()->back()->with('error', 'You are not registered for this event.');
        }

        $registration->update(['status' => 'cancelled']);

        return redirect()->route('events.show', $event)
            ->with('success', 'Registration cancelled successfully.');
    }

    public function uploadPhotos(Event $event, Request $request)
    {
        if ($event->created_by !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403, 'Only event organizers can upload photos.');
        }

        $request->validate([
            'photos.*' => 'required|image|max:10240', // 10MB
            'captions' => 'nullable|array',
            'captions.*' => 'nullable|string|max:255',
        ]);

        $uploadedPhotos = [];

        foreach ($request->file('photos') as $index => $photo) {
            // Create album for event if it doesn't exist
            $album = \App\Models\Album::firstOrCreate([
                'title' => $event->title . ' Photos',
                'created_by' => auth()->id(),
            ], [
                'description' => 'Photos from ' . $event->title,
                'privacy' => 'public',
                'category' => 'events',
                'album_year' => $event->start_date->year,
            ]);

            // Upload to media
            $path = $photo->store("albums/{$album->id}", 'public');
            
            $media = \App\Models\Media::create([
                'album_id' => $album->id,
                'uploaded_by' => auth()->id(),
                'file_path' => $path,
                'file_name' => $photo->getClientOriginalName(),
                'file_type' => 'image',
                'mime_type' => $photo->getMimeType(),
                'file_size' => $photo->getSize(),
                'caption' => $request->captions[$index] ?? null,
                'privacy' => 'public',
            ]);

            // Link to event
            EventPhoto::create([
                'event_id' => $event->id,
                'media_id' => $media->id,
                'uploaded_by' => auth()->id(),
            ]);

            $uploadedPhotos[] = $media;
        }

        return redirect()->back()
            ->with('success', count($uploadedPhotos) . ' photos uploaded successfully!');
    }
}

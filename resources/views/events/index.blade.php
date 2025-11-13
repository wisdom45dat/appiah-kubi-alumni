@extends('layouts.app')

@section('title', 'Events - Appiah Kubi Alumni')

@section('subtitle', 'Upcoming reunions, workshops, and alumni events')

@section('actions')
<a href="{{ route('events.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
    <i class="fas fa-plus mr-2"></i> Create Event
</a>
@endsection

@section('content')
<!-- Filters -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="flex flex-wrap gap-4">
        <a href="{{ route('events.index') }}" 
           class="px-4 py-2 rounded-md {{ !request('status') ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            All Events
        </a>
        <a href="{{ route('events.index', ['status' => 'upcoming']) }}" 
           class="px-4 py-2 rounded-md {{ request('status') == 'upcoming' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            Upcoming
        </a>
        <a href="{{ route('events.index', ['status' => 'current']) }}" 
           class="px-4 py-2 rounded-md {{ request('status') == 'current' ? 'bg-yellow-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            Ongoing
        </a>
        <a href="{{ route('events.index', ['status' => 'past']) }}" 
           class="px-4 py-2 rounded-md {{ request('status') == 'past' ? 'bg-gray-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            Past Events
        </a>
    </div>
</div>

@if($events->count() > 0)
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($events as $event)
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <div class="aspect-w-16 aspect-h-9 bg-gray-200 rounded-t-lg overflow-hidden">
                    <img src="{{ $event->featured_image_url }}" 
                         alt="{{ $event->title }}" 
                         class="object-cover w-full h-48">
                </div>
                
                <div class="p-6">
                    <div class="flex items-start justify-between mb-2">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <a href="{{ route('events.show', $event) }}" class="hover:text-blue-600">
                                {{ $event->title }}
                            </a>
                        </h3>
                        @if($event->is_featured)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-star mr-1"></i> Featured
                            </span>
                        @endif
                    </div>
                    
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                        {{ Str::limit($event->description, 120) }}
                    </p>
                    
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex items-center">
                            <i class="fas fa-calendar mr-2 text-blue-500"></i>
                            {{ $event->start_date->format('M j, Y g:i A') }}
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-map-marker-alt mr-2 text-red-500"></i>
                            {{ $event->venue_name }}
                        </div>
                        @if($event->registration_fee > 0)
                        <div class="flex items-center">
                            <i class="fas fa-tag mr-2 text-green-500"></i>
                            GHS {{ number_format($event->registration_fee, 2) }}
                        </div>
                        @endif
                    </div>
                    
                    <div class="mt-4 flex items-center justify-between">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                    {{ $event->type == 'reunion' ? 'bg-purple-100 text-purple-800' : 
                                       ($event->type == 'workshop' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst($event->type) }}
                        </span>
                        
                        <div class="text-right">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $event->registration_count }} registered
                            </div>
                            @if($event->isUpcoming())
                                <div class="text-xs text-green-600">Upcoming</div>
                            @elseif($event->isCurrent())
                                <div class="text-xs text-yellow-600">Ongoing</div>
                            @else
                                <div class="text-xs text-gray-600">Past</div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('events.show', $event) }}" 
                           class="block w-full text-center bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $events->links() }}
    </div>
@else
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <i class="fas fa-calendar text-4xl text-gray-300 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No events found</h3>
        <p class="text-gray-600 mb-4">Check back later for upcoming alumni events.</p>
        <a href="{{ route('events.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>
            Create First Event
        </a>
    </div>
@endif
@endsection

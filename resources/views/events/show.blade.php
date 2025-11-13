@extends('layouts.app')

@section('title', $event->title . ' - Appiah Kubi Alumni')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Event Header -->
    <div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
        <div class="aspect-w-16 aspect-h-9 bg-gray-200">
            <img src="{{ $event->featured_image_url }}" 
                 alt="{{ $event->title }}" 
                 class="object-cover w-full h-64">
        </div>
        
        <div class="p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $event->title }}</h1>
                    <p class="text-gray-600 mt-2">{{ $event->description }}</p>
                </div>
                @if($event->is_featured)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        <i class="fas fa-star mr-1"></i> Featured Event
                    </span>
                @endif
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div class="space-y-4">
                    <div class="flex items-center">
                        <i class="fas fa-calendar text-blue-500 w-6"></i>
                        <div class="ml-3">
                            <div class="font-medium">{{ $event->start_date->format('l, F j, Y') }}</div>
                            <div class="text-sm text-gray-600">{{ $event->start_date->format('g:i A') }} 
                                @if($event->end_date)
                                    - {{ $event->end_date->format('g:i A') }}
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <i class="fas fa-map-marker-alt text-red-500 w-6 mt-1"></i>
                        <div class="ml-3">
                            <div class="font-medium">{{ $event->venue_name }}</div>
                            <div class="text-sm text-gray-600">{{ $event->venue_address }}</div>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <i class="fas fa-users text-green-500 w-6"></i>
                        <div class="ml-3">
                            <div class="font-medium">{{ $event->registration_count }} registered</div>
                            @if($event->capacity)
                                <div class="text-sm text-gray-600">Capacity: {{ $event->capacity }} people</div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-center">
                        <i class="fas fa-tag text-purple-500 w-6"></i>
                        <div class="ml-3">
                            <div class="font-medium">
                                @if($event->registration_fee > 0)
                                    GHS {{ number_format($event->registration_fee, 2) }}
                                @else
                                    Free
                                @endif
                            </div>
                            <div class="text-sm text-gray-600">Registration Fee</div>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <i class="fas fa-calendar-check text-orange-500 w-6"></i>
                        <div class="ml-3">
                            <div class="font-medium capitalize">{{ $event->type }}</div>
                            <div class="text-sm text-gray-600">Event Type</div>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <i class="fas fa-user text-gray-500 w-6"></i>
                        <div class="ml-3">
                            <div class="font-medium">{{ $event->creator->name }}</div>
                            <div class="text-sm text-gray-600">Organizer</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Registration Section -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Registration</h2>
        
        @if($event->isUpcoming())
            @if($userRegistration)
                <div class="bg-green-50 border border-green-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-800">You are registered for this event!</h3>
                            <div class="mt-2 text-sm text-green-700">
                                <p>Status: <span class="font-medium capitalize">{{ $userRegistration->status }}</span></p>
                                @if($userRegistration->guests_count > 0)
                                    <p>Guests: {{ $userRegistration->guests_count }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <form method="POST" action="{{ route('events.cancel', $event) }}">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center px-3 py-1 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Cancel Registration
                            </button>
                        </form>
                    </div>
                </div>
            @else
                @if($event->isFull())
                    <div class="bg-red-50 border border-red-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Event Full</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <p>This event has reached its capacity.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <form method="POST" action="{{ route('events.register', $event) }}" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label for="guests_count" class="block text-sm font-medium text-gray-700">Number of Guests</label>
                            <select id="guests_count" name="guests_count" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @for($i = 0; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} guest(s)</option>
                                @endfor
                            </select>
                        </div>
                        
                        <div>
                            <label for="special_requirements" class="block text-sm font-medium text-gray-700">Special Requirements</label>
                            <textarea id="special_requirements" name="special_requirements" rows="3"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                      placeholder="Any dietary restrictions, accessibility needs, etc."></textarea>
                        </div>
                        
                        <button type="submit" 
                                class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Register for Event
                        </button>
                    </form>
                @endif
            @endif
        @else
            <div class="bg-gray-50 border border-gray-200 rounded-md p-4 text-center">
                <p class="text-gray-600">This event has already ended.</p>
            </div>
        @endif
    </div>

    <!-- Registered Alumni -->
    @if($registeredUsers->count() > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Registered Alumni ({{ $registeredUsers->count() }})</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($registeredUsers as $registration)
                    <div class="text-center">
                        <img src="{{ $registration->user->avatar_url }}" 
                             alt="{{ $registration->user->name }}"
                             class="w-12 h-12 rounded-full mx-auto mb-2">
                        <p class="text-sm font-medium text-gray-900">{{ $registration->user->name }}</p>
                        <p class="text-xs text-gray-600">Class of {{ $registration->user->graduation_year }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

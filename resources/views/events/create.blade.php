@extends('layouts.app')

@section('title', 'Create Event - Appiah Kubi Alumni')

@section('subtitle', 'Organize a new alumni event')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('events.store') }}">
            @csrf
            
            <div class="space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Event Title *</label>
                    <input type="text" id="title" name="title" required 
                           value="{{ old('title') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="e.g., Class of 2010 10-Year Reunion">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Event Description *</label>
                    <textarea id="description" name="description" required rows="4"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Describe your event...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Event Type *</label>
                        <select id="type" name="type" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Type</option>
                            <option value="reunion" {{ old('type') == 'reunion' ? 'selected' : '' }}>Class Reunion</option>
                            <option value="fundraising" {{ old('type') == 'fundraising' ? 'selected' : '' }}>Fundraising Event</option>
                            <option value="networking" {{ old('type') == 'networking' ? 'selected' : '' }}>Networking Event</option>
                            <option value="workshop" {{ old('type') == 'workshop' ? 'selected' : '' }}>Workshop/Seminar</option>
                            <option value="sports" {{ old('type') == 'sports' ? 'selected' : '' }}>Sports Event</option>
                            <option value="cultural" {{ old('type') == 'cultural' ? 'selected' : '' }}>Cultural Event</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date & Time *</label>
                        <input type="datetime-local" id="start_date" name="start_date" required 
                               value="{{ old('start_date') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date & Time</label>
                        <input type="datetime-local" id="end_date" name="end_date" 
                               value="{{ old('end_date') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="capacity" class="block text-sm font-medium text-gray-700">Capacity</label>
                        <input type="number" id="capacity" name="capacity" 
                               value="{{ old('capacity') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="Leave empty for unlimited">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="venue_name" class="block text-sm font-medium text-gray-700">Venue Name *</label>
                        <input type="text" id="venue_name" name="venue_name" required 
                               value="{{ old('venue_name') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="e.g., Appiah Kubi JHS Auditorium">
                        @error('venue_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="registration_fee" class="block text-sm font-medium text-gray-700">Registration Fee (GHS)</label>
                        <input type="number" id="registration_fee" name="registration_fee" step="0.01"
                               value="{{ old('registration_fee', 0) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="0.00">
                    </div>
                </div>

                <div>
                    <label for="venue_address" class="block text-sm font-medium text-gray-700">Venue Address *</label>
                    <textarea id="venue_address" name="venue_address" required rows="2"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Full address of the venue">{{ old('venue_address') }}</textarea>
                    @error('venue_address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input id="publish" name="publish" type="checkbox" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                           {{ old('publish') ? 'checked' : '' }}>
                    <label for="publish" class="ml-2 block text-sm text-gray-900">
                        Publish event immediately
                    </label>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('events.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700">
                        Create Event
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

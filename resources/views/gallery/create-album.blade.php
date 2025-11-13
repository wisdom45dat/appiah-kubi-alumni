@extends('layouts.app')

@section('title', 'Create Album - Appiah Kubi Alumni')

@section('subtitle', 'Create a new photo album to share memories')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('gallery.store-album') }}">
            @csrf
            
            <div class="space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Album Title *</label>
                    <input type="text" id="title" name="title" required 
                           value="{{ old('title') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="e.g., Class of 2020 Reunion">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Describe your album...">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700">Category *</label>
                        <select id="category" name="category" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Category</option>
                            <option value="school_days" {{ old('category') == 'school_days' ? 'selected' : '' }}>School Days</option>
                            <option value="reunions" {{ old('category') == 'reunions' ? 'selected' : '' }}>Reunions</option>
                            <option value="events" {{ old('category') == 'events' ? 'selected' : '' }}>Events</option>
                            <option value="sports" {{ old('category') == 'sports' ? 'selected' : '' }}>Sports</option>
                            <option value="academic" {{ old('category') == 'academic' ? 'selected' : '' }}>Academic</option>
                            <option value="general" {{ old('category') == 'general' ? 'selected' : '' }}>General</option>
                        </select>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="album_year" class="block text-sm font-medium text-gray-700">Year</label>
                        <select id="album_year" name="album_year"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Year</option>
                            @foreach($years as $year)
                                <option value="{{ $year }}" {{ old('album_year') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="privacy" class="block text-sm font-medium text-gray-700">Privacy *</label>
                    <select id="privacy" name="privacy" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="public" {{ old('privacy') == 'public' ? 'selected' : '' }}>
                            <i class="fas fa-globe mr-2"></i> Public - Anyone can view
                        </option>
                        <option value="alumni" {{ old('privacy') == 'alumni' ? 'selected' : '' }}>
                            <i class="fas fa-users mr-2"></i> Alumni Only - Only verified alumni can view
                        </option>
                        <option value="private" {{ old('privacy') == 'private' ? 'selected' : '' }}>
                            <i class="fas fa-lock mr-2"></i> Private - Only you can view
                        </option>
                    </select>
                    @error('privacy')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Album Creation</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>After creating the album, you will be able to upload photos and videos.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('gallery.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700">
                        Create Album
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

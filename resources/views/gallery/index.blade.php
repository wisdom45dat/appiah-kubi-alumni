@extends('layouts.app')

@section('title', 'Photo Gallery - Appiah Kubi Alumni')

@section('subtitle', 'Relive memories and share photos from Appiah Kubi JHS')

@section('actions')
<a href="{{ route('gallery.create-album') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
    <i class="fas fa-plus mr-2"></i> Create Album
</a>
@endsection

@section('content')
<!-- Filters -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <form method="GET" action="{{ route('gallery.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
            <select id="category" name="category" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $category)) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Year</label>
            <select id="year" name="year" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">All Years</option>
                @foreach($years as $year)
                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="md:col-span-2 flex items-end">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search albums..." 
                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <button type="submit" class="ml-2 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </form>
</div>

<!-- Albums Grid -->
@if($albums->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($albums as $album)
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <!-- FIXED: Using the correct route name 'public.album' -->
                <a href="{{ route('public.album', $album) }}" class="block">
                    <div class="aspect-w-16 aspect-h-9 bg-gray-200 rounded-t-lg overflow-hidden">
                        <img src="{{ $album->cover_image_url }}" 
                             alt="{{ $album->title }}" 
                             class="object-cover w-full h-48 hover:scale-105 transition duration-300">
                    </div>
                </a>
                
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900 mb-1 truncate">
                        <!-- FIXED: Using the correct route name 'public.album' -->
                        <a href="{{ route('public.album', $album) }}" class="hover:text-blue-600">
                            {{ $album->title }}
                        </a>
                    </h3>
                    
                    <p class="text-sm text-gray-600 mb-2 line-clamp-2">
                        {{ Str::limit($album->description, 80) }}
                    </p>
                    
                    <div class="flex items-center justify-between text-sm text-gray-500">
                        <div class="flex items-center">
                            <img src="{{ $album->creator->avatar_url }}" 
                                 alt="{{ $album->creator->name }}"
                                 class="w-5 h-5 rounded-full mr-2">
                            {{ $album->creator->name }}
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <span class="flex items-center">
                                <i class="fas fa-images mr-1"></i>
                                {{ $album->photo_count }}
                            </span>
                            @if($album->video_count > 0)
                            <span class="flex items-center">
                                <i class="fas fa-video mr-1"></i>
                                {{ $album->video_count }}
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mt-2 flex items-center justify-between">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                    {{ $album->privacy == 'public' ? 'bg-green-100 text-green-800' : 
                                       ($album->privacy == 'alumni' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                            <i class="fas fa-{{ $album->privacy == 'public' ? 'globe' : 
                                              ($album->privacy == 'alumni' ? 'users' : 'lock') }} mr-1"></i>
                            {{ ucfirst($album->privacy) }}
                        </span>
                        
                        <span class="text-xs text-gray-500">
                            {{ $album->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $albums->links() }}
    </div>
@else
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <i class="fas fa-images text-4xl text-gray-300 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No albums found</h3>
        <p class="text-gray-600 mb-4">Be the first to share photos from Appiah Kubi JHS!</p>
        <a href="{{ route('gallery.create-album') }}" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>
            Create Your First Album
        </a>
    </div>
@endif
@endsection
@extends('layouts.app')

@section('title', $album->title . ' - Appiah Kubi Alumni Gallery')

@section('subtitle', $album->description)

@section('actions')
<div class="flex space-x-2">
    <a href="{{ route('gallery.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
        <i class="fas fa-arrow-left mr-2"></i> Back to Gallery
    </a>
    @if(auth()->id() === $album->created_by)
        <a href="{{ route('gallery.album.upload', $album) }}" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
            <i class="fas fa-upload mr-2"></i> Upload Media
        </a>
    @endif
</div>
@endsection

@section('content')
<div class="bg-white rounded-lg shadow mb-6">
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $album->title }}</h1>
                <p class="text-gray-600 mt-1">{{ $album->description }}</p>
                <div class="flex items-center mt-2 space-x-4 text-sm text-gray-500">
                    <span class="flex items-center">
                        <img src="{{ $album->creator->avatar_url }}" 
                             alt="{{ $album->creator->name }}"
                             class="w-5 h-5 rounded-full mr-2">
                        By {{ $album->creator->name }}
                    </span>
                    <span>{{ $album->created_at->format('F j, Y') }}</span>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                {{ $album->privacy == 'public' ? 'bg-green-100 text-green-800' : 
                                   ($album->privacy == 'alumni' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                        <i class="fas fa-{{ $album->privacy == 'public' ? 'globe' : 
                                          ($album->privacy == 'alumni' ? 'users' : 'lock') }} mr-1"></i>
                        {{ ucfirst($album->privacy) }}
                    </span>
                </div>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold text-blue-600">{{ $album->media->count() }}</div>
                <div class="text-sm text-gray-600">Total Items</div>
            </div>
        </div>
    </div>
</div>

@if($media->count() > 0)
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
        @foreach($media as $item)
            <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                <a href="{{ route('gallery.media', $item) }}" class="block">
                    <div class="aspect-w-1 aspect-h-1 bg-gray-200 rounded-t-lg overflow-hidden">
                        <img src="{{ $item->thumbnail_url }}" 
                             alt="{{ $item->caption }}" 
                             class="object-cover w-full h-48 hover:scale-105 transition duration-300">
                    </div>
                </a>
                
                <div class="p-3">
                    @if($item->caption)
                        <p class="text-sm text-gray-700 mb-2 line-clamp-2">{{ $item->caption }}</p>
                    @endif
                    
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span>{{ $item->file_type == 'image' ? 'Photo' : 'Video' }}</span>
                        <span>{{ $item->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $media->links() }}
    </div>
@else
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <i class="fas fa-images text-4xl text-gray-300 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No media in this album yet</h3>
        <p class="text-gray-600 mb-4">This album doesn''t contain any photos or videos.</p>
        @if(auth()->id() === $album->created_by)
            <a href="{{ route('gallery.album.upload', $album) }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                <i class="fas fa-upload mr-2"></i>
                Upload Media
            </a>
        @endif
    </div>
@endif
@endsection

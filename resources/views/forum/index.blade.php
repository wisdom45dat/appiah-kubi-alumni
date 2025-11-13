@extends('layouts.app')

@section('title', 'Forum - Appiah Kubi Alumni')

@section('subtitle', 'Connect, discuss, and share with fellow alumni')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    <!-- Main Content -->
    <div class="lg:col-span-3 space-y-8">
        <!-- Forums List -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Discussion Forums</h2>
            </div>
            
            <div class="divide-y divide-gray-200">
                @foreach($forums as $forum)
                    <div class="p-6 hover:bg-gray-50 transition duration-150">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                    <a href="{{ route('forum.forum', $forum) }}" class="hover:text-blue-600">
                                        {{ $forum->name }}
                                    </a>
                                </h3>
                                <p class="text-gray-600 mb-3">{{ $forum->description }}</p>
                                
                                <div class="flex items-center text-sm text-gray-500">
                                    <span class="mr-4">
                                        <i class="fas fa-comments mr-1"></i>
                                        {{ $forum->topics_count }} topics
                                    </span>
                                    <span>
                                        <i class="fas fa-comment-dots mr-1"></i>
                                        {{ $forum->posts_count }} posts
                                    </span>
                                </div>
                            </div>
                            
                            <div class="text-right ml-6">
                                @if($forum->latest_topic)
                                    <div class="text-sm">
                                        <a href="{{ route('forum.topic', $forum->latest_topic) }}" 
                                           class="font-medium text-gray-900 hover:text-blue-600 line-clamp-1">
                                            {{ $forum->latest_topic->title }}
                                        </a>
                                        <div class="text-gray-500 mt-1">
                                            by {{ $forum->latest_topic->user->name }}
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            {{ $forum->latest_topic->last_reply_at->diffForHumans() }}
                                        </div>
                                    </div>
                                @else
                                    <div class="text-sm text-gray-500">No topics yet</div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Topics -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800">Recent Discussions</h2>
                <a href="{{ route('forum.search') }}" class="text-blue-600 hover:text-blue-500 text-sm">View All</a>
            </div>
            
            <div class="divide-y divide-gray-200">
                @foreach($recentTopics as $topic)
                    <div class="p-6 hover:bg-gray-50 transition duration-150">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                    <a href="{{ route('forum.topic', $topic) }}" class="hover:text-blue-600">
                                        {{ $topic->title }}
                                    </a>
                                </h3>
                                
                                <div class="flex items-center text-sm text-gray-500 mb-2">
                                    <span class="mr-4">
                                        <i class="fas fa-user mr-1"></i>
                                        {{ $topic->user->name }}
                                    </span>
                                    <span class="mr-4">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $topic->created_at->diffForHumans() }}
                                    </span>
                                    <span class="mr-4">
                                        <i class="fas fa-comment mr-1"></i>
                                        {{ $topic->replies_count }} replies
                                    </span>
                                    <span>
                                        <i class="fas fa-eye mr-1"></i>
                                        {{ $topic->views_count }} views
                                    </span>
                                </div>
                                
                                <p class="text-gray-700 line-clamp-2">
                                    {{ Str::limit(strip_tags($topic->content), 150) }}
                                </p>
                            </div>
                            
                            <div class="text-right ml-6">
                                <div class="text-sm text-gray-500">
                                    in <a href="{{ route('forum.forum', $topic->forum) }}" 
                                          class="text-blue-600 hover:text-blue-500">
                                        {{ $topic->forum->name }}
                                    </a>
                                </div>
                                @if($topic->last_reply_by)
                                    <div class="text-xs text-gray-400 mt-1">
                                        Last reply by {{ $topic->last_reply_by->name }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Popular Topics -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Popular Topics</h3>
            </div>
            <div class="p-6">
                @if($popularTopics->count() > 0)
                    <div class="space-y-4">
                        @foreach($popularTopics as $topic)
                            <div>
                                <a href="{{ route('forum.topic', $topic) }}" 
                                   class="font-medium text-gray-900 hover:text-blue-600 line-clamp-2">
                                    {{ $topic->title }}
                                </a>
                                <div class="flex items-center text-xs text-gray-500 mt-1">
                                    <span class="mr-3">{{ $topic->replies_count }} replies</span>
                                    <span>{{ $topic->views_count }} views</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">No popular topics yet.</p>
                @endif
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Forum Stats</h3>
            </div>
            <div class="p-6 space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Topics</span>
                    <span class="font-semibold">{{ $forums->sum('topics_count') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Posts</span>
                    <span class="font-semibold">{{ $forums->sum('posts_count') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Active Forums</span>
                    <span class="font-semibold">{{ $forums->count() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

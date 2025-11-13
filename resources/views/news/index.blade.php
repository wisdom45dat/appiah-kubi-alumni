@extends('layouts.app')

@section('title', 'News - Appiah Kubi Alumni')

@section('subtitle', 'Latest updates and stories from Appiah Kubi JHS')

@section('actions')
<a href="{{ route('news.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
    <i class="fas fa-plus mr-2"></i> Write Article
</a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    <!-- Main Content -->
    <div class="lg:col-span-3 space-y-8">
        <!-- Featured Articles -->
        @if($featuredArticles->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($featuredArticles as $article)
                    <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                        <div class="aspect-w-16 aspect-h-9 bg-gray-200 rounded-t-lg overflow-hidden">
                            <img src="{{ $article->featured_image_url }}" 
                                 alt="{{ $article->title }}" 
                                 class="object-cover w-full h-48">
                        </div>
                        
                        <div class="p-6">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mb-3">
                                <i class="fas fa-star mr-1"></i> Featured
                            </span>
                            
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">
                                <a href="{{ route('news.show', $article) }}" class="hover:text-blue-600">
                                    {{ $article->title }}
                                </a>
                            </h3>
                            
                            <p class="text-gray-600 mb-4 line-clamp-2">
                                {{ $article->excerpt ?? Str::limit(strip_tags($article->content), 120) }}
                            </p>
                            
                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <span>{{ $article->reading_time }}</span>
                                <span>{{ $article->published_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- All Articles -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Latest News</h2>
            </div>
            
            <div class="divide-y divide-gray-200">
                @foreach($articles as $article)
                    <article class="p-6 hover:bg-gray-50 transition duration-150">
                        <div class="flex items-start space-x-6">
                            @if($article->featured_image)
                                <div class="flex-shrink-0">
                                    <img src="{{ $article->featured_image_url }}" 
                                         alt="{{ $article->title }}"
                                         class="w-32 h-24 object-cover rounded-lg">
                                </div>
                            @endif
                            
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                    <a href="{{ route('news.show', $article) }}" class="hover:text-blue-600">
                                        {{ $article->title }}
                                    </a>
                                </h3>
                                
                                <p class="text-gray-600 mb-3">
                                    {{ $article->excerpt ?? Str::limit(strip_tags($article->content), 200) }}
                                </p>
                                
                                <div class="flex items-center justify-between text-sm text-gray-500">
                                    <div class="flex items-center space-x-4">
                                        <span class="flex items-center">
                                            <img src="{{ $article->author->avatar_url }}" 
                                                 alt="{{ $article->author->name }}"
                                                 class="w-5 h-5 rounded-full mr-2">
                                            {{ $article->author_name }}
                                        </span>
                                        <span>{{ $article->reading_time }}</span>
                                        <span>{{ $article->published_at->format('M j, Y') }}</span>
                                    </div>
                                    
                                    <div class="flex items-center space-x-2">
                                        <span class="flex items-center">
                                            <i class="fas fa-eye mr-1"></i>
                                            {{ $article->views_count }}
                                        </span>
                                        @if($article->is_featured)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Featured
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="p-6">
                {{ $articles->links() }}
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Popular Articles -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Popular News</h3>
            </div>
            <div class="p-6">
                @if($popularArticles->count() > 0)
                    <div class="space-y-4">
                        @foreach($popularArticles as $article)
                            <div>
                                <a href="{{ route('news.show', $article) }}" 
                                   class="font-medium text-gray-900 hover:text-blue-600 line-clamp-2">
                                    {{ $article->title }}
                                </a>
                                <div class="flex items-center text-xs text-gray-500 mt-1">
                                    <span class="mr-3">{{ $article->views_count }} views</span>
                                    <span>{{ $article->published_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">No popular articles yet.</p>
                @endif
            </div>
        </div>

        <!-- Newsletter Signup -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-2">Stay Updated</h3>
            <p class="text-blue-700 text-sm mb-4">Get the latest news from Appiah Kubi JHS delivered to your inbox.</p>
            <form class="space-y-3">
                <input type="email" placeholder="Your email address" 
                       class="w-full px-3 py-2 border border-blue-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <button type="submit" 
                        class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">
                    Subscribe
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

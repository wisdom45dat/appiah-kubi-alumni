@extends('layouts.app')

@section('title', 'Job Board - Appiah Kubi Alumni')

@section('subtitle', 'Career opportunities from fellow alumni')

@section('actions')
<a href="{{ route('jobs.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
    <i class="fas fa-plus mr-2"></i> Post Job
</a>
@endsection

@section('content')
<!-- Featured Jobs -->
@if($featuredJobs->count() > 0)
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Featured Jobs</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($featuredJobs as $job)
                <div class="bg-white border-l-4 border-blue-500 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-3">
                            <h3 class="text-xl font-semibold text-gray-900">
                                <a href="{{ route('jobs.show', $job) }}" class="hover:text-blue-600">
                                    {{ $job->title }}
                                </a>
                            </h3>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-star mr-1"></i> Featured
                            </span>
                        </div>
                        
                        <p class="text-gray-600 mb-4 line-clamp-2">
                            {{ Str::limit($job->description, 120) }}
                        </p>
                        
                        <div class="space-y-2 text-sm text-gray-600 mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-building mr-2 text-blue-500"></i>
                                {{ $job->company_name }}
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-map-marker-alt mr-2 text-red-500"></i>
                                {{ $job->location }}
                                @if($job->is_remote)
                                    <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Remote
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-briefcase mr-2 text-purple-500"></i>
                                {{ ucfirst(str_replace('_', ' ', $job->employment_type)) }}
                            </div>
                            @if($job->salary_min || $job->salary_max)
                            <div class="flex items-center">
                                <i class="fas fa-money-bill-wave mr-2 text-green-500"></i>
                                {{ $job->salary_range }}
                            </div>
                            @endif
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">
                                Posted {{ $job->created_at->diffForHumans() }}
                            </span>
                            <a href="{{ route('jobs.show', $job) }}" 
                               class="text-blue-600 hover:text-blue-500 font-medium">
                                View Details →
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<!-- All Jobs -->
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-800">All Job Opportunities</h2>
    </div>
    
    <!-- Filters -->
    <div class="p-6 border-b border-gray-200 bg-gray-50">
        <form method="GET" action="{{ route('jobs.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="employment_type" class="block text-sm font-medium text-gray-700 mb-1">Employment Type</label>
                <select id="employment_type" name="employment_type" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Types</option>
                    @foreach($employmentTypes as $value => $label)
                        <option value="{{ $value }}" {{ request('employment_type') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                <input type="text" id="location" name="location" value="{{ request('location') }}" 
                       placeholder="City or Country" 
                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="md:col-span-2 flex items-end">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search jobs, companies, or keywords..." 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <button type="submit" class="ml-2 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>
    
    <div class="p-6">
        @if($jobs->count() > 0)
            <div class="space-y-4">
                @foreach($jobs as $job)
                    <div class="border border-gray-200 rounded-lg hover:shadow-md transition-shadow p-6">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                    <a href="{{ route('jobs.show', $job) }}" class="hover:text-blue-600">
                                        {{ $job->title }}
                                    </a>
                                </h3>
                                <p class="text-gray-600 mb-2">{{ $job->company_name }}</p>
                                
                                <div class="flex flex-wrap gap-2 text-sm text-gray-600 mb-3">
                                    <span class="inline-flex items-center">
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        {{ $job->location }}
                                    </span>
                                    <span class="inline-flex items-center">
                                        <i class="fas fa-briefcase mr-1"></i>
                                        {{ ucfirst(str_replace('_', ' ', $job->employment_type)) }}
                                    </span>
                                    @if($job->is_remote)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-wifi mr-1"></i> Remote
                                        </span>
                                    @endif
                                    @if($job->salary_range != 'Negotiable')
                                        <span class="inline-flex items-center text-green-600 font-medium">
                                            <i class="fas fa-money-bill-wave mr-1"></i>
                                            {{ $job->salary_range }}
                                        </span>
                                    @endif
                                </div>
                                
                                <p class="text-gray-700 line-clamp-2">
                                    {{ Str::limit(strip_tags($job->description), 150) }}
                                </p>
                            </div>
                            
                            <div class="text-right ml-4">
                                <div class="text-sm text-gray-500 mb-2">
                                    {{ $job->created_at->diffForHumans() }}
                                </div>
                                @if($job->application_deadline && !$job->is_expired)
                                    <div class="text-sm text-orange-600">
                                        Apply by {{ $job->application_deadline->format('M j, Y') }}
                                    </div>
                                @elseif($job->is_expired)
                                    <div class="text-sm text-red-600">
                                        Expired
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between mt-4">
                            <div class="flex items-center text-sm text-gray-500">
                                <img src="{{ $job->poster->avatar_url }}" 
                                     alt="{{ $job->poster->name }}"
                                     class="w-6 h-6 rounded-full mr-2">
                                Posted by {{ $job->poster->name }}
                            </div>
                            
                            <a href="{{ route('jobs.show', $job) }}" 
                               class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
                                View Job
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-6">
                {{ $jobs->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-briefcase text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No jobs found</h3>
                <p class="text-gray-600 mb-4">Try adjusting your search criteria or check back later.</p>
                <a href="{{ route('jobs.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>
                    Post First Job
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

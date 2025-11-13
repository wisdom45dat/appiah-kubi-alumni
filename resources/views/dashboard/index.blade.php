@extends('layouts.app')

@section('title', 'Alumni Dashboard')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-8">
        <!-- Welcome Section -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}! 👋</h2>
                    <p class="text-gray-600 mt-2">Good to see you again. Here\'s what\'s happening with our alumni community.</p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">Profile Completion</div>
                    <div class="flex items-center">
                        <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ auth()->user()->profile_completion }}%"></div>
                        </div>
                        <span class="text-sm font-medium">{{ auth()->user()->profile_completion }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['total_alumni'] }}</div>
                <div class="text-sm text-gray-600">Total Alumni</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-2xl font-bold text-green-600">{{ $stats['upcoming_events']->count() }}</div>
                <div class="text-sm text-gray-600">Upcoming Events</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-2xl font-bold text-purple-600">{{ $stats['active_campaigns']->count() }}</div>
                <div class="text-sm text-gray-600">Active Campaigns</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-2xl font-bold text-orange-600">{{ $stats['forum_activity']->count() }}</div>
                <div class="text-sm text-gray-600">Recent Discussions</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
            </div>
            <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('directory') }}" class="text-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-users text-blue-600 text-2xl mb-2"></i>
                    <div class="font-medium">Alumni Directory</div>
                </a>
                <a href="{{ route('gallery.index') }}" class="text-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-images text-green-600 text-2xl mb-2"></i>
                    <div class="font-medium">Photo Gallery</div>
                </a>
                <a href="{{ route('events.index') }}" class="text-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-calendar text-purple-600 text-2xl mb-2"></i>
                    <div class="font-medium">Events</div>
                </a>
                <a href="{{ route('jobs.index') }}" class="text-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-briefcase text-orange-600 text-2xl mb-2"></i>
                    <div class="font-medium">Job Board</div>
                </a>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Profile Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-center">
                <img class="h-20 w-20 rounded-full mx-auto mb-4" src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}">
                <h3 class="text-lg font-medium text-gray-900">{{ auth()->user()->name }}</h3>
                <p class="text-sm text-gray-600">{{ auth()->user()->current_profession ?? 'Alumni' }}</p>
                <p class="text-sm text-gray-500">Class of {{ auth()->user()->graduation_year }}</p>
                
                <div class="mt-4 flex justify-center space-x-2">
                    <a href="{{ route('profile.edit') }}" 
                       class="inline-flex items-center px-3 py-1 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Edit Profile
                    </a>
                    <a href="{{ route('profile.show') }}" 
                       class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        View Profile
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
            </div>
            <div class="p-6">
                <div class="text-center text-gray-500">
                    <i class="fas fa-clock text-2xl mb-2"></i>
                    <p>No recent activity</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

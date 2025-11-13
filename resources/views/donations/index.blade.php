@extends('layouts.app')

@section('title', 'Donations - Appiah Kubi Alumni')

@section('subtitle', 'Support Appiah Kubi JHS development projects')

@section('content')
<!-- Statistics -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6 text-center">
        <div class="text-3xl font-bold text-green-600">GHS {{ number_format($totalRaised, 2) }}</div>
        <div class="text-sm text-gray-600">Total Raised</div>
    </div>
    <div class="bg-white rounded-lg shadow p-6 text-center">
        <div class="text-3xl font-bold text-blue-600">{{ $totalDonors }}</div>
        <div class="text-sm text-gray-600">Total Donors</div>
    </div>
    <div class="bg-white rounded-lg shadow p-6 text-center">
        <div class="text-3xl font-bold text-purple-600">{{ $campaigns->count() }}</div>
        <div class="text-sm text-gray-600">Active Campaigns</div>
    </div>
    <div class="bg-white rounded-lg shadow p-6 text-center">
        <div class="text-3xl font-bold text-orange-600">{{ $featuredCampaigns->count() }}</div>
        <div class="text-sm text-gray-600">Featured</div>
    </div>
</div>

<!-- Featured Campaigns -->
@if($featuredCampaigns->count() > 0)
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Featured Campaigns</h2>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @foreach($featuredCampaigns as $campaign)
                <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                    <div class="aspect-w-16 aspect-h-9 bg-gray-200 rounded-t-lg overflow-hidden">
                        <img src="{{ $campaign->featured_image_url }}" 
                             alt="{{ $campaign->title }}" 
                             class="object-cover w-full h-48">
                    </div>
                    
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                            <a href="{{ route('donations.campaign', $campaign) }}" class="hover:text-blue-600">
                                {{ $campaign->title }}
                            </a>
                        </h3>
                        
                        <p class="text-gray-600 mb-4 line-clamp-2">
                            {{ Str::limit($campaign->description, 120) }}
                        </p>
                        
                        <!-- Progress Bar -->
                        <div class="mb-4">
                            <div class="flex justify-between text-sm text-gray-600 mb-1">
                                <span>GHS {{ number_format($campaign->current_amount, 2) }} raised</span>
                                <span>Goal: GHS {{ number_format($campaign->target_amount, 2) }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full transition-all duration-300" 
                                     style="width: {{ $campaign->progress_percentage }}%"></div>
                            </div>
                            <div class="text-right text-sm text-gray-600 mt-1">
                                {{ $campaign->progress_percentage }}% funded
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between text-sm text-gray-600">
                            <span>
                                <i class="fas fa-users mr-1"></i>
                                {{ $campaign->donations->count() }} donors
                            </span>
                            @if($campaign->days_remaining !== null)
                                <span>
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $campaign->days_remaining }} days left
                                </span>
                            @endif
                        </div>
                        
                        <div class="mt-4">
                            <a href="{{ route('donations.campaign', $campaign) }}" 
                               class="block w-full text-center bg-green-600 text-white py-2 rounded-md hover:bg-green-700">
                                Donate Now
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<!-- All Campaigns -->
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-800">All Campaigns</h2>
    </div>
    
    <div class="p-6">
        @if($campaigns->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($campaigns as $campaign)
                    <div class="border border-gray-200 rounded-lg hover:shadow-md transition-shadow">
                        <div class="aspect-w-16 aspect-h-9 bg-gray-200 rounded-t-lg overflow-hidden">
                            <img src="{{ $campaign->featured_image_url }}" 
                                 alt="{{ $campaign->title }}" 
                                 class="object-cover w-full h-40">
                        </div>
                        
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 mb-2">
                                <a href="{{ route('donations.campaign', $campaign) }}" class="hover:text-blue-600">
                                    {{ $campaign->title }}
                                </a>
                            </h3>
                            
                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                {{ Str::limit($campaign->description, 80) }}
                            </p>
                            
                            <div class="mb-3">
                                <div class="flex justify-between text-xs text-gray-600 mb-1">
                                    <span>GHS {{ number_format($campaign->current_amount, 2) }}</span>
                                    <span>{{ $campaign->progress_percentage }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1">
                                    <div class="bg-green-500 h-1 rounded-full" 
                                         style="width: {{ $campaign->progress_percentage }}%"></div>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between text-xs text-gray-600">
                                <span>{{ $campaign->donations->count() }} donors</span>
                                <a href="{{ route('donations.campaign', $campaign) }}" 
                                   class="text-blue-600 hover:text-blue-500 font-medium">
                                    Donate →
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-6">
                {{ $campaigns->links() }}
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-hand-holding-heart text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No active campaigns</h3>
                <p class="text-gray-600">Check back later for fundraising campaigns.</p>
            </div>
        @endif
    </div>
</div>

<!-- Recent Donations -->
@if($recentDonations->count() > 0)
    <div class="mt-8 bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Recent Donations</h2>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                @foreach($recentDonations as $donation)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <div class="flex items-center">
                            <div class="bg-green-100 text-green-800 rounded-full p-2 mr-3">
                                <i class="fas fa-donate"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $donation->display_name }}</p>
                                <p class="text-sm text-gray-600">to {{ $donation->campaign->title }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-green-600">GHS {{ number_format($donation->amount, 2) }}</p>
                            <p class="text-xs text-gray-500">{{ $donation->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif
@endsection

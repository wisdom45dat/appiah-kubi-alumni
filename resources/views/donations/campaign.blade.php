@extends('layouts.app')

@section('title', $campaign->title . ' - Appiah Kubi Alumni')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Campaign Header -->
    <div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
        <div class="aspect-w-16 aspect-h-9 bg-gray-200">
            <img src="{{ $campaign->featured_image_url }}" 
                 alt="{{ $campaign->title }}" 
                 class="object-cover w-full h-64">
        </div>
        
        <div class="p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $campaign->title }}</h1>
                    <p class="text-gray-600 mt-2">{{ $campaign->description }}</p>
                </div>
                @if($campaign->is_featured)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 ml-4">
                        <i class="fas fa-star mr-1"></i> Featured
                    </span>
                @endif
            </div>
            
            <!-- Progress Section -->
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <div class="flex justify-between items-center mb-2">
                    <div class="text-2xl font-bold text-green-600">
                        GHS {{ number_format($campaign->current_amount, 2) }}
                    </div>
                    <div class="text-lg font-semibold text-gray-700">
                        {{ $campaign->progress_percentage }}% funded
                    </div>
                </div>
                
                <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                    <div class="bg-green-600 h-3 rounded-full transition-all duration-300" 
                         style="width: {{ $campaign->progress_percentage }}%"></div>
                </div>
                
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Raised of GHS {{ number_format($campaign->target_amount, 2) }} goal</span>
                    <span>{{ $campaign->donations->count() }} donations</span>
                </div>
                
                @if($campaign->days_remaining !== null)
                    <div class="mt-2 text-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-clock mr-1"></i>
                            {{ $campaign->days_remaining }} days remaining
                        </span>
                    </div>
                @endif
            </div>
            
            <!-- Donation Button -->
            @if($campaign->isActive())
                <a href="{{ route('donations.create', $campaign) }}" 
                   class="block w-full text-center bg-green-600 text-white py-3 px-6 rounded-lg text-lg font-semibold hover:bg-green-700 transition duration-300">
                    <i class="fas fa-donate mr-2"></i>
                    Donate Now
                </a>
            @else
                <div class="text-center bg-gray-100 py-3 px-6 rounded-lg">
                    <p class="text-gray-600 font-semibold">This campaign has ended</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Campaign Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Campaign Story -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">About This Campaign</h2>
                <div class="prose max-w-none">
                    <p>{{ $campaign->description }}</p>
                    
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div class="flex items-center">
                            <i class="fas fa-calendar text-blue-500 w-6"></i>
                            <div class="ml-3">
                                <div class="font-medium">Start Date</div>
                                <div class="text-gray-600">{{ $campaign->start_date->format('M j, Y') }}</div>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <i class="fas fa-flag text-green-500 w-6"></i>
                            <div class="ml-3">
                                <div class="font-medium">Campaign Type</div>
                                <div class="text-gray-600 capitalize">{{ $campaign->type }}</div>
                            </div>
                        </div>
                        
                        @if($campaign->end_date)
                        <div class="flex items-center">
                            <i class="fas fa-hourglass-end text-orange-500 w-6"></i>
                            <div class="ml-3">
                                <div class="font-medium">End Date</div>
                                <div class="text-gray-600">{{ $campaign->end_date->format('M j, Y') }}</div>
                            </div>
                        </div>
                        @endif
                        
                        <div class="flex items-center">
                            <i class="fas fa-user text-purple-500 w-6"></i>
                            <div class="ml-3">
                                <div class="font-medium">Organizer</div>
                                <div class="text-gray-600">{{ $campaign->creator->name }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Donations -->
            @if($donations->count() > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Recent Donations</h2>
                    <div class="space-y-3">
                        @foreach($donations as $donation)
                            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                                <div class="flex items-center">
                                    <div class="bg-green-100 text-green-800 rounded-full p-2 mr-3">
                                        <i class="fas fa-heart"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $donation->display_name }}</p>
                                        <p class="text-sm text-gray-600">{{ $donation->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-green-600">GHS {{ number_format($donation->amount, 2) }}</p>
                                    @if($donation->message)
                                        <p class="text-xs text-gray-500 italic">"{{ Str::limit($donation->message, 50) }}"</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($donations->hasPages())
                        <div class="mt-4">
                            {{ $donations->links() }}
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Top Donors -->
            @if($topDonors->count() > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Top Donors</h3>
                    <div class="space-y-3">
                        @foreach($topDonors as $donor)
                            <div class="flex items-center justify-between">
                                <span class="font-medium text-gray-900">{{ $donor->donor_name }}</span>
                                <span class="text-green-600 font-semibold">GHS {{ number_format($donor->total_donated, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Share Campaign -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Share This Campaign</h3>
                <div class="flex space-x-2">
                    <button class="flex-1 bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                        <i class="fab fa-facebook-f"></i>
                    </button>
                    <button class="flex-1 bg-blue-400 text-white py-2 rounded hover:bg-blue-500">
                        <i class="fab fa-twitter"></i>
                    </button>
                    <button class="flex-1 bg-green-500 text-white py-2 rounded hover:bg-green-600">
                        <i class="fab fa-whatsapp"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

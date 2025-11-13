@extends('layouts.app')

@section('title', 'Alumni Directory - Appiah Kubi Alumni')

@section('subtitle', 'Connect with your fellow Appiah Kubi JHS alumni')

@section('actions')
<a href="{{ route('dashboard') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
    <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
</a>
@endsection

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-800">Alumni Directory</h2>
        <p class="text-gray-600 mt-1">Find and connect with your batchmates and other alumni</p>
    </div>

    <!-- Filters -->
    <div class="p-6 border-b border-gray-200 bg-gray-50">
        <form method="GET" action="{{ route('directory') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="graduation_year" class="block text-sm font-medium text-gray-700 mb-1">Graduation Year</label>
                <select id="graduation_year" name="graduation_year" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Years</option>
                    @foreach($graduationYears as $year)
                        <option value="{{ $year }}" {{ request('graduation_year') == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="house" class="block text-sm font-medium text-gray-700 mb-1">House</label>
                <select id="house" name="house" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Houses</option>
                    @foreach($houses as $house)
                        @if($house)
                            <option value="{{ $house }}" {{ request('house') == $house ? 'selected' : '' }}>
                                {{ $house }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div>
                <label for="profession" class="block text-sm font-medium text-gray-700 mb-1">Profession</label>
                <input type="text" id="profession" name="profession" value="{{ request('profession') }}" 
                       placeholder="e.g. Doctor, Engineer" 
                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    <i class="fas fa-search mr-2"></i> Search
                </button>
            </div>
        </form>

        <!-- Search Form -->
        <form method="GET" action="{{ route('directory') }}" class="mt-4">
            <div class="flex">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search by name, company, or profession..." 
                       class="flex-1 border-gray-300 rounded-l-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-r-md hover:bg-blue-700">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Alumni Grid -->
    <div class="p-6">
        @if($alumni->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($alumni as $user)
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                        <div class="p-4 text-center">
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" 
                                 class="w-20 h-20 rounded-full mx-auto mb-3 object-cover">
                            <h3 class="font-semibold text-gray-900">{{ $user->name }}</h3>
                            <p class="text-sm text-gray-600">Class of {{ $user->graduation_year }}</p>
                            
                            @if($user->current_profession)
                                <p class="text-sm text-gray-700 mt-1">
                                    <i class="fas fa-briefcase mr-1"></i>
                                    {{ $user->current_profession }}
                                </p>
                            @endif

                            @if($user->current_company)
                                <p class="text-sm text-gray-600">
                                    at {{ $user->current_company }}
                                </p>
                            @endif

                            @if($user->house)
                                <p class="text-sm text-blue-600 mt-1">
                                    <i class="fas fa-home mr-1"></i>
                                    {{ $user->house }} House
                                </p>
                            @endif

                            <div class="mt-4">
                                <a href="{{ route('profile.user', $user) }}" 
                                   class="inline-block bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
                                    View Profile
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $alumni->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900">No alumni found</h3>
                <p class="text-gray-600 mt-2">Try adjusting your search criteria</p>
                <a href="{{ route('directory') }}" class="inline-block mt-4 text-blue-600 hover:text-blue-500">
                    Clear filters
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Statistics -->
<div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <div class="text-2xl font-bold text-blue-600">{{ $alumni->total() }}</div>
        <div class="text-sm text-gray-600">Total Alumni</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <div class="text-2xl font-bold text-green-600">{{ $graduationYears->count() }}</div>
        <div class="text-sm text-gray-600">Graduation Years</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <div class="text-2xl font-bold text-purple-600">{{ $houses->count() }}</div>
        <div class="text-sm text-gray-600">Houses Represented</div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Coming Soon - Appiah Kubi Alumni')

@section('content')
<div class="bg-white rounded-lg shadow p-8 text-center">
    <i class="fas fa-tools text-4xl text-gray-300 mb-4"></i>
    <h2 class="text-2xl font-bold text-gray-900 mb-2">Feature Under Development</h2>
    <p class="text-gray-600 mb-4">This page is currently being developed and will be available soon.</p>
    <a href="{{ url('/') }}" class="inline-block mt-4 text-blue-600 hover:text-blue-500">
        Return to Dashboard
    </a>
</div>
@endsection

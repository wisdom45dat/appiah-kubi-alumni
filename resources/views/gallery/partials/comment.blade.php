@extends('layouts.app')

@section('title', 'Coming Soon - Appiah Kubi Alumni')

@section('content')
<div class="bg-white rounded-lg shadow p-8 text-center">
    <i class="fas fa-tools text-4xl text-gray-300 mb-4"></i>
    <h2 class="text-2xl font-bold text-gray-900 mb-2">Coming Soon</h2>
    <p class="text-gray-600">This gallery feature is under development.</p>
    <a href="{{ route('gallery.index') }}" class="inline-block mt-4 text-blue-600 hover:text-blue-500">
        Return to Gallery
    </a>
</div>
@endsection

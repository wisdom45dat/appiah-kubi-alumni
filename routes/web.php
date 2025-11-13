<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\JobsController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/directory', [DashboardController::class, 'alumniDirectory'])->name('directory');
    Route::get('/search/alumni', [DashboardController::class, 'searchAlumni'])->name('search.alumni');

    // Profile Routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('update-password');
        Route::get('/albums', [ProfileController::class, 'albums'])->name('albums');
        Route::get('/activity', [ProfileController::class, 'activity'])->name('activity');
        Route::get('/{user}', [ProfileController::class, 'show'])->name('user');
    });

    // Gallery Routes
    Route::prefix('gallery')->name('gallery.')->group(function () {
        Route::get('/', [GalleryController::class, 'index'])->name('index');
        Route::get('/albums/create', [GalleryController::class, 'createAlbum'])->name('create-album');
        Route::post('/albums', [GalleryController::class, 'storeAlbum'])->name('store-album');
        Route::get('/albums/{album}/upload', [GalleryController::class, 'showUploadForm'])->name('album.upload');
        Route::post('/albums/{album}/upload', [GalleryController::class, 'uploadMedia'])->name('album.store-media');
        Route::get('/albums/{album}', [GalleryController::class, 'showAlbum'])->name('album');
        Route::get('/media/{media}', [GalleryController::class, 'showMedia'])->name('media');
        Route::post('/media/{media}/like', [GalleryController::class, 'likeMedia'])->name('media.like');
        Route::post('/media/{media}/unlike', [GalleryController::class, 'unlikeMedia'])->name('media.unlike');
        Route::post('/media/{media}/comment', [GalleryController::class, 'addComment'])->name('media.comment');
    });

    // Events Routes
    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/', [EventController::class, 'index'])->name('index');
        Route::get('/create', [EventController::class, 'create'])->name('create');
        Route::post('/', [EventController::class, 'store'])->name('store');
        Route::get('/{event}', [EventController::class, 'show'])->name('show');
        Route::post('/{event}/register', [EventController::class, 'register'])->name('register');
        Route::post('/{event}/cancel', [EventController::class, 'cancelRegistration'])->name('cancel');
        Route::post('/{event}/photos', [EventController::class, 'uploadPhotos'])->name('upload-photos');
    });

    // Donations Routes
    Route::prefix('donations')->name('donations.')->group(function () {
        Route::get('/', [DonationController::class, 'index'])->name('index');
        Route::get('/my-donations', [DonationController::class, 'myDonations'])->name('my-donations');
        Route::get('/campaigns/{campaign}', [DonationController::class, 'showCampaign'])->name('campaign');
        Route::post('/campaigns/{campaign}/donate', [DonationController::class, 'createDonation'])->name('create');
        Route::get('/success/{donation}', [DonationController::class, 'paymentSuccess'])->name('success');
        Route::post('/webhook', [DonationController::class, 'paymentWebhook'])->name('webhook');
    });

    // Jobs Routes
    Route::prefix('jobs')->name('jobs.')->group(function () {
        Route::get('/', [JobsController::class, 'index'])->name('index');
        Route::get('/create', [JobsController::class, 'create'])->name('create');
        Route::post('/', [JobsController::class, 'store'])->name('store');
        Route::get('/my-jobs', [JobsController::class, 'myJobs'])->name('my-jobs');
        Route::get('/{job}', [JobsController::class, 'show'])->name('show');
        Route::post('/{job}/apply', [JobsController::class, 'apply'])->name('apply');
    });

    // Forum Routes
    Route::prefix('forum')->name('forum.')->group(function () {
        Route::get('/', [ForumController::class, 'index'])->name('index');
        Route::get('/search', [ForumController::class, 'search'])->name('search');
        Route::get('/{forum}', [ForumController::class, 'showForum'])->name('forum');
        Route::get('/{forum}/create-topic', [ForumController::class, 'createTopic'])->name('create-topic');
        Route::post('/{forum}/topics', [ForumController::class, 'storeTopic'])->name('store-topic');
        Route::get('/topics/{topic}', [ForumController::class, 'showTopic'])->name('topic');
        Route::post('/topics/{topic}/posts', [ForumController::class, 'createPost'])->name('create-post');
        Route::post('/posts/{post}/like', [ForumController::class, 'likePost'])->name('like-post');
    });

    // News Routes
    Route::prefix('news')->name('news.')->group(function () {
        Route::get('/', [NewsController::class, 'index'])->name('index');
        Route::get('/create', [NewsController::class, 'create'])->name('create');
        Route::post('/', [NewsController::class, 'store'])->name('store');
        Route::get('/{article}', [NewsController::class, 'show'])->name('show');
        Route::get('/{article}/edit', [NewsController::class, 'edit'])->name('edit');
        Route::put('/{article}', [NewsController::class, 'update'])->name('update');
    });

    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware(['role:admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'userManagement'])->name('users');
        Route::post('/users/{user}/verify', [AdminController::class, 'verifyUser'])->name('verify-user');
        Route::post('/users/{user}/assign-role', [AdminController::class, 'assignRole'])->name('assign-role');
        Route::get('/moderation', [AdminController::class, 'contentModeration'])->name('moderation');
        Route::post('/approve-content', [AdminController::class, 'approveContent'])->name('approve-content');
        Route::get('/financial-reports', [AdminController::class, 'financialReports'])->name('financial-reports');
        Route::get('/settings', [AdminController::class, 'systemSettings'])->name('settings');
        Route::post('/settings', [AdminController::class, 'updateSettings'])->name('update-settings');
    });
});

// Public routes
Route::get('/alumni/{user}', [ProfileController::class, 'show'])->name('public.profile');
Route::get('/gallery/albums/{album}', [GalleryController::class, 'showAlbum'])->name('public.album');
Route::get('/events/{event}', [EventController::class, 'show'])->name('public.event');
Route::get('/donations/campaigns/{campaign}', [DonationController::class, 'showCampaign'])->name('public.campaign');
Route::get('/news/{article}', [NewsController::class, 'show'])->name('public.news');

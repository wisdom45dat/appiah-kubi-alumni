<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Appiah Kubi Alumni Platform Configuration
    |--------------------------------------------------------------------------
    */

    'school' => [
        'name' => 'Appiah Kubi Junior High School',
        'motto' => 'Knowledge, Integrity, Excellence',
        'established' => 1970,
        'location' => 'Accra, Ghana',
    ],

    'features' => [
        'donations' => env('FEATURE_DONATIONS', true),
        'events' => env('FEATURE_EVENTS', true),
        'jobs' => env('FEATURE_JOBS', true),
        'mentorship' => env('FEATURE_MENTORSHIP', true),
        'gallery' => true,
        'forum' => true,
        'news' => true,
    ],

    'gallery' => [
        'max_file_size' => 51200, // 50MB in KB
        'allowed_image_types' => ['jpeg', 'png', 'jpg', 'gif'],
        'allowed_video_types' => ['mp4', 'avi', 'mov', 'wmv'],
        'max_files_per_upload' => 20,
    ],

    'donations' => [
        'currency' => 'GHS',
        'min_amount' => 1,
        'payment_methods' => ['card', 'mobile_money', 'bank_transfer'],
    ],

    'notifications' => [
        'email' => [
            'from_address' => env('MAIL_FROM_ADDRESS', 'noreply@appiahkubi.edu.gh'),
            'from_name' => env('MAIL_FROM_NAME', 'Appiah Kubi Alumni'),
        ],
    ],

    'pwa' => [
        'enabled' => true,
        'offline_tolerance_hours' => env('PWA_OFFLINE_TOLERANCE_HOURS', 4),
    ],
];

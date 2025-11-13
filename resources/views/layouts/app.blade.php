<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Appiah Kubi Alumni')</title>
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#1e40af" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#1f2937" media="(prefers-color-scheme: dark)">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    
    <!-- Styles -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <link href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" rel="stylesheet">
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.0/dist/cdn.min.js" defer></script>
    
    <style>
        .dark-mode-transition {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50 dark:bg-gray-900 dark:text-gray-100 dark-mode-transition min-h-full">
    <!-- Navigation -->
    <nav class="bg-blue-800 dark:bg-gray-800 text-white shadow-lg dark-mode-transition">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex-shrink-0 flex items-center">
                        <!-- School Logo - Smaller on mobile -->
                        <img src="{{ asset('images/logo.jpg') }}" 
                             alt="Appiah Kubi JHS" 
                             class="h-8 w-8 sm:h-10 sm:w-10 mr-2 sm:mr-3 rounded-sm">
                        <!-- School Name - Hidden on very small screens, shows on sm+ -->
                        <div class="flex flex-col">
                            <span class="font-bold text-base sm:text-lg leading-tight hidden xs:block">Appiah Kubi</span>
                            <span class="text-blue-200 dark:text-gray-300 text-xs leading-tight hidden sm:block">Alumni Association</span>
                        </div>
                    </a>
                    
                    <!-- Primary Navigation - Hidden on mobile -->
                    <div class="hidden md:ml-6 md:flex md:space-x-4">
                        <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-blue-700 dark:hover:bg-gray-700 transition {{ request()->routeIs('dashboard') ? 'bg-blue-900 dark:bg-gray-900' : '' }}">
                            <i class="fas fa-home mr-1"></i> Dashboard
                        </a>
                        <a href="{{ route('directory') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-blue-700 dark:hover:bg-gray-700 transition {{ request()->routeIs('directory') ? 'bg-blue-900 dark:bg-gray-900' : '' }}">
                            <i class="fas fa-users mr-1"></i> Alumni
                        </a>
                        <a href="{{ route('gallery.index') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-blue-700 dark:hover:bg-gray-700 transition {{ request()->routeIs('gallery.*') ? 'bg-blue-900 dark:bg-gray-900' : '' }}">
                            <i class="fas fa-images mr-1"></i> Gallery
                        </a>
                        <a href="{{ route('events.index') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-blue-700 dark:hover:bg-gray-700 transition {{ request()->routeIs('events.*') ? 'bg-blue-900 dark:bg-gray-900' : '' }}">
                            <i class="fas fa-calendar mr-1"></i> Events
                        </a>
                        <a href="{{ route('donations.index') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-blue-700 dark:hover:bg-gray-700 transition {{ request()->routeIs('donations.*') ? 'bg-blue-900 dark:bg-gray-900' : '' }}">
                            <i class="fas fa-hand-holding-heart mr-1"></i> Donate
                        </a>
                    </div>
                </div>

                <!-- Right Side - Theme Toggle & User Menu -->
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <!-- Dark Mode Toggle -->
                    <button id="themeToggle" class="p-2 rounded-md hover:bg-blue-700 dark:hover:bg-gray-700 transition">
                        <i id="themeIcon" class="fas fa-moon text-white text-sm sm:text-base"></i>
                        <span class="sr-only">Toggle dark mode</span>
                    </button>

                    <!-- Mobile menu button -->
                    <button id="mobileMenuButton" class="md:hidden p-2 rounded-md hover:bg-blue-700 dark:hover:bg-gray-700 transition">
                        <i class="fas fa-bars text-white"></i>
                    </button>

                    <!-- User Menu -->
                    @auth
                    <div x-data="{ open: false }" class="ml-3 relative">
                        <button @click="open = !open" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-gray-500">
                            <span class="sr-only">Open user menu</span>
                            <img class="h-8 w-8 rounded-full" src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}">
                            <span class="ml-2 hidden sm:block">{{ auth()->user()->name }}</span>
                            <i class="fas fa-chevron-down ml-1 text-sm hidden sm:block"></i>
                        </button>

                        <div x-show="open" @click.away="open = false" 
                             class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-50 dark-mode-transition">
                            <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-user mr-2"></i> Profile
                            </a>
                            <a href="{{ route('profile.albums') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-images mr-2"></i> My Albums
                            </a>
                            <a href="{{ route('donations.my-donations') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-donate mr-2"></i> My Donations
                            </a>
                            <a href="{{ route('jobs.my-jobs') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-briefcase mr-2"></i> My Jobs
                            </a>
                            @can('access admin')
                            <div class="border-t border-gray-100 dark:border-gray-700"></div>
                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-cog mr-2"></i> Admin
                            </a>
                            @endcan
                            <div class="border-t border-gray-100 dark:border-gray-700"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                    @else
                    <div class="flex space-x-2">
                        <a href="{{ route('login') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-blue-700 hidden sm:block">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" class="px-3 py-2 rounded-md text-sm font-medium bg-blue-600 hover:bg-blue-700 hidden sm:block">
                            Join Alumni
                        </a>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Navigation Menu -->
    <div id="mobileMenu" class="md:hidden bg-blue-800 dark:bg-gray-800 text-white dark-mode-transition hidden">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-blue-700 dark:hover:bg-gray-700 {{ request()->routeIs('dashboard') ? 'bg-blue-900 dark:bg-gray-900' : '' }}">
                <i class="fas fa-home mr-2"></i> Dashboard
            </a>
            <a href="{{ route('directory') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-blue-700 dark:hover:bg-gray-700 {{ request()->routeIs('directory') ? 'bg-blue-900 dark:bg-gray-900' : '' }}">
                <i class="fas fa-users mr-2"></i> Alumni
            </a>
            <a href="{{ route('gallery.index') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-blue-700 dark:hover:bg-gray-700 {{ request()->routeIs('gallery.*') ? 'bg-blue-900 dark:bg-gray-900' : '' }}">
                <i class="fas fa-images mr-2"></i> Gallery
            </a>
            <a href="{{ route('events.index') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-blue-700 dark:hover:bg-gray-700 {{ request()->routeIs('events.*') ? 'bg-blue-900 dark:bg-gray-900' : '' }}">
                <i class="fas fa-calendar mr-2"></i> Events
            </a>
            <a href="{{ route('donations.index') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-blue-700 dark:hover:bg-gray-700 {{ request()->routeIs('donations.*') ? 'bg-blue-900 dark:bg-gray-900' : '' }}">
                <i class="fas fa-hand-holding-heart mr-2"></i> Donate
            </a>
            <a href="{{ route('jobs.index') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-blue-700 dark:hover:bg-gray-700 {{ request()->routeIs('jobs.*') ? 'bg-blue-900 dark:bg-gray-900' : '' }}">
                <i class="fas fa-briefcase mr-2"></i> Jobs
            </a>
            <a href="{{ route('forum.index') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-blue-700 dark:hover:bg-gray-700 {{ request()->routeIs('forum.*') ? 'bg-blue-900 dark:bg-gray-900' : '' }}">
                <i class="fas fa-comments mr-2"></i> Forum
            </a>
            <a href="{{ route('news.index') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-blue-700 dark:hover:bg-gray-700 {{ request()->routeIs('news.*') ? 'bg-blue-900 dark:bg-gray-900' : '' }}">
                <i class="fas fa-newspaper mr-2"></i> News
            </a>
            @guest
            <div class="border-t border-blue-700 dark:border-gray-700 pt-2">
                <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-blue-700">
                    <i class="fas fa-sign-in-alt mr-2"></i> Sign In
                </a>
                <a href="{{ route('register') }}" class="block px-3 py-2 rounded-md text-base font-medium bg-blue-600 hover:bg-blue-700 mt-1">
                    <i class="fas fa-user-plus mr-2"></i> Join Alumni
                </a>
            </div>
            @endguest
        </div>
    </div>

    <!-- Page Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="px-4 py-4 sm:px-0">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">@yield('title')</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">@yield('subtitle', '')</p>
                </div>
                <div class="flex space-x-2">
                    @yield('actions')
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
        <div class="mb-4 px-4">
            <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-4 px-4">
            <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        </div>
        @endif

        @if(session('warning'))
        <div class="mb-4 px-4">
            <div class="bg-yellow-100 dark:bg-yellow-900 border border-yellow-400 dark:border-yellow-700 text-yellow-700 dark:text-yellow-300 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('warning') }}</span>
            </div>
        </div>
        @endif

        @if(session('info'))
        <div class="mb-4 px-4">
            <div class="bg-blue-100 dark:bg-blue-900 border border-blue-400 dark:border-blue-700 text-blue-700 dark:text-blue-300 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('info') }}</span>
            </div>
        </div>
        @endif

        <!-- Page Content -->
        <div class="px-4 py-4 sm:px-0">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-8 dark-mode-transition">
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Appiah Kubi JHS</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">
                        Old Students Association - Connecting generations of Appiah Kubi alumni since 1970.
                    </p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <li><a href="{{ route('directory') }}" class="hover:text-blue-600 dark:hover:text-blue-400">Alumni Directory</a></li>
                        <li><a href="{{ route('events.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400">Upcoming Events</a></li>
                        <li><a href="{{ route('gallery.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400">Photo Gallery</a></li>
                        <li><a href="{{ route('donations.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400">Support Our School</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white mb-4">Connect</h4>
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <li><a href="#" class="hover:text-blue-600 dark:hover:text-blue-400"><i class="fab fa-facebook mr-2"></i>Facebook</a></li>
                        <li><a href="#" class="hover:text-blue-600 dark:hover:text-blue-400"><i class="fab fa-whatsapp mr-2"></i>WhatsApp Group</a></li>
                        <li><a href="#" class="hover:text-blue-600 dark:hover:text-blue-400"><i class="fas fa-envelope mr-2"></i>Email Updates</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white mb-4">Contact</h4>
                    <address class="text-sm text-gray-600 dark:text-gray-400 not-italic">
                        <p>Appiah Kubi Junior High School</p>
                        <p>P.O. Box 123</p>
                        <p>Accra, Ghana</p>
                        <p class="mt-2"><a href="mailto:alumni@appiahkubi.edu.gh" class="hover:text-blue-600 dark:hover:text-blue-400">alumni@appiahkubi.edu.gh</a></p>
                    </address>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700 text-center text-sm text-gray-500 dark:text-gray-400">
                <p>&copy; {{ date('Y') }} Appiah Kubi JHS Old Students Association. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script>
        // Initialize Fancybox for image galleries
        Fancybox.bind("[data-fancybox]", {
            // Your custom options
        });

        // Dark Mode Toggle Functionality
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const html = document.documentElement;

        // Check for saved theme preference or prefer OS scheme
        const currentTheme = localStorage.getItem('theme') || 
                           (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        
        // Apply the theme
        if (currentTheme === 'dark') {
            html.classList.add('dark');
            themeIcon.classList.replace('fa-moon', 'fa-sun');
        } else {
            html.classList.remove('dark');
            themeIcon.classList.replace('fa-sun', 'fa-moon');
        }

        // Toggle theme
        themeToggle.addEventListener('click', () => {
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
                themeIcon.classList.replace('fa-sun', 'fa-moon');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
                themeIcon.classList.replace('fa-moon', 'fa-sun');
            }
        });

        // Listen for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
            if (!localStorage.getItem('theme')) { // Only if user hasn't set a preference
                if (event.matches) {
                    html.classList.add('dark');
                    themeIcon.classList.replace('fa-moon', 'fa-sun');
                } else {
                    html.classList.remove('dark');
                    themeIcon.classList.replace('fa-sun', 'fa-moon');
                }
            }
        });

        // Mobile Menu Toggle
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const mobileMenu = document.getElementById('mobileMenu');
        
        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', (event) => {
            if (mobileMenu && !mobileMenu.contains(event.target) && !mobileMenuButton.contains(event.target)) {
                mobileMenu.classList.add('hidden');
            }
        });

        // PWA Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('ServiceWorker registration successful');
                    })
                    .catch(function(error) {
                        console.log('ServiceWorker registration failed: ', error);
                    });
            });
        }

        // Auto-dismiss flash messages after 5 seconds
        setTimeout(function() {
            const flashMessages = document.querySelectorAll('[role="alert"]');
            flashMessages.forEach(function(message) {
                message.style.opacity = '0';
                message.style.transition = 'opacity 0.5s';
                setTimeout(() => message.remove(), 500);
            });
        }, 5000);
    </script>

    @stack('scripts')
</body>
</html>
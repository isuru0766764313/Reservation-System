<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation System</title>
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        xintegrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2gBwF5K6S5J4E5Q5P5w==" crossorigin="anonymous"
        referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7fafc;
        }

        /* Hero section background gradient */
        .hero-bg {
            background-image: linear-gradient(to right, #6d28d9, #8b5cf6, #c084fc);
            position: relative;
            overflow: hidden;
        }

        /* Animation for the login and signup cards */
        .floating-card-container {
            transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            transform: translateX(100%);
            visibility: hidden;
            pointer-events: none;
            overflow-y: auto;
        }

        .floating-card-container.active {
            transform: translateX(0);
            visibility: visible;
            pointer-events: auto;
        }

        /* Password Reset Modal Animation */
        .password-reset-modal {
            transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
            opacity: 0;
            transform: translateY(-100%);
            z-index: 100;
        }

        .password-reset-modal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* Keyframes for the continuous scrolling animation */
        @keyframes scroll-left {
            from {
                transform: translateX(0%);
            }

            to {
                /* The scroll distance is a large negative value to ensure all cards move off-screen */
                transform: translateX(calc(-100% - 24px));
            }
        }

        /* Apply the animation to the card grid for all screen sizes */
        .card-grid {
            animation: scroll-left 60s linear infinite;
        }

        /* For form switching, hide inactive forms */
        .form-container.hidden {
            display: none;
        }

        /* Mobile navigation menu styles */
        #mobile-menu {
            transition: right 0.3s ease-in-out;
            right: -100%;
            z-index: 60;
            visibility: visible;
        }

        #mobile-menu.open {
            right: 0;
        }

        /* Mobile menu backdrop overlay */
        #mobile-menu-backdrop {
            transition: opacity 0.3s ease-in-out;
            opacity: 0;
            pointer-events: none;
            z-index: 55;
        }

        #mobile-menu-backdrop.open {
            opacity: 1;
            pointer-events: auto;
        }

        /* Flexbox adjustments for card content alignment */
        .venue-card-content {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            /* Ensure the flex container fills the card */
        }

        .card-image-container {
            flex-shrink: 0;
            /* Prevents the image from shrinking */
        }

        .card-details {
            flex-grow: 1;
            /* Allows this section to grow and take up available space */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding-bottom: 1rem;
            /* Add some padding at the bottom for spacing */
        }
    </style>
</head>

<body class="overflow-x-hidden">

    <script>
        // Define mobile menu functions globally (before any HTML) so inline onclick attributes work on all browsers
        function openMobileMenu() {
            var menu = document.getElementById('mobile-menu');
            var backdrop = document.getElementById('mobile-menu-backdrop');
            if (menu) { menu.classList.add('open'); }
            if (backdrop) { backdrop.classList.remove('hidden'); backdrop.classList.add('open'); }
            document.body.style.overflow = 'hidden';
        }
        function closeMobileMenu() {
            var menu = document.getElementById('mobile-menu');
            var backdrop = document.getElementById('mobile-menu-backdrop');
            if (menu) { menu.classList.remove('open'); }
            if (backdrop) { backdrop.classList.remove('open'); setTimeout(function() { backdrop.classList.add('hidden'); }, 300); }
            document.body.style.overflow = '';
        }
    </script>

    <!-- Mobile Navigation Menu (placed first to ensure it's above all stacking contexts) -->
    <div id="mobile-menu"
        class="fixed top-0 right-0 h-full w-4/5 sm:w-2/3 bg-gray-900 text-white shadow-2xl z-50 p-6 flex flex-col items-start space-y-4 md:hidden">
        <button id="close-mobile-menu-btn" onclick="closeMobileMenu();" class="self-end text-white">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <a href="#" class="w-full text-lg hover:bg-gray-700 rounded-lg p-2 transition-colors">Home</a>
        <a href="#" id="mobile-nav-customer-signin"
            class="w-full text-lg hover:bg-gray-700 rounded-lg p-2 transition-colors">Customer Sign In</a>
        <a href="#" id="mobile-nav-customer-register"
            class="w-full text-lg hover:bg-gray-700 rounded-lg p-2 transition-colors">Customer Register</a>
        <a href="#" id="mobile-nav-admin-login"
            class="w-full text-lg hover:bg-gray-700 rounded-lg p-2 transition-colors">Admin Login</a>
        <a href="#" id="mobile-nav-admin-register"
            class="w-full text-lg hover:bg-gray-700 rounded-lg p-2 transition-colors">Admin Register</a>
        <a href="#" class="w-full text-lg hover:bg-gray-700 rounded-lg p-2 transition-colors">About Us</a>
        <a href="#" class="w-full text-lg hover:bg-gray-700 rounded-lg p-2 transition-colors">Contact Us</a>
    </div>

    <!-- Mobile Menu Backdrop -->
    <div id="mobile-menu-backdrop" class="fixed inset-0 bg-black bg-opacity-50 md:hidden hidden"></div>

    <!-- Pass PHP Halls data and login error state to JavaScript -->
    <script>
        window.hallData = @json(isset($halls) ? $halls : []);
        window.hasLoginError = @json(session()->has('error_key_2'));
        window.hasAdminLoginError = @json(session()->has('admin_error_key_2'));
        console.log('Hall Data Loaded:', window.hallData);
    </script>

    <!-- Navbar -->
    <nav class="absolute top-0 left-0 w-full z-10 p-4 sm:p-6 lg:p-8 flex justify-between items-center text-white">
        <!-- Logo and Company Name -->
        <div class="flex items-center space-x-2">
            <!-- A simple SVG for the logo -->
            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                    clip-rule="evenodd"></path>
            </svg>
            <span class="text-xl md:text-2xl font-bold">Reservation System</span>
        </div>
        <!-- Right side menu links for desktop -->
        <div class="hidden md:flex items-center space-x-6 lg:space-x-10">
            <a href="#" class="hover:text-gray-200">Home</a>
            <a href="#" id="nav-customer-signin" class="hover:text-gray-200">Customer Login</a>
            <a href="#" id="nav-customer-register" class="hover:text-gray-200">Customer Register</a>
            <a href="#" id="nav-admin-login" class="hover:text-gray-200">Admin Login</a>
            <a href="#" id="nav-admin-register" class="hover:text-gray-200">Admin Register</a>
            <a href="#" class="hover:text-gray-200">About Us</a>
            <a href="#" class="hover:text-gray-200">Contact Us</a>
        </div>
        <!-- Hamburger menu icon for mobile -->
        <div class="md:hidden">
            <button id="mobile-menu-btn" onclick="openMobileMenu();" class="text-white focus:outline-none">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7">
                    </path>
                </svg>
            </button>
        </div>
    </nav>
    <!-- Hero Section -->
    <main class="hero-bg min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-12 relative">
        <!-- Main content container -->
        <div
            class="container mx-auto flex flex-col-reverse lg:flex-row items-center justify-between space-y-8 lg:space-y-0 lg:space-x-12 relative z-10">
            <!-- Left side text and search -->
            <div class="w-full lg:w-1/2 text-center lg:text-left text-white space-y-6">
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold leading-tight">Book Your Perfect Event Venue Today
                </h1>
                <p class="text-base sm:text-lg lg:text-xl text-gray-200 leading-relaxed">
                    Find and reserve the ideal space for your meeting, party, or concert with ease. Our system makes
                    booking halls, playgrounds, and arenas simple and secure.
                </p>

                <!-- Search Bar -->
                <div class="relative w-full max-w-2xl">
                    <div class="relative flex items-center">
                        <i class="fas fa-search absolute left-4 text-gray-400 text-base pointer-events-none z-10"></i>
                        <input type="text" id="hall-search" placeholder="Search halls by name, type, or location..."
                            class="w-full py-3 pl-12 pr-5 rounded-full text-gray-800 text-base focus:outline-none focus:ring-4 focus:ring-white focus:ring-opacity-30 shadow-xl placeholder-gray-400"
                            autocomplete="off">
                    </div>

                    <!-- Search Results Dropdown -->
                    <div id="search-results"
                        class="absolute w-full mt-2 bg-white rounded-xl shadow-2xl max-h-96 overflow-y-auto hidden z-50">
                        <!-- Results will be populated here -->
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center lg:items-start space-y-4 sm:space-y-0 sm:space-x-4">
                    <button id="make-reservation-btn"
                        class="bg-white text-indigo-600 font-bold py-3 px-8 rounded-full shadow-lg hover:bg-gray-100 transition-colors">
                        Make a Reservation
                    </button>
                    <!-- A simple search button placeholder -->
                    <button id="search-venue-btn"
                        class="bg-transparent border-2 border-white text-white font-bold py-3 px-8 rounded-full hover:bg-white hover:text-indigo-600 transition-colors">
                        Register Venue
                    </button>
                </div>
            </div>
            <!-- Right side illustration -->
            <!--<img src="https://cdn-in.icons8.com/WyjM0CitH0aSec0L3UAMFg/9JCpe3n9iUi77FcDQtw6NQ/Group.png" alt="Online Booking Illustration" class="w-full h-auto max-w-xl object-contain rounded-xl shadow-lg">-->
            <div class="w-full lg:w-1/2 flex justify-center lg:justify-end">
                <img src="https://cdn-in.icons8.com/WyjM0CitH0aSec0L3UAMFg/wwFymw5oKky_ibJ0UqttIQ/Group.png"
                    alt="Online Booking Illustration">
            </div>
        </div>
    </main>

    <!-- Floating Customer Authentication Card -->
    <div id="customer-auth-container"
        class="floating-card-container fixed top-0 right-0 h-screen w-full md:w-1/2 lg:w-1/3 bg-white shadow-2xl z-50">
        <div class="relative w-full h-full flex flex-col p-8 sm:p-12 space-y-6">
            <!-- Close Button -->
            <button id="close-customer-btn"
                class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Customer Login Form -->
            <div id="customer-login-form" class="form-container space-y-6">
                <h2 class="text-3xl font-bold text-center text-gray-800">Customer Log In</h2>
                <p class="text-center text-gray-600">Please sign in to make a reservation.</p>
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (session('error_key_2'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        {{ session('error_key_2') }}
                    </div>
                @endif
                <form action="{{route('login_post_route')}}" method="post" class="space-y-4">
                    @csrf
                    <input type="email" name="email" placeholder="Email Address"
                        class="w-full p-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$"
                        title="Valid email format required (e.g. user@example.com)" maxlength="255" required>
                    <div class="relative">
                        <input type="password" name="password" id="customer-login-password" placeholder="Password"
                            class="w-full p-3 pr-10 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            required>
                        <button type="button"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 toggle-password"
                            data-target="customer-login-password">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    <button type="submit"
                        class="w-full bg-indigo-600 text-white font-bold py-3 rounded-lg hover:bg-indigo-700 transition-colors">Sign
                        In</button>
                </form>
                <button id="forgot-password-customer-btn"
                    class="block w-full text-center text-sm text-indigo-600 font-semibold hover:underline mt-2 bg-transparent border-none p-0 cursor-pointer">Forgot
                    Password?</button>
                <p class="text-center text-sm text-gray-500 mt-4">
                    Don't have an account? <a href="#" id="open-customer-signup-link"
                        class="text-indigo-600 font-semibold hover:underline">Sign Up</a>
                </p>
            </div>

            <!-- Customer Sign Up Form -->
            <div id="customer-signup-form" class="form-container space-y-6 hidden">
                <h2 class="text-3xl font-bold text-center text-gray-800">Customer Register</h2>
                <p class="text-center text-gray-600">Join us to book your event today.</p>
                <form action="{{route('registration_post_route')}}" method="post" class="space-y-4" id="CustomerRegitrationForm">
                    @csrf
                    <select name="profile_title" required
                        class="w-full p-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="" disabled selected>Select Title</option>
                        <option value="Mr.">Mr.</option>
                        <option value="Mrs.">Mrs.</option>
                        <option value="Miss.">Miss.</option>
                        <option value="Doc.">Doc.</option>
                        <option value="Rev.">Rev.</option>
                        <option value="Prof.">Prof.</option>
                    </select>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="text" name="first_name" placeholder="First Name"
                            class="w-full p-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            pattern="[a-zA-Z\s'\-]{2,50}"
                            title="Only letters, spaces, hyphens and apostrophes allowed (2-50 characters)"
                            maxlength="50" required>
                        <input type="text" name="last_name" placeholder="Last Name"
                            class="w-full p-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            pattern="[a-zA-Z\s'\-]{2,50}"
                            title="Only letters, spaces, hyphens and apostrophes allowed (2-50 characters)"
                            maxlength="50" required>
                    </div>
                    <input type="email" name="email" placeholder="Email Address"
                        class="w-full p-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$"
                        title="Valid email format required (e.g. user@example.com)" maxlength="255" required>
                    <input type="tel" name="telephone_number" placeholder="Telephone Number"
                        class="w-full p-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        pattern="[0-9]{10}" title="10-digit phone number (e.g. 0771234567)" required>
                    <input type="text" name="national_id" placeholder="National ID Number"
                        class="w-full p-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        pattern="[a-zA-Z0-9]{10,12}" title="10-12 alphanumeric characters only" required>
                    <div class="relative">
                        <input type="password" name="password" id="customer-signup-password" placeholder="Password"
                            class="w-full p-3 pr-10 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                            title="Must contain 8+ chars with uppercase, lowercase and number" required>
                        <button type="button"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 toggle-password"
                            data-target="customer-signup-password">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    <div class="relative">
                        <input type="password" name="confirm_password" id="customer-confirm-password"
                            placeholder="Confirm Password"
                            class="w-full p-3 pr-10 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <button type="button"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 toggle-password"
                            data-target="customer-confirm-password">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    <div class="flex gap-6">
                        <div class="flex items-center space-x-2">
                            <input class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500" type="radio" name="type" value="private" id="typeprivate">
                            <label class="text-sm text-gray-700" for="typeprivate">Private</label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500" type="radio" name="type" value="government" id="typegovernment">
                            <label class="text-sm text-gray-700" for="typegovernment">Government</label>
                        </div>
                    </div>
                    <button type="submit"
                        class="w-full bg-indigo-600 text-white font-bold py-3 rounded-lg hover:bg-indigo-700 transition-colors">Sign
                        Up</button>
                    <div class="alert alert-danger d-none" id="passwordErrorCustomer"><i
                            class="fas fa-exclamation-circle me-2"></i>Passwords do not match!</div>
                </form>
                <p class="text-center text-sm text-gray-500">
                    Already have an account? <a href="#" id="open-customer-login-link"
                        class="text-indigo-600 font-semibold hover:underline">Sign In</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Floating Admin Authentication Card -->
    <div id="admin-auth-container"
        class="floating-card-container fixed top-0 right-0 h-screen w-full md:w-1/2 lg:w-1/3 bg-white shadow-2xl z-50">
        <div class="relative w-full h-full flex flex-col p-8 sm:p-12 space-y-6">
            <!-- Close Button -->
            <button id="close-admin-btn"
                class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Admin Login Form -->
            <div id="admin-login-form" class="form-container space-y-6">
                <h2 class="text-3xl font-bold text-center text-gray-800">Admin Log In</h2>
                <p class="text-center text-gray-600">Please sign in to access the admin panel.</p>
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (session('admin_error_key_2'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        {{ session('admin_error_key_2') }}
                    </div>
                @endif
                <form action="{{route('admin.login.post.route')}}" method="post" class="space-y-4">
                    @csrf
                    <input type="email" name="email" placeholder="Email Address"
                        class="w-full p-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <div class="relative">
                        <input type="password" name="password" id="admin-login-password" placeholder="Password"
                            class="w-full p-3 pr-10 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <button type="button"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 toggle-password"
                            data-target="admin-login-password">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    <button type="submit"
                        class="w-full bg-indigo-600 text-white font-bold py-3 rounded-lg hover:bg-indigo-700 transition-colors">Sign
                        In</button>
                </form>
                <button id="forgot-password-admin-btn"
                    class="block w-full text-center text-sm text-indigo-600 font-semibold hover:underline mt-2 bg-transparent border-none p-0 cursor-pointer">Forgot
                    Password?</button>
                <p class="text-center text-sm text-gray-500 mt-4">
                    Don't have an admin account? <a href="#" id="open-admin-signup-link"
                        class="text-indigo-600 font-semibold hover:underline">Sign Up</a>
                </p>
            </div>

            <!-- Admin Sign Up Form -->
            <div id="admin-signup-form" class="form-container space-y-6 hidden">
                <h2 class="text-3xl font-bold text-center text-gray-800">Admin Register</h2>
                <p class="text-center text-gray-600">Register as an administrator.</p>
                <form action="{{route('admin.registration.post.route')}}" method="post" class="space-y-4"
                    id="AdminRegitrationForm">
                    @csrf
                    <input type="text" name="company_name" placeholder="Company Name"
                        class="w-full p-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        pattern="[a-zA-Z\s'\-]{2,50}"
                        title="Only letters, spaces, hyphens and apostrophes allowed (2-50 characters)" maxlength="50"
                        required>
                    <input type="email" name="email" placeholder="Official Email"
                        class="w-full p-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$"
                        title="Valid email format required (e.g. user@example.com)" maxlength="255" required>
                    <input type="tel" name="telephone_number" placeholder="Official Contact Number"
                        class="w-full p-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        pattern="[0-9]{10}" title="10-digit phone number (e.g. 0771234567)" required>
                    <div class="relative">
                        <input type="password" name="password" id="admin-signup-password" placeholder="Password"
                            class="w-full p-3 pr-10 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                            title="Must contain 8+ chars with uppercase, lowercase and number" required>
                        <button type="button"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 toggle-password"
                            data-target="admin-signup-password">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    <div class="relative">
                        <input type="password" name="confirm_password" id="admin-confirm-password"
                            placeholder="Confirm Password"
                            class="w-full p-3 pr-10 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <button type="button"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 toggle-password"
                            data-target="admin-confirm-password">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    <button type="submit"
                        class="w-full bg-indigo-600 text-white font-bold py-3 rounded-lg hover:bg-indigo-700 transition-colors">
                        Sign Up
                    </button>
                    <div class="alert alert-danger d-none" id="passwordErrorAdmin"><i
                            class="fas fa-exclamation-circle me-2"></i>Passwords do not match!</div>
                </form>
                <p class="text-center text-sm text-gray-500">
                    Already have an admin account? <a href="#" id="open-admin-login-link"
                        class="text-indigo-600 font-semibold hover:underline">Sign In</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Customer Password Reset Modal -->
    <div id="customer-password-reset-modal"
        class="password-reset-modal fixed inset-0 bg-gray-900 bg-opacity-50 flex items-start justify-center p-4 pt-20 hidden">
        <div class="bg-white rounded-xl shadow-2xl p-6 sm:p-8 w-full max-w-sm relative">
            <!-- Close Button -->
            <button id="close-customer-reset-btn"
                class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <h3 class="text-2xl font-bold text-center text-gray-800 mb-2">Customer Password Reset</h3>
            <p class="text-center text-gray-600 mb-6 text-sm">Enter your email and phone number to receive a reset OTP.
            </p>

            <form action="#" method="post" class="space-y-4">
                <input type="email" name="customer-reset-email" placeholder="Email Address"
                    class="w-full p-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <input type="tel" name="customer-reset-phone" placeholder="Telephone Number"
                    class="w-full p-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <button type="submit"
                    class="w-full bg-indigo-600 text-white font-bold py-3 rounded-lg hover:bg-indigo-700 transition-colors">
                    Send Reset OTP
                </button>
            </form>
        </div>
    </div>

    <!-- Admin Password Reset Modal -->
    <div id="admin-password-reset-modal"
        class="password-reset-modal fixed inset-0 bg-gray-900 bg-opacity-50 flex items-start justify-center p-4 pt-20 hidden">
        <div class="bg-white rounded-xl shadow-2xl p-6 sm:p-8 w-full max-w-sm relative">
            <!-- Close Button -->
            <button id="close-admin-reset-btn"
                class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <h3 class="text-2xl font-bold text-center text-gray-800 mb-2">Admin Password Reset</h3>
            <p class="text-center text-gray-600 mb-6 text-sm">Enter your email and phone number to receive a reset OTP.
            </p>

            <form action="#" method="post" class="space-y-4">
                <input type="email" name="admin-reset-email" placeholder="Email Address"
                    class="w-full p-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <input type="tel" name="admin-reset-phone" placeholder="Telephone Number"
                    class="w-full p-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <button type="submit"
                    class="w-full bg-indigo-600 text-white font-bold py-3 rounded-lg hover:bg-indigo-700 transition-colors">
                    Send Reset OTP
                </button>
            </form>
        </div>
    </div>

    <!-- Our Key Features Section -->
    <section class="py-12 sm:py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl sm:text-3xl font-bold text-center text-gray-800 mb-8">Our Key Features</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <!-- Feature Card 1 -->
                <div class="bg-gray-50 p-6 rounded-xl shadow-lg">
                    <div
                        class="bg-indigo-600 text-white w-16 h-16 flex items-center justify-center rounded-full mx-auto mb-4 text-2xl">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Easy Booking</h3>
                    <p class="text-sm text-gray-600">Simple 3-step process to book any government hall online</p>
                </div>
                <!-- Feature Card 2 -->
                <div class="bg-gray-50 p-6 rounded-xl shadow-lg">
                    <div
                        class="bg-indigo-600 text-white w-16 h-16 flex items-center justify-center rounded-full mx-auto mb-4 text-2xl">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Island-wide Coverage</h3>
                    <p class="text-sm text-gray-600">Access halls in all provinces including Colombo, Kandy, Galle and
                        Jaffna</p>
                </div>
                <!-- Feature Card 3 -->
                <div class="bg-gray-50 p-6 rounded-xl shadow-lg">
                    <div
                        class="bg-indigo-600 text-white w-16 h-16 flex items-center justify-center rounded-full mx-auto mb-4 text-2xl">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Secure Payments</h3>
                    <p class="text-sm text-gray-600">Safe online payment gateway with government verification</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Auto Image Grid Section -->
    <section class="py-12 sm:py-16 bg-gray-100 overflow-hidden">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl sm:text-3xl font-bold text-center text-gray-800 mb-8">Discover a Public Venues</h2>
            <!-- The inner flex container now has the animation applied -->
            <div id="card-grid" class="card-grid flex space-x-6 py-4 px-2 -mx-2">
                <!-- Cards will be dynamically added here by JavaScript -->
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-10 md:py-16">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Footer Contact Info -->
                <div class="space-y-4">
                    <p class="text-xl font-bold text-white">Prime Minister's Office</p>
                    <p class="text-sm">
                        <span class="inline-flex items-center space-x-2"><i
                                class="fa-solid fa-map-marker-alt text-lg text-indigo-400"></i> <span>58, Sir Ernest De
                                Silva Mawatha, <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Colombo 07</span></span><br>
                        <span class="inline-flex items-center space-x-2"><i
                                class="fa-solid fa-phone text-lg text-indigo-400"></i> <span>Tel: (+94) 112 575317 / 18,
                                (+94) 112 370737 / 38</span></span><br>
                        <span class="inline-flex items-center space-x-2"><i
                                class="fa-solid fa-fax text-lg text-indigo-400"></i> <span>Fax: (+94) 112 575310, (+94)
                                112 574713</span></span><br>
                        <span class="inline-flex items-center space-x-2"><i
                                class="fa-solid fa-envelope text-lg text-indigo-400"></i> <span>Email:
                                info@pmoffice.gov.lk</span></span>
                    </p>
                </div>

                <!-- Footer Links 1 -->
                <div class="space-y-2">
                    <h4 class="text-lg font-semibold text-white mb-2">Useful Links</h4>
                    <ul class="space-y-1">
                        <li><a href="#" class="hover:text-white transition-colors"><i
                                    class="fa-solid fa-chevron-right text-indigo-400 mr-2"></i> News</a></li>
                        <li><a href="#" class="hover:text-white transition-colors"><i
                                    class="fa-solid fa-chevron-right text-indigo-400 mr-2"></i> Gallery</a></li>
                        <li><a href="#" class="hover:text-white transition-colors"><i
                                    class="fa-solid fa-chevron-right text-indigo-400 mr-2"></i> Contact</a></li>
                        <li><a href="#" class="hover:text-white transition-colors"><i
                                    class="fa-solid fa-chevron-right text-indigo-400 mr-2"></i> RTI</a></li>
                    </ul>
                </div>

                <!-- Footer Links 2 -->
                <div class="space-y-2">
                    <h4 class="text-lg font-semibold text-white mb-2">More Links</h4>
                    <ul class="space-y-1">
                        <li><a href="#" class="hover:text-white transition-colors"><i
                                    class="fa-solid fa-chevron-right text-indigo-400 mr-2"></i> Circulars</a></li>
                        <li><a href="#" class="hover:text-white transition-colors"><i
                                    class="fa-solid fa-chevron-right text-indigo-400 mr-2"></i> Notices</a></li>
                        <li><a href="#" class="hover:text-white transition-colors"><i
                                    class="fa-solid fa-chevron-right text-indigo-400 mr-2"></i> Reports</a></li>
                        <li><a href="#" class="hover:text-white transition-colors"><i
                                    class="fa-solid fa-chevron-right text-indigo-400 mr-2"></i> Public Grievances</a>
                        </li>
                    </ul>
                </div>

                <!-- Footer Logos -->
                <div class="flex flex-col items-center justify-center space-y-4">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/5f/Emblem_of_Sri_Lanka.svg"
                        alt="PM Office Logo" class="h-32 w-auto max-h-[130px] rounded-lg">
                    <img src="https://www.pmoffice.gov.lk/images/emblem.png" alt="GIC Logo"
                        class="h-24 w-auto max-h-[100px] rounded-lg">
                </div>
            </div>

            <div
                class="mt-8 pt-6 border-t border-gray-700 flex flex-col md:flex-row justify-between items-center text-center md:text-left space-y-4 md:space-y-0">
                <div class="text-sm">
                    © <span>Prime Minister's Office | 2025</span><br>
                    <a href="#" class="hover:text-white transition-colors">Developed by ICT Division</a>
                </div>
                <div class="flex space-x-4">
                    <a href="https://www.facebook.com/profile.php?id=61566266050143"
                        class="text-gray-400 hover:text-white transition-colors" target="_blank"><i
                            class="fa-brands fa-facebook-f fa-lg"></i></a>
                    <a href="https://www.youtube.com/@PrimeMinisterMedia16"
                        class="text-gray-400 hover:text-white transition-colors" target="_blank"><i
                            class="fa-brands fa-youtube fa-lg"></i></a>
                    <a href="https://wa.me/+94719997722" class="text-gray-400 hover:text-white transition-colors"
                        target="_blank"><i class="fa-brands fa-whatsapp fa-lg"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Auto-open customer login card if there's a login error from redirect-back
            if (window.hasLoginError) {
                const customerAuthContainer = document.getElementById('customer-auth-container');
                const adminAuthContainer = document.getElementById('admin-auth-container');
                const customerLoginForm = document.getElementById('customer-login-form');
                const customerSignupForm = document.getElementById('customer-signup-form');
                if (customerAuthContainer && adminAuthContainer && customerLoginForm && customerSignupForm) {
                    adminAuthContainer.classList.remove('active');
                    customerAuthContainer.classList.add('active');
                    customerSignupForm.classList.add('hidden');
                    customerLoginForm.classList.remove('hidden');
                }
            }

            // Auto-open admin login card if there's an admin login error
            if (window.hasAdminLoginError) {
                const adminAuthContainer = document.getElementById('admin-auth-container');
                const customerAuthContainer = document.getElementById('customer-auth-container');
                const adminLoginForm = document.getElementById('admin-login-form');
                const adminSignupForm = document.getElementById('admin-signup-form');
                if (adminAuthContainer && customerAuthContainer && adminLoginForm && adminSignupForm) {
                    customerAuthContainer.classList.remove('active');
                    adminAuthContainer.classList.add('active');
                    adminSignupForm.classList.add('hidden');
                    adminLoginForm.classList.remove('hidden');
                }
            }

            // Get references to the authentication card containers
            const customerAuthContainer = document.getElementById('customer-auth-container');
            const adminAuthContainer = document.getElementById('admin-auth-container');

            // Get references to the two distinct password reset modals
            const customerPasswordResetModal = document.getElementById('customer-password-reset-modal');
            const adminPasswordResetModal = document.getElementById('admin-password-reset-modal');

            // Get references to all forms
            const customerLoginForm = document.getElementById('customer-login-form');
            const customerSignupForm = document.getElementById('customer-signup-form');
            const adminLoginForm = document.getElementById('admin-login-form');
            const adminSignupForm = document.getElementById('admin-signup-form');

            // Get all buttons and links that control the cards and modal
            const makeReservationBtn = document.getElementById('make-reservation-btn');
            const searchVenueBtn = document.getElementById('search-venue-btn');
            const closeCustomerBtn = document.getElementById('close-customer-btn');
            const closeAdminBtn = document.getElementById('close-admin-btn');
            const closeCustomerResetBtn = document.getElementById('close-customer-reset-btn');
            const closeAdminResetBtn = document.getElementById('close-admin-reset-btn');
            const openCustomerSignupLink = document.getElementById('open-customer-signup-link');
            const openCustomerLoginLink = document.getElementById('open-customer-login-link');
            const openAdminSignupLink = document.getElementById('open-admin-signup-link');
            const openAdminLoginLink = document.getElementById('open-admin-login-link');
            const forgotPasswordCustomerBtn = document.getElementById('forgot-password-customer-btn');
            const forgotPasswordAdminBtn = document.getElementById('forgot-password-admin-btn');

            // Get the new navigation links
            const navCustomerSignin = document.getElementById('nav-customer-signin');
            const navCustomerRegister = document.getElementById('nav-customer-register');
            const navAdminLogin = document.getElementById('nav-admin-login');
            const navAdminRegister = document.getElementById('nav-admin-register');

            // Get the new mobile navigation links
            const mobileNavCustomerSignin = document.getElementById('mobile-nav-customer-signin');
            const mobileNavCustomerRegister = document.getElementById('mobile-nav-customer-register');
            const mobileNavAdminLogin = document.getElementById('mobile-nav-admin-login');
            const mobileNavAdminRegister = document.getElementById('mobile-nav-admin-register');

            // Get the mobile menu elements
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');
            const closeMobileMenuBtn = document.getElementById('close-mobile-menu-btn');
            const mobileMenuBackdrop = document.getElementById('mobile-menu-backdrop');
            // Get the body element for scroll locking
            const bodyEl = document.body;

            // Get the image grid for the continuous scroll
            const cardGrid = document.getElementById('card-grid');

            // Function to show a specific card container and hide the other
            const showCard = (cardToShow, cardToHide) => {
                cardToHide.classList.remove('active');
                cardToShow.classList.add('active');
            };

            // Function to switch between forms within a container
            const switchForm = (formToShow, formToHide) => {
                formToHide.classList.add('hidden');
                formToShow.classList.remove('hidden');
            };

            // Function to hide all card containers and modals
            const hideAll = () => {
                customerAuthContainer.classList.remove('active');
                adminAuthContainer.classList.remove('active');
                customerPasswordResetModal.classList.add('hidden');
                customerPasswordResetModal.classList.remove('active');
                adminPasswordResetModal.classList.add('hidden');
                adminPasswordResetModal.classList.remove('active');
                closeMobileMenu();
            };

            // Function to open the mobile menu with backdrop
            const openMobileMenu = () => {
                mobileMenu.classList.add('open');
                if (mobileMenuBackdrop) {
                    mobileMenuBackdrop.classList.remove('hidden');
                    mobileMenuBackdrop.classList.add('open');
                }
                bodyEl.style.overflow = 'hidden';
            };

            // Function to close the mobile menu with backdrop
            const closeMobileMenu = () => {
                mobileMenu.classList.remove('open');
                if (mobileMenuBackdrop) {
                    mobileMenuBackdrop.classList.remove('open');
                    setTimeout(() => mobileMenuBackdrop.classList.add('hidden'), 300);
                }
                bodyEl.style.overflow = '';
            };

            // Function to show a specific password reset modal
            const showPasswordResetModal = (modalToShow) => {
                hideAll(); // Hide any active cards and the mobile menu first
                modalToShow.classList.remove('hidden');
                modalToShow.classList.add('active');
            };

            // Event listeners for the main hero buttons
            if (makeReservationBtn) {
                makeReservationBtn.addEventListener('click', () => {
                    showCard(customerAuthContainer, adminAuthContainer);
                    switchForm(customerLoginForm, customerSignupForm); // Switch to login form
                });
            }
            if (searchVenueBtn) {
                searchVenueBtn.addEventListener('click', () => {
                    showCard(adminAuthContainer, customerAuthContainer);
                    switchForm(adminLoginForm, adminSignupForm);
                });
            }

            // Event listeners for the desktop navigation links
            if (navCustomerSignin) {
                navCustomerSignin.addEventListener('click', (e) => {
                    e.preventDefault();
                    showCard(customerAuthContainer, adminAuthContainer);
                    switchForm(customerLoginForm, customerSignupForm);
                });
            }
            if (navCustomerRegister) {
                navCustomerRegister.addEventListener('click', (e) => {
                    e.preventDefault();
                    showCard(customerAuthContainer, adminAuthContainer);
                    switchForm(customerSignupForm, customerLoginForm);
                });
            }
            if (navAdminLogin) {
                navAdminLogin.addEventListener('click', (e) => {
                    e.preventDefault();
                    showCard(adminAuthContainer, customerAuthContainer);
                    switchForm(adminLoginForm, adminSignupForm);
                });
            }
            if (navAdminRegister) {
                navAdminRegister.addEventListener('click', (e) => {
                    e.preventDefault();
                    showCard(adminAuthContainer, customerAuthContainer);
                    switchForm(adminSignupForm, adminLoginForm);
                });
            }

            // Event listeners for the mobile navigation links
            if (mobileNavCustomerSignin) {
                mobileNavCustomerSignin.addEventListener('click', (e) => {
                    e.preventDefault();
                    showCard(customerAuthContainer, adminAuthContainer);
                    switchForm(customerLoginForm, customerSignupForm);
                    closeMobileMenu();
                });
            }
            if (mobileNavCustomerRegister) {
                mobileNavCustomerRegister.addEventListener('click', (e) => {
                    e.preventDefault();
                    showCard(customerAuthContainer, adminAuthContainer);
                    switchForm(customerSignupForm, customerLoginForm);
                    closeMobileMenu();
                });
            }
            if (mobileNavAdminLogin) {
                mobileNavAdminLogin.addEventListener('click', (e) => {
                    e.preventDefault();
                    showCard(adminAuthContainer, customerAuthContainer);
                    switchForm(adminLoginForm, adminSignupForm);
                    closeMobileMenu();
                });
            }
            if (mobileNavAdminRegister) {
                mobileNavAdminRegister.addEventListener('click', (e) => {
                    e.preventDefault();
                    showCard(adminAuthContainer, customerAuthContainer);
                    switchForm(adminSignupForm, adminLoginForm);
                    closeMobileMenu();
                });
            }

            // Handle clicks on close button and backdrop (document level)
            document.addEventListener('click', function (e) {
                const closeBtn = document.getElementById('close-mobile-menu-btn');
                if (closeBtn && (e.target === closeBtn || closeBtn.contains(e.target))) {
                    e.preventDefault();
                    closeMobileMenu();
                    return;
                }
                const backdrop = document.getElementById('mobile-menu-backdrop');
                if (backdrop && (e.target === backdrop || backdrop.contains(e.target))) {
                    e.preventDefault();
                    closeMobileMenu();
                }
            });

            // Event listeners for closing the floating cards
            if (closeCustomerBtn) {
                closeCustomerBtn.addEventListener('click', hideAll);
            }
            if (closeAdminBtn) {
                closeAdminBtn.addEventListener('click', hideAll);
            }
            if (closeCustomerResetBtn) {
                closeCustomerResetBtn.addEventListener('click', () => {
                    customerPasswordResetModal.classList.remove('active');
                    customerPasswordResetModal.classList.add('hidden');
                });
            }
            if (closeAdminResetBtn) {
                closeAdminResetBtn.addEventListener('click', () => {
                    adminPasswordResetModal.classList.remove('active');
                    adminPasswordResetModal.classList.add('hidden');
                });
            }

            // Event listeners for switching between login and signup forms
            if (openCustomerSignupLink) {
                openCustomerSignupLink.addEventListener('click', (e) => {
                    e.preventDefault();
                    switchForm(customerSignupForm, customerLoginForm);
                });
            }
            if (openCustomerLoginLink) {
                openCustomerLoginLink.addEventListener('click', (e) => {
                    e.preventDefault();
                    switchForm(customerLoginForm, customerSignupForm);
                });
            }
            if (openAdminSignupLink) {
                openAdminSignupLink.addEventListener('click', (e) => {
                    e.preventDefault();
                    switchForm(adminSignupForm, adminLoginForm);
                });
            }
            if (openAdminLoginLink) {
                openAdminLoginLink.addEventListener('click', (e) => {
                    e.preventDefault();
                    switchForm(adminLoginForm, adminSignupForm);
                });
            }

            // Event listeners for opening password reset modals
            if (forgotPasswordCustomerBtn) {
                forgotPasswordCustomerBtn.addEventListener('click', () => {
                    // Redirect to dedicated customer login page with full functionality
                    window.location.href = '/customer/login';
                });
            }
            if (forgotPasswordAdminBtn) {
                forgotPasswordAdminBtn.addEventListener('click', () => {
                    showPasswordResetModal(adminPasswordResetModal);
                });
            }

            // Function to toggle password visibility
            const togglePasswordVisibility = (targetId) => {
                const passwordInput = document.getElementById(targetId);
                const icon = document.querySelector(`.toggle-password[data-target="${targetId}"] i`);
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            };

            // Event listeners for password toggle buttons
            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('click', () => {
                    const targetId = button.getAttribute('data-target');
                    togglePasswordVisibility(targetId);
                });
            });


            // Customer Password match validation
            if (document.getElementById('customer-confirm-password')) {
                document.getElementById('customer-confirm-password').addEventListener('input', function () {
                    const passwordField = document.getElementById('customer-signup-password');
                    const confirmPassword = this.value;
                    const errorDiv = document.getElementById('passwordErrorCustomer');
                    if (passwordField && passwordField.value && confirmPassword && passwordField.value !== confirmPassword) {
                        errorDiv.classList.remove('d-none');
                    }
                    else {
                        errorDiv.classList.add('d-none');
                    }
                });
            }
            // Customer registration form submission handling
            if (document.getElementById('CustomerRegitrationForm')) {
                document.getElementById('CustomerRegitrationForm').addEventListener('submit', function (e) {
                    const passwordField = document.getElementById('customer-signup-password');
                    const confirmPassword = document.getElementById('customer-confirm-password')?.value;
                    if (passwordField && passwordField.value && confirmPassword && passwordField.value !== confirmPassword) {
                        e.preventDefault();
                        document.getElementById('passwordErrorCustomer').classList.remove('d-none');
                    }
                });
            }


            // Add this inside the DOMContentLoaded event listener

            // Admin Password match validation
            if (document.getElementById('admin-confirm-password')) {
                document.getElementById('admin-confirm-password').addEventListener('input', function () {
                    const passwordField = document.getElementById('admin-signup-password');
                    const confirmPassword = this.value;
                    const errorDiv = document.getElementById('passwordErrorAdmin');

                    if (passwordField && passwordField.value && confirmPassword && passwordField.value !== confirmPassword) {
                        errorDiv.classList.remove('d-none');
                    } else {
                        errorDiv.classList.add('d-none');
                    }
                });
            }

            // Admin registration form submission handling
            if (document.getElementById('AdminRegitrationForm')) {
                document.getElementById('AdminRegitrationForm').addEventListener('submit', function (e) {
                    const passwordField = document.getElementById('admin-signup-password');
                    const confirmPassword = document.getElementById('admin-confirm-password')?.value;

                    if (passwordField && passwordField.value && confirmPassword && passwordField.value !== confirmPassword) {
                        e.preventDefault();
                        document.getElementById('passwordErrorAdmin').classList.remove('d-none');
                    }
                });
            }


            // Function to dynamically add the venue cards to the scrolling grid
            const populateCardGrid = () => {
                // Check if we have hall data from the server
                if (window.hallData && window.hallData.length > 0) {
                    const cardGrid = document.getElementById('card-grid');

                    // Clear any existing content
                    cardGrid.innerHTML = '';

                    // Create and append the cards to the grid
                    window.hallData.forEach(hall => {
                        // Get the first image from the images array or use a placeholder
                        let imageUrl = "https://placehold.co/300x200/6d28d9/ffffff?text=Image+Not+Found";
                        if (hall.images && hall.images.length > 0) {
                            // Get the first image path
                            // Note: You might need to adjust this path based on your storage setup
                            imageUrl = `/storage/${hall.images[0]}`;
                        }

                        const venueCard = document.createElement('div');
                        venueCard.className = 'flex-none w-64 md:w-80 bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200 flex flex-col';
                        venueCard.innerHTML = `
                <div class="card-image-container h-40 rounded-t-xl bg-purple-700 overflow-hidden flex items-center justify-center">
                    <img src="${imageUrl}" alt="${hall.name} Image" class="w-full h-full object-cover">
                </div>
                <div class="venue-card-content p-4 space-y-2 flex-grow">
                    <h3 class="text-lg font-bold text-gray-800">${hall.name}</h3>
                    <p class="text-sm text-gray-600 flex-grow">${hall.description.substring(0, 100)}${hall.description.length > 100 ? '...' : ''}</p>
                    <div class="flex items-center space-x-4 text-sm text-gray-500 mt-2">
                        <span class="inline-flex items-center space-x-1"><i class="fa-solid fa-users"></i><span>${hall.capacity} Capacity</span></span>
                        <span class="inline-flex items-center space-x-1"><i class="fa-solid fa-location-dot"></i><span>${hall.area}, ${hall.district}</span></span>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-indigo-600 font-bold">Rs. ${hall.price.toLocaleString()}</span>
                        <button class="bg-indigo-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-indigo-700 transition-colors view-details-btn" data-hall-id="${hall.id}">
                            View Details
                        </button>
                    </div>
                </div>
            `;
                        cardGrid.appendChild(venueCard);
                    });

                    // Set up a loop to duplicate the cards for a seamless scrolling effect
                    const originalCards = Array.from(cardGrid.children);
                    originalCards.forEach(card => {
                        cardGrid.appendChild(card.cloneNode(true));
                    });

                    // Add event listeners to the "View Details" buttons
                    document.querySelectorAll('.view-details-btn').forEach(button => {
                        button.addEventListener('click', () => {
                            const hallId = button.getAttribute('data-hall-id');
                            // Redirect to hall details page
                            window.location.href = `customer/halls/${hallId}`;
                        });
                    });
                } else {
                    // Fallback to dummy data if no hall data is available
                    const venues = [
                        {
                            name: "Nelum Pokuna Theatre",
                            description: "State-of-the-art performing arts venue, Colombo",
                            capacity: "1200 Capacity",
                            location: "Colombo",
                            image: "https://placehold.co/300x200/6d28d9/ffffff?text=Image+Not+Found"
                        },
                        {
                            name: "BMICH Main Hall",
                            description: "Iconic convention center and conference hall, Colombo",
                            capacity: "1500 Capacity",
                            location: "Colombo",
                            image: "https://placehold.co/300x200/6d28d9/ffffff?text=Image+Not+Found"
                        },
                        {
                            name: "Cinnamon Grand Hall",
                            description: "Luxurious ballroom for high-end events, Colombo",
                            capacity: "1000 Capacity",
                            location: "Colombo",
                            image: "https://placehold.co/300x200/6d28d9/ffffff?text=Image+Not+Found"
                        },
                        {
                            name: "Waters Edge Grand Ballroom",
                            description: "Elegant venue with scenic views, Battaramulla",
                            capacity: "800 Capacity",
                            location: "Battaramulla",
                            image: "https://placehold.co/300x200/6d28d9/ffffff?text=Image+Not+Found"
                        },
                        {
                            name: "Mahaweli Main Hall",
                            description: "Multi-purpose hall with modern facilities, Colombo",
                            capacity: "800 Capacity",
                            location: "Colombo",
                            image: "https://placehold.co/300x200/6d28d9/ffffff?text=Image+Not+Found"
                        }
                    ];

                    const cardGrid = document.getElementById('card-grid');
                    cardGrid.innerHTML = '';

                    venues.forEach(venue => {
                        const venueCard = document.createElement('div');
                        venueCard.className = 'flex-none w-64 md:w-80 bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200 flex flex-col';
                        venueCard.innerHTML = `
                <div class="card-image-container h-40 rounded-t-xl bg-purple-700 overflow-hidden flex items-center justify-center">
                    <img src="${venue.image}" alt="${venue.name} Image" class="w-full h-full object-cover">
                </div>
                <div class="venue-card-content p-4 space-y-2 flex-grow">
                    <h3 class="text-lg font-bold text-gray-800">${venue.name}</h3>
                    <p class="text-sm text-gray-600 flex-grow">${venue.description}</p>
                    <div class="flex items-center space-x-4 text-sm text-gray-500 mt-2">
                        <span class="inline-flex items-center space-x-1"><i class="fa-solid fa-users"></i><span>${venue.capacity}</span></span>
                        <span class="inline-flex items-center space-x-1"><i class="fa-solid fa-location-dot"></i><span>${venue.location}</span></span>
                    </div>
                    <button class="w-full bg-indigo-600 text-white font-semibold py-2 rounded-lg hover:bg-indigo-700 transition-colors mt-auto">
                        View Details
                    </button>
                </div>
            `;
                        cardGrid.appendChild(venueCard);
                    });

                    // Set up a loop to duplicate the cards for a seamless scrolling effect
                    const originalCards = Array.from(cardGrid.children);
                    originalCards.forEach(card => {
                        cardGrid.appendChild(card.cloneNode(true));
                    });
                }
            };
            // Call the function to populate the card grid on page load
            populateCardGrid();

            // Hall Search Functionality
            const searchInput = document.getElementById('hall-search');
            const searchResults = document.getElementById('search-results');
            let searchTimeout;

            if (searchInput && searchResults) {
                searchInput.addEventListener('input', function () {
                    clearTimeout(searchTimeout);
                    const query = this.value.trim();

                    if (query.length < 2) {
                        searchResults.classList.add('hidden');
                        return;
                    }

                    // Debounce search
                    searchTimeout = setTimeout(() => {
                        searchHalls(query);
                    }, 300);
                });

                // Close search results when clicking outside
                document.addEventListener('click', function (e) {
                    if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                        searchResults.classList.add('hidden');
                    }
                });
            }

            function searchHalls(query) {
                const halls = window.hallData || [];
                console.log('Searching in halls:', halls);
                console.log('Search query:', query);
                const filtered = halls.filter(hall => {
                    const searchText = query.toLowerCase();
                    return hall.name.toLowerCase().includes(searchText) ||
                        hall.type.toLowerCase().includes(searchText) ||
                        hall.area.toLowerCase().includes(searchText) ||
                        hall.district.toLowerCase().includes(searchText) ||
                        hall.province.toLowerCase().includes(searchText);
                });
                console.log('Filtered results:', filtered);

                displaySearchResults(filtered, query);
            }

            function displaySearchResults(halls, query) {
                const searchResults = document.getElementById('search-results');

                if (halls.length === 0) {
                    searchResults.innerHTML = `
                        <div class="p-4 text-center text-gray-500">
                            <i class="fas fa-search text-3xl mb-2"></i>
                            <p>No halls found for "${query}"</p>
                        </div>
                    `;
                    searchResults.classList.remove('hidden');
                    return;
                }

                const resultsHTML = halls.map((hall, index) => {
                    const images = hall.images && hall.images.length > 0
                        ? hall.images.map(img => `/storage/${img}`)
                        : ['https://placehold.co/400x60/6d28d9/ffffff?text=No+Image'];

                    const imageSliderHTML = images.length > 1 ? `
                        <div class="relative w-32 h-16 overflow-hidden rounded-lg group">
                            ${images.map((img, imgIndex) => `
                                <img src="${img}" alt="${hall.name}" 
                                     class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500 hall-image-${index}" 
                                     style="opacity: ${imgIndex === 0 ? '1' : '0'};">
                            `).join('')}
                            <div class="absolute bottom-1 left-1/2 transform -translate-x-1/2 flex space-x-1">
                                ${images.map((_, dotIndex) => `
                                    <div class="w-1.5 h-1.5 rounded-full bg-white ${dotIndex === 0 ? 'opacity-100' : 'opacity-50'} dot-${index}-${dotIndex}"></div>
                                `).join('')}
                            </div>
                        </div>
                    ` : `
                        <div class="w-32 h-16 overflow-hidden rounded-lg">
                            <img src="${images[0]}" alt="${hall.name}" class="w-full h-full object-cover">
                        </div>
                    `;

                    return `
                        <div class="flex items-center p-2 hover:bg-gray-50 cursor-pointer border-b border-gray-100 search-result-item" data-hall-id="${hall.id}">
                            ${imageSliderHTML}
                            <div class="flex-grow ml-3">
                                <h4 class="font-semibold text-gray-800 text-sm">${hall.name} <span class="text-gray-500 font-normal">(${hall.type.charAt(0).toUpperCase() + hall.type.slice(1)})</span></h4>
                                <p class="text-sm font-semibold text-indigo-600">
                                    LKR ${parseFloat(hall.price).toLocaleString()} / hr
                                </p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </div>
                    `;
                }).join('');

                searchResults.innerHTML = resultsHTML;
                searchResults.classList.remove('hidden');

                // Initialize image sliders
                halls.forEach((hall, index) => {
                    if (hall.images && hall.images.length > 1) {
                        let currentImageIndex = 0;
                        const images = document.querySelectorAll(`.hall-image-${index}`);
                        const dots = document.querySelectorAll(`[class*="dot-${index}-"]`);

                        setInterval(() => {
                            // Hide current image
                            images[currentImageIndex].style.opacity = '0';
                            dots[currentImageIndex].classList.remove('opacity-100');
                            dots[currentImageIndex].classList.add('opacity-50');

                            // Move to next image
                            currentImageIndex = (currentImageIndex + 1) % images.length;

                            // Show next image
                            images[currentImageIndex].style.opacity = '1';
                            dots[currentImageIndex].classList.remove('opacity-50');
                            dots[currentImageIndex].classList.add('opacity-100');
                        }, 3000);
                    }
                });

                // Add click handlers to results
                document.querySelectorAll('.search-result-item').forEach(item => {
                    item.addEventListener('click', function () {
                        const hallId = this.getAttribute('data-hall-id');
                        window.location.href = `customer/halls/${hallId}`;
                    });
                });
            }
        });






        // To eliminate 419|page expired and Sync CSRF token across tabs
        (function () {
            const currentToken = document.querySelector('meta[name="csrf-token"]').content;

            // Store the initial token in localStorage
            localStorage.setItem('csrf-token', currentToken);

            // Listen for changes to the CSRF token in other tabs
            window.addEventListener('storage', function (e) {
                if (e.key === 'csrf-token' && e.newValue !== currentToken) {
                    document.querySelector('meta[name="csrf-token"]').content = e.newValue;

                    // Update all forms with the new token
                    document.querySelectorAll('input[name="_token"]').forEach(input => {
                        input.value = e.newValue;
                    });
                }
            });

            // Update token when navigating back to page
            window.addEventListener('pageshow', function (e) {
                const storedToken = localStorage.getItem('csrf-token');
                if (storedToken && storedToken !== document.querySelector('meta[name="csrf-token"]').content) {
                    document.querySelector('meta[name="csrf-token"]').content = storedToken;

                    // Update all forms with the new token
                    document.querySelectorAll('input[name="_token"]').forEach(input => {
                        input.value = storedToken;
                    });
                }
            });
        });













    </script>
</body>

</html>

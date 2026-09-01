<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hall Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --primary-light: #eef2ff;
            --success: #10b981;
            --success-light: #d1fae5;
            --warning: #f59e0b;
            --warning-light: #fef3c7;
            --danger: #ef4444;
            --danger-light: #fee2e2;
            --info: #06b6d4;
            --dark: #1e293b;
            --gray: #64748b;
            --gray-light: #f8fafc;
            --white: #ffffff;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
            --shadow: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -2px rgba(0,0,0,0.05);
            --shadow-lg: 0 10px 25px -3px rgba(0,0,0,0.08), 0 4px 6px -4px rgba(0,0,0,0.04);
            --radius: 12px;
            --radius-sm: 8px;
            --radius-xs: 6px;
            --transition: all 0.2s ease;
        }

        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background: var(--gray-light);
            color: var(--dark);
        }

        /* ===== Navbar ===== */
        .navbar-venues {
            background: var(--white) !important;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 1.5rem !important;
            box-shadow: var(--shadow-sm);
        }
        .navbar-venues .navbar-brand {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--dark);
            letter-spacing: -0.3px;
        }
        .navbar-venues .navbar-brand i {
            color: var(--primary);
            margin-right: 10px;
        }
        .navbar-venues .nav-user {
            font-weight: 500;
            font-size: 0.9rem;
            color: var(--gray);
            display: flex;
            align-items: center;
            gap: 8px;
            margin-right: 12px;
        }
        .navbar-venues .nav-user i {
            font-size: 1.2rem;
            color: var(--primary);
        }
        .btn-dashboard {
            background: var(--primary-light) !important;
            color: var(--primary) !important;
            border: none !important;
            border-radius: var(--radius-xs) !important;
            padding: 0.45rem 1rem !important;
            font-weight: 600 !important;
            font-size: 0.85rem !important;
            transition: var(--transition) !important;
        }
        .btn-dashboard:hover {
            background: var(--primary) !important;
            color: #fff !important;
        }
        .btn-logout-venues {
            background: transparent !important;
            border: 1.5px solid #e2e8f0 !important;
            color: var(--danger) !important;
            border-radius: var(--radius-xs) !important;
            padding: 0.45rem 1rem !important;
            font-weight: 500 !important;
            font-size: 0.85rem !important;
            transition: var(--transition) !important;
        }
        .btn-logout-venues:hover {
            background: var(--danger-light) !important;
            border-color: var(--danger) !important;
        }

        /* ===== Filter Panel ===== */
        .filter-panel {
            background: var(--white);
            border-right: 1px solid #e2e8f0;
            height: calc(100vh - 80px);
            overflow-y: auto;
            position: sticky;
            top: 78px;
            padding: 1.5rem 1.25rem !important;
            box-shadow: var(--shadow-sm);
        }
        .filter-panel::-webkit-scrollbar {
            width: 4px;
        }
        .filter-panel::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .filter-panel .filter-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .filter-panel .filter-header h5 {
            font-weight: 700;
            font-size: 1rem;
            color: var(--dark);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .filter-panel .filter-header h5 i {
            color: var(--primary);
        }
        .filter-panel .filter-section {
            margin-bottom: 1.25rem;
        }
        .filter-panel .filter-section label.form-label {
            font-weight: 600;
            font-size: 0.8rem;
            color: var(--dark);
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 0.4rem;
        }
        .btn-reset {
            background: transparent;
            border: 1.5px solid #e2e8f0;
            color: var(--gray);
            border-radius: var(--radius-xs);
            padding: 0.3rem 0.8rem;
            font-size: 0.8rem;
            font-weight: 500;
            transition: var(--transition);
        }
        .btn-reset:hover {
            background: var(--gray-light);
            border-color: var(--gray);
            color: var(--dark);
        }
        .filter-panel .form-control,
        .filter-panel .form-select {
            border-radius: var(--radius-xs);
            border: 1.5px solid #e2e8f0;
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
            transition: var(--transition);
        }
        .filter-panel .form-control:focus,
        .filter-panel .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79,70,229,0.1);
        }
        .btn-apply-filter {
            background: var(--primary) !important;
            border: none !important;
            border-radius: var(--radius-sm) !important;
            padding: 0.6rem !important;
            font-weight: 600 !important;
            font-size: 0.9rem !important;
            transition: var(--transition) !important;
            margin-top: 0.25rem;
        }
        .btn-apply-filter:hover {
            background: var(--primary-dark) !important;
            box-shadow: 0 4px 10px rgba(79,70,229,0.25);
        }

        /* Price Range */
        .form-range::-webkit-slider-runnable-track {
            height: 6px;
            background: #e2e8f0;
            border-radius: 4px;
        }
        .form-range::-webkit-slider-thumb {
            background: var(--primary);
            width: 18px;
            height: 18px;
            border-radius: 50%;
            margin-top: -6px;
            box-shadow: 0 2px 4px rgba(79,70,229,0.2);
        }
        .price-range-labels {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
            color: var(--gray);
            margin-top: 0.25rem;
        }
        .price-range-labels .price-value {
            font-weight: 600;
            color: var(--primary);
        }

        /* ===== Main Content ===== */
        .main-content {
            height: calc(100vh - 78px);
            overflow-y: auto;
            background: var(--gray-light);
            padding-bottom: 2rem;
        }
        .main-content::-webkit-scrollbar {
            width: 4px;
        }
        .main-content::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        /* ===== Hall Cards ===== */
        .hall-card {
            transition: var(--transition);
            border: 1px solid #e2e8f0;
            border-radius: var(--radius);
            overflow: hidden;
            background: var(--white);
            box-shadow: var(--shadow-sm);
        }
        .hall-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-lg);
            border-color: transparent;
        }
        .hall-card .card-body {
            padding: 1.25rem;
        }
        .hall-card .card-title {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--dark);
        }
        .hall-card .type-badge {
            background: var(--primary-light) !important;
            color: var(--primary) !important;
            font-weight: 600 !important;
            font-size: 0.75rem !important;
            padding: 0.3rem 0.7rem !important;
            border-radius: 20px !important;
        }

        .price-tag {
            font-size: 1.3rem;
            color: var(--danger);
            font-weight: 700;
        }

        .hall-card .info-text {
            font-size: 0.85rem;
            color: var(--gray);
        }
        .hall-card .info-text i {
            width: 16px;
            color: var(--primary);
            margin-right: 4px;
        }

        .facility-badge {
            margin: 2px;
            white-space: normal;
            background: #f1f5f9 !important;
            color: var(--dark) !important;
            font-weight: 500 !important;
            font-size: 0.75rem !important;
            padding: 0.3rem 0.7rem !important;
            border-radius: 20px !important;
        }

        .hall-card .btn-outline-primary {
            border: 1.5px solid var(--primary) !important;
            color: var(--primary) !important;
            border-radius: var(--radius-xs) !important;
            font-weight: 600 !important;
            font-size: 0.82rem !important;
            padding: 0.45rem !important;
            transition: var(--transition) !important;
        }
        .hall-card .btn-outline-primary:hover {
            background: var(--primary) !important;
            color: #fff !important;
        }
        .hall-card .btn-outline-success {
            border: 1.5px solid var(--success) !important;
            color: var(--success) !important;
            border-radius: var(--radius-xs) !important;
            font-weight: 600 !important;
            font-size: 0.82rem !important;
            padding: 0.45rem !important;
            transition: var(--transition) !important;
        }
        .hall-card .btn-outline-success:hover {
            background: var(--success) !important;
            color: #fff !important;
        }

        /* Image Section */
        .hall-card .image-wrapper {
            width: 120px;
            height: 140px;
            border-radius: var(--radius-sm);
            overflow: hidden;
            flex-shrink: 0;
            position: relative;
            background: #f1f5f9;
        }

        /* ===== Pagination ===== */
        .pagination-custom .page-link {
            border-radius: var(--radius-xs) !important;
            margin: 0 2px;
            color: var(--dark);
            font-weight: 500;
            border: 1.5px solid #e2e8f0;
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
        }
        .pagination-custom .page-item.active .page-link {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }
        .pagination-custom .page-link:hover {
            background: var(--primary-light);
            border-color: var(--primary);
            color: var(--primary-dark);
        }

        /* ===== Empty State ===== */
        .empty-state {
            background: var(--white);
            border: 1px solid #e2e8f0;
            border-radius: var(--radius);
            padding: 3rem 2rem;
            text-align: center;
        }
        .empty-state i {
            font-size: 2.5rem;
            color: var(--gray);
            margin-bottom: 1rem;
        }
        .empty-state p {
            font-size: 1rem;
            color: var(--gray);
            margin: 0;
        }

        /* ===== Responsive ===== */
        @media (max-width: 768px) {
            .filter-panel {
                position: static;
                height: auto;
                border-right: none;
                border-bottom: 1px solid #e2e8f0;
            }
            .navbar-venues .nav-user span {
                display: none;
            }
            .hall-card .image-wrapper {
                width: 100px;
                height: 120px;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-venues shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-building"></i> Hall Booking System
            </a>
            <div class="d-flex align-items-center gap-2">
                <span class="nav-user">
                    <i class="fas fa-user-circle"></i>
                    <span>{{ auth()->guard('customer')->user()->first_name }} {{ auth()->guard('customer')->user()->last_name }}</span>
                </span>

                <form method="POST" action="{{ route('logout_route') }}" class="mb-0 d-flex gap-2">
                    @csrf
                    <a href="{{ route('load_customer_dashboard') }}" class="btn btn-dashboard">
                        <i class="fas fa-th-large me-1"></i> Dashboard
                    </a>
                    <button type="submit" class="btn btn-logout-venues">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Filter Panel -->
            <div class="col-md-3 col-lg-2 filter-panel">
                <div class="filter-header">
                    <h5><i class="fas fa-sliders-h"></i>Filter Halls</h5>
                    <a href="{{ route('load_venues_page') }}" class="btn-reset">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
                <form method="GET" action="{{ route('load_venues_page') }}">
                    <!-- Search Filter -->
                    <div class="filter-section">
                        <label class="form-label">Search by Name</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Enter hall name..." value="{{ $request->search }}">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Price Filter -->
                    <div class="filter-section">
                        <label class="form-label">Max Price</label>
                        <input type="range" class="form-range" min="0" max="{{ $maxPrice ?? 100000 }}" step="100" name="max_price" value="{{ $request->max_price ?? $maxPrice ?? 100000 }}">
                        <div class="price-range-labels">
                            <small>0</small>
                            <small>LKR <span class="price-value" id="priceDisplay">{{ number_format($request->max_price ?? $maxPrice ?? 100000) }}</span></small>
                        </div>
                    </div>

                    <!-- Capacity Filter -->
                    <div class="filter-section">
                        <label class="form-label">Min Capacity</label>
                        <input type="number" class="form-control" name="min_capacity" value="{{ $request->min_capacity }}">
                    </div>

                    <!-- Type Filter -->
                    <div class="filter-section">
                        <label class="form-label">Hall Type</label>
                        <select class="form-select" name="type">
                            <option value="">All Types</option>
                            @foreach($types as $type)
                            <option value="{{ $type }}" {{ $request->type == $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Location Filters -->
                    <div class="filter-section">
                        <label class="form-label">Province</label>
                        <select class="form-select" name="province">
                            <option value="">All Provinces</option>
                            @foreach($provinces as $province)
                            <option value="{{ $province }}" {{ $request->province == $province ? 'selected' : '' }}>
                                {{ $province }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-section">
                        <label class="form-label">District</label>
                        <select class="form-select" name="district">
                            <option value="">All Districts</option>
                            @foreach($districts as $district)
                            <option value="{{ $district }}" {{ $request->district == $district ? 'selected' : '' }}>
                                {{ $district }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-section">
                        <label class="form-label">Area</label>
                        <select class="form-select" name="area">
                            <option value="">All Areas</option>
                            @foreach($areas as $area)
                            <option value="{{ $area }}" {{ $request->area == $area ? 'selected' : '' }}>
                                {{ $area }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Facilities Filter -->
                    <div class="filter-section">
                        <label class="form-label">Facilities</label>
                        <div class="row row-cols-2 g-2">
                            @foreach($facilities as $facility)
                                <div class="col">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="facilities[]" value="{{ $facility }}" {{ in_array($facility, $request->facilities ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label small">
                                            {{ $facility }} <!-- Removed: ucfirst(str_replace('_', ' ', $facility)) -->
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Date/Time Filter -->
                    <div class="filter-section">
                        <label class="form-label">Available Date</label>
                        <input type="date" class="form-control" name="available_date" min="{{ now()->format('Y-m-d') }}" value="{{ $request->available_date }}">
                    </div>

                    <div class="row g-2 filter-section">
                        <div class="col">
                            <label class="form-label">From Time</label>
                            <input type="time" class="form-control" name="start_time" value="{{ $request->start_time }}">
                        </div>
                        <div class="col">
                            <label class="form-label">To Time</label>
                            <input type="time" class="form-control" name="end_time" value="{{ $request->end_time }}">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-apply-filter w-100"><i class="fas fa-search me-2"></i>Apply Filters</button>

                </form>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 p-3">
                    @forelse($halls as $hall)
                    <div class="col">
                        <div class="card h-100 hall-card">
                            @php
                                $images = is_array($hall->images) ? $hall->images : json_decode($hall->images, true);
                                $firstImage = !empty($images) ? asset('storage/' . $images[0]) : 'https://placehold.co/600x200/6d28d9/ffffff?text=No+Image';
                            @endphp
                            <div class="card-body">
                                <div class="d-flex gap-3">
                                    <!-- Image Section -->
                                    <div class="image-wrapper">
                                        @if(!empty($images) && count($images) > 1)
                                            @foreach($images as $index => $image)
                                                <img src="{{ asset('storage/' . $image) }}" 
                                                     class="position-absolute w-100 h-100 hall-slide-{{ $hall->id }}" 
                                                     alt="{{ $hall->name }}"
                                                     style="object-fit: cover; opacity: {{ $index === 0 ? '1' : '0' }}; transition: opacity 0.5s;">
                                            @endforeach
                                            <div class="position-absolute bottom-0 start-50 translate-middle-x mb-1 d-flex gap-1">
                                                @foreach($images as $index => $image)
                                                    <div class="rounded-circle bg-white slide-dot-{{ $hall->id }}-{{ $index }}" 
                                                         style="width: 5px; height: 5px; opacity: {{ $index === 0 ? '1' : '0.5' }};"></div>
                                                @endforeach
                                            </div>
                                        @else
                                            <img src="{{ $firstImage }}" class="w-100 h-100" alt="{{ $hall->name }}" style="object-fit: cover;">
                                        @endif
                                    </div>

                                    <!-- Content Section -->
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="card-title mb-0">{{ $hall->name }}</h5>
                                            <span class="badge type-badge">{{ $hall->type }}</span>
                                        </div>

                                        <div class="mb-2">
                                            <span class="price-tag">LKR {{ number_format($hall->price, 2) }}</span>
                                            <small class="text-muted">/ Hr</small>
                                        </div>

                                        <div class="mb-2">
                                            <p class="small mb-1">
                                                <i class="fas fa-users me-1"></i>Capacity: {{ $hall->capacity }}
                                            </p>
                                            <p class="small mb-0">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                {{ $hall->area }}, {{ $hall->district }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="facilities mb-3">
                                    @foreach(array_slice($hall->facilities ?? [], 0, 3) as $facility)
                                    <span class="badge bg-secondary facility-badge">
                                        {{ ucfirst(str_replace('_', ' ', $facility)) }}
                                    </span>
                                    @endforeach
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="{{ route('halls.show', $hall->id) }}" class="btn btn-outline-primary flex-fill">
                                        View Details
                                    </a>
                                    <a href="{{ route('customer.calendar', ['hall_id' => $hall->id]) }}" class="btn btn-outline-success flex-fill">
                                        View Calendar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="empty-state">
                            <i class="fas fa-search"></i>
                            <p>No halls found matching your criteria</p>
                        </div>
                    </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-5">
                    {{ $halls->links() }}
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Price Range Display
        const priceRange = document.querySelector('input[name="max_price"]');
        const priceDisplay = document.getElementById('priceDisplay');

        if (priceRange) {
            priceRange.addEventListener('input', function() {
                priceDisplay.textContent = this.value.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            });
        }

        // Time Validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const startTime = document.querySelector('input[name="start_time"]').value;
            const endTime = document.querySelector('input[name="end_time"]').value;

            if (startTime && endTime && startTime >= endTime) {
                e.preventDefault();
                alert('End time must be after start time');
            }
        });

        // Image Slider for Hall Cards
        @foreach($halls as $hall)
            @php
                $images = is_array($hall->images) ? $hall->images : json_decode($hall->images, true);
            @endphp
            @if(!empty($images) && count($images) > 1)
                (function() {
                    let currentIndex = 0;
                    const images = document.querySelectorAll('.hall-slide-{{ $hall->id }}');
                    const dots = document.querySelectorAll('[class*="slide-dot-{{ $hall->id }}-"]');
                    const totalImages = images.length;

                    setInterval(() => {
                        // Hide current
                        images[currentIndex].style.opacity = '0';
                        dots[currentIndex].style.opacity = '0.5';

                        // Move to next
                        currentIndex = (currentIndex + 1) % totalImages;

                        // Show next
                        images[currentIndex].style.opacity = '1';
                        dots[currentIndex].style.opacity = '1';
                    }, 3000);
                })();
            @endif
        @endforeach
    </script>
</body>

</html>
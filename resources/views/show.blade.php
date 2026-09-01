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
            --info: #06b6d4;
            --dark: #1e293b;
            --gray: #64748b;
            --gray-light: #f8fafc;
            --white: #ffffff;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
            --shadow: 0 4px 6px -1px rgba(0,0,0,0.07);
            --shadow-lg: 0 10px 25px -3px rgba(0,0,0,0.08);
            --radius: 12px;
            --radius-sm: 8px;
            --radius-xs: 6px;
            --transition: all 0.2s ease;
        }
        * { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
        body { background: var(--gray-light); color: var(--dark); padding: 0 !important; }
        .navbar-show {
            background: var(--white) !important;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.65rem 1.5rem !important;
            box-shadow: var(--shadow-sm);
        }
        .navbar-show .navbar-brand { font-weight: 700; font-size: 1.1rem; color: var(--dark); letter-spacing: -0.3px; }
        .navbar-show .navbar-brand i { color: var(--primary); margin-right: 10px; }
        .navbar-show .nav-user { font-weight: 500; font-size: 0.9rem; color: var(--gray); display: flex; align-items: center; gap: 8px; margin-right: 12px; }
        .navbar-show .nav-user i { font-size: 1.2rem; color: var(--primary); }
        .btn-dashboard-show {
            background: var(--primary-light) !important; color: var(--primary) !important; border: none !important;
            border-radius: var(--radius-xs) !important; padding: 0.4rem 1rem !important; font-weight: 600 !important;
            font-size: 0.85rem !important; transition: var(--transition) !important; text-decoration: none;
        }
        .btn-dashboard-show:hover { background: var(--primary) !important; color: #fff !important; }
        .btn-logout-show {
            background: transparent !important; border: 1.5px solid #e2e8f0 !important; color: var(--danger,#ef4444) !important;
            border-radius: var(--radius-xs) !important; padding: 0.4rem 1rem !important; font-weight: 500 !important;
            font-size: 0.85rem !important; transition: var(--transition) !important;
        }
        .btn-logout-show:hover { background: #fee2e2 !important; border-color: #ef4444 !important; }

        /* Base card refinement */
        .card { border-radius: var(--radius); border: 1px solid #e2e8f0; background: var(--white); }
        .card-header { border-bottom: 1px solid #e2e8f0; font-weight: 600; }
        .card.bg-success .card-header, .card.bg-info .card-header { border-bottom: 1px solid rgba(255,255,255,0.15); }

        /* Facility items */
        .facility-item { padding: 0.65rem 1rem; margin-bottom: 0.5rem; border-radius: var(--radius-xs); background: #f8fafc; border: 1px solid #e2e8f0; }

        /* Package cards */
        .package-card { cursor: default; border: 2px solid #e2e8f0; transition: var(--transition); }
        .package-card:hover { border-color: var(--primary); }
        .package-card.selected { border-color: var(--primary); background: var(--primary-light); }

        /* Price and total displays */
        .charge-label { font-weight: 600; font-size: 0.95rem; color: var(--dark); }
        .charge-amount { font-weight: 700; color: var(--dark); }
        .total-charge-box { background: var(--primary) !important; color: #fff; border-radius: var(--radius-sm); }
        .total-charge-box h5 { font-weight: 700; }
        .total-charge-box .charge-amount { color: #fff; }

        /* Image slider */
        .image-slider { height: 500px; overflow: hidden; border-radius: var(--radius); box-shadow: var(--shadow); }
        .slider-wrapper { position: relative; height: 100%; }
        .slide { position: absolute; opacity: 0; transition: opacity 0.5s ease-in-out; width: 100%; height: 100%; }
        .slide.active { opacity: 1; }
        .slider-arrow {
            position: absolute; top: 50%; transform: translateY(-50%);
            background: rgba(255,255,255,0.9); border: none; width: 40px; height: 40px;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            box-shadow: var(--shadow); cursor: pointer; transition: var(--transition);
        }
        .slider-arrow:hover { background: #fff; box-shadow: var(--shadow-lg); }
        .prev-arrow { left: 20px; }
        .next-arrow { right: 20px; }

        /* Time slot labels */
        .time-slot-label { position: relative; overflow: hidden; flex: 1 0 45%; white-space: nowrap; }
        .time-slot-label.active { background-color: var(--primary); color: #fff; border-color: var(--primary); }
        .time-slot-label:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .time-slot-label input[type="radio"]:checked+span { background-color: var(--bs-success); color: #fff; }

        /* Buttons */
        .btn-primary { background: var(--primary) !important; border: none !important; color: #fff !important; }
        .btn-primary:hover { background: var(--primary-dark) !important; color: #fff !important; }

        /* Total charge boxes */
        .bg-primary { background-color: var(--primary-light) !important; color: var(--primary) !important; }

        /* Facility section headers */
        .facility-header { background: var(--primary-light) !important; color: var(--primary) !important; border-bottom: 1px solid #e2e8f0 !important; }

        /* Open map */
        .open-map-btn { display: inline-flex; align-items: center; padding: 0.5rem 1.2rem; font-weight: 600; border-radius: var(--radius-xs); transition: var(--transition); background: var(--primary) !important; border: none !important; color: #fff !important; }
        .open-map-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(79,70,229,0.25); }

        /* Status alerts */
        #customTimeStatusRegular.alert-success, #customTimeStatusPackage.alert-success { border-left: 4px solid var(--success); }
        #customTimeStatusRegular.alert-danger, #customTimeStatusPackage.alert-danger { border-left: 4px solid var(--danger,#ef4444); }
        #customTimeStatusRegular.alert-info, #customTimeStatusPackage.alert-info { border-left: 4px solid var(--info); }

        @media (max-width: 768px) { .image-slider { height: 300px; } }
        @media (max-width: 576px) { .navbar-show .nav-user span { display: none; } }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-show">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('load_customer_dashboard') }}">
                <i class="fas fa-building"></i> Hall Booking System
            </a>
            <div class="d-flex align-items-center gap-2">
                <span class="nav-user">
                    <i class="fas fa-user-circle"></i>
                    <span>{{ auth()->guard('customer')->user()->first_name }} {{ auth()->guard('customer')->user()->last_name }}</span>
                </span>
                <form method="POST" action="{{ route('logout_route') }}" class="mb-0 d-flex gap-2">
                    @csrf
                    <a href="{{ route('load_customer_dashboard') }}" class="btn-dashboard-show">
                        <i class="fas fa-th-large me-1"></i> Dashboard
                    </a>
                    <button type="submit" class="btn-logout-show">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>
    <div class="container py-4">
        <!-- Image Slider Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="image-slider position-relative">
                    @php
$images = $hall->images ?? [];
                    @endphp

                    @if(count($images) > 0)
                        <div class="slider-wrapper">
                            @foreach($images as $key => $image)
                                <div class="slide {{ $key === 0 ? 'active' : '' }}">
                                    <img src="{{ asset('storage/' . $image) }}" class="img-fluid rounded-3 img-responsive"
                                        alt="Hall image {{ $key + 1 }}" style="width: 100%; min-height: 400px;">
                                </div>
                            @endforeach
                        </div>

                        @if(count($images) > 1)
                            <!-- Navigation Arrows -->
                            <button class="slider-arrow prev-arrow" aria-label="Previous image">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="slider-arrow next-arrow" aria-label="Next image">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        @endif
                    @else
                        <div class="no-image-placeholder bg-light rounded-3 d-flex align-items-center justify-content-center"
                            style="height: 400px;">
                            <i class="fas fa-image fa-4x text-muted"></i>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- Main Content Section -->
    <div class="row g-4">
        <!-- Left Column - Booking & Availability -->
        <div class="col-lg-6">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-body">
                    <!-- Booking Method Selection -->
                    @if($hall->booking_method === 'both')
                    <div class="mb-4">
                        <div class="card">
                            <div class="card-header facility-header">
                                <h5 class="mb-0">Booking Method</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bookingMethod"
                                        id="regularBooking" value="regular" checked>
                                    <label class="form-check-label" for="regularBooking">
                                        Regular Booking
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bookingMethod"
                                        id="packageBooking" value="package">
                                    <label class="form-check-label" for="packageBooking">
                                        Package Booking
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <h4 class="mb-4">Availability</h4>

                    <!-- Error Messages -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- REGULAR BOOKING FORM -->
                    <form method="POST" action="{{ route('reservation.request.regular.route', $hall->id) }}" id="regularForm" class="{{ $hall->booking_method === 'package' ? 'hidden' : '' }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Date Selection (Regular Only) -->
                        <div class="mb-4">
                            <label class="form-label">Select Date</label>
                            <input type="date" name="selected_date" id="datePickerRegular" class="form-control"
                                min="{{ now()->format('Y-m-d') }}" max="{{ now()->addDays(180)->format('Y-m-d') }}"
                                required>
                            <small class="form-text text-muted">Select a date between today and
                                {{ now()->addDays(180)->format('M d, Y') }}</small>
                        </div>

                        <!-- Time Slots (Regular Only) -->
                        <div class="mb-4">
                            <h5 class="mb-3">Available Time Slots</h5>
                            <div id="timeSlotsContainerRegular" class="d-flex flex-wrap gap-2">
                                <div class="alert alert-info w-100">
                                    <i class="fas fa-info-circle me-2"></i> Please select a date to see available time slots
                                </div>
                            </div>
                        </div>

                        <!-- Custom Time Selection (Regular Only) -->
                        <div class="mb-4">
                            <h5 class="mb-3">Or Select Custom Time</h5>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label">Start Time</label>
                                    <select class="form-select" id="customStartTimeRegular" disabled>
                                        <option value="">Select time</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">End Time</label>
                                    <select class="form-select" id="customEndTimeRegular" disabled>
                                        <option value="">Select time</option>
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div id="customTimeStatusRegular" class="mt-2 alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> Select date first
                            </div>
                        </div>

                        <!-- NEW: Extended Time Selection -->
                        <div class="mb-4 hidden">
                            <h5 class="mb-3">Extended Time Requirements</h5>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label">Pre-arrange Time (Setup)</label>
                                    <select class="form-select" name="pre_arrange_time" id="preArrangeTimeRegular">
                                        <option value="0">0 hours</option>
                                        @for ($i = 1; $i <= ($hall->max_pre_arrange_hours ?? 5); $i++)
                                            <option value="{{ $i }}">{{ $i }} hours</option>
                                        @endfor
                                    </select>
                                    <small class="form-text text-muted">Time needed before event for setup (Max: {{ $hall->max_pre_arrange_hours ?? 5 }} hours)</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Post-time (Cleanup)</label>
                                    <select class="form-select" name="post_arrange_time" id="postArrangeTimeRegular">
                                        <option value="0">0 hours</option>
                                        @for ($i = 1; $i <= ($hall->max_post_arrange_hours ?? 5); $i++)
                                            <option value="{{ $i }}">{{ $i }} hours</option>
                                        @endfor
                                    </select>
                                    <small class="form-text text-muted">Time needed after event for cleanup (Max: {{ $hall->max_post_arrange_hours ?? 5 }} hours)</small>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden Inputs for Regular Booking -->
                        <input type="hidden" name="booking_type" value="regular">
                        <input type="hidden" name="start_time" id="regularStartTimeInput" required>
                        <input type="hidden" name="end_time" id="regularEndTimeInput" required>
                        <!-- NEW: Extended time inputs -->
                        <input type="hidden" name="actual_start_time" id="regularActualStartTimeInput">
                        <input type="hidden" name="actual_end_time" id="regularActualEndTimeInput">

                        <!-- Regular Charge Breakdown -->
                        <div id="regularCharges">
                            <div class="mb-3 p-3 bg-light rounded">
                                <h5 class="mb-0">Property Charge: <span id="propertyChargeRegular">Rs. 0.00</span></h5>
                            </div>
                            @if ($hall->discount > 0)
                            <div class="mb-3 p-3 bg-light rounded">
                                <h5 class="mb-0">Hall Discount: <span id="propertyDiscountRegular">Rs. {{ $hall->discount }}</span></h5>
                            </div>                            
                            @endif
                            <div class="mb-3 p-3 bg-light rounded">
                                <h5 class="mb-0">Facilities Charge: <span id="facilitiesChargeRegular">Rs. 0.00</span>
                                </h5>
                            </div>
                        </div>

                        <!-- Total Charge Display for Regular -->
                        <div class="mb-3 p-3 bg-primary text-white rounded">
                            <h5 class="mb-0">Total Charge: <span id="regularTotalCharge">Rs. 0.00</span></h5>
                        </div>

                        <!-- Terms & Conditions Checkbox -->
                         <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="agree_terms" id="agreeTermsRegular" value="1" required>
                                <label class="form-check-label" for="agreeTermsRegular">
                                    I agree to the 
                                    <a href="#" class="open-terms-link" data-pdf="{{ asset('storage/' . $hall->pdf) }}"
                                        data-bs-toggle="modal" data-bs-target="#termsModal">
                                        Terms And Conditions
                                    </a>
                                </label>
                                <div class="invalid-feedback">
                                    You must agree to the terms and conditions to proceed.
                                </div>
                            </div>
                        </div>

                        <!-- Clearence Form Upload -->
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-file-pdf me-2"></i>Upload Dully Filled Application Form (PDF)
                            </label>
                            <input type="file" name="clearence_form" class="form-control" accept=".pdf" required>
                            @error('clearence_form')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Maximum file size: 10MB. Only PDF files are accepted.
                            </div>
                        </div>

                        <!-- Reserve Button for Regular -->
                        <button type="submit" class="btn btn-primary w-100 btn-lg" id="regularReserveButton" disabled>
                            <i class="fas fa-calendar-check me-2"></i>Request The Reservation (Regular)
                        </button>
                    </form>

                    <!-- PACKAGE BOOKING FORM -->
                    <form method="POST" action="{{ route('reservation.request.package.route', $hall->id) }}" id="packageForm" class="{{ $hall->booking_method === 'package' ? '' : 'hidden' }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Date Selection (Package Only) -->
                        <div class="mb-4">
                            <label class="form-label">Select Date</label>
                            <input type="date" name="selected_date" id="datePickerPackage" class="form-control"
                                min="{{ now()->format('Y-m-d') }}" max="{{ now()->addDays(180)->format('Y-m-d') }}"
                                required>
                            <small class="form-text text-muted">Select a date between today and
                                {{ now()->addDays(180)->format('M d, Y') }}</small>
                        </div>

                        <!-- Time Slots (Package Only) -->
                        <div class="mb-4">
                            <h5 class="mb-3">Available Time Slots</h5>
                            <div id="timeSlotsContainerPackage" class="d-flex flex-wrap gap-2">
                                <div class="alert alert-info w-100">
                                    <i class="fas fa-info-circle me-2"></i> Please select a date to see available time
                                    slots
                                </div>
                            </div>
                        </div>

                        <!-- Custom Time Selection (Package Only) -->
                        <div class="mb-4">
                            <h5 class="mb-3">Or Select Custom Time</h5>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label">Start Time</label>
                                    <select class="form-select" id="customStartTimePackage" disabled>
                                        <option value="">Select time</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">End Time</label>
                                    <select class="form-select" id="customEndTimePackage" disabled>
                                        <option value="">Select time</option>
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div id="customTimeStatusPackage" class="mt-2 alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> Select date first
                            </div>
                        </div>

                        <!-- NEW: Extended Time Selection for Package -->
                        <div class="mb-4">
                            <h5 class="mb-3">Extended Time Requirements</h5>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label">Pre-arrange Time (Setup)</label>
                                    <select class="form-select" name="pre_arrange_time" id="preArrangeTimePackage">
                                        <option value="0">0 hours</option>
                                        @for ($i = 1; $i <= ($hall->max_pre_arrange_hours ?? 5); $i++)
                                            <option value="{{ $i }}">{{ $i }} hour{{ $i > 1 ? 's' : '' }}</option>
                                        @endfor
                                    </select>
                                    <small class="form-text text-muted">Time needed before event for setup (Max: {{ $hall->max_pre_arrange_hours ?? 5 }} hours)</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Post-time (Cleanup)</label>
                                    <select class="form-select" name="post_arrange_time" id="postArrangeTimePackage">
                                        <option value="0">0 hours</option>
                                        @for ($i = 1; $i <= ($hall->max_post_arrange_hours ?? 5); $i++)
                                            <option value="{{ $i }}">{{ $i }} hour{{ $i > 1 ? 's' : '' }}</option>
                                        @endfor
                                    </select>
                                    <small class="form-text text-muted">Time needed after event for cleanup (Max: {{ $hall->max_post_arrange_hours ?? 5 }} hours)</small>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden Inputs for Package Booking -->
                        <input type="hidden" name="booking_type" value="package">
                        <input type="hidden" name="start_time" id="packageStartTimeInput" required>
                        <input type="hidden" name="end_time" id="packageEndTimeInput" required>
                        <input type="hidden" name="package_id" id="packageIdInput">

                        <!-- NEW: Dynamically calculated total charge -->
                        <input type="hidden" name="total_charge" id="packageTotalChargeInput" value="0">

                        <!-- NEW: Extended time inputs -->
                        <input type="hidden" name="actual_start_time" id="packageActualStartTimeInput">
                        <input type="hidden" name="actual_end_time" id="packageActualEndTimeInput">

                        <!-- Package Charge Breakdown -->
                        <div id="packageCharges">
                            <div class="mb-3 p-3 bg-light rounded">
                                <h5 class="mb-0">Package Price: <span id="packageCharge">Rs. 0.00</span></h5>
                            </div>
                            <div id="packageFacilitiesChargeSection" class="mb-3 p-3 bg-light rounded" style="display: none;">
                                <h5 class="mb-0">Facilities Charge: <span id="packageFacilitiesCharge">Rs. 0.00</span></h5>
                            </div>
                        </div>


                        <!-- Total Charge Display for Package -->
                        <div class="mb-3 p-3 bg-primary text-white rounded">
                            <h5 class="mb-0">Total Charge: <span id="packageTotalCharge">Rs. 0.00</span></h5>
                        </div>

                        <!-- Terms & Conditions Checkbox -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="agree_terms" id="agreeTermsPackage" value="1" required>
                                <label class="form-check-label" for="agreeTermsPackage">
                                    I agree to the 
                                    <a href="#" class="open-terms-link" data-pdf="{{ asset('storage/' . $hall->pdf) }}"
                                        data-bs-toggle="modal" data-bs-target="#termsModal">
                                        Terms And Conditions Link
                                    </a>
                                </label>
                                <div class="invalid-feedback">
                                    You must agree to the terms and conditions to proceed.
                                </div>
                            </div>
                        </div>

                        <!-- Clearence Form Upload -->
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-file-pdf me-2"></i>Upload Dully Filled Clearance Form (PDF)
                            </label>
                            <input type="file" name="clearence_form" class="form-control" accept=".pdf" required>
                            @error('clearence_form')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Maximum file size: 10MB. Only PDF files are accepted.
                            </div>
                        </div>

                        <!-- Reserve Button for Package -->
                        <button type="submit" class="btn btn-primary w-100 btn-lg" id="packageReserveButton" disabled>
                            <i class="fas fa-calendar-check me-2"></i>Request The Reservation (Package)
                        </button>
                    </form>

                </div>
            </div>
        </div>

        <!-- Right Column - Details -->
        <div class="col-lg-6">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="mb-3">{{ $hall->name }}</h1>

                    <!-- Basic Info -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Type:</strong> {{ $hall->type }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Price:</strong>Rs. {{ number_format($hall->price, 2) }}/Hr</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Capacity:</strong> {{ $hall->capacity }} people</p>
                        </div>
                    </div>

                    <!-- Regular Booking Section -->
                    <div id="regularBookingSection" class="{{ $hall->booking_method === 'package' ? 'hidden' : '' }}">
                        <div class="card mt-4">
                            <div class="card-header facility-header">
                                <h5 class="mb-0">Fixed Price Facilities (Rs.)</h5>
                            </div>
                            <div class="card-body">
                                @foreach ($hall->fixedfacilities as $fp_facility)
                                    <div class="facility-item">
                                        <div class="form-check">
                                            <input class="form-check-input facility-checkbox" type="checkbox"
                                                value="{{ $fp_facility->charge }}"
                                                id="fixed_facility_{{ $fp_facility->id }}">
                                            <label class="form-check-label" for="fixed_facility_{{ $fp_facility->id }}">
                                                {{$fp_facility->name}} - Rs. {{ number_format($fp_facility->charge, 2) }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="card mt-4">
                            <div class="card-header facility-header">
                                <h5 class="mb-0">Unit Price Facilities (Per Hour - Rs.)</h5>
                            </div>
                            <div class="card-body">
                                @foreach ($hall->unitfacilities as $up_facility)
                                    <div class="facility-item">
                                        <div class="form-check">
                                            <input class="form-check-input unit-facility" type="checkbox"
                                                value="{{ $up_facility->charge }}" id="unit_facility_{{ $up_facility->id }}"
                                                data-price="{{ $up_facility->charge }}">
                                            <label class="form-check-label" for="unit_facility_{{ $up_facility->id }}">
                                                {{$up_facility->name}} - Rs. {{ number_format($up_facility->charge, 2) }}
                                                per unit
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Package Booking Section -->
                    <div id="packageBookingSection" class="{{ $hall->booking_method === 'package' ? '' : 'hidden' }}">
                        <div class="card mt-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">Select a Package</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach ($hall->packages as $package)
                                        <div class="col-md-6 mb-3">
                                            @php
                                                $unitCharges = $package->getUnitFacilitiesAttribute()->pluck('charge')->toArray();
                                            @endphp
                                            <div class="card package-card h-100" data-duration="{{ $package->duration }}"
                                                data-price="{{ $package->price }}" data-discount="{{ $package->discount ?? 0 }}" data-package-id="{{ $package->id }}"
                                                data-free-hours="{{ $package->maximum_hours ?? 4 }}"
                                                data-unit-charges='{{ json_encode($unitCharges) }}'>
                                                <div class="card-body">
                                                    <h5 class="card-title">{{ $package->name }}</h5>
                                                    <p class="card-text">{{ $package->description }}</p>
                                                </div>
                                                <div class="card-footer text-center">
                                                    <h3 class="card-subtitle mb-1">Rs. {{ number_format($package->price, 2) }}</h3>
                                                    @if ($package->discount > 0)
                                                    <small class="text-muted">Discount: Rs. {{ number_format($package->discount, 2) }}</small>
                                                    @endif
                                                    <br><span class="text-muted">Duration: {{ $package->duration }} hours</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Package Facilities Selection (Fixed & Unit) -->
                        <div class="card mt-4">
                            <div class="card-header facility-header">
                                <h5 class="mb-0">Fixed Price Facilities (Rs.)</h5>
                            </div>
                            <div class="card-body">
                                @foreach ($hall->fixedfacilities as $fp_facility)
                                    <div class="facility-item">
                                        <div class="form-check">
                                            <input class="form-check-input package-fixed-facility" type="checkbox"
                                                value="{{ $fp_facility->charge }}"
                                                id="pkg_fixed_facility_{{ $fp_facility->id }}"
                                                data-facility-id="{{ $fp_facility->id }}">
                                            <label class="form-check-label" for="pkg_fixed_facility_{{ $fp_facility->id }}">
                                                {{$fp_facility->name}} - Rs. {{ number_format($fp_facility->charge, 2) }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="card mt-4">
                            <div class="card-header facility-header">
                                <h5 class="mb-0">Unit Price Facilities (Per Hour - Rs.)</h5>
                            </div>
                            <div class="card-body">
                                @foreach ($hall->unitfacilities as $up_facility)
                                    <div class="facility-item">
                                        <div class="form-check">
                                            <input class="form-check-input package-unit-facility" type="checkbox"
                                                value="{{ $up_facility->charge }}" id="pkg_unit_facility_{{ $up_facility->id }}"
                                                data-price="{{ $up_facility->charge }}"
                                                data-facility-id="{{ $up_facility->id }}">
                                            <label class="form-check-label" for="pkg_unit_facility_{{ $up_facility->id }}">
                                                {{$up_facility->name}} - Rs. {{ number_format($up_facility->charge, 2) }}
                                                per unit
                                            </label>
                                            <div class="mt-2" style="padding-left: 1.5rem;">
                                                <div class="input-group input-group-sm" style="max-width: 180px;">
                                                    <span class="input-group-text">Hours</span>
                                                    <input type="number" class="form-control package-unit-facility-hours" 
                                                        min="0" max="24" step="0.5" value="0"
                                                        data-facility-id="{{ $up_facility->id }}"
                                                        disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>

                    <!-- Location Section -->
                    <div class="mb-4">
                        <h4 class="mb-3"><i class="fas fa-map-marker-alt me-2"></i>Location</h4>
                        <p class="mb-1">{{ $hall->address }}</p>
                        <p class="mb-1">{{ $hall->area }}, {{ $hall->district }}</p>
                        <p class="mb-1">Coordinates: {{ $hall->longitude }}, {{ $hall->latitude }}</p>
                        <button class="btn btn-primary open-map-btn mt-2" data-lat="{{ $hall->latitude }}" data-lng="{{ $hall->longitude }}">
                            <i class="fas fa-location-dot me-2"></i> Find Our Location
                        </button>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <h4 class="mb-3"><i class="fas fa-align-left me-2"></i>Description</h4>
                        <p class="mb-1">{{ $hall->description }}</p>
                    </div>

                    <!-- Owner Info -->
                    <div class="border-top pt-4">
                        <h5 class="mb-3">Owner Information</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <p class="mb-1"><strong>Name:</strong> {{ $hall->admin->company_name }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1"><strong>Phone:</strong> {{ $hall->admin->telephone_number }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1"><strong>Email:</strong> {{ $hall->admin->email }}</p>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-primary open-terms-btn mt-2" data-pdf="{{ asset('storage/' . $hall->clearence_form) }}">
                        <i class="fas fa-file-contract me-2"></i> Download Application Form
                    </button>

                </div>
            </div>
        </div>
    </div>

    <style>
        /* Unique legacy styles not covered in head */
        .time-slot-btn { flex: 1 0 45%; white-space: nowrap; }
        .unavailable-slot { opacity: 0.6; position: relative; }
        .unavailable-slot::after { content: ""; position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: #dc3545; transform: rotate(-5deg); }
        .date-picker-unavailable { background-color: #fff8f8; color: #dc3545; text-decoration: line-through; }
        .fa-spinner { color: #0d6efd; }
        .section-title { border-bottom: 2px solid var(--primary); padding-bottom: 10px; margin-bottom: 20px; color: var(--dark); }
        .price-display { font-size: 1.5rem; font-weight: bold; color: var(--primary); }
        .hidden { display: none; }
        .package-features { list-style-type: none; padding: 0; }
        .package-features li { padding: 5px 0; }
        .package-features li:before { content: "✓ "; color: var(--primary); font-weight: bold; }
    </style>

    <script>
    function formatMoney(amount) {
        return Number(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    const hourlyRate = {{ $hall->price }};
    const fullyUnavailableDates = @json($fullyUnavailableDates);
    const hallBookingMethod = '{{ $hall->booking_method ?? 'both' }}';

    // Global variables for charge calculation
    let selectedPackagePrice = 0;
    let isPackageMode = (hallBookingMethod === 'package');
    let selectedPackageDuration = 0;
    let selectedFreeHours = 4;
    let currentUnavailablePeriods = [];
    let currentAvailableSlots = [];
    let selectedRegularTimeSlot = null;
    let selectedPackageTimeSlot = null;

    document.addEventListener('DOMContentLoaded', function ()
    {
        // Image Slider Logic
        const slides = document.querySelectorAll('.slide');
        let currentSlide = 0;
        const totalSlides = slides.length;

        function showSlide(n) {
            slides.forEach(slide => slide.classList.remove('active'));
            currentSlide = (n + totalSlides) % totalSlides;
            slides[currentSlide].classList.add('active');
        }

        document.querySelector('.prev-arrow')?.addEventListener('click', () => {
            showSlide(currentSlide - 1);
        });

        document.querySelector('.next-arrow')?.addEventListener('click', () => {
            showSlide(currentSlide + 1);
        });

        if (totalSlides > 1) {
            setInterval(() => showSlide(currentSlide + 1), 5000);
        }

        // Booking Method Toggle
        const regularBookingSection = document.getElementById('regularBookingSection');
        const packageBookingSection = document.getElementById('packageBookingSection');
        const regularForm = document.getElementById('regularForm');
        const packageForm = document.getElementById('packageForm');

        // Initialize both forms
        initializeRegularForm();
        initializePackageForm();

        document.querySelectorAll('input[name="bookingMethod"]').forEach(radio => {
            radio.addEventListener('change', function () {
                if (this.value === 'regular') {
                    regularBookingSection.classList.remove('hidden');
                    packageBookingSection.classList.add('hidden');
                    regularForm.classList.remove('hidden');
                    packageForm.classList.add('hidden');

                    isPackageMode = false;
                    selectedPackageDuration = 0;
                    selectedPackagePrice = 0;
                    document.querySelectorAll('.package-card').forEach(c => c.classList.remove('selected'));

                    updateFacilitiesInputs();
                    updateTotalCharge();
                } else {
                    regularBookingSection.classList.add('hidden');
                    packageBookingSection.classList.remove('hidden');
                    regularForm.classList.add('hidden');
                    packageForm.classList.remove('hidden');

                    isPackageMode = true;

                    document.querySelectorAll('input[name="fixed_facilities[]"]').forEach(el => el.remove());
                    document.querySelectorAll('input[name="unit_facilities[]"]').forEach(el => el.remove());
                }
                updateReserveButtonState();
            });
        });

        // Package Selection - Package auto-selected in validateCustomTimePackage() based on chargeable hours (R)
        document.querySelectorAll('.package-card').forEach(card => {
            card.addEventListener('click', function () {
                document.querySelectorAll('.package-card').forEach(c => {
                    c.classList.remove('selected');
                });
                this.classList.add('selected');

                const price = parseFloat(this.dataset.price);
                const discount = parseFloat(this.dataset.discount) || 0;
                const packageId = this.dataset.packageId;
                
                document.getElementById('packageIdInput').value = packageId;
                
                const startTime = document.getElementById('customStartTimePackage').value;
                const endTime = document.getElementById('customEndTimePackage').value;
                if (startTime && endTime) {
                    validateCustomTimePackage();
                } else {
                    selectedPackagePrice = price;
                    selectedPackageDuration = parseInt(this.dataset.duration) || 0;
                    
                    document.getElementById('packageCharge').textContent = `Rs. ${formatMoney(price)}`;
                    
                    const finalPrice = price - discount;
                    document.getElementById('packageTotalCharge').textContent = `Rs. ${formatMoney(finalPrice)}`;
                    document.getElementById('packageTotalChargeInput').value = finalPrice.toFixed(2);
                }
                
                updateReserveButtonState();
            });
        });

        // Package facility checkboxes enable/disable hour inputs
        document.querySelectorAll('.package-fixed-facility').forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                if (isPackageMode) {
                    const startTime = document.getElementById('customStartTimePackage').value;
                    const endTime = document.getElementById('customEndTimePackage').value;
                    if (startTime && endTime) {
                        validateCustomTimePackage();
                    }
                }
            });
        });

        document.querySelectorAll('.package-unit-facility').forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const facilityId = this.dataset.facilityId;
                const hoursInput = document.querySelector(`.package-unit-facility-hours[data-facility-id="${facilityId}"]`);
                if (hoursInput) {
                    hoursInput.disabled = !this.checked;
                    if (!this.checked) {
                        hoursInput.value = '0';
                    }
                }
                if (isPackageMode) {
                    const startTime = document.getElementById('customStartTimePackage').value;
                    const endTime = document.getElementById('customEndTimePackage').value;
                    if (startTime && endTime) {
                        validateCustomTimePackage();
                    }
                }
            });
        });

        // Package unit facility hours input change listener
        document.querySelectorAll('.package-unit-facility-hours').forEach(input => {
            input.addEventListener('change', function () {
                if (isPackageMode) {
                    const startTime = document.getElementById('customStartTimePackage').value;
                    const endTime = document.getElementById('customEndTimePackage').value;
                    if (startTime && endTime) {
                        validateCustomTimePackage();
                    }
                }
            });
            input.addEventListener('input', function () {
                if (isPackageMode) {
                    const startTime = document.getElementById('customStartTimePackage').value;
                    const endTime = document.getElementById('customEndTimePackage').value;
                    if (startTime && endTime) {
                        validateCustomTimePackage();
                    }
                }
            });
        });


        function updateFacilitiesInputs() {
            document.querySelectorAll('input[name="fixed_facilities[]"]').forEach(el => el.remove());
            document.querySelectorAll('input[name="unit_facilities[]"]').forEach(el => el.remove());

            const regularForm = document.getElementById('regularForm');

            document.querySelectorAll('.facility-checkbox:checked').forEach(checkbox => {
                const facilityId = checkbox.id.replace('fixed_facility_', '');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'fixed_facilities[]';
                input.value = facilityId;
                regularForm.appendChild(input);
            });

            document.querySelectorAll('.unit-facility:checked').forEach(checkbox => {
                const facilityId = checkbox.id.replace('unit_facility_', '');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'unit_facilities[]';
                input.value = facilityId;
                regularForm.appendChild(input);
            });
        }

        function updateReserveButtonState() {
            if (isPackageMode) {
                const hasDate = document.getElementById('datePickerPackage').value;
                const hasStartTime = document.getElementById('packageStartTimeInput').value;
                const hasEndTime = document.getElementById('packageEndTimeInput').value;
                const hasPackage = selectedPackageDuration > 0;

                document.getElementById('packageReserveButton').disabled = !(hasDate && hasStartTime && hasEndTime && hasPackage);
            } else {
                const hasDate = document.getElementById('datePickerRegular').value;
                const hasStartTime = document.getElementById('regularStartTimeInput').value;
                const hasEndTime = document.getElementById('regularEndTimeInput').value;

                document.getElementById('regularReserveButton').disabled = !(hasDate && hasStartTime && hasEndTime);
            }
        }

        // ==================== REGULAR FORM FUNCTIONS ====================
        function initializeRegularForm() {
            const datePickerRegular = document.getElementById('datePickerRegular');
            const timeSlotsContainerRegular = document.getElementById('timeSlotsContainerRegular');
            const customStartTimeRegular = document.getElementById('customStartTimeRegular');
            const customEndTimeRegular = document.getElementById('customEndTimeRegular');
            const customTimeStatusRegular = document.getElementById('customTimeStatusRegular');

            populateTimeSelectors(customStartTimeRegular, customEndTimeRegular);

            datePickerRegular.addEventListener('change', async function () {
                await handleDateChange(this.value, timeSlotsContainerRegular, customStartTimeRegular, customEndTimeRegular, customTimeStatusRegular, 'regular');
            });

            customStartTimeRegular.addEventListener('change', function () {
                validateCustomTimeRegular();
            });

            customEndTimeRegular.addEventListener('change', function () {
                validateCustomTimeRegular();
            });

            document.getElementById('preArrangeTimeRegular').addEventListener('change', function () {
                validateCustomTimeRegular();
            });

            document.getElementById('postArrangeTimeRegular').addEventListener('change', function () {
                validateCustomTimeRegular();
            });
        }

        // REGULAR FORM VALIDATION (KEEP AS IS - WORKING)
        function validateCustomTimeRegular()
        {
            const startTime = document.getElementById('customStartTimeRegular').value;
            const endTime = document.getElementById('customEndTimeRegular').value;
            const preArrange = parseInt(document.getElementById('preArrangeTimeRegular').value) || 0;
            const postArrange = parseInt(document.getElementById('postArrangeTimeRegular').value) || 0;
            const statusElement = document.getElementById('customTimeStatusRegular');
            const submitButton = document.getElementById('regularReserveButton');
            //const agreeTerms = document.getElementById('agreeTermsRegular').checked;

            /* Check if terms are agreed
            if (!agreeTerms)
            {
                statusElement.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i> You must agree to the terms and conditions';
                statusElement.className = 'alert alert-danger';
                submitButton.disabled = true;
                return false;
            }*/

            // Step 1: Check if time slot was selected first
            if (!selectedRegularTimeSlot) {
                statusElement.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i> Please select an available time slot first';
                statusElement.className = 'alert alert-danger';
                submitButton.disabled = true;
                return false;
            }

            // Step 2: Check if start time is selected
            if (!startTime) {
                statusElement.innerHTML = '<i class="fas fa-info-circle me-2"></i> Please select start time';
                statusElement.className = 'alert alert-info';
                submitButton.disabled = true;
                return false;
            }

            // Step 3: Check if end time is selected
            if (!endTime) {
                statusElement.innerHTML = '<i class="fas fa-info-circle me-2"></i> Please select end time';
                statusElement.className = 'alert alert-info';
                submitButton.disabled = true;
                return false;
            }

            // Step 4: Check if end time is after start time
            const startMinutes = timeToMinutes(startTime);
            const endMinutes = timeToMinutes(endTime);
            
            if (endMinutes <= startMinutes) {
                statusElement.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i> End time must be after start time';
                statusElement.className = 'alert alert-danger';
                submitButton.disabled = true;
                return false;
            }

            // Step 5: Calculate the actual extended period
            const actualStartMinutes = startMinutes - (preArrange * 60);
            const actualEndMinutes = endMinutes + (postArrange * 60);

            // Step 6: Get the selected time slot boundaries
            const slotStartMinutes = timeToMinutes(selectedRegularTimeSlot.start);
            const slotEndMinutes = timeToMinutes(selectedRegularTimeSlot.end);

            // Step 7: Check if extended period goes negative (before midnight of same day)
            if (actualStartMinutes < 0) {
                const neededPreTime = Math.ceil(Math.abs(actualStartMinutes) / 60);
                statusElement.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i> Pre-arrange time (${preArrange}h) extends before midnight. Reduce to ${preArrange - neededPreTime} hour(s) or select later start time`;
                statusElement.className = 'alert alert-danger';
                submitButton.disabled = true;
                return false;
            }

            // Step 8: Check if extended period goes beyond 23:59 (next day)
            if (actualEndMinutes > 1439) {
                const excessMinutes = actualEndMinutes - 1439;
                const excessHours = Math.ceil(excessMinutes / 60);
                statusElement.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i> Post-arrange time (${postArrange}h) extends beyond 11:59 PM. Reduce by ${excessHours} hour(s) or select earlier end time`;
                statusElement.className = 'alert alert-danger';
                submitButton.disabled = true;
                return false;
            }

            // Step 9: MAIN VALIDATION - Check if entire extended period fits within selected slot
            const fitsInSlot = (actualStartMinutes >= slotStartMinutes && actualEndMinutes <= slotEndMinutes);

            if (!fitsInSlot) {
                let errorMessage = '';
                
                if (actualStartMinutes < slotStartMinutes) {
                    const shortfallMinutes = slotStartMinutes - actualStartMinutes;
                    const shortfallHours = Math.ceil(shortfallMinutes / 60);
                    const slotStartFormatted = formatTime(selectedRegularTimeSlot.start);
                    
                    errorMessage = `<i class="fas fa-exclamation-triangle me-2"></i> Pre-arrange time extends ${shortfallHours}h before slot start (${slotStartFormatted}). Reduce pre-arrange time or select later start time`;
                } else if (actualEndMinutes > slotEndMinutes) {
                    const excessMinutes = actualEndMinutes - slotEndMinutes;
                    const excessHours = Math.ceil(excessMinutes / 60);
                    const slotEndFormatted = formatTime(selectedRegularTimeSlot.end);
                    
                    errorMessage = `<i class="fas fa-exclamation-triangle me-2"></i> Post-arrange time extends ${excessHours}h beyond slot end (${slotEndFormatted}). Reduce post-arrange time or select earlier end time`;
                } else {
                    const slotStartFormatted = formatTime(selectedRegularTimeSlot.start);
                    const slotEndFormatted = formatTime(selectedRegularTimeSlot.end);
                    errorMessage = `<i class="fas fa-exclamation-triangle me-2"></i> Selected time period conflicts with slot boundaries (${slotStartFormatted} - ${slotEndFormatted})`;
                }
                
                statusElement.innerHTML = errorMessage;
                statusElement.className = 'alert alert-danger';
                submitButton.disabled = true;
                return false;
            }

            // Step 10: All validations passed - Update form inputs
            document.getElementById('regularStartTimeInput').value = startTime;
            document.getElementById('regularEndTimeInput').value = endTime;
            
            const actualStartTime = formatMinutesToTime(actualStartMinutes);
            const actualEndTime = formatMinutesToTime(actualEndMinutes);
            document.getElementById('regularActualStartTimeInput').value = actualStartTime;
            document.getElementById('regularActualEndTimeInput').value = actualEndTime;

            // Show success message with details
            let successMessage = '<i class="fas fa-check-circle me-2"></i> Time slot is available';
            
            if (preArrange > 0 || postArrange > 0) {
                const details = [];
                if (preArrange > 0) details.push(`${preArrange}h setup`);
                details.push(`event (${formatTime(startTime)} - ${formatTime(endTime)})`);
                if (postArrange > 0) details.push(`${postArrange}h cleanup`);
                
                successMessage += ` (${details.join(' + ')})`;
            }
            
            statusElement.innerHTML = successMessage;
            statusElement.className = 'alert alert-success';
            submitButton.disabled = false;
            
            updateTotalCharge();
            return true;
        }

        // ==================== PACKAGE FORM FUNCTIONS ====================
        function initializePackageForm() {
            const datePickerPackage = document.getElementById('datePickerPackage');
            const timeSlotsContainerPackage = document.getElementById('timeSlotsContainerPackage');
            const customStartTimePackage = document.getElementById('customStartTimePackage');
            const customEndTimePackage = document.getElementById('customEndTimePackage');
            const customTimeStatusPackage = document.getElementById('customTimeStatusPackage');

            populateTimeSelectors(customStartTimePackage, customEndTimePackage);

            datePickerPackage.addEventListener('change', async function () {
                await handleDateChange(this.value, timeSlotsContainerPackage, customStartTimePackage, customEndTimePackage, customTimeStatusPackage, 'package');
            });

            customStartTimePackage.addEventListener('change', function () {
                validateCustomTimePackage();
            });

            // End time change listener (though it's disabled for packages)
            customEndTimePackage.addEventListener('change', function () {
                validateCustomTimePackage();
            });

            document.getElementById('preArrangeTimePackage').addEventListener('change', function () {
                validateCustomTimePackage();
            });

            document.getElementById('postArrangeTimePackage').addEventListener('change', function () {
                validateCustomTimePackage();
            });

            // Ensure total_charge is always set before form submission
            // and add hidden inputs for selected package facilities
            packageForm.addEventListener('submit', function(e) {
                const chargeInput = document.getElementById('packageTotalChargeInput');
                if (!chargeInput.value || chargeInput.value === '0' || chargeInput.value === '0.00') {
                    // Force validation to recalculate the charge
                    const validated = validateCustomTimePackage();
                    if (!validated) {
                        e.preventDefault();
                        return false;
                    }
                }
                
                // Remove any previously added hidden facility inputs
                document.querySelectorAll('#packageForm .pkg-facility-hidden').forEach(el => el.remove());
                
                // Add hidden inputs for selected package fixed facilities
                document.querySelectorAll('.package-fixed-facility:checked').forEach(checkbox => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'package_fixed_facilities[]';
                    input.value = checkbox.dataset.facilityId;
                    input.className = 'pkg-facility-hidden';
                    packageForm.appendChild(input);
                });
                
                // Add hidden inputs for selected package unit facilities (facility ID + hours)
                document.querySelectorAll('.package-unit-facility:checked').forEach(checkbox => {
                    const facilityId = checkbox.dataset.facilityId;
                    const hoursInput = document.querySelector(`.package-unit-facility-hours[data-facility-id="${facilityId}"]`);
                    const hours = parseFloat(hoursInput?.value || 0);
                    
                    // Only submit if hours > 0
                    if (hours > 0) {
                        // Submit facility ID
                        const idInput = document.createElement('input');
                        idInput.type = 'hidden';
                        idInput.name = 'package_unit_facilities[]';
                        idInput.value = facilityId;
                        idInput.className = 'pkg-facility-hidden';
                        packageForm.appendChild(idInput);
                        
                        // Submit hours
                        const hoursInputElem = document.createElement('input');
                        hoursInputElem.type = 'hidden';
                        hoursInputElem.name = 'package_unit_hours[]';
                        hoursInputElem.value = hours;
                        hoursInputElem.className = 'pkg-facility-hidden';
                        packageForm.appendChild(hoursInputElem);
                    }
                });

            });

        }

        // REWRITTEN: PACKAGE FORM VALIDATION - Auto-selects package based on total chargeable hours (R)
        function validateCustomTimePackage()
        {
            const startTime = document.getElementById('customStartTimePackage').value;
            const endTime = document.getElementById('customEndTimePackage').value;
            const preArrange = parseInt(document.getElementById('preArrangeTimePackage').value) || 0;
            const postArrange = parseInt(document.getElementById('postArrangeTimePackage').value) || 0;
            const statusElement = document.getElementById('customTimeStatusPackage');
            const submitButton = document.getElementById('packageReserveButton');

            // Step 1: Check if time slot was selected
            if (!selectedPackageTimeSlot) {
                statusElement.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i> Please select an available time slot first';
                statusElement.className = 'alert alert-danger';
                submitButton.disabled = true;
                return false;
            }

            // Step 2: Check if start time is selected
            if (!startTime) {
                statusElement.innerHTML = '<i class="fas fa-info-circle me-2"></i> Please select start time';
                statusElement.className = 'alert alert-info';
                submitButton.disabled = true;
                return false;
            }

            // Step 3: Check if end time is selected
            if (!endTime) {
                statusElement.innerHTML = '<i class="fas fa-info-circle me-2"></i> Please select end time';
                statusElement.className = 'alert alert-info';
                submitButton.disabled = true;
                return false;
            }

            // Step 4: Check if end time is after start time
            const startMinutes = timeToMinutes(startTime);
            const endMinutes = timeToMinutes(endTime);
            
            if (endMinutes <= startMinutes) {
                statusElement.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i> End time must be after start time';
                statusElement.className = 'alert alert-danger';
                submitButton.disabled = true;
                return false;
            }

            // Step 5: Calculate Q (event hours) and P (chargeable pre-post hours)
            const Q = (endMinutes - startMinutes) / 60;
            // Free hours per package from selectedFreeHours (falls back to 4 if no package selected yet)
            const freeHours = selectedFreeHours || 4;
            const chargeablePre = Math.max(0, preArrange - freeHours);
            const chargeablePost = Math.max(0, postArrange - freeHours);
            const P = chargeablePre + chargeablePost;
            const R = Q + P; // Total chargeable hours

            // Step 6: Calculate the actual extended period
            const actualStartMinutes = startMinutes - (preArrange * 60);
            const actualEndMinutes = endMinutes + (postArrange * 60);

            // Step 7: Get the selected time slot boundaries
            const slotStartMinutes = timeToMinutes(selectedPackageTimeSlot.start);
            const slotEndMinutes = timeToMinutes(selectedPackageTimeSlot.end);

            // Step 8: Check if extended period goes negative (before midnight of same day)
            if (actualStartMinutes < 0) {
                const neededPreTime = Math.ceil(Math.abs(actualStartMinutes) / 60);
                statusElement.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i> Pre-arrange time (${preArrange}h) extends before midnight. Reduce to ${preArrange - neededPreTime} hour(s) or select later start time`;
                statusElement.className = 'alert alert-danger';
                submitButton.disabled = true;
                return false;
            }

            // Step 9: Check if extended period goes beyond 23:59 (next day)
            if (actualEndMinutes > 1439) {
                const excessMinutes = actualEndMinutes - 1439;
                const excessHours = Math.ceil(excessMinutes / 60);
                statusElement.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i> Post-arrange time (${postArrange}h) extends beyond 11:59 PM. Reduce by ${excessHours} hour(s) or select earlier end time`;
                statusElement.className = 'alert alert-danger';
                submitButton.disabled = true;
                return false;
            }

            // Step 10: MAIN VALIDATION - Check if entire extended period fits within selected slot
            const fitsInSlot = (actualStartMinutes >= slotStartMinutes && actualEndMinutes <= slotEndMinutes);

            if (!fitsInSlot) {
                let errorMessage = '';
                
                if (actualStartMinutes < slotStartMinutes) {
                    const shortfallMinutes = slotStartMinutes - actualStartMinutes;
                    const shortfallHours = Math.ceil(shortfallMinutes / 60);
                    const slotStartFormatted = formatTime(selectedPackageTimeSlot.start);
                    
                    errorMessage = `<i class="fas fa-exclamation-triangle me-2"></i> Pre-arrange time extends ${shortfallHours}h before slot start (${slotStartFormatted}). Reduce pre-arrange time or select later start time`;
                } else if (actualEndMinutes > slotEndMinutes) {
                    const excessMinutes = actualEndMinutes - slotEndMinutes;
                    const excessHours = Math.ceil(excessMinutes / 60);
                    const slotEndFormatted = formatTime(selectedPackageTimeSlot.end);
                    
                    errorMessage = `<i class="fas fa-exclamation-triangle me-2"></i> Event + post-arrange time extends ${excessHours}h beyond slot end (${slotEndFormatted}). Reduce post-arrange time or select earlier end time`;
                } else {
                    const slotStartFormatted = formatTime(selectedPackageTimeSlot.start);
                    const slotEndFormatted = formatTime(selectedPackageTimeSlot.end);
                    errorMessage = `<i class="fas fa-exclamation-triangle me-2"></i> Selected time period conflicts with slot boundaries (${slotStartFormatted} - ${slotEndFormatted})`;
                }
                
                statusElement.innerHTML = errorMessage;
                statusElement.className = 'alert alert-danger';
                submitButton.disabled = true;
                return false;
            }

            // Step 11: Auto-select package whose duration matches R (chargeable hours)
            const packageCards = document.querySelectorAll('.package-card');
            let selectedCard = null;
            let selectedDiscount = 0;
            let selectedPackageId = null;
            let selectedPackageName = '';

            packageCards.forEach(c => c.classList.remove('selected'));

            // Find exact duration match
            for (const card of packageCards) {
                const cardDuration = parseInt(card.dataset.duration) || 0;
                if (cardDuration === Math.round(R)) {
                    selectedCard = card;
                    break;
                }
            }

            // If no exact match, find closest higher duration
            if (!selectedCard) {
                let bestCard = null;
                let bestDuration = Infinity;
                for (const card of packageCards) {
                    const cardDuration = parseInt(card.dataset.duration) || 0;
                    if (cardDuration >= R && cardDuration < bestDuration) {
                        bestDuration = cardDuration;
                        bestCard = card;
                    }
                }
                selectedCard = bestCard;
            }

            if (selectedCard) {
                selectedDiscount = parseFloat(selectedCard.dataset.discount) || 0;
                selectedPackageId = selectedCard.dataset.packageId;
                selectedPackageName = selectedCard.querySelector('.card-title')?.textContent || 'Package';
            }

            if (!selectedCard) {
                statusElement.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i> No suitable package found for ' + R.toFixed(1) + ' chargeable hours';
                statusElement.className = 'alert alert-danger';
                submitButton.disabled = true;
                return false;
            }

            selectedCard.classList.add('selected');

            // Step 12: Update global variables
            selectedPackageDuration = parseInt(selectedCard.dataset.duration) || 0;
            selectedPackagePrice = parseFloat(selectedCard.dataset.price) || 0;
            selectedFreeHours = parseFloat(selectedCard.dataset.freeHours) || 4;

            // Step 13: Calculate charges — simplified: just the package price
            const grossTotalWithoutFacilities = selectedPackagePrice;
            
            // Calculate facilities charge separately using customer-entered hours
            let totalFixedFacilitiesCharge = 0;
            let totalUnitFacilitiesCharge = 0;
            let facilitiesBreakdown = [];
            
            // Fixed facilities: charge added once per selection
            document.querySelectorAll('.package-fixed-facility:checked').forEach(checkbox => {
                totalFixedFacilitiesCharge += parseFloat(checkbox.value || 0);
            });
            if (totalFixedFacilitiesCharge > 0) {
                facilitiesBreakdown.push(`Fixed: Rs. ${totalFixedFacilitiesCharge.toFixed(2)}`);
            }
            
            // Unit facilities: charge = price × hours_entered by customer
            document.querySelectorAll('.package-unit-facility:checked').forEach(checkbox => {
                const facilityId = checkbox.dataset.facilityId;
                const hoursInput = document.querySelector(`.package-unit-facility-hours[data-facility-id="${facilityId}"]`);
                const hours = parseFloat(hoursInput?.value || 0);
                const price = parseFloat(checkbox.dataset.price || 0);
                const charge = price * hours;
                totalUnitFacilitiesCharge += charge;
                if (charge > 0) {
                    facilitiesBreakdown.push(`${checkbox.nextElementSibling?.textContent?.trim() || ''}: ${hours}h × Rs. ${price.toFixed(2)} = Rs. ${charge.toFixed(2)}`);
                }
            });
            
            const totalFacilitiesCharge = totalFixedFacilitiesCharge + totalUnitFacilitiesCharge;
            const grossTotal = grossTotalWithoutFacilities + totalFacilitiesCharge;
            const finalTotal = grossTotal - selectedDiscount;


            // Step 14: Update UI
            document.getElementById('packageIdInput').value = selectedPackageId;
            
            // Update package charge display — just the package price
            const packageBasePrice = selectedPackagePrice;
            document.getElementById('packageCharge').innerHTML = `Rs. ${formatMoney(packageBasePrice)}<br><small class="text-muted">${selectedPackageName} (${selectedPackageDuration}h duration)</small>`;

            
            // Update facilities charge section
            const facilitiesChargeSection = document.getElementById('packageFacilitiesChargeSection');
            const facilitiesChargeDisplay = document.getElementById('packageFacilitiesCharge');
            let facilitiesHtml = '';
            if (totalFixedFacilitiesCharge > 0) {
                facilitiesHtml += `Fixed Facilities: Rs. ${formatMoney(totalFixedFacilitiesCharge)}`;
                if (totalUnitFacilitiesCharge > 0) facilitiesHtml += `<br>`;
            }
            if (totalUnitFacilitiesCharge > 0) {
                facilitiesHtml += `Unit Facilities: Rs. ${formatMoney(totalUnitFacilitiesCharge)}`;
            }
            
            if (totalFacilitiesCharge > 0) {
                facilitiesChargeSection.style.display = 'block';
                facilitiesChargeDisplay.innerHTML = `Rs. ${formatMoney(totalFacilitiesCharge)}`;
            } else {
                facilitiesChargeSection.style.display = 'none';
                facilitiesChargeDisplay.textContent = 'Rs. 0.00';
            }

            document.getElementById('packageTotalCharge').textContent = `Rs. ${formatMoney(finalTotal)}`;
            document.getElementById('packageTotalChargeInput').value = finalTotal.toFixed(2);

            // Step 15: Update form hidden inputs
            document.getElementById('packageStartTimeInput').value = startTime;
            document.getElementById('packageEndTimeInput').value = endTime;
            
            const actualStartTime = formatMinutesToTime(actualStartMinutes);
            const actualEndTime = formatMinutesToTime(actualEndMinutes);
            document.getElementById('packageActualStartTimeInput').value = actualStartTime;
            document.getElementById('packageActualEndTimeInput').value = actualEndTime;

            // Step 16: Show success message
            let successMessage = `<i class="fas fa-check-circle me-2"></i> ${selectedPackageName} selected (${R.toFixed(1)} chargeable hours)`;
            
            if (preArrange > 0 || postArrange > 0) {
                const details = [];
                if (preArrange > 0) details.push(`${preArrange}h setup`);
                details.push(`event (${formatTime(startTime)} - ${formatTime(endTime)})`);
                if (postArrange > 0) details.push(`${postArrange}h cleanup`);
                if (P > 0) details.push(`${P.toFixed(0)}h chargeable prepost`);
                
                successMessage = `<i class="fas fa-check-circle me-2"></i> ${selectedPackageName} (${details.join(' + ')})`;
            }
            
            statusElement.innerHTML = successMessage;
            statusElement.className = 'alert alert-success';
            submitButton.disabled = false;
            
            return true;
        }

        // ==================== COMMON UTILITY FUNCTIONS ====================
        function generateTimeOptions() {
            const times = [];
            for (let hour = 0; hour < 24; hour++) {
                for (let minute = 0; minute < 60; minute += 30) {
                    const time = `${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}`;
                    times.push(time);
                }
            }
            return times;
        }

        function populateTimeSelectors(startSelect, endSelect) {
            const times = generateTimeOptions();
            startSelect.innerHTML = '<option value="">Select start time</option>';
            endSelect.innerHTML = '<option value="">Select end time</option>';

            times.forEach(time => {
                const formattedTime = formatTime(time);
                const startOption = document.createElement('option');
                startOption.value = time;
                startOption.textContent = formattedTime;
                startSelect.appendChild(startOption);

                const endOption = document.createElement('option');
                endOption.value = time;
                endOption.textContent = formattedTime;
                endSelect.appendChild(endOption);
            });
        }

        function formatTime(timeStr) {
            const [hours, minutes] = timeStr.split(':');
            const hour = parseInt(hours);
            const period = hour >= 12 ? 'PM' : 'AM';
            const displayHour = hour % 12 || 12;
            return `${displayHour}:${minutes} ${period}`;
        }

        function timeToMinutes(timeStr) {
            const [hours, minutes] = timeStr.split(':').map(Number);
            return hours * 60 + minutes;
        }

        function formatMinutesToTime(minutes) {
            const hours = Math.floor(minutes / 60);
            const mins = minutes % 60;
            return `${String(hours).padStart(2, '0')}:${String(mins).padStart(2, '0')}`;
        }

        function calculateEndTime(startTime, durationHours) {
            const [hours, minutes] = startTime.split(':').map(Number);
            const totalMinutes = hours * 60 + minutes + (durationHours * 60);
            const endHours = Math.floor(totalMinutes / 60) % 24;
            const endMinutes = totalMinutes % 60;
            return `${String(endHours).padStart(2, '0')}:${String(endMinutes).padStart(2, '0')}`;
        }

        async function handleDateChange(selectedDate, timeSlotsContainer, customStartTime, customEndTime, customTimeStatus, formType)
        {
            // Reset selected time slot when date changes
            if (formType === 'regular') {
                selectedRegularTimeSlot = null;
                document.getElementById('regularStartTimeInput').value = '';
                document.getElementById('regularEndTimeInput').value = '';
                document.getElementById('regularReserveButton').disabled = true;
            } else {
                selectedPackageTimeSlot = null;
                document.getElementById('packageStartTimeInput').value = '';
                document.getElementById('packageEndTimeInput').value = '';
                document.getElementById('packageReserveButton').disabled = true;
            }

            customStartTime.value = '';
            customEndTime.value = '';
            customStartTime.disabled = true;
            customEndTime.disabled = true;

            if (fullyUnavailableDates.includes(selectedDate)) {
                timeSlotsContainer.innerHTML = `<div class="alert alert-danger"><i class="fas fa-times-circle me-2"></i> Completely unavailable</div>`;
                customTimeStatus.innerHTML = '<i class="fas fa-info-circle me-2"></i> Date unavailable';
                customTimeStatus.className = 'alert alert-danger';
                return;
            }

            timeSlotsContainer.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i></div>';
            customTimeStatus.innerHTML = '<i class="fas fa-info-circle me-2"></i> Loading availability...';
            customTimeStatus.className = 'alert alert-info';

            try {
                const hallId = '{{ $hall->id }}';
                const url = `/customer/hall/availability/${selectedDate}?hall_id=${hallId}`;
                console.log('Fetching availability from:', url); // Debug log
                
                const response = await fetch(url);
                
                console.log('Response status:', response.status); // Debug log
                console.log('Response headers:', response.headers.get('content-type')); // Debug log
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    console.error('Received non-JSON response:', text.substring(0, 200)); // Show first 200 chars
                    throw new Error('Server returned HTML instead of JSON. Please check the route and controller.');
                }

                const unavailablePeriods = await response.json();
                console.log('Received unavailable periods:', unavailablePeriods); // Debug log
                
                currentUnavailablePeriods = unavailablePeriods;
                renderTimeSlots(selectedDate, unavailablePeriods, timeSlotsContainer, customStartTime, customEndTime, customTimeStatus, formType);
            } catch (error) {
                console.error('Full error:', error); // Debug log
                timeSlotsContainer.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i> Error: ${error.message}</div>`;
                customTimeStatus.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i> Error loading availability. Check console for details.`;
                customTimeStatus.className = 'alert alert-danger';
            }
        }

        function renderTimeSlots(selectedDate, unavailablePeriods, timeSlotsContainer, customStartTime, customEndTime, customTimeStatus, formType) {
            timeSlotsContainer.innerHTML = '';
            currentAvailableSlots = [];

            if (!Array.isArray(unavailablePeriods)) {
                unavailablePeriods = Object.values(unavailablePeriods);
            }

            if (unavailablePeriods.length === 0) {
                const entireDaySlot = document.createElement('div');
                entireDaySlot.innerHTML = `<label class="btn btn-outline-success time-slot-label mb-2">
                <input type="radio" name="time_slot_${formType}" data-start="00:00" data-end="23:59" class="d-none">12:00 AM - 11:59 PM</label>`;
                timeSlotsContainer.appendChild(entireDaySlot);

                currentAvailableSlots = [{ start: '00:00', end: '23:59' }];

                entireDaySlot.querySelector('input[type="radio"]').addEventListener('change', function (e) {
                    handleTimeSlotSelection(e, formType, customStartTime, customEndTime, customTimeStatus, timeSlotsContainer);
                });
            }
            else {
                let availableSlots = [];
                let lastEnd = '00:00';

                unavailablePeriods.sort((a, b) => a.start_time.localeCompare(b.start_time));

                unavailablePeriods.forEach(period => {
                    if (lastEnd < period.start_time) {
                        availableSlots.push({ start: lastEnd, end: period.start_time });
                    }
                    lastEnd = period.end_time > lastEnd ? period.end_time : lastEnd;
                });

                if (lastEnd < '23:59') {
                    availableSlots.push({ start: lastEnd, end: '23:59' });
                }

                currentAvailableSlots = availableSlots;

                if (availableSlots.length > 0) {
                    availableSlots.forEach(slot => {
                        const start = formatTime(slot.start);
                        const end = formatTime(slot.end);

                        const slotElement = document.createElement('div');
                        slotElement.innerHTML =
                            `<label class="btn btn-outline-success time-slot-label mb-2">
                            <input type="radio" name="time_slot_${formType}" data-start="${slot.start}" data-end="${slot.end}" class="d-none">${start} - ${end}
                            </label>`;
                        timeSlotsContainer.appendChild(slotElement);
                    });

                    timeSlotsContainer.querySelectorAll('.time-slot-label input[type="radio"]').forEach(radio =>
                    {
                        radio.addEventListener('change', function (e) {
                            handleTimeSlotSelection(e, formType, customStartTime, customEndTime, customTimeStatus, timeSlotsContainer);
                        });
                    });

                } else {
                    timeSlotsContainer.innerHTML = `<div class="alert alert-danger"><i class="fas fa-times-circle me-2"></i> No available time slots</div>`;
                }
            }

            customStartTime.disabled = false;
            customEndTime.disabled = false;
            customEndTime.style.backgroundColor = '';

            customTimeStatus.innerHTML = '<i class="fas fa-info-circle me-2"></i> Select custom time or choose a slot above';
            customTimeStatus.className = 'alert alert-info';
        }

        // Filter time options based on selected slot
        function filterTimeOptions(startSelect, endSelect, slotStart, slotEnd) {
            const times = generateTimeOptions();
            const slotStartMinutes = timeToMinutes(slotStart);
            const slotEndMinutes = timeToMinutes(slotEnd);

            // Filter times that fall within the selected slot
            const filteredTimes = times.filter(time => {
                const timeMinutes = timeToMinutes(time);
                return timeMinutes >= slotStartMinutes && timeMinutes <= slotEndMinutes;
            });

            // Populate start time selector
            startSelect.innerHTML = '<option value="">Select start time</option>';
            filteredTimes.forEach(time => {
                const formattedTime = formatTime(time);
                const option = document.createElement('option');
                option.value = time;
                option.textContent = formattedTime;
                startSelect.appendChild(option);
            });

            // Populate end time selector
            endSelect.innerHTML = '<option value="">Select end time</option>';
            filteredTimes.forEach(time => {
                const formattedTime = formatTime(time);
                const option = document.createElement('option');
                option.value = time;
                option.textContent = formattedTime;
                endSelect.appendChild(option);
            });

            // Enable both selectors
            startSelect.disabled = false;
            endSelect.disabled = false;
        }

        // Handle time slot button selection
        function handleTimeSlotSelection(e, formType, customStartTime, customEndTime, customTimeStatus, timeSlotsContainer) {
            const startTime = e.target.dataset.start;
            const endTime = e.target.dataset.end;

            // Store the selected time slot boundaries (for validation only)
            if (formType === 'regular') {
                selectedRegularTimeSlot = {
                    start: startTime,
                    end: endTime
                };
            } else {
                selectedPackageTimeSlot = {
                    start: startTime,
                    end: endTime
                };
            }
                
            // Clear the time inputs - user must select manually
            customStartTime.value = '';
            customEndTime.value = '';
            if (formType === 'regular') {
                document.getElementById('regularStartTimeInput').value = '';
                document.getElementById('regularEndTimeInput').value = '';
            } else {
                document.getElementById('packageStartTimeInput').value = '';
                document.getElementById('packageEndTimeInput').value = '';
            }

            // Filter time options based on selected slot
            filterTimeOptions(customStartTime, customEndTime, startTime, endTime);

            // Highlight the selected slot
            document.querySelectorAll(`#${timeSlotsContainer.id} .time-slot-label`).forEach(label => {
                label.classList.remove('active');
            });
            e.target.closest('.time-slot-label').classList.add('active');

            customTimeStatus.innerHTML = '<i class="fas fa-info-circle me-2"></i> Time slot selected. Now choose your start and end times';
            customTimeStatus.className = 'alert alert-info';

            updateReserveButtonState();
        }

        function calculateDurationHours(startTime, endTime) {
            const startMinutes = timeToMinutes(startTime);
            const endMinutes = timeToMinutes(endTime);
            return (endMinutes - startMinutes) / 60;
        }

        function calculateFacilitiesCharge(durationHours) {
            let facilitiesCharge = 0;

            document.querySelectorAll('.facility-checkbox:checked').forEach(checkbox => {
                facilitiesCharge += parseFloat(checkbox.value);
            });

            document.querySelectorAll('.unit-facility:checked').forEach(checkbox => {
                const pricePerHour = parseFloat(checkbox.dataset.price);
                facilitiesCharge += durationHours * pricePerHour;
            });

            return facilitiesCharge;
        }

        function updateTotalCharge() {
            if (isPackageMode) {
                //document.getElementById('packageTotalCharge').textContent = `Rs. ${selectedPackagePrice.toFixed(2)}`;
            } else {
                const start = document.getElementById('regularStartTimeInput').value;
                const end = document.getElementById('regularEndTimeInput').value;
                const hallDiscount = {{ $hall->discount ?? 0 }};

                if (!start || !end) {
                    document.getElementById('propertyChargeRegular').textContent = 'Rs. 0.00';
                    document.getElementById('facilitiesChargeRegular').textContent = 'Rs. 0.00';
                    document.getElementById('regularTotalCharge').textContent = 'Rs. 0.00';
                    return;
                }

                const durationHours = calculateDurationHours(start, end);
                const propertyCharge = (durationHours * hourlyRate).toFixed(2);
                const facilitiesCharge = calculateFacilitiesCharge(durationHours);
                
                let totalCharge;
                if(hallDiscount>0)
                {
                    totalCharge = (parseFloat(propertyCharge) + facilitiesCharge - hallDiscount).toFixed(2);
                }
                else
                {
                    totalCharge = (parseFloat(propertyCharge) + facilitiesCharge).toFixed(2);
                }                

                document.getElementById('propertyChargeRegular').textContent = `Rs. ${propertyCharge}`;
                document.getElementById('facilitiesChargeRegular').textContent = `Rs. ${facilitiesCharge.toFixed(2)}`;
                document.getElementById('regularTotalCharge').textContent = `Rs. ${totalCharge}`;
            }
        }

        // Facility checkboxes event listeners
        document.querySelectorAll('.facility-checkbox, .unit-facility').forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                if (!isPackageMode) {
                    updateFacilitiesInputs();
                    updateTotalCharge();
                }
            });
        });

        // Handle Terms and Conditions button click
        const termsButtons = document.querySelectorAll('.open-terms-btn');
        termsButtons.forEach(button => {
            button.addEventListener('click', function () {
                const pdfUrl = this.getAttribute('data-pdf');

                if (pdfUrl) {
                    // Open PDF in new tab
                    window.open(pdfUrl, '_blank');
                } else {
                    console.error('PDF URL not found');
                    alert('Terms and conditions PDF is not available.');
                }
            });
        });

        // Handle all Terms & Conditions link clicks - open PDF in new tab
        document.querySelectorAll('.open-terms-link').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault(); // Prevent default link + Bootstrap modal behavior
                const pdfUrl = this.getAttribute('data-pdf');
                window.open(pdfUrl, '_blank');
            });
        });

    });

    // Map button handler
    document.querySelector('.open-map-btn')?.addEventListener('click', function () {
        const lat = this.dataset.lat;
        const lng = this.dataset.lng;
        const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
        if (isMobile) {
            window.location.href = `geo:${lat},${lng}?q=${lat},${lng}`;
        } else {
            window.open(`https://www.google.com/maps/search/?api=1&query=${lat},${lng}`, '_blank');
        }
    });
</script>
</body>

</html>
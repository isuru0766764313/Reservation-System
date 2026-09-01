<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hall Booking System - Enter Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .form-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .required:after {
            content: "*";
            color: red;
            margin-left: 4px;
        }
        .checkbox-grid {
            margin-bottom: 1.5rem;
        }
        @media (max-width: 768px) {
            .nav-brand-text {
                font-size: 1rem;
            }
            .checkbox-grid .col-md-2 {
                margin-bottom: 0.5rem;
            }
        }
        #map {
            height: 400px;
            width: 100%;
            border-radius: 8px;
            margin: 15px 0;
            border: 1px solid #ddd;
        }
        .map-overlay {
            position: absolute;
            top: 10px;
            left: 50px;
            z-index: 100;
            background: rgba(255, 255, 255, 0.9);
            padding: 8px 15px;
            border-radius: 4px;
            font-size: 14px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .location-preview {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            font-size: 14px;
        }
        .coordinates {
            font-family: monospace;
            background: #e9ecef;
            padding: 3px 6px;
            border-radius: 3px;
            margin-top: 5px;
        }
        @media (max-width: 768px) {
            .nav-brand-text {
                font-size: 1rem;
            }
            .checkbox-grid .col-md-2 {
                margin-bottom: 0.5rem;
            }
            #map {
                height: 300px;
            }
        }
        /****for image section****/
        .imagePreview
        {
            width: 100%;
            height: 180px;
            background-position: center center;
            background-color: #fff;
            background-size: cover;
            background-repeat: no-repeat;
            display: inline-block;
            box-shadow: 0px -3px 6px 2px rgba(0,0,0,0.2);
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .btn-primary
        {
            display: block;
            border-radius: 0px;
            box-shadow: 0px 4px 6px 2px rgba(0,0,0,0.2);
            margin-top: -5px;
            width: 100%;
            background: #8947e3 !important;
            border-color: #8947e3 !important;
            color: #fff !important;
        }
        .btn-primary:hover, .btn-primary:focus
        {
            background: #7a3ad0 !important;
            border-color: #7a3ad0 !important;
            color: #fff !important;
        }
        .card-header.bg-primary, .bg-primary
        {
            background: #8947e3 !important;
        }
        .imgUp
        {
            margin-bottom: 15px;
            position: relative;
        }
        .del
        {
            position: absolute;
            top: 0px;
            right: 15px;
            width: 30px;
            height: 30px;
            text-align: center;
            line-height: 30px;
            background-color: rgba(255,255,255,0.8);
            cursor: pointer;
            color: #000;
            font-weight: bold;
            border-radius: 50%;
            border: 1px solid #ccc;
        }
        .imgAdd
        {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #8947e3;
            color: #fff;
            box-shadow: 0px 0px 2px 1px rgba(0,0,0,0.2);
            text-align: center;
            line-height: 30px;
            margin-top: 80px;
            cursor: pointer;
            font-size: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand nav-brand-text" href="{{ route('admin.dashboard.route') }}">
                <i class="fas fa-building me-2"></i>Hall Booking System Admin Panel
            </a>
            <div class="d-flex align-items-center">
                <span class="text-white me-3">
                    <i class="fas fa-user-circle"></i> 
                    {{ auth()->guard('admin')->user()->company_name }}
                </span>
                <form method="POST" action="{{ route('admin.logout.route') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Form Container -->
    <div class="container py-4">
        <h2 class="mb-4">
            @if (isset($hall))
                Update Property :  {{ $hall->name }}
            @else
                Add New Hall
            @endif
        </h2>

        @if($errors->any())
        <div class="alert alert-danger">
            <h5>Error!</h5>
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <div class="alert alert-info" id="mapMessage" style="display: none;">
            <i class="fas fa-info-circle me-2"></i> 
            <span id="mapMessageText">Click on the map to set your hall location</span>
        </div>

        <form method="POST" action="{{ isset($hall) ? route('halls.update', $hall) : route('insert.hall.data.route') }}" enctype="multipart/form-data">
            @csrf
            @if (isset($hall))
            @method('PUT')
            @endif
            <!-- Hall Details Section -->
            <div class="form-section">
                <h4 class="mb-4"><i class="fas fa-info-circle me-2"></i>Hall Details</h4>
                
                <div class="row g-3">
                    <!-- Name -->
                    <div class="col-md-6">
                        <label class="form-label required">Hall Name</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name', $hall->name ?? '') }}" required>
                    </div>

                    <!-- Type (6x4 Grid) -->
                    <div class="col-12 checkbox-grid mb-4">
                        <label class="form-label required">Hall Type</label>
                        
                        <!-- Row 1 -->
                        <div class="row mb-3">
                            <div class="col-md-2 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" value="wedding" id="typeWedding" {{ (old('type', $hall->type ?? '') == 'wedding') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="typeWedding">Wedding</label>
                                </div>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" value="party" id="typeParty" {{ (old('type', $hall->type ?? '') == 'party') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="typeParty">Party</label>
                                </div>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" value="exhibition" id="typeExhibition" {{ (old('type', $hall->type ?? '') == 'exhibition') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="typeExhibition">Exhibition</label>
                                </div>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" value="reception" id="typeReception" {{ (old('type', $hall->type ?? '') == 'reception') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="typeReception">Reception</label>
                                </div>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" value="sport" id="typeSport" {{ (old('type', $hall->type ?? '') == 'sport') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="typeSport">Sport</label>
                                </div>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" value="arena" id="typeArena" {{ (old('type', $hall->type ?? '') == 'arena') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="typeArena">Arena</label>
                                </div>
                            </div>
                        </div>

                        <!-- Row 2 -->
                        <div class="row mb-3">
                            <div class="col-md-2 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" value="concert" id="typeConcert" {{ (old('type', $hall->type ?? '') == 'concert') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="typeConcert">Concert</label>
                                </div>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" value="memorial" id="typeMemorial" {{ (old('type', $hall->type ?? '') == 'memorial') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="typeMemorial">Memorial</label>
                                </div>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" value="lecture" id="typeLecture" {{ (old('type', $hall->type ?? '') == 'lecture') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="typeLecture">Lecture</label>
                                </div>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" value="building" id="typeBuilding" {{ (old('type', $hall->type ?? '') == 'building') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="typeBuilding">Building</label>
                                </div>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" value="floor" id="typeFloor" {{ (old('type', $hall->type ?? '') == 'floor') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="typeFloor">Floor</label>
                                </div>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" value="room" id="typeRoom" {{ (old('type', $hall->type ?? '') == 'room') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="typeRoom">Room</label>
                                </div>
                            </div>
                        </div>

                        <!-- Row 3 -->
                        <div class="row mb-3">
                            <div class="col-md-2 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" value="outdoortheator" id="typeOutdoortheator" {{ (old('type', $hall->type ?? '') == 'outdoortheator') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="typeOutdoortheator">Outdoor Theater</label>
                                </div>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" value="multipurpose" id="typeMultipurpose" {{ (old('type', $hall->type ?? '') == 'multipurpose') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="typeMultipurpose">Multipurpose</label>
                                </div>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" value="resorts" id="typeResorts" {{ (old('type', $hall->type ?? '') == 'resorts') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="typeResorts">Resorts</label>
                                </div>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" value="bangalow" id="typeBangalow" {{ (old('type', $hall->type ?? '') == 'bangalow') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="typeBangalow">Circuit Bungalows</label>
                                </div>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" value="conference" id="typeConference" {{ (old('type', $hall->type ?? '') == 'conference') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="typeConference">Conference</label>
                                </div>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" value="banquet" id="typeBanquet" {{ (old('type', $hall->type ?? '') == 'banquet') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="typeBanquet">Banquet Hall</label>
                                </div>
                            </div>
                        </div>

                        <!-- Row 4 -->
                        <div class="row mb-3">
                            <div class="col-md-2 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" value="convention" id="typeConvention" {{ (old('type', $hall->type ?? '') == 'convention') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="typeConvention">Exam Center</label>
                                </div>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" value="crematorium" id="typeCrematorium" {{ (old('type', $hall->type ?? '') == 'crematorium') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="typeCrematorium">Crematorium</label>
                                </div>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" value="auditorium" id="typeAuditorium" {{ (old('type', $hall->type ?? '') == 'auditorium') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="typeAuditorium">Auditorium</label>
                                </div>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" value="community" id="typeCommunity" {{ (old('type', $hall->type ?? '') == 'community') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="typeCommunity">Community Center</label>
                                </div>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" value="stadium" id="typeStadium" {{ (old('type', $hall->type ?? '') == 'stadium') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="typeStadium">Stadium</label>
                                </div>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" value="outdoorground" id="typeGround" {{ (old('type', $hall->type ?? '') == 'outdoorground') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="typeGround">Play Ground</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Capacity, Cancellation Fee & Refundable Deposit -->
                    <div class="row g-3 mt-3">
                        <div class="col-md-4">
                            <label class="form-label required">Capacity</label>
                            <input type="number" class="form-control" name="capacity" step="1" value="{{ old('price', $hall->capacity ?? '') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Cancellation Fee (LKR)</label>
                            <input type="number" class="form-control" name="cancellation_fee" step="0.01" value="{{ old('discount', $hall->cancellation_fee ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Refundable Deposit Amount (LKR)</label>
                            <input type="number" class="form-control" name="deposit" step="0.01" value="{{ old('deposit', $hall->deposit ?? '') }}">
                        </div>
                        <!--<div class="col-md-4">
                            <label class="form-label required">Advance Amount (LKR)</label>
                            <input type="number" class="form-control" name="advance_amount" step="0.01" value="{{ old('advance_amount', $hall->advance_amount ?? '') }}">
                        </div>-->
                    </div>


                    <!-- Booking Method Selection -->
                    <div class="row g-3 mt-3">
                        <div class="col-12">
                            <label class="form-label required">Booking Method</label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="booking_method" id="booking_regular" value="regular"
                                        {{ (old('booking_method', $hall->booking_method ?? 'both') == 'regular') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="booking_regular">Regular Booking</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="booking_method" id="booking_package" value="package"
                                        {{ (old('booking_method', $hall->booking_method ?? 'both') == 'package') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="booking_package">Package Booking</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="booking_method" id="booking_both" value="both"
                                        {{ (old('booking_method', $hall->booking_method ?? 'both') == 'both') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="booking_both">Both</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Price & Discount -->
                    <div class="row g-3 mt-3" id="priceSection">
                        <div class="col-md-4">
                            <label class="form-label required">Price Per Hour (LKR)</label>
                            <input type="number" class="form-control" name="price" step="0.01" value="{{ old('price', $hall->price ?? '') }}" required>
                        </div>
                        <div class="col-md-4" style="display: none;">
                            <label class="form-label required">Disdcount (LKR)</label>
                            <input type="number" class="form-control" name="discount" step="0.01" value="{{ old('discount', $hall->discount ?? '') }}" style="display: none;">
                        </div>
                    </div>

                    <!-- Extended Time Settings -->
                    <div class="row g-3 mt-3" id="setupSection">
                        <div class="col-md-6">
                            <label class="form-label">Max Pre-arrange (Setup) Hours</label>
                            <select class="form-select" name="max_pre_arrange_hours">
                                @for ($i = 0; $i <= 24; $i++)
                                    <option value="{{ $i }}" {{ (old('max_pre_arrange_hours', $hall->max_pre_arrange_hours ?? 5) == $i) ? 'selected' : '' }}>{{ $i }} hours</option>
                                @endfor
                            </select>
                            <small class="form-text text-muted">Maximum setup time before event (customer can select 0 to this value)</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Max Post-arrange (Cleanup) Hours</label>
                            <select class="form-select" name="max_post_arrange_hours">
                                @for ($i = 0; $i <= 24; $i++)
                                    <option value="{{ $i }}" {{ (old('max_post_arrange_hours', $hall->max_post_arrange_hours ?? 5) == $i) ? 'selected' : '' }}>{{ $i }} hours</option>
                                @endfor
                            </select>
                            <small class="form-text text-muted">Maximum cleanup time after event (customer can select 0 to this value)</small>
                        </div>
                    </div>

                    <!--Fixed Price Facilities-->
                    <div class="card mt-4">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Fixed Price Facilities</h5>
                            <button type="button" class="btn btn-sm btn-light" id="addFixedFacility">
                                <i class="bi bi-plus-circle"></i> Add Fixed Priced Facilities
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="fixedFacilitiesTable">
                                    <thead>
                                        <tr>
                                            <th>Facility Name</th>
                                            <th>Price (Rs.)</th>
                                            <th class="table-action">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Existing Facilities -->
                                        @php
                                            // Get old input for fixedpricefacility if validation failed
                                            $oldFixedFacilities = old('fixedpricefacility', []);
                                            // If we're editing an existing hall and there's no old input, use the hall's facilities
                                            if (isset($hall) && $hall->fixedfacilities->count() > 0 && empty($oldFixedFacilities)) {
                                                $fixedFacilitiesToShow = $hall->fixedfacilities;
                                            } else {
                                                // Use old input (could be empty array if creating new with no old input)
                                                $fixedFacilitiesToShow = collect($oldFixedFacilities)->map(function($item, $index) {
                                                    return (object) [
                                                        'name' => $item['name'] ?? '',
                                                        'charge' => $item['charge'] ?? ''
                                                    ];
                                                });
                                                // If empty, add one empty row
                                                if ($fixedFacilitiesToShow->isEmpty()) {
                                                    $fixedFacilitiesToShow = collect([(object) ['name' => '', 'charge' => '']]);
                                                }
                                            }
                                        @endphp
                                        
                                        @foreach($fixedFacilitiesToShow as $index => $facility)
                                            <tr class="fixed-price-facility-row">
                                                <td>
                                                    <input type="text" class="form-control fixed-price-facity-name-input"
                                                        placeholder="e.g., WiFi" name="fixedpricefacility[{{ $index }}][name]"
                                                        value="{{ $facility->name }}">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control fixed-price-facity-charge-input" placeholder="0.00"
                                                        step="0.01" name="fixedpricefacility[{{ $index }}][charge]"
                                                        value="{{ $facility->charge }}">
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger remove-row1">
                                                        <i class="bi bi-trash"></i> Remove
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                    <!--Unit Price Facilities-->
                    <div class="card mt-4">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Unit Price Facilities (Per Hour)</h5>
                            <button type="button" class="btn btn-sm btn-light" id="addUnitFacility">
                                <i class="bi bi-plus-circle"></i> Add Unit Priced Facilities
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="unitFacilitiesTable">
                                    <thead>
                                        <tr>
                                            <th>Facility Name</th>
                                            <th>Price Per Hour (Rs.)</th>
                                            <th class="table-action">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            // Get old input for unitpricefacility if validation failed
                                            $oldUnitFacilities = old('unitpricefacility', []);
                                            // If we're editing an existing hall and there's no old input, use the hall's facilities
                                            if (isset($hall) && $hall->unitfacilities->count() > 0 && empty($oldUnitFacilities)) {
                                                $unitFacilitiesToShow = $hall->unitfacilities;
                                            } else {
                                                // Use old input (could be empty array if creating new with no old input)
                                                $unitFacilitiesToShow = collect($oldUnitFacilities)->map(function($item, $index) {
                                                    return (object) [
                                                        'name' => $item['name'] ?? '',
                                                        'charge' => $item['charge'] ?? ''
                                                    ];
                                                });
                                                // If empty, add one empty row
                                                if ($unitFacilitiesToShow->isEmpty()) {
                                                    $unitFacilitiesToShow = collect([(object) ['name' => '', 'charge' => '']]);
                                                }
                                            }
                                        @endphp
                                        
                                        @foreach($unitFacilitiesToShow as $index => $facility)
                                            <tr class="unit-price-facility-row">
                                                <td>
                                                    <input type="text" class="form-control unit-price-facity-name-input"
                                                        placeholder="e.g., Generator" name="unitpricefacility[{{ $index }}][name]"
                                                        value="{{ $facility->name }}">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control unit-price-facity-charge-input" placeholder="0.00"
                                                        step="0.01" name="unitpricefacility[{{ $index }}][charge]"
                                                        value="{{ $facility->charge }}">
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger remove-row2">
                                                        <i class="bi bi-trash"></i> Remove
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>                   

                    <!-- Description -->
                    <div class="col-12 mt-3">
                        <label class="form-label required">Description</label>
                        <textarea class="form-control" name="description" rows="3" required>{{ old('description', $hall->description ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Location Section -->
            <div class="form-section">
                <h4 class="mb-4"><i class="fas fa-map-marker-alt me-2"></i>Location Details</h4>
            
                <div class="row g-3">
                    <!-- Address -->
                    <div class="col-md-6">
                        <label class="form-label required">Address</label>
                        <input type="text" class="form-control" name="address" id="address"
                            value="{{ old('address', $hall->address ?? '') }}" required>
                    </div>
            
                    <!-- Location Hierarchy -->
                    <div class="col-md-2">
                        <label class="form-label required">Province</label>
                        <input type="text" class="form-control" name="province" id="province"
                            value="{{ old('province', $hall->province ?? '') }}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label required">District</label>
                        <input type="text" class="form-control" name="district" id="district"
                            value="{{ old('district', $hall->district ?? '') }}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label required">Area</label>
                        <input type="text" class="form-control" name="area" id="area" value="{{ old('area', $hall->area ?? '') }}"
                            required>
                    </div>
            
                    <!-- Hidden fields for coordinates -->
                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $hall->latitude ?? '') }}">
                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $hall->longitude ?? '') }}">
            
                    <!-- Google Map Container -->
                    <div class="col-12">
                        <label class="form-label required">Click on the point of location of your entity on the map</label>
                        <div style="position: relative;">
                            <div id="map"></div>
                        </div>
            
                        <div class="location-preview">
                            <strong>Selected Location:</strong>
                            <div id="addressPreview">
                                @if(isset($hall) && $hall->address)
                                    {{ $hall->address }}
                                @else
                                    No location selected yet
                                @endif
                            </div>
                            <div class="coordinates" id="coordinatesPreview">
                                @if(isset($hall) && $hall->latitude && $hall->longitude)
                                    Coordinates: Latitude: {{ number_format($hall->latitude, 6) }}, Longitude:
                                    {{ number_format($hall->longitude, 6) }}
                                @else
                                    Coordinates: Not selected
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact & Availability Section -->
            <div class="form-section">
                <div class="row g-3">
                    <div class="form-section">
                        <h4 class="mb-4"><i class="fas fa-calendar-alt me-2"></i>Unavailability of a Property</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="availability-table">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Date</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // Get old input for availability if validation failed
                                        $oldAvailability = old('availability', []);
                                        // If we're editing an existing hall and there's no old input, use the hall's availability
                                        if (isset($hall) && $hall->availability->count() > 0 && empty($oldAvailability)) {
                                            $availabilityToShow = $hall->availability;
                                        } else {
                                            // Use old input (could be empty array if creating new with no old input)
                                            $availabilityToShow = collect($oldAvailability)->map(function($item, $index) {
                                                return (object) [
                                                    'date' => $item['date'] ?? '',
                                                    'start_time' => $item['start_time'] ?? '',
                                                    'end_time' => $item['end_time'] ?? ''
                                                ];
                                            });
                                            // If empty, add one empty row
                                            if ($availabilityToShow->isEmpty()) {
                                                $availabilityToShow = collect([(object) ['date' => '', 'start_time' => '', 'end_time' => '']]);
                                            }
                                        }
                                    @endphp
                                    
                                    @foreach($availabilityToShow as $index => $slot)
                                        <tr class="availability-row">
                                            <td>
                                                <input type="date" class="form-control date-input" name="availability[{{ $index }}][date]" min="{{ now()->format('Y-m-d') }}" value="{{ $slot->date }}" required>
                                            </td>
                                            <td>
                                                <input type="time" class="form-control start-time" name="availability[{{ $index }}][start_time]" value="{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}" required>
                                            </td>
                                            <td>
                                                <input type="time" class="form-control end-time" name="availability[{{ $index }}][end_time]" data-start-time="availability[{{ $index }}][start_time]" value="{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}" required>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm remove-row">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-primary" id="add-row">
                            <i class="fas fa-plus-circle me-2"></i>Add Time Slot
                        </button>
                    </div>
                </div>
            </div>

            <!-- Image Upload Section -->
            <div class="form-section">
                <h4 class="mb-4">Upload Images (Max 5) :</h4>
            
                <div class="container-fluid">
                    <div class="row align-items-start" id="imageUploadRow">
                        <!-- Existing Images -->
                        @if(isset($hall) && !empty($hall->images))
                            @foreach($hall->images as $index => $imagePath)
                                <div class="col-sm-2 imgUp mb-3">
                                    <div class="imagePreview" style="background-image: url('{{ asset('storage/' . $imagePath) }}');"></div>
                                    <label class="btn btn-primary w-100">
                                        Replace
                                        <input type="file" name="images[{{ $index }}]" class="uploadFile img"
                                            style="width: 0px; height: 0px; overflow: hidden;" accept="image/*">
                                    </label>
                                    <i class="fa fa-times del" data-existing-image="{{ $imagePath }}"></i>
                                    <input type="hidden" name="existing_images[]" value="{{ $imagePath }}">
                                </div>
                            @endforeach
                        @endif
            
                        <!-- Empty upload boxes for new images (only show if we have less than 5 images) -->
                        @php
                            $existingCount = isset($hall) ? count($hall->images) : 0;
                            $emptySlots = 5 - $existingCount;
                        @endphp
                        @if($emptySlots > 0)
                            <!-- Initial empty upload box -->
                            <div class="col-sm-2 imgUp mb-3">
                                <div class="imagePreview"></div>
                                <label class="btn btn-primary w-100">
                                    Upload
                                    <input type="file" name="images[]" class="uploadFile img" style="width: 0px; height: 0px; overflow: hidden;" accept="image/*">
                                </label>
                                <i class="fa fa-times del"></i>
                            </div>

                            <!-- Plus button for adding more empty slots -->
                            @if($emptySlots > 1)
                                <div class="col-sm-2">
                                    <i class="fa fa-plus imgAdd" style="align-self: center; margin-left: 10px;"></i>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <!--PDF section-->
            <div class="form-section">
                <h4 class="mb-4">Upload Terms and Conditions (PDF) :</h4>
            
                <!-- Display existing PDF if available -->
                @if(isset($hall) && $hall->pdf)
                    <div class="mb-3">
                        <label class="form-label">Current PDF File:</label>
                        <div class="d-flex align-items-center p-3 border rounded bg-light">
                            <i class="fas fa-file-pdf text-danger me-2 fs-4"></i>
                            <div>
                                <a href="{{ route('view.hall.terms', $hall) }}" target="_blank" class="text-decoration-none fw-bold">
                                    Open the terms and conditions
                                </a>
                            </div>
                        </div>
                    </div>
                    <hr>
                @endif            
                <!-- PDF Upload -->
                <div class="mb-3">
                    <label class="form-label">
                        @if(isset($hall) && $hall->pdf)
                            Upload New PDF (Optional - replaces current file)
                        @else
                            Upload Terms and Conditions (PDF)
                        @endif
                    </label>
                    <input type="file" name="pdf" class="form-control" accept=".pdf" id="pdfInput">
                    @error('pdf')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        Maximum file size: 10MB. Only PDF files are accepted.
                    </div>
                    <div id="pdfFilename" class="mt-2" style="display: none;">
                        <i class="fas fa-file-pdf text-danger me-2"></i>
                        <span id="pdfName"></span>
                        <small class="text-muted">(Please re-select if you want to change)</small>
                    </div>
                </div>
            </div>


            <!-- Clearence PDF section-->
            <div class="form-section">
                <h4 class="mb-4">Upload Clearence form (PDF) :</h4>
            
                <!-- Display existing PDF if available -->
                @if(isset($hall) && $hall->clearence_form)
                    <div class="mb-3">
                        <label class="form-label">Current Clearence Form:</label>
                        <div class="d-flex align-items-center p-3 border rounded bg-light">
                            <i class="fas fa-file-pdf text-danger me-2 fs-4"></i>
                            <div>
                                <a href="{{ route('view.hall.clearence', $hall) }}" target="_blank" class="text-decoration-none fw-bold">
                                    Open the clearence form
                                </a>
                            </div>
                        </div>
                    </div>
                    <hr>
                @endif            
                <!-- Clerence PDF Upload -->
                <div class="mb-3">
                    <label class="form-label">
                        @if(isset($hall) && $hall->clearence_form)
                            Upload New Clearence Form (Optional - replaces current file)
                        @else
                            Upload Clearence File (PDF)
                        @endif
                    </label>
                    <input type="file" name="clearence_form" class="form-control" accept=".pdf" id="clearenceInput">
                    @error('clearence_form')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        Maximum file size: 10MB. Only PDF files are accepted.
                    </div>
                    <div id="clearenceFilename" class="mt-2" style="display: none;">
                        <i class="fas fa-file-pdf text-danger me-2"></i>
                        <span id="clearenceName"></span>
                        <small class="text-muted">(Please re-select if you want to change)</small>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-2"></i>
                    @if (isset($hall))
                        Update Hall Details
                    @else
                        Save Hall Details
                    @endif                    
                </button>
            </div>          
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Google Maps API with YOUR API KEY -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBTzYyCvcUNmjoNZSMIuA16xV6_uUFkK2k&libraries=places"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded',function()
    {
        // Initialize Google Map centered on Sri Lanka
        const map = new google.maps.Map(document.getElementById("map"),
        {
            center: { lat: 7.8731, lng: 80.7718 }, // Sri Lanka coordinates
            zoom: 8,
            streetViewControl: false,
            mapTypeControl: true,
            mapTypeControlOptions: {
                style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
            }
        });

        let marker = null;
        const geocoder = new google.maps.Geocoder();
        const infoWindow = new google.maps.InfoWindow();

        // Show map loading message
        document.getElementById('mapMessage').style.display = 'block';
        document.getElementById('mapMessageText').textContent = 'Loading map... Please wait';

        // Create or move the location marker (shared by click and saved-location restore)
        function placeMarker(position)
        {
            if (marker) {
                marker.setPosition(position);
            } else {
                marker = new google.maps.Marker({
                    position: position,
                    map: map,
                    draggable: true,
                    title: "Hall Location",
                    animation: google.maps.Animation.DROP
                });

                // Update on marker drag
                marker.addListener("dragend", (event) =>
                {
                    const newPosition = marker.getPosition();
                    document.getElementById('latitude').value = newPosition.lat();
                    document.getElementById('longitude').value = newPosition.lng();
                    const newCoords = `Latitude: ${newPosition.lat().toFixed(6)}, Longitude: ${newPosition.lng().toFixed(6)}`;
                    document.getElementById('coordinatesPreview').textContent = `Coordinates: ${newCoords}`;
                    reverseGeocode(newPosition);
                });
            }
        }

        // If editing an existing hall with saved coordinates, show its saved location
        const savedLat = document.getElementById('latitude').value;
        const savedLng = document.getElementById('longitude').value;
        if (savedLat && savedLng) {
            const savedPosition = { lat: parseFloat(savedLat), lng: parseFloat(savedLng) };
            map.setCenter(savedPosition);
            map.setZoom(15);
            placeMarker(savedPosition);
            document.getElementById('mapMessage').style.display = 'none';
        }

        // Add click listener for the map
        map.addListener("click", (mapsMouseEvent) => {
            const clickedLocation = mapsMouseEvent.latLng;
            
            // Update hidden fields
            document.getElementById('latitude').value = clickedLocation.lat();
            document.getElementById('longitude').value = clickedLocation.lng();
            
            // Update coordinates preview
            const coordinates = `Latitude: ${clickedLocation.lat().toFixed(6)}, Longitude: ${clickedLocation.lng().toFixed(6)}`;
            document.getElementById('coordinatesPreview').textContent = `Coordinates: ${coordinates}`;
            
            // Place or move marker
            placeMarker(clickedLocation);
            
            // Reverse geocode to get address details
            reverseGeocode(clickedLocation);
            
            // Hide loading message
            document.getElementById('mapMessage').style.display = 'none';
        });

        // Function to reverse geocode coordinates
        function reverseGeocode(latLng)
        {
            geocoder.geocode({ location: latLng }, (results, status) => {
                if (status === "OK" && results[0]) {
                    // Update address preview
                    document.getElementById('addressPreview').textContent = results[0].formatted_address;
                    
                    // Parse address components
                    const addressComponents = results[0].address_components;
                    let province = '';
                    let district = '';
                    let area = '';
                    
                    addressComponents.forEach(component => {
                        if (component.types.includes("administrative_area_level_1")) {
                            province = component.long_name;
                        }
                        if (component.types.includes("administrative_area_level_2")) {
                            district = component.long_name;
                        }
                        if (component.types.includes("sublocality") || component.types.includes("locality")) {
                            area = component.long_name;
                        }
                    });
                    
                    // Update form fields
                    document.getElementById('address').value = results[0].formatted_address;
                    document.getElementById('province').value = province;
                    document.getElementById('district').value = district;
                    document.getElementById('area').value = area;
                    
                    // Show address in info window
                    infoWindow.setContent(results[0].formatted_address);
                    infoWindow.open(map, marker);
                } else {
                    document.getElementById('addressPreview').textContent = "Address not found";
                }
            });
        }

        // Map loaded successfully
        google.maps.event.addDomListener(map, 'idle', function()
        {
            document.getElementById('mapMessageText').textContent = 'Click on the map to set your hall location';
        });


        /*******************************************Booking method dependent field visibility***********************************/

        const bookingMethodRadios = document.querySelectorAll('input[name="booking_method"]');
        const priceSection = document.getElementById('priceSection');
        const setupSection = document.getElementById('setupSection');
        const priceInput = document.querySelector('input[name="price"]');

        function updateBookingMethodFields()
        {
            const selected = document.querySelector('input[name="booking_method"]:checked');
            if (!selected) return;
            const value = selected.value;

            const showPrice = (value === 'regular' || value === 'both');
            const showSetup = (value === 'package' || value === 'both');

            priceSection.style.display = showPrice ? '' : 'none';
            setupSection.style.display = showSetup ? '' : 'none';

            if (priceInput) priceInput.required = showPrice;
        }

        bookingMethodRadios.forEach(radio => radio.addEventListener('change', updateBookingMethodFields));
        updateBookingMethodFields();


        /*******************************************Fixed-Price facilities table, Unit-Price facilitites table , packages table***********************************/

        // Initialize row counts based on current rows
        let fixedpricerowcount = document.querySelectorAll('.fixed-price-facility-row').length;

        /*Fixed price facilities table **/

        document.getElementById('addFixedFacility').addEventListener('click',function ()
        {
            const newRow = document.createElement('tr');
            newRow.className = 'fixed-price-facility-row';
            newRow.innerHTML =`
            <td>
            <input type="text" class="form-control fixed-price-facity-name-input" placeholder="e.g., WiFi" name="fixedpricefacility[${fixedpricerowcount}][name]">
            </td>
            <td>
            <input type="number" class="form-control fixed-price-facity-charge-input" placeholder="0.00" step="0.01" name="fixedpricefacility[${fixedpricerowcount}][charge]">
            </td>
            <td>
            <button type="button" class="btn btn-sm btn-danger remove-row1"><i class="bi bi-trash"></i>Remove</button>
            </td>
            `;
            document.querySelector('#fixedFacilitiesTable tbody').appendChild(newRow);
            fixedpricerowcount++;
        });

        document.addEventListener('click', function (e)
        {
            if (e.target.closest('.remove-row1'))
                {
                const row = e.target.closest('.fixed-price-facility-row');
                if (document.querySelectorAll('.fixed-price-facility-row').length > 1){
                    row.remove();
                }
                else {
                    alert('At least one time slot is required');
                }
            }
        });

        /* Unit price facilities table */

        let unitpricerowcount = document.querySelectorAll('.unit-price-facility-row').length;

        document.getElementById('addUnitFacility').addEventListener('click',function ()
        {
            const newRow = document.createElement('tr');
            newRow.className = 'unit-price-facility-row';
            newRow.innerHTML =`
            <td>
            <input type="text" class="form-control unit-price-facity-name-input" placeholder="e.g., Generator" name="unitpricefacility[${unitpricerowcount}][name]">
            </td>
            <td>
            <input type="number" class="form-control unit-price-facity-charge-input" placeholder="0.00" step="0.01" name="unitpricefacility[${unitpricerowcount}][charge]">
            </td>
            <td>
            <button type="button" class="btn btn-sm btn-danger remove-row2"><i class="bi bi-trash"></i>Remove</button>
            </td>
            `;
            document.querySelector('#unitFacilitiesTable tbody').appendChild(newRow);
            unitpricerowcount++;
        });

        document.addEventListener('click', function (e)
        {
            if (e.target.closest('.remove-row2'))
                {
                const row = e.target.closest('.unit-price-facility-row');
                if (document.querySelectorAll('.unit-price-facility-row').length > 1){
                    row.remove();
                }
                else {
                    alert('At least one time slot is required');
                }
            }
        });


        /*************************************************************Availability table functionality**********************************************************/

        let rowCount = document.querySelectorAll('.availability-row').length;
        // Add new row
        document.getElementById('add-row').addEventListener('click', function()
        {
            const newRow = document.createElement('tr');
            newRow.className = 'availability-row';
            newRow.innerHTML = 
            `
            <td>
            <input type="date" class="form-control date-input" name="availability[${rowCount}][date]" min="{{ now()->format('Y-m-d') }}" required>
            </td>
            <td>
            <input type="time" class="form-control start-time" name="availability[${rowCount}][start_time]" required>
            </td>
            <td>
            <input type="time" class="form-control end-time" name="availability[${rowCount}][end_time]" data-start-time="availability[${rowCount}][start_time]" required>
            </td>
            <td>
            <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
            </td>
            `;
            document.querySelector('#availability-table tbody').appendChild(newRow);
            rowCount++;
        });
        // Remove row
        document.addEventListener('click', function(e)
        {
            if(e.target.closest('.remove-row'))
            {
                const row = e.target.closest('.availability-row');
                if(document.querySelectorAll('.availability-row').length > 1)
                {
                    row.remove();
                }
                else
                {
                    alert('At least one time slot is required');
                }
            }
        });
        // Real-time end time validation
        document.addEventListener('change', function(e)
        {
            if(e.target.classList.contains('end-time'))
            {
                const startTime = document.querySelector(`[name="${e.target.dataset.startTime}"]`);
                if(startTime.value && e.target.value <= startTime.value)
                {
                    alert('End time must be after start time');
                    e.target.value = '';
                }
            }
        });
    });

    /***************************************************Image script section*************************************************/


    $(document).ready(function ()
    {
    const maxImages = 5;
    // CORRECTED: Only count the .imgUp elements that are actually on the page
    let totalImageCount = $('.imgUp').length;

    // Function to update the plus button visibility
    function updatePlusButton() {
        if (totalImageCount >= maxImages) {
            $('.imgAdd').hide();
        } else {
            $('.imgAdd').show();
        }
    }

    // Initialize button state on page load
    updatePlusButton();

    // Add new image box
    $(document).on("click", ".imgAdd", function ()
    {
        // CORRECTED: Check against the actual count of elements
        if (totalImageCount < maxImages) {
            const newBox = $(`
                <div class="col-sm-2 imgUp mb-3">
                    <div class="imagePreview"></div>
                    <label class="btn btn-primary w-100">Upload
                        <input type="file" name="images[]" class="uploadFile img" style="width: 0px; height: 0px; overflow: hidden;" accept="image/*">
                    </label>
                    <i class="fa fa-times del"></i>
                </div>
            `);
            
            $(this).closest('.row').find('.imgAdd').parent().before(newBox);
            totalImageCount++;
            updatePlusButton();
        }
    });

    // Remove image box
    $(document).on("click", ".del", function ()
    {
        const $parent = $(this).parent();
        const existingImage = $(this).data('existing-image');
        
        if (existingImage) {
            // For existing images, mark for deletion and hide the card
            $parent.hide();
            $parent.append(`<input type="hidden" name="deleted_images[]" value="${existingImage}">`);
        } else {
            // For new upload boxes, just remove them
            $parent.remove();
        }
        
        totalImageCount--;
        updatePlusButton();
    });

    // (Your image preview functionality remains the same)
    $(document).on("change", ".uploadFile", function ()
    {
        var uploadFile = $(this);
        var files = !!this.files ? this.files : [];
        if (!files.length || !window.FileReader) return;
        
        if (/^image/.test(files[0].type)) {
            var reader = new FileReader();
            reader.readAsDataURL(files[0]);
            reader.onloadend = function () {
                uploadFile.closest(".imgUp").find('.imagePreview').css("background-image", "url(" + this.result + ")");
            }
        }
    });
});

    /***************************************************PDF file handling*************************************************/

    // Handle PDF file selection display
    document.addEventListener('DOMContentLoaded', function() {
        const pdfInput = document.getElementById('pdfInput');
        const clearenceInput = document.getElementById('clearenceInput');
        
        if (pdfInput) {
            pdfInput.addEventListener('change', function(e) {
                if (this.files.length > 0) {
                    const fileName = this.files[0].name;
                    document.getElementById('pdfName').textContent = fileName;
                    document.getElementById('pdfFilename').style.display = 'block';
                } else {
                    document.getElementById('pdfFilename').style.display = 'none';
                }
            });
            
            // If there's already a file selected (from previous submission with errors)
            if (pdfInput.files.length > 0) {
                document.getElementById('pdfName').textContent = pdfInput.files[0].name;
                document.getElementById('pdfFilename').style.display = 'block';
            }
        }
        
        if (clearenceInput) {
            clearenceInput.addEventListener('change', function(e) {
                if (this.files.length > 0) {
                    const fileName = this.files[0].name;
                    document.getElementById('clearenceName').textContent = fileName;
                    document.getElementById('clearenceFilename').style.display = 'block';
                } else {
                    document.getElementById('clearenceFilename').style.display = 'none';
                }
            });
            
            // If there's already a file selected (from previous submission with errors)
            if (clearenceInput.files.length > 0) {
                document.getElementById('clearenceName').textContent = clearenceInput.files[0].name;
                document.getElementById('clearenceFilename').style.display = 'block';
            }
        }
    });
    </script>
</body>
</html>
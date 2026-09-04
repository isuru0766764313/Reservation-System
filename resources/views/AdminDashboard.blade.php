<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard - Hall Booking System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/index.global.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.8/index.global.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/dist/tippy.css" />
  <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/themes/light-border.css" />
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
      --info-light: #cffafe;
      --dark: #1e293b;
      --gray: #64748b;
      --gray-light: #f1f5f9;
      --white: #ffffff;
      --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
      --shadow: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -2px rgba(0,0,0,0.05);
      --shadow-lg: 0 10px 25px -3px rgba(0,0,0,0.08), 0 4px 6px -4px rgba(0,0,0,0.04);
      --radius: 12px;
      --radius-sm: 8px;
      --transition: all 0.2s ease;
    }

    * {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    body {
      background: #f8fafc;
      color: var(--dark);
    }

    /* ===== Top Navbar ===== */
    .navbar-dashboard {
      background: linear-gradient(135deg, #1e293b 0%, #334155 100%) !important;
      padding: 0.85rem 1.5rem !important;
      box-shadow: 0 2px 10px rgba(0,0,0,0.15);
    }
    .navbar-dashboard .navbar-brand {
      font-weight: 700;
      font-size: 1.1rem;
      letter-spacing: -0.3px;
    }
    .navbar-dashboard .nav-user {
      color: rgba(255,255,255,0.85);
      font-weight: 500;
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      gap: 8px;
      margin-right: 16px;
    }
    .navbar-dashboard .nav-user i {
      font-size: 1.2rem;
    }
    .btn-logout {
      background: rgba(255,255,255,0.1);
      border: 1px solid rgba(255,255,255,0.2);
      color: #fff !important;
      border-radius: var(--radius-sm);
      padding: 0.45rem 1rem;
      font-size: 0.85rem;
      font-weight: 500;
      transition: var(--transition);
    }
    .btn-logout:hover {
      background: rgba(255,255,255,0.2);
      border-color: rgba(255,255,255,0.3);
    }

    /* ===== Dashboard Section ===== */
    .dashboard-section {
      background: var(--white);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      padding: 1.75rem;
      margin-bottom: 1.75rem;
      border: 1px solid rgba(0,0,0,0.04);
      transition: var(--transition);
    }
    .dashboard-section:hover {
      box-shadow: var(--shadow-lg);
    }
    .dashboard-section .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.25rem;
      flex-wrap: wrap;
      gap: 0.75rem;
    }
    .dashboard-section .section-header h4 {
      font-weight: 700;
      font-size: 1.2rem;
      color: var(--dark);
      margin: 0;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .dashboard-section .section-header h4 i {
      color: var(--primary);
      background: var(--primary-light);
      width: 36px;
      height: 36px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1rem;
    }

    /* ===== Buttons ===== */
    .action-btn {
      min-width: 90px;
      margin: 2px;
      border-radius: var(--radius-sm) !important;
      font-weight: 500 !important;
      font-size: 0.82rem !important;
      padding: 0.45rem 0.85rem !important;
      transition: var(--transition) !important;
      border: none !important;
    }
    .action-btn i {
      margin-right: 6px;
    }
    .action-btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 10px rgba(0,0,0,0.12);
    }
    .packages-btn {
      background: #1b10e6ff !important;
      color: #fff !important;
    }
    .update-btn {
      background: #1b10e6ff !important;
      color: #fff !important;
    }
    .view-btn {
      background: #1b10e6ff !important;
      color: #fff !important;
    }
    .open-terms-btn {
      background: #1b10e6ff !important;
      color: #fff !important;
    }
    .btn-add-hall {
      background: #1b10e6ff !important;
      color: #fff !important;
      border-radius: var(--radius-sm) !important;
      padding: 0.55rem 1.25rem !important;
      font-weight: 600 !important;
      font-size: 0.9rem !important;
    }
    .btn-calendar {
      background: var(--primary) !important;
      color: #fff !important;
      border-radius: var(--radius-sm) !important;
      padding: 0.55rem 1.25rem !important;
      font-weight: 600 !important;
    }
    .btn-bank {
      background: var(--primary) !important;
      color: #fff !important;
      border-radius: var(--radius-sm) !important;
      padding: 0.5rem 1rem !important;
      font-weight: 500 !important;
    }

    /* ===== Table ===== */
    .table-custom {
      border-collapse: separate;
      border-spacing: 0 8px;
      margin-bottom: 0;
    }
    .table-custom thead th {
      background: #c5c3f7;
      color: var(--dark);
      border: none;
      padding: 0.85rem 1rem;
      font-weight: 600;
      font-size: 0.82rem;
      text-transform: uppercase;
      letter-spacing: 0.4px;
    }
    .table-custom thead th:first-child {
      border-radius: var(--radius-sm) 0 0 var(--radius-sm);
    }
    #packagesTable thead th {
      background: #c5c3f7;
      color: var(--dark);
    }
    .table-custom thead th:last-child {
      border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
    }
    .table-custom tbody tr {
      background: var(--white);
      transition: var(--transition);
      box-shadow: var(--shadow-sm);
      border-radius: var(--radius-sm);
    }
    .table-custom tbody tr:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow);
    }
    .table-custom tbody td {
      padding: 0.9rem 1rem;
      vertical-align: middle;
      border: none;
      font-size: 0.88rem;
    }
    .table-custom tbody td:first-child {
      border-radius: var(--radius-sm) 0 0 var(--radius-sm);
    }
    .table-custom tbody td:last-child {
      border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
    }
    .table-custom:not(.hall-table) th:nth-child(2),
    .table-custom:not(.hall-table) th:nth-child(3),
    .table-custom:not(.hall-table) td:nth-child(2),
    .table-custom:not(.hall-table) td:nth-child(3) {
      min-width: 150px;
      width: 15%;
    }

    /* ===== Status Badges ===== */
    .status-badge {
      padding: 0.4rem 0.9rem;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      white-space: nowrap;
    }
    .badge-custom {
      padding: 0.45rem 0.85rem;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
    }

    /* ===== Pagination ===== */
    .pagination-custom .page-link {
      border-radius: var(--radius-sm) !important;
      margin: 0 2px;
      color: var(--dark);
      font-weight: 500;
      border: 1px solid #e2e8f0;
      padding: 0.45rem 0.85rem;
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

    /* ===== Bank Card ===== */
    .bank-card {
      border: none;
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      overflow: hidden;
    }
    .bank-card .card-header {
      background: #c5c3f7;
      color: var(--dark);
      padding: 1rem 1.25rem;
      border: none;
    }
    .bank-card .card-header h5 {
      color: var(--dark);
      font-weight: 700;
      font-size: 1rem;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .bank-card .bank-stat {
      background: var(--white);
      border: 1px solid #e2e8f0;
      border-radius: var(--radius-sm);
      padding: 1rem 1.25rem;
      transition: var(--transition);
      height: 100%;
    }
    .bank-card .bank-stat:hover {
      border-color: var(--success);
      box-shadow: 0 2px 8px rgba(16,185,129,0.08);
    }
    .bank-card .bank-stat .stat-icon {
      width: 36px;
      height: 36px;
      border-radius: 10px;
      background: #eef2ff;
      color: #4f46e5;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.9rem;
    }
    .bank-card .bank-stat .stat-label {
      font-size: 0.78rem;
      color: var(--gray);
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.3px;
    }
    .bank-card .bank-stat .stat-value {
      font-size: 1.05rem;
      font-weight: 700;
      color: var(--dark);
    }

    /* ===== Mobile Bank List ===== */
    .bank-mobile-list .list-group-item {
      border: none;
      padding: 0.9rem 0;
      border-bottom: 1px solid #f1f5f9 !important;
    }
    .bank-mobile-list .list-group-item:last-child {
      border-bottom: none !important;
    }

    /* ===== Modals ===== */
    .modal-content {
      border: none;
      border-radius: var(--radius);
      box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }
    .modal-header {
      border-bottom: none;
      padding: 1.25rem 1.5rem;
    }
    .modal-header.bg-primary {
      background: linear-gradient(135deg, var(--primary), var(--primary-dark)) !important;
    }
    .modal-header .modal-title {
      font-weight: 700;
      font-size: 1.05rem;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .modal-body {
      padding: 1.5rem;
    }
    .modal-footer {
      border-top: 1px solid #e2e8f0;
      padding: 1rem 1.5rem;
    }

    /* ===== Buttons (match modal header gradient) ===== */
    .btn-primary {
      background: linear-gradient(135deg, var(--primary), var(--primary-dark)) !important;
      border: none !important;
    }
    .btn-primary:hover {
      background: linear-gradient(135deg, var(--primary-dark), var(--primary)) !important;
      color: #fff !important;
    }

    /* ===== Form Controls ===== */
    .form-control, .form-select {
      border-radius: var(--radius-sm);
      border: 1.5px solid #e2e8f0;
      padding: 0.55rem 0.85rem;
      font-size: 0.88rem;
      transition: var(--transition);
    }
    .form-control:focus, .form-select:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(79,70,229,0.1);
    }
    .form-label {
      font-weight: 600;
      font-size: 0.85rem;
      color: var(--dark);
      margin-bottom: 0.35rem;
    }

    /* ===== Info Alert ===== */
    .alert-custom {
      border-radius: var(--radius-sm);
      border: none;
      padding: 1rem 1.25rem;
      font-size: 0.85rem;
    }

    /* ===== Misc ===== */
    .section-divider {
      margin: 1rem -1.75rem;
      border: none;
      border-top: 1px solid #f1f5f9;
    }
    .fw-700 { font-weight: 700; }
    .text-muted-custom { color: var(--gray); }

    @media(max-width:768px) {
      .action-btn {
        width: 100%;
        margin-bottom: 5px;
      }
      .dashboard-section {
        padding: 1.25rem;
      }
      .navbar-dashboard .navbar-brand span {
        display: none;
      }
      .dashboard-section .section-header h4 {
        font-size: 1rem;
      }
    }
  </style>
</head>

<body>

  <nav class="navbar navbar-expand-lg navbar-dark navbar-dashboard shadow-sm">
    <div class="container-fluid">
      <a class="navbar-brand" href="#"><i class="fas fa-building me-2"></i>Hall Booking System Admin Panel</a>
      <div class="d-flex align-items-center">
        <span class="text-white nav-user"><i class="fas fa-user-circle"></i>
          {{ auth()->guard('admin')->user()->company_name }}</span>
        <form method="POST" action="{{ route('admin.logout.route') }}">
          @csrf
          <button type="submit" class="btn btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </form>
      </div>
    </div>
  </nav>

  <div class="container-fluid mt-4">
    <div class="dashboard-section">
      <div class="section-header">
        <h4 style="color: #1b10e6ff;"><i class="fas fa-building me-2" style="color: #1d12eeff;"></i>Hall Management</h4>
        <a href="{{ route('open.insert.hall.data.page') }}" class="btn btn-add-hall"><i
            class="fas fa-plus-circle me-2"></i>Add Hall</a>
      </div>
      <div class="table-responsive">
        <table class="table table-custom hall-table">
          <thead>
            <tr>
              <th>Hall</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($halls as $hall)
              <tr>
                <td>{{ $hall->name }}</td>
                <td>
                  @if ($hall->available)
                    <span class="status-badge bg-success"><i class="fas fa-check-circle me-2"></i>Available</span>
                  @else
                    <span class="status-badge bg-danger"><i class="fas fa-times-circle me-2"></i>Not available</span>
                  @endif
                </td>
                <td class="d-flex align-items-center flex-wrap gap-2">
                  <button class="btn btn-primary btn-sm action-btn packages-btn" data-hall-id="{{ $hall->id }}"
                    data-name="{{ $hall->name }}" data-fixed-facilities='@json($hall->fixedfacilities)'
                    data-unit-facilities='@json($hall->unitfacilities)' data-existing-packages='@json($hall->packages)'
                    data-bs-toggle="modal" data-bs-target="#AddPackagesModal">
                    <i class="fas fa-edit me-2"></i>
                    {{ $hall->packages->count() > 0 ? 'Edit Packages' : 'Packages' }}
                  </button>
                  <form action="{{ route('open.hall.update.page', $hall) }}" method="PUT">
                    <button type="submit" class="btn btn-primary btn-sm action-btn update-btn">Edit Property</button>
                  </form>
                  @if ($hall->available === true)
                      <form action="{{ route('deactivate.hall', $hall) }}" method="POST" style="display: inline;"
                        class="deactivate-form" data-hall-id="{{ $hall->id }}" data-check-ongoing="1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Make unavailable</button>
                      </form>
                    @else
                      <form action="{{ route('deactivate.hall', $hall) }}" method="POST" style="display: inline;"
                        class="deactivate-form" data-hall-id="{{ $hall->id }}" data-check-ongoing="0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Make available</button>
                      </form>
                    @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <!-- Reservation Requests Section -->
    <div class="dashboard-section">
      <div class="section-header">
        <h4 style="color: #1b10e6ff;"><i class="fas fa-history me-2" style="color: #1b10e6ff;"></i>Reservation Requests</h4>
        <div class="d-flex gap-2 flex-wrap">
          <button class="btn btn-calendar" data-bs-toggle="modal" data-bs-target="#calendarModal">
            <i class="fas fa-calendar-alt me-2"></i>Calendar View
          </button>
          <button class="btn btn-bank" data-bs-toggle="modal" data-bs-target="#bankinfo">
            <i class="fas fa-university me-2"></i>Bank Details
          </button>
        </div>
      </div>

      <!-- Bank Account Information Section -->
      <div class="bank-info-section mt-4 mb-4">
        <!-- Desktop/Tablet View - Grid Layout -->
        <div class="d-none d-md-block">
          <div class="bank-card card">
            <div class="card-header">
              <h5 class="mb-0"><i class="fas fa-university me-2"></i>Bank Account Information</h5>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <div class="col-md-4">
                  <div class="bank-stat">
                    <div class="d-flex align-items-center gap-3 mb-2">
                      <div class="stat-icon"><i class="fas fa-landmark"></i></div>
                      <span class="stat-label">Bank Name</span>
                    </div>
                    <p class="stat-value mb-0">{{ $admin->bank }}</p>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="bank-stat">
                    <div class="d-flex align-items-center gap-3 mb-2">
                      <div class="stat-icon"><i class="fas fa-user"></i></div>
                      <span class="stat-label">Account Name</span>
                    </div>
                    <p class="stat-value mb-0">{{ $admin->account_name }}</p>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="bank-stat">
                    <div class="d-flex align-items-center gap-3 mb-2">
                      <div class="stat-icon"><i class="fas fa-hashtag"></i></div>
                      <span class="stat-label">Account Number</span>
                    </div>
                    <p class="stat-value mb-0">{{ $admin->account_number }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Mobile View Only - Stacked Layout -->
        <div class="d-block d-md-none">
          <div class="bank-card card">
            <div class="card-header">
              <h5 class="mb-0"><i class="fas fa-university me-2"></i>Bank Account Details</h5>
            </div>
            <div class="card-body">
              <ul class="list-group list-group-flush bank-mobile-list">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <div><i class="fas fa-landmark text-success me-2"></i><strong>Bank Name:</strong></div>
                  <span>{{ $admin->bank }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <div><i class="fas fa-user text-success me-2"></i><strong>Account Name:</strong></div>
                  <span>{{ $admin->account_name }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <div><i class="fas fa-hashtag text-success me-2"></i><strong>Account Number:</strong></div>
                  <span>{{ $admin->account_number }}</span>
                </li>
              </ul>
              <div class="alert alert-info mt-3 mb-0 alert-custom">
                <i class="fas fa-info-circle me-2"></i>
                <small>Include reservation ID when making payments. Processing may take 24 hours.</small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="table-responsive">
        <br>
        <table class="table table-custom">
          <thead>
            <tr>
              <th>Reference Code</th>
              <th>Customer</th>
              <th>Property</th>
              <th>Date</th>
              <th>Period</th>
              <th>Status</th>
              <th>Reservation Details</th>
              <th>Payment Details</th>
            </tr>
          </thead>
          <tbody>
            @foreach($reservations as $reservation)
              @php
  // Calculate total amount paid for this reservation
  $totalPaid = $reservation->payments->where('status', 2)->sum('amount');
  $preliminaryPayment = $reservation->advanceAmount;
  $remainingAmount = max(0, (($reservation->charge - ($reservation->discount_custom ?? 0)) + $reservation->deposit) - $totalPaid);
              @endphp
              <tr>
                <td class="align-middle">{{ $reservation->ref_code ?? $loop->iteration }}</td>
                <td class="align-middle">{{ $reservation->customer->first_name }} {{ $reservation->customer->last_name }}
                </td>
                <td class="align-middle">{{ $reservation->hall->name }}</td>
                <td class="align-middle">{{ $reservation->reservation_date }}</td>
                <td class="align-middle">{{ date('h.i A', strtotime($reservation->start_time)) }} To {{ date('h.i A', strtotime($reservation->end_time)) }}</td>
                <td class="align-middle">
                                    @php $badge = \App\Http\Controllers\ReservationController::getCustomerStatusBadge($reservation); @endphp
                                    <span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                                </td>
                <td class="align-middle">
                  <button class="btn btn-success btn-sm action-btn view-btn" data-bs-toggle="modal"
                    data-bs-target="#reservationModal-{{ $reservation->id }}"><i class="fas fa-eye me-2"></i>View
                  </button>
                </td>

                <td class="align-middle">
                  <button class="btn btn-success btn-sm action-btn view-btn" data-bs-toggle="modal"
                    data-bs-target="#slipModal-{{ $reservation->id }}" @if(!$reservation->accepted) disabled @endif>
                    <i class="fas fa-eye me-2"></i>See Payment Summary
                  </button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <!-- Add pagination links -->
      <div class="mt-4 d-flex justify-content-center">{{ $reservations->links('pagination::bootstrap-4') }}</div>
    </div>

  </div>
  <!-- ends "div class="container-fluid mt-4"-->

  <!-- Packages Modal - Dynamic Version -->
	  <div class="modal fade" id="AddPackagesModal" data-bs-backdrop="static" data-bs-keyboard="false">
		    <div class="modal-dialog modal-fullscreen">
	      <div class="modal-content h-100 d-flex flex-column">
	        <div class="modal-header bg-primary text-white">
	          <h5 class="modal-title" id="modalTitle"></h5>
	          <button class="btn-close" data-bs-dismiss="modal"></button>
	        </div>
	        <form method="POST" action="" id="packagesForm" class="d-flex flex-column flex-grow-1 overflow-hidden">
	          @csrf
	          <input type="hidden" name="hall_id" id="modalHallId" value="">

	          <div class="modal-body flex-grow-1 overflow-auto">
            <div class="form-section">
              <h4 class="mb-4"><i class="fas fa-package me-2"></i>Packages</h4>

              <!-- Display available facilities for this hall -->
              <div class="mb-4">
                <h6>Available Facilities for <span id="modalFacilitiesTitle"></span>:</h6>
                <div id="modalFacilitiesContainer">
                  <p class="text-muted">Select a hall to see available facilities.</p>
                </div>
              </div>

              <div class="table-responsive">
                <table class="table table-hover" id="packagesTable">
                  <thead>
                    <tr>
                      <th>Package Name</th>
                      <th>Package Price (Rs.)</th>
                      <th>Discount (Rs.)</th>
                      <th>Description</th>
                      <th>Allocated Hours</th>
                      <th>Pre/Post free hours</th>
                      <th>Fixed Price Facilities</th>
                      <th>Unit Price Facilities</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- Package rows will be populated by JavaScript -->
                  </tbody>
                </table>
              </div>
              <button type="button" class="btn btn-primary btn-sm" id="addPackageBtn">
                <i class="fas fa-plus-circle me-2"></i>Add Another Package
              </button>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary" id="submitBtn">Save Packages</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!--Bank detail model-->

  <!-- Ongoing reservations warning modal -->
  <div class="modal fade" id="ongoingReservationsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Cannot Make Hall Unavailable</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="text-center mb-3">
            <i class="fas fa-calendar-times" style="font-size: 3rem; color: #dc3545;"></i>
          </div>
          <p class="mb-1">This hall has <strong id="ongoingReservationsCount">0</strong> ongoing reservation(s) that have not passed yet.</p>
          <p class="text-muted">A hall with ongoing reservations cannot be made unavailable. Please wait until all reservation dates have passed.</p>
          <hr>
          <ul id="ongoingReservationsList" class="mb-0" style="max-height: 250px; overflow-y: auto;"></ul>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="bankinfo" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Add or Update Bank Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form action="{{ route('admin.updateBank') }}" method="POST">
            @csrf
            @method('POST')
            <div class="row mb-4">
              <div class="col-12 col-md-6">
                <h6 class="mb-3"><i class="fas fa-user me-2"></i>Bank Details</h6>
                <div class="mb-3">
                  <label for="bank" class="form-label">Bank Name</label>
                  <input type="text" class="form-control" id="bank" name="bank" value="{{ $admin->bank }}">
                </div>
                <div class="mb-3">
                  <label for="account_name" class="form-label">Account Name</label>
                  <input type="text" class="form-control" id="account_name" name="account_name"
                    value="{{ $admin->account_name }}">
                </div>
                <div class="mb-3">
                  <label for="account_number" class="form-label">Account Number</label>
                  <input type="text" class="form-control" id="account_number" name="account_number"
                    value="{{ $admin->account_number }}">
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">Save Bank Details</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Reservation request Detail Modal -->
  @foreach($reservations as $reservation)
    @php
  // Calculate total amount paid for this reservation
  $totalPaid = $reservation->payments->where('status', 2)->sum('amount');
  $preliminaryPayment = $reservation->advanceAmount;
  $remainingAmount = max(0, (($reservation->charge - ($reservation->discount_custom ?? 0)) + $reservation->deposit) - $totalPaid);
    @endphp
    <div class="modal fade" id="reservationModal-{{ $reservation->id }}" tabindex="-1">
      <div class="modal-dialog modal-lg" style="max-width: 1100px;">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title">Reservation Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="row mb-4">
              <div class="col-md-6">
                <h6><i class="fas fa-user me-2"></i>Customer Details</h6>
                <dl class="row">
                  <dt class="col-sm-4">Name:</dt>
                  <dd class="col-sm-8">{{ $reservation->customer_name }}</dd>

                  <dt class="col-sm-4">Email:</dt>
                  <dd class="col-sm-8">{{ $reservation->customer_email }}</dd>

                  <dt class="col-sm-4">Phone:</dt>
                  <dd class="col-sm-8">{{ $reservation->customer_tel }}</dd>
                  <dt class="col-sm-4">Advance payment due date:</dt>
                  <dd class="col-sm-8">
                      <input type="date" class="form-control form-control-sm" id="advancePaymentDate-{{ $reservation->id }}" name="advancePaymentDate" min="{{ now()->format('Y-m-d') }}" max="{{ $reservation->created_at->copy()->addDays(7)->format('Y-m-d') }}" value="{{ $reservation->advancePaymentDate ?? $reservation->created_at->copy()->addDays(7)->format('Y-m-d') }}">
                  </dd>
                  <dt class="col-sm-4">Cancell due date:</dt>
                  <dd class="col-sm-8">
                      <input type="date" class="form-control form-control-sm" id="cancellationExpiryDate-{{ $reservation->id }}" name="cancellationExpiryDate" min="{{ now()->format('Y-m-d') }}" max="{{ \Carbon\Carbon::parse($reservation->reservation_date)->subDays(7)->format('Y-m-d') }}" value="{{ $reservation->cancellationExpiryDate ?? \Carbon\Carbon::parse($reservation->reservation_date)->subDays(7)->format('Y-m-d') }}">
                  </dd>
                  <dt class="col-sm-4">Re-schedule due date:</dt>
                  <dd class="col-sm-8">
                      <input type="date" class="form-control form-control-sm" id="rescheduledExpiryDate-{{ $reservation->id }}" name="rescheduledExpiryDate" min="{{ now()->format('Y-m-d') }}" max="{{ \Carbon\Carbon::parse($reservation->reservation_date)->subDays(7)->format('Y-m-d') }}" value="{{ $reservation->rescheduledExpiryDate ?? \Carbon\Carbon::parse($reservation->reservation_date)->subDays(7)->format('Y-m-d') }}">
                  </dd>
                </dl>
              </div>
              <div class="col-md-6">
                <!--<h6><i class="fas fa-building me-2"></i>Hall Details</h6>-->
                <dl class="row">
                  <dt class="col-sm-4">Hall Name:</dt>
                  <dd class="col-sm-8">{{ $reservation->hall_name }}</dd>

                  <dt class="col-sm-4">Reservation Type:</dt>
                  <dd class="col-sm-8">{{ ucfirst($reservation->reservation_type) }}</dd>
                    @if($reservation->reservation_type === 'package' && $reservation->package)
                      <dt class="col-sm-4">Package:</dt>
                      <dd class="col-sm-8">{{ $reservation->package->name }}</dd>
                    @endif 

                  <dt class="col-sm-4">Reservation Date:</dt>
                  <dd class="col-sm-8">{{ date('M d, Y', strtotime($reservation->reservation_date)) }}</dd>

                  <dt class="col-sm-4">Reservation period:</dt>
                  <dd class="col-sm-8">
                    {{ date('h:i A', strtotime($reservation->start_time)) }} -
                    {{ date('h:i A', strtotime($reservation->end_time)) }}
                  </dd>
                  @if($reservation->reservation_type === 'package')
                  <dt class="col-sm-4">Pre-arrange hours:</dt>
                  <dd class="col-sm-8">{{ $reservation->pre_arrange_time }} hours</dd>
                  <dt class="col-sm-4">Post-arrange hours:</dt>
                  <dd class="col-sm-8">{{ $reservation->post_arrange_time }} hours</dd>
                  @endif
                  <dt class="col-sm-4">Charge:</dt>
                  <dd class="col-sm-8 fw-bold">Rs. {{ number_format($reservation->charge, 2) }}</dd>
                  @if($reservation->customer && $reservation->customer->type !== 'private')
                  <dt class="col-sm-4">Discount (%):</dt>
                  <dd class="col-sm-8">
                      <div class="input-group input-group-sm">
                          <input type="number" class="form-control form-control-sm" step="0.1" min="0" max="100"
                              id="discount-custom-{{ $reservation->id }}" placeholder="0"
                              data-charge="{{ $reservation->charge }}"
                              value="{{ $reservation->discount_custom ? round(($reservation->discount_custom / $reservation->charge) * 100, 2) : '' }}">
                          <span class="input-group-text">%</span>
                      </div>
                      <small class="text-muted" id="discount-amount-{{ $reservation->id }}">
                          @if($reservation->discount_custom)
                              = Rs. {{ number_format($reservation->discount_custom, 2) }}
                          @endif
                      </small>
                  </dd>
                  <dt class="col-sm-4">Final Charge:</dt>
                  <dd class="col-sm-8 fw-bold text-success" id="final-charge-{{ $reservation->id }}">
                      Rs. {{ number_format($reservation->charge - ($reservation->discount_custom ?? 0), 2) }}
                  </dd>
                  @endif
                  <dt class="col-sm-4">Advance Payment:</dt>
                  <dd class="col-sm-8">
                      <input type="number" class="form-control form-control-sm" step="0.01" min="0" max="{{ $reservation->advanceAmount }}" id="advanceAmount-{{ $reservation->id }}" placeholder="0.00" value="{{ $reservation->advanceAmount ?? 0 }}" data-cancellation-fee="{{ $reservation->hall->cancellation_fee }}">
                  </dd>
                  <dt class="col-sm-4">Refundable Deposit:</dt>
                  <dd class="col-sm-8 fw-bold">Rs. {{ number_format($reservation->deposit, 2) }}</dd>
                </dl>
                <button class="btn btn-primary open-terms-btn mt-2"
                  data-pdf="{{ asset('storage/' . $reservation->clearence_form) }}">
                  <i class="fas fa-file-contract me-2"></i> View Application form
                </button>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <div class="me-auto">
              <form method="POST" action="{{route('admin.reservations.accept', $reservation->id)}}" onsubmit="updateDiscountCustom('{{ $reservation->id }}')">
                @csrf @method('PATCH')
                <input type="hidden" name="discount_custom" id="discount-custom-hidden-{{ $reservation->id }}" value="{{ $reservation->discount_custom ?? 0 }}">
                <input type="hidden" name="advanceAmount" id="advanceAmount-hidden-{{ $reservation->id }}" value="{{ $reservation->advanceAmount ?? 0 }}">
                <input type="hidden" name="advancePaymentDate" id="advancePaymentDate-hidden-{{ $reservation->id }}" value="{{ $reservation->advancePaymentDate ?? '' }}">
                <input type="hidden" name="cancellationExpiryDate" id="cancellationExpiryDate-hidden-{{ $reservation->id }}" value="{{ $reservation->cancellationExpiryDate ?? '' }}">
                <input type="hidden" name="rescheduledExpiryDate" id="rescheduledExpiryDate-hidden-{{ $reservation->id }}" value="{{ $reservation->rescheduledExpiryDate ?? '' }}">
                <button type="button" class="btn btn-success" onclick="acceptReservation(this, '{{ $reservation->id }}')" @if($reservation->accepted !== null) disabled @endif>
                  <i class="fas fa-check me-2"></i>{{ $reservation->accepted !== null && $reservation->accepted ? 'Already Accepted' : 'Accept' }}</button>
              </form>
            </div>
            <div>
              <form method="POST" action="{{route('admin.reservations.reject', $reservation->id)}}">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-danger" @if($reservation->accepted !== null) disabled @endif>
                  <i class="fas fa-times me-2"></i>{{ $reservation->accepted !== null && !$reservation->accepted ? 'Already rejected' : 'Reject' }}
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  @endforeach

  <!-- Reservation Slip Detail Modal -->
  @foreach($reservations as $reservation)
                <div class="modal fade" id="slipModal-{{ $reservation->id }}" tabindex="-1">
          <div class="modal-dialog modal-xl" style="max-width: 1250px;">
                    <div class="modal-content">
                      <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                          <i class="fas fa-file-invoice me-2"></i>
                          <- Payment Details -> Reservation Ref Code :  {{ $reservation->ref_code }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <!-- Reservation Details -->
                        <div class="mb-4">
                          <div class="row">
                            <div class="col-md-6">
                              <h6 class="border-bottom pb-2" style="color: #291fecff;">
                                <i class="fas fa-user me-2" style="color: #241ae2ff;"></i>Customer Details
                              </h6>
                              <dl class="row">
                                <dt class="col-sm-4">Name:</dt>
                                <dd class="col-sm-8">{{ $reservation->customer_name }}</dd>

                                <dt class="col-sm-4">Email:</dt>
                                <dd class="col-sm-8">{{ $reservation->customer_email }}</dd>

                                <dt class="col-sm-4">Phone:</dt>
                                <dd class="col-sm-8">{{ $reservation->customer_tel }}</dd>
                              </dl>
                            </div>

                            <div class="col-md-6">
                              <h6 class="border-bottom pb-2" style="color: #160ce4ff;">
                                <i class="fas fa-building me-2" style="color: #1b10e6ff;"></i>Reservation Details
                              </h6>
                              <dl class="row">
                                <dt class="col-sm-4">Hall Name:</dt>
                                <dd class="col-sm-8">{{ $reservation->hall_name }}</dd>

                                <dt class="col-sm-4">Reservation Type:</dt>
                                <dd class="col-sm-8">{{ ucfirst($reservation->reservation_type) }}</dd>
                                @if($reservation->reservation_type === 'package' && $reservation->package)
                                  <dt class="col-sm-4">Package:</dt>
                                  <dd class="col-sm-8">{{ $reservation->package->name }}</dd>
                                @endif                      

                                <dt class="col-sm-4">Reservation Date:</dt>
                                <dd class="col-sm-8">{{ date('M d, Y', strtotime($reservation->reservation_date)) }}</dd>

                                <dt class="col-sm-4">Reservation period:</dt>
                                <dd class="col-sm-8">
                                  {{ date('h:i A', strtotime($reservation->start_time)) }} -
                                  {{ date('h:i A', strtotime($reservation->end_time)) }}
                                </dd>
                                @if($reservation->reservation_type === 'package')
                                <dt class="col-sm-4">Pre-arrange hours:</dt>
                                <dd class="col-sm-8">{{ $reservation->pre_arrange_time }} hours</dd>
                                <dt class="col-sm-4">Post-arrange hours:</dt>
                                <dd class="col-sm-8">{{ $reservation->post_arrange_time }} hours</dd>
                                @endif

                                <dt class="col-sm-4">Charge:</dt>
                                <dd class="col-sm-8 fw-bold">Rs. {{ number_format($reservation->charge, 2) }}</dd>

                                @php
    $totalPaidSlip = $reservation->payments->where('status', 2)->sum('amount');
    if ((int) $reservation->status === 5) {
      $approvedExceptCancellation = $reservation->payments->where('status', 2)->where('payment_alias', '!=', 'Cancellation')->sum('amount');
      $totalPaidSlip = max(0, $approvedExceptCancellation - ($reservation->hall->cancellation_fee ?? 0));
    }
    $advancePaidStatus = $reservation->advancePaid ? 'Yes' : 'No';
    $remainingSlip = max(0, (($reservation->charge - $reservation->discount_custom) + $reservation->deposit) - $totalPaidSlip);
                                @endphp

                                <dt class="col-sm-4">Discount:</dt>
                                <dd class="col-sm-8 fw-bold">
                                  @if($reservation->discount_custom)
                                    Rs. {{ number_format($reservation->discount_custom, 2) }}
                                  @else
                                    <span class="text-muted">None</span>
                                  @endif
                                </dd>

                                <dt class="col-sm-4">Final Charge:</dt>
                                <dd class="col-sm-8 fw-bold text-success" id="final-charge-{{ $reservation->id }}">
                                  Rs. {{ number_format($reservation->charge - ($reservation->discount_custom ?? 0), 2) }}
                                </dd>

                                <dt class="col-sm-4">Advance Payment:</dt>
                                <dd class="col-sm-8 fw-bold">Rs. {{ number_format($reservation->advanceAmount, 2) }}</dd>

                                <dt class="col-sm-4">Refundable Deposit:</dt>
                                <dd class="col-sm-8 fw-bold">Rs. {{ number_format($reservation->deposit, 2) }}</dd>

                                <dt class="col-sm-4">Total Paid:</dt>
                                <dd class="col-sm-8 fw-bold text-success">Rs. {{ number_format($totalPaidSlip, 2) }}</dd>

                                @if(!in_array($reservation->status, [5, 6, 7]))
                                <dt class="col-sm-4">Remaining to be paid:</dt>
                                <dd class="col-sm-8 fw-bold text-danger">Rs. {{ number_format($remainingSlip, 2) }}</dd>
                                @endif
                              </dl>
                            </div>
                          </div>
                        </div>

                        <!-- Payment Slip Display - Show all payment slips from payments table -->
                        <div class="mb-4">
                          <h6 class="border-bottom pb-2" >Payment Slips</h6>
                          <div class="text-center bg-light p-3 rounded">
                            @php $paymentCount = $reservation->payments->where('payment_alias', '!=', 'Cancellation')->count(); @endphp
                            @if($paymentCount > 0)
                              @foreach($reservation->payments->where('payment_alias', '!=', 'Cancellation') as $index => $payment)
                                <div class="mb-3">
                                  <h6 class="text-muted">Payment number : {{ $index + 1 }} 
                                    @if($payment->payment_alias && $payment->payment_alias != 'Preliminary')
                                      <span class="badge bg-info ms-2">Advance Payment</span>
                                    @elseif($payment->payment_alias && $payment->payment_alias != 'Remainings')
                                      <span class="badge bg-info ms-2">Balance Payment</span>
                                    @endif
                                    <span class="badge bg-secondary ms-1">Rs. {{ number_format($payment->amount, 2) }}</span>
                                  </h6>
                                  @if(\Illuminate\Support\Str::endsWith($payment->receipt_path, '.pdf'))
                                    <iframe src="{{ asset('storage/' . $payment->receipt_path) }}" width="100%" height="400px"
                                      class="border">
                                    </iframe>
                                  @else
                                    <img src="{{ asset('storage/' . $payment->receipt_path) }}" alt="Payment Slip #{{ $index + 1 }}" class="img-fluid"
                                      style="max-height: 400px">
                                  @endif
                                  <div class="mt-3 d-flex justify-content-center align-items-center gap-3" id="slipActions-{{ $payment->id }}">
                                    @if($payment->status == 1)
                                      <form action="{{ route('admin.payment.accept', $payment) }}" method="POST" class="d-inline slip-action-form">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success" title="Accept payment">
                                          <i class="fas fa-check me-2"></i> Accept
                                        </button>
                                      </form>
                                      <!--<form action="{{ route('admin.payment.reject', $payment) }}" method="POST" class="d-inline slip-action-form">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-danger" title="Reject payment">
                                          <i class="fas fa-times me-2"></i> Reject
                                        </button>
                                      </form>-->
                                    @else
                                      @if($payment->status == 2)
                                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Accepted</span>
                                      @elseif($payment->status == 3)
                                        <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Rejected</span>
                                      @endif
                                    @endif
                                  </div>
                                </div>
                                @if(!$loop->last)<hr>@endif
                              @endforeach
                            @else
                              <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                No payment is done yet for this reservation. May be it is pending or rejected.
                              </div>
                            @endif
                          </div>
                        </div>
                      </div>

                      <div class="modal-footer d-flex justify-content-between">
                        <div class="d-flex gap-2">
                          @php
    $hasCancellationPending = $reservation->payments->where('payment_alias', 'Cancellation')->where('status', 1)->count() > 0;
                          @endphp

                          {{-- Accept Cancellation Payment Button --}}
                          @if($hasCancellationPending)
                          <form action="{{ route('admin.slip.accept', $reservation) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-warning">
                              <i class="fas fa-check-circle me-2"></i>Accept Cancellation Payment
                            </button>
                          </form>
                          @endif

                          {{-- Reject Reservation Button --}}
                          @if((int) $reservation->status !== 6)
                          <form action="{{ route('admin.reservation.reject', $reservation) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-danger">
                              <i class="fas fa-times-circle me-2"></i>Reject Reservation
                            </button>
                          </form>
                          @endif
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
  @endforeach

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBTzYyCvcUNmjoNZSMIuA16xV6_uUFkK2k&libraries=places"></script>
  <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/index.global.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.8/index.global.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@6.1.8/index.global.min.js"></script>
  <script src="https://unpkg.com/@popperjs/core@2"></script>
  <script src="https://unpkg.com/tippy.js@6"></script>

  <script>

    document.addEventListener('DOMContentLoaded', function () {
      /*******************************************************************************Add Packages- inside the DOMContentLoaded******************************************************************************/
      /* Packages Modal Event Listener*/
      const packagesModal = document.getElementById('AddPackagesModal');
      if (packagesModal) {
        packagesModal.addEventListener('show.bs.modal', function (event) {
          const button = event.relatedTarget;
          // Get data from button
          const hallId = button.getAttribute('data-hall-id');
          const hallName = button.getAttribute('data-name');
          const facilitiesData = button.getAttribute('data-fixed-facilities');
          const unitFacilitiesData = button.getAttribute('data-unit-facilities');
          const packagesData = button.getAttribute('data-existing-packages');
          console.log('Modal opened for hall:', hallId, hallName);
          // Update facilities title
          document.getElementById('modalFacilitiesTitle').textContent = hallName;
          document.getElementById('modalHallId').value = hallId;
          // Parse data
          currentHallFacilities = facilitiesData ? JSON.parse(facilitiesData) : [];
          currentHallUnitFacilities = unitFacilitiesData ? JSON.parse(unitFacilitiesData) : [];
          const existingPackagesData = packagesData ? JSON.parse(packagesData) : [];

          displayModalFacilities(currentHallFacilities, currentHallUnitFacilities);
          resetPackageTable(hallId, currentHallFacilities, currentHallUnitFacilities, existingPackagesData, hallName);
        });
      }
      // Add Package Button Event Listener
      document.getElementById('addPackageBtn').addEventListener('click', function () { addPackageRowDynamic(); });
      // Remove Package Row Event Listener (event delegation)
      document.addEventListener('click', function (e) {
        if (e.target.closest('.remove-package-row')) {
          const row = e.target.closest('.package-row');
          const tbody = document.querySelector('#packagesTable tbody');
          if (tbody && tbody.rows.length > 1) {
            row.remove();
          } else {
            alert('At least one package is required');
          }
        }
      });

      /***************************************************Make unavailable - ongoing reservations check**************************************************/
      const ongoingReservationsModal = document.getElementById('ongoingReservationsModal');
      const ongoingModalInstance = new bootstrap.Modal(ongoingReservationsModal);

      document.querySelectorAll('.deactivate-form').forEach(function (form) {
        if (form.dataset.checkOngoing !== '1') return;
        form.addEventListener('submit', function (e) {
          e.preventDefault();
          const hallId = form.dataset.hallId;
          fetch(`/admin/halls/${hallId}/ongoing-reservations`)
            .then(response => response.json())
            .then(data => {
              if (data.ongoing) {
                const listEl = document.getElementById('ongoingReservationsList');
                listEl.innerHTML = '';
                data.reservations.forEach(function (res) {
                  const li = document.createElement('li');
                  li.textContent = `${res.customer_name} - ${res.reservation_date} (${res.start_time} - ${res.end_time})`;
                  listEl.appendChild(li);
                });
                document.getElementById('ongoingReservationsCount').textContent = data.count;
                ongoingModalInstance.show();
              } else {
                form.submit();
              }
            })
            .catch(() => form.submit());
        });
      });

      // Live update discount amount display as percentage changes
      document.addEventListener('input', function (e) {
        if (e.target.id && e.target.id.startsWith('discount-custom-')) {
          const reservationId = e.target.id.replace('discount-custom-', '');
          const charge = parseFloat(e.target.getAttribute('data-charge')) || 0;
          const percentage = parseFloat(e.target.value) || 0;
          const amount = (charge * percentage / 100).toFixed(2);
          const displayEl = document.getElementById('discount-amount-' + reservationId);
          if (displayEl) {
            displayEl.textContent = percentage > 0 ? '= Rs. ' + parseFloat(amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '';
          }
          const finalChargeEl = document.getElementById('final-charge-' + reservationId);
          if (finalChargeEl) {
            const finalCharge = charge - parseFloat(amount);
            finalChargeEl.textContent = 'Rs. ' + finalCharge.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
          }
        }
      });

      // Open Clearance Form in New Tab
      document.addEventListener('click', function (e) {
        if (e.target.closest('.open-terms-btn')) {
          const pdfPath = e.target.closest('.open-terms-btn').getAttribute('data-pdf');
          if (pdfPath) {
            window.open(pdfPath, '_blank');
          }
        }
      });

    });

    /*******************************************************************************Add Packages - outside DOMContentLoaded******************************************************************************/

    /*PACKAGES MODAL - GLOBAL VARIABLES*/
    let currentHallFacilities = [];
    let currentHallUnitFacilities = [];
    let currentHallId = '';
    let nextRowId = 0; // Use unique ID for each row instead of count

    /* PACKAGES MODAL - GLOBAL FUNCTIONS*/

    // Function to display facilities in modal
    function displayModalFacilities(facilities, unitFacilities) {
      const container = document.getElementById('modalFacilitiesContainer');
      if (!container) return;

      container.innerHTML = '';

      let html = '';

      // Fixed price facilities
      if (facilities && facilities.length > 0) {
        html += '<div class="mb-3"><h6 class="text-primary">Fixed Price Facilities:</h6><div class="row">';
        facilities.forEach(facility => {
          html += `
                  <div class="col-md-4 mb-2">
                      <div class="card">
                          <div class="card-body py-2">
                              <h6 class="card-title mb-1">${facility.name}</h6>
                              <p class="card-text text-success mb-0">Rs. ${parseFloat(facility.charge).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</p>
                          </div>
                      </div>
                  </div>
              `;
        });
        html += '</div></div>';
      }

      // Unit price facilities
      if (unitFacilities && unitFacilities.length > 0) {
        html += '<div class="mb-3"><h6 class="text-info">Unit Price Facilities:</h6><div class="row">';
        unitFacilities.forEach(facility => {
          html += `
                  <div class="col-md-4 mb-2">
                      <div class="card">
                          <div class="card-body py-2">
                              <h6 class="card-title mb-1">${facility.name}</h6>
                              <p class="card-text text-info mb-0">Rs. ${parseFloat(facility.charge).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})} per unit</p>
                          </div>
                      </div>
                  </div>
              `;
        });
        html += '</div></div>';
      }

      if (!html) {
        html = '<p class="text-muted">No facilities available for this hall.</p>';
      }

      container.innerHTML = html;
    }

    // Function to generate fixed facilities checkboxes
    function generateFacilitiesCheckboxes(facilities, rowId, selectedFacilities = []) {
      if (!facilities || facilities.length === 0) {
        return '<span class="text-muted small">No fixed facilities available</span>';
      }

      let checkboxes = '';
      facilities.forEach(facility => {
        const isChecked = selectedFacilities.map(String).includes(String(facility.id));
        checkboxes += `
              <div class="form-check form-check-sm">
                  <input class="form-check-input" type="checkbox" 
                         value="${facility.id}" 
                         id="facility-${facility.id}-${rowId}"
                         name="packages[${rowId}][fixed_price_facilities][]"
                         ${isChecked ? 'checked' : ''}>
                  <label class="form-check-label small" for="facility-${facility.id}-${rowId}">
                      ${facility.name}
                  </label>
              </div>
          `;
      });
      return checkboxes;
    }

    // Function to generate unit facilities checkboxes
    function generateUnitFacilitiesCheckboxes(unitFacilities, rowId, selectedFacilities = []) {
      if (!unitFacilities || unitFacilities.length === 0) {
        return '<span class="text-muted small">No unit facilities available</span>';
      }

      let checkboxes = '';
      unitFacilities.forEach(facility => {
        const isChecked = selectedFacilities.map(String).includes(String(facility.id));
        checkboxes += `
              <div class="form-check form-check-sm">
                  <input class="form-check-input" type="checkbox" 
                         value="${facility.id}" 
                         id="unit-facility-${facility.id}-${rowId}"
                         name="packages[${rowId}][unit_price_facilities][]"
                         ${isChecked ? 'checked' : ''}>
                  <label class="form-check-label small" for="unit-facility-${facility.id}-${rowId}">
                      ${facility.name}
                  </label>
              </div>
          `;
      });
      return checkboxes;
    }

    // Function to add package row dynamically with unique row ID
    function addPackageRowDynamic(packageData = null, existingRowId = null) {
      const tbody = document.querySelector('#packagesTable tbody');
      if (!tbody) return;

      const rowId = existingRowId !== null ? existingRowId : nextRowId++;

      // Get selected facilities for this package
      const fixedFacilities = packageData && packageData.fixed_price_facilities ?
        (Array.isArray(packageData.fixed_price_facilities) ?
          packageData.fixed_price_facilities :
          JSON.parse(packageData.fixed_price_facilities || '[]')) :
        [];

      const unitFacilities = packageData && packageData.unit_price_facilities ?
        (Array.isArray(packageData.unit_price_facilities) ?
          packageData.unit_price_facilities :
          JSON.parse(packageData.unit_price_facilities || '[]')) :
        [];

      const newRow = document.createElement('tr');
      newRow.className = 'package-row';
      newRow.dataset.rowId = rowId;

      newRow.innerHTML = `
          ${packageData && packageData.id ? `<input type="hidden" name="packages[${rowId}][id]" value="${packageData.id}">` : ''}
          <td>
              <input type="text" class="form-control" placeholder="e.g., Premium" 
                     name="packages[${rowId}][name]" 
                     value="${packageData ? (packageData.name || '') : ''}" required>
          </td>
          <td>
              <input type="number" class="form-control" placeholder="0.00" step="0.01" 
                     name="packages[${rowId}][price]" 
                     value="${packageData ? (packageData.price || '') : ''}">
          </td>
          <td>
              <input type="number" class="form-control" placeholder="0.00" step="0.01"
                     name="packages[${rowId}][discount]" 
                     value="${packageData ? (packageData.discount || '') : ''}">
          </td>
          <td>
              <input type="text" class="form-control" placeholder="Package description" 
                     name="packages[${rowId}][description]" 
                     value="${packageData ? (packageData.description || '') : ''}" required>
          </td>
          <td>
              <input type="number" class="form-control" placeholder="e.g., 4" step="1" 
                     name="packages[${rowId}][duration]" 
                     value="${packageData ? (packageData.duration || '') : ''}" required>
          </td>
          <td>
              <input type="number" class="form-control" placeholder="e.g., 4" step="1" 
                     name="packages[${rowId}][maximum_hours]" 
                     value="${packageData ? (Math.round(packageData.maximum_hours) || '') : ''}" required>
          </td>
          <td>
              <div class="facilities-checkboxes" style="max-height: 150px; overflow-y: auto;">
                  ${generateFacilitiesCheckboxes(currentHallFacilities, rowId, fixedFacilities)}
              </div>
          </td>
          <td>
              <div class="facilities-checkboxes" style="max-height: 150px; overflow-y: auto;">
                  ${generateUnitFacilitiesCheckboxes(currentHallUnitFacilities, rowId, unitFacilities)}
              </div>
          </td>
          <td>
              <button type="button" class="btn btn-sm btn-danger remove-package-row" data-row-id="${rowId}">
                  <i class="fas fa-trash"></i>
              </button>
          </td>
      `;

      tbody.appendChild(newRow);
    }

    // Function to reset package table
    function resetPackageTable(hallId, facilities, unitFacilities, packages, hallName) {
      const tbody = document.querySelector('#packagesTable tbody');
      if (!tbody) return;

      tbody.innerHTML = '';
      nextRowId = 0;
      currentHallFacilities = facilities || [];
      currentHallUnitFacilities = unitFacilities || [];
      currentHallId = hallId;

      const existingPackages = packages || [];

      // Update UI based on whether we're editing or creating
      const modalTitle = document.getElementById('modalTitle');
      const submitBtn = document.getElementById('submitBtn');
      const form = document.getElementById('packagesForm');

      if (existingPackages.length > 0) {
        // Edit mode - populate existing packages
        modalTitle.innerHTML = `<i class="fas fa-edit me-2"></i>Edit Packages for ${hallName}`;
        submitBtn.textContent = 'Update Packages';
        form.action = `/admin/packages/update/${hallId}`;

        // Ensure method is PUT for update
        let methodInput = form.querySelector('input[name="_method"]');
        if (!methodInput) {
          methodInput = document.createElement('input');
          methodInput.type = 'hidden';
          methodInput.name = '_method';
          form.appendChild(methodInput);
        }
        methodInput.value = 'PUT';

        existingPackages.forEach((pkg, index) => {
          addPackageRowDynamic(pkg, index);
          nextRowId = Math.max(nextRowId, index + 1);
        });
      } else {
        // Create mode - add empty row
        modalTitle.innerHTML = `<i class="fas fa-plus me-2"></i>Add Packages for ${hallName}`;
        submitBtn.textContent = 'Save Packages';
        form.action = `/admin/packages/store/${hallId}`;

        // Remove method spoofing for create
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) {
          methodInput.remove();
        }

        addPackageRowDynamic(null, 0);
        nextRowId = 1;
      }
    }

    // Function to update discount_custom, advanceAmount, and date hidden inputs before form submission
    function updateDiscountCustom(reservationId) {
        // Validate date constraints before submitting
        const advDateInput = document.getElementById('advancePaymentDate-' + reservationId);
        if (advDateInput && advDateInput.value && advDateInput.validity.rangeOverflow) {
            alert('Advance payment due date cannot be more than 7 days after the reservation request date.');
            advDateInput.focus();
            return false;
        }
        const cancelDateInput = document.getElementById('cancellationExpiryDate-' + reservationId);
        if (cancelDateInput && cancelDateInput.value && cancelDateInput.validity.rangeOverflow) {
            alert('Cancellation due date must be at least 7 days before the venue date.');
            cancelDateInput.focus();
            return false;
        }
        const rescheduleDateInput = document.getElementById('rescheduledExpiryDate-' + reservationId);
        if (rescheduleDateInput && rescheduleDateInput.value && rescheduleDateInput.validity.rangeOverflow) {
            alert('Re-schedule due date must be at least 7 days before the venue date.');
            rescheduleDateInput.focus();
            return false;
        }
        const advanceInput = document.getElementById('advanceAmount-' + reservationId);
        if (advanceInput) {
            const advanceAmount = parseFloat(advanceInput.value) || 0;
            const cancellationFee = parseFloat(advanceInput.getAttribute('data-cancellation-fee')) || 0;
            if (cancellationFee > 0 && advanceAmount < cancellationFee) {
                alert('Advance payment must be greater than the cancellation fee (Rs. ' + cancellationFee.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ').');
                advanceInput.focus();
                return false;
            }
        }

        const discountInput = document.getElementById('discount-custom-' + reservationId);
        const discountHidden = document.getElementById('discount-custom-hidden-' + reservationId);
        if (discountInput && discountHidden) {
            // Calculate discount from percentage: charge × percentage / 100
            const charge = parseFloat(discountInput.getAttribute('data-charge')) || 0;
            const percentage = parseFloat(discountInput.value) || 0;
            discountHidden.value = (charge * percentage / 100).toFixed(2);
        } else if (discountHidden) {
            // For private customers, discount field is hidden so keep it at 0
            discountHidden.value = 0;
        }
        const advanceHidden = document.getElementById('advanceAmount-hidden-' + reservationId);
        if (advanceInput && advanceHidden) {
            advanceHidden.value = advanceInput.value || 0;
        }
        // Copy advancePaymentDate value
        const advancePaymentDateInput = document.getElementById('advancePaymentDate-' + reservationId);
        const advancePaymentDateHidden = document.getElementById('advancePaymentDate-hidden-' + reservationId);
        if (advancePaymentDateInput && advancePaymentDateHidden) {
            advancePaymentDateHidden.value = advancePaymentDateInput.value || '';
        }
        // Copy cancellationExpiryDate value
        const cancellationExpiryDateInput = document.getElementById('cancellationExpiryDate-' + reservationId);
        const cancellationExpiryDateHidden = document.getElementById('cancellationExpiryDate-hidden-' + reservationId);
        if (cancellationExpiryDateInput && cancellationExpiryDateHidden) {
            cancellationExpiryDateHidden.value = cancellationExpiryDateInput.value || '';
        }
        // Copy rescheduledExpiryDate value
        const rescheduledExpiryDateInput = document.getElementById('rescheduledExpiryDate-' + reservationId);
        const rescheduledExpiryDateHidden = document.getElementById('rescheduledExpiryDate-hidden-' + reservationId);
        if (rescheduledExpiryDateInput && rescheduledExpiryDateHidden) {
            rescheduledExpiryDateHidden.value = rescheduledExpiryDateInput.value || '';
        }
    }

    // Submit accept form only via explicit button click after validation passes
    function acceptReservation(btn, reservationId) {
      if (updateDiscountCustom(reservationId) === false) {
        return;
      }
      btn.closest('form').submit();
    }

    // Hide accept/reject buttons for a slip once admin clicks either one
    document.addEventListener('click', function (e) {
        const form = e.target.closest('.slip-action-form');
        if (form) {
            const actionsDiv = form.closest('[id^="slipActions-"]');
            if (actionsDiv) {
                actionsDiv.style.display = 'none';
            }
        }
    });

  </script>

  <!-- Calendar Modal -->
  <div class="modal fade" id="calendarModal" tabindex="-1" aria-labelledby="calendarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white py-2">
          <h6 class="modal-title mb-0" id="calendarModalLabel">
            <i class="fas fa-calendar-alt me-2"></i>Reservations Calendar
          </h6>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-3">
          <div class="row mb-2">
            <div class="col-6">
              <select id="hallFilter" class="form-select form-select-sm">
                <option value="all">All Halls</option>
                @foreach($halls as $hall)
                  <option value="{{ $hall->id }}">{{ $hall->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-6 text-end">
              <a href="{{ route('admin.calendar.full') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-expand me-1"></i>Full View
              </a>
            </div>
          </div>
          <div id="adminCalendarModal" style="min-height: 400px;"></div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Admin Calendar Modal
    let adminCalendar = null;
    const adminId = {{ $admin->id }};

    document.getElementById('calendarModal').addEventListener('shown.bs.modal', function () {
      // Small delay to ensure modal is fully rendered
      setTimeout(() => {
        if (adminCalendar) {
          adminCalendar.destroy();
        }

        const calendarEl = document.getElementById('adminCalendarModal');
        adminCalendar = new FullCalendar.Calendar(calendarEl, {
          initialView: 'dayGridMonth',
          headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: ''
          },
          height: 'auto',
          contentHeight: 380,
          aspectRatio: 1.5,
          events: function (fetchInfo, successCallback, failureCallback) {
            const hallId = document.getElementById('hallFilter').value;
            const start = fetchInfo.startStr.split('T')[0];
            const end = fetchInfo.endStr.split('T')[0];

            let url = `/admin/calendar/events?admin_id=${adminId}&start=${start}&end=${end}`;
            if (hallId !== 'all') {
              url += `&hall_id=${hallId}`;
            }

            fetch(url)
              .then(response => {
                if (!response.ok) throw new Error('Network error');
                return response.json();
              })
              .then(data => {
                console.log('Calendar events loaded:', data.length);
                successCallback(data);
              })
              .catch(err => {
                console.error('Calendar error:', err);
                alert('Failed to load calendar events. Please try again.');
                failureCallback(err);
              });
          },
          eventDidMount: function (info) {
            // Add tooltip on hover
            const props = info.event.extendedProps;
            const tooltip = `
              <div class="p-2">
                <strong>${info.event.title}</strong><br>
                <strong>Customer:</strong> ${props.customer_name || 'N/A'}<br>
                <strong>Hall:</strong> ${props.hall_name}<br>
                <strong>Time:</strong> ${props.time_slot}<br>
                <strong>Status:</strong> ${props.status}
              </div>
            `;

            tippy(info.el, {
              content: tooltip,
              allowHTML: true,
              theme: 'light-border',
              placement: 'top',
              arrow: true
            });
          },
          eventClick: function (info) {
            // Show reservation details
            const props = info.event.extendedProps;
            const details = `Reservation Details:\n\nCustomer: ${props.customer_name}\nHall: ${props.hall_name}\nDate: ${props.reservation_date}\nTime: ${props.time_slot}\nStatus: ${props.status}`;
            alert(details);
          },
          loading: function (isLoading) {
            if (isLoading) {
              calendarEl.style.opacity = '0.5';
            } else {
              calendarEl.style.opacity = '1';
            }
          }
        });

        adminCalendar.render();
      }, 100);
    });

    // Reload calendar when hall filter changes
    document.getElementById('hallFilter').addEventListener('change', function () {
      if (adminCalendar) {
        adminCalendar.refetchEvents();
      }
    });

    // Clean up when modal is hidden
    document.getElementById('calendarModal').addEventListener('hidden.bs.modal', function () {
      if (adminCalendar) {
        adminCalendar.destroy();
        adminCalendar = null;
      }
    });
  </script>

</body>

</html>
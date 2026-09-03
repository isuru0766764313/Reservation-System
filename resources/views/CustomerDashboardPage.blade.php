<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Customer Dashboard - Hall Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
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
            --gray-light: #f8fafc;
            --white: #ffffff;
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.07), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 10px 25px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -4px rgba(0, 0, 0, 0.04);
            --radius: 12px;
            --radius-sm: 8px;
            --radius-xs: 6px;
            --transition: all 0.2s ease;
        }

        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: var(--gray-light);
            color: var(--dark);
            min-height: 100vh;
        }

        /* ===== Navbar ===== */
        .top-nav {
            background: var(--white) !important;
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 0.75rem 1.5rem !important;
            box-shadow: var(--shadow-sm);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .dashboard-title-nav {
            font-weight: 700;
            font-size: 1.15rem;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dashboard-title-nav i {
            color: var(--primary);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .settings-btn {
            background: transparent;
            border: 1.5px solid #e2e8f0;
            color: var(--gray);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .settings-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--primary-light);
        }

        .btn-logout-dash {
            background: transparent !important;
            border: 1.5px solid #e2e8f0 !important;
            color: var(--danger) !important;
            border-radius: var(--radius-xs) !important;
            padding: 0.4rem 1rem !important;
            font-weight: 500 !important;
            font-size: 0.85rem !important;
            transition: var(--transition) !important;
        }

        .btn-logout-dash:hover {
            background: var(--danger-light) !important;
            border-color: var(--danger) !important;
        }

        /* ===== Content ===== */
        .content-area {
            padding: 1.5rem;
            flex-grow: 1;
        }

        /* ===== Stats ===== */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1.25rem 1.5rem;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: var(--transition);
            border: 1px solid #e2e8f0;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }

        .stat-icon.pending {
            background: var(--warning-light);
            color: var(--warning);
        }

        .stat-icon.confirmed {
            background: var(--info-light);
            color: var(--info);
        }

        .stat-icon.closed {
            background: var(--danger-light);
            color: var(--danger);
        }

        .stat-icon.success {
            background: var(--success-light);
            color: var(--success);
        }

        .stat-icon.rejected {
            background: var(--danger-light);
            color: var(--danger);
        }

        .stat-icon.payment-rejected {
            background: var(--warning-light);
            color: var(--warning);
        }

        .stat-icon.total {
            background: var(--primary-light);
            color: var(--primary);
        }

        .stat-content {
            flex: 1;
        }

        .stat-number {
            font-size: 1.6rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 0.2rem;
        }

        .stat-label {
            color: var(--gray);
            font-size: 0.85rem;
        }

        /* ===== Reservations Card ===== */
        .reservations-card {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }

        .reservations-card .card-header {
            background: var(--white);
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-title i {
            color: var(--primary);
            background: var(--primary-light);
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ===== Table ===== */
        .table-responsive {
            overflow-x: auto;
        }

        .table-custom {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-custom th {
            background: var(--gray-light);
            color: var(--gray);
            font-weight: 600;
            padding: 0.85rem 1rem;
            text-align: left;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .table-custom td {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .table-custom tr:last-child td {
            border-bottom: none;
        }

        .table-custom tr:hover td {
            background-color: var(--primary-light);
        }

        .reservations-table .property-name {
            min-width: 180px;
            width: 18%;
        }

        /* ===== Badges ===== */
        .badge {
            padding: 0.35rem 0.65rem;
            font-weight: 500;
            border-radius: var(--radius-xs);
            font-size: 0.8rem;
        }

        .badge-pending {
            background: var(--warning-light);
            color: #b45309;
        }

        .badge-confirmed {
            background: var(--success-light);
            color: #065f46;
        }

        .badge-closed {
            background: var(--danger-light);
            color: #991b1b;
        }

        .badge.bg-success {
            background: var(--success-light) !important;
            color: #065f46 !important;
        }

        .badge.bg-danger {
            background: var(--danger-light) !important;
            color: #991b1b !important;
        }

        /* ===== Buttons ===== */
        .btn-custom {
            padding: 0.4rem 0.85rem;
            border-radius: var(--radius-xs);
            font-weight: 500;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: var(--transition);
        }

        .btn-view {
            background: var(--primary-light);
            color: var(--primary);
            border: none;
        }

        .btn-view:hover {
            background: var(--primary);
            color: #fff;
        }

        .btn-primary {
            background: var(--primary);
            border: none;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        /* ===== Popover ===== */
        .popover {
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            border: 1px solid #e2e8f0;
            max-width: 320px;
        }

        .popover-header {
            background: var(--white);
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
        }

        .popover-body {
            padding: 1.25rem;
        }

        .popover .arrow::after {
            border-bottom-color: var(--white);
        }

        .popover .arrow::before {
            display: none;
        }

        .popover-container {
            position: relative;
        }

        .user-info-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 1.25rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .user-avatar-lg {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 600;
            font-size: 1.3rem;
        }

        .user-details-lg h5 {
            margin-bottom: 0.2rem;
            font-weight: 600;
        }

        .user-details-lg p {
            margin-bottom: 0;
            color: var(--gray);
        }

        .popover-actions {
            display: flex;
            gap: 10px;
            margin-top: 1.25rem;
        }

        /* ===== OTP ===== */
        .otp-input {
            width: 45px;
            height: 50px;
            margin: 0 5px;
            font-size: 1.5rem;
            text-align: center;
            border-radius: var(--radius-xs);
            border: 2px solid #e2e8f0;
        }

        .otp-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem var(--primary-light);
        }

        /* ===== Pagination ===== */
        .d-none.d-md-block .pagination,
        .d-block.d-md-none .pagination {
            display: flex !important;
        }

        .mobile-reservations .pagination {
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 15px;
        }

        .mobile-reservations .page-item {
            margin: 2px;
            font-size: 0.9rem;
        }

        /* ===== Mobile ===== */
        @media (min-width: 768px) {
            .mobile-reservations {
                display: none !important;
            }
        }

        @media (max-width: 767px) {
            .reservations-table {
                display: none !important;
            }
        }

        @media (max-width: 768px) {
            .top-nav {
                padding: 0.75rem 1rem !important;
                flex-wrap: wrap;
            }

            .user-info {
                width: 100%;
                justify-content: flex-end;
            }

            .content-area {
                padding: 1rem;
            }

            .stats-container {
                grid-template-columns: 1fr;
            }

            .table-custom th,
            .table-custom td {
                padding: 0.6rem 0.75rem;
            }

            .reservation-id,
            .customer-name {
                display: none;
            }

            .popover-actions {
                flex-direction: column;
                gap: 0.5rem;
            }
        }

        @media (max-width: 576px) {

            .property-name,
            .time-period {
                display: none;
            }
        }

        /* ===== Mobile Card ===== */
        .reservation-mobile-card {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid #e2e8f0;
            padding: 1rem;
            margin-bottom: 1rem;
            display: none;
        }

        @media (max-width: 768px) {
            .reservation-mobile-card {
                display: block;
            }
        }

        .reservation-mobile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .reservation-mobile-id {
            font-weight: 600;
            color: var(--primary);
        }

        .reservation-mobile-content {
            display: grid;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .reservation-mobile-row {
            display: flex;
            justify-content: space-between;
        }

        .reservation-mobile-label {
            color: var(--gray);
            font-size: 0.85rem;
        }

        .reservation-mobile-value {
            font-weight: 500;
            text-align: right;
        }

        .reservation-mobile-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
        }

        .reservation-mobile-actions .btn {
            flex: 1 1 auto;
            min-width: 0;
            font-size: 0.75rem;
            padding: 0.35rem 0.6rem;
        }

        .reservation-mobile-actions .btn-mobile-pay {
            flex: 1 1 100%;
        }

        .reservation-mobile-charge {
            font-weight: 600;
            color: var(--primary);
        }

        /* ===== Mobile Action Buttons Wrapping ===== */
        .action-btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem;
        }

        .action-btn-group .btn {
            font-size: 0.78rem;
            padding: 0.3rem 0.6rem;
            white-space: nowrap;
        }

        /* ===== Modal Mobile Improvements ===== */
        @media (max-width: 576px) {
            .modal-body {
                padding: 1rem !important;
            }

            .bank-details dl.row dt {
                width: 100%;
                margin-bottom: 0;
            }

            .bank-details dl.row dd {
                width: 100%;
                padding-left: 0;
                margin-bottom: 0.5rem;
            }

            .payment-upload .btn-lg {
                font-size: 0.85rem;
                white-space: normal;
                padding: 0.6rem 1rem;
            }

            .modal-header h5 {
                font-size: 0.95rem;
            }

            .property-info-grid {
                grid-template-columns: 1fr;
            }

            .facilities-grid {
                grid-template-columns: 1fr;
            }

            .location-details {
                padding: 0.8rem;
            }

            .reservation-dl dt {
                width: 100%;
                margin-bottom: 0;
            }

            .reservation-dl dd {
                width: 100%;
                padding-left: 0;
                margin-bottom: 0.75rem;
            }
        }

        @media (max-width: 768px) {
            .modal-content {
                border-radius: 0;
            }

            .modal-dialog {
                margin: 0.5rem;
            }
        }

        .reservation-dl dt {
            font-weight: 600;
            color: var(--gray);
            font-size: 0.85rem;
        }

        .reservation-dl dd {
            font-weight: 500;
        }

        .bank-details {
            background: var(--gray-light) !important;
            padding: 1rem !important;
            border-radius: var(--radius-sm);
        }

        .bank-details dt {
            font-weight: 600;
            color: var(--gray);
            font-size: 0.85rem;
        }

        .bank-details dd {
            font-weight: 500;
        }

        /* ===== Modals ===== */
        .modal-header.bg-primary,
        .modal-header.bg-primary.text-white {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark)) !important;
        }

        .modal-body {
            padding: 1.5rem 2rem;
        }

        .property-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .info-item {
            background: var(--white);
            border-radius: var(--radius-sm);
            padding: 1rem 1.2rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid #e2e8f0;
        }

        .info-label {
            font-weight: 600;
            color: var(--gray);
            font-size: 0.85rem;
            margin-bottom: 0.2rem;
        }

        .info-value {
            font-weight: 500;
            color: var(--dark);
            font-size: 1rem;
        }

        .section-title {
            position: relative;
            padding-bottom: 0.6rem;
            margin: 1.5rem 0 1rem;
            font-weight: 700;
            color: var(--dark);
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--primary);
            border-radius: 3px;
        }

        .facilities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 0.75rem;
        }

        .facility-item {
            display: flex;
            align-items: center;
            padding: 0.6rem 0.8rem;
            background: var(--gray-light);
            border-radius: var(--radius-xs);
            border: 1px solid #e2e8f0;
        }

        .facility-icon {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--primary-light);
            border-radius: 50%;
            margin-right: 0.6rem;
            color: var(--primary);
        }

        .additional-info {
            background: var(--gray-light);
            border-radius: var(--radius-sm);
            padding: 1.25rem;
            border: 1px solid #e2e8f0;
            line-height: 1.7;
        }

        .location-section {
            background: #fce7f3;
            border-radius: var(--radius-sm);
            overflow: hidden;
            border: 1px solid #fbcfe8;
            margin-bottom: 2rem;
        }

        .location-header {
            padding: 1rem 1.25rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
        }

        .location-details {
            padding: 1.25rem;
        }

        .open-map-btn {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1.2rem;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: var(--radius-xs);
            font-weight: 600;
            transition: var(--transition);
            margin-top: 0.75rem;
        }

        .open-map-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);
        }

        @media (max-width: 768px) {
            .modal-body {
                padding: 1.25rem;
            }

            .property-info-grid {
                grid-template-columns: 1fr;
            }

            .facilities-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <!-- Top Navigation -->
    <div class="top-nav">
        <div class="dashboard-title-nav">
            <i class="fas fa-th-large"></i> Dashboard
        </div>
        <div class="user-info">
            <div class="popover-container">
                <button class="settings-btn" id="settingsButton" data-bs-toggle="popover" data-bs-placement="bottom"
                    data-bs-custom-class="popover-settings" data-bs-title="User Settings" data-bs-content='
                            <div class="user-info-header">
                                <div class="user-avatar-lg">IT</div>
                                <div class="user-details-lg">
                                    <h6 class="mb-1">{{ $customer->first_name }} {{ $customer->last_name }}</h6>
                                    <p class="small text-muted mb-2">{{ $customer->email }}</p>
                                </div>
                            </div>
                            <div class="user-info-content">
                                <div class="info-item">
                                    <div class="info-label">Tel :</div>
                                    <div class="info-value">{{ $customer->telephone_number }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">NID:</div>
                                    <div class="info-value">{{ $customer->national_id }}</div>
                                </div>
                                <div class="popover-actions mt-3">
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editAccountModal">
                                        <i class="fas fa-edit me-2"></i> Edit Account
                                    </button>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                                        <i class="fas fa-key me-2"></i> Reset Password
                                    </button>
                                </div>
                            </div>
                        '>
                    <i class="fas fa-cog"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('logout_route') }}" class="mb-0">
                @csrf
                <button type="submit" class="btn-logout-dash">
                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Content Area -->
    <div class="content-area">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon total"><i class="fas fa-calendar"></i></div>
                <div class="stat-content">
                    <div class="stat-number">{{ $total }}</div>
                    <div class="stat-label">Total Reservations</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon pending"><i class="fas fa-clock"></i></div>
                <div class="stat-content">
                    <div class="stat-number">{{ $pendingRequest }}</div>
                    <div class="stat-label">Pending Requests</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon confirmed"><i class="fas fa-spinner"></i></div>
                <div class="stat-content">
                    <div class="stat-number">{{ $paymentInProgress }}</div>
                    <div class="stat-label">Payment in Progress</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon success"><i class="fas fa-check-circle"></i></div>
                <div class="stat-content">
                    <div class="stat-number">{{ $successful }}</div>
                    <div class="stat-label">Successful Reservations</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon closed"><i class="fas fa-times-circle"></i></div>
                <div class="stat-content">
                    <div class="stat-number">{{ $closed }}</div>
                    <div class="stat-label">Cancelled</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon rejected"><i class="fas fa-ban"></i></div>
                <div class="stat-content">
                    <div class="stat-number">{{ $rejected }}</div>
                    <div class="stat-label">Reservation Rejected</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon payment-rejected"><i class="fas fa-money-bill-wave"></i></div>
                <div class="stat-content">
                    <div class="stat-number">{{ $paymentRejected }}</div>
                    <div class="stat-label">Payment Rejected</div>
                </div>
            </div>
        </div>

        <!-- Reservations Section -->
        <div class="reservations-card">
            <div class="card-header">
                <h5 class="card-title"><i class="fas fa-list"></i> Reservations</h5>
                <div>
                    <a href="{{ route('load_venues_page') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Make New Reservation
                    </a>
                </div>
            </div>

            <!-- Desktop Table -->
            <div class="table-responsive reservations-table">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th class="reservation-id">Reference Code</th>
                            <th class="property-name">Property Name</th>
                            <th>Property Details</th>
                            <th>Reservation Date</th>
                            <th class="time-period">Reservation Period</th>
                            <th>Reservation Charge</th>
                            <th>Status</th>
                            <th>Payment Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reservations as $reservation)
                                                    <tr>
                                                        <td class="reservation-id">{{ $reservation->ref_code ?? $reservation->id }}</td>
                                                        <td class="property-name">{{ $reservation->hall_name }}</td>
                                                        <td>
                                                            <button class="btn btn-view btn-custom" data-bs-toggle="modal"
                                                                data-bs-target="#PropertyDetailsModel-{{ $reservation->id }}">
                                                                <i class="fas fa-eye"></i> Details
                                                            </button>
                                                        </td>
                                                        <td>{{ $reservation->reservation_date }}</td>
                                                        <td class="time-period">{{ date('h.i A', strtotime($reservation->start_time)) }} to {{ date('h.i A', strtotime($reservation->end_time)) }}</td>
                                                        <td>Rs. {{ number_format($reservation->charge, 2) }}</td>
                                                        <!-- Reservation Status -->
                                                        <td>
                                                            <div class="d-flex flex-column gap-1 align-items-start">
                                                                @php
                                                                    $Rbadge = \App\Http\Controllers\ReservationController::getCustomerStatusBadge($reservation);
                                                                    $cancelRecord = $reservation->payments->where('payment_alias', 'Cancellation')->first();
                                                                    $cancelAvail = empty($reservation->cancellationExpiryDate) || \Carbon\Carbon::parse($reservation->cancellationExpiryDate)->isFuture();
                                                                    $rescheduleAvail = empty($reservation->rescheduledExpiryDate) || \Carbon\Carbon::parse($reservation->rescheduledExpiryDate)->isFuture();
                                                                @endphp
                                                                <span>{{ $Rbadge['label'] }}</span>
                                                                @if (in_array($Rbadge['status_id'], [3, 4]) && !$cancelRecord && ($cancelAvail || $rescheduleAvail))
                                                                    @if($cancelAvail)
                                                                    <button type="button" class="btn btn-warning btn-sm action-btn w-100"
onclick="setupCancellationPayment('{{ $reservation->id }}', '{{ number_format($reservation->hall->cancellation_fee, 2) }}', '{{ $reservation->cancellationExpiryDate ?? '' }}', '{{ $reservation->status }}', '{{ route('customer.reservation.cancel', $reservation->id) }}')">
                                                                        <i class="fas fa-times me-1"></i>Cancel
                                                                    </button>
                                                                    @endif
                                                                    @if($rescheduleAvail)
                                                                    <button type="button" class="btn btn-warning btn-sm action-btn w-100 reschedule-btn"
                                                                        data-reservation-id="{{ $reservation->id }}"
                                                                        data-hall-id="{{ $reservation->hall_id }}"
                                                                        data-hall-name="{{ $reservation->hall_name }}"
                                                                        data-date="{{ $reservation->reservation_date }}"
                                                                        data-start-time="{{ $reservation->start_time }}"
                                                                        data-end-time="{{ $reservation->end_time }}"
                                                                        data-pre-arrange="{{ $reservation->pre_arrange_time }}"
                                                                        data-post-arrange="{{ $reservation->post_arrange_time }}"
                                                                        data-rescheduled-expiry="{{ $reservation->rescheduledExpiryDate ?? '' }}">
                                                                        <i class="fas fa-calendar-alt me-1"></i>Re-schedule
                                                                    </button>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        </td>

                                                        <!-- Payment Details -->
                                                        <td>
                                                            @php
    $prelim = $reservation->payments->where('payment_alias', 'Preliminary')->first();
    $remain = $reservation->payments->where('payment_alias', 'Remainings')->first();
	    $totalPaid = $reservation->payments->where('status', 2)->sum('amount');
	    $preliminaryPayment = $reservation->advanceAmount;
$remainingAmount = max(0, (($reservation->charge - ($reservation->discount_custom ?? 0)) + $reservation->deposit) - $totalPaid);
	    $Rbadge = \App\Http\Controllers\ReservationController::getCustomerStatusBadge($reservation);
    $Pbadge = \App\Http\Controllers\ReservationController::getCustomerPaymentStatusBadge($reservation);
    $payments = $reservation->payments; // collection of all payments for this reservation
                                                            @endphp

                                                            <ul class="list-unstyled mb-0" style="font-size:0.85rem;">
                                                                    @foreach($payments as $payment)
                                                                        @php
        $stageLabel = match ($payment->payment_alias) {
            'Preliminary' => 'Advance Payment',
            'Remainings' => 'Balance Payment',
            'Cancellation' => 'Cancellation Fee',
            default => $payment->payment_alias,
        };
        $statusIcon = match ((int) $payment->status) {
            2 => '<i class="fas fa-check-circle text-success"></i>',
            3 => '<i class="fas fa-times-circle text-danger"></i>',
            default => '<i class="fas fa-clock text-warning"></i>',
        };
                                                                        @endphp
                                                                        <li class="mb-1 d-flex justify-content-between align-items-center">
                                                                            <span class="text-muted">{{ $stageLabel }} : Rs. {{ number_format($payment->amount, 2) }}</span>
                                                                            <span>{!! $statusIcon !!}</span>
                                                                        </li>
                                                                    @endforeach
                                                                    <button class="btn btn-primary btn-sm action-btn view-btn" data-bs-toggle="modal" data-bs-target="#PayNowModel-{{ $reservation->id }}">View</button>
                                                                </ul>

























                                                            <!--@if ($reservation->status == 1)
                                                                                                                                                                                                                    <button
                                                                                                                                                                                                                        class="btn {{ $reservation->total_paid == 0 ? 'btn-success' : 'btn-danger' }} btn-sm action-btn view-btn"
                                                                                                                                                                                                                        data-bs-toggle="modal"
                                                                                                                                                                                                                        data-bs-target="#PayNowModel-{{ $reservation->id }}">View</button>
                                                                                                                                                                                                                @elseif ($reservation->status == 2)
                                                                                                                                                                                                                    @if ($prelim && $prelim->status == 1)
                                                                                                                                                                                                                        <button
                                                                                                                                                                                                                            class="btn {{ $reservation->total_paid == 0 ? 'btn-success' : 'btn-danger' }} btn-sm action-btn view-btn"
                                                                                                                                                                                                                            data-bs-toggle="modal"
                                                                                                                                                                                                                            data-bs-target="#PayNowModel-{{ $reservation->id }}">View</button>
                                                                                                                                                                                                                    @elseif ($prelim && $prelim->status == 3)
                                                                                                                                                                                                                        <span class="badge bg-danger">Advance Payment Rejected</span>
                                                                                                                                                                                                                    @else
                                                                                                                                                                                                                        <button
                                                                                                                                                                                                                            class="btn {{ $reservation->total_paid == 0 ? 'btn-success' : 'btn-danger' }} btn-sm action-btn view-btn"
                                                                                                                                                                                                                            data-bs-toggle="modal" data-bs-target="#PayNowModel-{{ $reservation->id }}">
                                                                                                                                                                                                                            Pay Advance Rs. {{ number_format($preliminaryPayment, 2) }}
                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                    @endif
                                                                                                                                                                                                                @elseif ($reservation->status == 3)                                                                                                                                
                                                                                                                                                                                                                    @if ($remain && $remain->status == 1)
                                                                                                                                                                                                                        <button
                                                                                                                                                                                                                            class="btn {{ $reservation->total_paid == 0 ? 'btn-success' : 'btn-danger' }} btn-sm action-btn view-btn"
                                                                                                                                                                                                                            data-bs-toggle="modal"
                                                                                                                                                                                                                            data-bs-target="#PayNowModel-{{ $reservation->id }}">View</button>
                                                                                                                                                                                                                    @elseif ($remain && $remain->status == 3)
                                                                                                                                                                                                                        <span class="badge bg-danger">Remaining Payment Rejected</span>
                                                                                                                                                                                                                    @else
                                                                                                                                                                                                                        <button
                                                                                                                                                                                                                            class="btn {{ $reservation->total_paid == 0 ? 'btn-success' : 'btn-danger' }} btn-sm action-btn view-btn"
                                                                                                                                                                                                                            data-bs-toggle="modal" data-bs-target="#PayNowModel-{{ $reservation->id }}">
                                                                                                                                                                                                                            Pay Remaining Rs. {{ number_format($remainingAmount, 2) }}</button>
                                                                                                                                                                                                                    @endif
                                                                                                                                                                                                                @elseif ($reservation->status == 4)                                                                
                                                                                                                                                                                                                @elseif ($reservation->status == 5)
                                                                                                                                                                                                                     <span class="badge bg-danger">{{ \App\Http\Controllers\ReservationController::getReservationStatusLabel($reservation->status) }}</span>
                                                                                                                                                                                                                 @elseif ($reservation->status == 7)
                                                                                                                                                                                                                     <span class="badge bg-danger">{{ \App\Http\Controllers\ReservationController::getReservationStatusLabel($reservation->status) }}</span>
                                                                                                                                                                                                                 @else
                                                                                                                                                                                                                     <span class="badge bg-danger">{{ \App\Http\Controllers\ReservationController::getReservationStatusLabel($reservation->status) }}</span>
                                                                                                                                                                                                                @endif-->
                                                        </td>
                                                    </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4 d-flex justify-content-center">
                    {{ $reservations->links('pagination::bootstrap-4') }}
                </div>
            </div>

            <!-- Mobile Reservations -->
            <div class="mobile-reservations">
                @foreach($reservations as $reservation)
                    @php
    $totalPaid = $reservation->payments->where('status', 2)->sum('amount');
    $preliminaryPayment = $reservation->advanceAmount;
    $remainingAmount = max(0, (($reservation->charge - ($reservation->discount_custom ?? 0)) + $reservation->deposit) - $totalPaid);
                                        @endphp
                                        <!-- Desktop table payment details -->
                    <div class="reservation-mobile-card">
                        <div class="reservation-mobile-header">
                            <span class="reservation-mobile-id">ID : {{ $reservation->id }}</span>
                            @if($reservation->accepted === null)
                                <span class="badge badge-pending">Pending Request</span>
                            @elseif(!$reservation->accepted)
                                <span class="badge badge-closed">Reservation Cancelled</span>
                            @else
                                @if($reservation->reserved === null)
                                    <span class="badge badge-confirmed">Payment to be verified</span>
                                @elseif($reservation->reserved)
                                    <span class="badge badge-confirmed">Reserved</span>
                                @else
                                    <span class="badge badge-closed">Payment Rejected</span>
                                @endif
                            @endif
                        </div>
                        <div class="reservation-mobile-content">
                            <div class="reservation-mobile-row"><span class="reservation-mobile-label">Property
                                    Name:</span><span class="reservation-mobile-value">{{ $reservation->hall_name }}</span>
                            </div>
                            <div class="reservation-mobile-row"><span class="reservation-mobile-label">Reservation
                                    Date:</span><span
                                    class="reservation-mobile-value">{{ $reservation->reservation_date }}</span></div>
                            <div class="reservation-mobile-row"><span class="reservation-mobile-label">Call
                                    Time:</span><span class="reservation-mobile-value">{{ $reservation->start_time }} -
                                    {{ $reservation->end_time }}</span></div>
                            <div class="reservation-mobile-row"><span class="reservation-mobile-label">Charge:</span><span
                                    class="reservation-mobile-charge">Rs.
                                    {{ number_format($reservation->charge, 2) }}</span></div>
                        </div>
                        <div class="reservation-mobile-actions">
                            @if($reservation->logged && !in_array($reservation->status, [5, 6, 7]))
                                @if($reservation->logged && (!$reservation->advancePaid || $preliminaryPayment > $totalPaid))
                                    <button class="btn btn-success btn-sm btn-mobile-pay" data-bs-toggle="modal"
                                        data-bs-target="#PayNowModel-{{ $reservation->id }}">
                                        <i class="fas fa-credit-card me-1"></i>Pay Advance Rs.
                                        {{ number_format($preliminaryPayment, 2) }}
                                    </button>
                                @elseif($reservation->logged && $reservation->advancePaid && $totalPaid < $reservation->charge)
                                    <button class="btn btn-danger btn-sm btn-mobile-pay" data-bs-toggle="modal"
                                        data-bs-target="#PayNowModel-{{ $reservation->id }}">
                                        <i class="fas fa-credit-card me-1"></i>Pay Remaining Rs.
                                        {{ number_format($remainingAmount, 2) }}
                                    </button>
                                @else
                                    <span class="badge bg-success w-100 text-center py-2">Fully Paid</span>
                                @endif
                                @if ($reservation->user_cancelled && !$reservation->re_scheduled)
                                    <button type="button" class="btn btn-warning btn-sm reschedule-btn"
                                        data-reservation-id="{{ $reservation->id }}" data-hall-id="{{ $reservation->hall_id }}"
                                        data-hall-name="{{ $reservation->hall_name }}"
                                        data-date="{{ $reservation->reservation_date }}"
                                        data-start-time="{{ $reservation->start_time }}"
                                        data-end-time="{{ $reservation->end_time }}"
                                        data-pre-arrange="{{ $reservation->pre_arrange_time }}"
                                        data-post-arrange="{{ $reservation->post_arrange_time }}"
                                        data-rescheduled-expiry="{{ $reservation->rescheduledExpiryDate ?? '' }}">
                                        <i class="fas fa-calendar-alt me-1"></i>Re-schedule
                                    </button>
                                @else
                                    <button type="button" class="btn btn-warning btn-sm"
                                        onclick="setupCancellationPayment('{{ $reservation->id }}', '{{ number_format($reservation->hall->cancellation_fee, 2) }}', '{{ $reservation->cancellationExpiryDate ?? '' }}', '{{ $reservation->status }}', '{{ route('customer.reservation.cancel', $reservation->id) }}')">
                                        <i class="fas fa-times me-1"></i>Cancel
                                    </button>
                                @endif
                            @elseif(in_array($reservation->status, [5, 6, 7]))
                                <span class="badge bg-danger w-100 text-center py-2">{{ \App\Http\Controllers\ReservationController::getReservationStatusLabel($reservation->status) }}</span>
                            @endif
                            <button class="btn btn-view btn-custom" data-bs-toggle="modal"
                                data-bs-target="#PropertyDetailsModel-{{ $reservation->id }}"><i class="fas fa-eye"></i>
                                View Details</button>
                            <button class="btn btn-view btn-custom" data-bs-toggle="modal"
                                data-bs-target="#contactUsModel-{{ $reservation->id }}"><i class="fas fa-phone"></i>
                                Contact</button>
                        </div>
                    </div>
                @endforeach
                <div class="mt-4 d-flex justify-content-center">
                    {{ $reservations->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>

    @foreach ($reservations as $reservation)
        <!-- Re-schedule Modal -->
        <div class="modal fade" id="rescheduleModal" tabindex="-1" aria-labelledby="rescheduleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="rescheduleModalLabel"><i
                                class="fas fa-calendar-alt me-2"></i>Re-schedule Reservation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="rescheduleForm" action="{{ route('customer.reservation.reschedule', $reservation->id) }}"
                        method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <h6><strong>Current Reservation Details:</strong></h6>
                                <p class="mb-1"><strong>Date:</strong> <span id="currentDate"></span></p>
                                <p class="mb-1"><strong>Time:</strong> <span id="currentTime"></span></p>
                                <p class="mb-1"><strong>Total Duration:</strong> <span id="currentDuration"></span> hours
                                </p>
                                <p class="mb-0"><strong>Hall:</strong> <span id="currentHall"></span></p>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <label for="newDate" class="form-label">New Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="newDate" name="new_date"
                                    min="{{ now()->format('Y-m-d') }}" max="{{ now()->addDays(180)->format('Y-m-d') }}"
                                    required>
                                <small class="text-muted">Select a date between today and
                                    {{ now()->addDays(180)->format('M d, Y') }}</small>
                            </div>
                            <div class="mb-4" id="timeSlotsSection" style="display: none;">
                                <h6 class="mb-3">Available Time Slots for Selected Date</h6>
                                <div id="availableTimeSlotsContainer" class="d-flex flex-wrap gap-2">
                                    <div class="alert alert-info w-100"><i class="fas fa-info-circle me-2"></i>Select a date
                                        to see available time slots</div>
                                </div>
                                <small class="text-muted">Click on a time slot to auto-fill start and end times</small>
                            </div>
                            <hr>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="newStartTime" class="form-label">Start Time <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="newStartTime" name="new_start_time" required>
                                        <option value="">Select start time</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="newEndTime" class="form-label">End Time <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="newEndTime" name="new_end_time" required>
                                        <option value="">Select end time</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="newPreArrangeTime" class="form-label">Pre-arrange Time (Setup)</label>
                                    <select class="form-select" id="newPreArrangeTime" name="new_pre_arrange_time">
                                        <option value="0">0 hours</option>
                                        @for ($i = 1; $i <= 5; $i++)
                                            <option value="{{ $i }}">{{ $i }} hour{{ $i > 1 ? 's' : '' }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="newPostArrangeTime" class="form-label">Post-arrange Time (Cleanup)</label>
                                    <select class="form-select" id="newPostArrangeTime" name="new_post_arrange_time">
                                        <option value="0">0 hours</option>
                                        @for ($i = 1; $i <= 5; $i++)
                                            <option value="{{ $i }}">{{ $i }} hour{{ $i > 1 ? 's' : '' }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="alert alert-warning" id="durationWarning" style="display: none;"><i
                                    class="fas fa-exclamation-triangle me-2"></i><span id="durationMessage"></span></div>
                            <div class="alert alert-success" id="durationSuccess" style="display: none;"><i
                                    class="fas fa-check-circle me-2"></i>Total duration matches original reservation!</div>
                            <input type="hidden" id="reservationId" name="reservation_id">
                            <input type="hidden" id="hallId" name="hall_id">
                            <input type="hidden" id="originalDuration" name="original_duration">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" id="saveRescheduleBtn" disabled><i
                                    class="fas fa-save me-2"></i>Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Edit Account Modal -->
    <div class="modal fade" id="editAccountModal" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-user-edit me-2"></i>Edit Account Details</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editAccountForm" method="POST" action="{{ route('customer.details.update', $customer->id) }}">
                    @csrf @method('PUT')
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required">First Name</label>
                                <input type="text" class="form-control" name="first_name"
                                    value="{{ $customer->first_name }}" pattern="[a-zA-Z\s'\-]{2,50}"
                                    title="Only letters, spaces, hyphens and apostrophes allowed (2-50 characters)"
                                    maxlength="50" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">Last Name</label>
                                <input type="text" class="form-control" name="last_name"
                                    value="{{ $customer->last_name }}" pattern="[a-zA-Z\s'\-]{2,50}"
                                    title="Only letters, spaces, hyphens and apostrophes allowed (2-50 characters)"
                                    maxlength="50" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">Email</label>
                                <input type="email" class="form-control" name="email" value="{{ $customer->email }}"
                                    pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$"
                                    title="Valid email format required" maxlength="255" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">Telephone Number</label>
                                <input type="tel" class="form-control" name="telephone_number"
                                    value="{{ $customer->telephone_number }}" pattern="[0-9]{10}"
                                    title="10-digit phone number (e.g. 0771234567)" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label required">National ID</label>
                                <input type="text" class="form-control" name="national_id"
                                    value="{{ $customer->national_id }}" pattern="[a-zA-Z0-9]{10,12}"
                                    title="10-12 alphanumeric characters only" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                class="fas fa-times me-2"></i>Cancel</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save
                            Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div class="modal fade" id="resetPasswordModal" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-key me-2"></i>Reset Password</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="resetPasswordForm" method="POST" action="{{ route('customer.password.reset.request') }}">
                    @csrf
                    <input type="hidden" id="customerId" value="{{ Auth::guard('customer')->user()->id }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label required">Current Password</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                name="current_password" required>
                            @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">New Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                name="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                title="Must contain 8+ chars with uppercase, lowercase and number" required>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Confirm New Password</label>
                            <input type="password" class="form-control" name="password_confirmation" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                class="fas fa-times me-2"></i>Cancel</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-2"></i>Send
                            OTP</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- OTP Verification Modal -->
    <div class="modal fade" id="otpVerificationModal" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-shield-alt me-2"></i>Verify OTP</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="otpVerificationForm" method="POST" action="{{ route('customer.password.reset.verify') }}"
                    data-resend-url="{{ route('customer.password.reset.resend') }}">
                    @csrf
                    <input type="hidden" name="customer_id" id="customerIdInput">
                    <div class="modal-body">
                        <p class="mb-3">We've sent a 6-digit OTP to your email. Please enter it below to verify your
                            identity.</p>
                        <div class="d-flex justify-content-center mb-4 gap-2">
                            @for ($i = 1; $i <= 6; $i++)
                                <input type="text" class="form-control otp-input text-center" name="otp{{ $i }}"
                                    maxlength="1" autocomplete="off" required style="width: 50px;">
                            @endfor
                        </div>
                        <div class="text-center">
                            <p class="text-muted small mb-0">Didn't receive the OTP?</p>
                            <button type="button" class="btn btn-link p-0" id="resendOtpBtn">Resend OTP</button>
                            <p id="otpTimer" class="text-danger mt-2 mb-0">Resend available in <span
                                    id="countdown">120</span> seconds</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                class="fas fa-times me-2"></i>Cancel</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-check me-2"></i>Verify & Update
                            Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Property details modals -->
    @foreach($reservations as $reservation)
        <div class="modal fade" id="PropertyDetailsModel-{{ $reservation->id }}" tabindex="-1"
            aria-labelledby="propertyDetailsLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content property-modal">
                    <div class="modal-header">
                        <h5 class="modal-title" id="propertyDetailsLabel"><i class="fas fa-building me-2"></i>
                            {{ $reservation->hall_name }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="property-info-grid">
                            <div class="info-item">
                                <div class="info-label">Hall Type :</div>
                                <div class="info-value">{{ $reservation->hall->type }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Reservation Type :</div>
                                @if($reservation->reservation_type === 'regular')
                                    <div class="info-value">Regular</div>
                                @else
                                    <div class="info-value">Package</div>
                                @endif                                
                            </div>
                            <div class="info-item">
                                <div class="info-label">Capacity :</div>
                                <div class="info-value">{{ $reservation->hall->capacity }} Guests</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Owner :</div>
                                <div class="info-value">{{ $reservation->hall->admin->company_name }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Inquiry :</div>
                                <div class="info-value">{{ $reservation->hall->admin->telephone_number }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Email :</div>
                                <div class="info-value">{{ $reservation->hall->admin->email }}</div>
                            </div>
                        </div>
                        <div class="location-section">
                            <div class="location-header">
                                <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i> Location Details</h5>
                            </div>
                            <div class="location-details">
                                <p><strong>Full Address:</strong> {{ $reservation->hall->address }},
                                    {{ $reservation->hall->area }}, {{ $reservation->hall->district }},
                                    {{ $reservation->hall->province }}
                                </p>
                                <p><strong>Coordinates:</strong> {{ $reservation->hall->latitude }},
                                    {{ $reservation->hall->longitude }}
                                </p>
                                <button class="open-map-btn" data-lat="{{ $reservation->hall->latitude }}"
                                    data-lng="{{ $reservation->hall->longitude }}">
                                    <i class="fas fa-location-dot me-2"></i> Locate Us
                                </button>
                            </div>
                        </div>
                        <h5 class="section-title"><i class="fas fa-star me-2"></i>Fix Price Facilities</h5>
                        <div class="facilities-grid">
                            @php
                                $selectedFixedFacilityIds = $reservation->fixedFacilities->pluck('id')->toArray();
                            @endphp
                            @foreach($reservation->hall->fixedfacilities as $fp_facility)
                                <div class="facility-item">
                                    <div class="facility-icon">
                                        @if(in_array($fp_facility->id, $selectedFixedFacilityIds))
                                            <i class="fas fa-check"></i>
                                        @endif
                                    </div>
                                    <span>{{ $fp_facility->name }}</span>
                                </div>
                            @endforeach
                        </div>
                        <h5 class="section-title"><i class="fas fa-star me-2"></i>Unit Price Facilities</h5>
                        <div class="facilities-grid">
                            @php
                                $selectedUnitFacilityIds = $reservation->unitFacilities->pluck('id')->toArray();
                            @endphp
                            @foreach($reservation->hall->unitfacilities as $up_facility)
                                <div class="facility-item">
                                    <div class="facility-icon">
                                        @if(in_array($up_facility->id, $selectedUnitFacilityIds))
                                            <i class="fas fa-check"></i>
                                        @endif
                                    </div>
                                    <span>{{ $up_facility->name }}</span>
                                </div>
                            @endforeach
                        </div>
                        <h5 class="section-title"><i class="fas fa-info-circle me-2"></i> Additional Information</h5>
                        <div class="additional-info">
                            <p>{{ $reservation->hall->description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach   

    <!--Pay Now Modal-->
    @foreach($reservations as $reservation)
                    <div class="modal fade" id="PayNowModel-{{ $reservation->id }}" tabindex="-1" aria-labelledby="payNowModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog {{ (int)$reservation->status === 1 ? 'modal-lg' : 'modal-fullscreen' }}">
                            <div class="modal-content h-100 d-flex flex-column">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="payNowModalLabel"><i class="fas fa-money-check-alt me-2"></i>Reservation/Payment Details</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body flex-grow-1 overflow-auto">
                                    @php
        $totalPaid = $reservation->payments->where('status', 2)->sum('amount');
        $preliminaryPayment = $reservation->advanceAmount;
        $remainingAmount = max(0, (($reservation->charge - ($reservation->discount_custom ?? 0)) + $reservation->deposit) - $totalPaid);
                                    @endphp
                                    <div class="mb-4">
                                        <h4 class="mb-3"><i class="fas fa-calendar-alt me-2"></i>Reservation Details</h4>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <dl class="row reservation-dl">                                        
                                                    <dt class="col-sm-4">Ref Code:</dt>
                                                    <dd class="col-sm-8">{{ $reservation->ref_code ?? 'N/A' }}</dd>
                                                    <dt class="col-sm-4">Reservation Type:</dt>
                                                    <dd class="col-sm-8">{{ ucfirst($reservation->reservation_type) }}</dd>
                                                    @if($reservation->reservation_type === 'package' && $reservation->package)
                                                        <dt class="col-sm-4">Package:</dt>
                                                        <dd class="col-sm-8">{{ $reservation->package->name }}</dd>
                                                    @endif
                                                    <dt class="col-sm-4">Hall Name:</dt>
                                                    <dd class="col-sm-8">{{ $reservation->hall->name }}</dd>
                                                    <dt class="col-sm-4">Date:</dt>
                                                    <dd class="col-sm-8">{{ $reservation->reservation_date }}</dd>
                                                    <dt class="col-sm-4">Time Slot:</dt>
                                                    <dd class="col-sm-8">{{ date('h:i A', strtotime($reservation->start_time)) }} - {{ date('h:i A', strtotime($reservation->end_time)) }}</dd>
                                                    @if(strtolower($reservation->reservation_type) !== 'regular')
                                                        @if((int) $reservation->pre_arrange_time > 0)
                                                        <dt class="col-sm-4">Pre-arrange Time:</dt>
                                                        <dd class="col-sm-8">{{ $reservation->pre_arrange_time }} hour(s)</dd>
                                                        @endif
                                                        @if((int) $reservation->post_arrange_time > 0)
                                                        <dt class="col-sm-4">Post-arrange Time:</dt>
                                                        <dd class="col-sm-8">{{ $reservation->post_arrange_time }} hour(s)</dd>
                                                        @endif
                                                    @endif
                                                    @if((int)$reservation->status !== 1)
                                                    <dt class="col-sm-4">Advance Payment Due Date:</dt>
                                                    <dd class="col-sm-8">{{ $reservation->advancePaymentDate ?? 'N/A' }}</dd>
                                                    <dt class="col-sm-4">Cancellation Due:</dt>
                                                    <dd class="col-sm-8">{{ $reservation->cancellationExpiryDate ?? 'N/A' }}</dd>
                                                    <dt class="col-sm-4">Re-schedule Due:</dt>
                                                    <dd class="col-sm-8">{{ $reservation->rescheduledExpiryDate ?? 'N/A' }}</dd>
                                                    @endif
                                                    <dt class="col-sm-4">Current Status:</dt>
                                                    @php
                                                        $condition = \App\Http\Controllers\ReservationController::getCustomerStatusBadge($reservation);
                                                        $statusId = $condition['status_id'];
                                                        $prelimPay = $reservation->payments->where('payment_alias', 'Preliminary')->first();
                                                        $remainPay = $reservation->payments->where('payment_alias', 'Remainings')->first();
                                                        $cancelPay = $reservation->payments->where('payment_alias', 'Cancellation')->first();
                                                    @endphp
                                                    @switch($statusId)
                                                        @case(1)
                                                            <dd class="col-sm-8">Pending - Your reservation request is under review.</dd>
                                                        @break
                                                        @case(2)
                                                            @if($prelimPay && $prelimPay->status == 1)
                                                                <dd class="col-sm-8"><strong>Reservation accepted.</strong><br><small class="text-muted">Advance payment need to be approved.</small></dd>
                                                            @elseif($prelimPay && $prelimPay->status == 2)
                                                                <dd class="col-sm-8"><strong>Reservation accepted.</strong><br><small class="text-muted">Advance payment was approved by admin.<br>You could pay advance amount.</small></dd>
                                                            @elseif($prelimPay && $prelimPay->status == 3)
                                                                <dd class="col-sm-8"><strong>Reservation accepted.</strong><br><small class="text-muted">Advance payment was rejected.</small></dd>
                                                            @else
                                                                <dd class="col-sm-8"><strong>Reservation accepted.</strong><br><small class="text-muted">Pay advance payment.</small></dd>
                                                            @endif                                                            
                                                        @break
                                                        @case(3)
                                                            @if($remainPay && $remainPay->status == 1)
                                                                <dd class="col-sm-8"><strong>Reservation confirmed.</strong><br><small class="text-muted">Balance payment need to be approved.</small></dd>
                                                            @elseif($remainPay && $remainPay->status == 2)
                                                                <dd class="col-sm-8"><strong>Reservation confirmed.</strong><br><small class="text-muted">Balance payment was approved by admin.</small></dd>
                                                            @elseif($remainPay && $remainPay->status == 3)
                                                                <dd class="col-sm-8">Balance payment was rejected.</dd>
                                                            @else
                                                                <dd class="col-sm-8"><strong>Reservation confirmed.</strong><br><small class="text-muted">Pay Balance amount.</small></dd>
                                                            @endif 
                                                        @break
                                                        @case(4)
                                                            <dd class="col-sm-8"><strong>Finished</strong><br><small class="text-muted">Full Payment is done.</small></dd>
                                                        @break
                                                        @case(5)
                                                            @if($cancelPay && $cancelPay->status == 1)
                                                                <dd class="col-sm-8">Cancellation payment need to approved.</dd>
                                                            @elseif($cancelPay && $cancelPay->status == 2)
                                                                <dd class="col-sm-8"><strong>Reservation cancelled</strong><br><small class="text-muted">Cancellation payment was approved by admin.</small></dd>
                                                            @elseif($cancelPay && $cancelPay->status == 3)
                                                                <dd class="col-sm-8">Cancellation payment was rejected.</dd>
                                                            @else
                                                                <dd class="col-sm-8"><strong>Reservation cancelled</strong><br><small class="text-muted">Pay Cancellation fee.</small></dd>
                                                            @endif 
                                                        @break
                                                        @case(6)
                                                            <dd class="col-sm-8">Rejected by admin</dd>
                                                        @break
                                                        @case(7)
                                                            <dd class="col-sm-8">Re-scheduled</dd>
                                                        @break
                                                        @default
                                                            <dd class="col-sm-8">Withdrawn</dd>
                                                    @endswitch                                                    
                                                </dl>
                                            </div>
                                            <div class="col-md-6">
                                                <dl class="row reservation-dl">
                                                    <dt class="col-sm-4">Charge :</dt>
                                                    <dd class="col-sm-8">Rs. {{ number_format($reservation->charge, 2) }}</dd>
                                                    @if((int)$reservation->status !== 1)
                                                    @if($reservation->discount_custom)
                                                        <dt class="col-sm-4">Discount :</dt>
                                                        <dd class="col-sm-8">Rs. {{ number_format($reservation->discount_custom, 2) }}</dd>
                                                    @else
                                                    <dt class="col-sm-4">Discount :</dt>
                                                    <dd class="col-sm-8">Rs. 0.00</dd>
                                                    @endif
                                                    <dt class="col-sm-4">Final Charge:</dt>
                                                    <dd class="col-sm-8 fw-bold text-success" id="final-charge-{{ $reservation->id }}">
                                                        Rs. {{ number_format($reservation->charge - ($reservation->discount_custom ?? 0), 2) }}
                                                    </dd>
                                                    <dt class="col-sm-4">Refundable Deposit :</dt>
                                                    <dd class="col-sm-8">Rs. {{ number_format($reservation->deposit, 2) }}</dd>
                                                    <dt class="col-sm-4">Advance Payment:</dt>
                                                    <dd class="col-sm-8">Rs. {{ number_format($reservation->advanceAmount, 2) }}</dd>                                                    
                                                    @php
                                                        $totalPaidDisplay = $totalPaid;
                                                        if ((int) $reservation->status === 5) {
                                                            $approvedExceptCancellation = $reservation->payments->where('status', 2)->where('payment_alias', '!=', 'Cancellation')->sum('amount');
                                                            $totalPaidDisplay = max(0, $approvedExceptCancellation - ($reservation->hall->cancellation_fee ?? 0));
                                                        }
                                                    @endphp
                                                    <dt class="col-sm-4">Total Paid:</dt>
                                                    <dd class="col-sm-8">Rs. {{ number_format($totalPaidDisplay, 2) }}</dd>
                                                    @if(!in_array($reservation->status, [5, 6, 7]))
                                                        <dt class="col-sm-4">Remaining Amount:</dt>
                                                        <dd class="col-sm-8">Rs. {{ number_format(max(0, (($reservation->charge - $reservation->discount_custom) + $reservation->deposit) - $totalPaid), 2) }}</dd>
                                                    @endif
                                                    @endif
                                                    
                                                    @php
                                                        // Determine if there is a pending payment action
                                                        $nextLabel = null;
                                                        $nextAmount = null;
                                                        if ($statusId == 2 && (!$prelimPay || $prelimPay->status == 3)) {
                                                            $nextLabel = 'Pay Advance';
                                                            $nextAmount = $reservation->advanceAmount;
                                                        } elseif ($statusId == 3 && (!$remainPay || $remainPay->status == 3)) {
                                                            $nextLabel = 'Pay balance amount';
                                                            $nextAmount = max(0, (($reservation->charge - ($reservation->discount_custom ?? 0)) + $reservation->deposit) - $totalPaid);
                                                        } elseif ($statusId == 5 && (!$cancelPay || $cancelPay->status == 3)) {
                                                            $nextLabel = 'Pay Cancellation Fee';
                                                            $nextAmount = $reservation->hall->cancellation_fee ?? 0;
                                                        }
                                                    @endphp
                                                    @if($nextLabel)
                                                        <dt class="col-sm-4">Next : </dt>
                                                        <dd class="col-sm-8">{{ $nextLabel }} : Rs. {{ number_format($nextAmount, 2) }}</dd>
                                                    @endif
                                                </dl>
                                            </div>
                                        </div>
                                    </div>
                                    @if((int)$reservation->status !== 1)
                                    <div class="mb-4">
                                        <h4 class="mb-3"><i class="fas fa-university me-2"></i>Bank Transfer Details</h4>
                                        <div class="alert alert-info">                                            
                                                @if($reservation->status == 1)
                                                    <p class="mb-3">Wait for admin acept the reservation. Once Done You could proceed.</p>
                                                @elseif($reservation->status == 2)
                                                    <p class="mb-3">Please transfer Rs. {{ number_format($preliminaryPayment, 2) }} to the following bank account and upload your payment receipt below:</p>
                                                @elseif($reservation->status == 3)
                                                    <p class="mb-3">Please transfer Rs. {{ number_format($remainingAmount, 2) }} to the following bank account and upload your payment receipt below:</p>
                                                @elseif($reservation->status == 4)
                                                    <p class="mb-3">Everything is paid already.</p>
                                                @elseif($reservation->status == 5)
                                                    <p class="mb-3">Since cancelled nothing to pay</p>
                                                @elseif($reservation->status == 6)
                                                    <p class="mb-3">Nothing to pay. You have just re-scheduled.</p>
                                                @else
                                                @endif
                                            
                                            <div class="bank-details bg-light p-4 rounded">
                                                <dl class="row mb-0">
                                                    <dt class="col-sm-3">Bank Name:</dt>
                                                    <dd class="col-sm-9">{{ $reservation->hall->admin->bank }}</dd>
                                                    <dt class="col-sm-3">Account Name:</dt>
                                                    <dd class="col-sm-9">{{ $reservation->hall->admin->account_name }}</dd>
                                                    <dt class="col-sm-3">Account Number:</dt>
                                                    <dd class="col-sm-9">{{ $reservation->hall->admin->account_number }}</dd>
                                                    <dt class="col-sm-3">Mobile Number:</dt>
                                                    <dd class="col-sm-9">{{ $reservation->hall->admin->telephone_number }}</dd>
                                                    <dt class="col-sm-3">Email:</dt>
                                                    <dd class="col-sm-9">{{ $reservation->hall->admin->email }}</dd>
                                                </dl>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="payment-upload pt-3 mt-3 border-top">
                                        <h4 class="mb-3"><i class="fas fa-receipt me-2"></i>Upload Payment Receipt</h4>
                                        <form action="{{ route('payment.submit', $reservation->id) }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="receipt" class="form-label">Upload Bank Receipt (PDF or Image)</label>
                                                <input type="file" class="form-control" id="receipt" name="receipt"
                                                    accept=".pdf,.jpg,.jpeg,.png" required>
                                                <div class="form-text">Max file size: 5MB</div>
                                            </div>
                                            {{-- Payment type inputs (values set by JS for cancellation, Blade for regular) --}}
                                            <input type="hidden" name="payment_alias" id="paymentAlias-{{ $reservation->id }}"
                                                value="{{ $totalPaid >= $preliminaryPayment ? 'Remainings' : 'Preliminary' }}">
                                            <input type="hidden" name="amount" id="paymentAmount-{{ $reservation->id }}"
                                                value="{{ $totalPaid >= $preliminaryPayment ? number_format($remainingAmount, 2) : number_format($preliminaryPayment, 2) }}">
                                            <div class="d-grid">
                                                @php
                                                    $hasPendingPayment = $reservation->payments->where('status', 1)->count() > 0;
                                                @endphp
                                                <button type="submit" class="btn btn-success btn-lg" id="paySubmitBtn-{{ $reservation->id }}" @if(in_array($reservation->status, [1, 4, 5]) || $hasPendingPayment) disabled @endif>
                                                    Submit Payment Receipt
                                                </button>
                                            </div>
                                            <!--<div class="d-grid">
                                                @php
                                                    $prelim = $reservation->payments->where('payment_alias', 'Preliminary')->first();
                                                    $remain = $reservation->payments->where('payment_alias', 'Remainings')->first();
                                                    $hasPendingPayment = $reservation->payments->where('status', 1)->count() > 0;
                                                @endphp
                                                <button type="submit" class="btn btn-success btn-lg"
                                                    id="paySubmitBtn-{{ $reservation->id }}" @if(in_array($reservation->status, [1, 4, 5]) || $hasPendingPayment) disabled @endif>
                                                    @if ($reservation->status == 1)
                                                        <i class="fas fa-clock me-2"></i>Wait for admin approval
                                                    @elseif ($reservation->status == 2)
                                                        @if ($prelim && $prelim->status == 1)
                                                            <i class="fas fa-spinner me-2"></i>Await admin approval for advance payment
                                                        @elseif ($prelim && $prelim->status == 3)
                                                            <i class="fas fa-times-circle me-2"></i>Advance payment rejected. Contact admin.
                                                        @else
                                                            <i class="fas fa-check-circle me-2"></i>Pay Advance amount Rs.
                                                            {{ number_format($preliminaryPayment, 2) }}
                                                        @endif
                                                    @elseif ($reservation->status == 3)
                                                        @if ($remain && $remain->status == 1)
                                                            <i class="fas fa-spinner me-2"></i>Awaiting admin approval for remaining payment
                                                        @elseif ($remain && $remain->status == 3)
                                                            <i class="fas fa-times-circle me-2"></i>Remaining payment rejected. Contact admin.
                                                        @else
                                                            <i class="fas fa-check-circle me-2"></i>Pay Remaining (including Deposit) Rs.
                                                            {{ number_format($remainingAmount, 2) }}
                                                        @endif
                                                    @elseif ($reservation->status == 4)
                                                        <i class="fas fa-check-circle me-2"></i>Fully Paid
                                                    @elseif ($reservation->status == 5)
                                                        <i class="fas fa-ban me-2"></i>Reservation Cancelled
                                                    @elseif ($reservation->status == 7)
                                                        <i class="fas fa-calendar-alt me-2"></i>Reservation Re-scheduled
                                                    @elseif ($reservation->status == 6)
                                                        <i class="fas fa-times-circle me-2"></i>Reservation Rejected
                                                    @else
                                                        <i class="fas fa-clock me-2"></i>Wait for admin approval
                                                    @endif
                                                </button>                                               
                                            </div>-->
                                        </form>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
    @endforeach

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function setupCancellationPayment(reservationId, cancellationFee, cancellationExpiryDate, currentStatus, cancelUrl) {
            // Check cancellation expiry date
            if (cancellationExpiryDate) {
                const currentDate = new Date();
                currentDate.setHours(0, 0, 0, 0);
                const expiryDate = new Date(cancellationExpiryDate + 'T00:00:00');
                if (currentDate > expiryDate) {
                    alert('You cannot cancel. Cancellation date has passed.');
                    return false;
                }
            }
            if (!confirm('Are you sure you want to cancel this reservation?\nCancellation fee: Rs. ' + cancellationFee + '/= will be deducted from your paid amount.')) {
                return false;
            }
            // Direct cancellation - no slip upload, no admin approval needed
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = cancelUrl;
            form.style.display = 'none';
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = csrfToken;
            form.appendChild(tokenInput);
            document.body.appendChild(form);
            form.submit();
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Popover
            const popoverTrigger = document.getElementById('settingsButton');
            const popover = new bootstrap.Popover(popoverTrigger, {
                html: true,
                sanitize: false,
                placement: window.innerWidth < 576 ? 'bottom' : 'bottom'
            });

            window.addEventListener('resize', function () { popover.update(); });

            document.addEventListener('click', function (e) {
                if (!popoverTrigger.contains(e.target) && !document.querySelector('.popover')?.contains(e.target)) {
                    popover.hide();
                }
            });

            document.addEventListener('click', function (e) {
                if (e.target.id === 'popoverEditAccount' || e.target.id === 'popoverResetPassword') {
                    popover.hide();
                }
            });

            ['editAccountModal', 'resetPasswordModal', 'propertyDetailsModal', 'contactUsModal'].forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.addEventListener('show.bs.modal', function () { popover.hide(); });
                }
            });

            // Password Reset
            const resetPasswordForm = document.getElementById('resetPasswordForm');
            if (resetPasswordForm) {
                resetPasswordForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const form = this;
                    const formData = new FormData(form);
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalBtnText = submitBtn.innerHTML;
                    form.querySelectorAll('.is-invalid').forEach(input => input.classList.remove('is-invalid'));
                    form.querySelectorAll('.invalid-feedback').forEach(error => error.remove());
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Sending...';
                    submitBtn.disabled = true;

                    fetch(form.action, { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const resetModal = bootstrap.Modal.getInstance(document.getElementById('resetPasswordModal'));
                                if (resetModal) resetModal.hide();
                                const otpModal = new bootstrap.Modal(document.getElementById('otpVerificationModal'));
                                otpModal.show();
                                document.getElementById('customerIdInput').value = document.getElementById('customerId').value;
                                document.querySelectorAll('.otp-input').forEach(input => { input.value = ''; input.style.borderColor = ''; input.style.boxShadow = ''; });
                                startTimer(120);
                                const firstOtp = document.querySelector('.otp-input');
                                if (firstOtp) firstOtp.focus();
                            } else {
                                if (data.message) alert(data.message);
                                if (data.errors) {
                                    Object.keys(data.errors).forEach(field => {
                                        const input = form.querySelector(`[name="${field}"]`);
                                        if (input) {
                                            input.classList.add('is-invalid');
                                            let errorElement = input.nextElementSibling;
                                            if (!errorElement || !errorElement.classList.contains('invalid-feedback')) {
                                                errorElement = document.createElement('div');
                                                errorElement.className = 'invalid-feedback';
                                                input.parentNode.appendChild(errorElement);
                                            }
                                            errorElement.textContent = data.errors[field][0];
                                        }
                                    });
                                }
                            }
                        })
                        .catch(error => { console.error('Error:', error); alert('An unexpected error occurred'); })
                        .finally(() => { submitBtn.innerHTML = originalBtnText; submitBtn.disabled = false; });
                });
            }

            // OTP Verification
            const otpVerificationForm = document.getElementById('otpVerificationForm');
            if (otpVerificationForm) {
                otpVerificationForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const form = this;
                    const formData = new FormData(form);
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalBtnText = submitBtn.innerHTML;
                    document.querySelectorAll('.otp-input').forEach(input => { input.style.borderColor = ''; input.style.boxShadow = ''; });
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Verifying...';
                    submitBtn.disabled = true;

                    fetch(form.action, { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                if (data.message) alert(data.message);
                                const otpModal = bootstrap.Modal.getInstance(document.getElementById('otpVerificationModal'));
                                if (otpModal) otpModal.hide();
                                if (data.redirect) window.location.href = data.redirect;
                            } else {
                                if (data.errors && data.errors.otp) {
                                    alert(data.errors.otp[0]);
                                    document.querySelectorAll('.otp-input').forEach(input => { input.value = ''; input.style.borderColor = '#dc3545'; input.style.boxShadow = '0 0 0 0.25rem rgba(220,53,69,0.25)'; });
                                    const firstOtp = document.querySelector('.otp-input');
                                    if (firstOtp) firstOtp.focus();
                                } else if (data.errors) {
                                    Object.keys(data.errors).forEach(field => alert(data.errors[field][0]));
                                } else if (data.message) alert(data.message);
                            }
                        })
                        .catch(error => { console.error('Error:', error); alert('An unexpected error occurred'); })
                        .finally(() => { submitBtn.innerHTML = originalBtnText; submitBtn.disabled = false; });
                });
            }

            // OTP Input Navigation
            const otpInputs = document.querySelectorAll('.otp-input');
            otpInputs.forEach((input, index) => {
                input.addEventListener('input', function () {
                    this.value = this.value.replace(/[^0-9]/g, '');
                    this.style.borderColor = '';
                    this.style.boxShadow = '';
                    if (this.value.length === 1 && index < otpInputs.length - 1) otpInputs[index + 1].focus();
                });
                input.addEventListener('keydown', function (e) {
                    if (e.key === 'Backspace' && this.value === '' && index > 0) otpInputs[index - 1].focus();
                });
                input.addEventListener('paste', function (e) {
                    e.preventDefault();
                    const paste = (e.clipboardData || window.clipboardData).getData('text');
                    const digits = paste.replace(/[^0-9]/g, '').slice(0, 6);
                    digits.split('').forEach((digit, i) => { if (otpInputs[index + i]) otpInputs[index + i].value = digit; });
                    const nextIndex = Math.min(index + digits.length, otpInputs.length - 1);
                    otpInputs[nextIndex].focus();
                });
            });

            // Timer
            let countdown;
            function startTimer(duration) {
                clearInterval(countdown);
                const timerDisplay = document.getElementById('countdown');
                const resendButton = document.getElementById('resendOtpBtn');
                const otpTimer = document.getElementById('otpTimer');
                if (resendButton) resendButton.style.display = 'none';
                if (otpTimer) otpTimer.style.display = 'block';
                let timer = duration;
                const updateTimer = () => { if (timerDisplay) timerDisplay.textContent = timer; };
                updateTimer();
                countdown = setInterval(() => {
                    timer--;
                    updateTimer();
                    if (timer <= 0) { clearInterval(countdown); if (resendButton) resendButton.style.display = 'inline'; if (otpTimer) otpTimer.style.display = 'none'; }
                }, 1000);
            }

            // Resend OTP
            const resendOtpBtn = document.getElementById('resendOtpBtn');
            if (resendOtpBtn) {
                resendOtpBtn.addEventListener('click', function () {
                    const customerId = document.getElementById('customerIdInput').value;
                    const btn = this;
                    const originalBtnText = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Sending...';
                    btn.disabled = true;
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value;

                    fetch(otpVerificationForm.getAttribute('data-resend-url') || '/customer/password/reset-resend-otp', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                        body: JSON.stringify({ customer_id: customerId })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                btn.style.display = 'none';
                                const otpTimer = document.getElementById('otpTimer');
                                if (otpTimer) otpTimer.style.display = 'block';
                                startTimer(120);
                                document.querySelectorAll('.otp-input').forEach(input => { input.value = ''; input.style.borderColor = ''; input.style.boxShadow = ''; });
                                const firstOtp = document.querySelector('.otp-input');
                                if (firstOtp) firstOtp.focus();
                            } else if (data.error) alert(data.error);
                        })
                        .catch(error => { console.error('Error:', error); alert('An error occurred while resending OTP'); })
                        .finally(() => { btn.innerHTML = originalBtnText; btn.disabled = false; });
                });
            }

            // Map buttons
            document.querySelectorAll('.open-map-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const lat = this.dataset.lat;
                    const lng = this.dataset.lng;
                    const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
                    if (isMobile) window.location.href = `geo:${lat},${lng}?q=${lat},${lng}`;
                    else window.open(`https://www.google.com/maps/search/?api=1&query=${lat},${lng}`, '_blank');
                });
            });

            // ===== Re-Schedule Logic =====
            let originalTotalDuration = 0;
            let currentReservationId = null;
            let currentHallId = null;
            let selectedRescheduleSlot = null;

            document.querySelectorAll('.reschedule-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const rescheduledExpiry = this.getAttribute('data-rescheduled-expiry');
                    if (rescheduledExpiry) {
                        const currentDate = new Date();
                        currentDate.setHours(0, 0, 0, 0);
                        const expiryDate = new Date(rescheduledExpiry + 'T00:00:00');
                        if (currentDate > expiryDate) { alert('You can re-schedule. The due date of re-sheduling is over.'); return false; }
                    }
                    const reservationId = this.getAttribute('data-reservation-id');
                    const hallId = this.getAttribute('data-hall-id');
                    const date = this.getAttribute('data-date');
                    const startTime = this.getAttribute('data-start-time');
                    const endTime = this.getAttribute('data-end-time');
                    const preArrange = this.getAttribute('data-pre-arrange') || 0;
                    const postArrange = this.getAttribute('data-post-arrange') || 0;
                    const hallName = this.getAttribute('data-hall-name');
                    currentReservationId = reservationId;
                    currentHallId = hallId;
                    selectedRescheduleSlot = null;
                    const startMinutes = timeToMinutes(startTime);
                    const endMinutes = timeToMinutes(endTime);
                    const mainDuration = (endMinutes - startMinutes) / 60;
                    originalTotalDuration = mainDuration + parseInt(preArrange) + parseInt(postArrange);
                    document.getElementById('currentDate').textContent = date;
                    document.getElementById('currentTime').textContent = `${formatTime12Hour(startTime)} - ${formatTime12Hour(endTime)}`;
                    document.getElementById('currentDuration').textContent = mainDuration;
                    document.getElementById('currentHall').textContent = hallName;
                    document.getElementById('reservationId').value = reservationId;
                    document.getElementById('hallId').value = hallId;
                    document.getElementById('originalDuration').value = originalTotalDuration;
                    resetForm();
                    const modal = new bootstrap.Modal(document.getElementById('rescheduleModal'));
                    modal.show();
                });
            });

            document.getElementById('newDate').addEventListener('change', async function () {
                const selectedDate = this.value;
                if (!selectedDate) { document.getElementById('timeSlotsSection').style.display = 'none'; return; }
                const container = document.getElementById('availableTimeSlotsContainer');
                container.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i></div>';
                document.getElementById('timeSlotsSection').style.display = 'block';
                try {
                    const url = `/customer/hall/availability/${selectedDate}?hall_id=${currentHallId}&exclude_reservation=${currentReservationId}`;
                    const response = await fetch(url);
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) { const text = await response.text(); console.error('Non-JSON:', text.substring(0, 200)); throw new Error('Non-JSON response'); }
                    const unavailablePeriods = await response.json();
                    renderAvailableSlots(unavailablePeriods);
                } catch (error) {
                    container.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error: ${error.message}</div>`;
                }
            });

            function renderAvailableSlots(unavailablePeriods) {
                const container = document.getElementById('availableTimeSlotsContainer');
                if (!Array.isArray(unavailablePeriods)) unavailablePeriods = Object.values(unavailablePeriods);
                let availableSlots = [];
                let lastEnd = '00:00';
                unavailablePeriods.sort((a, b) => a.start_time.localeCompare(b.start_time));
                unavailablePeriods.forEach(period => {
                    if (lastEnd < period.start_time) availableSlots.push({ start: lastEnd, end: period.start_time, duration: (timeToMinutes(period.start_time) - timeToMinutes(lastEnd)) / 60 });
                    lastEnd = period.end_time > lastEnd ? period.end_time : lastEnd;
                });
                if (lastEnd < '23:59') availableSlots.push({ start: lastEnd, end: '23:59', duration: (timeToMinutes('23:59') - timeToMinutes(lastEnd)) / 60 });
                if (availableSlots.length === 0) { container.innerHTML = '<div class="alert alert-warning w-100">No available slots for this date</div>'; return; }
                let html = '';
                availableSlots.forEach(slot => { html += `<label class="btn btn-outline-success time-slot-label mb-2" style="flex:1 0 45%;white-space:nowrap;"><input type="radio" name="time_slot_reschedule" data-start="${slot.start}" data-end="${slot.end}" class="d-none"><i class="far fa-clock me-2"></i>${formatTime12Hour(slot.start)} - ${formatTime12Hour(slot.end)}</label>`; });
                container.innerHTML = html;
                container.querySelectorAll('.time-slot-label input[type="radio"]').forEach(radio => { radio.addEventListener('change', function (e) { handleTimeSlotSelection(e); }); });
            }

            function handleTimeSlotSelection(e) {
                const startTime = e.target.dataset.start;
                const endTime = e.target.dataset.end;
                selectedRescheduleSlot = { start: startTime, end: endTime };
                document.querySelectorAll('#availableTimeSlotsContainer .time-slot-label').forEach(label => label.classList.remove('active'));
                e.target.closest('.time-slot-label').classList.add('active');
                document.getElementById('newStartTime').value = startTime;
                document.getElementById('newEndTime').value = endTime;
                enableAndPopulateTimeDropdowns(startTime, endTime);
                calculateAndValidateDuration();
            }

            function enableAndPopulateTimeDropdowns(slotStart, slotEnd) {
                const startTimeSelect = document.getElementById('newStartTime');
                const endTimeSelect = document.getElementById('newEndTime');
                startTimeSelect.innerHTML = '<option value="">Select start time</option>';
                endTimeSelect.innerHTML = '<option value="">Select end time</option>';
                const slotStartMinutes = timeToMinutes(slotStart);
                const slotEndMinutes = timeToMinutes(slotEnd);
                const times = [];
                for (let minutes = slotStartMinutes; minutes <= slotEndMinutes; minutes += 30) times.push(formatMinutesToTime(minutes));
                times.forEach(time => { if (timeToMinutes(time) < slotEndMinutes) { const opt = document.createElement('option'); opt.value = time; opt.textContent = formatTime12Hour(time); startTimeSelect.appendChild(opt); } });
                times.forEach(time => { if (timeToMinutes(time) > slotStartMinutes) { const opt = document.createElement('option'); opt.value = time; opt.textContent = formatTime12Hour(time); endTimeSelect.appendChild(opt); } });
                startTimeSelect.value = slotStart;
                endTimeSelect.value = slotEnd;
            }

            document.getElementById('newStartTime').addEventListener('change', function () {
                if (selectedRescheduleSlot) { if (timeToMinutes(this.value) < timeToMinutes(selectedRescheduleSlot.start)) { alert('Start time cannot be before the selected slot start time'); this.value = selectedRescheduleSlot.start; } }
                calculateAndValidateDuration();
            });
            document.getElementById('newEndTime').addEventListener('change', function () {
                if (selectedRescheduleSlot) { if (timeToMinutes(this.value) > timeToMinutes(selectedRescheduleSlot.end)) { alert('End time cannot be after the selected slot end time'); this.value = selectedRescheduleSlot.end; } }
                calculateAndValidateDuration();
            });
            document.getElementById('newPreArrangeTime').addEventListener('change', calculateAndValidateDuration);
            document.getElementById('newPostArrangeTime').addEventListener('change', calculateAndValidateDuration);

            function calculateAndValidateDuration() {
                const startTime = document.getElementById('newStartTime').value;
                const endTime = document.getElementById('newEndTime').value;
                const preArrange = parseInt(document.getElementById('newPreArrangeTime').value) || 0;
                const postArrange = parseInt(document.getElementById('newPostArrangeTime').value) || 0;
                const durationWarning = document.getElementById('durationWarning');
                const durationSuccess = document.getElementById('durationSuccess');
                const saveBtn = document.getElementById('saveRescheduleBtn');
                durationWarning.style.display = 'none';
                durationSuccess.style.display = 'none';
                saveBtn.disabled = true;
                if (!startTime || !endTime || !selectedRescheduleSlot) return;
                const startMinutes = timeToMinutes(startTime);
                const endMinutes = timeToMinutes(endTime);
                if (endMinutes <= startMinutes) { document.getElementById('durationMessage').textContent = 'End time must be after start time'; durationWarning.style.display = 'block'; return; }
                const mainDuration = (endMinutes - startMinutes) / 60;
                const newTotalDuration = mainDuration + preArrange + postArrange;
                const slotStartMinutes = timeToMinutes(selectedRescheduleSlot.start);
                const slotEndMinutes = timeToMinutes(selectedRescheduleSlot.end);
                const actualStartMinutes = startMinutes - (preArrange * 60);
                const actualEndMinutes = endMinutes + (postArrange * 60);
                if (actualStartMinutes < 0) { document.getElementById('durationMessage').textContent = 'Pre-arrange time extends before midnight. Please reduce or select later start time'; durationWarning.style.display = 'block'; return; }
                if (actualEndMinutes > 1439) { document.getElementById('durationMessage').textContent = 'Post-arrange time extends beyond 11:59 PM. Please reduce or select earlier end time'; durationWarning.style.display = 'block'; return; }
                const fitsInSlot = (actualStartMinutes >= slotStartMinutes && actualEndMinutes <= slotEndMinutes);
                if (!fitsInSlot) {
                    let msg = '';
                    if (actualStartMinutes < slotStartMinutes) msg = `Pre-arrange time extends ${Math.ceil((slotStartMinutes - actualStartMinutes) / 60)}h before slot start (${formatTime12Hour(selectedRescheduleSlot.start)}). Reduce pre-arrange time or select later start time`;
                    else if (actualEndMinutes > slotEndMinutes) msg = `Post-arrange time extends ${Math.ceil((actualEndMinutes - slotEndMinutes) / 60)}h beyond slot end (${formatTime12Hour(selectedRescheduleSlot.end)}). Reduce post-arrange time or select earlier end time`;
                    document.getElementById('durationMessage').textContent = msg;
                    durationWarning.style.display = 'block';
                    return;
                }
                if (newTotalDuration > originalTotalDuration) { document.getElementById('durationMessage').textContent = `Total duration (${newTotalDuration.toFixed(1)} hours) exceeds original duration (${originalTotalDuration.toFixed(1)} hours). Please adjust your selection.`; durationWarning.style.display = 'block'; }
                else if (newTotalDuration === originalTotalDuration) { durationWarning.style.display = 'none'; durationSuccess.style.display = 'block'; saveBtn.disabled = false; }
                else { document.getElementById('durationMessage').textContent = `Total duration (${newTotalDuration.toFixed(1)} hours) is less than original (${originalTotalDuration.toFixed(1)} hours). You can proceed, but unused time will be released.`; durationWarning.style.display = 'block'; saveBtn.disabled = false; }
            }

            function formatTime12Hour(time24) { if (!time24) return ''; const [h, m] = time24.split(':'); const hour = parseInt(h); return `${hour % 12 || 12}:${m} ${hour >= 12 ? 'PM' : 'AM'}`; }
            function timeToMinutes(t) { if (!t) return 0; const [h, m] = t.split(':').map(Number); return h * 60 + m; }
            function formatMinutesToTime(min) { return `${String(Math.floor(min / 60)).padStart(2, '0')}:${String(min % 60).padStart(2, '0')}`; }
            function resetForm() {
                document.getElementById('newDate').value = '';
                document.getElementById('newStartTime').value = '';
                document.getElementById('newEndTime').value = '';
                document.getElementById('newPreArrangeTime').value = '0';
                document.getElementById('newPostArrangeTime').value = '0';
                selectedRescheduleSlot = null;
                document.getElementById('durationWarning').style.display = 'none';
                document.getElementById('durationSuccess').style.display = 'none';
                document.getElementById('timeSlotsSection').style.display = 'none';
                document.getElementById('saveRescheduleBtn').disabled = true;
                document.getElementById('availableTimeSlotsContainer').innerHTML = '<div class="alert alert-info w-100"><i class="fas fa-info-circle me-2"></i>Select a date to see available time slots</div>';
                document.getElementById('newStartTime').innerHTML = '<option value="">Select start time</option>';
                document.getElementById('newEndTime').innerHTML = '<option value="">Select end time</option>';
            }

            document.getElementById('rescheduleForm').addEventListener('submit', function (e) {
                e.preventDefault();
                const form = this;
                const submitBtn = document.getElementById('saveRescheduleBtn');
                const originalBtnHtml = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
                fetch(form.action, {
                    method: 'PATCH',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value, 'X-Requested-With': 'XMLHttpRequest' },
                    body: new URLSearchParams(new FormData(form))
                })
                    .then(response => response.json())
                    .then(data => { if (data.success) { alert('Reservation rescheduled successfully!'); location.reload(); } else throw new Error(data.message || 'Error rescheduling'); })
                    .catch(error => { alert(error.message); submitBtn.disabled = false; submitBtn.innerHTML = originalBtnHtml; });
            });
        });
    </script>
</body>

</html>
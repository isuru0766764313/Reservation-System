<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Details - Hall Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        body {
            background: #f8fafc;
            color: var(--dark);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .navbar-dashboard {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%) !important;
            padding: 0.85rem 1.5rem !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }

        .card {
            border: none;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark)) !important;
            border-bottom: none;
            padding: 1.25rem 1.5rem;
        }

        .card-header .modal-title {
            font-weight: 700;
            font-size: 1.05rem;
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
        }

        .slip-image {
            max-height: 400px;
            object-fit: contain;
        }

        .badge {
            padding: 0.45rem 0.85rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        dl.row {
            margin-bottom: 0;
        }

        dl.row dt {
            font-weight: 600;
            color: var(--dark);
        }

        dl.row dd {
            font-weight: 500;
        }

        .section-title {
            font-size: 1rem;
            font-weight: 700;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-success {
            background: #1b10e6ff !important;
            border: none !important;
        }

        .btn-success:hover {
            background: #160ce4ff !important;
        }

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
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-dashboard shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('admin.dashboard.route') }}">
                <i class="fas fa-building me-2"></i>Hall Booking System Admin Panel
            </a>
            <div class="d-flex align-items-center">
                <span class="text-white nav-user">
                    <i class="fas fa-user-circle"></i>
                    {{ auth()->guard('admin')->user()->company_name }}
                </span>
                <form method="POST" action="{{ route('admin.logout.route') }}">
                    @csrf
                    <button type="submit" class="btn btn-logout" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff !important; border-radius: 8px; padding: 0.45rem 1rem; font-size: 0.85rem; font-weight: 500;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    @if(session('success'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <div class="container py-4">
        <div class="card">
            <div class="card-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-invoice me-2"></i>
                    <- Payment Details -> Reservation Ref Code : {{ $reservation->ref_code }}
                </h5>
            </div>

            <div class="card-body">
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
                    <h6 class="border-bottom pb-2">Payment Slips</h6>
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

                <!-- Footer Actions -->
                <div class="d-flex justify-content-between border-top pt-4">
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
                        <form id="reject-reservation-form-{{ $reservation->id }}" action="{{ route('admin.reservation.reject', $reservation) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="button" class="btn btn-danger" onclick="rejectReservationWithReason({{ $reservation->id }})">
                                <i class="fas fa-times-circle me-2"></i>Reject Reservation
                            </button>
                        </form>
                        @endif
                    </div>
                    <div>
                        <a href="{{ route('admin.dashboard.route') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function rejectReservationWithReason(reservationId) {
            const reason = prompt('Please enter the reason for rejecting this reservation:');
            if (reason !== null && reason.trim() !== '') {
                const form = document.getElementById('reject-reservation-form-' + reservationId);
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'rejection_reason';
                input.value = reason.trim();
                form.appendChild(input);
                form.submit();
            } else if (reason !== null) {
                alert('Rejection reason is required.');
            }
        }
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Slip Verification - Hall Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .card {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .card-header {
            border-bottom: none;
        }
        .slip-image {
            max-height: 400px;
            object-fit: contain;
        }
        .badge {
            padding: 0.5rem 0.75rem;
            font-weight: 500;
            border-radius: 6px;
            font-size: 0.85rem;
        }
        dl.row {
            margin-bottom: 0;
        }
        dl.row dt {
            font-weight: 600;
            color: #495057;
        }
        dl.row dd {
            font-weight: 500;
        }
        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #dee2e6;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('admin.dashboard.route') }}">
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
            <div class="card-header bg-primary text-white">
                <h2 class="h4 mb-0">
                    <i class="fas fa-file-invoice me-2"></i>
                    Payment Slip Verification - {{ $reservation->hall_name }}
                </h2>
            </div>

            <div class="card-body">
                <!-- Reservation Details -->
                <div class="mb-4">
                    <h5 class="section-title"><i class="fas fa-info-circle me-2"></i>Reservation Details</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Customer:</dt>
                                <dd class="col-sm-8">{{ $reservation->customer_name }}</dd>

                                <dt class="col-sm-4">Email:</dt>
                                <dd class="col-sm-8">{{ $reservation->customer_email }}</dd>

                                <dt class="col-sm-4">Phone:</dt>
                                <dd class="col-sm-8">{{ $reservation->customer_tel }}</dd>
                            </dl>
                        </div>

                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Hall Name:</dt>
                                <dd class="col-sm-8">{{ $reservation->hall_name }}</dd>

                                <dt class="col-sm-4">Date:</dt>
                                <dd class="col-sm-8">{{ date('M d, Y', strtotime($reservation->reservation_date)) }}</dd>

                                <dt class="col-sm-4">Time Slot:</dt>
                                <dd class="col-sm-8">
                                    {{ date('h:i A', strtotime($reservation->start_time)) }} -
                                    {{ date('h:i A', strtotime($reservation->end_time)) }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                @php
                    $totalPaidSlip = $reservation->payments->sum('amount');
                    $remainingSlip = ($reservation->charge + $reservation->deposit) - $totalPaidSlip;
                @endphp

                <!-- Financial Details -->
                <div class="mb-4">
                    <h5 class="section-title"><i class="fas fa-money-bill-wave me-2"></i>Financial Details</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Total Charge:</dt>
                                <dd class="col-sm-8 fw-bold">Rs. {{ number_format($reservation->charge, 2) }}</dd>

                                <dt class="col-sm-4">Advance Amount:</dt>
                                <dd class="col-sm-8 fw-bold">Rs. {{ number_format($reservation->advanceAmount, 2) }}</dd>

                                <dt class="col-sm-4">Advance Paid?</dt>
                                <dd class="col-sm-8">
                                    @if($reservation->advancePaid)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-warning text-dark">No</span>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Deposit:</dt>
                                <dd class="col-sm-8 fw-bold">Rs. {{ number_format($reservation->deposit, 2) }}</dd>

                                <dt class="col-sm-4">Total Paid:</dt>
                                <dd class="col-sm-8 fw-bold text-success">Rs. {{ number_format($totalPaidSlip, 2) }}</dd>

                                <dt class="col-sm-4">Remaining:</dt>
                                <dd class="col-sm-8 fw-bold text-danger">Rs. {{ number_format($remainingSlip, 2) }}</dd>

                                @if($reservation->discount_custom)
                                <dt class="col-sm-4">Discount (Custom):</dt>
                                <dd class="col-sm-8 text-info">Rs. {{ number_format($reservation->discount_custom, 2) }}</dd>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Payment Slips Display -->
                <div class="mb-4">
                    <h5 class="section-title"><i class="fas fa-receipt me-2"></i>Payment Slips</h5>
                    <div class="text-center bg-light p-3 rounded">
                        @php $paymentCount = $reservation->payments->count(); @endphp
                        @if($paymentCount > 0)
                            @foreach($reservation->payments as $index => $payment)
                                <div class="mb-3">
                                    <h6 class="text-muted">
                                        Payment #{{ $index + 1 }}
                                        @if($payment->payment_alias)
                                            <span class="badge bg-info ms-2">{{ $payment->payment_alias }}</span>
                                        @endif
                                        <span class="badge bg-secondary ms-1">Rs. {{ number_format($payment->amount, 2) }}</span>
                                    </h6>
                                    @if(\Illuminate\Support\Str::endsWith($payment->receipt_path, '.pdf'))
                                        <iframe src="{{ asset('storage/' . $payment->receipt_path) }}" width="100%" height="400px"
                                            class="border">
                                        </iframe>
                                    @else
                                        <img src="{{ asset('storage/' . $payment->receipt_path) }}" alt="Payment Slip #{{ $index + 1 }}"
                                            class="img-fluid slip-image">
                                    @endif
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

                <!-- Verification Actions -->
                <div class="d-flex justify-content-between align-items-center border-top pt-4">
                    <a href="{{ route('admin.dashboard.route') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                    </a>

                    <div class="d-flex gap-2">
                        {{-- Accept Advance Payment Button (intermediate step) --}}
                        <form action="{{ route('admin.slip.acceptAdvance', $reservation) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-info text-white"
                                @if(!$reservation->accepted || $reservation->advance_accepted !== null) disabled @endif>
                                <i class="fas fa-check me-2"></i>
                                @if($reservation->advance_accepted === true)
                                    Advance Accepted
                                @elseif($reservation->advance_accepted === false)
                                    Advance Rejected
                                @elseif(!$reservation->accepted)
                                    Accept Request First
                                @else
                                    Accept Advance Payment
                                @endif
                            </button>
                        </form>

                        {{-- Final Accept Full Payment Button (active only after advance is accepted) --}}
                        <form action="{{ route('admin.slip.accept', $reservation) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success"
                                @if(!$reservation->accepted || $reservation->advance_accepted !== true || ($reservation->accepted && $reservation->reserved !== null)) disabled @endif>
                                <i class="fas fa-check-circle me-2"></i>
                                {{ $reservation->accepted && $reservation->reserved == !null && $reservation->reserved ? 'Fully Paid' : 'Accept Full Payment' }}
                            </button>
                        </form>

                        {{-- Reject Payment Button --}}
                        <form action="{{ route('admin.slip.reject', $reservation) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-danger"
                                @if(!$reservation->accepted || ($reservation->accepted && $reservation->reserved !== null)) disabled @endif>
                                <i class="fas fa-times-circle me-2"></i>
                                {{ $reservation->accepted && $reservation->reserved !== null && !$reservation->reserved ? 'Already rejected' : 'Reject Payment' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payment - Hall Booking System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        .bank-details dt {
            font-weight: 500;
        }
        .payment-upload {
            border-top: 2px solid #eee;
            padding-top: 2rem;
            margin-top: 2rem;
        }
        .card-header {
            border-radius: 0.5rem 0.5rem 0 0 !important;
        }
        .badge {
            padding: 0.5rem 0.75rem;
            font-weight: 500;
            border-radius: 6px;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('load_venues_page') }}">
                <i class="fas fa-building"></i> Hall Booking System
            </a>
            <div class="d-flex align-items-center">
                <span class="me-3">
                    {{ auth()->guard('customer')->user()->first_name }}
                    {{ auth()->guard('customer')->user()->last_name }}
                </span>
                <form method="POST" action="{{ route('logout_route') }}" class="mb-0">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fas fa-sign-out-alt"></i>Logout
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

    <div class="container py-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0"><i class="fas fa-money-check-alt me-2"></i>Upload payment slip as a proof</h3>
            </div>

            <div class="card-body">
                @php
                    // Calculate total amount paid for this reservation
                    $totalPaid = $reservation->payments->sum('amount');
                    $preliminaryPayment = $reservation->advanceAmount;
                    $remainingAmount = max(0, (($reservation->charge - ($reservation->discount_custom ?? 0)) + $reservation->deposit) - $totalPaid);
                @endphp

                <!-- Reservation Details -->
                <div class="mb-5">
                    <h4 class="mb-4"><i class="fas fa-calendar-alt me-2"></i>Reservation Details</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Hall Name:</dt>
                                <dd class="col-sm-8">{{ $reservation->hall->name }}</dd>

                                <dt class="col-sm-4">Date:</dt>
                                <dd class="col-sm-8">{{ $reservation->reservation_date }}</dd>

                                <dt class="col-sm-4">Time Slot:</dt>
                                <dd class="col-sm-8">{{ $reservation->start_time }} - {{ $reservation->end_time }}</dd>
                            </dl>
                        </div>

                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Charge :</dt>
                                <dd class="col-sm-8">Rs. {{ number_format($reservation->charge, 2) }}</dd>

                                <dt class="col-sm-4">Deposit :</dt>
                                <dd class="col-sm-8">Rs. {{ number_format($reservation->deposit, 2) }}</dd>

                                <dt class="col-sm-4">Advance :</dt>
                                <dd class="col-sm-8">Rs. {{ number_format($reservation->advanceAmount, 2) }}</dd>

                                @if($reservation->discount_custom !== null)
                                <dt class="col-sm-4">Discount :</dt>
                                <dd class="col-sm-8">Rs. {{ number_format($reservation->discount_custom, 2) }}</dd>
                                @endif

                                <dt class="col-sm-4">Payment Status:</dt>
                                <dd class="col-sm-8">
                                    @if ($reservation->logged && (!$reservation->advancePaid || $preliminaryPayment > $totalPaid))
                                        <span class="badge bg-danger">Pay Advance : Rs.{{ number_format($reservation->advanceAmount, 2) }}</span>
                                    @elseif ($reservation->logged && $reservation->advancePaid && $totalPaid < $reservation->charge)
                                        <span class="badge bg-danger">Pay Remaining (including Deposit) : Rs.{{ number_format($remainingAmount, 2) }}/=</span>
                                    @else
                                        <span class="badge bg-success">Fully paid</span>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Bank Transfer Instructions -->
                <div class="mb-5">
                    <h4 class="mb-4"><i class="fas fa-university me-2"></i>Bank Transfer Details</h4>
                    <div class="alert alert-info">
                        <p class="mb-3">Please transfer the exact amount to the following bank account and upload your payment receipt below:</p>

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

                <!-- Payment Upload Section -->
                <div class="payment-upload">
                    <h4 class="mb-4"><i class="fas fa-receipt me-2"></i>Upload Payment Receipt</h4>

                    <form action="{{ route('payment.submit', $reservation->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="receipt" class="form-label">Upload Bank Receipt (PDF or Image)</label>
                            <input type="file" class="form-control" id="receipt" name="receipt"
                                   accept=".pdf,.jpg,.jpeg,.png" required>
                            <div class="form-text">Max file size: 5MB</div>
                        </div>

                        @if ($totalPaid >= 0 && $totalPaid < $preliminaryPayment)
                            <input type="hidden" name="payment_alias" value="Preliminary">
                            <input type="hidden" name="amount" value="{{ number_format($preliminaryPayment, 2) }}">
                        @elseif($totalPaid >= $preliminaryPayment && $totalPaid <= $reservation->charge)
                            <input type="hidden" name="payment_alias" value="Remainings">
                            <input type="hidden" name="amount" value="{{ number_format($remainingAmount, 2) }}">
                        @endif

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg"
                                @if(($totalPaid >= $reservation->charge) || $reservation->accepted == false || ($reservation->advancePaid && !$reservation->advance_accepted)) disabled @endif>
                                @if ($reservation->accepted)
                                    @if ($reservation->logged && (!$reservation->advancePaid || $preliminaryPayment > $totalPaid))
                                    <i class="fas fa-check-circle me-2"></i>Pay Advance amount Rs. {{ number_format($preliminaryPayment, 2) }} to get admin approval
                                    @elseif ($reservation->logged && $reservation->advancePaid && $totalPaid < $reservation->charge && $reservation->advance_accepted)
                                    <i class="fas fa-check-circle me-2"></i>Pay Remaining (including Deposit) Rs. {{ number_format($remainingAmount, 2) }}
                                    @elseif ($reservation->logged && $reservation->advancePaid && !$reservation->advance_accepted)
                                    <i class="fas fa-check-circle me-2"></i>Awaiting admin approval for advance payment
                                    @else
                                    <i class="fas fa-check-circle me-2"></i>Fully Paid
                                    @endif
                                @else
                                    <i class="fas fa-check-circle me-2"></i>Your request is under review. Please wait for admin approval to make the payment.
                                @endif
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

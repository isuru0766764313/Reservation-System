<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hall Booking - Customer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: var(--secondary);
            border: none;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .section-title {
            border-bottom: 2px solid var(--secondary);
            padding-bottom: 10px;
            margin-bottom: 20px;
            color: var(--dark);
        }

        .facility-item {
            padding: 10px 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            background-color: var(--light);
        }

        .price-display {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--accent);
        }

        .hidden {
            display: none;
        }

        .package-card {
            cursor: pointer;
            border: 2px solid #ddd;
            transition: all 0.3s ease;
        }

        .package-card:hover {
            border-color: var(--secondary);
        }

        .package-card.selected {
            border-color: var(--secondary);
            background-color: rgba(52, 152, 219, 0.1);
        }

        .package-features {
            list-style-type: none;
            padding: 0;
        }

        .package-features li {
            padding: 5px 0;
        }

        .package-features li:before {
            content: "✓ ";
            color: var(--secondary);
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Customer Section -->
        <section id="customer-section" class="mb-5">
            <h2 class="section-title"><i class="bi bi-person"></i> Customer Panel - Book a Hall</h2>

            <!-- Charge Method Selection -->
            <div class="card mt-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Booking Method</h5>
                </div>
                <div class="card-body">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="bookingMethod" id="regularBooking"
                            value="regular" checked>
                        <label class="form-check-label" for="regularBooking">
                            Regular Booking
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="bookingMethod" id="packageBooking"
                            value="package">
                        <label class="form-check-label" for="packageBooking">
                            Package Booking
                        </label>
                    </div>
                </div>
            </div>

            <!-- Regular Booking Section -->
            <div id="regularBookingSection">

                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Fixed Price Facilities (Rs.)</h5>
                    </div>
                    <div class="card-body">                        
                        @foreach ($hall->fixedfacilities as $fp_facility)
                            <div class="facility-item">
                                <div class="form-check">
                                    <input class="form-check-input facility-checkbox" type="checkbox" value="100"
                                        id="wifiFacility">
                                    <label class="form-check-label" for="wifiFacility">
                                        {{$fp_facility->name}} - {{ $fp_facility->charge }}
                                    </label>
                                </div>
                            </div>                        
                        @endforeach                        
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Unit Price Facilities (Per Hour - Rs.)</h5>
                    </div>
                    <div class="card-body">                        
                        @foreach ($hall->unitfacilities as $up_facility)
                            <div class="facility-item">
                                <div class="form-check">
                                    <input class="form-check-input unit-facility" type="checkbox" value="20"
                                        id="generatorFacility" data-price="20">
                                    <label class="form-check-label" for="generatorFacility">
                                        {{$up_facility->name}} - {{ $up_facility->charge }}
                                    </label>
                                </div>
                            </div>                        
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- Package Booking Section -->
            <div id="packageBookingSection" class="hidden">
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Select a Package</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($hall->packages as $package)
                                <div class="col-md-4 mb-3">
                                    <div class="card package-card h-100">
                                        <div class="card-body">
                                            <h1 class="card-title">{{ $package->name }}</h1>
                                            <p class="card-text">{{ $package->description }}</p>

                                            <h6 class="card-title"><u><strong>Fixed Priced Facilities</strong></u></h6>

                                            <!-- Fixed Price Facilities -->
                                             <ul class="package-features">
                                                @foreach($package->getFixedFacilitiesAttribute() as $facility)
                                                    <div class="my-2">
                                                        <li>{{ $facility->name }}</li>
                                                    </div>
                                                @endforeach
                                             </ul>


                                            <h6 class="card-title"><u><strong>Unit Priced Facilities</strong></u> (Per unit)</h6>

                                            <!-- Unit Price Facilities -->
                                            <ul class="package-features">
                                                @foreach($package->getUnitFacilitiesAttribute() as $facility)
                                                    <div class="my-2">
                                                        <li>{{ $facility->name }}</li>
                                                    </div>
                                                @endforeach
                                            </ul>

                                        </div>
                                        <div class="card-footer text-center">
                                            <h3 class="card-subtitle mb-2 text-muted">Rs. {{ number_format($package->price, 2) }}</h3>
                                            <span class="text-muted">Up to {{ $package->duration }} hours</span><br>
                                            <span class="text-muted">Charge per extended hour : 
                                                Rs. {{ intval($package->maximum_hours) }} /hr</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>           





            </div>


            <!-- Price Calculation -->
            <div class="card mt-4">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">Price Calculation</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p>Base Charge: <span id="baseCharge">Rs. 0.00</span></p>
                            <p>Facilities Charge: <span id="facilitiesCharge">Rs. 0.00</span></p>
                            <!-- Extended Hours Charge removed -->
                        </div>
                        <div class="col-md-6 text-end">
                            <p class="price-display">Total: <span id="totalCharge">Rs. 0.00</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Reservation -->
            <div class="mt-4 text-center">
                <button type="button" class="btn btn-success btn-lg">
                    <i class="bi bi-check-circle"></i> Submit Reservation
                </button>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
                // Customer panel functionality
                const regularBookingSection = document.getElementById('regularBookingSection');
                const packageBookingSection = document.getElementById('packageBookingSection');

                document.querySelectorAll('input[name="bookingMethod"]').forEach(radio => {
                    radio.addEventListener('change', function () {
                        if (this.value === 'regular') {
                            regularBookingSection.classList.remove('hidden');
                            packageBookingSection.classList.add('hidden');
                        } else {
                            regularBookingSection.classList.add('hidden');
                            packageBookingSection.classList.remove('hidden');
                        }
                        calculateTotal();
                    });
                });

                // Package selection
                document.querySelectorAll('.package-card').forEach(card => {
                    card.addEventListener('click', function () {
                        // Remove selected class from all cards
                        document.querySelectorAll('.package-card').forEach(c => {
                            c.classList.remove('selected');
                        });

                        // Add selected class to clicked card
                        this.classList.add('selected');

                        calculateTotal();
                    });
                });

                // Add event listeners for calculation
                document.querySelectorAll('.facility-checkbox, .unit-facility').forEach(element => {
                    element.addEventListener('change', calculateTotal);
                });

                // Initial calculation
                calculateTotal();
            });

            function calculateTotal() {
                let baseCharge = 0;
                let facilitiesCharge = 0;

                // Check which booking method is selected
                const isRegular = document.getElementById('regularBooking').checked;

                if (isRegular) {
                    // REGULAR BOOKING LOGIC
                    const hourlyRate = 50; // You might want to get this from your hall data
                    const hours = 4; // Default hours for calculation

                    baseCharge = hours * hourlyRate;

                    // Calculate facilities charge for regular booking
                    document.querySelectorAll('.facility-checkbox:checked').forEach(checkbox => {
                        facilitiesCharge += parseFloat(checkbox.value);
                    });

                    // Unit facilities are charged per hour
                    document.querySelectorAll('.unit-facility:checked').forEach(checkbox => {
                        const pricePerHour = parseFloat(checkbox.dataset.price);
                        facilitiesCharge += hours * pricePerHour;
                    });
                } else {
                    // PACKAGE BOOKING LOGIC
                    const selectedPackage = document.querySelector('.package-card.selected');

                    if (selectedPackage) {
                        // Get the package price from the displayed text
                        const priceText = selectedPackage.querySelector('.card-subtitle').textContent;
                        const priceMatch = priceText.match(/Rs\. ([\d,]+\.\d{2})/);

                        if (priceMatch) {
                            // Remove commas and convert to number
                            baseCharge = parseFloat(priceMatch[1].replace(/,/g, ''));
                        }

                        // In package mode, facilities charge is 0 (included in package price)
                        facilitiesCharge = 0;
                    }
                }

                // Calculate total
                const total = baseCharge + facilitiesCharge;

                // Update display
                document.getElementById('baseCharge').textContent = `Rs. ${baseCharge.toFixed(2)}`;
                document.getElementById('facilitiesCharge').textContent = `Rs. ${facilitiesCharge.toFixed(2)}`;
                document.getElementById('totalCharge').textContent = `Rs. ${total.toFixed(2)}`;
            }
    </script>
</body>

</html>
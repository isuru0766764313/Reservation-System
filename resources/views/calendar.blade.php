<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hall Calendar - {{ $hall->name }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/index.global.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.8/index.global.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body { 
            padding-top: 20px; 
            background: #f8f9fa; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .hall-info { margin-bottom: 1rem; }
        #calendar { 
            background: #fff; 
            padding: 1.5rem; 
            border-radius: 8px; 
            box-shadow: 0 2px 12px rgba(0,0,0,0.08); 
        }
        
        /* Make calendar dates look like buttons with colored borders */
        .fc-daygrid-day {
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .fc-daygrid-day-frame {
            transition: all 0.2s ease;
        }
        
        /* Available - Green border */
        .fc-daygrid-day.day-available {
            border: 3px solid #28a745 !important;
            background-color: rgba(40, 167, 69, 0.05) !important;
        }
        .fc-daygrid-day.day-available .fc-daygrid-day-number {
            color: #28a745 !important;
            font-weight: bold;
            font-size: 1.1em;
        }
        .fc-daygrid-day.day-available:hover {
            background-color: rgba(40, 167, 69, 0.15) !important;
            border-color: #218838 !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }
        
        /* Partially Available - Yellow/Orange border */
        .fc-daygrid-day.day-partially {
            border: 3px solid #ffc107 !important;
            background-color: rgba(255, 193, 7, 0.05) !important;
        }
        .fc-daygrid-day.day-partially .fc-daygrid-day-number {
            color: #ff8800 !important;
            font-weight: bold;
            font-size: 1.1em;
        }
        .fc-daygrid-day.day-partially:hover {
            background-color: rgba(255, 193, 7, 0.15) !important;
            border-color: #e0a800 !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
        }
        
        /* Unavailable - Red border */
        .fc-daygrid-day.day-unavailable {
            border: 3px solid #dc3545 !important;
            background-color: rgba(220, 53, 69, 0.05) !important;
            cursor: not-allowed;
        }
        .fc-daygrid-day.day-unavailable .fc-daygrid-day-number {
            color: #dc3545 !important;
            font-weight: bold;
            font-size: 1.1em;
        }
        .fc-daygrid-day.day-unavailable:hover {
            background-color: rgba(220, 53, 69, 0.1) !important;
            transform: none;
        }
        
        /* Past dates - gray with lighter border */
        .fc-daygrid-day.fc-day-past:not(.day-available):not(.day-partially):not(.day-unavailable) {
            background-color: #f8f9fa !important;
            cursor: not-allowed;
            opacity: 0.5;
        }
        .fc-daygrid-day.fc-day-past .fc-daygrid-day-number {
            color: #adb5bd !important;
        }
        
        /* Today highlight */
        .fc-daygrid-day.fc-day-today {
            background-color: rgba(13, 110, 253, 0.05) !important;
        }
        .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
            background-color: #0d6efd;
            color: white !important;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .fc .fc-button-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        
        .legend-box {
            display: inline-block;
            width: 16px;
            height: 16px;
            margin-right: 8px;
            border-radius: 3px;
            vertical-align: middle;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="fas fa-calendar-alt me-2"></i>Select Date — {{ $hall->name }}</h3>
        <div>
            <a href="{{ route('load_venues_page') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back to Venues</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="card hall-info p-3 mb-3 shadow-sm">
                <h5 class="mb-2">{{ $hall->name }}</h5>
                <p class="mb-2 text-muted"><i class="fas fa-tag me-2"></i>{{ ucfirst($hall->type) }}</p>
                <p class="mb-2 text-muted"><i class="fas fa-users me-2"></i>Capacity: {{ number_format($hall->capacity) }}</p>
                <p class="mb-0 text-primary fw-bold"><i class="fas fa-rupee-sign me-2"></i>LKR {{ number_format($hall->price, 2) }} / hr</p>
            </div>

            <div class="card p-3 shadow-sm">
                <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i>Availability Legend</h6>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <span class="legend-box" style="background:#28a745;"></span>
                        <strong>Available</strong> - Fully open
                    </li>
                    <li class="mb-2">
                        <span class="legend-box" style="background:#ffc107;"></span>
                        <strong>Partially</strong> - Some slots taken
                    </li>
                    <li class="mb-3">
                        <span class="legend-box" style="background:#dc3545;"></span>
                        <strong>Unavailable</strong> - Fully booked
                    </li>
                    <li class="small text-muted">
                        <i class="fas fa-hand-pointer me-2"></i>Click any available date to proceed to booking page where you can select time.
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-md-9">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@6.1.8/index.global.min.js"></script>

<script>
    (function() {
        const hallId = "{{ $hall->id }}";
        const calendarEl = document.getElementById('calendar');

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: { 
                left: 'prev,next today', 
                center: 'title', 
                right: '' // Remove view switcher - month only
            },
            navLinks: false,
            selectable: false,
            nowIndicator: true,
            fixedWeekCount: false,
            height: 'auto',
            datesSet: function(dateInfo) {
                // When calendar view changes (month navigation), fetch availability
                const start = dateInfo.startStr.split('T')[0];
                const end = dateInfo.endStr.split('T')[0];
                
                fetch(`/customer/hall/availability-range?hall_id=${hallId}&start=${start}&end=${end}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Store availability data for coloring
                        const dayData = {};
                        if (Array.isArray(data)) {
                            data.forEach(event => {
                                if (event.display === 'background' && event.color) {
                                    const date = event.start;
                                    // Map color codes to our availability classes
                                    if (event.color === '#28a745') {
                                        dayData[date] = 'available'; // Green - fully available
                                    } else if (event.color === '#ffc107') {
                                        dayData[date] = 'partially'; // Yellow - partially booked
                                    } else if (event.color === '#dc3545') {
                                        dayData[date] = 'unavailable'; // Red - fully booked
                                    }
                                }
                            });
                        }
                        
                        // Apply colors to calendar cells
                        calendar.availabilityData = dayData;
                        
                        // Update all visible day cells
                        document.querySelectorAll('.fc-daygrid-day').forEach(dayEl => {
                            const dateStr = dayEl.getAttribute('data-date');
                            if (dateStr && dayData[dateStr]) {
                                // Remove old classes
                                dayEl.classList.remove('day-available', 'day-partially', 'day-unavailable');
                                // Add new class
                                dayEl.classList.add('day-' + dayData[dateStr]);
                            }
                        });
                    })
                    .catch(err => {
                        console.error('Calendar fetch error:', err);
                        alert('Failed to load availability. Please refresh the page.');
                    });
            },
            dayCellDidMount: function(info) {
                // Apply colors when cells are mounted
                const dateStr = info.date.toISOString().split('T')[0];
                const availability = calendar.availabilityData && calendar.availabilityData[dateStr];
                
                if (availability) {
                    info.el.classList.add('day-' + availability);
                }
            },
            dateClick: function(info) {
                const dateStr = info.dateStr;
                const dateObj = new Date(dateStr);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                // Don't allow clicking past dates
                if (dateObj < today) {
                    return;
                }
                
                // Check if day is fully unavailable
                const availability = calendar.availabilityData && calendar.availabilityData[dateStr];
                if (availability === 'unavailable') {
                    alert('This date is fully booked. Please select another date.');
                    return;
                }
                
                // Redirect to booking page with selected date
                window.location.href = `/customer/halls/${hallId}?selectedDate=${encodeURIComponent(dateStr)}`;
            }
        });

        calendar.render();
    })();
</script>

</body>
</html>
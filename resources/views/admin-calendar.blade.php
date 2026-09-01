<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Calendar - Reservations</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/index.global.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.8/index.global.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/dist/tippy.css" />
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/themes/light-border.css" />
    
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .calendar-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            margin-top: 2rem;
        }
        .fc-event {
            cursor: pointer;
            border-radius: 4px;
            padding: 2px 4px;
        }
        .fc-event:hover {
            opacity: 0.8;
            transform: scale(1.02);
        }
        .legend-item {
            display: inline-flex;
            align-items: center;
            margin-right: 20px;
            margin-bottom: 10px;
        }
        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            margin-right: 8px;
        }
        .hall-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.85em;
            font-weight: 500;
            margin-right: 8px;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <h2><i class="fas fa-calendar-alt me-3"></i>Reservations Calendar</h2>
                    <a href="{{ route('admin.dashboard.route') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-9">
                <div class="calendar-container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <label for="hallFilter" class="form-label me-2 fw-bold">Filter by Hall:</label>
                            <select id="hallFilter" class="form-select d-inline-block" style="width: 250px;">
                                <option value="all">All Halls</option>
                                @foreach($halls as $hall)
                                    <option value="{{ $hall->id }}">{{ $hall->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <button id="refreshBtn" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-sync-alt me-2"></i>Refresh
                            </button>
                        </div>
                    </div>
                    <div id="adminCalendar"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Legend</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="mb-3">Your Halls:</h6>
                        @foreach($halls as $hall)
                            <div class="hall-badge" style="background-color: {{ $hall->calendar_color ?? '#' . substr(md5($hall->id), 0, 6) }}; color: white;">
                                {{ $hall->name }}
                            </div>
                        @endforeach
                        
                        <hr>
                        
                        <h6 class="mb-3">Booking Status:</h6>
                        <div class="legend-item">
                            <div class="legend-color" style="background-color: rgba(40, 167, 69, 0.8);"></div>
                            <span>Confirmed</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background-color: rgba(255, 193, 7, 0.8);"></div>
                            <span>Pending</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background-color: rgba(220, 53, 69, 0.8);"></div>
                            <span>Rejected</span>
                        </div>
                        
                        <hr>
                        
                        <div class="small text-muted">
                            <p><i class="fas fa-hand-pointer me-2"></i>Click on any event to view full details</p>
                            <p><i class="fas fa-mouse me-2"></i>Hover over events to see quick info</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reservation Details Modal -->
    <div class="modal fade" id="reservationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Reservation Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="reservationDetails">
                    <!-- Details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@6.1.8/index.global.min.js"></script>
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>

    <script>
        const adminId = {{ $admin->id }};
        let calendar = null;

        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('adminCalendar');

            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: ''
                },
                height: 'auto',
                events: function(fetchInfo, successCallback, failureCallback) {
                    const hallId = document.getElementById('hallFilter').value;
                    const start = fetchInfo.startStr.split('T')[0];
                    const end = fetchInfo.endStr.split('T')[0];
                    
                    let url = `/admin/calendar/events?admin_id=${adminId}&start=${start}&end=${end}`;
                    if (hallId !== 'all') {
                        url += `&hall_id=${hallId}`;
                    }

                    fetch(url)
                        .then(response => response.json())
                        .then(data => successCallback(data))
                        .catch(err => {
                            console.error('Calendar error:', err);
                            failureCallback(err);
                        });
                },
                eventDidMount: function(info) {
                    // Add tooltip on hover
                    const props = info.event.extendedProps;
                    const tooltip = `
                        <div class="p-2">
                            <strong>${info.event.title}</strong><br>
                            <strong>Customer:</strong> ${props.customer_name || 'N/A'}<br>
                            <strong>Hall:</strong> ${props.hall_name}<br>
                            <strong>Time:</strong> ${props.time_slot}<br>
                            <strong>Status:</strong> <span class="badge bg-${props.status_class}">${props.status}</span>
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
                eventClick: function(info) {
                    // Show detailed modal
                    const props = info.event.extendedProps;
                    const detailsHtml = `
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Booking Information</h6>
                                <p><strong>Reservation ID:</strong> #${props.reservation_id}</p>
                                <p><strong>Hall:</strong> ${props.hall_name}</p>
                                <p><strong>Date:</strong> ${props.reservation_date}</p>
                                <p><strong>Time:</strong> ${props.time_slot}</p>
                                <p><strong>Status:</strong> <span class="badge bg-${props.status_class}">${props.status}</span></p>
                            </div>
                            <div class="col-md-6">
                                <h6>Customer Information</h6>
                                <p><strong>Name:</strong> ${props.customer_name}</p>
                                <p><strong>Email:</strong> ${props.customer_email || 'N/A'}</p>
                                <p><strong>Phone:</strong> ${props.customer_phone || 'N/A'}</p>
                            </div>
                        </div>
                    `;
                    
                    document.getElementById('reservationDetails').innerHTML = detailsHtml;
                    new bootstrap.Modal(document.getElementById('reservationModal')).show();
                }
            });

            calendar.render();

            // Filter change
            document.getElementById('hallFilter').addEventListener('change', function() {
                calendar.refetchEvents();
            });

            // Refresh button
            document.getElementById('refreshBtn').addEventListener('click', function() {
                calendar.refetchEvents();
            });
        });
    </script>
</body>
</html>

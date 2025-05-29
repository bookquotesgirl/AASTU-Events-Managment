<?php
include 'php/db_connect.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Total Events
$totalEvents = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM events"))['total'];

// Upcoming Events (events today or later)
$today = date('Y-m-d');
$upcomingEvents = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS upcoming FROM events WHERE start_date >= '$today'"))['upcoming'];

// Total Bookings
$totalBookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM bookings"))['total'];

// Get upcoming events list (limit to 5)
$eventsList = mysqli_query($conn, "SELECT e.id, e.name, e.start_date, e.venue,
    (SELECT COUNT(*) FROM bookings WHERE event_id = e.id) AS attendees
    FROM events e
    ORDER BY start_date ASC");

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AASTU EVENTS - Admin Panel</title> 
    <link rel="icon" href="favicon-16x16.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body> 
    <div class="container">
        <button id="menu-toggle" class="menu-toggle" aria-label="Toggle menu">
            <i class="fas fa-bars"></i>
        </button>
        
        <nav class="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-user-shield"></i> Admin Panel</h2>
            </div>
            <ul>
                <li><a href="#dashboard" class="menu-link active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="#add-event" class="menu-link"><i class="fas fa-calendar-plus"></i> Add Event</a></li>
                <li><a href="#view-bookings" class="menu-link"><i class="fas fa-ticket-alt"></i> View Bookings</a></li>
                <li class="logout-link"><a href="#logout" class="menu-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
        
        <div class="main-content">
            <!-- Breadcrumbs -->
            <div class="breadcrumbs">
                <span>Admin Panel</span> / <span class="current-page">Dashboard</span>
            </div>
            
            <!-- Dashboard Section -->
            <section id="dashboard" class="content-section active">
                <h1><i class="fas fa-tachometer-alt"></i> Dashboard Overview</h1>
                
                <!-- Stats Cards -->
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-icon blue">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Events</h3>
                            <p class="stat-value"><?php echo $totalEvents; ?></p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon green">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Upcoming Events</h3>
                            <p class="stat-value"><?php echo $upcomingEvents; ?></p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-card orange">
    <div class="stat-icon">
        <i class="fas fa-ticket-alt"></i>
    </div>
    <div class="stat-info">
        <h3>Total Bookings</h3>
        <p class="stat-value"><?php echo $totalBookings; ?></p>
    </div>
</div>

                    </div>
                </div>
                
                <div class="dashboard-row">
                    <div class="dashboard-col">
                        <h2><i class="fas fa-calendar-day"></i> Upcoming Events</h2>
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Event ID</th>
                                        <th>Event Name</th>
                                        <th>Date</th>
                                        <th>Venue</th>
                                        <th>Attendees</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
    <?php while ($row = mysqli_fetch_assoc($eventsList)): ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['name']); ?></td>
        <td><?php echo date('m/d/y', strtotime($row['start_date'])); ?></td>
        <td><?php echo htmlspecialchars($row['venue']); ?></td>
        <td><?php echo $row['attendees']; ?></td>
        <td>
            <a href="php/edit_event.php?id=<?php echo $row['id']; ?>" class="btn-edit">Edit</a>
            <a href="php/delete_events.php?id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Are you sureyou want to delete this event?');">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</tbody>

                            </table>
                        </div>
                    </div>
                    
                    
            </section>

           

<!--  Event Section -->
<section id="add-event" class="content-section">
    <div class="section-header">
        <h1><i class="fas fa-calendar-plus"></i> Create New Event</h1>
        <div class="section-actions">
            <button class="btn-secondary" id="save-draft">Save Draft</button>
            <button class="btn-primary" id="publish-event">Publish Event</button>
        </div>
    </div>
    
    <div class="form-stepper">
        <div class="stepper-progress">
            <div class="step active" data-step="1">Basic Info</div>
            <div class="step" data-step="2">Details</div>
            <div class="step" data-step="3">Tickets</div>
            <div class="step" data-step="4">Publish</div>
        </div>
    </div>
    
    <form id="event-form" class="event-form" method="POST" action="php/add_event.php" enctype="multipart/form-data">

        <!-- Step 1: Basic Info -->
        <div class="form-step active" data-step="1">
            <div class="form-group">
                <label for="event-name">Event Name *</label>
                <input type="text" id="event-name" name="event_name" placeholder="Give it a short, distinct name" required>
            </div>

            

            <div class="form-group">
                <label for="short-description">Short Description *</label>
                <textarea id="short-description"name="short_description" placeholder="Brief description that will appear in listings" required></textarea>
                <small class="char-count"><span id="desc-count">0</span>/160 characters</small>
            </div>

            <div class="form-group">
                <label>Event Image *</label>
                <div class="image-upload-container">
                    <div class="upload-box" id="uploadBox">
                        <input type="file" id="imageUpload" name="event_image" accept="image/*" required>
                        <div class="upload-content">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Click to upload or drag and drop</p>
                            <small>Recommended size: 1200x630px (Max 5MB)</small>
                        </div>
                    </div>
                    <div class="image-preview" id="imagePreview"></div>
                </div>
            </div>
            
            <div class="form-navigation">
                <button type="button" class="btn-next">Next <i class="fas fa-arrow-right"></i></button>
            </div>
        </div>

        <!-- Step 2: Details -->
        <div class="form-step" data-step="2">
            <div class="form-row">
                <div class="form-group">
                    <label for="start-date">Start Date *</label>
                    <input type="date" id="start-date" name="start_date" required>
                </div>
                <div class="form-group">
                    <label for="start-time">Start Time *</label>
                    <input type="time" name="start_time" id="start-time" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="end-date">End Date</label>
                    <input type="date" id="end-date" name="end_date">
                </div>
                <div class="form-group">
                    <label for="end-time">End Time</label>
                    <input type="time" id="end-time" name="end_time">
                </div>
            </div>

            <div class="form-group">
                <label>Event Type *</label>
                <div class="radio-group">
                    <label class="radio-option">
                        <input type="radio" name="event_type" value="physical" checked>
                        <div class="radio-card">
                            <i class="fas fa-building"></i>
                            <h4>Physical Event</h4>
                            <p>Held at a specific location</p>
                        </div>
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="event_type" value="online">
                        <div class="radio-card">
                            <i class="fas fa-globe"></i>
                            <h4>Online Event</h4>
                            <p>Virtual event with online access</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="form-group location-group" id="physical-location">
                <label for="location">Location *</label>
                <input type="text" name="location" id="location" placeholder="Search for venue or address">
                <div class="map-preview" id="map-preview"></div>
            </div>

            <div class="form-group location-group" id="online-details" style="display:none;">
                <label for="online-url">Online Event Details *</label>
                <input type="url" name="online_url" id="online-url" placeholder="https://example.com/event">
                <small>Zoom, YouTube, or other streaming link</small>
            </div>

            <div class="form-navigation">
                <button type="button" class="btn-prev"><i class="fas fa-arrow-left"></i> Previous</button>
                <button type="button" class="btn-next">Next <i class="fas fa-arrow-right"></i></button>
            </div>
        </div>

        <!-- Step 3: Tickets -->
        <div class="form-step" data-step="3">
            <div class="form-group">
                <label>Registration Type *</label>
                <div class="radio-group">
                    <label class="radio-option">
                        <input type="radio" name="registration_type" value="free" checked>
                        <div class="radio-card">
                            <i class="fas fa-ticket-alt"></i>
                            <h4>Free Registration</h4>
                            <p>No payment required</p>
                        </div>
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="registration_type" value="paid">
                        <div class="radio-card">
                            <i class="fas fa-money-bill-wave"></i>
                            <h4>Paid Tickets</h4>
                            <p>Set prices for attendance</p>
                        </div>
                    </label>
                </div>
            </div>

            <div id="ticket-options" style="display:none;">
                <div class="ticket-header">
                    <h3><i class="fas fa-ticket-alt"></i> Ticket Options</h3>
                    <button type="button" class="btn-secondary" id="add-ticket">
                        <i class="fas fa-plus"></i> Add Ticket
                    </button>
                </div>
                
                <div class="ticket-list" id="ticket-list">
                    <!-- Tickets will be added here dynamically -->
                </div>
            </div>

            <div class="form-group">
                <label for="capacity">Attendance Capacity</label>
                <input type="number" name="capacity" id="capacity" placeholder="Leave empty for unlimited">
            </div>

            <div class="form-group">
                <label for="registration-deadline">Registration Deadline</label>
                <input type="datetime-local" name="registration_deadline" id="registration-deadline">
            </div>

            <div class="form-navigation">
                <button type="button" class="btn-prev"><i class="fas fa-arrow-left"></i> Previous</button>
                <button type="button" class="btn-next">Next <i class="fas fa-arrow-right"></i></button>
            </div>
        </div>

        <!-- Step 4: Publish -->
        <div class="form-step" data-step="4">
            <div class="form-group">
                <label for="full-description">Full Description</label>
                <div id="editor" class="rich-text-editor"></div>
            </div>

            <div class="form-group">
                <label>Additional Options</label>
                <div class="checkbox-group">
                    <label class="checkbox-option">
                        <input type="checkbox" name="send_notifications" id="send-notifications">
                        <span class="checkmark"></span>
                        Notify subscribers about this event
                    </label>
                    <label class="checkbox-option">
                        <input type="checkbox" name="featured_event" id="featured-event">
                        <span class="checkmark"></span>
                        Feature this event on homepage
                    </label>
                    <label class="checkbox-option">
                        <input type="checkbox" name="require_approval" id="require-approval">
                        <span class="checkmark"></span>
                        Manually approve registrations
                    </label>
                </div>
            </div>

            <div class="publish-actions">
                <button type="button" class="btn-prev"><i class="fas fa-arrow-left"></i> Previous</button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-check"></i> Publish Event
                </button>
            </div>
        </div>
    </form>
</section>



<!-- View Bookings Section -->
<section id="view-bookings" class="content-section">
    <div class="section-header">
        <h1><i class="fas fa-ticket-alt"></i> Event Bookings</h1>
        
    </div>

    <div class="filter-bar">
        <div class="filter-group">
            <label for="event-select">Event:</label>
            <select id="event-select">
                
            </select>
        </div>
        <div class="filter-group">
            <label for="status-filter">Status:</label>
            <select id="status-filter">
                <option value="all">All Statuses</option>
                <option value="confirmed">Confirmed</option>
                <option value="pending">Pending</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
        <div class="filter-group">
            <label for="date-filter">Date Range:</label>
            <input type="date" id="date-from">
            <span>to</span>
            <input type="date" id="date-to">
        </div>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Event</th>
                    <th>Attendee</th>
                    <th>Ticket Type</th>
                    <th>Booking Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="booking-list">
                
            </tbody>
        </table>
    </div>

    <div class="table-footer">
        <div class="table-info">
            Showing 1 to 2 of 15 bookings
        </div>
        <div class="pagination">
            <button class="page-btn disabled"><i class="fas fa-angle-left"></i></button>
            <button class="page-btn active">1</button>
            <button class="page-btn">2</button>
            <button class="page-btn">3</button>
            <button class="page-btn"><i class="fas fa-angle-right"></i></button>
        </div>
    </div>
</section>

<!-- Logout Section -->
<section id="logout" class="content-section">
    <form action="php/logout.php" method="post">
  <button type="submit">Logout</button>
</form>


</section>
            
        </div>
    </div>

    

     <script src="js/admin.js"></script>
         </body>
       </html>
# Group members and user names
1. Hermela Ashagre (hermi-2525)
2. Hermela Asmamaw(Hermelasis)
3. Hermela Mulugeta(123hermela123)
4. Hewan Solomon(evana2905)
5. Hiwot Abere(bookquotesgirl)

# AASTU Events Management System
This web-based application allows users to browse, book, and manage events at AASTU. It also provides a separate admin panel to manage events and view bookings.

# AASTU Events Management - Database Schema 

This project uses a MySQL database named `aastuevent` to manage event bookings at AASTU. Below is an overview of the database structure and relationships to help developers and collaborators understand how data is stored and accessed.

---

## Database Name: `aastuevent`

### Tables & Descriptions

---

### 1. `users`
Stores user account information.

| Column     | Type      | Description                     |
|------------|-----------|---------------------------------|
| `id`       | INT, PK   | Unique user ID                  |
| `username` | VARCHAR   | User's name                     |
| `email`    | VARCHAR, Unique | User's email address     |
| `password` | VARCHAR   | Hashed password                 |
| `phone`    | VARCHAR   | Optional phone number           |
| `role`     | ENUM('user') | User role, default is 'user' |

**Indexes**:
- `PRIMARY (id)`
- `email` (Unique)

---

### 2. `events`
Stores information about each event.

| Column       | Type      | Description                      |
|--------------|-----------|----------------------------------|
| `id`         | INT, PK   | Unique event ID                  |
| `name`       | VARCHAR   | Event name                       |
| `category`   | VARCHAR   | Event category                   |
| `event_type` | ENUM      | 'physical' or 'virtual'          |
| `venue`      | VARCHAR   | Location of event                |
| `start_date` | DATE      | Start date of the event          |
| `start_time` | TIME      | Start time of the event          |
| `end_date`   | DATE      | End date of the event            |
| `description`| TEXT      | Description of the event         |
| `image`      | VARCHAR   | Event image path                 |
| `capacity`   | INT       | Max attendees                    |
| `status`     | ENUM      | 'draft', 'published', etc.       |
| `created_by` | INT, FK   | Reference to `users.id`          |
| `created_at` | TIMESTAMP | Timestamp of creation            |

**Indexes**:
- `PRIMARY (id)`
- `created_by` (FK)

---

### 3. `bookings`
Stores event booking records by users.

| Column        | Type      | Description                      |
|---------------|-----------|----------------------------------|
| `id`          | INT, PK   | Unique booking ID                |
| `event_id`    | INT, FK   | Reference to `events.id`         |
| `user_id`     | INT, FK   | Reference to `users.id`          |
| `status`      | ENUM      | 'pending', 'confirmed', etc.     |
| `booking_date`| TIMESTAMP | Time of booking                  |
| `created_at`  | TIMESTAMP | Record creation time             |
| `updated_at`  | TIMESTAMP | Record update time               |

**Indexes**:
- `PRIMARY (id)`
- `idx_bookings_event (event_id)`
- `idx_bookings_user (user_id)`
- `fk_booking_ticket` (foreign key to `booking_tickets`)

---

### 4. `booking_tickets`
Stores ticket details related to each booking.

| Column      | Type       | Description                    |
|-------------|------------|--------------------------------|
| `id`        | INT, PK    | Unique ID                      |
| `booking_id`| INT, FK    | Reference to `bookings.id`     |
| `ticket_id` | INT, FK    | Reference to `tickets.id`      |
| `quantity`  | INT        | Number of tickets booked       |
| `unit_price`| DECIMAL    | Price per ticket               |

**Indexes**:
- `PRIMARY (id)`
- `booking_id` (FK)
- `ticket_id` (FK)

---

### 5. `tickets`
Stores ticket types and prices for each event.

| Column      | Type     | Description                   |
|-------------|----------|-------------------------------|
| `id`        | INT, PK  | Unique ticket ID              |
| `event_id`  | INT, FK  | Reference to `events.id`      |
| `name`      | VARCHAR  | Ticket type name              |
| `description`| TEXT    | Optional ticket details       |
| `price`     | DECIMAL  | Price of the ticket           |
| `quantity`  | INT      | Number of tickets available   |

**Indexes**:
- `PRIMARY (id)`
- `event_id` (FK)

---

## Relationships

- A **user** can **book** many events (`users → bookings`).
- Each **event** can have multiple **tickets** (`events → tickets`).
- A **booking** can include multiple **ticket types** (`bookings → booking_tickets`).
- Each **ticket** in `booking_tickets` references a ticket from the `tickets` table.

---

## How to Set Up the Database

1. Open **phpMyAdmin**.
2. Create a new database: `aastuevent`.
3. Import the provided `.sql` file (if available) using the **Import** tab.
4. Ensure all foreign key relationships are respected.

---

## Tip

When cloning the project, make sure to:
- Use **XAMPP** or **another local server** to run your project.
- Configure database connection in `php/db_connect.php`.

---
 
# Sample Credentials
Use these accounts to test the application with different privileges.

Admin Access
Admins can:

Create, edit, and delete events

View all bookings

Manage event statistics

Field	Value
Username	admin
Email	admin@example.com
Phone	0912345678
Role	admin
Password	admin123

User Access
Users can:

View available events

Book tickets

Cancel their bookings

View their booking history

You can register a new user through the app interface, or manually add one to the database.
# How to Run
Place the project folder in your htdocs directory (e.g., C:\xampp\htdocs\AASTU-Events-Managment)

Start Apache and MySQL via XAMPP.

Access the app via your browser:

http://localhost/IPII/AASTU-Events-Managment/home.html
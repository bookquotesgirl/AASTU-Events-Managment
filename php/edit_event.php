<?php
include 'db_connect.php';

if (!isset($_GET['id'])) {
    header('Location: /IPII/AASTU-Events-Managment/admin.php');
    exit;
}

$id = intval($_GET['id']);

// Fetch event details using prepared statement for security
$stmt = mysqli_prepare($conn, "SELECT * FROM events WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$event = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $start_date = $_POST['start_date'];
    $venue = mysqli_real_escape_string($conn, $_POST['venue']);
    
    // Use prepared statement for update
    $updateStmt = mysqli_prepare($conn, 
        "UPDATE events SET name=?, start_date=?, venue=? WHERE id=?");
    mysqli_stmt_bind_param($updateStmt, "sssi", $name, $start_date, $venue, $id);
    
    if (mysqli_stmt_execute($updateStmt)) {
        header('Location: /IPII/AASTU-Events-Managment/admin.php?msg=updated');
        exit;
    } else {
        $error = "Update failed: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - AASTU Events Management</title>
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --error-color: #e74c3c;
            --success-color: #2ecc71;
            --text-color: #333;
            --light-gray: #f5f5f5;
            --border-radius: 6px;
            --box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--light-gray);
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }
        
        h1 {
            color: var(--primary-color);
            margin-bottom: 20px;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-color);
        }
        
        input[type="text"],
        input[type="date"],
        input[type="email"],
        input[type="tel"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input:focus,
        select:focus,
        textarea:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
        
        button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: 600;
            border-radius: var(--border-radius);
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s;
        }
        
        button:hover {
            background-color: var(--secondary-color);
        }
        
        .error-message {
            color: var(--error-color);
            background-color: rgba(231, 76, 60, 0.1);
            padding: 12px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            border-left: 4px solid var(--error-color);
        }
        
        .success-message {
            color: var(--success-color);
            background-color: rgba(46, 204, 113, 0.1);
            padding: 12px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            border-left: 4px solid var(--success-color);
        }
        
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Event</h1>
        
        <?php if (isset($error)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
            <div class="success-message">
                Event updated successfully!
            </div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="name">Event Name</label>
                <input type="text" id="name" name="name" 
                       value="<?php echo htmlspecialchars($event['name'] ?? ''); ?>" 
                       required>
            </div>
            
            <div class="form-group">
                <label for="start_date">Start Date</label>
                <input type="date" id="start_date" name="start_date" 
                       value="<?php echo htmlspecialchars($event['start_date'] ?? ''); ?>" 
                       required>
            </div>
            
            <div class="form-group">
                <label for="venue">Venue</label>
                <input type="text" id="venue" name="venue" 
                       value="<?php echo htmlspecialchars($event['venue'] ?? ''); ?>" 
                       required>
            </div>
            
            <button type="submit">Update Event</button>
        </form>
        
        <a href="/IPII/AASTU-Events-Managment/admin.php" class="back-link">
            &larr; Back to Events List
        </a>
    </div>
</body>
</html>
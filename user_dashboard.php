<?php
require 'includes/auth.php';
include('includes/db_connect.php');

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

// Query to fetch user data for either client or worker
$query = $user_type == 'client' ? 
    "SELECT * FROM client WHERE id = '$user_id'" : 
    "SELECT * FROM worker WHERE id = '$user_id'";

$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// If user doesn't exist, redirect back to login page
if (!$user) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <h2>SEOMarketplace</h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="user_dashboard.php">Dashboard</a></li>
                <li><a href="profile.php">Profile</a></li>
                
                <!-- Show different menu options based on user type -->
                <?php if ($user_type == 'client') { ?>
                    <li><a href="post_job.php">Post Job</a></li>
                    <li><a href="view_jobs.php">View Posted Jobs</a></li>
                    <li><a href="applicants.php">View Applicants</a></li>
                <?php } elseif ($user_type == 'worker') { ?>
                    <li><a href="view_jobs.php">View Jobs</a></li>
                    <li><a href="application_status.php">Application Status</a></li> <!-- Added Application Status -->
                <?php } ?>
                
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <header class="main-header">
                <h1>Welcome, <?php echo htmlspecialchars($user['first_name']); ?>!</h1>
                <p>You're logged in as <?php echo ucfirst($user_type); ?>.</p>
            </header>

            <div class="dashboard-section">
                <h3>Dashboard Content</h3>
                <p>This is the main content area where users can interact with the dashboard.</p>
                
                <!-- Specific content for clients or workers -->
                <?php if ($user_type == 'client') { ?>
                    <p>As a client, you can post job requests and view the jobs you've posted.</p>
                <?php } elseif ($user_type == 'worker') { ?>
                    <p>As a worker, you can browse available jobs and submit applications.</p>
                <?php } ?>
            </div>
        </div>
    </div>
</body>
</html>
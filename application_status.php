<?php
require 'includes/auth.php';
include('includes/db_connect.php');

if ($_SESSION['user_type'] != 'worker') {
    header("Location: login.php");
    exit;
}

$worker_id = $_SESSION['user_id'];

// Fetch applications for the logged-in worker
$query = "SELECT a.id AS application_id, a.job_title, a.application_date, a.status, j.title AS job_name, c.first_name AS client_first_name, c.last_name AS client_last_name
          FROM applications a
          JOIN jobs j ON a.job_id = j.id
          JOIN client c ON j.client_id = c.id
          WHERE a.worker_id = ?
          ORDER BY a.application_date DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $worker_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$applications = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Status - SEOMarketplace</title>
    <style>
    body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

.navbar {
    background-color: #333;
    padding: 15px;
    color: white;
    text-align: center;
}

.main-content {
    width: 80%;
    margin: 20px auto;
    background: white;
    padding: 20px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
}

.applications-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.applications-table th, .applications-table td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: left;
}

.applications-table th {
    background-color: #f8f8f8;
}

.status {
    font-weight: bold;
    text-transform: capitalize;
}

.status.pending {
    color: orange;
}

.status.accepted {
    color: green;
}

.status.rejected {
    color: red;
}

.cta-btn {
    display: inline-block;
    padding: 10px 15px;
    margin-top: 20px;
    background-color: #333;
    color: white;
    text-decoration: none;
    border-radius: 5px;
}

.cta-btn:hover {
    background-color: #555;
}
</style>
</head>
<body>
    <header class="navbar">
        <div class="logo"><h1>SEOMarketplace</h1></div>
    </header>
    <main class="main">
        <div class="main-content">
            <h2>Your Job Applications</h2>
            <?php if (empty($applications)): ?>
                <div class="no-applications">
                    <h3>You have not applied for any jobs yet.</h3>
                </div>
            <?php else: ?>
                <table class="applications-table">
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Client Name</th>
                            <th>Application Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $application): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($application['job_name']); ?></td>
                                <td><?php echo htmlspecialchars($application['client_first_name'] . ' ' . $application['client_last_name']); ?></td>
                                <td><?php echo htmlspecialchars($application['application_date']); ?></td>
                                <td class="status <?php echo $application['status']; ?>">
                                    <?php echo ucfirst($application['status']); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            <a href="user_dashboard.php" class="cta-btn back-btn">Back to Dashboard</a>
        </div>
    </main>
</body>
</html>

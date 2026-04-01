<?php
require 'includes/auth.php';
include('includes/db_connect.php');

if ($_SESSION['user_type'] != 'client') {
    header("Location: login.php");
    exit;
}

$client_id = $_SESSION['user_id'];

// Handle AJAX request to update application status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id'], $_POST['status'])) {
    $application_id = $_POST['application_id'];
    $status = $_POST['status'];
    
    if (!in_array($status, ['accepted', 'rejected'])) {
        echo json_encode(["success" => false, "message" => "Invalid status."]);
        exit;
    }

    $query = "UPDATE applications a JOIN jobs j ON a.job_id = j.id SET a.status = ? WHERE a.id = ? AND j.client_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sii", $status, $application_id, $client_id);
    $result = mysqli_stmt_execute($stmt);

    echo json_encode(["success" => $result, "message" => $result ? "Application status updated." : "Failed to update."]);
    exit;
}

// Fetch applications for jobs posted by the client
$query = "SELECT a.id AS application_id, a.worker_id, a.worker_email, a.job_title, a.application_date, a.status, w.first_name, w.last_name, w.phone
          FROM applications a
          JOIN jobs j ON a.job_id = j.id
          JOIN worker w ON a.worker_id = w.id
          WHERE j.client_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $client_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$applications = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Applicants - SEOMarketplace</title>
    <link rel="stylesheet" href="css/view_applicants.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header class="navbar">
        <div class="logo"><h1>SEOMarketplace</h1></div>
    </header>
    <main class="main">
        <div class="main-content">
            <h2>Job Applications</h2>
            <?php if (empty($applications)): ?>
                <div class="no-applications">
                    <h3>No applications available for your jobs at the moment.</h3>
                </div>
            <?php else: ?>
                <table class="applications-table">
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Worker Name</th>
                            <th>Worker Email</th>
                            <th>Phone</th>
                            <th>Application Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $application): ?>
                            <tr id="app-<?php echo $application['application_id']; ?>">
                                <td><?php echo htmlspecialchars($application['job_title']); ?></td>
                                <td><?php echo htmlspecialchars($application['first_name'] . ' ' . $application['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($application['worker_email']); ?></td>
                                <td><?php echo htmlspecialchars($application['phone']); ?></td>
                                <td><?php echo htmlspecialchars($application['application_date']); ?></td>
                                <td id="status-<?php echo $application['application_id']; ?>">
                                    <?php echo ucfirst($application['status']); ?>
                                </td>
                                <td>
                                    <?php if ($application['status'] == 'pending'): ?>
                                        <button onclick="updateStatus(<?php echo $application['application_id']; ?>, 'accepted')">Accept</button>
                                        <button onclick="updateStatus(<?php echo $application['application_id']; ?>, 'rejected')">Reject</button>
                                    <?php else: ?>
                                        <span><?php echo ucfirst($application['status']); ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            <a href="user_dashboard.php" class="cta-btn back-btn">Back to Dashboard</a>
        </div>
    </main>
    <script>
        function updateStatus(applicationId, status) {
            $.post("view_applicants.php", { application_id: applicationId, status: status }, function(response) {
                let res = JSON.parse(response);
                if (res.success) {
                    $("#status-" + applicationId).text(status.charAt(0).toUpperCase() + status.slice(1));
                    $("#app-" + applicationId + " button").remove();
                } else {
                    alert(res.message);
                }
            });
        }
    </script>
</body>
</html>
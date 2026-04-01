<?php
require 'includes/auth.php';
include('includes/db_connect.php');

if ($_SESSION['user_type'] != 'client') {
    header("Location: login.php");
    exit;
}

$client_id = $_SESSION['user_id'];

// Query to fetch applications for the jobs posted by the client
$query = "
    SELECT 
        applications.id AS application_id,
        jobs.title AS job_title,
        applications.worker_email,
        applications.application_date,
        worker.first_name AS worker_first_name,
        worker.last_name AS worker_last_name,
        worker.phone AS worker_phone
    FROM 
        applications
    INNER JOIN 
        jobs ON applications.job_id = jobs.id
    INNER JOIN 
        worker ON applications.worker_id = worker.id
    WHERE 
        jobs.client_id = '$client_id'
    ORDER BY 
        applications.application_date DESC
";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

// Fetch all applications as an array
$applications = [];
while ($row = mysqli_fetch_assoc($result)) {
    $applications[] = $row;
}

// Store the applications data in session for use in view_applicants.php
$_SESSION['applications'] = $applications;

// Redirect to the view applicants page
header("Location: view_applicants.php");
exit;
?>

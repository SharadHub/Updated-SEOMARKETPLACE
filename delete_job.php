<?php
require 'includes/auth.php';
include('includes/db_connect.php');

if ($_SESSION['user_type'] != 'client') {
    header("Location: login.php");
    exit;
}

$client_id = $_SESSION['user_id'];

if (!isset($_GET['job_id'])) {
    header("Location: user_dashboard.php");
    exit;
}

$job_id = intval($_GET['job_id']);

// Verify ownership using prepared statement
$stmt = mysqli_prepare($conn, "SELECT id FROM jobs WHERE id = ? AND client_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $job_id, $client_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header("Location: user_dashboard.php");
    exit;
}

// Delete applications first
$stmt = mysqli_prepare($conn, "DELETE FROM applications WHERE job_id = ?");
mysqli_stmt_bind_param($stmt, "i", $job_id);
mysqli_stmt_execute($stmt);

// Delete job
$stmt = mysqli_prepare($conn, "DELETE FROM jobs WHERE id = ? AND client_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $job_id, $client_id);

if (mysqli_stmt_execute($stmt)) {
    header("Location: user_dashboard.php");
    exit;
} else {
    die("Error deleting job: " . mysqli_error($conn));
}
?>

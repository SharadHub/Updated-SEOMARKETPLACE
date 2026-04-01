<?php
require 'includes/auth.php';
include('includes/db_connect.php');

if ($_SESSION['user_type'] != 'worker') {
    header("Location: login.php");
    exit;
}

$worker_id = $_SESSION['user_id'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Handle job application
if ($action == 'apply' && isset($_GET['job_id'])) {
    $job_id = $_GET['job_id'];

    // Fetch job details for insertion
    $job_query = "SELECT title FROM jobs WHERE id = '$job_id'";
    $job_result = mysqli_query($conn, $job_query);
    
    if (mysqli_num_rows($job_result) == 0) {
        $_SESSION['message'] = "The job you are trying to apply for does not exist.";
        $_SESSION['message_type'] = "error";
        header("Location: view_jobs.php");
        exit;
    }
    
    $job_row = mysqli_fetch_assoc($job_result);
    $job_title = $job_row['title'];

    // Fetch worker email
    $worker_query = "SELECT email FROM worker WHERE id = '$worker_id'";
    $worker_result = mysqli_query($conn, $worker_query);
    
    if (mysqli_num_rows($worker_result) == 0) {
        $_SESSION['message'] = "Worker information could not be retrieved.";
        $_SESSION['message_type'] = "error";
        header("Location: view_jobs.php");
        exit;
    }

    $worker_row = mysqli_fetch_assoc($worker_result);
    $worker_email = $worker_row['email'];

    // Check if the worker has already applied for this job
    $check_query = "SELECT * FROM applications WHERE job_id = '$job_id' AND worker_id = '$worker_id'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['message'] = "You have already applied for this job.";
        $_SESSION['message_type'] = "error";
        header("Location: view_jobs.php");
        exit;
    }

    // Insert application into the applications table
    $query = "INSERT INTO applications (job_id, worker_id, worker_email, job_title, application_date) 
              VALUES ('$job_id', '$worker_id', '$worker_email', '$job_title', NOW())";

    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = "You have successfully applied for the job!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error applying for the job: " . mysqli_error($conn);
        $_SESSION['message_type'] = "error";
    }

    header("Location: view_jobs.php");
    exit;
}

// Handle application cancellation
elseif ($action == 'cancel' && isset($_GET['job_id'])) {
    $job_id = $_GET['job_id'];
    $worker_id = $_SESSION['user_id'];

    // Delete the application
    $query = "DELETE FROM applications WHERE job_id = '$job_id' AND worker_id = '$worker_id'";

    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = "Your application has been successfully cancelled.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error cancelling application: " . mysqli_error($conn);
        $_SESSION['message_type'] = "error";
    }

    header("Location: view_jobs.php");
    exit;
}

// If no valid action is specified
else {
    $_SESSION['message'] = "Invalid request. Please specify a valid action.";
    $_SESSION['message_type'] = "error";
    header("Location: view_jobs.php");
    exit;
}
?>
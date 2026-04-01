<?php 
// Include database connection file
include('includes/db_connect.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$client_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

// Handle job deletion via AJAX (for clients)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['job_id']) && $user_type == 'client') {
    $job_id = $_POST['job_id'];
    $query = "DELETE FROM jobs WHERE id = ? AND client_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $job_id, $client_id);
    $result = mysqli_stmt_execute($stmt);
    
    echo json_encode(["success" => $result, "message" => $result ? "Job deleted successfully!" : "Failed to delete job."]);
    exit;
}

// Fetch jobs based on user type
if ($user_type == 'client') {
    $query = "SELECT id, title, job_type, description, requirements FROM jobs WHERE client_id = ?";
} else {
    $query = "SELECT id, title, job_type, description, requirements FROM jobs";
}

$stmt = mysqli_prepare($conn, $query);

// Bind parameters for clients
if ($user_type == 'client') {
    mysqli_stmt_bind_param($stmt, "i", $client_id);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Fetch applied jobs for workers
$applied_jobs = [];
if ($user_type == 'worker') {
    $query = "SELECT job_id FROM applications WHERE worker_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $client_id);
    mysqli_stmt_execute($stmt);
    $result_applied = mysqli_stmt_get_result($stmt);
    
    while ($row = mysqli_fetch_assoc($result_applied)) {
        $applied_jobs[] = $row['job_id'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Jobs - SEOMarketplace</title>
    <link rel="stylesheet" href="css/view_jobs.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .job-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        .edit-btn {
            background-color: #4CAF50;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
            border: none;
        }
        .edit-btn:hover {
            background-color: #45a049;
        }
        .applied-btn {
            background-color: #808080;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: not-allowed;
            border: none;
        }
    </style>
    
<style>
    .error-message {
        background-color: #ffdddd;
        color: #ff0000;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ff0000;
        border-radius: 5px;
        text-align: center;
    }
    
    .success-message {
        background-color: #ddffdd;
        color: #008800;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #008800;
        border-radius: 5px;
        text-align: center;
    }
</style>
</head>
<body>
<?php if (isset($_SESSION['message'])): ?>
    <div id="message" class="<?php echo isset($_SESSION['message_type']) && $_SESSION['message_type'] == 'error' ? 'error-message' : 'success-message'; ?>">
        <?php 
            echo $_SESSION['message']; 
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        ?>
    </div>
    <script>
        // Make the message disappear after 2 seconds
        setTimeout(function() {
            document.getElementById('message').style.display = 'none';
        }, 2000);
    </script>
<?php endif; ?>

    <header class="navbar">
        <div class="logo"><h1>SEOMarketplace</h1></div>
    </header>
    <main class="main">
        <div class="main-content">
            <h2>Available Jobs</h2>
            <?php if (mysqli_num_rows($result) == 0): ?>
                <div class="no-jobs">
                    <h3><?php echo ($user_type == 'client') ? "You have not posted a job yet." : "No jobs available at the moment."; ?></h3>
                </div>
            <?php else: ?>
                <div class="job-grid">
                    <?php while ($job = mysqli_fetch_assoc($result)): ?>
                        <div class="job-item" id="job-<?php echo $job['id']; ?>">
                            <!-- <div> -->
                            <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                            <p><strong>Type:</strong> <?php echo htmlspecialchars($job['job_type']); ?></p>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($job['description']); ?></p>
                            <p><strong>Requirements:</strong> <?php echo htmlspecialchars($job['requirements']); ?></p>
<!-- </div> -->
                            <?php if ($user_type == 'client'): ?>
                                <div class="job-actions">
                                    <a href="post_job.php?edit=<?php echo $job['id']; ?>" class="cta-btn edit-btn">Edit</a>
                                    <button class="cta-btn delete-btn" onclick="deleteJob(<?php echo $job['id']; ?>)">Delete</button>
                                </div>
                                <?php elseif ($user_type == 'worker'): ?>
                                    <?php if (in_array($job['id'], $applied_jobs)): ?>
                                    <div class="job-actions">
                                        <button class="cta-btn applied-btn" disabled>Already Applied</button>
                                        <button class="cta-btn delete-btn" onclick="cancelApplication(<?php echo $job['id']; ?>)">Cancel Application</button>
                                    </div>
                                    <?php else: ?>
                                        <button class="cta-btn apply-btn" onclick="applyForJob(<?php echo $job['id']; ?>)">Apply Now</button>
                                    <?php endif; ?>
                                <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
            <a href="user_dashboard.php" class="cta-btn back-btn">Back to Dashboard</a>
        </div>
    </main>

    <script>
        function deleteJob(jobId) {
            if (confirm("Are you sure you want to delete this job?")) {
                $.ajax({
                    url: "view_jobs.php",
                    type: "POST",
                    data: { job_id: jobId },
                    success: function(response) {
                        let res = JSON.parse(response);
                        if (res.success) {
                            $("#job-" + jobId).fadeOut("slow", function() {
                                $(this).remove();
                            });
                        } else {
                            alert(res.message);
                        }
                    },
                    error: function() {
                        alert("Failed to delete the job. Please try again.");
                    }
                });
            }
        }

        function applyForJob(jobId) {
    window.location.href = "apply_job.php?action=apply&job_id=" + jobId;
}
function cancelApplication(jobId) {
    if(confirm("Are you sure you want to cancel your application for this job?")) {
        window.location.href = "apply_job.php?action=cancel&job_id=" + jobId;
    }
}
    </script>
    
</body>
</html>
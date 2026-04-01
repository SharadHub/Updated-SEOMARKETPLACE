<?php
// Include database connection
include('includes/db_connect.php');

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password']; // User's entered password

    // Fetch hashed passwords from both tables securely
    $clientQuery = "SELECT id, password FROM client WHERE email = ?";
    $workerQuery = "SELECT id, password FROM worker WHERE email = ?";

    // Prepare and execute for client
    $clientStmt = mysqli_prepare($conn, $clientQuery);
    mysqli_stmt_bind_param($clientStmt, "s", $email);
    mysqli_stmt_execute($clientStmt);
    mysqli_stmt_store_result($clientStmt);

    $clientId = null;
    $clientHashedPassword = null;

    if (mysqli_stmt_num_rows($clientStmt) > 0) {
        mysqli_stmt_bind_result($clientStmt, $clientId, $clientHashedPassword);
        mysqli_stmt_fetch($clientStmt);
    }
    mysqli_stmt_close($clientStmt);

    // Prepare and execute for worker
    $workerStmt = mysqli_prepare($conn, $workerQuery);
    mysqli_stmt_bind_param($workerStmt, "s", $email);
    mysqli_stmt_execute($workerStmt);
    mysqli_stmt_store_result($workerStmt);

    $workerId = null;
    $workerHashedPassword = null;

    if (mysqli_stmt_num_rows($workerStmt) > 0) {
        mysqli_stmt_bind_result($workerStmt, $workerId, $workerHashedPassword);
        mysqli_stmt_fetch($workerStmt);
    }
    mysqli_stmt_close($workerStmt);

    // Check if user exists and verify password
    $isClient = $clientId && password_verify($password, $clientHashedPassword);
    $isWorker = $workerId && password_verify($password, $workerHashedPassword);

    if ($isClient && $isWorker) {
        // User exists in both tables - store in session temporarily
        $_SESSION['email'] = $email;

        // Redirect to role selection page
        header("Location: role_selection.php");
        exit();
    } elseif ($isClient) {
        // Login as client
        $_SESSION['user_id'] = $clientId;
        $_SESSION['user_type'] = 'client';
        header("Location: user_dashboard.php");
        exit();
    } elseif ($isWorker) {
        // Login as worker
        $_SESSION['user_id'] = $workerId;
        $_SESSION['user_type'] = 'worker';
        header("Location: user_dashboard.php");
        exit();
    } else {
        // Invalid credentials
        $error = "Invalid email or password.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SEOMarketplace</title>
    <style>
        /* General styles for the page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            position: relative;
        }

        header {
            width: 100%;
            background-color: #333;
            color: white;
            padding: 20px 0;
            text-align: center;
            position: absolute;
            top: 0;
        }

        header h1 {
            font-size: 2em;
            margin: 0;
        }

        /* Back to home button on the top-right corner */
        .back-to-home {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }

        .back-to-home:hover {
            background-color: #0056b3;
        }

        /* Main content and form styling */
        .login-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            margin: 20px;
            z-index: 1; /* Ensure the form is above header */
        }

        h2 {
            text-align: center;
            font-size: 1.5em;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input[type="email"], input[type="password"] {
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Style for error message */
        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }

        .cta-btn {
            display: inline-block;
            text-align: center;
            background-color: #007bff;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        .cta-btn:hover {
            background-color: #0056b3;
        }

        /* Media Queries for responsiveness */
        @media (max-width: 768px) {
            .login-container {
                padding: 20px;
                max-width: 90%;
            }

            header h1 {
                font-size: 1.5em;
            }

            h2 {
                font-size: 1.2em;
            }

            input[type="email"], input[type="password"] {
                padding: 12px;
            }

            button {
                padding: 14px;
            }
        }
    </style>
    
</head>
<body>
    <a href="index.html" class="back-to-home">Back to Home</a>

    <div class="login-container">
        <h2>SEOMarketplace Login</h2>

        <?php if (isset($error)) { echo "<p class='error-message'>$error</p>"; } ?>

        <form method="POST" action="login.php">
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
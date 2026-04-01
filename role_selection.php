<?php
session_start();
if (!isset($_SESSION['email']) || !isset($_SESSION['password'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include('includes/db_connect.php');

    $email = $_SESSION['email'];
    $password = $_SESSION['password'];
    $selectedRole = $_POST['role'];

    if ($selectedRole == "client") {
        $query = "SELECT id FROM client WHERE email = '$email' AND password = '$password'";
    } else {
        $query = "SELECT id FROM worker WHERE email = '$email' AND password = '$password'";
    }

    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = $selectedRole;
        header("Location: user_dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Role</title>
</head>
<body>
    <style>
        /* Apply styles to the entire page */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

/* Style the form container */
.container {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    width: 300px;
}

/* Style the heading */
h2 {
    margin-bottom: 20px;
    color: #333;
}

/* Style the radio buttons */
input[type="radio"] {
    margin-right: 10px;
}

/* Style the buttons */
button {
    margin-top: 15px;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background 0.3s;
}

/* Continue button */
button[type="submit"] {
    background-color: #28a745;
    color: white;
}
button[type="submit"]:hover {
    background-color: #218838;
}

/* Back button */
.back-button {
    display: block;
    background-color: #dc3545;
    color: white;
    text-decoration: none;
    padding: 10px 15px;
    border-radius: 5px;
    margin-top: 10px;
    transition: background 0.3s;
}
.back-button:hover {
    background-color: #c82333;
}
    </style>
    <h2>Select Your Role</h2>
    <form method="POST">
        <input type="radio" name="role" value="client" required> Client<br>
        <input type="radio" name="role" value="worker" required> Worker<br>
        <button type="submit">Continue</button>
    </form>
</body>
</html>
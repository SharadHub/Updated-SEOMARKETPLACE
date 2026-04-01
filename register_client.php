<?php
// db_connect.php includes the database connection
include('includes/db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture the form data
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $phone = trim($_POST['phone']);
    $gender = $_POST['gender'];

   // Phone validation (97 or 98 followed by 8 digits)
   if (!preg_match('/^(97|98)\d{8}$/', $phone)) {
    echo json_encode(["status" => "error", "message" => "Invalid phone number. Must start with 97/98 and be 10 digits."]);
    exit;
}

// Email validation (Strict domain check)
if (!preg_match('/^[a-zA-Z0-9._%+-]+@(gmail\.com|email\.com|hotmail\.com|([a-zA-Z0-9.-]+\.(gov|edu|org|net)))$/', $email)) {
    echo json_encode(["status" => "error", "message" => "Invalid email"]);
    exit;
}

// Check existing email
$emailCheck = $conn->prepare("SELECT id FROM client WHERE email = ?");
$emailCheck->bind_param("s", $email);
$emailCheck->execute();
if ($emailCheck->get_result()->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Email already registered."]);
    exit;
}

// Check existing phone
$phoneCheck = $conn->prepare("SELECT id FROM client WHERE phone = ?");
$phoneCheck->bind_param("s", $phone);
$phoneCheck->execute();
if ($phoneCheck->get_result()->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Phone number already registered."]);
    exit;
}


    // Hash the password before storing
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email or phone number already exists
    $checkQuery = "SELECT id FROM client WHERE email = ? OR phone = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ss", $email, $phone);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Email or phone number already registered."]);
        exit;
    }
    $stmt->close();

    // Insert the data if email and phone are unique
    $query = "INSERT INTO client (first_name, last_name, gender, email, password, phone) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssss", $firstName, $lastName, $gender, $email, $hashed_password, $phone);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Registration successful. Redirecting to login..."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register as Client</title>
    <style>
        /* Basic Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            display: flex;
            width: 100%;
            max-width: 800px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .brand {
            background-color:rgb(0, 0, 255);
            color: white;
            padding: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex: 1;
        }

        .brand h1 {
            font-size: 32px;
            font-weight: bold;
        }

        .form-section {
            flex: 2;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        input[type="text"], input[type="email"], input[type="password"], input[type="tel"], select {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
            background-color: #f9f9f9;
        }

        select {
            font-size: 14px;
        }

        button.cta-btn {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button.cta-btn:hover {
            background-color: #45a049;
        }

        a.cta-btn {
            background-color: #f44336;
            color: white;
            padding: 12px;
            text-decoration: none;
            text-align: center;
            border-radius: 8px;
            font-size: 16px;
            margin-top: 10px;
        }

        a.cta-btn:hover {
            background-color: #e53935;
        }

        /* Responsive Styles */
        @media screen and (max-width: 768px) {
            .container {
                flex-direction: column;
                max-width: 90%;
            }

            .brand {
                padding: 20px;
            }

            .brand h1 {
                font-size: 24px;
            }

            .form-section {
                padding: 20px;
            }
        }

        @media screen and (max-width: 480px) {
            input[type="text"], input[type="email"], input[type="password"], input[type="tel"], select {
                padding: 10px;
            }

            button.cta-btn, a.cta-btn {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Brand Section -->
        <div class="brand">
            <h1>SEOMarketplace</h1>
        </div>

        <!-- Form Section -->
        <div class="form-section">
            <h2>Register as Client</h2>
            <form method="POST" action="register_client.php" onsubmit="return validateForm()">
                <input type="text" name="first_name" placeholder="First Name" required>
                
                <input type="text" name="last_name" placeholder="Last Name" required>

                <input type="email" name="email" placeholder="Email" required>

                <div class="password-container" style="position: relative; width: 100%;">
                    <input type="password" name="password" id="password" placeholder="Password" required>
                    <button type="button" id="togglePassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); border: none; background: none; cursor: pointer;">
                    <span id="eyeIcon">👁️</span>
                 </button>
                </div>              
                <small style="color: #666; font-size: 12px;">Password must be 8-15 characters with at least one uppercase letter, one lowercase letter, one digit, and one special character.</small>
                
                <!-- Phone Number Input with Pattern -->
                <input type="tel" name="phone" id="phone" placeholder="Phone Number" 
                    pattern="98[0-9]{8}" title="Phone number must start with 98 and be exactly 10 digits" required>

                <!-- Gender Section (Dropdown) -->
                <select name="gender" required>
                    <option value="" disabled selected>Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>

                <button type="submit" class="cta-btn">Register</button>
            </form>
            <a href="index.html" class="cta-btn">Back</a>
        </div>
    </div>

    <script>
       function validateForm() {
    let firstName = document.forms["registerForm"]["first_name"].value.trim();
    let lastName = document.forms["registerForm"]["last_name"].value.trim();
    let email = document.forms["registerForm"]["email"].value.trim();
    let password = document.forms["registerForm"]["password"].value;
    let phone = document.forms["registerForm"]["phone"].value.trim();
    let gender = document.forms["registerForm"]["gender"].value;

    // Regex patterns
    let namePattern = /^[A-Za-z]+$/;
    // update email
    let emailPattern = /^[a-zA-Z0-9._%+-]+@(gmail\.com|email\.com)$/;
    let phonePattern = /^98^97[0-9]{8}$/;  
    let passwordPattern = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).{8,15}$/; 
    

    // Validate first name and last name
    if (!namePattern.test(firstName)) {
        alert("First name should contain only letters.");
        return false;
    }
    if (!namePattern.test(lastName)) {
        alert("Last name should contain only letters.");
        return false;
    }

    // Validate email format
    if (!emailPattern.test(email)) {
        alert("Please enter a valid email address with @gmail.com or @email.com domain.");
        return false;
    }

    // Validate password
    if (!passwordPattern.test(password)) {
        alert("Password must be 8-15 characters and contain at least:\n- One uppercase letter\n- One lowercase letter\n- One digit\n- One special character");
        return false;
    }

    // Validate phone number
    if (!phonePattern.test(phone)) {
        alert("Phone number must be exactly 10 digits and start with '98'.");
        return false;
    }

    // Validate gender selection
    if (gender === "") {
        alert("Please select your gender.");
        return false;
    }

    return true;
}
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        const toggleButton = document.getElementById('togglePassword');
        const eyeIcon = document.getElementById('eyeIcon');

        toggleButton.addEventListener('click', function() {
        // Toggle the type attribute
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Toggle the eye icon
        eyeIcon.textContent = type === 'password' ? '👁️' : '🔒';
    });
});
    </script>
    <script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelector("form").addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent form submission
        
        let formData = new FormData(this);
        let submitButton = document.querySelector(".cta-btn");
        let errorBox = document.createElement("div");

        // Remove previous error messages
        let existingError = document.getElementById("error-message");
        if (existingError) existingError.remove();

        errorBox.id = "error-message";
        errorBox.style.color = "red";
        errorBox.style.textAlign = "center";
        errorBox.style.marginBottom = "10px";

        // Disable button to prevent multiple clicks
        submitButton.disabled = true;
        submitButton.innerText = "Processing...";

        fetch("register_client.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            submitButton.disabled = false;
            submitButton.innerText = "Register";

            if (data.status === "error") {
                errorBox.innerText = data.message;
                document.querySelector("form").prepend(errorBox);
            } else {
                errorBox.style.color = "green";
                errorBox.innerText = data.message;
                document.querySelector("form").prepend(errorBox);
                
                setTimeout(() => {
                    window.location.href = "login.php"; // Redirect after success
                }, 2000);
            }
        })
        .catch(error => {
            errorBox.innerText = "An unexpected error occurred. Please try again.";
            document.querySelector("form").prepend(errorBox);
            submitButton.disabled = false;
            submitButton.innerText = "Register";
        });
    });
});
</script>

</body>
</html>
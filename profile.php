<?php
require 'includes/auth.php';
include('includes/db_connect.php');

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];
$query = $user_type == 'client' ? "SELECT * FROM client WHERE id = '$user_id'" : "SELECT * FROM worker WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);

if ($result) {
    $user = mysqli_fetch_assoc($result);
    if (!$user) {
        header("Location: login.php");
        exit;
    }
} else {
    die("Error fetching user data: " . mysqli_error($conn));
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $profile_image = $user['profile_image']; // Keep old image by default

    // Handle Image Upload
    if (!empty($_FILES['profile_image']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_name = basename($_FILES['profile_image']['name']);
        $target_file = $target_dir . time() . "_" . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                $profile_image = $target_file;
            } else {
                $error_message = "Error uploading file.";
            }
        } else {
            $error_message = "Invalid file type. Only JPG, PNG, and GIF are allowed.";
        }
    }

    // Update query
    $update_query = $user_type == 'client' ?
        "UPDATE client SET first_name = '$first_name', last_name = '$last_name', email = '$email', phone = '$phone', profile_image = '$profile_image' WHERE id = '$user_id'" :
        "UPDATE worker SET first_name = '$first_name', last_name = '$last_name', email = '$email', phone = '$phone', profile_image = '$profile_image' WHERE id = '$user_id'";

    if (mysqli_query($conn, $update_query)) {
        $success_message = "Profile updated successfully!";
        header("Refresh:0"); // Reload the page to show the new image
    } else {
        $error_message = "Error updating profile: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - SEOMarketplace</title>
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>
    <header class="navbar">
        <div class="logo"><h1>SEOMarketplace</h1></div>
    </header>
    <main class="main">
        <div class="main-content">
            <h2>Profile Settings</h2>
            
            <?php if (isset($success_message)): ?>
                <div class="message success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="message error">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <h3>Current Profile Details</h3>

            <!-- Profile Picture Display -->
            <div>
    <img id="profileImage" 
         src="<?php echo $user['profile_image'] ? $user['profile_image'] : 'uploads/default.png'; ?>"   
         alt="Profile Image" 
         width="150" height="150" 
         style="border-radius: 50%; display: none;">
</div>

<script>
    // Get the image element
    const profileImg = document.getElementById('profileImage');
    
    // Get the image source from the PHP-generated attribute
    const imgSrc = "<?php echo $user['profile_image'] ? $user['profile_image'] : 'uploads/default.png'; ?>";
    
    // Check if the image source is not the default one
    if (imgSrc && imgSrc !== 'uploads/default.png') {
        profileImg.style.display = 'block'; // Show the image if a custom photo exists
    } else {
        profileImg.style.display = 'none'; // Keep hidden if it's the default image
    }
</script>

            <div class="profile-details">
                <p><strong>First Name:</strong> <?php echo htmlspecialchars($user['first_name']); ?></p>
                <p><strong>Last Name:</strong> <?php echo htmlspecialchars($user['last_name']); ?></p>
                <p><strong>Email Address:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
            </div>

            <form method="POST" action="profile.php" id="editForm" class="edit-form" style="display: none;" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" name="first_name" id="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" name="last_name" id="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" name="phone" id="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                    </div>

                    <!-- Profile Image Upload -->
                    <div class="form-group">
                        <label for="profile_image">Profile Image</label>
                        <input type="file" name="profile_image" id="profile_image" accept="image/*">
                    </div>
                </div>

                <div class="buttons-container">
                    <button type="submit" name="update" class="cta-btn">Save Changes</button>
                    <button type="button" onclick="toggleEditForm()" class="cta-btn secondary">Cancel</button>
                </div>
            </form>

            <div class="buttons-container" id="mainButtons">
                <button onclick="toggleEditForm()" id="editButton" class="cta-btn">Edit Profile</button>
                <a href="user_dashboard.php" class="cta-btn secondary">Back to Dashboard</a>
            </div>
        </div>
    </main>

    <script>
        function toggleEditForm() {
            const editForm = document.getElementById("editForm");
            const mainButtons = document.getElementById("mainButtons");

            if (editForm.style.display === "none" || !editForm.style.display) {
                editForm.style.display = "block";
                mainButtons.style.display = "none";
            } else {
                editForm.style.display = "none";
                mainButtons.style.display = "flex";
            }
        }
    </script>
</body>
</html>

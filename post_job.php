<?php
require 'includes/auth.php';
include('includes/db_connect.php');

if ($_SESSION['user_type'] != 'client') {
    header("Location: login.php");
    exit;
}
$client_id = $_SESSION['user_id'];
$job_id = isset($_GET['job_id']) ? mysqli_real_escape_string($conn, $_GET['job_id']) : null;
$title = $job_type = $description = $requirements = "";
$edit_mode = isset($_GET['edit']) && !empty($_GET['edit']);



if ($_SERVER["REQUEST_METHOD"] == "GET"){
  if(isset($_GET['edit'])){
  $job_id = $_GET['edit'];
  if ($job_id) {
    $query = "SELECT title, job_type, description, requirements FROM jobs WHERE id = ? AND client_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $job_id, $client_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        $title = $row['title'];
        $job_type = $row['job_type'];
        $description = $row['description'];
        $requirements = $row['requirements'];
        $edit_mode = true;

    } else {
        $_SESSION['message'] = "Job not found!";
        $_SESSION['message_type'] = "error";
        // header("Location: view_jobs.php");
        // exit;
    }

  }
}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
  $title = mysqli_real_escape_string($conn, $_POST['title']);
  $job_type = mysqli_real_escape_string($conn, $_POST['job_type']);
  $description = mysqli_real_escape_string($conn, $_POST['description']);
  $requirements = mysqli_real_escape_string($conn, $_POST['requirements']);

  if ($edit_mode) {
      $job_id = isset($_GET['edit']) ? $_GET['edit'] : null;
      
      $query = "UPDATE jobs SET title = ?, job_type = ?, description = ?, requirements = ? WHERE id = ? AND client_id = ?";
      $stmt = mysqli_prepare($conn, $query);
      mysqli_stmt_bind_param($stmt, "ssssii", $title, $job_type, $description, $requirements, $job_id, $client_id);
  } else {
      // Insert new job if not in edit mode
      $query = "INSERT INTO jobs (client_id, title, job_type, description, requirements) VALUES (?, ?, ?, ?, ?)";
      $stmt = mysqli_prepare($conn, $query);
      mysqli_stmt_bind_param($stmt, "issss", $client_id, $title, $job_type, $description, $requirements);
  }

  if (mysqli_stmt_execute($stmt)) {
      $_SESSION['message'] = $edit_mode ? "Job updated successfully!" : "Job posted successfully!";
      $_SESSION['message_type'] = "success";
      header("Location: view_jobs.php");
      exit;
  } else {
      $_SESSION['message'] = "Something went wrong. Please try again.";
      $_SESSION['message_type'] = "error";
  }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
  <script type="text/javascript">
    window.tailwind.config = {
      darkMode: ['class'],
      theme: {
        extend: {
          colors: {
            border: 'hsl(var(--border))',
            input: 'hsl(var(--input))',
            ring: 'hsl(var(--ring))',
            background: 'hsl(var(--background))',
            foreground: 'hsl(var(--foreground))',
            primary: { DEFAULT: 'hsl(var(--primary))', foreground: 'hsl(var(--primary-foreground))' },
            secondary: { DEFAULT: 'hsl(var(--secondary))', foreground: 'hsl(var(--secondary-foreground))' },
            destructive: { DEFAULT: 'hsl(var(--destructive))', foreground: 'hsl(var(--destructive-foreground))' },
            muted: { DEFAULT: 'hsl(var(--muted))', foreground: 'hsl(var(--muted-foreground))' },
            accent: { DEFAULT: 'hsl(var(--accent))', foreground: 'hsl(var(--accent-foreground))' },
            popover: { DEFAULT: 'hsl(var(--popover))', foreground: 'hsl(var(--popover-foreground))' },
            card: { DEFAULT: 'hsl(var(--card))', foreground: 'hsl(var(--card-foreground))' },
          },
        }
      }
    }
  </script>
  <script src="https://unpkg.com/unlazy@0.11.3/dist/unlazy.with-hashing.iife.js" defer init></script>
  <style type="text/tailwindcss">
    @layer base {
      :root {
        --background: 0 0% 100%;
        --foreground: 240 10% 3.9%;
        --card: 0 0% 100%;
        --card-foreground: 240 10% 3.9%;
        --popover: 0 0% 100%;
        --popover-foreground: 240 10% 3.9%;
        --primary: 240 5.9% 10%;
        --primary-foreground: 0 0% 98%;
        --secondary: 240 4.8% 95.9%;
        --secondary-foreground: 240 5.9% 10%;
        --muted: 240 4.8% 95.9%;
        --muted-foreground: 240 3.8% 46.1%;
        --accent: 240 4.8% 95.9%;
        --accent-foreground: 240 5.9% 10%;
        --destructive: 0 84.2% 60.2%;
        --destructive-foreground: 0 0% 98%;
        --border: 240 5.9% 90%;
        --input: 240 5.9% 90%;
        --ring: 240 5.9% 10%;
        --radius: 0.5rem;
      }
      .dark {
        --background: 240 10% 3.9%;
        --foreground: 0 0% 98%;
        --card: 240 10% 3.9%;
        --card-foreground: 0 0% 98%;
        --popover: 240 10% 3.9%;
        --popover-foreground: 0 0% 98%;
        --primary: 0 0% 98%;
        --primary-foreground: 240 5.9% 10%;
        --secondary: 240 3.7% 15.9%;
        --secondary-foreground: 0 0% 98%;
        --muted: 240 3.7% 15.9%;
        --muted-foreground: 240 5% 64.9%;
        --accent: 240 3.7% 15.9%;
        --accent-foreground: 0 0% 98%;
        --destructive: 0 62.8% 30.6%;
        --destructive-foreground: 0 0% 98%;
        --border: 240 3.7% 15.9%;
        --input: 240 3.7% 15.9%;
        --ring: 240 4.9% 83.9%;
      }
    }
  </style>
  <title>Post New Job - SEOMarketplace</title>
</head>
<body class="container mx-auto p-4 bg-background">
  <nav class="bg-card border-b border-border mb-8">
    <div class="container mx-auto px-4 py-3">
      <div class="logo">
        <span class="text-foreground font-semibold text-lg">SEOMarketplace</span>
      </div>
    </div>
  </nav>
  <div class="max-w-2xl mx-auto space-y-6">
    <?php if (isset($_SESSION['message'])): ?>
      <div id="message" class="p-4 mb-4 text-sm rounded-lg <?= $_SESSION['message_type'] == 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
        <?= $_SESSION['message'] ?>
      </div>
      <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <div class="card border border-border rounded-lg p-6">
      <h2 class="text-2xl font-semibold mb-6 text-foreground">Post a New Job</h2>
      
      <form id="job-form" method="POST" class="space-y-6">
        <!-- Job Title -->
        <div>
          <label for="title" class="block text-sm font-medium text-foreground mb-2">Job Title</label>
          <input type="text" id="title" name="title" value="<?= $title ?>"
                 class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-ring focus:border-transparent"
                 placeholder="Enter job title" maxlength="100" required>
          <div class="text-sm text-muted-foreground mt-1">
            <span id="title-count">0</span>/100
          </div>
        </div>

        <!-- Job Type -->
        <div class="relative">
          <label class="block text-sm font-medium text-foreground mb-2">Job Type</label>
          <input type="hidden" name="job_type" id="selected-job-type" value="<?= htmlspecialchars($job_type) ?>">  
                  <!-- Job Type Dropdown -->
                  <button type="button" id="job-type-toggle" 
                    class="w-full px-4 py-2 text-left bg-background border border-border rounded-lg focus:ring-2 focus:ring-ring focus:border-transparent">
                    <?= $edit_mode && !empty($job_type) ? $job_type : 'Select job type' ?>
                </button>

          <div id="dropdown-menu" class="absolute w-full mt-1 bg-background border border-border rounded-lg shadow-lg opacity-0 invisible transition-opacity z-10">
            <div class="p-2 space-y-1">
              <button type="button" class="w-full text-left px-4 py-2 text-foreground hover:bg-secondary focus:bg-secondary focus:outline-none" data-value="Full-time">Full-time</button>
              <button type="button" class="w-full text-left px-4 py-2 text-foreground hover:bg-secondary focus:bg-secondary focus:outline-none" data-value="Part-time">Part-time</button>
              <button type="button" class="w-full text-left px-4 py-2 text-foreground hover:bg-secondary focus:bg-secondary focus:outline-none" data-value="Freelance">Freelance</button>
              <button type="button" class="w-full text-left px-4 py-2 text-foreground hover:bg-secondary focus:bg-secondary focus:outline-none" data-value="Remote">Remote</button>
            </div>
          </div>
        </div>

        <!-- Job Description -->
        <div>
          <label for="description" class="block text-sm font-medium text-foreground mb-2">Job Description</label>
          <textarea id="description" name="description" rows="6"
                    class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-ring focus:border-transparent"
                    placeholder="Describe job details..." maxlength="2000" required><?= htmlspecialchars($description) ?></textarea>
          <div class="text-sm text-muted-foreground mt-1">
            <span id="desc-count">0</span>/2000
          </div>
        </div>
        

        <!-- Job Requirements -->
        <div>
          <label for="requirements" class="block text-sm font-medium text-foreground mb-2">Requirements</label>
          <textarea id="requirements" name="requirements" rows="5"
                    class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-ring focus:border-transparent"
                    placeholder="List job requirements..." maxlength="1500" required><?= htmlspecialchars($requirements) ?></textarea>
          <div class="text-sm text-muted-foreground mt-1">
            <span id="req-count">0</span>/1500
          </div>
        </div>

        <div class="flex justify-between gap-4">
          <button type="button" id="Back-btn" 
                  class="flex-1 py-2 px-4 bg-secondary text-secondary-foreground rounded-lg hover:bg-secondary/80 transition-colors">
            Back
          </button>
          <button type="submit" 
                  class="flex-1 py-2 px-4 bg-primary text-primary-foreground rounded-lg hover:bg-primary/80 transition-colors"><?= $edit_mode ? "Update Job" : "Post Job" ?>
            Post Job
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Character counters
      const title = document.getElementById('title');
      const description = document.getElementById('description');
      const requirements = document.getElementById('requirements');
      const titleCount = document.getElementById('title-count');
      const descCount = document.getElementById('desc-count');
      const reqCount = document.getElementById('req-count');

      function updateCount(input, display) {
        display.textContent = input.value.length;
      }

      title.addEventListener('input', () => updateCount(title, titleCount));
      description.addEventListener('input', () => updateCount(description, descCount));
      requirements.addEventListener('input', () => updateCount(requirements, reqCount));

      // Back button functionality
      document.getElementById('Back-btn').addEventListener('click', () => {
        window.location.href = 'user_dashboard.php';
      });

      // Message timeout
      const messageDiv = document.getElementById('message');
      if (messageDiv) {
        setTimeout(() => {
          messageDiv.style.display = 'none';
        }, 2000);
      }

      // Job type dropdown
      const dropdownButton = document.getElementById('job-type-toggle');
      const dropdownMenu = document.getElementById('dropdown-menu');
      const selectedJobType = document.getElementById('selected-job-type');

      dropdownButton.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdownMenu.classList.toggle('opacity-0');
        dropdownMenu.classList.toggle('invisible');
        dropdownMenu.classList.toggle('opacity-100');
      });

      document.querySelectorAll('#dropdown-menu button').forEach(button => {
  button.addEventListener('click', () => {
    selectedJobType.value = button.dataset.value;
    dropdownButton.textContent = button.textContent;  // Display selected job type
    dropdownMenu.classList.add('opacity-0', 'invisible');  // Close dropdown
  });
});

      document.addEventListener('click', (e) => {
        if (!e.target.closest('.relative')) {
          dropdownMenu.classList.add('opacity-0', 'invisible');
        }
      });
    });
  </script>
</body>
</html>
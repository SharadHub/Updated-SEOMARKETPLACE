<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: user_dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEOMarketplace</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script src="https://unpkg.com/unlazy@0.11.3/dist/unlazy.with-hashing.iife.js" defer init></script>
    <script type="text/javascript">
        window.tailwind.config = {
            ...window.tailwind.config,
            theme: {
                extend: {
                    backgroundImage: {
                        'seo-bg': "url('images/seo.webp')",
                        
                    },
                },
            },
        };
    </script>
    <style>
        body {
            background-image: url('images/seo.webp');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
    </style>
</head>
<body class="container mx-auto p-4">

    <!-- Navbar -->
    <header class="bg-gray-900 text-white py-4">
        <div class="flex justify-between items-center max-w-6xl mx-auto">
            <div class="logo">
                <h1 class="text-3xl font-bold">SEOMarketplace</h1>
            </div>
            <nav>
                <ul class="flex space-x-6">
                    <li><a href="login.php" class="text-blue-500 hover:text-blue-300">Log In</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto mt-16 text-center">
        <div class="mb-16">
            <h1 class="text-4xl font-bold text-indigo-600 mb-6">Welcome to SEOMarketplace</h1>
            <p class="text-lg text-black mb-8">Connect with SEO professionals and businesses looking for SEO services</p>

            <!-- Registration Buttons -->
            <div class="flex justify-center space-x-4">
                <button class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-400 transition-colors">
                    <a href="register_client.php">Get Started as Client</a>
                </button>
                <button class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-500 transition-colors">
                    <a href="register_worker.php">Join as SEO Expert</a>
                </button>
            </div>
        </div>

        <!-- Features for Clients and Workers -->
        <div class="grid md:grid-cols-2 gap-8 mb-16">
            <div class="bg-gray-800 p-6 rounded-lg shadow-sm">
                <h2 class="text-2xl font-semibold text-teal-400 mb-4">For Clients</h2>
                <ul class="space-y-3 text-gray-200">
                    <li>• Access top SEO professionals</li>
                    <li>• Post your SEO projects</li>
                    <li>• Get quality work delivered</li>
                    <li>• Manage projects easily</li>
                </ul>
            </div>

            <div class="bg-gray-800 p-6 rounded-lg shadow-sm">
                <h2 class="text-2xl font-semibold text-teal-400 mb-4">For Workers</h2>
                <ul class="space-y-3 text-gray-200">
                    <li>• Find freelance SEO jobs</li>
                    <li>• Showcase your portfolio</li>
                    <li>• Get hired by top businesses</li>
                    <li>• Work on your terms</li>
                </ul>
            </div>
        </div>

        <!-- Popular SEO Services -->
        <div class="mb-16">
            <h2 class="text-2xl font-semibold text-black mb-6">Popular SEO Services</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-gray-800 p-4 rounded-lg text-center hover:shadow-md transition-shadow">
                    <img src="https://placehold.co/60x60?text=SEO" alt="SEO" class="mx-auto mb-4">
                    <h3 class="font-medium text-white mb-2">SEO Optimization</h3>
                </div>
                <div class="bg-gray-800 p-4 rounded-lg text-center hover:shadow-md transition-shadow">
                    <img src="https://placehold.co/60x60?text=SEM" alt="SEM" class="mx-auto mb-4">
                    <h3 class="font-medium text-white mb-2">Search Engine Marketing</h3>
                </div>
                <div class="bg-gray-800 p-4 rounded-lg text-center hover:shadow-md transition-shadow">
                    <img src="https://placehold.co/60x60?text=SMM" alt="SMM" class="mx-auto mb-4">
                    <h3 class="font-medium text-white mb-2">Social Media Marketing</h3>
                </div>
                <div class="bg-gray-800 p-4 rounded-lg text-center hover:shadow-md transition-shadow">
                    <img src="https://placehold.co/60x60?text=Analytics" alt="Analytics" class="mx-auto mb-4">
                    <h3 class="font-medium text-white mb-2">Analytics & Reporting</h3>
                </div>
            </div>
        </div>
        

    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 mt-16 py-8">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-indigo-600 font-bold mb-4 md:mb-0">SEOMarketplace</div>
                <ul class="flex space-x-6">
                    <li><a href="#" class="text-teal-400 hover:text-teal-200 font-medium transition-colors duration-200">About Us</a></li>
                    <!-- <li><a href="#" class="text-teal-400 hover:text-teal-200 font-medium transition-colors duration-200">Contact</a></li> -->
                    <li><a href="#" class="text-teal-400 hover:text-teal-200 font-medium transition-colors duration-200">Blog</a></li>
                </ul>
            </div>
        </div>              
    </footer>
    <div class="text-center text-black mt-6">
        &copy; <span id="year"></span> SEOMarketplace. All rights reserved.
    </div>
</body>
</html>
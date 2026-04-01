// Toggle the mobile navigation menu on click
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('nav ul');
    
    // Toggle the menu visibility
    menuToggle.addEventListener('click', function() {
        navLinks.classList.toggle('show');
    });
});

// Form validation for login and registration forms
function validateForm(form) {
    const email = form.email.value.trim();
    const password = form.password.value.trim();
    
    // Simple email validation
    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
    if (!email || !email.match(emailPattern)) {
        alert('Please enter a valid email address.');
        return false;
    }

    // Password validation
    if (password.length < 6) {
        alert('Password must be at least 6 characters.');
        return false;
    }
    return true;
}

// Handle form submission for login and registration
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(event) {
        if (!validateForm(form)) {
            event.preventDefault(); // Prevent form submission if validation fails
        }
    });
});

// Dynamic content for user dashboard (example: update posts list)
function loadPosts() {
    fetch('getPosts.php')  // Assuming you have a PHP script to get posts
        .then(response => response.json())
        .then(data => {
            const postsContainer = document.getElementById('posts-container');
            postsContainer.innerHTML = ''; // Clear existing posts

            if (data.length > 0) {
                data.forEach(post => {
                    const postElement = document.createElement('div');
                    postElement.classList.add('post');
                    postElement.innerHTML = `
                        <h3>${post.title}</h3>
                        <p>${post.content}</p>
                        <p><strong>Created at: </strong>${post.created_at}</p>
                        <button onclick="deletePost(${post.id})">Delete</button>
                    `;
                    postsContainer.appendChild(postElement);
                });
            } else {
                postsContainer.innerHTML = '<p>No posts available.</p>';
            }
        })
        .catch(error => {
            console.error('Error loading posts:', error);
        });
}

// Delete a post dynamically from the dashboard
function deletePost(postId) {
    if (confirm('Are you sure you want to delete this post?')) {
        fetch(`deletepost.php?id=${postId}`, { method: 'GET' })
            .then(response => response.text())
            .then(result => {
                alert(result);
                loadPosts(); // Reload the posts after deletion
            })
            .catch(error => {
                console.error('Error deleting post:', error);
            });
    }
}

// Load posts when the page is loaded
document.addEventListener('DOMContentLoaded', loadPosts);

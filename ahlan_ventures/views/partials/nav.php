<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tile Catalog</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --badge-bg-color: #ff5722;
            --badge-text-color: #fff;
        }

        /* Cart Badge */
        .cart-badge {
            position: relative;
        }
        .cart-badge .badge {
            position: absolute;
            top: -8px;
            right: -12px;
            background-color: var(--badge-bg-color);
            color: var(--badge-text-color);
            font-size: 0.75rem;
            font-weight: bold;
            padding: 3px 6px;
            border-radius: 50%;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        /* Header/Navbar */
        .navbar-brand {
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--primary-color);
        }
        .navbar-brand:hover {
            color: var(--secondary-color);
        }
        .nav-link {
            font-weight: 500;
            color: var(--secondary-color);
            transition: color 0.3s ease;
        }
        .nav-link:hover {
            color: var(--primary-color);
        }
        .search-cart {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('pic/housetile.jpg') no-repeat center;
            background-size: cover;
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        .hero h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
        }
        .hero p {
            font-size: 1.5rem;
            margin-bottom: 30px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        }
    </style>
        
    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</head>
<body>
    <!-- Bootstrap Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="">
                <img src="pic/ahlanlogo.png" alt="AHLAN VENTURES" style="height: 80px; width: auto;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav me-auto mb-2 mb-md-0">
                <li class="nav-item">
                    <a href="/" class="nav-link <?= urlIs('/') ? 'active' : '' ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a href="/about" class="nav-link <?= urlIs('/about.php') ? 'active' : '' ?>">About</a>
                </li>
                <li class="nav-item">
                    <a href="/catalogue" class="nav-link <?= urlIs('/catalogue.php') ? 'active' : '' ?>">catalogue</a>
                </li>
                <li class="nav-item">
                    <a href="/contact" class="nav-link <?= urlIs('/contact.php') ? 'active' : '' ?>">Contact</a>
                </li>
                
                </ul>
                <div class="search-cart me-3">
                    <!-- Search Form -->
                    <form action="search.controller.php" method="GET" class="d-flex align-items-center" onsubmit="return handleSearch(event)">
                        <input type="text" name="query" id="searchQuery" class="form-control me-1" placeholder="Search products..." required style="max-width: 250px;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                    <div id="searchResults" class="position-absolute bg-white shadow-sm p-3 mt-2 rounded" style="max-width: 300px; display: none; z-index: 1000;">
                        <!-- Search results will be dynamically populated here -->
                    </div>


                </div>

                <!-- Login and Register Buttons -->
                <?php if (isset($_SESSION['first_name'])): ?>
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="authDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle"></i> <?= htmlspecialchars($_SESSION['first_name']) ?>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="authDropdown">
                            <li><a class="dropdown-item" href="dashboard.php">Profile</a></li>
                            <li><a class="dropdown-item" href="logout.php">Log out</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="authDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Account
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="authDropdown">
                            <li><a class="dropdown-item" href="login.php">Log in</a></li>
                            <li><a class="dropdown-item" href="register.php">Register</a></li>
                        </ul>
                    </div>
                <?php endif; ?>

            <!-- Cart Button -->
            <div class="ms-3">
                <a href="cart.php" class="btn btn-outline-secondary cart-badge">
                    <i class="fas fa-shopping-cart"></i>
                   
                </a>
            </div>


<script>
    async function handleSearch(event) {
        event.preventDefault();
        const query = document.getElementById('searchQuery').value;
        const resultsContainer = document.getElementById('searchResults');

        if (query.trim() === '') {
            resultsContainer.style.display = 'none';
            return;
        }

        try {
            const response = await fetch(`search.controller.php?query=${encodeURIComponent(query)}`);
            const data = await response.json();

            if (data.success && data.data.length > 0) {
            resultsContainer.innerHTML = data.data.map(result => `
                
                <div>
                    <a href="search.result.php?id=${result.id}" class="text-decoration-none text-dark d-flex align-items-center">
                        <img src="${result.image}" alt="${result.name}" class="me-3 rounded" style="width: 50px; height: 50px; object-fit: cover;">
                        <div>
                            <strong>${result.name}</strong><br>
                            <small class="text-muted">₦${result.price}</small>
                        </div>
                    </a>
                </div>
                </div>
            `).join('');
            resultsContainer.style.display = 'block';
            resultsContainer.style.position = 'fixed';
            resultsContainer.style.top = '50%';
            resultsContainer.style.left = '50%';
            resultsContainer.style.transform = 'translate(-50%, -50%)';
            resultsContainer.style.zIndex = '1050';
            resultsContainer.style.width = '50%';
            resultsContainer.style.maxHeight = '90%'; 
            resultsContainer.style.overflowY = 'auto';
            resultsContainer.style.padding = '20px';
            resultsContainer.style.backgroundColor = 'white';
            resultsContainer.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.2)';
            resultsContainer.style.borderRadius = '10px';
            } else {
            resultsContainer.innerHTML = `<div class="p-2 text-muted">${data.message || 'No result found'}</div>`;
            resultsContainer.style.display = 'block';
            }
        } catch (error) {
            console.error('Error fetching search results:', error);
            resultsContainer.innerHTML = '<div class="p-2 text-danger">Error fetching results</div>';
            resultsContainer.style.display = 'block';
        }
        }

        document.addEventListener('click', function (event) {
        const resultsContainer = document.getElementById('searchResults');
        if (!resultsContainer.contains(event.target) && event.target.id !== 'searchQuery') {
            resultsContainer.style.display = 'none';
        }
        });
    </script>

       

                </div>
            </div>
        </div>
    </nav>

    
</html>

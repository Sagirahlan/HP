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
        }
        /* Header/Navbar */
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        .nav-link {
            font-weight: 500;
        }
        .search-cart {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(63, 14, 14, 0.6), rgba(162, 14, 14, 0.6)), url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') no-repeat center;
            background-size: cover;
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        .hero h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .hero p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }
        /* Tile Catalog */
        .tile-catalog .card {
            transition: transform 0.2s;
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .tile-catalog .card:hover {
            transform: translateY(-5px);
        }
        .tile-catalog .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        .tile-catalog .bg-primary .card {
            background-color: rgba(13, 110, 253, 0.05);
        }
        /* Footer */
        footer {
            background-color: #f8f9fa;
            padding: 30px 0;
            margin-top: 50px;
        }
        footer ul {
            list-style: none;
            padding: 0;
            display: flex;
            justify-content: center;
            gap: 20px;
        }
    </style>
</head>
</html>
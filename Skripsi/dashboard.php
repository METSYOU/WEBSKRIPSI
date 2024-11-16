<?php
session_start();

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['logged_in']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'owner')) {
    header('location: login.php');
    exit();
}

// Handle logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('location: login.php?logout_success=1');
    exit();
}

$user_role = $_SESSION['role'];
$user_name = $_SESSION['user_name'];

// Menu items for roles
$menu_items = [
    'admin' => [
        'Manage Brand' => 'manage_brand.php',
        'Manage Tipe Mesin' => 'manage_tipe.php',
        'Manage Kategori' => 'manage_kategori.php',
        'Manage Products' => 'manage_products.php',
        'View Orders' => 'view_orders.php',
    ],
    'owner' => [
        'View Sales Report' => 'view_sales_report.php',
    ],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="Assets/CSS/style.css">
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar p-3 bg-light" style="min-width: 250px;">
        <h3 class="mb-4">Lancar Diesel Dashboard</h3>
        <p>Welcome, <strong><?php echo htmlspecialchars($user_name); ?></strong></p>
        <p>Your role: <strong><?php echo htmlspecialchars($user_role); ?></strong></p>
        <hr>

        <?php foreach ($menu_items[$user_role] as $label => $link): ?>
            <a href="<?php echo $link; ?>" class="d-block mb-2"><?php echo $label; ?></a>
        <?php endforeach; ?>
        <a href="dashboard.php?logout=1" class="btn btn-danger mt-3">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="container my-5">
        <h1>Welcome to the Dashboard</h1>
        <p>This is your main dashboard area. Use the sidebar to navigate through the options available to you.</p>
    </div>
</div>

<!-- Footer -->
<footer class="bg-light py-4 mt-5">
    <div class="container text-center">
        <p>&copy; Lancar Diesel 2024 | Dashboard for Admins and Owners</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

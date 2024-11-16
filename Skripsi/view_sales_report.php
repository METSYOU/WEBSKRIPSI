<?php
session_start();
include('Assets/server/connection.php');

// Ensure the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php?error=You must log in first.');
    exit();
}

// Handle logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('location: login.php?logout_success=1');
    exit();
}

// Fetch pemesanan data
$query_pemesanan = "SELECT id_pemesanan, id_user, tanggal_pemesanan, order_status FROM pemesanan ORDER BY tanggal_pemesanan DESC";
$result_pemesanan = $conn->query($query_pemesanan);

// Fetch detail_pemesanan data
$query_detail = "SELECT dp.id_pemesanan, dp.products_id, dp.jumlah, p.products_name, p.products_image 
                 FROM detail_pemesanan dp 
                 JOIN products p ON dp.products_id = p.products_id
                 ORDER BY dp.id_pemesanan, dp.products_id";
$result_detail = $conn->query($query_detail);

// User role and name (optional, if needed for the sidebar)
$user_role = $_SESSION['role'] ?? 'User';
$user_name = $_SESSION['user_name'] ?? 'Guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesanan and Detail Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="Assets/CSS/style.css">
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar p-3 bg-light" style="min-width: 250px;">
        <h3>Lancar Diesel</h3>
        <p>Welcome, <strong><?php echo htmlspecialchars($user_name); ?></strong></p>
        <p>Role: <strong><?php echo htmlspecialchars($user_role); ?></strong></p>
        <hr>

        <!-- Sidebar Menu -->
        <a href="dashboard.php" class="d-block mb-2">Dashboard</a>
        <a href="view_sales_report.php" class="d-block mb-2">View Sales Report</a>
        <a href="dashboard.php?logout=1" class="btn btn-danger mt-3">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="container my-5 py-5">
        <h2 class="text-center">Pemesanan Report</h2>
        <hr class="mx-auto">

        <!-- Pemesanan Table -->
        <table class="table table-bordered mt-5">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>User ID</th>
                    <th>Order Date</th>
                    <th>Order Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_pemesanan && $result_pemesanan->num_rows > 0): ?>
                    <?php while ($row = $result_pemesanan->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id_pemesanan']); ?></td>
                            <td><?php echo htmlspecialchars($row['id_user']); ?></td>
                            <td><?php echo htmlspecialchars(date('d M Y', strtotime($row['tanggal_pemesanan']))); ?></td>
                            <td><?php echo htmlspecialchars($row['order_status']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">No pemesanan data found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <h2 class="text-center mt-5">Detail Pemesanan Report</h2>
        <hr class="mx-auto">

        <!-- Detail Pemesanan Table -->
        <table class="table table-bordered mt-5">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Product Image</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_detail && $result_detail->num_rows > 0): ?>
                    <?php while ($row = $result_detail->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id_pemesanan']); ?></td>
                            <td><?php echo htmlspecialchars($row['products_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['products_name']); ?></td>
                            <td>
                                <img src="Assets/imgs/<?php echo htmlspecialchars($row['products_image']); ?>" alt="Product Image" style="width: 50px; height: 50px;">
                            </td>
                            <td><?php echo htmlspecialchars($row['jumlah']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No detail pemesanan data found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Footer -->
<footer class="bg-light py-4 mt-5">
    <div class="container text-center">
        <p>&copy; Lancar Diesel 2024 | Pemesanan and Detail Report</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

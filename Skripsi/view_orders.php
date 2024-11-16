<?php
session_start();

// Include database connection
include('Assets/server/connection.php');

// Check if the user is logged in and has the correct role (admin or owner)
if (!isset($_SESSION['logged_in']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'owner')) {
    // Redirect to login page if not logged in or unauthorized
    header('location: login.php');
    exit();
}

// Handle status update request
if (isset($_POST['update_status'])) {
    $id_pemesanan = $_POST['id_pemesanan'];
    $new_status = $_POST['order_status'];

    // Update order status in the database
    $update_query = "UPDATE pemesanan SET order_status = ? WHERE id_pemesanan = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("si", $new_status, $id_pemesanan);
    $stmt->execute();
    $stmt->close();
}

// Fetch orders from the pemesanan table
$query = "SELECT id_pemesanan, id_user, tanggal_pemesanan, order_status FROM pemesanan";
$result = $conn->query($query);

// Check if query was successful
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Handle logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('location: login.php');
    exit;
}

$user_role = $_SESSION['role'];
$user_name = $_SESSION['user_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="Assets/CSS/style.css">
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar">
        <h3>Lancar Diesel Dashboard</h3>
        <p>Welcome, <?php echo htmlspecialchars($user_name); ?></p>
        <p>Your role: <?php echo htmlspecialchars($user_role); ?></p>
        <hr>

        <?php if ($user_role == 'admin'): ?>
            <h5>Admin Menu</h5>
            <a href="manage_brand.php">Manage Brand</a>
            <a href="manage_tipe.php">Manage Tipe Mesin</a>
            <a href="manage_kategori.php">Manage Kategori</a>
            <a href="manage_products.php">Manage Products</a>
            <a href="view_orders.php">View Orders</a>
        <?php elseif ($user_role == 'owner'): ?>
            <h5>Owner Menu</h5>
            <a href="view_sales_report.php">View Sales Report</a>
        <?php endif; ?>
        <a href="view_orders.php?logout=1" id="logout-btn" class="btn btn-danger mt-3">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="container my-5">
        <h2 class="mb-4">Orders</h2>
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Order ID</th>
                    <th>Customer ID</th>
                    <th>Order Date</th>
                    <th>Status</th>
                    <th>Change Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <a href="view_payment.php?id_pemesanan=<?php echo $row['id_pemesanan']; ?>">
                                    <?php echo htmlspecialchars($row['id_pemesanan']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($row['id_user']); ?></td>
                            <td><?php echo htmlspecialchars($row['tanggal_pemesanan']); ?></td>
                            <td><?php echo htmlspecialchars($row['order_status']); ?></td>
                            <td>
                                <form method="POST" action="view_orders.php" class="d-flex align-items-center">
                                    <input type="hidden" name="id_pemesanan" value="<?php echo $row['id_pemesanan']; ?>">
                                    <select name="order_status" class="form-select me-2" style="width: auto;">
                                        <option value="Dikirim" <?php if ($row['order_status'] == 'Dikirim') echo 'selected'; ?>>Dikirim</option>
                                        <option value="Diterima" <?php if ($row['order_status'] == 'Diterima') echo 'selected'; ?>>Diterima</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn btn-primary btn-sm">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No orders found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Footer -->
<footer class="bg-light py-4 mt-5">
    <div class="container text-center">
        <p>&copy; Lancar Diesel 2024 | Orders Management</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
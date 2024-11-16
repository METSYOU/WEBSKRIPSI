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

// Fetch brands from the brand table
$query = "SELECT brand_id, brand_name, brand_description, brand_img FROM brand";
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

// Handle delete request
if (isset($_POST['delete_brand'])) {
    $brand_id = $_POST['brand_id'];
    
    // Delete brand from the database
    $delete_query = "DELETE FROM brand WHERE brand_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $brand_id);
    $stmt->execute();
    $stmt->close();

    // Refresh the page to see changes
    header("Location: manage_brand.php");
    exit();
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
    <title>View Brands</title>
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
        
        <a href="manage_brand.php?logout=1" id="logout-btn" class="btn btn-danger mt-3">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="container my-5">
        <h2 class="mb-4">Brands</h2>
        <!-- Button to Add New Brand -->
        <a href="tambah_brand.php" class="btn btn-success mb-3">Tambah Brand</a>
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Brand ID</th>
                    <th>Brand Name</th>
                    <th class="brand-description">Brand Description</th>
                    <th>Brand Image</th> <!-- New column for brand image -->
                    <th class="actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['brand_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['brand_name']); ?></td>
                            <td class="brand-description"><?php echo htmlspecialchars($row['brand_description']); ?></td>
                            <td>
                            <img class="img-fluid mb-3" src="Assets/imgs/<?php echo htmlspecialchars($row['brand_img']); ?>" style="width: 100px; height: auto;">
                            </td>
                            <td class="actions">
                                <a href="edit_brand.php?brand_id=<?php echo $row['brand_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <form method="POST" action="manage_brand.php" class="d-inline">
                                    <input type="hidden" name="brand_id" value="<?php echo $row['brand_id']; ?>">
                                    <button type="submit" name="delete_brand" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this brand?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No brands found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Footer -->
<footer class="bg-light py-4 mt-5">
    <div class="container text-center">
        <p>&copy; Lancar Diesel 2024 | Brand Management</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
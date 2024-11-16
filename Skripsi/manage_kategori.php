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

// Handle category deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['category_id'])) {
    $category_id = $_POST['category_id'];

    // Prepare the delete statement
    $delete_query = "DELETE FROM category WHERE category_id = ?";
    $stmt = $conn->prepare($delete_query);
    
    if ($stmt) {
        $stmt->bind_param("i", $category_id);
        if ($stmt->execute()) {
            // Deletion successful
            $success = "Category deleted successfully.";
        } else {
            // Deletion failed
            $error = "Error deleting category: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Error preparing statement: " . $conn->error;
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('location: login.php');
    exit;
}

// Fetch categories from the category table
$query = "SELECT category_id, machine_id, category_name, category_description, category_img FROM category";
$result = $conn->query($query);

// Check if query was successful
if (!$result) {
    die("Query failed: " . $conn->error);
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
    <title>View Categories</title>
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
        <a href="manage_kategori.php?logout=1" id="logout-btn" class="btn btn-danger mt-3">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="container my-5">
        <h2 class="mb-4">Categories</h2>
        <a href="tambah_kategori.php" class="btn btn-success mb-3">Tambah Kategori Baru</a>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php elseif (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Category ID</th>
                    <th>Machine ID</th>
                    <th>Category Name</th>
                    <th>Category Description</th>
                    <th class="category_img text-center">Category Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['category_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['machine_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['category_description']); ?></td>
                            <td style="text-align: center;">
                                <img class="img-fluid mb-3" src="Assets/imgs/<?php echo htmlspecialchars($row['category_img']); ?>" style="width: 100px; height: auto;">
                            </td>
                            <td>
                                <a href="edit_kategori.php?category_id=<?php echo $row['category_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <form action="delete_product.php" method="POST" class="d-inline">
                                    <input type="hidden" name="category_id" value="<?php echo $row['category_id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No categories found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Footer -->
<footer class="bg-light py-4 mt-5">
    <div class="container text-center">
        <p>&copy; Lancar Diesel 2024 | Categories Management</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
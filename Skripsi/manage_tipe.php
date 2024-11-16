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

// Fetch machines from the diesel_machine table
$query = "SELECT machine_Id, brand_id, machine_name, machine_description FROM diesel_machine"; // Include machine_name in the query
$result = $conn->query($query);

// Check if query was successful
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Handle delete request
if (isset($_POST['delete_machine'])) { 
    $machine_id = $_POST['machine_id']; 
    
    // Delete machine from the database
    $delete_query = "DELETE FROM diesel_machine WHERE machine_Id = ?"; // Ensure this is the correct table name
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $machine_id);
    $stmt->execute();
    $stmt->close();

    // Refresh the page to see changes
    header("Location: manage_tipe.php"); 
    exit();
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
    <title>View Machines</title>
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
        <a href="manage_tipe.php?logout=1" id="logout-btn" class="btn btn-danger mt-3">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="container my-5">
        <h2 class="mb-4">Machines</h2>
        <!-- Button to Add New Machine -->
        <a href="tambah_tipe.php" class="btn btn-success mb-3">Tambah Tipe Mesin Baru</a>
        <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>Machine ID</th>
                <th>Brand ID</th>
                <th>Machine Name</th> <!-- New column for Machine Name -->
                <th>Machine Description</th>
                <th class="actions text-center">Actions</th> <!-- Centered Actions Header -->
            </tr>
        </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['machine_Id']); ?></td> <!-- Display Machine ID -->
                            <td><?php echo htmlspecialchars($row['brand_id']); ?></td> <!-- Display Brand ID -->
                            <td><?php echo htmlspecialchars($row['machine_name']); ?></td> <!-- Display Machine Name -->
                            <td><?php echo htmlspecialchars($row['machine_description']); ?></td> <!-- Display Machine Description -->
                            <td class="actions text-start"> <!-- Align text to the left -->
                                <div class="d-flex justify-content-center"> <!-- Center the buttons -->
                                    <a href="edit_tipe.php?machine_id=<?php echo $row['machine_Id']; ?>" class="btn btn-warning btn-sm me-2">Edit</a>
                                    <form method="POST" action="manage_tipe.php" class="d-inline">
                                        <input type="hidden" name="machine_id" value="<?php echo $row['machine_Id']; ?>">
                                        <button type="submit" name="delete_machine" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this machine?');">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No machines found</td> <!-- Updated colspan to 5 -->
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Footer -->
<footer class="bg-light py-4 mt-5">
    <div class="container text-center">
        <p>&copy; Lancar Diesel 2024 | Machine Management</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

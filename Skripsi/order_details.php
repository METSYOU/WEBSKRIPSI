<?php
include('Assets/server/connection.php');

// Validate that the order ID is passed
if (isset($_GET['order_id'])) {
    $order_id = filter_var($_GET['order_id'], FILTER_SANITIZE_NUMBER_INT);

    // Prepare the statement to fetch order details
    $stmt = $conn->prepare("SELECT product_name, product_image, jumlah FROM detail_pemesanan WHERE id_pemesanan = ?");
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $order_details = $stmt->get_result();

    // Check if the order details exist
    if ($order_details->num_rows === 0) {
        header('location: account.php?error=No details found for this order.');
        exit;
    }
} else {
    header('location: account.php?error=Order ID is missing.');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="Assets/CSS/style.css"/>
</head>
<body> 

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white py-3 fixed-top">
    <div class="container">
        <img class="logo" src="Assets/imgs/images.png" alt="Lancar Diesel Logo"/>
        <h2 class="brand">Lancar Diesel</h2>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse nav-buttons" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="shop.php">Catalog</a></li>
                <li class="nav-item">
                    <a href="cart.php"><i class="fas fa-shopping-cart"></i></a>
                    <a href="account.php"><i class="fas fa-user"></i></a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Order Details Section -->
<section id="orders" class="orders container my-5 py-3">
    <div class="container mt-2">
        <h2 class="font-weight-bold text-center">Order Details</h2>
        <hr class="mx-auto">
    </div>

    <!-- Order Table -->
    <table class="table table-bordered mt-5">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Product Image</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $order_details->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                    <td>
                        <img src="Assets/imgs/<?php echo htmlspecialchars($row['product_image']); ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>" width="50" height="50"/>
                    </td>
                    <td><?php echo htmlspecialchars($row['jumlah']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Back to Account Button -->
    <div class="text-center mt-4">
        <a href="account.php" class="btn btn-primary">Back to Account</a>
    </div>
</section>

<!-- Footer -->
<footer class="mt-5 py-5">
    <div class="container">
        <p>Lancar Diesel @ 2024 All Rights Reserved</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

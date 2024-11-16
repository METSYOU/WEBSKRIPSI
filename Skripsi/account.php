<?php
session_start();
include('Assets/server/connection.php');

if (!isset($_SESSION['logged_in'])) {
    header('location: login.php');
    exit;
}

// Handle logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('location: login.php');
    exit;
}

// Handle password change
if (isset($_POST['change_password'])) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirmPassword'];
    $user_email = $_SESSION['user_email'];

    if ($password !== $confirm_password) {
        header('location: account.php?error=Password tidak sama');
        exit();
    } elseif (strlen($password) < 6) {
        header('location: account.php?error=Password harus lebih dari 6 huruf');
        exit();
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE users SET user_password=? WHERE user_email=?;");
        $stmt->bind_param('ss', $hashed_password, $user_email);

        if ($stmt->execute()) {
            header('location: account.php?message=Password berhasil diperbarui');
        } else {
            header('location: account.php?error=Password gagal diperbarui');
        }
        $stmt->close();
    }
    $conn->close();
}

// Handle order status update
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];

    $stmt = $conn->prepare("UPDATE pemesanan SET order_status='diterima' WHERE id_pemesanan=?");
    $stmt->bind_param('i', $order_id);

    if ($stmt->execute()) {
        header('location: account.php?message=Order status berhasil diperbarui');
    } else {
        header('location: account.php?error=Order status gagal diperbarui');
    }
    $stmt->close();
}

// Retrieve orders for the logged-in user
if (isset($_SESSION['logged_in'])) {
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT id_pemesanan, tanggal_pemesanan, order_status FROM pemesanan WHERE id_user = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $orders = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link rel="stylesheet" href="Assets/CSS/style.css"/>
</head>
<body> 

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white py-3 fixed-top">
    <div class="container">
        <img class="logo" src="Assets/imgs/images.png"/>
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

<!-- Account Section -->
<section class="my-5 py-5">
    <div class="row container mx-auto">
        <div class="text-center mt-3 pt-5 col-lg-6 col-md-12 col-sm-12">
          <p class="text-center" style="color:green"><?php if (isset($_GET['register_success'])){ echo $_GET['register_success']; }?></p>
          <p class="text-center" style="color:green"><?php if (isset($_GET['login_success'])){ echo $_GET['login_success']; }?></p>
            <h3 class="font-weight-bold">Account Info</h3>
            <hr class="mx-auto">
            <div class="account-info">
                <p><span><?php echo $_SESSION['user_name'] ?? ''; ?></span></p>
                <p><a href="orders.php" id="orders-btn">Your Orders</a></p>
                <p><a href="account.php?logout=1" id="logout-btn">Logout</a></p>
            </div>
        </div>
        <div class="col-lg-6 col-md-12 col-sm-12">
            <form id="account-form" method="POST" action="account.php">
                <p class="text-center" style="color:red"><?php echo $_GET['error'] ?? ''; ?></p>
                <p class="text-center" style="color:green"><?php echo $_GET['message'] ?? ''; ?></p>
                <h3>Change Password</h3>
                <hr class="mx-auto">
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-control" id="account-password" name="password" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" class="form-control" id="account-password-confirm" name="confirmPassword" placeholder="Confirm Password" required>
                </div>
                <div class="form-group">
                    <input type="submit" name="change_password" value="Change Password" class="btn" id="change-pass-btn">
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Orders Section -->
<!-- Orders Section -->
<section id="orders" class="orders container my-5 py-3">
    <div class="container mt-2">
        <h2 class="font-weight-bold text-center">Your Orders</h2>
        <hr class="mx-auto">
    </div>

    <table class="table table-bordered mt-5">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Order Date</th>
                <th>Status</th>
                <th>Order Detail</th>
                <th>Action</th> <!-- New column for action buttons -->
            </tr>
        </thead>
        <tbody>
            <?php while ($order = $orders->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $order['id_pemesanan']; ?></td>
                    <td><?php echo $order['tanggal_pemesanan']; ?></td>
                    <td><?php echo $order['order_status']; ?></td>
                    <td>
                        <form method="GET" action="order_details.php">
                            <input type="hidden" name="order_id" value="<?php echo $order['id_pemesanan']; ?>"/>
                            <input class="btn order-details-btn" type="submit" value="Details"/>
                        </form>
                    </td>
                    <td>
                        <!-- Form to update order status -->
                        <?php if ($order['order_status'] !== 'diterima'): ?>
                            <form method="POST" action="account.php">
                                <input type="hidden" name="order_id" value="<?php echo $order['id_pemesanan']; ?>"/>
                                <button class="btn btn-success" type="submit" name="update_status">Diterima</button>
                            </form>
                        <?php else: ?>
                            <span class="text-success">Paket Sudah Diterima</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</section>

<!-- Footer -->
<footer class="mt-5 py-5">
    <div class="container">
        <p>Lancar Diesel @ 2024 All Right Reserved</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
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
                <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="shop.html">Catalog</a></li>
                <li class="nav-item">
                    <a href="cart.html"><i class="fas fa-shopping-cart"></i></a>
                    <a href="account.html"><i class="fas fa-user"></i></a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Payment Section -->
<section class="my-5 py-5">
    <div class="container text-center mt-3 pt-5">
        <h2 class="form-weight-bold">Payment</h2>
        <hr class="mx-auto">
    </div>
    <div class="mx-auto container text-center">
        <p>
            <?php 
            if (isset($_GET['order_status']) && $_SESSION['total']) { 
                echo $_GET['order_status'];
            }
            ?>
        </p>
        <p>Total Harga: Rp <?php if(isset($_SESSION['total'])){echo $_SESSION['total'];}?></p>

        <!-- Display Owner's Bank Number -->
        <div class="mt-3">
            <h5>Bank Number:</h5>
            <p><strong>1234-5678-9012 (Bank Name: ABC)</strong></p>
            <p>Please transfer to the above bank account and upload your payment proof below.</p>
        </div>

        <!-- Upload Payment Proof Form -->
        <?php if (isset($_SESSION['total']) && $_SESSION['total'] != 0) { ?>
            <form action="complete_payment.php" method="post" enctype="multipart/form-data">
                
                <!-- Shipment Option -->
                <div class="mb-3">
                    <label for="shipment" class="form-label">Select Shipment Option</label>
                    <select class="form-select" id="shipment" name="shipment" required>
                        <option value="" disabled selected>Choose your shipment option</option>
                        <option value="standard">Grab/GoJek - Rp 10,000</option>
                        <option value="express">JNE - Rp 30,000</option>
                    </select>
                </div>

                <input type="file" name="payment_proof" class="form-control mb-3" required>
                <input class="btn btn-primary" type="submit" name="complete_payment" value="Upload Payment Proof">
            </form>
        <?php } else { ?>
            <p>Anda Belum Memesan Apapun</p>
        <?php } ?>

        <?php if (isset($_GET['order_status']) && $_GET['order_status'] == "not paid") { ?>
            <input class="btn btn-primary" type="submit" value="Pay Now"/>
        <?php } ?>
    </div>
</section>

<!-- Footer -->
<footer class="mt-5 py-5">
    <div class="row container mx-auto pt-5">
        <div class="footer-one col-lg-3 col-md-6 col-sm-12">
            <img class="logo" src="Assets/imgs/images.png">
            <p class="pt-3">Kita memberikan produk terbaik dengan harga terjangkau</p>
        </div>
        <div class="footer-one col-lg-3 col-md-6 col-sm-12">
            <h5 class="pb-2">Spareparts</h5>
            <ul class="text-uppercase">
                <li><a href="">Piston</a></li>
                <li><a href="">Valve</a></li>
                <li><a href="">Filter</a></li>
                <li><a href="">Knalpot</a></li>
                <li><a href="">Nozzle</a></li>
                <li><a href="">Plunger</a></li>
            </ul>
        </div>
        <div class="footer-one col-lg-3 col-md-6 col-sm-12">
            <h5 class="pb-2">Hubungi Kami</h5>
            <div>
                <h6 class="text-uppercase">Alamat</h6>
                <p>Jalan Raya Bandar Selatan No 180</p>
            </div>
            <div>
                <h6 class="text-uppercase">Nomor Handphone</h6>
                <p>123 456 7890</p>
            </div>
        </div>
        <div class="footer-one col-lg-3 col-md-6 col-sm-12">
            <h5 class="pb-2">Email</h5>
            <p>info@email.com</p>
        </div>
    </div>

    <div class="copyright mt-5">
        <div class="row container mx-auto">
            <div class="col-lg-3 col-md-5 col-sm-12 mb-4"></div>
            <div class="col-lg-3 col-md-5 col-sm-12 mb-4 text-nowrap mb-2">
                <p>Lancar Diesel @ 2024 All Right Reserved</p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

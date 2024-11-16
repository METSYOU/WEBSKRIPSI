<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Define bank number and base total price
$bankNumber = "1234567890"; // Set your bank account number here
$baseTotal = $_SESSION['total']; // Original total price without shipping

// Check if the cart is not empty and 'checkout' is set
if (!empty($_SESSION['cart']) && isset($_POST['checkout'])) {
    // Proceed with checkout logic (if any)
} else {
    // Redirect or show an error if conditions are not met
    header("Location: cart.html"); // Redirect back to cart page if needed
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous"/>
    <link rel="stylesheet" href="Assets/CSS/style.css"/>
</head>
<body> 

<!--navbar-->
<nav class="navbar navbar-expand-lg navbar-light bg-white py-3 fixed-top">
    <div class="container">
      <img class="logo" src="Assets/imgs/images.png"/>
      <h2 class="brand">Lancar Diesel</h2>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse nav-buttons" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
                <a class="nav-link" href="index.html">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="shop.html">Catalog</a>
            </li>
            <li class="nav-item">
                <a href="cart.php"><i class="fas fa-shopping-cart"></i></a>
                <a href="account.php"><i class="fas fa-user"></i></a>
            </li>
        </ul>
      </div>
    </div>
</nav>

<!--Checkout-->
<section class="my-5 py-5">
    <div class="container text-center mt-3 pt-5">
        <h2 class="form-weight-bold">Check Out</h2>
        <hr class="mx-auto">
    </div>
    <div class="mx-auto container">
        <form id="checkout-form" method="POST" action="Assets/server/place_order.php" enctype="multipart/form-data">
            <div class="form-group checkout-small-element">
                <label>Nama</label>
                <input type="text" class="form-control" id="checkout-name" name="name" placeholder="Name" required>
            </div>
            <div class="form-group checkout-small-element">
                <label>Nomor Hp</label>
                <input type="tel" class="form-control" id="checkout-phone" name="phone" placeholder="Nomor Hp" required>
            </div>
            <div class="form-group checkout-small-element">
                <label>Email</label>
                <input type="text" class="form-control" id="checkout-email" name="email" placeholder="Email" required>
            </div>
            <div class="form-group checkout-large-element">
                <label>Address</label>
                <input type="text" class="form-control" id="checkout-address" name="address" placeholder="Alamat" required>
            </div>
            <!-- Bank Account Information -->
            <div class="form-group checkout-large-element">
                <label>Bank Account for Payment</label>
                <p>Please transfer the payment to: <strong><?php echo $bankNumber; ?></strong></p>
            </div>
            <!-- Shipping Options -->
<!-- Shipping Options -->
            <div class="form-group checkout-large-element">
                <label>Shipping Method</label>
                <select class="form-control" id="shipping-method" name="shipping_method" required onchange="updateTotal()">
                    <option value="" disabled selected>Select a shipping option</option>
                    <option value="10000" data-method="Grab/GoJek">Grab/GoJek - Rp 10,000</option>
                    <option value="25000" data-method="JNE">JNE - Rp 25,000</option>
                </select>
            </div>
            <!-- Payment Proof Upload -->
            <div class="form-group checkout-large-element">
                <label>Upload Payment Proof</label>
                <input type="file" class="form-control" id="payment-proof" name="payment_proof" accept="image/*" required>
            </div>
            <div class="form-group checkout-btn-container">
                <p id="total-price">Total Harga: Rp <?php echo $baseTotal; ?></p>
                <input type="hidden" id="total-price-input" name="total_price" value="<?php echo $baseTotal; ?>">
                <input type="submit" class="btn" id="checkout-btn" name="place_order" value="Place Order">
            </div>
        </form>
    </div>
</section>

<!--footer-->
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
            <h5 class="pb-2"> Email</h5>
            <p>info@email.com</p>
        </div>
    </div>
    <div class="copyright mt-5">
        <div class="row container mx-auto">
            <div class="col-lg-3 col-md-5 col-sm-12 mb-4">
            </div>
            <div class="col-lg-3 col-md-5 col-sm-12 mb-4 text-nowrap mb-2">
                <p>Lancar Diesel @ 2024 All Right Reserved</p>
            </div>
        </div>
    </div>
</footer> 

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script>
    const baseTotal = <?php echo $baseTotal; ?>;

    function updateTotal() {
        const shippingSelect = document.getElementById('shipping-method');
        const shippingCost = parseInt(shippingSelect.value) || 0;
        const shippingMethod = shippingSelect.options[shippingSelect.selectedIndex].getAttribute('data-method') || 'None';
        const total = baseTotal + shippingCost;

        document.getElementById('total-price').innerText = `Total Harga: Rp ${total} (Shipping: ${shippingMethod})`;
        document.getElementById('total-price-input').value = total;
    }
</script>

</body>
</html>

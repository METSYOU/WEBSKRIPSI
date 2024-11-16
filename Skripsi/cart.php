<?php
session_start();

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle adding to cart
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $product_quantity = $_POST['product_quantity'];

    // Check if product is already in cart
    if (array_key_exists($product_id, $_SESSION['cart'])) {
        echo '<script>alert("Product is already added to cart")</script>';
    } else {
        // Add product to cart
        $_SESSION['cart'][$product_id] = [
            'product_id' => $product_id,
            'product_name' => $_POST['product_name'],
            'product_price' => $_POST['product_price'],
            'product_image' => $_POST['product_image'],
            'product_quantity' => $product_quantity
        ];
    }
    calculateTotalCart();
}

// Handle removing a product from the cart
if (isset($_POST['remove_product'])) {
    $product_id = $_POST['product_id'];
    unset($_SESSION['cart'][$product_id]);
    calculateTotalCart();
}

// Handle updating product quantity
if (isset($_POST['edit_quantity'])) {
    $product_id = $_POST['product_id'];
    $product_quantity = $_POST['product_quantity'];

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['product_quantity'] = $product_quantity;
    }
    calculateTotalCart();
}

// Calculate total cart amount
function calculateTotalCart() {
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['product_price'] * $item['product_quantity'];
    }
    $_SESSION['total'] = $total;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous"/>
    <link rel="stylesheet" href="Assets/CSS/style.css"/>
</head>
<body> 

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
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="shop.php">Catalog</a>
                </li>
                <li class="nav-item">
                    <a href="cart.php"><i class="fas fa-shopping-cart"></i></a>
                    <a href="account.php"><i class="fas fa-user"></i></a>
                </li>
            </ul>
          </div>
        </div>
    </nav>

    <!-- Cart Section -->
    <section class="cart container my-5 py-5">
        <div class="container mt-5">
            <h2 class="font-weight-bold">Your Cart</h2>
            <hr>
        </div>

        <table class="mt-5 pt-5">
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>

            <?php 
            if (!empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $item) { 
                    $subtotal = $item['product_price'] * $item['product_quantity'];
            ?>

            <tr>
                <td>
                    <div class="product-info">
                        <img src="Assets/imgs/<?php echo $item['product_image']; ?>" alt="<?php echo $item['product_name']; ?>"/>
                        <div>
                            <p><?php echo $item['product_name']; ?></p>
                            <small><span>RP</span><?php echo $item['product_price']; ?></small>
                            <br>
                            <form method="POST" action="cart.php">
                                <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>"/>
                                <input type="submit" name="remove_product" class="remove-btn" value="Remove"/>
                            </form>
                        </div>
                    </div>
                </td>
                <td>
                    <form method="POST" action="cart.php">
                        <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>"/>
                        <input type="number" name="product_quantity" value="<?php echo $item['product_quantity']; ?>" min="1" required/>
                        <input type="submit" class="edit-btn" value="Edit" name="edit_quantity"/>
                    </form>
                </td>
                <td>
                    <span>Rp</span>
                    <span class="product-price"><?php echo $subtotal; ?></span>
                </td>
            </tr>
            <?php } } else { ?>
            <tr>
                <td colspan="3" class="text-center">Your cart is empty.</td>
            </tr>
            <?php } ?>
        </table>

        <!-- Cart Total Section -->
        <div class="cart-total">
            <table>
                <tr>
                    <td>Total</td>
                    <td>Rp <?php echo $_SESSION['total'] ?? 0; ?></td>
                </tr>
            </table>
        </div>

        <div class="checkout-container">
            <form method="POST" action="checkout.php">
                <input type="submit" class="btn checkout-btn" value="Checkout" name="checkout">
            </form>
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
</body>
</html>

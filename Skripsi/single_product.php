<?php

include('Assets/server/connection.php');

if(
    isset($_GET['products_id'])){
        $products_id = $_GET['products_id'];

        $stmt = $conn->prepare("SELECT * FROM products WHERE products_id= ?");
        $stmt->bind_param("i",$products_id);

        $stmt-> execute();

        $product = $stmt->get_result();
    } else{
      header('location: index.php');
    }
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Piston</title>

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
                <a href="cart.html"><i class="fas fa-shopping-cart"></i></a>
                <a href="account.html"><i class="fas fa-user"></i></a>
              </li>

            </ul>
          </div>
        </div>
      </nav>


  <!--SingleProduct-->
<section class="container single-product my-5 pt-5">
    <div class="row mt-5">

    <?php while($row = $product->fetch_assoc()){ ?>


        <div class= "col-lg-5 col-md-6 col-sm-12">
            <img class="img-fluid w-100" src="Assets/imgs/<?php echo $row ['products_image']; ?>" id="mainIMG"/>
        </div>

        <div class="col-lg-6 col-md-12 col-12">
            <h6>Spare Parts</h6>
            <h3 class="py-4"><?php echo $row['products_name']; ?></h3>
            <h2>$<?php echo $row['products_price'];?> </h2>

            <form method="POST" action="cart.php">
              <input type="hidden" name="product_id" value="<?php echo $row ['products_id']; ?>"/>
              <input type="hidden" name="product_image" value="<?php echo $row ['products_image']; ?>"/>
              <input type="hidden" name="product_name" value="<?php echo $row ['products_name']; ?>"/>
              <input type="hidden" name="product_price" value="<?php echo $row ['products_price']; ?>"/>

                <input type="number" name="product_quantity" value="1"/>
                <button class="buy-btn" type="submit" name="add_to_cart">Masukan ke keranjang</button>
            </form>
            <div class="col-lg-6 col-md-12 col-12">
            </div>
            <h4 class="mt-5 mb-5">Detail Product</h4>
            <span><?php echo $row['products_description'];?></span>
        </div>
      
        <?php } ?>

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
                      <li><a href="">Tank</a></li>
                      <li><a href="">Pipe</a></li>
                      <li><a href="">Cylinder</a></li>
                      <li><a href="">Knalpot</a></li>
                      <li><a href="">Nozzle</a></li>
                      <li><a href="">Plunger</a></li>
                      <li><a href="">Oil Pump</a></li>
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
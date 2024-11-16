<?php
session_start();

// Include database connection
include('Assets/server/connection.php');

// Redirect if already logged in
if (isset($_SESSION['logged_in'])) {
    header('location: account.php');
    exit;
}

if (isset($_POST['login_btn'])) {
    // Get form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare statement to check user credentials and retrieve role
    $stmt = $conn->prepare("SELECT id_user, user_name, user_email, user_password, role FROM users WHERE user_email = ? LIMIT 1");
    $stmt->bind_param('s', $email);

    if ($stmt->execute()) {
        $stmt->store_result();

        // Check if the user exists
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($user_id, $user_name, $user_email, $user_password, $role);
            $stmt->fetch();

            // Verify the password using password_verify
            if (password_verify($password, $user_password)) {
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_name'] = $user_name;
                $_SESSION['user_email'] = $user_email;
                $_SESSION['role'] = $role;
                $_SESSION['logged_in'] = true;

                // Redirect based on user role
                if ($role == 'pengunjung') {
                    header('location: account.php');
                } elseif ($role == 'admin' || $role == 'owner') {
                    header('location: dashboard.php');
                } else {
                    // Default redirection if role is not recognized
                    header('location: account.php');
                }
                exit;
            } else {
                header('location: login.php?error=Password salah');
                exit;
            }
        } else {
            header('location: login.php?error=Tidak dapat memverifikasi akun');
            exit;
        }
    } else {
        // Error handling for database execution failure
        header('location: login.php?error=Terjadi kesalahan');
        exit;
    }
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
                    <a class="nav-link" href="index.php">Home</a>
                  </li>

              <li class="nav-item">
                <a class="nav-link" href="shop.php">Catalog</a>
              </li>

              <li class="nav-item">
                <i class="fas fa-shopping-cart"></i>
                <i class="fas fa-user"></i>
              </li>

            </ul>
          </div>
        </div>
      </nav>



    <!--login-->
    <section class="my-5 py-5">
        <div class="container text-center mt-3 pt-5">
            <h2 class="form-weight-bold">Login</h2>
            <hr class="mx-auto">
        </div>
        <div class="mx-auto container">
            <form id="login-form" method='POST' action='login.php'>
                <div class="form-group">
                    <label>Email</label>
                    <input type="text" class="form-control" id="login-email" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-control" id="login-password" name="password" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn" id="login-btn" name='login_btn' value="Login">
                </div>
                <div class="form-group">
                    <a id="register-url" href="register.php" class="btn">Don't Have An Account ?</a>
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
        </body>
        </html>
<?php
session_start();

include('Assets/server/connection.php');

if (isset($_POST['register'])) {
    // Retrieve and sanitize user input
    $name = trim($_POST['name']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);
    $hp = trim($_POST['hp']);
    $alamat = trim($_POST['alamat']);
    $confirmpassword = trim($_POST['confirmPassword']);

    // Error handling
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('location: register.php?error=Invalid email format');
        exit();
    } elseif ($password !== $confirmpassword) {
        header('location: register.php?error=Password does not match');
        exit();
    } elseif (strlen($password) < 6) {
        header('location: register.php?error=Password harus lebih dari 6 huruf');
        exit();
    }

    // Check if the email is already used
    $stmt1 = $conn->prepare("SELECT COUNT(*) FROM users WHERE user_email = ?");
    $stmt1->bind_param('s', $email);
    $stmt1->execute();
    $stmt1->bind_result($num_rows);
    $stmt1->fetch();
    $stmt1->close();

    if ($num_rows != 0) {
        header('location: register.php?error=Email sudah dipakai');
        exit();
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Set default role to "pengunjung"
    $role = "pengunjung";

    // Insert new user into the database
    $stmt = $conn->prepare("INSERT INTO users (user_name, user_email, user_password, user_address, phone_number, role) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssssss", $name, $email, $hashed_password, $alamat, $hp, $role);

        // Execute the statement and check if successful
        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $name;
            $_SESSION['logged_in'] = true;
            header('location: login.php?register_success=Registrasi Berhasil, silakan login');
            exit();
        } else {
            header('location: register.php?error=Registrasi gagal, coba lagi');
            exit();
        }
    } else {
        header('location: register.php?error=Registrasi gagal, coba lagi');
        exit();
    }

    $conn->close();
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
                    <a class="nav-link" href="#">Home</a>
                  </li>

              <li class="nav-item">
                <a class="nav-link" href="#">Catalog</a>
              </li>

              <li class="nav-item">
                <i class="fas fa-shopping-cart"></i>
                <i class="fas fa-user"></i>
              </li>

            </ul>
          </div>
        </div>
      </nav>



    <!--register-->
    <section class="my-5 py-5">
        <div class="container text-center mt-3 pt-5">
            <h2 class="form-weight-bold">Register</h2>
            <hr class="mx-auto">
        </div>
        <div class="mx-auto container">
            <form id="register-form" method="POST" action="register.php">
              <p style = "color: red;"><?php if(isset($_GET['error'])){ echo $_GET['error']; }?></p>  
                <div class="form-group">
                <label>Nama</label>
                <input type="text" class="form-control" id="register-name" name="name" placeholder="Name" required>
                </div>
                <div class="form-group">
                  <label>Nomor Hp</label>
                  <input type="tel" class="form-control" id="register-hp" name="hp" placeholder="Nomor Hp" required>
                  </div>
                  <div class="form-group">
                  <label>Alamat</label>
                  <input type="text" class="form-control" id="register-alamat" name="alamat" placeholder="Alamat" required>
                  </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" id="register-email" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-control" id="register-password" name="password" placeholder="Password" required>
                </div>
                <div class="form-group">
                  <label>Confirm Password</label>
                  <input type="password" class="form-control" id="register-confirmPassword" name="confirmPassword" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn" id="register-btn" name="register" value="Register">
                </div>
                <div class="form-group">
                    <a id="login-url" href="login.php" class="btn">Have An Account</a>
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
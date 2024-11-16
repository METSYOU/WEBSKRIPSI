<?php
session_start();
include('connection.php');

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: /index.php?message=Silakan login terlebih dahulu untuk melakukan pemesanan');
    exit;
}

// Check if the user ID is set in the session
if (!isset($_SESSION['user_id'])) {
    die("Error: User ID is not set in the session.");
}

// Retrieve user data from form
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$address = $_POST['address'];
$shipping_method_value = $_POST['shipping_method']; // Shipping method from checkout form
$user_id = $_SESSION['user_id'];  // User ID should be set in session after login
$order_status = "Diproses";
$order_date = date('Y-m-d H:i:s');

// Define shipping methods
$shipping_methods = [
    "10000" => "Grab/GoJek",
    "25000" => "JNE"
];

// Get the shipping method name based on the selected value
$shipping_method_name = isset($shipping_methods[$shipping_method_value]) ? $shipping_methods[$shipping_method_value] : "Unknown";

// Payment proof upload
$payment_proof = $_FILES['payment_proof']['name'];
$payment_proof_tmp = $_FILES['payment_proof']['tmp_name'];
$payment_proof_error = $_FILES['payment_proof']['error'];
$payment_proof_size = $_FILES['payment_proof']['size'];

// Validate file upload
if ($payment_proof_error != UPLOAD_ERR_OK) {
    die("Error: Payment proof file upload failed.");
}

// Validate file type and size
$allowed_types = ['image/jpeg', 'image/png', 'image/gif']; // Allowed MIME types
if (!in_array($_FILES['payment_proof']['type'], $allowed_types)) {
    die("Error: Only JPG, PNG, and GIF files are allowed.");
}

if ($payment_proof_size > 5 * 1024 * 1024) { // Limit to 5 MB
    die("Error: File size exceeds 5 MB.");
}

// Create a unique filename to avoid collisions
$unique_filename = uniqid('payment_proof_', true) . '.' . pathinfo($payment_proof, PATHINFO_EXTENSION);
$payment_proof_folder = "Assets/uploads/" . $unique_filename;

// Check if the uploads directory exists, if not create it
if (!is_dir('Assets/uploads')) {
    mkdir('Assets/uploads', 0755, true); // Create the directory with appropriate permissions
}

// Move uploaded file to the destination folder
if (!move_uploaded_file($payment_proof_tmp, $payment_proof_folder)) {
    die("Error uploading payment proof image.");
}

// Step 1: Insert data into the pemesanan table
$stmt = $conn->prepare("INSERT INTO pemesanan (id_user, tanggal_pemesanan, order_status) VALUES (?, ?, ?)");
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

// Bind parameters and execute query
$stmt->bind_param("iss", $user_id, $order_date, $order_status);
if (!$stmt->execute()) {
    die("Error executing statement: " . $stmt->error);
}

// Get the newly inserted pemesanan ID
$id_pemesanan = $stmt->insert_id;
$stmt->close();

// Calculate the total amount from the cart (including shipping)
$total_amount = $_SESSION['total'] + (int)$shipping_method_value; // Keep the total amount calculation

// Step 2: Insert order details into the detail_pemesanan table
$insert_detail_query = "INSERT INTO detail_pemesanan (id_pemesanan, products_id, jumlah, product_name, product_image) VALUES (?, ?, ?, ?, ?)";
$stmt_detail = $conn->prepare($insert_detail_query);
if (!$stmt_detail) {
    die("Error preparing detail statement: " . $conn->error);
}

// Loop through each item in the cart and insert into detail_pemesanan
foreach ($_SESSION['cart'] as $item) {
    $products_id = $item['product_id'];
    $jumlah = $item['product_quantity'];
    $product_name = $item['product_name'];
    $product_image = $item['product_image'];

    // Bind parameters and execute for each item
    $stmt_detail->bind_param("iiiss", $id_pemesanan, $products_id, $jumlah, $product_name, $product_image);
    if (!$stmt_detail->execute()) {
        die("Error executing detail statement: " . $stmt_detail->error);
    }
}
$stmt_detail->close();

// Step 3: Insert payment details into the pembayaran table
$tanggal_pembayaran = date('Y-m-d H:i:s');
$stmt_payment = $conn->prepare("INSERT INTO pembayaran (id_pemesanan, tanggal_pembayaran, payment_proof, total_harga) VALUES (?, ?, ?, ?)");
if (!$stmt_payment) {
    die("Error preparing payment statement: " . $conn->error);
}

// Bind parameters and execute payment insertion
$stmt_payment->bind_param("isss", $id_pemesanan, $tanggal_pembayaran, $unique_filename, $total_amount);
if (!$stmt_payment->execute()) {
    die("Error executing payment statement: " . $stmt_payment->error);
}

// Get the newly inserted pembayaran ID
$id_pembayaran = $stmt_payment->insert_id;
$stmt_payment->close();

// Step 4: Insert shipment details into the pengiriman table
$tanggal_pengiriman = date('Y-m-d H:i:s');
$stmt_shipment = $conn->prepare("INSERT INTO pengiriman (id_pembayaran, tanggal_pengiriman, nama_penerima, no_telepon, alamat_penerima, jasa) VALUES (?, ?, ?, ?, ?, ?)");
if (!$stmt_shipment) {
    die("Error preparing shipment statement: " . $stmt_shipment->error);
}

// Bind parameters and execute shipment insertion, including the shipping method name (`jasa`)
$stmt_shipment->bind_param("isssss", $id_pembayaran, $tanggal_pengiriman, $name, $phone, $address, $shipping_method_name);
if (!$stmt_shipment->execute()) {
    die("Error executing shipment statement: " . $stmt_shipment->error);
}
$stmt_shipment->close();

// Clear the shopping cart after checkout
unset($_SESSION['cart']);
unset($_SESSION['total']);

// Redirect to a success page or any other desired page after checkout with a success message
header('Location: /index.php?message=Pemesanan berhasil');
exit;
?>

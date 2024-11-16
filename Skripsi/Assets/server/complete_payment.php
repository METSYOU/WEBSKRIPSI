<?php
session_start();
include('connection.php');

if (isset($_POST['submit_payment'])) {
    // Retrieve form data from the order form
    $id_pemesanan = $_POST['id_pemesanan'];
    $user_id = $_SESSION['user_id'];
    $tanggal_pembayaran = date('Y-m-d H:i:s');
    
    // Use existing order form data for shipment details
    $nama_penerima = $_POST['name'];
    $no_telepon = $_POST['phone'];
    $alamat_penerima = $_POST['address'];
    $tanggal_pengiriman = date('Y-m-d H:i:s', strtotime('+1 day')); // Set shipment date as the next day

    // Handle payment proof upload
    $upload_dir = "uploads/";
    $payment_proof = $_FILES['payment_proof']['name'];
    $temp_name = $_FILES['payment_proof']['tmp_name'];
    $proof_path = $upload_dir . basename($payment_proof);

    if (!move_uploaded_file($temp_name, $proof_path)) {
        die("Failed to upload payment proof.");
    }

    // Step 1: Insert into pembayaran table
    $stmt_payment = $conn->prepare("INSERT INTO pembayaran (id_pemesanan, tanggal_pembayaran, payment_proof) VALUES (?, ?, ?)");
    if (!$stmt_payment) {
        die("Error preparing statement for pembayaran: " . $conn->error);
    }
    $stmt_payment->bind_param("iss", $id_pemesanan, $tanggal_pembayaran, $proof_path);
    if (!$stmt_payment->execute()) {
        die("Error executing statement for pembayaran: " . $stmt_payment->error);
    }

    // Get the newly created id_pembayaran
    $id_pembayaran = $stmt_payment->insert_id;
    $stmt_payment->close();

    // Step 2: Insert into pengiriman table using order form data
    $stmt_shipment = $conn->prepare("INSERT INTO pengiriman (id_pembayaran, tanggal_pengiriman, nama_penerima, no_telepon, alamat_penerima) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt_shipment) {
        die("Error preparing statement for pengiriman: " . $conn->error);
    }
    $stmt_shipment->bind_param("issss", $id_pembayaran, $tanggal_pengiriman, $nama_penerima, $no_telepon, $alamat_penerima);
    if (!$stmt_shipment->execute()) {
        die("Error executing statement for pengiriman: " . $stmt_shipment->error);
    }
    $stmt_shipment->close();

    // Redirect or display success message
    header("Location: payment_success.php?status=success");
    exit();
}
?>

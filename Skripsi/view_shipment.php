<?php
session_start();
include('Assets/server/connection.php');

// Check if id_pembayaran is provided in the URL
if (isset($_GET['id_pembayaran'])) {
    $id_pembayaran = $_GET['id_pembayaran'];

    // Fetch shipment details from the pengiriman table for the given id_pembayaran
    $query = "SELECT * FROM pengiriman WHERE id_pembayaran = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_pembayaran);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $shipment = $result->fetch_assoc();
    } else {
        // If no shipment info found
        $error_message = "No shipment information found for this payment.";
    }

    $stmt->close();
} else {
    // If id_pembayaran is missing from URL
    $error_message = "Invalid payment ID.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shipment Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php else: ?>
        <h2>Shipment Details for Payment ID: <?php echo htmlspecialchars($id_pembayaran); ?></h2>
        <table class="table table-bordered">
            <tr>
                <th>Shipment ID</th>
                <td><?php echo htmlspecialchars($shipment['id_pengiriman']); ?></td>
            </tr>
            <tr>
                <th>Payment ID</th>
                <td><?php echo htmlspecialchars($shipment['id_pembayaran']); ?></td>
            </tr>
            <tr>
                <th>Shipment Date</th>
                <td><?php echo htmlspecialchars($shipment['tanggal_pengiriman']); ?></td>
            </tr>
            <tr>
                <th>Recipient Name</th>
                <td><?php echo htmlspecialchars($shipment['nama_penerima']); ?></td>
            </tr>
            <tr>
                <th>Recipient Phone</th>
                <td><?php echo htmlspecialchars($shipment['no_telepon']); ?></td>
            </tr>
            <tr>
                <th>Recipient Address</th>
                <td><?php echo htmlspecialchars($shipment['alamat_penerima']); ?></td>
            </tr>
        </table>
        <a href="view_orders.php" class="btn btn-primary">Back to Orders</a>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

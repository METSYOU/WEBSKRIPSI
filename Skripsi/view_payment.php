<?php
session_start();
include('Assets/server/connection.php');

// Check if id_pemesanan is provided in the URL
if (isset($_GET['id_pemesanan'])) {
    $id_pemesanan = $_GET['id_pemesanan'];

    // Fetch payment details from the pembayaran table for the given id_pemesanan
    $query = "SELECT * FROM pembayaran WHERE id_pemesanan = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_pemesanan);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $payment = $result->fetch_assoc();
    } else {
        echo "<p>No payment information found for this order.</p>";
        exit();
    }

    $stmt->close();
} else {
    echo "<p>Invalid order ID.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h2>Payment Details for Order ID: <?php echo htmlspecialchars($id_pemesanan); ?></h2>
    <table class="table table-bordered">
        <tr>
            <th>Payment ID</th>
            <td><?php echo htmlspecialchars($payment['id_pembayaran']); ?></td>
        </tr>
        <tr>
            <th>Order ID</th>
            <td><?php echo htmlspecialchars($payment['id_pemesanan']); ?></td>
        </tr>
        <tr>
            <th>Payment Date</th>
            <td><?php echo htmlspecialchars($payment['tanggal_pembayaran']); ?></td>
        </tr>
        <tr>
            <th>Total Price</th>
            <td><?php echo htmlspecialchars($payment['total_harga']); ?></td>
        </tr>
        <tr>
            <th>Payment Proof</th>
            <td>
                <?php if (!empty($payment['payment_proof'])): ?>
                    <img src="Assets\server\Assets\uploads\<?php echo htmlspecialchars($payment['payment_proof']); ?>" alt="Payment Proof" style="max-width: 100%; height: auto;">
                <?php else: ?>
                    No proof of payment uploaded.
                <?php endif; ?>
            </td>
        </tr>
    </table>
    <a href="view_orders.php" class="btn btn-primary">Back to Orders</a>
    <a href="view_shipment.php?id_pembayaran=<?php echo htmlspecialchars($payment['id_pembayaran']); ?>" class="btn btn-secondary">View Shipment</a> <!-- Updated button for shipment -->
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
session_start();
include('Assets/server/connection.php');

// Check if the user is logged in and has the correct role (admin or owner)
if (!isset($_SESSION['logged_in']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'owner')) {
    header('location: login.php');
    exit();
}

// Initialize variables to store error and success messages
$error = "";
$success = "";

// Check if machine ID is provided
if (!isset($_GET['machine_id'])) {
    header('location: manage_tipe.php');
    exit();
}

$machine_id = $_GET['machine_id'];

// Fetch the machine details for editing
$query = "SELECT * FROM diesel_machine WHERE machine_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $machine_id);
$stmt->execute();
$result = $stmt->get_result();
$machine = $result->fetch_assoc();
$stmt->close();

if (!$machine) {
    header('location: manage_tipe.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $brand_id = $_POST['brand_id'];
    $machine_name = $_POST['machine_name'];
    $machine_description = $_POST['machine_description'];

    // Update the machine in the database
    $update_query = "UPDATE diesel_machine SET brand_id = ?, machine_name = ?, machine_description = ? WHERE machine_id = ?";
    $stmt = $conn->prepare($update_query);

    if ($stmt) {
        $stmt->bind_param("issi", $brand_id, $machine_name, $machine_description, $machine_id);
        if ($stmt->execute()) {
            $success = "Machine updated successfully.";
            header("Location: manage_tipe.php");
            exit();
        } else {
            $error = "Error updating machine: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Error preparing statement: " . $conn->error;
    }
}

// Fetch all brands for dropdown
$brands_query = "SELECT brand_id, brand_name FROM brand"; // Assuming you have a brands table
$brands_result = $conn->query($brands_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Machine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h2>Edit Machine</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php elseif (!empty($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <!-- Machine Form -->
    <form method="POST" action="">
        <div class="mb-3">
            <label for="brand_id" class="form-label">Brand</label>
            <select class="form-control" id="brand_id" name="brand_id" required>
                <option value="">Select Brand</option>
                <?php while ($brand = $brands_result->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($brand['brand_id']); ?>" <?php echo ($brand['brand_id'] == $machine['brand_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($brand['brand_name']); ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="machine_name" class="form-label">Machine Name</label>
            <input type="text" class="form-control" id="machine_name" name="machine_name" value="<?php echo htmlspecialchars($machine['machine_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="machine_description" class="form-label">Machine Description</label>
            <textarea class="form-control" id="machine_description" name="machine_description" rows="3" required><?php echo htmlspecialchars($machine['machine_description']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update Machine</button>
        <a href="manage_tipe.php" class="btn btn-secondary">Back to List</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
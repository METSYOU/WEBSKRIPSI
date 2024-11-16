<?php
session_start();
include('Assets/server/connection.php');

// Check if the user is logged in and has the correct role (admin or owner)
if (!isset($_SESSION['logged_in']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'owner')) {
    // Redirect to login page if not logged in or unauthorized
    header('location: login.php');
    exit();
}

// Initialize variables to store error and success messages
$error = "";
$success = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve diesel machine data from the form
    $brand_id = $_POST['brand_id'];
    $machine_name = $_POST['machine_name'];
    $machine_description = $_POST['machine_description'];

    // Validate input
    if (empty($brand_id) || empty($machine_name) || empty($machine_description)) {
        $error = "Please fill in all fields.";
    } else {
        // Insert new machine into the diesel_machine table
        $query = "INSERT INTO diesel_machine (brand_id, machine_name, machine_description) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("iss", $brand_id, $machine_name, $machine_description);

            // Execute and check if insertion was successful
            if ($stmt->execute()) {
                $success = "Diesel machine added successfully.";
            } else {
                $error = "Error adding machine: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $error = "Error preparing statement: " . $conn->error;
        }
    }
}

// Fetch existing diesel machines from the diesel_machine table
$query = "SELECT dm.machine_id, dm.brand_id, dm.machine_name, dm.machine_description FROM diesel_machine dm";
$result = $conn->query($query);

// Fetch all brands for the dropdown in the form
$brands_query = "SELECT brand_id, brand_name FROM brand";
$brands_result = $conn->query($brands_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Diesel Machine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h2>Add New Diesel Machine</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php elseif (!empty($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <!-- Diesel Machine Form -->
    <form method="POST" action="tambah_tipe.php">
        <div class="mb-3">
            <label for="brand_id" class="form-label">Brand</label>
            <select class="form-control" id="brand_id" name="brand_id" required>
                <option value="">Select Brand</option>
                    <?php while ($brand = $brands_result->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($brand['brand_id']); ?>">
                    <?php echo htmlspecialchars($brand['brand_id'] . ' - ' . $brand['brand_name']); ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="machine_name" class="form-label">Machine Name</label>
            <input type="text" class="form-control" id="machine_name" name="machine_name" required>
        </div>
        <div class="mb-3">
            <label for="machine_description" class="form-label">Machine Description</label>
            <textarea class="form-control" id="machine_description" name="machine_description" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Add Machine</button>
        <a href="manage_tipe.php" class="btn btn-secondary">Back to List</a>
    </form>

    <!-- Display Existing Diesel Machines -->
    <h3 class="mt-5">Existing Diesel Machines</h3>
    <table class="table table-bordered table-hover mt-3">
        <thead class="table-light">
            <tr>
                <th>Machine ID</th>
                <th>Brand ID</th>
                <th>Machine Name</th>
                <th>Machine Description</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['machine_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['brand_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['machine_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['machine_description']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">No diesel machines found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

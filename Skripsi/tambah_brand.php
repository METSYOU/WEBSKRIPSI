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
    // Retrieve brand data from the form
    $brand_name = $_POST['brand_name'];
    $brand_description = $_POST['brand_description'];
    $brand_img = ""; // Initialize brand_img variable

    // Validate input
    if (empty($brand_name) || empty($brand_description)) {
        $error = "Please fill in all fields.";
    } else {
        // Handle image upload
        if (isset($_FILES['brand_img']) && $_FILES['brand_img']['error'] == UPLOAD_ERR_OK) {
            $target_dir = "Assets/imgs/";
            $image_name = basename($_FILES["brand_img"]["name"]);
            $target_file = $target_dir . $image_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Validate file type
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($imageFileType, $allowed_types)) {
                // Move the uploaded file to the target directory
                if (move_uploaded_file($_FILES["brand_img"]["tmp_name"], $target_file)) {
                    $brand_img = $image_name;
                } else {
                    $error = "Error uploading the image.";
                }
            } else {
                $error = "Only JPG, JPEG, PNG & GIF files are allowed.";
            }
        }

        // Insert new brand into the brand table
        if (empty($error)) {
            $query = "INSERT INTO brand (brand_name, brand_description, brand_img) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            
            if ($stmt) {
                $stmt->bind_param("sss", $brand_name, $brand_description, $brand_img);

                // Execute and check if insertion was successful
                if ($stmt->execute()) {
                    $success = "Brand added successfully.";
                } else {
                    $error = "Error adding brand: " . $stmt->error;
                }

                $stmt->close();
            } else {
                $error = "Error preparing statement: " . $conn->error;
            }
        }
    }
}

// Fetch existing brands from the brand table
$query = "SELECT brand_id, brand_name, brand_description, brand_img FROM brand";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Brand</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h2>Add New Brand</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php elseif (!empty($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <!-- Brand Form -->
    <form method="POST" action="tambah_brand.php" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="brand_name" class="form-label">Brand Name</label>
            <input type="text" class="form-control" id="brand_name" name="brand_name" required>
        </div>
        <div class="mb-3">
            <label for="brand_description" class="form-label">Brand Description</label>
            <textarea class="form-control" id="brand_description" name="brand_description" rows="3" required></textarea>
        </div>
        <div class="mb-3">
            <label for="brand_img" class="form-label">Brand Image</label>
            <input type="file" class="form-control" id="brand_img" name="brand_img" accept="image/*" required>
            <small class="form-text text-muted">Upload an image for the brand (JPG, JPEG, PNG, GIF).</small>
        </div>
        <button type="submit" class="btn btn-primary">Add Brand</button>
        <a href="manage_brand.php" class="btn btn-secondary">Back to Brand List</a>
    </form>

    <!-- Display Existing Brands -->
    <h3 class="mt-5">Existing Brands</h3>
    <table class="table table-bordered table-hover mt-3">
        <thead class="table-light">
            <tr>
                <th>Brand ID</th>
                <th>Brand Name</th>
                <th>Brand Description</th>
                <th>Brand Image</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['brand_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['brand_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['brand_description']); ?></td>
                        <td>
                            <?php if ($row['brand_img']): ?>
                                <img class="img-fluid mb-3" src="Assets/imgs/<?php echo htmlspecialchars($row['brand_img']); ?>" alt="Brand Image" style="width: 100px; height: auto;">
                            <?php else: ?>
                                No image
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">No brands found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

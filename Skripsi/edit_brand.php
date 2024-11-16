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

// Check if brand ID is provided
if (!isset($_GET['brand_id'])) {
    header('location: manage_brand.php'); // Redirect to the brand management page
    exit();
}

$brand_id = $_GET['brand_id'];

// Fetch the brand details for editing
$query = "SELECT * FROM brand WHERE brand_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $brand_id);
$stmt->execute();
$result = $stmt->get_result();
$brand = $result->fetch_assoc();
$stmt->close();

if (!$brand) {
    header('location: manage_brand.php'); // Redirect if brand not found
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $brand_name = $_POST['brand_name'];
    $brand_description = $_POST['brand_description'];
    $brand_img = $brand['brand_img']; // Default to existing image

    // Check if a new image is uploaded
    if (isset($_FILES['brand_img']) && $_FILES['brand_img']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "Assets/imgs/";
        $target_file = $target_dir . basename($_FILES["brand_img"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate file type
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $allowed_types)) {
            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES["brand_img"]["tmp_name"], $target_file)) {
                $brand_img = basename($_FILES["brand_img"]["name"]); // Save only the image name
            } else {
                $error = "Error uploading the image.";
            }
        } else {
            $error = "Only JPG, JPEG, PNG & GIF files are allowed.";
        }
    }

    // Update the brand in the database
    $update_query = "UPDATE brand SET brand_name = ?, brand_description = ?, brand_img = ? WHERE brand_id = ?";
    $stmt = $conn->prepare($update_query);

    if ($stmt) {
        $stmt->bind_param("sssi", $brand_name, $brand_description, $brand_img, $brand_id);
        if ($stmt->execute()) {
            $success = "Brand updated successfully.";
            header("Location: manage_brand.php"); // Redirect to the brand management page
            exit();
        } else {
            $error = "Error updating brand: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Error preparing statement: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Brand</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h2>Edit Brand</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php elseif (!empty($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <!-- Brand Form -->
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="brand_name" class="form-label">Brand Name</label>
            <input type="text" class="form-control" id="brand_name" name="brand_name" value="<?php echo htmlspecialchars($brand['brand_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="brand_description" class="form-label">Brand Description</label>
            <textarea class="form-control" id="brand_description" name="brand_description" rows="3" required><?php echo htmlspecialchars($brand['brand_description']); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="brand_img" class="form-label">Brand Image</label>
            <input type="file" class="form-control" id="brand_img" name="brand_img" accept="image/*">
            <small class="form-text text-muted">Leave blank to keep the current image.</small>
            <?php if ($brand['brand_img']): ?>
                <img class="img-fluid mb-3" src="Assets/imgs/<?php echo htmlspecialchars($brand['brand_img']); ?>" alt="Current Brand Image" style="width: 100px; height: auto; margin-top: 10px;">
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Update Brand</button>
        <a href="manage_brand.php" class="btn btn-secondary">Back to List</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

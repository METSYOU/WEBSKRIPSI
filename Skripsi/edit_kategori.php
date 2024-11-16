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

// Check if category ID is provided
if (!isset($_GET['category_id'])) {
    header('location: manage_kategori.php');
    exit();
}

$category_id = $_GET['category_id'];

// Fetch the category details for editing
$query = "SELECT * FROM category WHERE category_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();
$stmt->close();

if (!$category) {
    header('location: manage_kategori.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $machine_id = $_POST['machine_id'];
    $category_name = $_POST['category_name'];
    $category_description = $_POST['category_description'];
    $category_img = $_FILES['category_img'];

    $uploadOk = 1;
    $new_image_name = $category['category_img']; // Default to the current image

    // Handle file upload if a new image is provided
    if (!empty($category_img['name'])) {
        $target_dir = "Assets/imgs/";
        $original_filename = basename($category_img["name"]);
        $imageFileType = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));

        // Sanitize and ensure unique filename
        $sanitized_filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $original_filename);
        $target_file = $target_dir . $sanitized_filename;
        $i = 0;
        while (file_exists($target_file)) {
            $i++;
            $sanitized_filename = pathinfo($original_filename, PATHINFO_FILENAME) . "_$i." . $imageFileType;
            $target_file = $target_dir . $sanitized_filename;
        }

        // Validate the file
        $check = getimagesize($category_img["tmp_name"]);
        if ($check === false) {
            $error = "File is not an image.";
            $uploadOk = 0;
        }
        if ($category_img["size"] > 5000000) {
            $error = "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Attempt to upload file
        if ($uploadOk == 1) {
            if (move_uploaded_file($category_img["tmp_name"], $target_file)) {
                $new_image_name = $sanitized_filename;
            } else {
                $error = "Sorry, there was an error uploading your file.";
                $uploadOk = 0;
            }
        }
    }

    if ($uploadOk == 1) {
        // Update the category in the database
        $update_query = "UPDATE category SET machine_id = ?, category_name = ?, category_description = ?, category_img = ? WHERE category_id = ?";
        $stmt = $conn->prepare($update_query);

        if ($stmt) {
            $stmt->bind_param("isssi", $machine_id, $category_name, $category_description, $new_image_name, $category_id);
            if ($stmt->execute()) {
                $success = "Category updated successfully.";
                header("Location: manage_kategori.php");
                exit();
            } else {
                $error = "Error updating category: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Error preparing statement: " . $conn->error;
        }
    }
}

// Fetch all machines for dropdown
$machines_query = "SELECT machine_id, machine_name FROM diesel_machine";
$machines_result = $conn->query($machines_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h2>Edit Category</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php elseif (!empty($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <!-- Category Form -->
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="machine_id" class="form-label">Machine</label>
            <select class="form-control" id="machine_id" name="machine_id" required>
                <option value="">Select Machine</option>
                <?php while ($machine = $machines_result->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($machine['machine_id']); ?>" <?php echo ($machine['machine_id'] == $category['machine_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($machine['machine_id'] . ' - ' . $machine['machine_name']); ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="category_name" class="form-label">Category Name</label>
            <input type="text" class="form-control" id="category_name" name="category_name" value="<?php echo htmlspecialchars($category['category_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="category_description" class="form-label">Category Description</label>
            <textarea class="form-control" id="category_description" name="category_description" rows="3" required><?php echo htmlspecialchars($category['category_description']); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="category_img" class="form-label">Category Image</label>
            <input type="file" class="form-control" id="category_img" name="category_img">
            <img src="Assets/imgs/<?php echo htmlspecialchars($category['category_img']); ?>" alt="<?php echo htmlspecialchars($category['category_name']); ?>" style="width: 100px; height: auto;" class="mt-2">
        </div>
        <button type="submit" class="btn btn-primary">Update Category</button>
        <a href="manage_categories.php" class="btn btn-secondary">Back to List</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

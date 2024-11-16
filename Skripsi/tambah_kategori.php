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
    // Retrieve category data from the form
    $machine_id = $_POST['machine_id']; // Assuming you have a foreign key for machine_id
    $category_name = $_POST['category_name'];
    $category_description = $_POST['category_description'];
    
    // Handle file upload
    $category_image = $_FILES['category_image'];
    $target_dir = "Assets/imgs/"; // Corrected path to the target directory
    $target_file = $target_dir . basename($category_image["name"]); // Full path for uploading
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($category_image["tmp_name"]);
    if ($check === false) {
        $error = "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size (limit to 5MB)
    if ($category_image["size"] > 5000000) {
        $error = "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
        $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        // Do not attempt to upload the file
    } else {
        // If everything is ok, try to upload file
        if (move_uploaded_file($category_image["tmp_name"], $target_file)) {
            // Insert new category into the category table
            $query = "INSERT INTO category (machine_id, category_name, category_description, category_img) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            
            if ($stmt) {
                // Store only the image filename in the database
                $stmt->bind_param("isss", $machine_id, $category_name, $category_description, $category_image["name"]);

                // Execute and check if insertion was successful
                if ($stmt->execute()) {
                    $success = "Category added successfully.";
                    // Redirect to the same page to prevent resubmission
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                } else {
                    $error = "Error adding category: " . $stmt->error;
                }

                $stmt->close();
            } else {
                $error = "Error preparing statement: " . $conn->error;
            }
        } else {
            $error = "Sorry, there was an error uploading your file.";
        }
    }
}

// Fetch existing categories from the category table
$query = "SELECT category_id, machine_id, category_name, category_description, category_img FROM category";
$result = $conn->query($query);

// Fetch all machines for the dropdown in the form
$machines_query = "SELECT machine_id, machine_name FROM diesel_machine"; // Assuming this table exists
$machines_result = $conn->query($machines_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Category</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h2>Add New Category</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php elseif (!empty($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <!-- Category Form -->
    <form method="POST" action="tambah_kategori.php" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="machine_id" class="form-label">Machine</label>
            <select class="form-control" id="machine_id" name="machine_id" required>
                <option value="">Select Machine</option>
                <?php while ($machine = $machines_result->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($machine['machine_id']); ?>">
                    <?php echo htmlspecialchars($machine['machine_id'] . ' - ' . $machine['machine_name']); ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="category_name" class="form-label">Category Name</label>
            <input type="text" class="form-control" id="category_name" name="category_name" required>
        </div>
        <div class="mb-3">
            <label for="category_description" class="form-label">Category Description</label>
            <textarea class="form-control" id="category_description" name="category_description" rows="3" required></textarea>
        </div>
        <div class="mb-3">
            <label for="category_image" class="form-label">Category Image</label>
            <input type="file" class="form-control" id="category_image" name="category_image" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Category</button>
        <a href="manage_kategori.php" class="btn btn-secondary">Back to List</a>
    </form>

    <!-- Display Existing Categories -->
    <h3 class="mt-5">Existing Categories</h3>
    <table class="table table-bordered table-hover mt-3">
        <thead class="table-light">
            <tr>
                <th>Category ID</th>
                <th>Machine ID</th>
                <th>Category Name</th>
                <th>Category Description</th>
                <th>Category Image</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['category_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['machine_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['category_description']); ?></td>
                        <td style="text-align: center;">
                            <img class="img-fluid mb-3" src="Assets/imgs/<?php echo htmlspecialchars($row['category_img']); ?>" style="width: 100px; height: auto;">
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">No categories found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
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
    // Retrieve product data from the form
    $category_id = $_POST['category_id']; // Foreign key
    $ukuranid = $_POST['ukuranid'];       // Foreign key
    $products_name = $_POST['products_name'];
    $products_description = $_POST['products_description'];
    $products_price = $_POST['products_price'];

    // Handle file upload
    $products_image = $_FILES['products_image'];
    $target_dir = "Assets/imgs/"; // Corrected path to the target directory
    $original_filename = basename($products_image["name"]);
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

    $uploadOk = 1;

    // Check if image file is a valid image
    $check = getimagesize($products_image["tmp_name"]);
    if ($check === false) {
        $error = "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size (limit to 5MB)
    if ($products_image["size"] > 5000000) {
        $error = "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
        $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 1) {
        if (move_uploaded_file($products_image["tmp_name"], $target_file)) {
            // Insert new product into the products table
            $query = "INSERT INTO products (category_id, ukuranid, products_name, products_description, products_image, products_price) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            
            if ($stmt) {
                $stmt->bind_param("iissss", $category_id, $ukuranid, $products_name, $products_description, $sanitized_filename, $products_price);

                // Execute and check if insertion was successful
                if ($stmt->execute()) {
                    $success = "Product added successfully.";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                } else {
                    $error = "Error adding product: " . $stmt->error;
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

// Fetch existing products from the products table
$query = "SELECT products_id, category_id, ukuranid, products_name, products_description, products_image, products_price FROM products";
$result = $conn->query($query);

// Fetch all categories for the dropdown in the form
$categories_query = "SELECT category_id, category_name FROM category";
$categories_result = $conn->query($categories_query);

// Fetch all sizes for the dropdown in the form
$sizes_query = "SELECT ukuranid, detailukuran FROM ukuran";
$sizes_result = $conn->query($sizes_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h2>Add New Product</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php elseif (!empty($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <!-- Product Form -->
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="category_id" class="form-label">Category</label>
            <select class="form-control" id="category_id" name="category_id" required>
                <option value="">Select Category</option>
                <?php while ($category = $categories_result->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($category['category_id']); ?>">
                    <?php echo htmlspecialchars($category['category_id'] . ' - ' . $category['category_name']); ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="ukuranid" class="form-label">Size</label>
            <select class="form-control" id="ukuranid" name="ukuranid" required>
                <option value="">Select Size</option>
                <?php while ($size = $sizes_result->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($size['ukuranid']); ?>">
                    <?php echo htmlspecialchars($size['ukuranid'] . ' - ' . $size['detailukuran']); ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="products_name" class="form-label">Product Name</label>
            <input type="text" class="form-control" id="products_name" name="products_name" required>
        </div>
        <div class="mb-3">
            <label for="products_description" class="form-label">Product Description</label>
            <textarea class="form-control" id="products_description" name="products_description" rows="3" required></textarea>
        </div>
        <div class="mb-3">
            <label for="products_price" class="form-label">Product Price</label>
            <input type="number" class="form-control" id="products_price" name="products_price" step="0.01" required>
        </div>
        <div class="mb-3">
            <label for="products_image" class="form-label">Product Image</label>
            <input type="file" class="form-control" id="products_image" name="products_image" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Product</button>
        <a href="manage_products.php" class="btn btn-secondary">Back to List</a>
    </form>

    <!-- Display Existing Products -->
    <h3 class="mt-5">Existing Products</h3>
    <table class="table table-bordered table-hover mt-3">
        <thead class="table-light">
            <tr>
                <th>Product ID</th>
                <th>Category ID</th>
                <th>Size ID</th>
                <th>Product Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Image</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['products_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['category_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['ukuranid']); ?></td>
                        <td><?php echo htmlspecialchars($row['products_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['products_description']); ?></td>
                        <td><?php echo htmlspecialchars($row['products_price']); ?></td>
                        <td>
                            <img src="Assets/imgs/<?php echo htmlspecialchars($row['products_image']); ?>" alt="<?php echo htmlspecialchars($row['products_name']); ?>" style="width: 100px; height: auto;">
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No products found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

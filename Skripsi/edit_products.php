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

// Check if product ID is provided
if (!isset($_GET['products_id'])) {
    header('location: manage_products.php');
    exit();
}

$products_id = $_GET['products_id'];

// Fetch the product details for editing
$query = "SELECT * FROM products WHERE products_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $products_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    header('location: manage_products.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_id = $_POST['category_id'];
    $ukuranid = $_POST['ukuranid'];
    $products_name = $_POST['products_name'];
    $products_description = $_POST['products_description'];
    $products_price = $_POST['products_price'];
    $products_image = $_FILES['products_image'];

    $uploadOk = 1;
    $new_image_name = $product['products_image']; // Default to the current image

    // Handle file upload if a new image is provided
    if (!empty($products_image['name'])) {
        $target_dir = "Assets/imgs/";
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

        // Validate the file
        $check = getimagesize($products_image["tmp_name"]);
        if ($check === false) {
            $error = "File is not an image.";
            $uploadOk = 0;
        }
        if ($products_image["size"] > 5000000) {
            $error = "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Attempt to upload file
        if ($uploadOk == 1) {
            if (move_uploaded_file($products_image["tmp_name"], $target_file)) {
                $new_image_name = $sanitized_filename;
            } else {
                $error = "Sorry, there was an error uploading your file.";
                $uploadOk = 0;
            }
        }
    }

    if ($uploadOk == 1) {
        // Update the product in the database
        $update_query = "UPDATE products SET category_id = ?, ukuranid = ?, products_name = ?, products_description = ?, products_image = ?, products_price = ? WHERE products_id = ?";
        $stmt = $conn->prepare($update_query);

        if ($stmt) {
            $stmt->bind_param("iissssi", $category_id, $ukuranid, $products_name, $products_description, $new_image_name, $products_price, $products_id);
            if ($stmt->execute()) {
                $success = "Product updated successfully.";
                header("Location: manage_products.php");
                exit();
            } else {
                $error = "Error updating product: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Error preparing statement: " . $conn->error;
        }
    }
}

// Fetch all categories and sizes for dropdowns
$categories_query = "SELECT category_id, category_name FROM category";
$categories_result = $conn->query($categories_query);

$sizes_query = "SELECT ukuranid, detailukuran FROM ukuran";
$sizes_result = $conn->query($sizes_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h2>Edit Product</h2>

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
                <option value="<?php echo htmlspecialchars($category['category_id']); ?>" <?php echo ($category['category_id'] == $product['category_id']) ? 'selected' : ''; ?>>
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
                <option value="<?php echo htmlspecialchars($size['ukuranid']); ?>" <?php echo ($size['ukuranid'] == $product['ukuranid']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($size['ukuranid'] . ' - ' . $size['detailukuran']); ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="products_name" class="form-label">Product Name</label>
            <input type="text" class="form-control" id="products_name" name="products_name" value="<?php echo htmlspecialchars($product['products_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="products_description" class="form-label">Product Description</label>
            <textarea class="form-control" id="products_description" name="products_description" rows="3" required><?php echo htmlspecialchars($product['products_description']); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="products_price" class="form-label">Product Price</label>
            <input type="number" class="form-control" id="products_price" name="products_price" step="0.01" value="<?php echo htmlspecialchars($product['products_price']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="products_image" class="form-label">Product Image</label>
            <input type="file" class="form-control" id="products_image" name="products_image">
            <img src="Assets/imgs/<?php echo htmlspecialchars($product['products_image']); ?>" alt="<?php echo htmlspecialchars($product['products_name']); ?>" style="width: 100px; height: auto;" class="mt-2">
        </div>
        <button type="submit" class="btn btn-primary">Update Product</button>
        <a href="manage_products.php" class="btn btn-secondary">Back to List</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

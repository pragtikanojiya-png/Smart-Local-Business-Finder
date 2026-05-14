<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

requireRole('seller');
$seller_id = $_SESSION['user_id'];
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id <= 0) {
    header("Location: dashboard.php");
    exit();
}

// Fetch existing product and ensure it belongs to this seller
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND seller_id = ?");
$stmt->bind_param("ii", $product_id, $seller_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: dashboard.php");
    exit();
}

$product = $result->fetch_assoc();
$cat_res = $conn->query("SELECT * FROM categories");

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = trim($_POST['product_name']);
    $category = $_POST['category'];
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $location = trim($_POST['location']);
    
    // Handle Image Upload
    $image_name = $product['image']; // Keep old image by default
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $target_dir = "uploads/";
        $image_ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $new_image_name = uniqid() . '.' . $image_ext;
        $target_file = $target_dir . $new_image_name;
        
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array(strtolower($image_ext), $allowed_types)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                // Delete old image if it exists
                if (!empty($image_name) && file_exists($target_dir . $image_name)) {
                    unlink($target_dir . $image_name);
                }
                $image_name = $new_image_name;
            }
        } else {
            $error = "Only JPG, JPEG, PNG, GIF, WEBP files are allowed.";
        }
    }

    if (empty($error)) {
        if (empty($product_name) || empty($category) || empty($price) || empty($location)) {
            $error = "Please fill all required fields.";
        } else {
            $upd_stmt = $conn->prepare("UPDATE products SET product_name=?, category=?, description=?, price=?, image=?, location=? WHERE id=? AND seller_id=?");
            $upd_stmt->bind_param("sssdssii", $product_name, $category, $description, $price, $image_name, $location, $product_id, $seller_id);
            
            if ($upd_stmt->execute()) {
                $success = "Product updated successfully!";
                // Update local variable to reflect new data
                $product['product_name'] = $product_name;
                $product['category'] = $category;
                $product['description'] = $description;
                $product['price'] = $price;
                $product['location'] = $location;
                $product['image'] = $image_name;
            } else {
                $error = "Error updating product.";
            }
        }
    }
}
include 'includes/header.php';
?>

<div class="container">
    <div class="dashboard-layout">
        <aside class="dashboard-sidebar">
            <h3 style="margin-bottom: 1rem; color: var(--text-main);">Seller Dashboard</h3>
            <ul class="dashboard-nav">
                <li><a href="dashboard.php">My Products</a></li>
                <li><a href="add_product.php">Add New Product</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>
        <main class="dashboard-content">
            <div class="form-container" style="margin: 0; max-width: 100%;">
                <h2>Edit Product</h2>
                
                <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <?php if($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Product Name *</label>
                        <input type="text" name="product_name" class="form-control" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="category" class="form-control" required>
                            <?php while($cat = $cat_res->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($cat['category_name']); ?>" <?php echo $product['category'] === $cat['category_name'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['category_name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Price ($) *</label>
                        <input type="number" step="0.01" name="price" class="form-control" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Location / City *</label>
                        <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($product['location']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="5"><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Update Product Image (Leave blank to keep current)</label>
                        <?php if(!empty($product['image'])): ?>
                            <div style="margin-bottom: 0.5rem;">
                                <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" style="width: 100px; border-radius: 4px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update Product</button>
                    <a href="dashboard.php" class="btn btn-secondary" style="margin-left: 1rem;">Cancel</a>
                </form>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

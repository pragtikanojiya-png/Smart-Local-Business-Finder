<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

requireRole('seller');
$seller_id = $_SESSION['user_id'];

// Get seller business location to auto-fill
$stmt_biz = $conn->prepare("SELECT city FROM businesses WHERE owner_id = ?");
$stmt_biz->bind_param("i", $seller_id);
$stmt_biz->execute();
$biz_res = $stmt_biz->get_result();
$biz_city = $biz_res->num_rows > 0 ? $biz_res->fetch_assoc()['city'] : '';

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
    $image_name = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $target_dir = "uploads/";
        $image_ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $image_name = uniqid() . '.' . $image_ext;
        $target_file = $target_dir . $image_name;
        
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array(strtolower($image_ext), $allowed_types)) {
            move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        } else {
            $error = "Only JPG, JPEG, PNG, GIF, WEBP files are allowed.";
        }
    }

    if (empty($error)) {
        if (empty($product_name) || empty($category) || empty($price) || empty($location)) {
            $error = "Please fill all required fields.";
        } else {
            $stmt = $conn->prepare("INSERT INTO products (seller_id, product_name, category, description, price, image, location) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssdss", $seller_id, $product_name, $category, $description, $price, $image_name, $location);
            
            if ($stmt->execute()) {
                $success = "Product added successfully!";
            } else {
                $error = "Error adding product.";
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
                <li><a href="add_product.php" class="active">Add New Product</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>
        <main class="dashboard-content">
            <div class="form-container" style="margin: 0; max-width: 100%;">
                <h2>Add New Product</h2>
                
                <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <?php if($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?> <a href="dashboard.php" style="font-weight:bold;text-decoration:underline;">Go to Dashboard</a></div>
                <?php endif; ?>

                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Product Name *</label>
                        <input type="text" name="product_name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="category" class="form-control" required>
                            <option value="">Select Category</option>
                            <?php while($cat = $cat_res->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($cat['category_name']); ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Price ($) *</label>
                        <input type="number" step="0.01" name="price" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Location / City *</label>
                        <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($biz_city); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="5"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Product Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </form>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<?php
require_once 'includes/db.php';
include 'includes/header.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$city = isset($_GET['city']) ? trim($_GET['city']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

// Build dynamic query
$sql = "SELECT p.*, b.business_name FROM products p LEFT JOIN businesses b ON p.seller_id = b.owner_id WHERE 1=1";
$params = [];
$types = "";

if ($q !== '') {
    $sql .= " AND (p.product_name LIKE ? OR p.description LIKE ?)";
    $q_param = "%$q%";
    $params[] = $q_param;
    $params[] = $q_param;
    $types .= "ss";
}

if ($city !== '') {
    $sql .= " AND p.location LIKE ?";
    $city_param = "%$city%";
    $params[] = $city_param;
    $types .= "s";
}

if ($category !== '') {
    $sql .= " AND p.category = ?";
    $params[] = $category;
    $types .= "s";
}

$sql .= " ORDER BY p.created_at DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Get categories for filter dropdown
$cat_res = $conn->query("SELECT * FROM categories");
?>

<div class="container">
    <h1 style="margin-bottom: 2rem;">Browse Products</h1>
    
    <form action="products.php" method="GET" class="search-form" style="margin-bottom: 3rem; background: var(--dark-card); padding: 1.5rem; border-radius: 0.5rem; border: 1px solid var(--border-color);">
        <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Search keyword..." class="form-control">
        <input type="text" name="city" value="<?php echo htmlspecialchars($city); ?>" placeholder="City..." class="form-control">
        <select name="category" class="form-control">
            <option value="">All Categories</option>
            <?php while($cat = $cat_res->fetch_assoc()): ?>
                <option value="<?php echo $cat['category_name']; ?>" <?php echo $category === $cat['category_name'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat['category_name']); ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <div class="grid">
        <?php if($result && $result->num_rows > 0): ?>
            <?php while($product = $result->fetch_assoc()): ?>
                <div class="card">
                    <img src="uploads/<?php echo htmlspecialchars($product['image'] ? $product['image'] : 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="card-img" onerror="this.src='https://source.unsplash.com/random/400x300/?product'">
                    <div class="card-body">
                        <span style="font-size: 0.8rem; background: var(--primary-color); padding: 0.2rem 0.5rem; border-radius: 1rem; color: white; display: inline-block; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($product['category']); ?></span>
                        <h3 class="card-title"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                        <p class="card-text" style="font-size: 0.9rem; margin-bottom: 0.5rem;">
                            Seller: <?php echo htmlspecialchars($product['business_name'] ?? 'Unknown'); ?> <br>
                            📍 <?php echo htmlspecialchars($product['location']); ?>
                        </p>
                        <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                        <a href="product_details.php?id=<?php echo $product['id']; ?>" class="btn btn-secondary btn-block" style="margin-top: 1rem;">View Details</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No products found matching your criteria.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

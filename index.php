<?php
require_once 'includes/db.php';
include 'includes/header.php';

// Fetch latest products
$query = "SELECT p.*, b.business_name FROM products p 
          LEFT JOIN businesses b ON p.seller_id = b.owner_id 
          ORDER BY p.created_at DESC LIMIT 6";
$result = $conn->query($query);
?>

<div class="hero">
    <div class="container">
        <h1>Find Local Businesses & Products</h1>
        <p>Support your community by shopping local. Discover exactly what you need nearby.</p>
        
        <form action="products.php" method="GET" class="search-form">
            <input type="text" name="q" placeholder="What are you looking for?" class="form-control">
            <input type="text" name="city" placeholder="City or Location" class="form-control">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>
</div>

<div class="container">
    <h2 style="text-align: center; margin-bottom: 2rem;">Explore Categories</h2>
    <div class="grid" style="margin-bottom: 4rem;">
        <?php
        $cat_query = "SELECT * FROM categories LIMIT 4";
        $cat_res = $conn->query($cat_query);
        while($cat = $cat_res->fetch_assoc()):
        ?>
        <a href="products.php?category=<?php echo urlencode($cat['category_name']); ?>" class="card" style="text-align: center; display: block; padding: 2rem;">
            <h3><?php echo htmlspecialchars($cat['category_name']); ?></h3>
        </a>
        <?php endwhile; ?>
    </div>

    <h2 style="margin-bottom: 2rem;">Latest Additions</h2>
    <div class="grid">
        <?php if($result && $result->num_rows > 0): ?>
            <?php while($product = $result->fetch_assoc()): ?>
                <div class="card">
                    <img src="uploads/<?php echo htmlspecialchars($product['image'] ? $product['image'] : 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="card-img" onerror="this.src='https://source.unsplash.com/random/400x300/?product'">
                    <div class="card-body">
                        <h3 class="card-title"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                        <p class="card-text" style="font-size: 0.9rem; margin-bottom: 0.5rem;">
                            By: <?php echo htmlspecialchars($product['business_name'] ?? 'Unknown Seller'); ?> <br>
                            📍 <?php echo htmlspecialchars($product['location']); ?>
                        </p>
                        <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                        <a href="product_details.php?id=<?php echo $product['id']; ?>" class="btn btn-secondary btn-block" style="margin-top: 1rem;">View Details</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No products found. Be the first to list one!</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

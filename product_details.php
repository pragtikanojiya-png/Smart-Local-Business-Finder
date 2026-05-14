<?php
require_once 'includes/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: products.php");
    exit();
}

$stmt = $conn->prepare("SELECT p.*, b.business_name, b.address, b.contact, u.name as seller_name, u.email as seller_email 
                        FROM products p 
                        LEFT JOIN businesses b ON p.seller_id = b.owner_id 
                        LEFT JOIN users u ON p.seller_id = u.id 
                        WHERE p.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: products.php");
    exit();
}

$product = $result->fetch_assoc();
include 'includes/header.php';
?>

<div class="container">
    <div class="product-details">
        <div>
            <img src="uploads/<?php echo htmlspecialchars($product['image'] ? $product['image'] : 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="product-img-large" onerror="this.src='https://source.unsplash.com/random/800x600/?product'">
        </div>
        <div class="product-info">
            <span style="font-size: 0.8rem; background: var(--primary-color); padding: 0.2rem 0.5rem; border-radius: 1rem; color: white; display: inline-block; margin-bottom: 1rem;"><?php echo htmlspecialchars($product['category']); ?></span>
            <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($product['product_name']); ?></h1>
            <p class="price" style="font-size: 2rem; margin-bottom: 1.5rem;">$<?php echo number_format($product['price'], 2); ?></p>
            
            <h3 style="margin-bottom: 0.5rem;">Description</h3>
            <p style="color: var(--text-muted); margin-bottom: 2rem; white-space: pre-line;"><?php echo htmlspecialchars($product['description']); ?></p>
            
            <div style="background: var(--dark-bg); padding: 1.5rem; border-radius: 0.5rem; border: 1px solid var(--border-color);">
                <h3 style="margin-bottom: 1rem;">Seller Information</h3>
                <p><strong>Business Name:</strong> <?php echo htmlspecialchars($product['business_name'] ?? 'N/A'); ?></p>
                <p><strong>Seller Name:</strong> <?php echo htmlspecialchars($product['seller_name']); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($product['location']); ?></p>
                <?php if(!empty($product['address'])): ?>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($product['address']); ?></p>
                <?php endif; ?>
                <?php if(!empty($product['contact'])): ?>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($product['contact']); ?></p>
                <?php endif; ?>
                
                <a href="mailto:<?php echo htmlspecialchars($product['seller_email']); ?>?subject=Inquiry about <?php echo urlencode($product['product_name']); ?>" class="btn btn-primary" style="margin-top: 1rem; display: inline-block;">Contact Seller via Email</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

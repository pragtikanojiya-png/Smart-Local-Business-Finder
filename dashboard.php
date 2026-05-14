<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

requireRole('seller');
$seller_id = $_SESSION['user_id'];

// Get seller's business info
$stmt = $conn->prepare("SELECT * FROM businesses WHERE owner_id = ?");
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$business = $stmt->get_result()->fetch_assoc();

// Get seller's products
$stmt_prod = $conn->prepare("SELECT * FROM products WHERE seller_id = ? ORDER BY created_at DESC");
$stmt_prod->bind_param("i", $seller_id);
$stmt_prod->execute();
$products = $stmt_prod->get_result();

include 'includes/header.php';
?>

<div class="container">
    <div class="dashboard-layout">
        <aside class="dashboard-sidebar">
            <h3 style="margin-bottom: 1rem; color: var(--text-main);">Seller Dashboard</h3>
            <ul class="dashboard-nav">
                <li><a href="dashboard.php" class="active">My Products</a></li>
                <li><a href="add_product.php">Add New Product</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>
        <main class="dashboard-content">
            <div style="background: var(--dark-card); padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 2rem; border: 1px solid var(--border-color);">
                <h2>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h2>
                <?php if($business): ?>
                    <p style="color: var(--text-muted);">Business: <strong><?php echo htmlspecialchars($business['business_name']); ?></strong> | <?php echo htmlspecialchars($business['city']); ?></p>
                <?php else: ?>
                    <p style="color: var(--danger);">Please update your business details.</p>
                <?php endif; ?>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2>My Products</h2>
                <a href="add_product.php" class="btn btn-primary">+ Add Product</a>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; background: var(--dark-card); border-radius: 0.5rem; overflow: hidden;">
                    <thead>
                        <tr style="background: rgba(0,0,0,0.2); text-align: left;">
                            <th style="padding: 1rem;">Image</th>
                            <th style="padding: 1rem;">Name</th>
                            <th style="padding: 1rem;">Category</th>
                            <th style="padding: 1rem;">Price</th>
                            <th style="padding: 1rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($products->num_rows > 0): ?>
                            <?php while($prod = $products->fetch_assoc()): ?>
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <td style="padding: 1rem;">
                                        <img src="uploads/<?php echo htmlspecialchars($prod['image'] ?: 'default.jpg'); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                    </td>
                                    <td style="padding: 1rem;"><?php echo htmlspecialchars($prod['product_name']); ?></td>
                                    <td style="padding: 1rem;"><?php echo htmlspecialchars($prod['category']); ?></td>
                                    <td style="padding: 1rem;">$<?php echo number_format($prod['price'], 2); ?></td>
                                    <td style="padding: 1rem;">
                                        <a href="edit_product.php?id=<?php echo $prod['id']; ?>" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.9rem;">Edit</a>
                                        <a href="delete_product.php?id=<?php echo $prod['id']; ?>" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.9rem;">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="padding: 1rem; text-align: center;">You haven't added any products yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

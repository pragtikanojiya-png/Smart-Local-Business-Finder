<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

requireRole('seller');
$seller_id = $_SESSION['user_id'];
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id > 0) {
    // Check ownership and get image
    $stmt = $conn->prepare("SELECT image FROM products WHERE id = ? AND seller_id = ?");
    $stmt->bind_param("ii", $product_id, $seller_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $product = $result->fetch_assoc();
        
        // Delete image file if exists
        if (!empty($product['image']) && file_exists("uploads/" . $product['image'])) {
            unlink("uploads/" . $product['image']);
        }
        
        // Delete record
        $del_stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $del_stmt->bind_param("i", $product_id);
        $del_stmt->execute();
    }
}

header("Location: dashboard.php");
exit();
?>

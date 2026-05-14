<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // If seller, get business details
    $business_name = isset($_POST['business_name']) ? trim($_POST['business_name']) : '';
    $city = isset($_POST['city']) ? trim($_POST['city']) : '';
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';

    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $error = "Please fill all required fields.";
    } elseif ($role === 'seller' && (empty($business_name) || empty($city))) {
        $error = "Sellers must provide business name and city.";
    } else {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Email already exists!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);
            
            if ($stmt->execute()) {
                $user_id = $stmt->insert_id;
                
                if ($role === 'seller') {
                    $stmt_biz = $conn->prepare("INSERT INTO businesses (owner_id, business_name, city, address) VALUES (?, ?, ?, ?)");
                    $stmt_biz->bind_param("isss", $user_id, $business_name, $city, $address);
                    $stmt_biz->execute();
                }
                
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
include 'includes/header.php';
?>

<div class="container">
    <div class="form-container">
        <h2>Register</h2>
        <?php if($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form action="" method="POST" id="registerForm">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email Address *</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password *</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Role *</label>
                <select name="role" id="roleSelect" class="form-control" required>
                    <option value="buyer">Buyer (Looking for products)</option>
                    <option value="seller">Seller (Want to list products)</option>
                </select>
            </div>

            <div id="sellerFields" style="display: none; padding: 1rem; border: 1px dashed var(--border-color); border-radius: 5px; margin-bottom: 1.5rem;">
                <h4 style="margin-bottom: 1rem;">Business Details</h4>
                <div class="form-group">
                    <label>Business Name *</label>
                    <input type="text" name="business_name" class="form-control">
                </div>
                <div class="form-group">
                    <label>City *</label>
                    <input type="text" name="city" class="form-control">
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" class="form-control"></textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Register</button>
            <p style="margin-top: 1rem; text-align: center;">Already have an account? <a href="login.php" style="color: var(--primary-color);">Login here</a></p>
        </form>
    </div>
</div>

<script>
document.getElementById('roleSelect').addEventListener('change', function() {
    const sellerFields = document.getElementById('sellerFields');
    if (this.value === 'seller') {
        sellerFields.style.display = 'block';
        sellerFields.querySelectorAll('input:not([type="hidden"])').forEach(i => i.setAttribute('required', 'required'));
        sellerFields.querySelector('textarea').removeAttribute('required'); // address optional
    } else {
        sellerFields.style.display = 'none';
        sellerFields.querySelectorAll('input').forEach(i => i.removeAttribute('required'));
    }
});
</script>

<?php include 'includes/footer.php'; ?>

<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

requireRole('admin');

// Get stats
$stats = [
    'users' => $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'],
    'businesses' => $conn->query("SELECT COUNT(*) as count FROM businesses")->fetch_assoc()['count'],
    'products' => $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count']
];

// Get recent users
$users = $conn->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 10");

include 'includes/header.php';
?>

<div class="container">
    <div class="dashboard-layout">
        <aside class="dashboard-sidebar">
            <h3 style="margin-bottom: 1rem; color: var(--text-main);">Admin Panel</h3>
            <ul class="dashboard-nav">
                <li><a href="admin.php" class="active">Overview</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>
        <main class="dashboard-content">
            <div style="background: var(--dark-card); padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 2rem; border: 1px solid var(--border-color);">
                <h2>Admin Dashboard</h2>
                <p style="color: var(--text-muted);">Manage the Smart Local Business Finder platform.</p>
            </div>

            <div class="grid" style="margin-bottom: 2rem;">
                <div class="card" style="padding: 1.5rem; text-align: center;">
                    <h3 style="color: var(--text-muted); margin-bottom: 0.5rem;">Total Users</h3>
                    <p style="font-size: 2.5rem; font-weight: bold; color: var(--primary-color);"><?php echo $stats['users']; ?></p>
                </div>
                <div class="card" style="padding: 1.5rem; text-align: center;">
                    <h3 style="color: var(--text-muted); margin-bottom: 0.5rem;">Registered Businesses</h3>
                    <p style="font-size: 2.5rem; font-weight: bold; color: var(--primary-color);"><?php echo $stats['businesses']; ?></p>
                </div>
                <div class="card" style="padding: 1.5rem; text-align: center;">
                    <h3 style="color: var(--text-muted); margin-bottom: 0.5rem;">Listed Products</h3>
                    <p style="font-size: 2.5rem; font-weight: bold; color: var(--primary-color);"><?php echo $stats['products']; ?></p>
                </div>
            </div>

            <div style="background: var(--dark-card); padding: 1.5rem; border-radius: 0.5rem; border: 1px solid var(--border-color);">
                <h3 style="margin-bottom: 1rem;">Recent Users</h3>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: rgba(0,0,0,0.2); text-align: left;">
                                <th style="padding: 1rem;">ID</th>
                                <th style="padding: 1rem;">Name</th>
                                <th style="padding: 1rem;">Email</th>
                                <th style="padding: 1rem;">Role</th>
                                <th style="padding: 1rem;">Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($user = $users->fetch_assoc()): ?>
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <td style="padding: 1rem;"><?php echo $user['id']; ?></td>
                                    <td style="padding: 1rem;"><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td style="padding: 1rem;"><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td style="padding: 1rem;"><span style="background: var(--primary-color); padding: 0.2rem 0.5rem; border-radius: 1rem; font-size: 0.8rem;"><?php echo htmlspecialchars($user['role']); ?></span></td>
                                    <td style="padding: 1rem;"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

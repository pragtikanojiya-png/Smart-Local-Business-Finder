<?php
require_once 'includes/db.php';
include 'includes/header.php';

$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // In a real app, you would send an email here using mail() or PHPMailer
    $success = true;
}
?>

<div class="container">
    <div class="form-container" style="max-width: 600px;">
        <h2 style="text-align: center; margin-bottom: 2rem;">Contact Us</h2>
        
        <?php if($success): ?>
            <div class="alert alert-success">
                Thank you for your message! We will get back to you soon.
            </div>
        <?php else: ?>
            <p style="text-align: center; margin-bottom: 2rem; color: var(--text-muted);">Have questions or feedback? Fill out the form below to reach the administration team.</p>
            <form action="" method="POST">
                <div class="form-group">
                    <label>Your Name *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Your Email *</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Subject</label>
                    <input type="text" name="subject" class="form-control">
                </div>
                <div class="form-group">
                    <label>Message *</label>
                    <textarea name="message" class="form-control" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Send Message</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

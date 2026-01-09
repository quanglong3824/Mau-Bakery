<?php
// views/profile.php
require_once 'controllers/ProfileController.php';

// Route Handler
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';
$valid_tabs = ['dashboard', 'info', 'orders', 'addresses', 'contacts', 'password'];
if (!in_array($tab, $valid_tabs)) {
    $tab = 'dashboard';
}
?>

<link rel="stylesheet" href="assets/css/profile.css">

<div class="container mt-2 mb-2">
    <div class="profile-layout">
        
        <!-- Sidebar -->
        <?php include 'views/profile/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="profile-content">
            <!-- Messages -->
            <?php if (!empty($message)): ?>
                    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 15px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                        <i class="fas fa-check-circle"></i> <?php echo $message; ?>
                    </div>
            <?php endif; ?>
            <?php if (!empty($error_msg)): ?>
                    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 15px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error_msg; ?>
                    </div>
            <?php endif; ?>

            <!-- Dynamic Tab Content -->
            <?php
            $file = "views/profile/{$tab}.php";
            if (file_exists($file)) {
                include $file;
            } else {
                echo "<p>Tab not found.</p>";
            }
            ?>
        </main>
    </div>
</div>

<script src="assets/js/profile.js"></script>
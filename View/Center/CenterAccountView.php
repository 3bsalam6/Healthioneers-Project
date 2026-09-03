<?php
function renderCenterAccountView($center, $message, $error, $modalError, $modalMessage) {
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Center Account</title>
    <link rel="stylesheet" href="/Public/css/style.css">
    <style>
        .site-header { position: sticky; top: 0; z-index: 50; background: var(--teal); color: white; padding: 25px 0; box-shadow: 0 4px 12px rgba(13,95,111,0.3); }
        .site-header .container { display:flex; justify-content:space-between; align-items:center; }
        .account-card{background:#fff;border-radius:12px;padding:2rem;box-shadow:0 6px 18px rgba(0,0,0,0.1);max-width:600px;margin:2rem auto;}
        .account-card h2{text-align:center;color:var(--teal);border-bottom:2px solid #f0f2f8;padding-bottom:.5rem;}
        .account-card form{display:grid;gap:1rem;}
        .account-card label{font-weight:600;color:#333;}
        .account-card input{width:100%;padding:12px 14px;border:1px solid #ddd;border-radius:8px;font-size:1rem;}
        .btn{background:linear-gradient(135deg,var(--teal),var(--teal-lighter));color:#fff;border:none;padding:12px 20px;border-radius:8px;font-weight:600;cursor:pointer;}
        .btn:hover{transform:translateY(-2px);}
        .large-btn{font-size:1.2rem;padding:14px 28px;margin-top:1rem;display:inline-block;}
        .modal{display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,0.55);backdrop-filter:blur(4px);}
        .modal-content{background:#fff;margin:auto;padding:2.5rem;border-radius:18px;width:95%;max-width:520px;box-shadow:0 12px 28px rgba(0,0,0,0.25);position:relative;top:50%;transform:translateY(-50%);}
        .close{position:absolute;top:16px;right:20px;font-size:1.6rem;cursor:pointer;color:#888;}
        .close:hover{color:var(--teal);}
        .password-field{display:flex;align-items:center;position:relative;}
        .password-field input{flex:1;padding:14px 16px;border:1px solid #ccc;border-radius:10px;font-size:1rem;background:#fafafa;}
        .toggle-eye{margin-left:-40px;cursor:pointer;font-size:1.3rem;color:#666;}
        .toggle-eye:hover{color:var(--teal);}
        .error{color:#d9534f;font-weight:bold;}
        .message{color:#28a745;font-weight:bold;}
        .main-nav a { color:#fff; margin-left:16px; text-decoration:none; font-weight:bold; }
    </style>
</head>
<body>
<header class="site-header">
    <div class="container">
        <div class="logo">
            <img src="/Public/imgs/WhatsApp Image 2025-09-11 at 15.19.37_d6b5bce8.jpg" alt="Healthineers Logo">
            <a href="CenterDashboardController.php">Healthioneers</a>
        </div>
        <nav class="main-nav">
            <a href="CenterDashboardController.php">Home</a>
            <div class="dropdown">
                <button class="dropbtn">Account ▾</button>
                <div class="dropdown-content">
                    <a href="CenterAccountController.php">My Account</a>
                    <a href="../logout.php">Logout</a>
                </div>
            </div>
        </nav>
        <button class="nav-toggle">☰</button>
    </div>
</header>

<div class="account-card">
    <h2>Center Account</h2>

    <?php if (!empty($message)): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if ($center): ?>
        <form method="POST">
            <input type="hidden" name="update" value="1">
            <label>Center Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($center['Center_Name']); ?>" required>
            <label>Username (Email)</label>
            <input type="text" name="username" value="<?= htmlspecialchars($center['Center_Username']); ?>" required>
            <label>Address</label>
            <input type="text" name="address" value="<?= htmlspecialchars($center['Center_Address']); ?>" required>
            <label>Contact Number</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($center['Center_Contact_No']); ?>">
            <label>Password</label>
            <input type="password" value="********" readonly>
            <button type="button" class="btn large-btn" id="openPasswordModal">Change Password</button>
            <button type="submit" class="btn">Update Account</button>
        </form>
    <?php else: ?>
        <p style="color:red; text-align:center;">No center data found. Please log in again.</p>
    <?php endif; ?>
</div>

<!-- Password Modal -->
<div id="passwordModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Change Password</h3>
        <?php if ($modalError): ?><p class="error"><?= $modalError ?></p><?php endif; ?>
        <?php if ($modalMessage): ?><p class="message"><?= $modalMessage ?></p><?php endif; ?>
        <form method="POST">
            <input type="hidden" name="change_password" value="1">
            <div class="password-field">
                <label>Old Password</label>
                <input type="password" name="old_password" id="oldPassword" required>
                <span class="toggle-eye" onclick="togglePassword('oldPassword')">👁</span>
            </div>
            <div class="password-field">
                <label>New Password</label>
                <input type="password" name="new_password" id="newPassword" required>
                <span class="toggle-eye" onclick="togglePassword('newPassword')">👁</span>
            </div>
            <div class="password-field">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" id="confirmPassword" required>
                <span class="toggle-eye" onclick="togglePassword('confirmPassword')">👁</span>
            </div>
            <button type="submit" class="btn">Update Password</button>
        </form>
    </div>
</div>
<!-- OTP Modal -->
<div id="otpModal" class="modal" style="display:<?= isset($_SESSION['otp']) ? 'block' : 'none' ?>">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('otpModal').style.display='none'">&times;</span>
        <h3>Verify Email Change</h3>

        <?php if (!empty($error)): ?><p class="error"><?= $error ?></p><?php endif; ?>
        <?php if (!empty($message)): ?><p class="message"><?= $message ?></p><?php endif; ?>

        <form method="POST">
            <input type="hidden" name="verify_otp" value="1">
            <input type="text" name="otp" placeholder="Enter OTP" required>
            <button type="submit" class="btn">Verify</button>
        </form>

        <form method="POST" style="margin-top:1rem;">
            <input type="hidden" name="resend_otp" value="1">
            <button type="submit" class="btn">Resend OTP</button>
        </form>
    </div>
</div>

<script>
    const passModal = document.getElementById("passwordModal");
    const openBtn = document.getElementById("openPasswordModal");
    const closeBtn = passModal.querySelector(".close");

    if (openBtn) {
        openBtn.onclick = () => passModal.style.display = "block";
    }
    if (closeBtn) {
        closeBtn.onclick = () => passModal.style.display = "none";
    }
    window.onclick = (event) => {
        if (event.target == passModal) passModal.style.display = "none";
    };

    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        field.type = field.type === "password" ? "text" : "password";
    }

    window.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            passModal.style.display = 'none';
            document.getElementById("otpModal").style.display = 'none';
        }
    });
</script>

<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-section">
            <strong>Healthineers</strong>
            <div class="muted">Free Vaccination & Wellness</div>
            <p>Your trusted partner in community health and preventive care.</p>
        </div>
        <div class="footer-section">
            <h4>Quick Links</h4>
            <nav class="footer-nav">
                <a href="CenterDashboardController.php">Home</a>
                <a href="CenterAccountController.php" data-link>Center Data</a>

            </nav>
        </div>
        <div class="footer-section">
            <h4>Medical Info</h4>
            <nav class="footer-nav">
                <a href="">Vaccination Guide</a>
                <a href="">FAQs</a>
                <a href="">Medical Disclaimer</a>
            </nav>
        </div>
        <div class="footer-section">

            <div class="social-links">

            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2025 Healthineers. Providing free vaccination services for all. All rights reserved.</p>
    </div>
</footer>
</body>
    </html>
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && ( !empty($modalError) || !empty($modalMessage) )): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                document.getElementById("passwordModal").style.display = "block";
            });
        </script>
    <?php endif; ?>
    <?php
}
<?php
function renderPatientAccountView($patient, $cities, $message, $error, $modalMessage, $modalError) {
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>My Account — Healthioneers</title>
    <link rel="stylesheet" href="/Public/css/style.css">
    <style>
        .account-card{background:#fff;border-radius:12px;padding:2rem;box-shadow:0 6px 18px rgba(0,0,0,0.1);max-width:600px;margin:2rem auto;}
        .account-card h2{text-align:center;color:var(--teal);border-bottom:2px solid #f0f2f8;padding-bottom:.5rem;}
        .account-card form{display:grid;gap:1rem;}
        .account-card label{font-weight:600;color:#333;}
        .account-card input,.account-card select{width:100%;padding:12px 14px;border:1px solid #ddd;border-radius:8px;font-size:1rem;}
        .btn{background:linear-gradient(135deg,var(--teal),var(--teal-lighter));color:#fff;border:none;padding:12px 20px;border-radius:8px;font-weight:600;cursor:pointer;}
        .btn:hover{background-position:right center;transform:translateY(-2px);}
        .modal{display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,0.55);backdrop-filter:blur(4px);}
        .modal-content{background:#fff;margin:auto;padding:2.5rem;border-radius:18px;width:95%;max-width:520px;box-shadow:0 12px 28px rgba(0,0,0,0.25);position:relative;top:50%;transform:translateY(-50%);}
        .close{position:absolute;top:16px;right:20px;font-size:1.6rem;cursor:pointer;color:#888;}
        .close:hover{color:var(--teal);}
        .error{color:#d9534f;font-weight:bold;}
        .message{color:#28a745;font-weight:bold;}
        .password-field{display:flex;align-items:center;position:relative;}
        .password-field input{flex:1;padding:14px 16px;border:1px solid #ccc;border-radius:10px;font-size:1rem;background:#fafafa;}
        .toggle-eye{margin-left:-40px;cursor:pointer;font-size:1.3rem;color:#666;}
        .toggle-eye:hover{color:var(--teal);}

        /* Profile container */
        .profile {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 2rem auto;
            text-align: center;
        }

        /* Profile image */
        .profile img {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--teal);
            box-shadow: 0 6px 18px rgba(0,0,0,0.15);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor:pointer;
        }
        .profile img:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 24px rgba(0,0,0,0.25);
        }
    </style>
</head>
<body>
<header class="site-header">
    <div class="container nav-row">
        <div class="logo">
            <img src="/../../Public/Imgs/WhatsApp Image 2025-09-11 at 15.19.37_d6b5bce8.jpg" alt="Healthineers Logo">
            <a href="PatientHomeController.php">Healthioneers</a>
        </div>
        <nav class="main-nav">
            <a href="PatientHomeController.php">Home</a>
            <a href="PatientVaccinesController.php">Vaccines</a>
            <a href="PatientReservationController.php" class="active">My Reservation</a>
            <div class="dropdown">
                <button class="dropbtn">Account ▾</button>
                <div class="dropdown-content">
                    <a href="PatientAccountController.php">My Account</a>
                    <a href="../logout.php">Logout</a>
                </div>
            </div>
        </nav>
        <button class="nav-toggle">☰</button>
    </div>
</header>

<main>
    <div class="account-card">
        <h2>My Account</h2>

        <?php if (!empty($message)): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <div class="profile">
            <!--  Display uploaded photo or default -->
            <img src="<?= htmlspecialchars($patient['Patient_Image'] ?? '/Uploads/Patients/default.jpeg'); ?>"
                 alt="Profile Picture"
                 id="profilePic">
        </div>

        <!--  Modal for photo upload -->
        <div id="uploadModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h3>Upload / Remove Profile Picture</h3>
                <form method="POST" enctype="multipart/form-data">
                    <input type="file" name="patient_image" accept="image/*">
                    <button type="submit" class="btn">Upload</button>
                </form>
                <form method="POST" style="margin-top:1rem;">
                    <input type="hidden" name="remove_image" value="1">
                    <button type="submit" class="btn" style="background:#d9534f;">Remove Picture</button>
                </form>
                <?php if ($message): ?><p class="message"><?= htmlspecialchars($message) ?></p><?php endif; ?>
                <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
            </div>
        </div>

        <?php if ($patient): ?>
            <form method="POST" action="PatientAccountController.php">
                <input type="hidden" name="update" value="1">
                <label>First Name</label>
                <input type="text" name="fname" value="<?= htmlspecialchars($patient['Patient_FName']); ?>" required>
                <label>Last Name</label>
                <input type="text" name="lname" value="<?= htmlspecialchars($patient['Patient_LName']); ?>" required>
                <label>Username (Email)</label>
                <input type="text" name="username" value="<?= htmlspecialchars($patient['Patient_Username']); ?>" required>
                <label>Password</label>
                <input type="password" value="********" readonly class="readonly">
                <button type="button" class="btn" id="openPasswordModal">Change Password</button>
                <label>City</label>
                <select name="city_id" required>
                    <?php foreach ($cities as $c): ?>
                        <option value="<?= (int)$c['City_ID']; ?>" <?= $c['City_ID']==$patient['Patient_City_ID']?'selected':''; ?>>
                            <?= htmlspecialchars($c['City_Name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label>Phone</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($patient['Patient_Phone']); ?>" required>
                <label>National ID</label>
                <input type="text" value="<?= htmlspecialchars($patient['Patient_National_ID']); ?>" readonly class="readonly">
                <button type="submit" class="btn">Update Account</button>
            </form>
        <?php endif; ?>
    </div>
</main>

<!-- Password Modal -->
<div id="passwordModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Change Password</h3>
        <?php if ($modalError): ?><p class="error"><?= $modalError ?></p><?php endif; ?>
        <?php if ($modalMessage): ?><p class="message"><?= $modalMessage ?></p><?php endif; ?>
        <form method="POST" action="PatientAccountController.php">
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

<?php if (!empty($modalError) || !empty($modalMessage)): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("passwordModal").style.display = "block";
    });
</script>
<?php endif; ?>

<!-- OTP Modal -->
<div id="otpModal" class="modal" style="display:<?= isset($_SESSION['otp']) ? 'block' : 'none' ?>">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('otpModal').style.display='none'">&times;</span>
        <h3>Verify Email Change</h3>
        <?php if (!empty($error)): ?><p class="error"><?= $error ?></p><?php endif; ?>
        <?php if (!empty($message)): ?><p class="message"><?= $message ?></p><?php endif; ?>
        <form method="POST" action="PatientAccountController.php">
            <input type="hidden" name="verify_otp" value="1">
            <input type="text" name="otp" placeholder="Enter OTP" required>
            <button type="submit" class="btn">Verify</button>
        </form>
        <form method="POST" style="margin-top:1rem;" action="PatientAccountController.php">
            <input type="hidden" name="resend_otp" value="1">
            <button type="submit" class="btn">Resend OTP</button>
        </form>
    </div>
</div>

<script>
    // Profile photo modal logic
    const uploadModal = document.getElementById("uploadModal");
    const profilePic = document.getElementById("profilePic");
    const uploadClose = uploadModal.querySelector(".close");

    profilePic.onclick = () => uploadModal.style.display = "block";
    uploadClose.onclick = () => uploadModal.style.display = "none";
    window.onclick = (event) => { if (event.target == uploadModal) uploadModal.style.display = "none"; };

    // Password modal logic
    const passModal = document.getElementById("passwordModal");
    const openBtn = document.getElementById("openPasswordModal");
    const closeBtn = passModal.querySelector(".close");
    openBtn.onclick = () => passModal.style.display = "block";
    closeBtn.onclick = () => passModal.style.display = "none";
    window.onclick = (event) => { if (event.target == passModal) passModal.style.display = "none"; };

    // Toggle eye function
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        field.type = field.type === "password" ? "text" : "password";
    }
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
                <a href="PatientHomeController.php">Home</a>
                <a href="PatientReservationController.php" data-link>Reservations</a>
                <a href="PatientVaccinesController.php" data-link>Vaccines</a>
                <a href="PatientAccountController.php" data-link>My Info</a>
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
    <?php
}
?>
<?php
function renderUpdatePasswordView($message, $error) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Update Password - Healthioneers</title>
        <link rel="stylesheet" href="/Public/css/login&register.css">
        <link rel="stylesheet" href="/Public/css/style.css">
    </head>
    <body>
    <section class="content" id="update-password">
        <div class="form-container">
            <h2>Update Password</h2>

            <?php if ($message): ?>
                <p class="message" style="font-weight:bold;"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <?php if ($error): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <?php if (!isset($_SESSION['reset_user'])): ?>
                <!-- Enter email -->
                <form method="POST" action="">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    <button type="submit" name="request_reset">Continue</button>
                </form>

                <p class="form-link">
                    Remembered your password?
                    <a href="../Control/LoginController.php">Back to login</a>
                </p>

            <?php elseif (!isset($_SESSION['otp_verified'])): ?>
                <!-- Enter OTP -->
                <form method="POST" action="">
                    <label for="otp">Enter OTP</label>
                    <input type="text" id="otp" name="otp" placeholder="Enter OTP" required>
                    <button type="submit" name="verify_otp">Verify OTP</button>
                </form>

                <form method="POST" action="" style="margin-top:10px;">
                    <button type="submit" name="resend_otp" value="1">Resend OTP</button>
                </form>

                <form method="POST" action="" style="margin-top:10px;">
                    <button type="submit" name="start_over" value="1">Use a different email</button>
                </form>

            <?php else: ?>
                <!--Enter new password -->
                <form method="POST" action="">
                    <label for="new_password">New password</label>
                    <input type="password" id="new_password" name="new_password" placeholder="New Password" required>

                    <label for="confirm_password">Confirm password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>

                    <button type="submit" name="set_new_password">Update Password</button>
                </form>
            <?php endif; ?>
        </div>
    </section>
    </body>
    </html>
    <?php
}
?>
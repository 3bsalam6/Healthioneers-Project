<?php
function renderLoginView($showOtpBox, $message, $error) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Login - Healthioneers</title>
        <link rel="stylesheet" href="/Public/css/login&register.css">
        <link rel="stylesheet" href="/Public/css/style.css">
    </head>
    <body>
    <section class="content" id="login">
        <?php if (!$showOtpBox): ?>
            <!-- Login Box -->
            <div class="form-container">
                <h2>Login</h2>
                <?php if ($message) echo "<p class='message' style='font-weight:bold;'>".htmlspecialchars($message)."</p>"; ?>
                <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

                <form method="POST" action="">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>

                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>

                    <button type="submit" name="login">Login</button>
                    <p class="form-link">
                        Don't have an account?
                        <a href="../Control/RegisterController.php">Register here</a>
                    </p>


                    <p class="form-link">
                        Forgot your password?
                        <a href="../Control/UpdatePasswordController.php">Update it here</a>
                    </p>


        <?php else: ?>
            <!-- OTP Box -->
            <div class="form-container">
                <h2>OTP Verification</h2>
                <?php if ($message) echo "<p class='message' style='font-weight:bold;'>".htmlspecialchars($message)."</p>"; ?>
                <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

                <form method="POST" action="">
                    <label for="otp">Enter OTP</label>
                    <input type="text" id="otp" name="otp" placeholder="Enter OTP" required>
                    <button type="submit" name="verify_otp" value="1">Verify OTP</button>
                </form>

                <form method="POST" action="" style="margin-top:10px;">
                    <button type="submit" name="resend_otp" value="1">Resend OTP</button>
                </form>

                <form method="POST" action="" style="margin-top:10px;">
                    <button type="submit" name="back_to_login" value="1">Back to Login</button>
                </form>
            </div>
        <?php endif; ?>
    </section>
    </body>
    </html>
    <?php
}
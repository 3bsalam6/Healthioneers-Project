<?php
function renderVerifyView($message, $error) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Verify Registration</title>
        <link rel="stylesheet" href="../Public/css/login&register.css"/>
        <link rel="stylesheet" href="../Public/css/style.css"/>
    </head>
    <body>
    <section class="content" id="verify">
        <div class="form-container">
            <h2>Verify Your Email</h2>

            <?php if (!empty($error)): ?>
                <p class="error-msg"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <?php if (!empty($message)): ?>
                <p class="success-msg"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <form method="POST" action="VerifyController.php">
                <label for="otp">Enter OTP</label>
                <input type="text" id="otp" name="otp" maxlength="6" required>
                <button type="submit" name="verify_otp" value="1">Verify</button>
            </form>

            <form method="POST" action="VerifyController.php" style="margin-top:1rem;">
                <input type="hidden" name="resend_otp" value="1">
                <button type="submit">Resend OTP</button>
            </form>

            <p class="form-link">Already verified? <a href="../Control/LoginController.php">Login here</a></p>
        </div>
    </section>
    </body>
    </html>
    <?php
}
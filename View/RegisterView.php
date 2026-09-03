<?php
function renderRegisterView($error, $cities) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>Register - Healthineers</title>

        <link rel="stylesheet" href="../Public/css/login&register.css"/>
        <link rel="stylesheet" href="../Public/css/style.css"/>
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>

        <style>
            #city {
                -webkit-appearance: none;  /* Chrome/Safari */
                -moz-appearance: none;     /* Firefox */
                appearance: none;          /* Standard */
                background: transparent;   /* remove default background */
                position: absolute;        /* take it out of flow */
                left: -9999px;             /* move it off-screen */
            }

            /* Clean up Select2 arrow box */
            .select2-container .select2-selection__arrow {
                background: none !important;   /* remove any default background */
                border: none !important;       /* remove border that can look like an arrow */
                top: 50% !important;
                transform: translateY(-50%) !important;
            }

            .select2-container .select2-selection__arrow {
                background: none !important;   /* remove any default background */
                border: none !important;       /* remove border that can look like an arrow */
                top: 50% !important;
                transform: translateY(-50%) !important;
            }

            .select2-container .select2-selection__arrow b {
                border-color: #333 transparent transparent transparent !important;
                border-style: solid;
                border-width: 5px 4px 0 4px;   /* triangle pointing down */
                display: inline-block;
                margin-left: -4px;
            }


            .select2-container {
                width: 100% !important;
            }
            .select2-container .select2-selection--single {
                height: 44px;                /* same height as inputs */
                border: 1px solid #ccc;
                border-radius: 4px;
                font-size: 16px;
                padding: 0;                  /* remove default padding that offsets text */
                position: relative;          /* for arrow positioning */
                box-sizing: border-box;
                background-color: #fff;
            }
            .select2-container .select2-selection__rendered {
                display: block;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                line-height: 44px !important; /* vertical centering via line-height */
                text-align: center !important; /* ✅ center text horizontally */
                color: #333;
            }
            .select2-container .select2-selection__placeholder {
                color: #888;                 /* placeholder style centered */
            }
            .select2-container .select2-selection__arrow {
                position: absolute;
                right: 10px;
                top: 50%;
                transform: translateY(-50%);
                height: 24px;                /* keep arrow visible */
            }
            .select2-container .select2-selection__clear {
                position: absolute;
                left: 10px;
                top: 50%;
                transform: translateY(-50%);
            }

            /* Dropdown and search field styling for consistency */

            .select2-dropdown {
                border-color: #ccc;
                border-radius: 4px;
                overflow: hidden;
            }
            .select2-search--dropdown .select2-search__field {
                font-size: 16px;
                padding: 8px;
                border: 1px solid #ddd;
                border-radius: 4px;
                box-sizing: border-box;
                width: calc(100% - 16px);
                margin: 8px;
            }
            .select2-results__option {
                padding: 8px 12px;
                font-size: 15px;
            }

            /* Button spacing and centering */
            form button {
                display: block;
                margin: 20px auto 0;          /* space above, centered horizontally */
                padding: 10px 20px;
                font-size: 16px;
                border-radius: 4px;
                cursor: pointer;
            }


        </style>
    </head>
    <body>
    <section class="content" id="register">
        <div class="form-container">
            <h2>Register</h2>
            <?php if (!empty($error)): ?>
                <p class="error-msg"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form id="registerForm" method="POST" action="RegisterController.php">
                <label for="fname">First Name</label>
                <input type="text" id="fname" name="fname"
                       value="<?= htmlspecialchars($_POST['fname'] ?? '') ?>" required>

                <label for="lname">Last Name</label>
                <input type="text" id="lname" name="lname"
                       value="<?= htmlspecialchars($_POST['lname'] ?? '') ?>" required>

                <label for="username">Username (Email)</label>
                <input type="email" id="username" name="username"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>

                <label for="city">City</label>
                <select id="city" name="city" required>
                    <option value="">Select your city</option>
                    <?php foreach ($cities as $c): ?>
                        <option value="<?= $c['City_ID'] ?>"
                                <?= (($_POST['city'] ?? '') == $c['City_ID']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['City_Name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>

                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm-password" name="confirm-password" required>

                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone"
                       value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>

                <label for="national_id">National ID</label>
                <input type="text" id="national_id" name="national_id" maxlength="14"
                       pattern="\d{14}" title="National ID must be exactly 14 digits"
                       value="<?= htmlspecialchars($_POST['national_id'] ?? '') ?>" required>

                <button type="submit">Register</button>
            </form>

            <p class="form-link">Already have an account? <a href="../Control/loginController.php">Login here</a></p>
        </div>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#city').select2({
                placeholder: "Select your city",
                allowClear: true,
                width: '100%'
            });

            $('#registerForm').on('submit', function(e) {
                const nationalId = $('#national_id').val().trim();
                if (!/^\d{14}$/.test(nationalId)) {
                    e.preventDefault();
                    alert("⚠️ National ID must be exactly 14 digits.");
                    $('#national_id').css('border', '1px solid red');
                }
            });
        });
    </script>
    </body>
    </html>
    <?php
}
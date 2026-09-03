<?php
function renderCenterHomeView() {
    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Center Dashboard</title>
        <link rel="stylesheet" href="/Public/css/style.css">

        <style>
            body {
                display: flex;
                flex-direction: column;
                min-height: 100vh; /* full viewport height */
                margin: 0;
            }

            main {
                flex: 1; /* grows to fill space */
            }


            .center-buttons {
                display: flex;
                justify-content: center;
                gap: 2rem;
                margin-top: 50px;
            }

            .center-btn {
                background: linear-gradient(to right, #009688, #26a69a);
                color: #fff;
                border: none;
                padding: 20px 40px;
                border-radius: 8px;
                font-size: 1.2rem;
                cursor: pointer;
                transition: transform 0.2s ease;
                text-decoration: none;
            }

            .center-btn:hover {
                transform: translateY(-3px);
            }


        </style>
    </head>
    <body>
    <header class="site-header">
        <div class="container nav-row">
            <div class="logo">
                <img src="/../../Public/Imgs/WhatsApp Image 2025-09-11 at 15.19.37_d6b5bce8.jpg" alt="Healthineers Logo">
                <a href="/../Overflow%20MVC/Control/Center/CenterDashboardController.php">Healthioneers</a>
            </div>


            <nav class="main-nav">
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
            </nav>
            <button class="nav-toggle">☰</button>
        </div>
    </header>

    <main class="container">
        <div class="center-buttons">
            <a href="CenterSearchController.php" class="center-btn">Search Reservation</a>
            <a href="CenterTodayController.php" class="center-btn">Today’s Schedule</a>
<!--            <a href="CenterAwaitingController.php" class="center-btn">Awaiting Confirmations</a>-->
        </div>
    </main>
    <!-- Footer -->
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
    <?php
}
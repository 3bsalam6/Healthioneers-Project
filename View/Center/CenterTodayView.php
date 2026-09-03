<?php
function renderCenterTodayView($todayList, $message, $error) {
    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Today’s Schedule</title>
        <link rel="stylesheet" href="/Public/css/style.css">
        <style>
            table { width:100%; border-collapse: collapse; margin-top:20px; border-radius:8px; overflow:hidden; }
            th, td { border:1px solid #ccc; padding:12px; text-align:left; }
            th { background:#009688; color:#fff; }
            tr:nth-child(even) { background:#f9f9f9; }
            tr:hover { background:#e0f2f1; }
            .btn { background:#009688; color:#fff; padding:8px 12px; border:none; border-radius:4px; cursor:pointer; }
            .btn:hover { background:#00796b; }
            .search-box { display:flex; align-items:center; justify-content:center; margin:30px 0; }
            .search-box input[type="text"] { width:350px; padding:12px 16px; border:2px solid #ccc; border-radius:25px 0 0 25px; font-size:1rem; outline:none; transition:all 0.3s ease; }
            .search-box input[type="text"]:focus { border-color:#009688; box-shadow:0 0 8px rgba(0,150,136,0.3); }
            .search-box button { padding:12px 20px; border:none; border-radius:0 25px 25px 0; background:linear-gradient(to right,#009688,#26a69a); color:#fff; font-size:1rem; cursor:pointer; transition:background 0.3s ease, transform 0.2s ease; }
            .search-box button:hover { background:linear-gradient(to right,#26a69a,#009688); transform:translateY(-2px); }

            html, body {
                height: 100%;          /* full viewport height */
                margin: 0;
            }

            body {
                display: flex;
                flex-direction: column;
                min-height: 100vh;     /* ensure body fills viewport */
            }

            main {
                flex: 1;               /* grow to fill space */
            }

        </style>
    </head>
    <body>
    <header class="site-header">
        <div class="container nav-row">
            <div class="logo">
                <img src="/Public/Imgs/WhatsApp Image 2025-09-11 at 15.19.37_d6b5bce8.jpg" alt="Healthineers Logo">
                <a href="/../Overflow%20MVC/Control/Center/CenterDashboardController.php">Healthioneers</a>
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
    <main class="container">
        <?php if ($message): ?><p class="message"><?= htmlspecialchars($message) ?></p><?php endif; ?>
        <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

        <!-- Search Form -->
        <form method="POST" class="search-box">
            <input type="text" name="search_term" placeholder="🔍 Search today by Reservation ID, Patient Name, or Vaccine Name" required>
            <button type="submit" name="search_today">Search</button>
        </form>

        <!-- Results -->
        <?php if (empty($todayList)): ?>
            <p>No reservations scheduled today.</p>
        <?php else: ?>
            <table>
                <thead>
                <tr>
                    <th>ID</th><th>Patient</th><th>National ID</th>
                    <th>Vaccine</th><th>Center</th><th>Date</th>
                    <th>Status</th><th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($todayList as $row): ?>
                    <tr>
                        <td><?= $row['Reservation_ID'] ?></td>
                        <td><?= htmlspecialchars($row['Patient_FName']." ".$row['Patient_LName']) ?></td>
                        <td><?= htmlspecialchars($row['Patient_National_ID']) ?></td>
                        <td><?= htmlspecialchars($row['Vaccine_Name']) ?></td>
                        <td><?= htmlspecialchars($row['Center_Name']) ?></td>
                        <td>
                            <?php
                            $dateValue = $row['Scheduled_Date'];
                            echo htmlspecialchars(
                                    $dateValue instanceof DateTime ? $dateValue->format('Y-m-d') : (string)$dateValue
                            );
                            ?>
                        </td>
                        <td><?= htmlspecialchars($row['Reservation_Status']) ?></td>
                        <td>
                            <?php if (!$row['First_Confirmation']): ?>
                                <!-- Show only Confirm Dose 1 -->
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="reservation_id" value="<?= $row['Reservation_ID'] ?>">
                                    <button type="submit" name="confirm_dose" value="1" class="btn">Confirm Dose 1</button>
                                </form>
                            <?php elseif ($row['First_Confirmation'] && !$row['Second_Confirmation']): ?>
                                <?php if (!empty($row['dose_gap_days']) && (int)$row['dose_gap_days'] > 0): ?>
                                    <!-- Show Confirm Dose 2 only if vaccine requires it -->
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="reservation_id" value="<?= $row['Reservation_ID'] ?>">
                                        <button type="submit" name="confirm_dose" value="2" class="btn">Confirm Dose 2</button>
                                    </form>
                                <?php else: ?>
                                    <!-- Single-dose vaccine -->
                                    <span>✅ Single-dose vaccine — no second dose required</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <!-- All doses confirmed -->
                                <span>✅ All doses confirmed</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
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
<?php
function renderPatientReservationView($patient, $reservations) {
    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <title>My Reservation — Healthioneers</title>
        <link rel="stylesheet" href="/Public/css/style.css">
        <style>
            body { display:flex; flex-direction:column; min-height:100vh; margin:0; font-family:Arial,sans-serif; background:#f9f9f9; }
            main { flex:1; padding:1.5rem 2rem; }
            h2 { margin-bottom:1rem; color:#333; }
            .reservation-list { display:block; margin:0; padding:0; }
            .reservation-item { width:100%; margin:12px 0; }
            .reservation-link {
                display:flex; justify-content:space-between; align-items:center;
                width:100%; padding:16px 20px; border-radius:8px; background:#fff;
                box-shadow:0 4px 12px rgba(0,0,0,0.08); text-align:left;
                text-decoration:none; color:#333; font-weight:500;
                transition:transform 0.2s ease, box-shadow 0.3s ease;
            }
            .reservation-link:hover { transform:translateY(-2px); box-shadow:0 6px 16px rgba(0,0,0,0.12); }
            .res-vaccine { font-weight:700; color:#009688; }
            .res-center { flex:1; margin:0 12px; color:#555; }
            .res-date { color:#444; }
            .res-number { color:#777; font-weight:600; }
            .res-status { display:flex; align-items:center; font-weight:600; }
            .status-dot { width:8px; height:8px; border-radius:50%; margin-right:6px; }
            .status-ongoing { color:#e53935; }
            .status-ongoing .status-dot { background:#e53935; }
            .status-finished { color:#43a047; }
            .status-finished .status-dot { background:#43a047; }
            .reservation-details { margin:10px 0; padding:12px; background:#e6e6e6; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.06); }
            .reservation-table { width:100%; border-collapse:collapse; }
            .reservation-table th { text-align:left; padding:8px; background:#009688; color:#fff; width:220px; }
            .reservation-table td { padding:8px; background:#fff; }
            .btn { background:#009688; color:#fff; padding:8px 14px; border:none; border-radius:6px; cursor:pointer; text-decoration:none; }
            .btn:hover { background:#00796b; }

            .button {
                justify-self: center;
                align-self: center;
                height: 75px;
                width: 270px;
                border: none;
                border-radius: 10px;
                cursor: pointer;
                position: relative;
                overflow: hidden;
                transition: all 0.5s ease-in-out;
            }

            .button:hover {
                box-shadow: .5px .5px 150px #252525;
            }

            .type1::after {
                content: "Book a vaccine here";
                height: 75px;
                width: 270px;
                background-color: #008080;
                color: #fff;
                position: absolute;
                top: 0%;
                left: 0%;
                transform: translateY(75px);
                font-size: 1.2rem;
                font-weight: 600;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.5s ease-in-out;
            }

            .type1::before {
                content: "No reservations found";
                height: 75px;
                width: 270px;
                background-color: #fff;
                color: #008080;
                position: absolute;
                top: 0%;
                left: 0%;
                transform: translateY(0px) scale(1.2);
                font-size: 1.2rem;
                font-weight: 600;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.5s ease-in-out;
            }

            .type1:hover::after {
                transform: translateY(0) scale(1.2);
            }

            .type1:hover::before {
                transform: translateY(-75px) scale(0) rotate(120deg);
            }

            /*----------------------------------------------------*/




            .reserve-button {
                padding: 1.3em 3em;
                font-size: 12px;
                text-transform: uppercase;
                letter-spacing: 2.5px;
                font-weight: 500;
                color: #000;
                background-color: #fff;
                border: none;
                border-radius: 45px;
                box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease 0s;
                cursor: pointer;
                outline: none;
            }

            .reserve-button:hover {
                background-color: #23c483;
                box-shadow: 0px 15px 20px rgba(46, 229, 157, 0.4);
                color: #fff;
                transform: translateY(-7px);
            }

            .reserve-button:active {
                transform: translateY(-1px);
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
        <h2>My Reservations</h2>
        <?php if (empty($reservations)): ?>
            <button class="button type1" onclick='window.location.href = "PatientVaccinesController.php"'></button>
        <?php else: ?>
            <div class="reservation-list">
                <?php foreach ($reservations as $row):
                    $date = $row['Scheduled_Date'] instanceof DateTime ? $row['Scheduled_Date']->format('Y-m-d') : $row['Scheduled_Date'];
                    $statusClass = strtolower($row['Reservation_Status']) === 'ongoing' ? 'status-ongoing' : 'status-finished';
                    $gap = (int)($row['dose_gap_days'] ?? 0);
                    ?>
                    <div class="reservation-item">
                        <button type="button" class="reservation-link" data-id="<?= $row['Reservation_ID'] ?>">
                            <div class="res-info">
                                <span class="res-vaccine"><?= htmlspecialchars($row['Vaccine_Name']) ?></span> —
                                <span class="res-center"><?= htmlspecialchars($row['Center_Name']) ?></span> —
                                <span class="res-date"><?= htmlspecialchars($date) ?></span> —
                                <span class="res-number">#<?= htmlspecialchars($row['Reservation_ID']) ?></span>
                            </div>
                            <div class="res-status <?= $statusClass ?>">
                                <span class="status-dot"></span> <?= htmlspecialchars($row['Reservation_Status']) ?>
                            </div>
                        </button>

                        <div class="reservation-details" id="details-<?= $row['Reservation_ID'] ?>" style="display:none;">
                            <table class="reservation-table">
                                <tr><th>Reservation Number</th><td><?= htmlspecialchars($row['Reservation_ID']) ?></td></tr>
                                <tr><th>Patient</th><td><?= htmlspecialchars($patient['Patient_FName']." ".$patient['Patient_LName']) ?></td></tr>
                                <tr><th>Vaccine</th><td><?= htmlspecialchars($row['Vaccine_Name']) ?></td></tr>
                                <tr><th>Center</th><td><?= htmlspecialchars($row['Center_Name']) ?></td></tr>
                                <tr><th>Location</th><td><?= htmlspecialchars($row['Center_Address']) ?></td></tr>
                                <tr><th>Reservation Date</th>
                                    <td>
                                        <?php if (!empty($row['Reservation_Date'])): ?>
                                            <?= $row['Reservation_Date'] instanceof DateTime
                                                    ? $row['Reservation_Date']->format('Y-m-d H:i')
                                                    : htmlspecialchars($row['Reservation_Date']) ?>
                                        <?php else: ?>
                                            Not recorded
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr><th>First Dose Date</th><td><?= htmlspecialchars($date) ?></td></tr>
                                <?php if ($gap > 0):
                                    $secondDate = date("Y-m-d", strtotime($date." +".$gap." days")); ?>
                                    <tr><th>Second Dose Date</th><td><?= htmlspecialchars($secondDate) ?></td></tr>
                                    <tr><th>Dose Gap</th><td><?= htmlspecialchars($gap) ?> days</td></tr>
                                <?php endif; ?>
                                <tr><th>Status</th><td><?= htmlspecialchars($row['Reservation_Status']) ?></td></tr>
                                <tr><th>Action</th><td>
                                        <?php if (empty($row['First_Confirmation'])): ?>
                                            Waiting for center to confirm first dose ⏳
                                        <?php else:
                                            $fd = $row['First_Confirmation_Date'] instanceof DateTime ? $row['First_Confirmation_Date']->format('Y-m-d') : $row['First_Confirmation_Date'];
                                            echo "First Dose Confirmed on ".htmlspecialchars($fd);
                                        endif; ?>
                                        <br>
                                        <?php if ($gap > 0): ?>
                                            <?php if (!empty($row['Second_Confirmation_Date'])):
                                                $sd = $row['Second_Confirmation_Date'] instanceof DateTime ? $row['Second_Confirmation_Date']->format('Y-m-d') : $row['Second_Confirmation_Date'];
                                                echo "Second Dose Confirmed on ".htmlspecialchars($sd);
                                            else: ?>
                                                Waiting for center to confirm second dose ⏳
                                            <?php endif; ?>
                                        <?php else: ?>
                                            Single‑dose vaccine — no second dose required ✅
                                        <?php endif; ?>
                                        <?php if (strtolower($row['Reservation_Status']) === 'finished'): ?>
                                            <br>
                                            <a href="PatientCertificateController.php?reservation_id=<?= $row['Reservation_ID'] ?>" class="btn">Show Certificate</a>
                                        <?php endif; ?>
                                    </td></tr>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

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

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll(".reservation-link").forEach(btn => {
                btn.addEventListener("click", () => {
                    const id = btn.dataset.id;
                    const details = document.getElementById("details-" + id);
                    document.querySelectorAll(".reservation-details").forEach(d => {
                        if (d !== details) d.style.display = "none";
                    });
                    details.style.display = (details.style.display === "block") ? "none" : "block";
                });
            });
        });
    </script>
    </body>
    </html>
    <?php
}
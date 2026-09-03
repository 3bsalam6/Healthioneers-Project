<?php
function renderPatientCertificateView($reservation) {
    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Vaccination Certificate</title>
        <link rel="stylesheet" href="/Public/css/style.css">

        <style>
            html, body {
                height: 100%;
                margin: 0;
            }

            body {
                display: flex;
                flex-direction: column;
                min-height: 100vh;
                font-family: 'Georgia', serif;
                background: #f0f0f0;
            }

            main {
                flex: 1; /* certificate area grows */
                padding: 2rem;
            }

            .certificate {
                max-width: 800px;
                margin: 2rem auto;
                background: #fff;
                border: 10px solid #009688;
                border-radius: 12px;
                padding: 40px;
                box-shadow: 0 6px 20px rgba(0,0,0,0.15);
                text-align: center;
            }

            .certificate h1 {
                font-size: 2.2rem;
                color: #009688;
                margin-bottom: 0.5rem;
            }

            .certificate h2 {
                font-size: 1.5rem;
                margin: 1rem 0;
                color: #333;
            }

            .certificate p {
                font-size: 1.1rem;
                margin: 0.5rem 0;
                color: #444;
            }

            .certificate .highlight {
                font-weight: bold;
                color: #000;
            }

            .download-btn {
                display: inline-block;
                margin-top: 2rem;
                padding: 12px 20px;
                background: #009688;
                color: #fff;
                font-size: 1rem;
                border-radius: 6px;
                text-decoration: none;
                transition: background 0.3s ease;
            }
            .download-btn:hover { background: #00796b; }

            header.site-header, footer.site-footer {
                background: #009688;
                color: #fff;
                text-align: center;
                padding: 1rem;
            }

            footer.site-footer {
                margin-top: auto; /* pushes footer down */
            }
        </style>
    </head>
    <body>

    <header class="site-header">
        <h2>Healthioneers</h2>
    </header>

    <div class="certificate">
        <h1>Vaccination Certificate</h1>
        <?php if ($reservation): ?>
            <h2>This certifies that <span class="highlight">
            <?= htmlspecialchars($reservation['Patient_FName']." ".$reservation['Patient_LName']) ?>
        </span></h2>

            <p>has successfully completed vaccination with</p>
            <p class="highlight"><?= htmlspecialchars($reservation['Vaccine_Name']) ?></p>

            <p>First Dose Date:
                <?php if (!empty($reservation['First_Confirmation_Date'])): ?>
                    <?= $reservation['First_Confirmation_Date'] instanceof DateTime
                        ? $reservation['First_Confirmation_Date']->format('Y-m-d')
                        : htmlspecialchars($reservation['First_Confirmation_Date']) ?>
                <?php else: ?>
                    Not recorded
                <?php endif; ?>
            </p>

            <p>Second Dose Date:
                <?php if (!empty($reservation['Second_Confirmation_Date'])): ?>
                    <?= $reservation['Second_Confirmation_Date'] instanceof DateTime
                        ? $reservation['Second_Confirmation_Date']->format('Y-m-d')
                        : htmlspecialchars($reservation['Second_Confirmation_Date']) ?>
                <?php else: ?>
                    Not required / Not recorded
                <?php endif; ?>
            </p>

            <form method="post" action="DownloadCertificate.php">
                <input type="hidden" name="reservation_id" value="<?= $reservation['Reservation_ID'] ?>">
                <button type="submit" class="download-btn">Download As PDF</button>
            </form>
        <?php else: ?>
            <p>No certificate found.</p>
        <?php endif; ?>
    </div>

    <footer class="site-footer">
        <p>&copy; 2025 Healthioneers. Providing free vaccination services for all. All rights reserved.</p>
    </footer>

    </body>
    </html>
    <?php
}
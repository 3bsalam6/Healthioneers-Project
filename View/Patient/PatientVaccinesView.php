<?php
function renderPatientReservationView($patientFName, $vaccines, $centers, $message, $error) {
    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Patient Reservation</title>
        <link rel="stylesheet" href="../../Public/css/style.css">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            .grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                gap: 1.5rem;
            }
            .card-item {
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.08);
                padding: 16px;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
            }
            .card-item h4 {
                margin-top: 0;
                margin-bottom: 8px;
                color: #009688;
            }
            .card-item p { margin: 4px 0; }
            .card-item details { margin-top: 8px; }
            .reserve-btn {
                margin-top: auto;
                background: linear-gradient(to right, #009688, #26a69a);
                color: #fff;
                border: none;
                padding: 10px 14px;
                border-radius: 6px;
                cursor: pointer;
                transition: transform 0.2s ease;
            }
            .reserve-btn:hover { transform: translateY(-2px); }

            .search-bar {
                margin: 20px 0;
                text-align: left;
            }
            .search-input {
                padding: 10px;
                width: 250px;
                border: 1px solid #ccc;
                border-radius: 6px;
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

    <main class="container" style="padding:1.5rem 0;">
        <h2>Choose Your Vaccination Carefully,
            <span class="accent"><?= htmlspecialchars($patientFName) ?></span>!</h2>
        <?php if ($message): ?><p class="message"><?= htmlspecialchars($message) ?></p><?php endif; ?>
        <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search vaccine by name..." class="search-input">
        </div>

        <!-- Vaccine Cards -->
        <div class="grid">
            <?php foreach ($vaccines as $v): ?>
                <div class="card-item vaccine-card">
                    <h4><?= htmlspecialchars($v['Vaccine_Name']) ?></h4>
                    <p><strong>Gap days:</strong> <?= (int)$v['dose_gap_days'] ?></p>
                    <p><strong>Status:</strong> <?= htmlspecialchars($v['Status']) ?></p>
                    <details><summary>Precautions</summary>
                        <p><?= nl2br(htmlspecialchars($v['Precautions'] ?? '')) ?></p>
                    </details>
                    <button class="reserve-btn"
                            data-vaccine="<?= $v['Vaccine_ID'] ?>"
                            data-vaccine-name="<?= htmlspecialchars($v['Vaccine_Name']) ?>">
                        Reserve Vaccine
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Reservation Modal -->
    <div id="reservationModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3 id="modalTitle">Reserve Vaccine</h3>
            <form method="POST" action="PatientVaccinesController.php">
                <input type="hidden" name="reserve" value="1">
                <input type="hidden" name="vaccine_id" id="modalVaccineId">

                <label>Vaccination center</label>
                <select id="centerSelect" name="center_id" required style="width:100%">
                    <option value="">Select center</option>
                    <?php foreach ($centers as $c): ?>
                        <option value="<?= (int)$c['Center_ID'] ?>"
                                data-city="<?= htmlspecialchars($c['City_Name'] ?? '') ?>">
                            <?= htmlspecialchars($c['Center_Name']) ?> — <?= htmlspecialchars($c['Center_Address']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>First dose date</label>
                <input type="date" name="first_dose_date" required>

                <button type="submit" class="btn">Confirm Reservation</button>
            </form>
        </div>
    </div>

    <!-- jQuery + Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // Modal logic
        const modal = document.getElementById("reservationModal");
        const closeBtn = document.querySelector(".close");

        document.querySelectorAll(".reserve-btn").forEach(btn => {
            btn.addEventListener("click", () => {
                document.getElementById("modalTitle").textContent = "Reserve " + btn.dataset.vaccineName;
                document.getElementById("modalVaccineId").value = btn.dataset.vaccine;
                modal.style.display = "block";
            });
        });

        closeBtn.onclick = () => modal.style.display = "none";
        window.onclick = (event) => { if (event.target == modal) modal.style.display = "none"; };

        // Initialize Select2 with custom matcher for city + center name
        $(document).ready(function() {
            $('#centerSelect').select2({
                placeholder: "Search by city or center name",
                allowClear: true,
                matcher: function(params, data) {
                    if ($.trim(params.term) === '') {
                        return data;
                    }
                    const term = params.term.toLowerCase();
                    const text = (data.text || '').toLowerCase();
                    const city = ($(data.element).data('city') || '').toLowerCase();
                    if (text.includes(term) || city.includes(term)) {
                        return data;
                    }
                    return null;
                }
            });
        });

        // Vaccine search AJAX
        const searchInput = document.getElementById("searchInput");
        const grid = document.querySelector(".grid");

        searchInput.addEventListener("keyup", () => {
            const query = searchInput.value;
            fetch("PatientVaccinesSearch.php?q=" + encodeURIComponent(query))
                .then(res => res.json())
                .then(data => {
                    grid.innerHTML = "";
                    if (!data.length) {
                        grid.innerHTML = "<div class='card-item'>No vaccines found.</div>";
                        return;
                    }
                    data.forEach(v => {
                        const card = document.createElement("div");
                        card.className = "card-item vaccine-card";
                        card.innerHTML = `
                        <h4>${v.Vaccine_Name}</h4>
                        <p><strong>Gap days:</strong> ${v.dose_gap_days}</p>
                        <p><strong>Status:</strong> ${v.Status}</p>
                        <details><summary>Precautions</summary>
                            <p>${v.Precautions ?? ''}</p>
                        </details>
                        <button class="reserve-btn"
                            data-vaccine="${v.Vaccine_ID}"
                            data-vaccine-name="${v.Vaccine_Name}">
                            Reserve Vaccine
                        </button>
                    `;
                        grid.appendChild(card);
                    });

                    // re-bind modal buttons
                    document.querySelectorAll(".reserve-btn").forEach(btn => {
                        btn.addEventListener("click", () => {
                            document.getElementById("modalTitle").textContent = "Reserve " + btn.dataset.vaccineName;
                            document.getElementById("modalTitle").textContent = "Reserve " + btn.dataset.vaccineName;
                            document.getElementById("modalVaccineId").value = btn.dataset.vaccine;
                            document.getElementById("reservationModal").style.display = "block";
                        });
                    });
                })
                .catch(err => {
                    console.error("Search error:", err);
                    grid.innerHTML = "<div class='card-item'>Error loading vaccines.</div>";
                });
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
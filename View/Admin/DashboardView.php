<?php
function renderDashboardView($adminName, $message, $cities, $searchResults, $userList, $userSearchResults, $searchedCityId = null) {
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Healthioneers — Admin Dashboard</title>
    <link rel="stylesheet" href="/Public/css/style.css">
    <style>
        body { font-family: Arial, sans-serif; background:#f5f7fb; margin:0; padding:0; }
        header { background:#2f7df6; color:#fff; padding:12px 0; }
        .site-header .container { display:flex; justify-content:space-between; align-items:center; }
        .logo a { color:#fff; font-weight:bold; font-size:20px; text-decoration:none; }
        .container { max-width:1100px; margin:0 auto; padding:20px; }
        #menu { display:flex; flex-wrap:wrap; gap:12px; margin-top:20px; }
        .admin-section { background:#fff; border-radius:12px; padding:16px; margin:18px 0; box-shadow:0 2px 12px rgba(0,0,0,0.06); display:none; }
        button { padding:8px 16px; border:none; border-radius:6px; cursor:pointer; font-size:15px; }
        .btn-primary { background:#2f7df6; color:#fff; }
        .btn-danger { background:#e74c3c; color:#fff; }
        table { width:100%; border-collapse:collapse; margin-top:12px; }
        th, td { border:1px solid #eee; padding:8px; text-align:left; }
        th { background:#f5f7fb; }
        .center-card { border:1px solid #eee; border-radius:10px; padding:12px; margin:8px 0; background:#fafafa; }
        .center-summary { display:flex; justify-content:space-between; align-items:center; }
        .center-edit { display:none; margin-top:10px; }
        .actions { display:flex; gap:8px; }
    </style>
</head>
<body>
<header class="site-header">
    <div class="container">
        <div class="logo">
            <img src="/Public/imgs/WhatsApp Image 2025-09-11 at 15.19.37_d6b5bce8.jpg" alt="Healthineers Logo">
            <a href="DashboardController.php">Healthioneers</a>
        </div>
        <nav class="main-nav">
            <a href="DashboardController.php">Home</a>
            <div class="dropdown">
                <button class="dropbtn">Account ▾</button>
                <div class="dropdown-content">
                    <a href="AccountController.php">My Account</a>
                    <a href="../logout.php">Logout</a>
                </div>
            </div>
        </nav>
        <button class="nav-toggle">☰</button>
    </div>
</header>

<div class="container">
    <h1>Admin Dashboard</h1>
    <h2 style="color:#2f7df6;">Hello <?= htmlspecialchars($adminName ?: 'Admin') ?>!</h2>
    <?php if ($message) echo "<p style='color:#2f7df6;font-weight:bold;'>$message</p>"; ?>

    <!-- Main Menu -->
    <div id="menu">
        <button class="btn-primary" onclick="showSection('city')">Add City</button>
        <button class="btn-primary" onclick="showSection('center')">Add Vaccination Center</button>
        <button class="btn-primary" onclick="showSection('vaccine')">Add Vaccine</button>
        <button class="btn-primary" onclick="showSection('searchCenters')">Search Centers</button>
        <button class="btn-primary" onclick="showSection('searchUsers')">Search Users</button>
        <button class="btn-primary" onclick="showSection('users')">Registered Users</button>
    </div>

    <!-- Add City -->
    <section id="city" class="admin-section">
        <h2>Add City</h2>
        <form method="POST">
            <input type="text" name="city_name" placeholder="City Name" required>
            <button class="btn-primary" type="submit" name="add_city">Add City</button>
        </form>
        <button class="btn-danger" onclick="goBack()">⬅ Back</button>
    </section>

    <!-- Add Center -->
    <section id="center" class="admin-section">
        <h2>Add Vaccination Center</h2>
        <form method="POST" class="center-form">
            <input type="text" name="center_name" placeholder="Center Name" required class="wide-input">
            <select name="city_id" required class="wide-input">
                <?php foreach ($cities as $city): ?>
                    <option value="<?= $city['City_ID'] ?>"><?= htmlspecialchars($city['City_Name']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="address" placeholder="Address" required class="wide-input">
            <input type="text" name="contact" placeholder="Contact No." required class="wide-input">
            <select name="center_type" class="wide-input" required>
                <option value="" disabled selected>Select Center Type</option>
                <option value="Government">Government</option>
                <option value="Private">Private</option>
                <option value="Other">Other</option>
            </select>
            <input type="text" name="username" placeholder="Email" required class="wide-input">
            <input type="password" name="password" placeholder="Password" required class="wide-input">

            <div class="form-actions">
                <button class="btn-primary" type="submit" name="add_center">Add Center</button>
                <button class="btn-danger" type="button" onclick="goBack()">⬅ Back</button>
            </div>
        </form>
    </section>

    <!-- Add Vaccine -->
    <section id="vaccine" class="admin-section">
        <h2>Add Vaccine</h2>
        <form method="POST" class="vaccine-form">
            <div class="form-row">
                <input type="text" name="vaccine_name" placeholder="Vaccine Name" required class="wide-input">
                <input type="number" name="gap_days" placeholder="Gap Between Doses (days)" required class="wide-input">
            </div>
            <div class="form-row-precautions">
                <textarea name="precautions" placeholder="Precautions"></textarea>
            </div>
            <div class="form-actions">
                <button class="btn-primary" type="submit" name="add_vaccine">Add Vaccine</button>
                <button class="btn-danger" type="button" onclick="goBack()">⬅ Back</button>
            </div>
        </form>
    </section>

    <!-- Search Centers -->
    <section id="searchCenters" class="admin-section">
        <h2>Search Centers by City (Update/Delete)</h2>
        <form method="POST">
            <select name="search_city_id" required>
                <?php foreach ($cities as $city): ?>
                    <option value="<?= $city['City_ID'] ?>" <?= ($searchedCityId === (int)$city['City_ID']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($city['City_Name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button class="btn-primary" type="submit" name="search_center">Search</button>
        </form>

        <?php if (!empty($searchResults)): ?>
        <h3>Centers Found:</h3>
        <?php foreach ($searchResults as $center): ?>
        <div class="center-card">
            <div class="center-summary">
                <strong><?= htmlspecialchars($center['Center_Name']) ?></strong>
                — <?= htmlspecialchars($center['Center_Address']) ?>
                — <?= htmlspecialchars($center['Center_Contact_No']) ?>
                <div class="actions">
                    <button type="button" class="btn-primary" onclick="toggleEdit(this)">Edit</button>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="center_id" value="<?= $center['Center_ID'] ?>">
                        <button class="btn-danger" name="delete_center" onclick="return confirm('Delete this center?');">Delete</button>
                    </form>
                </div>
            </div>
            <div class="center-edit">
                <form method="POST" class="center-form">
                    <input type="hidden" name="center_id" value="<?= $center['Center_ID'] ?>">

                    <input type="text" name="center_name" value="<?= htmlspecialchars($center['Center_Name']) ?>" placeholder="Center Name" required class="wide-input">

                    <select name="city_id" class="wide-input" required>
                        <?php foreach ($cities as $city): ?>
                            <option value="<?= $city['City_ID'] ?>" <?= ($center['Center_City_ID'] == $city['City_ID']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($city['City_Name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <input type="text" name="address" value="<?= htmlspecialchars($center['Center_Address']) ?>" placeholder="Center Address" class="wide-input">
                    <input type="text" name="contact" value="<?= htmlspecialchars($center['Center_Contact_No']) ?>" placeholder="Contact Number" class="wide-input">
                    <input type="text" name="username" value="<?= htmlspecialchars($center['Center_Username']) ?>" placeholder="Center Username" class="wide-input">
                    <input type="password" name="password" placeholder="Enter New Password" class="wide-input">
                    <select name="center_type" class="wide-input" required>
                        <option value="" disabled>Select Center Type</option>
                        <option value="Government" <?= ($center['Center_Type'] === 'Government') ? 'selected' : '' ?>>Government</option>
                        <option value="Private" <?= ($center['Center_Type'] === 'Private') ? 'selected' : '' ?>>Private</option>
                        <option value="Other" <?= ($center['Center_Type'] === 'Other') ? 'selected' : '' ?>>Other</option>
                    </select>

                    <!-- Status dropdown -->
                    <select name="status" class="wide-input" required>
                        <option value="Active"   <?= ($center['Status'] === 'Active')   ? 'selected' : '' ?>>Active</option>
                        <option value="Inactive" <?= ($center['Status'] === 'Inactive') ? 'selected' : '' ?>>Inactive</option>
                        <option value="Pending"  <?= ($center['Status'] === 'Pending')  ? 'selected' : '' ?>>Pending</option>
                    </select>

                    <div class="form-actions">
                        <button class="btn-primary" type="submit" name="update_center">Save Changes</button>
                        <button class="btn-danger" type="button" onclick="goBack()">⬅ Back</button>
                    </div>
                </form>
            </div>
        </div>
            <?php endforeach; ?>
        <?php elseif ($searchedCityId !== null): ?>
            <p>No centers found for the selected city.</p>
        <?php endif; ?>
        <button class="btn-danger" onclick="goBack()">⬅ Back</button>
    </section>

    <!-- Search Users -->
    <section id="searchUsers" class="admin-section">
        <h2>Search Users</h2>
        <form method="POST">
            <input type="text" name="fname" placeholder="First Name">
            <input type="text" name="lname" placeholder="Last Name">
            <input type="number" name="userid" placeholder="User ID">
            <input type="text" name="nid" placeholder="National ID">
            <button class="btn-primary" type="submit" name="search_user">Search</button>
        </form>
        <?php if (!empty($userSearchResults)): ?>
            <h3>Search Results:</h3>
            <table>
                <tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Phone</th><th>National ID</th></tr>
                <?php foreach ($userSearchResults as $user): ?>
                    <tr>
                        <td><?= $user['Patient_ID'] ?></td>
                        <td><?= htmlspecialchars($user['Patient_FName']) ?></td>
                        <td><?= htmlspecialchars($user['Patient_LName']) ?></td>
                        <td><?= htmlspecialchars($user['Patient_Username']) ?></td>
                        <td><?= htmlspecialchars($user['Patient_Phone']) ?></td>
                        <td><?= htmlspecialchars($user['Patient_National_ID']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php elseif (isset($_POST['search_user'])): ?>
            <p style="color:red;font-weight:bold;">❌ Not able to find any users matching your search.</p>
        <?php endif; ?>
        <button class="btn-danger" onclick="goBack()">⬅ Back</button>
    </section>

    <!-- Registered Users -->
    <section id="users" class="admin-section">
        <h2>Registered Users</h2>
        <table>
            <tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Phone</th><th>National ID</th></tr>
            <?php foreach ($userList as $user): ?>
                <tr>
                    <td><?= $user['Patient_ID'] ?></td>
                    <td><?= htmlspecialchars($user['Patient_FName']) ?></td>
                    <td><?= htmlspecialchars($user['Patient_LName']) ?></td>
                    <td><?= htmlspecialchars($user['Patient_Username']) ?></td>
                    <td><?= htmlspecialchars($user['Patient_Phone']) ?></td>
                    <td><?= htmlspecialchars($user['Patient_National_ID']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <button class="btn-danger" onclick="goBack()">⬅ Back</button>
    </section>
</div>

<script>
    function showSection(id) {
        document.getElementById('menu').style.display = 'none';
        document.querySelectorAll('.admin-section').forEach(sec => sec.style.display = 'none');
        document.getElementById(id).style.display = 'block';
    }
    function goBack() {
        document.querySelectorAll('.admin-section').forEach(sec => sec.style.display = 'none');
        document.getElementById('menu').style.display = 'flex';
    }
    function toggleEdit(btn) {
        const card = btn.closest('.center-card');
        const edit = card.querySelector('.center-edit');
        edit.style.display = (edit.style.display === 'none' || edit.style.display === '') ? 'block' : 'none';
    }
</script>

<?php if ($searchedCityId !== null): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            showSection('searchCenters');
        });
    </script>
<?php endif; ?>

<?php if (isset($_POST['search_user'])): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            showSection('searchUsers');
        });
    </script>
<?php endif; ?>

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
                <a href="DashboardController.php">Home</a>
                <a href="AccountController.php" data-link>My Data</a>

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
            <div class="social-links"></div>
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
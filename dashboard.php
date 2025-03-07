<?php
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php");
    exit();
}
$first_name = $_SESSION['user_name'] ?? 'Employee'; // Fallback value
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Powerguide Tracking System</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="logo">
                <img src="pgsi_logo.png" alt="Powerguide Solutions Inc.">
            </div>
            <div class="logo">Hi, <?php echo htmlspecialchars($first_name); ?></div>
            <nav>
                <div class="nav-item active">Tracking</div>
                <div class="nav-item">Inventory</div>
                <div class="nav-item">Add Client</div>
                <div class="nav-item">Add Transaction</div>
                <div class="nav-item logout" id="logoutButton" style="cursor: pointer;">Logout</div>
            </nav>
        </aside>

        <main class="main-content">
            <div class="header">
                <h1 style="font-family: 'Syne', sans-serif;">Tracking</h1>
                <button class="btn btn-primary">Add Transaction</button>
            </div>

            <div class="search-section">
                <input type="text" id="companySearch" class="search-input" placeholder="Enter Company Name">
                <input type="text" class="date-input" placeholder="mm/dd/yyyy" readonly>
            </div>

            <div class="input-row">
                <input type="text" class="full-width" placeholder="Display the Address Company Address here" readonly>
                <input type="text" class="half-width" placeholder="Display the TIN no. here" readonly>
            </div>

            <div class="input-row">
                <input type="text" class="half-width" placeholder="Display the Contact Person here" readonly>
                <input type="text" class="half-width" placeholder="Display the Contact Number here" readonly>
            </div>

            <div class="input-row">
                <input type="text" class="full-width" placeholder="Display the Business Style here" readonly>
            </div>

            <div class="items-section">
                <div class="items-header">
                    <h3>Items Availed</h3>
                    <h3>Quantity</h3>
                </div>
                <div class="items-row">
                    <input type="text" placeholder="Display the Items Availed">
                    <input type="text" placeholder="Display the Quantity">
                </div>
                <div class="items-row">
                    <input type="text" placeholder="Display the Items Availed">
                    <input type="text" placeholder="Display the Quantity">
                </div>
                <div class="items-row">
                    <input type="text" placeholder="Display the Items Availed">
                    <input type="text" placeholder="Display the Quantity">
                </div>
            </div>

            <div class="companies-list" id="companiesList"></div>
        </main>
    </div>
    <script src="script.js"></script>
</body>
</html>
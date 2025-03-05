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
    <title>Employee Tracking System</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <style>
        .logout-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            font-family: 'Inter', sans-serif;
        }
        .modal-buttons {
            margin-top: 20px;
        }
        .modal-buttons button {
            margin: 0 10px;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
        }
        #confirmLogout {
            background-color: #4CAF50;
            color: white;
        }
        #cancelLogout {
            background-color: #f44336;
            color: white;
        }
    </style>
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

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        // Sample companies data
        const companies = [
            "Corner Steel Systems Corporation",
            "SCHWER POWER MANUFACTURING CORP.",
            "C Company",
            "D Company Inc.",
            "E Company",
            "F Company Inc.",
            "A Company",
        ];

        // Populate companies list
        const companiesList = document.getElementById("companiesList");
        const companySearch = document.getElementById("companySearch");

        function renderCompanies(filter = "") {
            companiesList.innerHTML = "";
            companies
                .filter((company) => company.toLowerCase().includes(filter.toLowerCase()))
                .forEach((company) => {
                    const div = document.createElement("div");
                    div.className = "company-item";
                    div.textContent = company;
                    div.addEventListener("click", () => selectCompany(company));
                    companiesList.appendChild(div);
                });
        }

        // Initial render
        renderCompanies();

        // Search functionality
        if (companySearch) {
            companySearch.addEventListener("input", (e) => {
                renderCompanies(e.target.value);
            });
        } else {
            console.error("Element with ID 'companySearch' not found.");
        }

        // Company selection
        function selectCompany(company) {
            fetch(`fetch_company.php?company_name=${encodeURIComponent(company)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        showNotification(data.error, "error");
                    } else {
                        document.querySelector(".search-input").value = data.company_name || "";
                        document.querySelectorAll(".full-width")[0].value = data.address || "";
                        document.querySelectorAll(".half-width")[0].value = data.tin_no || "";
                        document.querySelectorAll(".half-width")[1].value = data.contact_person || "";
                        document.querySelectorAll(".half-width")[2].value = data.contact_number || "";
                        document.querySelectorAll(".full-width")[1].value = data.business_style || "";

                        showNotification(`Company "${data.company_name}" selected successfully!`, "success");

                        companiesList.innerHTML = "";

                        const itemsSection = document.querySelector(".items-section");
                        itemsSection.innerHTML = `<div class="items-header">
                            <h3>Items Availed</h3>
                            <h3>Quantity</h3>
                        </div>`;

                        if (data.items.length > 0) {
                            data.items.forEach(item => {
                                const itemRow = document.createElement("div");
                                itemRow.className = "items-row";
                                itemRow.innerHTML = `
                                    <input type="text" value="${item.item_name}" readonly>
                                    <input type="text" value="${item.quantity}" readonly>
                                `;
                                itemsSection.appendChild(itemRow);
                            });
                        } else {
                            itemsSection.innerHTML += `<p>No items availed for this company.</p>`;
                        }

                        const existingPrintBtn = document.querySelector(".print-invoice-btn");
                        if (existingPrintBtn) existingPrintBtn.remove();

                        const printInvoiceBtn = document.createElement("button");
                        printInvoiceBtn.textContent = "Print Invoice";
                        printInvoiceBtn.className = "btn btn-primary print-invoice-btn";
                        printInvoiceBtn.onclick = function () {
                            const modal = document.createElement("div");
                            modal.className = "invoice-modal";
                            modal.innerHTML = `
                                <div class="modal-content">
                                    <span class="close-modal">×</span>
                                    <h3>Invoice Preview</h3>
                                    <div class="invoice-image-container">
                                        <img src="${data.invoice_image || 'placeholder.jpg'}" alt="Invoice Image" id="invoiceImage">
                                    </div>
                                    <button class="print-btn">Print</button>
                                </div>
                            `;
                            document.body.appendChild(modal);

                            modal.querySelector(".close-modal").onclick = function() {
                                modal.remove();
                            };

                            modal.querySelector(".print-btn").onclick = function() {
                                const printWindow = window.open('', '_blank');
                                printWindow.document.write(`
                                    <html>
                                    <head><title>Print Invoice</title></head>
                                    <body>
                                        <img src="${data.invoice_image || 'placeholder.jpg'}" onload="window.print();window.close()">
                                    </body>
                                    </html>
                                `);
                                printWindow.document.close();
                            };

                            window.onclick = function(event) {
                                if (event.target == modal) {
                                    modal.remove();
                                }
                            };
                        };
                        document.querySelector(".main-content").appendChild(printInvoiceBtn);
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    showNotification("Failed to fetch company details!", "error");
                });
        }

        // Notification function
        function showNotification(message, type = "success") {
            const existingNotification = document.querySelector(".notification-box");
            if (existingNotification) {
                existingNotification.remove();
            }

            const notification = document.createElement("div");
            notification.className = `notification-box ${type}`;
            notification.innerHTML = `
                <div class="notification-content">
                    <p>${message}</p>
                    <button class="close-btn">OK</button>
                </div>
            `;
            document.body.appendChild(notification);

            notification.querySelector(".close-btn").addEventListener("click", () => {
                notification.remove();
            });

            setTimeout(() => {
                if (notification) {
                    notification.remove();
                }
            }, 3000);
        }

        // Other utility functions
        function viewDocument(type) {
            alert(`Viewing ${type} document`);
        }

        function downloadDocument(type) {
            alert(`Downloading ${type} document`);
        }

        function addTransaction() {
            alert("Adding new transaction");
        }

        function viewInvoice(invoiceId) {
            window.open(`view_invoice.php?invoice_id=${invoiceId}`, '_blank');
        }

        // Logout functionality
        document.getElementById('logoutButton').addEventListener('click', function() {
            showLogoutConfirmation();
        });

        function showLogoutConfirmation() {
            const modal = document.createElement('div');
            modal.className = 'logout-modal';
            modal.innerHTML = `
                <div class="modal-content">
                    <p>Are you sure you want to log out?</p>
                    <div class="modal-buttons">
                        <button id="confirmLogout">Yes</button>
                        <button id="cancelLogout">No</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);

            document.getElementById('confirmLogout').addEventListener('click', function() {
                window.location.href = 'logout.php';
            });

            document.getElementById('cancelLogout').addEventListener('click', function() {
                modal.remove();
            });

            modal.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.remove();
                }
            });
        }
    });
    </script>
</body>
</html>
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
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="logo">
                <img src="pgsi_logo.png" alt="Powerguide Solutions Inc.">
            </div>
            <div class="logo">Hi, <?php echo htmlspecialchars($first_name); ?></div> <!-- Show first_name -->
         <nav>
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
                <input type="text" id ="companySearch" class="search-input" placeholder="Enter Company Name" readonly>
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
                </div>
            </div>
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
                // Fill company details
                document.querySelector(".search-input").value = data.company_name || "";
                document.querySelectorAll(".full-width")[0].value = data.address || "";
                document.querySelectorAll(".half-width")[0].value = data.tin_no || "";
                document.querySelectorAll(".half-width")[1].value = data.contact_person || "";
                document.querySelectorAll(".half-width")[2].value = data.contact_number || "";
                document.querySelectorAll(".full-width")[1].value = data.business_style || "";

                showNotification(`Company "${data.company_name}" selected successfully!`, "success");

                // Hide the company list
                companiesList.innerHTML = "";

                // Display items availed and quantity
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

                // Remove existing buttons if any
                const existingPrintBtn = document.querySelector(".print-invoice-btn");
                if (existingPrintBtn) existingPrintBtn.remove();

                // Add Print Invoice button with image preview functionality
                const printInvoiceBtn = document.createElement("button");
                printInvoiceBtn.textContent = "Print Invoice";
                printInvoiceBtn.className = "btn btn-primary print-invoice-btn";
                printInvoiceBtn.onclick = function () {
                    // Create a modal to display the invoice image
                    const modal = document.createElement("div");
                    modal.className = "invoice-modal";
                    modal.innerHTML = `
                        <div class="modal-content">
                            <span class="close-modal">&times;</span>
                            <h3>Invoice Preview</h3>
                            <div class="invoice-image-container">
                                <img src="${data.invoice_image || 'placeholder.jpg'}" alt="Invoice Image" id="invoiceImage">
                            </div>
                            <button class="print-btn">Print</button>
                        </div>
                    `;
                    
                    document.body.appendChild(modal);

                    // Close modal functionality
                    modal.querySelector(".close-modal").onclick = function() {
                        modal.remove();
                    };

                    // Print functionality
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
                    // Close modal when clicking outside
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
        
    
        // Function to show a centered notification pop-up box
        function showNotification(message, type = "success") {
            // Remove existing notification if any
            const existingNotification = document.querySelector(".notification-box");
            if (existingNotification) {
                existingNotification.remove();
            }

            // Create notification
            const notification = document.createElement("div");
            notification.className = `notification-box ${type}`;
            notification.innerHTML = `
                <div class="notification-content">
                    <p>${message}</p>
                    <button class="close-btn">OK</button>
                </div>
            `;

            document.body.appendChild(notification);

            // Close notification when button is clicked
            notification.querySelector(".close-btn").addEventListener("click", () => {
                notification.remove();
            });

            // Auto remove after 3 seconds
            setTimeout(() => {
                if (notification) {
                    notification.remove();
                }
            }, 3000);
        }
    
        // Document actions
        function viewDocument(type) {
            alert(`Viewing ${type} document`);
        }
    
        function downloadDocument(type) {
            alert(`Downloading ${type} document`);
        }
    
        function addTransaction() {
            alert("Adding new transaction");
        }
    });
    function viewInvoice(invoiceId) {
    window.open(`view_invoice.php?invoice_id=${invoiceId}`, '_blank');

    document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("logoutButton").addEventListener("click", function() {
        console.log("Logout button clicked");
        if (confirm("Are you sure you want to logout?")) {
            window.location.href = "logout.php";
        }
    });
});
}
</script>  
</body>
</html>
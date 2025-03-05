<?php
session_start();
include 'db_connection.php'; // Ensure this file is correct

$showError = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id = trim($_POST['employee_id'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($employee_id) && !empty($password)) {
        $query = "SELECT id, first_name, password FROM employees WHERE employee_id = ?";

        $stmt = $conn->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("s", $employee_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user) {
                // Verify if password is hashed
                if (password_verify($password, $user['password'])) {  
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['first_name']; // Fetch first_name instead of name
                    header("Location: dashboard.php");
                    exit();
                } elseif ($password === $user['password']) { // If stored as plain text
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['first_name']; // Fetch first_name instead of name
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $showError = "Invalid employee ID or password.";
                }
            } else {
                $showError = "Invalid employee ID or password.";
            }
            $stmt->close();
        } else {
            $showError = "Database error. Please try again.";
        }
    } else {
        $showError = "Please enter both Employee ID and Password.";
    }
}

$conn->close();
?>

<!-- Display Minimal Error Message -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let error = "<?php echo $showError; ?>";
        if (error.trim() !== "") {
            alert(error); // Show a minimal alert box
        }
    });
</script>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;600&family=Inter:wght@400;500&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #A8E6CF, #DCEDC1);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Inter', sans-serif;
            padding: 1rem;
        }

        .login-container {
            background-color: #FFF;
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 450px;
            animation: fadeIn 0.8s ease-out forwards;
            position: relative;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h1 {
            font-family: 'Syne', sans-serif;
            color: #2A6041;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            color: #2A6041;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        input {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #2A6041;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: #1C0F13;
            box-shadow: 0 0 8px rgba(42, 96, 65, 0.3);
        }

        button {
            width: 100%;
            padding: 1rem;
            background-color: #2A6041;
            color: #FFF;
            border: none;
            border-radius: 8px;
            font-family: 'Syne', sans-serif;
            font-size: 1.1rem;
            cursor: pointer;
            transition: transform 0.2s ease, background-color 0.3s ease;
        }

        button:hover {
            background-color: #1C0F13;
            transform: translateY(-2px);
        }

        .forgot-password {
            text-align: right;
            margin: 1rem 0;
        }

        .forgot-password a {
            color: #2A6041;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: #1C0F13;
        }

        /* Redesigned popup styles */
        .error-popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.3);
            z-index: 999;
            display: none;
            backdrop-filter: blur(5px);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .error-popup {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            max-width: 350px;
            width: 90%;
            padding: 25px;
            text-align: center;
            position: relative;
            transform: scale(0.9);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .error-popup.show {
            transform: scale(1);
            opacity: 1;
        }

        .error-popup h2 {
            color: #FF6B6B;
            margin-bottom: 15px;
            font-family: 'Syne', sans-serif;
        }

        .error-popup p {
            color: #333;
            margin-bottom: 20px;
        }

        .error-popup-close {
            position: absolute;
            top: 12px;
            right: 12px;
            background: none;
            border: none;
            color: #888;
            font-size: 16px;
            line-height: 1;
            cursor: pointer;
            transition: color 0.3s ease;
            width: 20px;
            height: 20px;
            text-align: center;
        }

        .error-popup-close:hover {
            color: #333;
        }

        .error-popup-close::before {
            content: '×';
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Log In</h1>
        <form id="loginForm" method="POST">
            <div class="form-group">
                <label for="employeeId">Employee ID</label>
                <input type="text" name="employee_id" id="employeeId" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="forgot-password">
                <a href="#">Forgot Password?</a>
            </div>
            <button type="submit">Submit</button>
        </form>
    </div>

    <?php if ($showError): ?>
    <div id="errorPopupOverlay" class="error-popup-overlay">
        <div id="errorPopup" class="error-popup">
            <button class="error-popup-close" onclick="closeErrorPopup()"></button>
            <h2>Login Failed</h2>
            <p>Incorrect Employee ID or Password</p>
        </div>
    </div>
    <?php endif; ?>

    <script>
        <?php if ($showError): ?>
        // Show error popup when page loads if login fails
        document.addEventListener('DOMContentLoaded', function() {
            const errorPopupOverlay = document.getElementById('errorPopupOverlay');
            const errorPopup = document.getElementById('errorPopup');
            
            errorPopupOverlay.style.display = 'flex';
            
            // Use setTimeout to trigger the show class for animation
            setTimeout(() => {
                errorPopup.classList.add('show');
                
                // Auto-close popup after 3 seconds
                setTimeout(closeErrorPopup, 3000);
            }, 10);
        });
        <?php endif; ?>

        function closeErrorPopup() {
            const errorPopupOverlay = document.getElementById('errorPopupOverlay');
            const errorPopup = document.getElementById('errorPopup');
            
            errorPopup.classList.remove('show');
            
            // Wait for animation to complete before hiding
            setTimeout(() => {
                errorPopupOverlay.style.display = 'none';
            }, 300);
        }

        // Optional: Close popup when clicking outside
        document.getElementById('errorPopupOverlay')?.addEventListener('click', function(event) {
            if (event.target === this) {
                closeErrorPopup();
            }
        });
    </script>
</body>
</html>
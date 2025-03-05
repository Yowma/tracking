<?php
include "db_connection.php"; // Include database connection
$response = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $birthdate = trim($_POST["birthdate"]);
    $employee_id = trim($_POST["employee_id"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    if (empty($first_name) || empty($last_name) || empty($birthdate) || empty($employee_id) || empty($password) || empty($confirm_password)) {
        $response = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $response = "Passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM employees WHERE employee_id = ?");
        $stmt->bind_param("s", $employee_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $response = "Employee ID already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO employees (first_name, last_name, birthdate, employee_id, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $first_name, $last_name, $birthdate, $employee_id, $hashed_password);

            if ($stmt->execute()) {
                header("Location: login.php"); // Redirect to login page after successful signup
                exit();
            } else {
                $response = "Database error: " . $conn->error;
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
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

        .signup-container {
            background-color: #FFF;
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 450px;
            animation: fadeIn 0.8s ease-out forwards;
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

        .form-group-row {
            display: flex;
            gap: 1rem;
        }

        .form-group-row .form-group {
            flex: 1;
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

        .error-message {
            color: #ff4444;
            font-size: 0.8rem;
            margin-top: 0.5rem;
            display: none;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-8px); }
            75% { transform: translateX(8px); }
        }

        .shake {
            animation: shake 0.4s ease;
        }

        .logo-container {
        text-align: center;
        margin-bottom: 1rem;
        }

        .logo-container img {
            max-width: 200px;
            width: 100%;
            height: auto;
        }

        @media (max-width: 480px) {
            .signup-container {
                padding: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="logo-container">
            <img src="pgsi_logo.png" alt="Logo">
        </div>
        <h1>Create Account</h1>
        <form id="signupForm" method="POST">
            <div class="form-group-row">
                <div class="form-group">
                    <label for="firstName">First Name</label>
                    <input type="text" name="first_name" id="firstName" required>
                </div>
                <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <input type="text" name="last_name" id="lastName" required>
                </div>
            </div>
            <div class="form-group">
                <label for="birthdate">Birthdate</label>
                <input type="date" name="birthdate" id="birthdate" required>
            </div>
            <div class="form-group">
                <label for="employeeId">Employee ID</label>
                <input type="text" name="employee_id" id="employeeId" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirmPassword" required>
            </div>
            <button type="submit">Sign Up</button>
            <p><?php echo $response; ?></p>
        </form>
    </div>
    <script>
        const form = document.getElementById('signupForm');
        const firstNameError = document.getElementById('firstNameError');
        const lastNameError = document.getElementById('lastNameError');
        const birthdateError = document.getElementById('birthdateError');
        const employeeIdError = document.getElementId('employeeIdError');
        const passwordError = document.getElementById('passwordError');
        const confirmPasswordError = document.getElementById('confirmPasswordError');

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const firstName = document.getElementById('firstName').value;
            const lastName = document.getElementById('lastName').value;
            const birthdate = document.getElementById('birthdate').value;
            const employeeId = document.getElementById('employeeId').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            let isValid = true;

            firstNameError.style.display = firstName ? 'none' : 'block';
            lastNameError.style.display = lastName ? 'none' : 'block';
            birthdateError.style.display = birthdate ? 'none' : 'block';
            employeeIdError.style.display = employeeId ? 'none' : 'block';
            passwordError.style.display = password ? 'none' : 'block';

            if (password !== confirmPassword) {
                confirmPasswordError.style.display = 'block';
                isValid = false;
            } else {
                confirmPasswordError.style.display = 'none';
            }

            if (isValid) {
                alert('Sign up successful!');
                form.reset();
            }
        });
    </script>
</body>
</html>
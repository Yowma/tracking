<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Database connection
$conn = new mysqli("localhost", "root", "", "powerguide");

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed!"]));
}

// Check if company_name is provided
if (!isset($_GET['company_name']) || empty($_GET['company_name'])) {
    echo json_encode(["error" => "Company name is required"]);
    exit;
}

$company_name = $conn->real_escape_string($_GET['company_name']);

// Fetch company details
$query = "SELECT * FROM companies WHERE company_name = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $company_name);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $company_data = $result->fetch_assoc();
    $company_id = $company_data['id']; // Get the company ID

    // Fetch items availed
    $items_query = "SELECT item_name, quantity FROM company_items WHERE company_id = ?";
    $stmt_items = $conn->prepare($items_query);
    $stmt_items->bind_param("i", $company_id);
    $stmt_items->execute();
    $items_result = $stmt_items->get_result();

    $items = [];
    while ($row = $items_result->fetch_assoc()) {
        $items[] = $row;
    }

    // Add items to response
    $company_data['items'] = $items;

    echo json_encode($company_data);
} else {
    echo json_encode(["error" => "Company not found"]);
}

// Close connection
$stmt->close();
$conn->close();
?>

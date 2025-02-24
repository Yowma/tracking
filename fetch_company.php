<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Database connection
$conn = new mysqli("localhost", "root", "", "powerguide");

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

if (!isset($_GET['company_name'])) {
    die(json_encode(["error" => "Company name is required"]));
}

$company_name = $conn->real_escape_string($_GET['company_name']);

// Fetch company details
$query = "SELECT * FROM companies WHERE name = '$company_name'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $company_data = $result->fetch_assoc();
    $company_id = $company_data['id']; // Get the company ID

    // Fetch items availed
    $items_query = "SELECT item_name, quantity FROM company_items WHERE company_id = '$company_id'";
    $items_result = $conn->query($items_query);

    $items = [];
    while ($row = $items_result->fetch_assoc()) {
        $items[] = $row;
    }

    // Combine company details with items
    $company_data['items'] = $items;

    echo json_encode($company_data);
} else {
    echo json_encode(["error" => "Company not found"]);
}

$conn->close();
?>

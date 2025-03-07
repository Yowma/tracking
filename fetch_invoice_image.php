<?php
session_start();
header('Content-Type: application/json');

// Database connection (adjust according to your setup)
$db = new PDO("mysql:host=localhost;dbname=your_database", "username", "password");

if (!isset($_GET['company_name']) || !isset($_GET['month']) || !isset($_GET['year'])) {
    echo json_encode(['error' => 'Missing required parameters']);
    exit;
}

$company_name = $_GET['company_name'];
$month = $_GET['month'];
$year = $_GET['year'];

try {
    $stmt = $db->prepare("
        SELECT invoice_image 
        FROM invoices 
        WHERE company_name = :company_name 
        AND MONTH(invoice_date) = :month 
        AND YEAR(invoice_date) = :year 
        LIMIT 1
    ");
    
    $stmt->execute([
        ':company_name' => $company_name,
        ':month' => $month,
        ':year' => $year
    ]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && $result['invoice_image']) {
        echo json_encode(['invoice_image' => $result['invoice_image']]);
    } else {
        echo json_encode(['error' => 'No invoice found for the selected period']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
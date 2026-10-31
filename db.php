<?php
declare(strict_types=1);

// XAMPP default settings. Update these if your MySQL credentials differ.
$dbHost = 'localhost';
$dbUser = 'root';
$dbPassword = '';
$dbName = 'wanderdesk';

mysqli_report(MYSQLI_REPORT_OFF);

$conn = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

if ($conn->connect_error) {
    http_response_code(500);
    exit(
        'Database connection failed. Check that MySQL is running and that '
        . 'the wanderdesk database has been imported. Details: '
        . htmlspecialchars($conn->connect_error, ENT_QUOTES, 'UTF-8')
    );
}

$conn->set_charset('utf8mb4');

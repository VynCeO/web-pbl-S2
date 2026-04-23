<?php
// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'bumdes_db');
define('DB_PORT', 3306);

// Koneksi Database
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to utf8
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    die("Database Error: " . $e->getMessage());
}

// Define base URL - PERHATIAN: Sesuaikan dengan URL Anda di Laragon
// Jika mengakses http://localhost/web-pbl-S2/, gunakan line di bawah ini
define('BASE_URL', 'http://localhost/web-pbl-S2');

// Jika mengakses http://web-pbl-s2.test/ (virtual host), gunakan:
// define('BASE_URL', 'http://web-pbl-s2.test');

define('SITE_NAME', 'BUMDes Sukses Bersama');

// Error handling
if (ini_get('display_errors') == 0) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

?>

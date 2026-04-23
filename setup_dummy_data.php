<?php
/**
 * Setup Dummy Data untuk BUMDes Sugihwaras
 * 
 * File ini untuk load data dummy ke database untuk testing & development
 * Jalankan file ini sekali untuk populate database dengan sample data
 * 
 * Usage:
 * 1. Copy file ini ke root folder
 * 2. Buka di browser: http://localhost/path-to-project/setup_dummy_data.php
 * 3. Atau jalankan dari terminal: php setup_dummy_data.php
 */

require_once 'app/config/database.php';

// Set timeout karena ada banyak query
set_time_limit(300);

// Function untuk execute SQL file
function executeSqlFile($conn, $filename) {
    $sql = file_get_contents($filename);
    
    if (!$sql) {
        return ['success' => false, 'message' => "File {$filename} tidak ditemukan"];
    }

    // Split multiple statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $executed = 0;
    $errors = [];

    foreach ($statements as $statement) {
        // Skip comments and empty statements
        if (empty($statement) || substr(trim($statement), 0, 2) === '--') {
            continue;
        }

        if (!$conn->query($statement)) {
            $errors[] = "Error: " . $conn->error . " | Query: " . substr($statement, 0, 100) . "...";
        } else {
            $executed++;
        }
    }

    if (empty($errors)) {
        return ['success' => true, 'message' => "{$executed} queries executed successfully"];
    } else {
        return ['success' => false, 'message' => implode("\n", $errors), 'executed' => $executed];
    }
}

// Check if database exists
$database_check = $conn->query("SELECT DATABASE()");
if (!$database_check) {
    die("Database connection error: " . $conn->error);
}

$db_name = $database_check->fetch_row()[0];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Dummy Data - BUMDes Sugihwaras</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #1f5b3a 0%, #2d7a4a 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            padding: 40px;
        }

        h1 {
            color: #1f5b3a;
            margin-bottom: 10px;
            text-align: center;
        }

        .subtitle {
            color: #666;
            text-align: center;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .info-box {
            background: #f0f7ff;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .info-box strong {
            color: #1976d2;
        }

        .button-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 20px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: #1f5b3a;
            color: white;
        }

        .btn-primary:hover {
            background: #17452c;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(31, 91, 58, 0.3);
        }

        .btn-danger {
            background: #f44336;
            color: white;
        }

        .btn-danger:hover {
            background: #d32f2f;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(244, 67, 54, 0.3);
        }

        .btn-secondary {
            background: #ff9500;
            color: white;
        }

        .btn-secondary:hover {
            background: #e68900;
        }

        .output {
            background: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
            max-height: 300px;
            overflow-y: auto;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.5;
        }

        .output.success {
            background: #e8f5e9;
            border-color: #4caf50;
            color: #2e7d32;
        }

        .output.error {
            background: #ffebee;
            border-color: #f44336;
            color: #c62828;
        }

        .status {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            text-align: center;
        }

        .status.success {
            background: #c8e6c9;
            color: #2e7d32;
            border: 1px solid #4caf50;
        }

        .status.error {
            background: #ffcdd2;
            color: #c62828;
            border: 1px solid #f44336;
        }

        .status.info {
            background: #bbdefb;
            color: #1565c0;
            border: 1px solid #2196f3;
        }

        .db-info {
            background: #fff3e0;
            border-left: 4px solid #ff9500;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 14px;
        }

        .db-info strong {
            color: #e65100;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }

            .button-group {
                grid-template-columns: 1fr;
            }

            h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎯 Setup Dummy Data</h1>
        <p class="subtitle">BUMDes Sukses Bersama - Desa Sugihwaras</p>

        <div class="db-info">
            <strong>Database:</strong> <?php echo htmlspecialchars($db_name); ?><br>
            <strong>Host:</strong> <?php echo htmlspecialchars(DB_HOST); ?>
        </div>

        <div class="info-box">
            <strong>ℹ️ Info:</strong><br>
            File ini akan load data dummy ke database untuk testing & development.<br><br>
            <strong>Data yang akan di-load:</strong>
            <ul style="margin: 10px 0 0 20px;">
                <li>✓ 4 Pimpinan dengan foto dari internet</li>
                <li>✓ 6 Unit Usaha</li>
                <li>✓ 21 Layanan (service)</li>
                <li>✓ 8 Reservasi (various status)</li>
                <li>✓ 3 Admin User (admin, marsudi, agus)</li>
                <li>✓ Kontak & Social Media</li>
            </ul>
        </div>

        <div class="button-group">
            <form method="POST" style="display: contents;">
                <button type="submit" name="action" value="load_dummy" class="btn btn-primary">
                    ✅ Load Dummy Data
                </button>
                <button type="submit" name="action" value="clear_data" class="btn btn-danger">
                    ⚠️ Clear All Data
                </button>
            </form>
        </div>

        <a href="../index.php" class="btn btn-secondary" style="width: 100%; margin-bottom: 20px;">
            ← Back to Homepage
        </a>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'load_dummy') {
                echo '<div class="status info">⏳ Loading dummy data...</div>';
                
                $result = executeSqlFile($conn, 'app/database/dummy_data.sql');
                
                if ($result['success']) {
                    echo '<div class="status success">✅ ' . htmlspecialchars($result['message']) . '</div>';
                    echo '<div class="output success">';
                    echo "Dummy data berhasil di-load ke database!<br><br>";
                    echo "Data yang ter-load:<br>";
                    echo "• Pimpinan: 4 records<br>";
                    echo "• Unit Usaha: 6 records<br>";
                    echo "• Layanan: 21 records<br>";
                    echo "• Reservasi: 8 records<br>";
                    echo "• Admin User: 3 records<br>";
                    echo "• Kontak: 1 record<br><br>";
                    echo "Credential untuk login:<br>";
                    echo "<strong>Username:</strong> admin<br>";
                    echo "<strong>Password:</strong> admin123<br>";
                    echo "</div>";
                } else {
                    echo '<div class="status error">❌ Error: ' . htmlspecialchars($result['message']) . '</div>';
                    echo '<div class="output error">' . htmlspecialchars($result['message']) . '</div>';
                }
            } elseif ($action === 'clear_data') {
                $clear_queries = [
                    "DELETE FROM reservasi",
                    "DELETE FROM layanan",
                    "DELETE FROM unit_usaha",
                    "DELETE FROM pimpinan",
                    "DELETE FROM kontak",
                    "DELETE FROM admin_user"
                ];

                $success_count = 0;
                foreach ($clear_queries as $query) {
                    if ($conn->query($query)) {
                        $success_count++;
                    }
                }

                if ($success_count === count($clear_queries)) {
                    echo '<div class="status success">✅ All data cleared successfully!</div>';
                    echo '<div class="output success">Database telah dikosongkan. Silakan load dummy data lagi untuk testing.</div>';
                } else {
                    echo '<div class="status error">❌ Error clearing data</div>';
                }
            }
        }
        ?>

        <div style="background: #fafafa; padding: 20px; border-radius: 4px; margin-top: 20px; border: 1px solid #e0e0e0;">
            <strong style="color: #1f5b3a;">📖 Panduan:</strong><br>
            <ol style="margin: 10px 0 0 20px; font-size: 13px;">
                <li>Klik tombol "Load Dummy Data" untuk populate database</li>
                <li>Tunggu proses selesai (biasanya cepat)</li>
                <li>Login ke admin dengan: username <code>admin</code>, password <code>admin123</code></li>
                <li>Akses homepage untuk melihat semua data sudah ter-load</li>
                <li>Jika perlu reset, klik "Clear All Data" terlebih dahulu</li>
            </ol>
        </div>
    </div>
</body>
</html>

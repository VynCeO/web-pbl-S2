<?php
/**
 * BUMDes Sukses Bersama - Database Setup Script
 * 
 * Jalankan script ini untuk setup database awal
 * http://localhost:8000/setup.php
 */

require_once 'config/database.php';
require_once 'includes/functions.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup'])) {
    // Baca file SQL
    $sql_file = 'database/init.sql';
    
    if (!file_exists($sql_file)) {
        $error = 'File init.sql tidak ditemukan!';
    } else {
        // Baca dan execute SQL
        $sql = file_get_contents($sql_file);
        $queries = array_filter(
            array_map('trim', explode(';', $sql)),
            function($query) {
                return !empty($query) && substr($query, 0, 2) !== '--';
            }
        );

        $success = true;
        foreach ($queries as $query) {
            if (!$conn->query($query)) {
                $error .= "Error: " . htmlspecialchars($conn->error) . "<br>";
                $success = false;
                break;
            }
        }

        if ($success) {
            $message = '✓ Database berhasil disetup! Anda bisa login ke admin panel dengan username "admin" dan password "admin123"';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup BUMDes Website</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2d5016 0%, #1e3a0f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .setup-container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
        }

        h1 {
            color: #2d5016;
            margin-bottom: 1rem;
            font-size: 1.8rem;
        }

        .info {
            background: #f0f8ff;
            border-left: 4px solid #2d5016;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 4px;
            color: #333;
        }

        .info h2 {
            font-size: 1rem;
            margin-bottom: 0.5rem;
            color: #2d5016;
        }

        .info p {
            font-size: 0.9rem;
            margin: 0.5rem 0;
        }

        .message {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }

        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .setup-form {
            margin: 1.5rem 0;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 600;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            font-family: monospace;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #2d5016;
            box-shadow: 0 0 0 3px rgba(45, 80, 22, 0.1);
        }

        .btn {
            width: 100%;
            padding: 0.75rem;
            background: #2d5016;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: #1e3a0f;
        }

        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .db-status {
            margin-top: 1rem;
            padding: 1rem;
            background: #f5f5f5;
            border-radius: 4px;
        }

        .db-status h3 {
            margin-bottom: 0.5rem;
            color: #333;
        }

        .status-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #ddd;
        }

        .status-item:last-child {
            border-bottom: none;
        }

        .status-label {
            color: #666;
        }

        .status-value {
            font-weight: 600;
            color: #2d5016;
        }

        .footer {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <h1>🏗️ Setup BUMDes Website</h1>

        <div class="info">
            <h2>Informasi Database</h2>
            <p><strong>Host:</strong> <?php echo DB_HOST; ?></p>
            <p><strong>Database:</strong> <?php echo DB_NAME; ?></p>
            <p><strong>User:</strong> <?php echo DB_USER; ?></p>
        </div>

        <?php if (!empty($message)): ?>
            <div class="message success">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="message error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($message) || !empty($error)): ?>
            <div class="setup-form">
                <h2 style="color: #2d5016; margin-bottom: 1rem;">Setup Database</h2>
                
                <form method="POST">
                    <div class="form-group">
                        <label for="confirm">
                            <input type="checkbox" id="confirm" name="confirm" required> 
                            Saya memahami bahwa ini akan membuat tabel baru dalam database
                        </label>
                    </div>

                    <button type="submit" name="setup" value="1" class="btn">Setup Database Sekarang</button>
                </form>
            </div>
        <?php endif; ?>

        <div class="db-status">
            <h3>Status Koneksi</h3>
            <div class="status-item">
                <span class="status-label">Koneksi Database:</span>
                <span class="status-value">✓ Terhubung</span>
            </div>
            <div class="status-item">
                <span class="status-label">PHP Version:</span>
                <span class="status-value"><?php echo phpversion(); ?></span>
            </div>
            <div class="status-item">
                <span class="status-label">MySQL Version:</span>
                <span class="status-value"><?php echo $conn->server_info; ?></span>
            </div>
        </div>

        <div class="footer">
            <p>Setelah setup selesai, hapus file setup.php untuk keamanan.</p>
            <p><a href="src/index.php" style="color: #2d5016; text-decoration: none;">Buka Website →</a></p>
        </div>
    </div>
</body>
</html>

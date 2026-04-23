<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

start_session();

// Check if user is logged in
if (!is_logged_in()) {
    redirect('admin/login.php');
}

// Get total data counts
$pimpinan_count = $conn->query("SELECT COUNT(*) as count FROM pimpinan")->fetch_assoc()['count'];
$unit_count = $conn->query("SELECT COUNT(*) as count FROM unit_usaha")->fetch_assoc()['count'];
$reservasi_count = $conn->query("SELECT COUNT(*) as count FROM reservasi")->fetch_assoc()['count'];
$reservasi_pending = $conn->query("SELECT COUNT(*) as count FROM reservasi WHERE status = 'pending'")->fetch_assoc()['count'];

$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - BUMDes Sukses Bersama</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #2d5016;
            --secondary-color: #ff9500;
            --light-bg: #f5f5f5;
            --dark-text: #333;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
            color: var(--dark-text);
        }

        .admin-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
        }

        .sidebar {
            background-color: var(--primary-color);
            color: white;
            padding: 2rem 0;
            position: fixed;
            width: 250px;
            height: 100vh;
            overflow-y: auto;
            left: 0;
            top: 0;
        }

        .sidebar h2 {
            padding: 0 1.5rem;
            margin-bottom: 2rem;
            font-size: 1.2rem;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
            padding-bottom: 1rem;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar li {
            margin: 0.5rem 0;
        }

        .sidebar a {
            display: block;
            padding: 0.75rem 1.5rem;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s ease;
            border-left: 3px solid transparent;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: rgba(255, 255, 255, 0.1);
            border-left-color: var(--secondary-color);
        }

        .main-content {
            margin-left: 250px;
            padding: 2rem;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            color: var(--primary-color);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logout-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #c82333;
        }

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-left: 4px solid var(--primary-color);
        }

        .card h3 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            text-transform: uppercase;
        }

        .card .count {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--secondary-color);
        }

        .flash-message {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }

        .flash-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .flash-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .flash-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        @media (max-width: 768px) {
            .admin-container {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }

            .main-content {
                margin-left: 0;
            }

            .dashboard-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <sidebar class="sidebar">
            <h2>Menu Admin</h2>
            <ul>
                <li><a href="<?php echo BASE_URL; ?>/admin/index.php" class="active">Dashboard</a></li>
                <li><a href="<?php echo BASE_URL; ?>/admin/manage_pimpinan.php">Kelola Pimpinan</a></li>
                <li><a href="<?php echo BASE_URL; ?>/admin/manage_unit.php">Kelola Unit Usaha</a></li>
                <li><a href="<?php echo BASE_URL; ?>/admin/manage_reservasi.php">Kelola Reservasi</a></li>
                <li><a href="<?php echo BASE_URL; ?>/admin/manage_kontak.php">Kelola Kontak</a></li>
                <li><a href="<?php echo BASE_URL; ?>/admin/logout.php">Logout</a></li>
            </ul>
        </sidebar>

        <div class="main-content">
            <div class="header">
                <div>
                    <h1>Dashboard Admin</h1>
                    <p>Selamat datang, <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?></p>
                </div>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="<?php echo BASE_URL; ?>/admin/logout.php" class="logout-btn">Logout</a>
                </div>
            </div>

            <?php if ($flash): ?>
                <div class="flash-message flash-<?php echo htmlspecialchars($flash['type']); ?>">
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>

            <div class="dashboard-cards">
                <div class="card">
                    <h3>Total Pimpinan</h3>
                    <div class="count"><?php echo $pimpinan_count; ?></div>
                </div>
                <div class="card">
                    <h3>Total Unit Usaha</h3>
                    <div class="count"><?php echo $unit_count; ?></div>
                </div>
                <div class="card">
                    <h3>Total Reservasi</h3>
                    <div class="count"><?php echo $reservasi_count; ?></div>
                </div>
                <div class="card">
                    <h3>Reservasi Pending</h3>
                    <div class="count" style="color: #ff9500;"><?php echo $reservasi_pending; ?></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

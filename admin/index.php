<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

start_session();

// Check if user is logged in
if (!is_logged_in()) {
    redirect('/admin/login.php');
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
            padding: 2rem 1rem;
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 250px;
            overflow-y: auto;
        }

        .sidebar h2 {
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar li {
            margin-bottom: 0.5rem;
        }

        .sidebar a {
            display: block;
            padding: 0.75rem 1rem;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: var(--secondary-color);
            color: var(--primary-color);
        }

        .main-content {
            margin-left: 250px;
            padding: 2rem;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .top-bar h1 {
            font-size: 1.5rem;
            color: var(--primary-color);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-name {
            text-align: right;
        }

        .user-name p {
            margin: 0;
            font-size: 0.9rem;
            color: #666;
        }

        .logout-btn {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #ff7d00;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-top: 4px solid var(--secondary-color);
        }

        .stat-card h3 {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
        }

        .stat-card .number {
            font-size: 2.5rem;
            color: var(--primary-color);
            font-weight: bold;
        }

        .stat-card a {
            display: inline-block;
            margin-top: 1rem;
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.9rem;
        }

        .stat-card a:hover {
            text-decoration: underline;
        }

        .flash-message {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .flash-message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .flash-message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .recent-section {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .recent-section h2 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table thead {
            background-color: var(--light-bg);
        }

        table th, table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            font-weight: 600;
            color: var(--primary-color);
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        @media (max-width: 768px) {
            .admin-container {
                grid-template-columns: 1fr;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .main-content {
                margin-left: 0;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .top-bar {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .user-info {
                width: 100%;
                justify-content: space-between;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="index.php" class="active">📊 Dashboard</a></li>
                <li><a href="manage_pimpinan.php">👥 Pimpinan</a></li>
                <li><a href="manage_unit.php">🏢 Unit Usaha</a></li>
                <li><a href="manage_reservasi.php">📅 Reservasi</a></li>
                <li><a href="manage_kontak.php">📞 Kontak</a></li>
                <li style="margin-top: 2rem;"><a href="logout.php">🚪 Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <div class="top-bar">
                <h1>Dashboard</h1>
                <div class="user-info">
                    <div class="user-name">
                        <p>Selamat Datang</p>
                        <strong><?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?></strong>
                    </div>
                    <a href="logout.php" class="logout-btn">Logout</a>
                </div>
            </div>

            <!-- Flash Message -->
            <?php if ($flash): ?>
                <div class="flash-message <?php echo $flash['type']; ?>">
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>

            <!-- Dashboard Stats -->
            <div class="dashboard-grid">
                <div class="stat-card">
                    <h3>Total Pimpinan</h3>
                    <div class="number"><?php echo $pimpinan_count; ?></div>
                    <a href="manage_pimpinan.php">Lihat Pimpinan →</a>
                </div>

                <div class="stat-card">
                    <h3>Total Unit Usaha</h3>
                    <div class="number"><?php echo $unit_count; ?></div>
                    <a href="manage_unit.php">Lihat Unit →</a>
                </div>

                <div class="stat-card">
                    <h3>Total Reservasi</h3>
                    <div class="number"><?php echo $reservasi_count; ?></div>
                    <a href="manage_reservasi.php">Lihat Reservasi →</a>
                </div>

                <div class="stat-card">
                    <h3>Reservasi Pending</h3>
                    <div class="number" style="color: #ff9500;"><?php echo $reservasi_pending; ?></div>
                    <a href="manage_reservasi.php?status=pending">Lihat Pending →</a>
                </div>
            </div>

            <!-- Recent Reservasi -->
            <div class="recent-section">
                <h2>Reservasi Terbaru</h2>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>No. HP</th>
                                <th>Tanggal</th>
                                <th>Unit</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT r.*, u.nama as unit_nama FROM reservasi r 
                                      LEFT JOIN unit_usaha u ON r.unit_usaha_id = u.id 
                                      ORDER BY r.created_at DESC LIMIT 5";
                            $result = $conn->query($query);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $status_class = 'status-' . strtolower($row['status']);
                                    echo "<tr>";
                                    echo "<td>#" . htmlspecialchars($row['id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['no_hp']) . "</td>";
                                    echo "<td>" . date('d M Y', strtotime($row['tanggal'])) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['unit_nama']) . "</td>";
                                    echo "<td><span class=\"status-badge $status_class\">" . ucfirst($row['status']) . "</span></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' style='text-align: center; padding: 2rem;'>Belum ada reservasi</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

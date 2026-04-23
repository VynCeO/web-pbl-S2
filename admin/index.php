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
            --white: #ffffff;
            --dark-text: #333;
            --border-color: #e0e0e0;
        }

        html, body {
            height: 100%;
            width: 100%;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
            color: var(--dark-text);
            line-height: 1.6;
        }

        /* LAYOUT CONTAINER */
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            width: 250px;
            background-color: var(--primary-color);
            color: white;
            padding: 2rem 0;
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.15);
            z-index: 100;
        }

        .sidebar h2 {
            font-size: 1.2rem;
            padding: 0 1.5rem 1rem;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar li {
            margin: 0;
        }

        .sidebar a {
            display: block;
            padding: 0.9rem 1.5rem;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .sidebar a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-left-color: var(--secondary-color);
            color: white;
        }

        .sidebar a.active {
            background-color: var(--secondary-color);
            color: var(--primary-color);
            border-left-color: var(--primary-color);
        }

        /* MAIN CONTENT */
        .main-content {
            margin-left: 250px;
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
        }

        /* TOP BAR */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            gap: 2rem;
        }

        .top-bar h1 {
            font-size: 1.8rem;
            color: var(--primary-color);
            margin: 0;
        }

        .user-section {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-left: auto;
        }

        .user-info {
            text-align: right;
        }

        .user-info p {
            margin: 0;
            font-size: 0.85rem;
            color: #999;
        }

        .user-info strong {
            display: block;
            font-size: 1rem;
            color: var(--primary-color);
            margin-top: 0.25rem;
        }

        .logout-btn {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .logout-btn:hover {
            background-color: #ff7d00;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        /* FLASH MESSAGE */
        .flash-message {
            padding: 1rem 1.5rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
            border-left: 4px solid;
        }

        .flash-message.success {
            background-color: #d4edda;
            color: #155724;
            border-left-color: #28a745;
        }

        .flash-message.error {
            background-color: #f8d7da;
            color: #721c24;
            border-left-color: #dc3545;
        }

        .flash-message.info {
            background-color: #d1ecf1;
            color: #0c5460;
            border-left-color: #17a2b8;
        }

        /* DASHBOARD GRID */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        /* STAT CARD */
        .stat-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-top: 4px solid var(--secondary-color);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .stat-card h3 {
            color: #999;
            font-size: 0.85rem;
            margin-bottom: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .stat-card .number {
            font-size: 2.8rem;
            color: var(--secondary-color);
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .stat-card a {
            display: inline-block;
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .stat-card a:hover {
            color: var(--secondary-color);
        }

        /* SECTION */
        .section {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .section h2 {
            font-size: 1.3rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--light-bg);
        }

        /* TABLE */
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

        table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--primary-color);
            border-bottom: 2px solid var(--border-color);
        }

        table td {
            padding: 0.9rem 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        table tbody tr:hover {
            background-color: #fafafa;
        }

        /* STATUS BADGE */
        .status-badge {
            display: inline-block;
            padding: 0.35rem 0.9rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-confirmed {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-aktif {
            background-color: #d4edda;
            color: #155724;
        }

        .status-nonaktif {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* EMPTY STATE */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #999;
        }

        .empty-state p {
            font-size: 1rem;
        }

        /* SCROLLBAR */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* RESPONSIVE */
        @media (max-width: 1024px) {
            .main-content {
                padding: 1.5rem;
            }

            .dashboard-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .top-bar {
                flex-wrap: wrap;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .top-bar {
                flex-direction: column;
                align-items: flex-start;
            }

            .user-section {
                width: 100%;
                margin-left: 0;
                justify-content: space-between;
            }

            .user-info {
                text-align: left;
            }

            table {
                font-size: 0.9rem;
            }

            table th, table td {
                padding: 0.6rem 0.5rem;
            }

            .stat-card {
                padding: 1.5rem;
            }

            .stat-card .number {
                font-size: 2rem;
            }

            .section {
                padding: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .sidebar {
                position: fixed;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 1000;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                padding: 1rem;
            }

            .dashboard-grid {
                gap: 1rem;
            }

            .top-bar h1 {
                font-size: 1.5rem;
            }

            table th {
                font-size: 0.85rem;
            }

            table td {
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- SIDEBAR -->
        <div class="sidebar">
            <h2>🏢 Admin Panel</h2>
            <ul>
                <li><a href="<?php echo BASE_URL; ?>/admin/index.php" class="active">📊 Dashboard</a></li>
                <li><a href="<?php echo BASE_URL; ?>/admin/manage_pimpinan.php">👥 Pimpinan</a></li>
                <li><a href="<?php echo BASE_URL; ?>/admin/manage_unit.php">🏭 Unit Usaha</a></li>
                <li><a href="<?php echo BASE_URL; ?>/admin/manage_reservasi.php">📅 Reservasi</a></li>
                <li><a href="<?php echo BASE_URL; ?>/admin/manage_kontak.php">📞 Kontak</a></li>
                <li style="margin-top: 3rem; padding-top: 1.5rem; border-top: 1px solid rgba(255, 255, 255, 0.1);">
                    <a href="<?php echo BASE_URL; ?>/admin/logout.php">🚪 Logout</a>
                </li>
            </ul>
        </div>

        <!-- MAIN CONTENT -->
        <div class="main-content">
            <!-- TOP BAR -->
            <div class="top-bar">
                <h1>Dashboard</h1>
                <div class="user-section">
                    <div class="user-info">
                        <p>Selamat Datang</p>
                        <strong><?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?></strong>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/admin/logout.php" class="logout-btn">Logout</a>
                </div>
            </div>

            <!-- FLASH MESSAGE -->
            <?php if ($flash): ?>
                <div class="flash-message <?php echo htmlspecialchars($flash['type']); ?>">
                    ✓ <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>

            <!-- DASHBOARD STATS -->
            <div class="dashboard-grid">
                <div class="stat-card">
                    <h3>👥 Total Pimpinan</h3>
                    <div class="number"><?php echo $pimpinan_count; ?></div>
                    <a href="<?php echo BASE_URL; ?>/admin/manage_pimpinan.php">Kelola Pimpinan →</a>
                </div>

                <div class="stat-card">
                    <h3>🏭 Total Unit Usaha</h3>
                    <div class="number"><?php echo $unit_count; ?></div>
                    <a href="<?php echo BASE_URL; ?>/admin/manage_unit.php">Kelola Unit →</a>
                </div>

                <div class="stat-card">
                    <h3>📅 Total Reservasi</h3>
                    <div class="number"><?php echo $reservasi_count; ?></div>
                    <a href="<?php echo BASE_URL; ?>/admin/manage_reservasi.php">Lihat Semua →</a>
                </div>

                <div class="stat-card">
                    <h3>⚠️ Reservasi Pending</h3>
                    <div class="number"><?php echo $reservasi_pending; ?></div>
                    <a href="<?php echo BASE_URL; ?>/admin/manage_reservasi.php?status=pending">Review Pending →</a>
                </div>
            </div>

            <!-- RECENT RESERVATIONS -->
            <div class="section">
                <h2>📋 Reservasi Terbaru</h2>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Pemesan</th>
                                <th>No. HP</th>
                                <th>Tanggal Pesan</th>
                                <th>Unit Usaha</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT r.*, u.nama as unit_nama FROM reservasi r 
                                      LEFT JOIN unit_usaha u ON r.unit_usaha_id = u.id 
                                      ORDER BY r.created_at DESC LIMIT 10";
                            $result = $conn->query($query);

                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $status_class = 'status-' . strtolower(str_replace(' ', '-', $row['status']));
                                    echo "<tr>";
                                    echo "<td><strong>#" . htmlspecialchars($row['id']) . "</strong></td>";
                                    echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['no_hp']) . "</td>";
                                    echo "<td>" . date('d M Y', strtotime($row['created_at'])) . "</td>";
                                    echo "<td>" . (isset($row['unit_nama']) ? htmlspecialchars($row['unit_nama']) : '-') . "</td>";
                                    echo "<td><span class=\"status-badge $status_class\">" . ucfirst($row['status']) . "</span></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6'><div class='empty-state'><p>Belum ada data reservasi</p></div></td></tr>";
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

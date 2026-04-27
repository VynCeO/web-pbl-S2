<?php
require_once '../app/config/database.php';
require_once '../app/includes/functions.php';

start_session();

if (!is_logged_in()) {
    redirect('/admin/login.php');
}

// Get financial data
$total_reservasi = $conn->query("SELECT COUNT(*) as count FROM reservasi WHERE status != 'cancelled'")->fetch_assoc()['count'];
$confirmed_reservasi = $conn->query("SELECT COUNT(*) as count FROM reservasi WHERE status = 'confirmed'")->fetch_assoc()['count'];
$pending_reservasi = $conn->query("SELECT COUNT(*) as count FROM reservasi WHERE status = 'pending'")->fetch_assoc()['count'];
$completed_reservasi = $conn->query("SELECT COUNT(*) as count FROM reservasi WHERE status = 'completed'")->fetch_assoc()['count'];

// Get reservasi data for revenue calculation
$query = "SELECT r.*, u.nama as unit_nama FROM reservasi r 
          LEFT JOIN unit_usaha u ON r.unit_usaha_id = u.id 
          WHERE r.status != 'cancelled'
          ORDER BY r.created_at DESC";
$result = $conn->query($query);
$reservasi_list = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reservasi_list[] = $row;
    }
}

// Calculate duration for each reservation
foreach ($reservasi_list as &$res) {
    $start = new DateTime($res['tanggal']);
    $end = new DateTime($res['tanggal_kembali'] ?? $res['tanggal']);
    $interval = $start->diff($end);
    $res['durasi_hari'] = $interval->days + 1;
}

// Get data grouped by unit
$unit_query = "SELECT u.id, u.nama, COUNT(r.id) as jumlah_reservasi 
               FROM unit_usaha u 
               LEFT JOIN reservasi r ON u.id = r.unit_usaha_id AND r.status != 'cancelled'
               GROUP BY u.id, u.nama";
$unit_result = $conn->query($unit_query);
$unit_stats = [];
if ($unit_result->num_rows > 0) {
    while ($row = $unit_result->fetch_assoc()) {
        $unit_stats[] = $row;
    }
}

// Get monthly reservasi data
$monthly_query = "SELECT DATE_FORMAT(tanggal, '%Y-%m') as bulan, COUNT(*) as jumlah 
                  FROM reservasi 
                  WHERE status != 'cancelled' 
                  GROUP BY DATE_FORMAT(tanggal, '%Y-%m')
                  ORDER BY bulan DESC
                  LIMIT 12";
$monthly_result = $conn->query($monthly_query);
$monthly_stats = [];
if ($monthly_result->num_rows > 0) {
    while ($row = $monthly_result->fetch_assoc()) {
        $monthly_stats[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - BUMDes Sukses Bersama</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #1f5b3a;
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

        .section {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .section h2 {
            color: var(--primary-color);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--secondary-color);
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
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

        .back-btn {
            display: inline-block;
            margin-bottom: 1rem;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }

        .back-btn:hover {
            text-decoration: underline;
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
                <li><a href="index.php">📊 Dashboard</a></li>
                <li><a href="manage_pimpinan.php">👥 Pimpinan</a></li>
                <li><a href="manage_unit.php">🏢 Unit Usaha</a></li>
                <li><a href="manage_reservasi.php">📅 Reservasi</a></li>
                <li><a href="manage_kontak.php">📞 Kontak</a></li>
                <li><a href="laporan_keuangan.php" class="active">📊 Laporan Keuangan</a></li>
                <li style="margin-top: 2rem;"><a href="logout.php">🚪 Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <div class="top-bar">
                <h1>Laporan Keuangan</h1>
                <div class="user-info">
                    <div class="user-name">
                        <p>Selamat Datang</p>
                        <strong><?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?></strong>
                    </div>
                    <a href="logout.php" class="logout-btn">Logout</a>
                </div>
            </div>

            <a href="index.php" class="back-btn">← Kembali ke Dashboard</a>

            <!-- Statistics Cards -->
            <div class="dashboard-grid">
                <div class="stat-card">
                    <h3>Total Reservasi</h3>
                    <div class="number"><?php echo $total_reservasi; ?></div>
                </div>

                <div class="stat-card">
                    <h3>Reservasi Confirmed</h3>
                    <div class="number" style="color: #4caf50;"><?php echo $confirmed_reservasi; ?></div>
                </div>

                <div class="stat-card">
                    <h3>Reservasi Pending</h3>
                    <div class="number" style="color: #ff9500;"><?php echo $pending_reservasi; ?></div>
                </div>

                <div class="stat-card">
                    <h3>Reservasi Completed</h3>
                    <div class="number" style="color: #2196f3;"><?php echo $completed_reservasi; ?></div>
                </div>
            </div>

            <!-- Unit Usaha Statistics -->
            <div class="section">
                <h2>Statistik per Unit Usaha</h2>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Unit Usaha</th>
                                <th>Jumlah Reservasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($unit_stats as $unit): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($unit['nama']); ?></td>
                                    <td><?php echo $unit['jumlah_reservasi']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Monthly Statistics -->
            <div class="section">
                <h2>Statistik Bulanan</h2>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Bulan</th>
                                <th>Jumlah Reservasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($monthly_stats as $month): ?>
                                <tr>
                                    <td><?php echo date('F Y', strtotime($month['bulan'] . '-01')); ?></td>
                                    <td><?php echo $month['jumlah']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Reservations -->
            <div class="section">
                <h2>Riwayat Reservasi</h2>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>No. HP</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Kembali</th>
                                <th>Durasi (Hari)</th>
                                <th>Unit</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservasi_list as $res): ?>
                                <tr>
                                    <td>#<?php echo htmlspecialchars($res['id']); ?></td>
                                    <td><?php echo htmlspecialchars($res['nama']); ?></td>
                                    <td><?php echo htmlspecialchars($res['no_hp']); ?></td>
                                    <td><?php echo date('d M Y', strtotime($res['tanggal'])); ?></td>
                                    <td><?php echo date('d M Y', strtotime($res['tanggal_kembali'] ?? $res['tanggal'])); ?></td>
                                    <td><?php echo $res['durasi_hari']; ?></td>
                                    <td><?php echo htmlspecialchars($res['unit_nama']); ?></td>
                                    <td><span class="status-badge status-<?php echo strtolower($res['status']); ?>"><?php echo ucfirst($res['status']); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

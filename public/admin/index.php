<?php
require_once '../../app/config/database.php';
require_once '../../app/includes/functions.php';

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
            z-index: 1000;
            transition: left 0.3s ease;
        }

        .sidebar.mobile-open {
            left: 0;
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
            transition: margin-left 0.3s ease;
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
        }

        .menu-close {
            display: none;
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            color: white;
            font-size: 1.8rem;
            cursor: pointer;
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
            gap: 1rem;
            flex-wrap: wrap;
        }

        .top-bar h1 {
            font-size: 1.5rem;
            color: var(--primary-color);
            flex: 1;
            min-width: 150px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .user-name {
            text-align: right;
            min-width: 150px;
        }

        .user-name p {
            margin: 0;
            font-size: 0.9rem;
            color: #666;
        }

        .user-name strong {
            display: block;
            font-size: 0.95rem;
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
            white-space: nowrap;
            font-size: 0.95rem;
        }

        .logout-btn:hover {
            background-color: #ff7d00;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-top: 4px solid var(--secondary-color);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .stat-card h3 {
            color: #666;
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card .number {
            font-size: 2rem;
            color: var(--primary-color);
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .stat-card a {
            display: inline-block;
            margin-top: 1rem;
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
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
            font-size: 0.95rem;
        }

        table th {
            font-weight: 600;
            color: var(--primary-color);
            background-color: var(--light-bg);
        }

        .status-badge {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
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

        /* Mobile Styles */
        @media (max-width: 1024px) {
            .admin-container {
                grid-template-columns: 1fr;
            }

            .sidebar {
                width: 70vw;
                left: -70vw;
            }

            .sidebar.mobile-open {
                left: 0;
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            .menu-toggle {
                display: block;
            }

            .menu-close {
                display: block;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .stat-card {
                padding: 1rem;
            }

            .stat-card h3 {
                font-size: 0.8rem;
            }

            .stat-card .number {
                font-size: 1.8rem;
            }

            .top-bar {
                flex-direction: column;
                align-items: flex-start;
                padding: 1rem;
                gap: 0.5rem;
            }

            .top-bar h1 {
                font-size: 1.3rem;
                width: 100%;
            }

            .user-info {
                width: 100%;
                justify-content: space-between;
            }

            .user-name {
                text-align: left;
                min-width: auto;
            }

            .logout-btn {
                padding: 0.5rem 0.8rem;
                font-size: 0.85rem;
            }

            .recent-section {
                padding: 1rem;
            }

            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            table {
                font-size: 0.9rem;
                min-width: 500px;
            }

            table th, table td {
                padding: 0.5rem;
                font-size: 0.85rem;
            }

            .sidebar li {
                margin-bottom: 0.25rem;
            }

            .sidebar a {
                padding: 0.6rem 0.8rem;
                font-size: 0.95rem;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 80vw;
                left: -80vw;
            }

            .main-content {
                padding: 0.75rem;
            }

            .top-bar {
                padding: 0.75rem;
                margin-bottom: 1rem;
            }

            .top-bar h1 {
                font-size: 1.1rem;
            }

            .dashboard-grid {
                gap: 0.75rem;
            }

            .stat-card {
                padding: 0.75rem;
            }

            .stat-card h3 {
                font-size: 0.75rem;
            }

            .stat-card .number {
                font-size: 1.5rem;
            }

            .stat-card a {
                font-size: 0.8rem;
            }

            .recent-section {
                padding: 0.75rem;
            }

            .recent-section h2 {
                font-size: 1.1rem;
            }

            table th, table td {
                padding: 0.4rem;
                font-size: 0.75rem;
            }

            .status-badge {
                padding: 0.3rem 0.6rem;
                font-size: 0.7rem;
            }

            .user-name p,
            .user-name strong {
                font-size: 0.85rem;
            }

            .logout-btn {
                padding: 0.4rem 0.6rem;
                font-size: 0.8rem;
            }
        }

        @media (max-width: 480px) {
            .sidebar {
                width: 100%;
                left: -100%;
            }

            .admin-container {
                min-height: auto;
            }

            .main-content {
                padding: 0.5rem;
            }

            .top-bar {
                padding: 0.5rem;
                margin-bottom: 0.5rem;
            }

            .top-bar h1 {
                font-size: 1rem;
                margin-bottom: 0.5rem;
            }

            .user-info {
                flex-direction: column;
                align-items: flex-start;
                width: 100%;
            }

            .user-name {
                width: 100%;
                text-align: left;
            }

            .logout-btn {
                width: 100%;
                text-align: center;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }

            .stat-card {
                padding: 0.6rem;
            }

            .stat-card h3 {
                font-size: 0.7rem;
            }

            .stat-card .number {
                font-size: 1.3rem;
            }

            .stat-card a {
                font-size: 0.75rem;
                margin-top: 0.5rem;
            }

            .recent-section {
                padding: 0.5rem;
                margin-top: 0.5rem;
            }

            .recent-section h2 {
                font-size: 1rem;
                margin-bottom: 0.5rem;
            }

            table {
                font-size: 0.8rem;
                min-width: 100%;
            }

            table th, table td {
                padding: 0.3rem;
                font-size: 0.7rem;
            }

            .status-badge {
                padding: 0.2rem 0.4rem;
                font-size: 0.65rem;
            }

            .sidebar h2 {
                font-size: 1.1rem;
                margin-bottom: 1rem;
            }

            .sidebar a {
                padding: 0.5rem 0.6rem;
                font-size: 0.9rem;
            }

            .menu-close {
                top: 0.5rem;
                right: 0.5rem;
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <button class="menu-close" onclick="toggleSidebar()">✕</button>
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="index.php" class="active">📊 Dashboard</a></li>
                <li><a href="manage_pimpinan.php">👥 Pimpinan</a></li>
                <li><a href="manage_unit.php">🏢 Unit Usaha</a></li>
                <li><a href="manage_reservasi.php">📅 Reservasi</a></li>
                <li><a href="manage_kontak.php">📞 Kontak</a></li>
                <li><a href="laporan_keuangan.php">💰 Laporan Keuangan</a></li>
                <li style="margin-top: 2rem;"><a href="logout.php">🚪 Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <div class="top-bar">
                <button class="menu-toggle" onclick="toggleSidebar()">☰</button>
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

                <div class="stat-card">
                    <h3>Laporan Keuangan</h3>
                    <div class="number">📊</div>
                    <a href="laporan_keuangan.php">Lihat Laporan →</a>
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

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('mobile-open');
        }

        // Close sidebar when clicking on a link
        document.querySelectorAll('.sidebar a').forEach(link => {
            link.addEventListener('click', function() {
                const sidebar = document.getElementById('sidebar');
                if (window.innerWidth <= 1024) {
                    sidebar.classList.remove('mobile-open');
                }
            });
        });

        // Close sidebar when clicking outside
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const menuToggle = document.querySelector('.menu-toggle');
            if (sidebar && !sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
                sidebar.classList.remove('mobile-open');
            }
        });
    </script>
</body>
</html>

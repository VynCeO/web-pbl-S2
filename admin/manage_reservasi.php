<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

start_session();

if (!is_logged_in()) {
    redirect('/admin/login.php');
}

// Handle actions
$action = isset($_GET['action']) ? sanitize($_GET['action']) : '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$flash = get_flash();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $post_action = sanitize($_POST['action']);
    
    if ($post_action === 'update_status') {
        $update_id = (int)($_POST['id'] ?? 0);
        $new_status = sanitize($_POST['status'] ?? '');
        
        if ($update_id > 0 && !empty($new_status)) {
            $data = ['status' => $new_status];
            if (update_data($conn, 'reservasi', $data, $update_id)) {
                set_flash('success', 'Status reservasi berhasil diperbarui');
                redirect('/admin/manage_reservasi.php' . ($status_filter ? '?status=' . $status_filter : ''));
            }
        }
    } elseif ($post_action === 'delete') {
        $delete_id = (int)($_POST['id'] ?? 0);
        if ($delete_id > 0 && delete_data($conn, 'reservasi', $delete_id)) {
            set_flash('success', 'Reservasi berhasil dihapus');
            redirect('/admin/manage_reservasi.php' . ($status_filter ? '?status=' . $status_filter : ''));
        }
    }
}

// Get reservations
$where = '';
if ($status_filter) {
    $where = "status = '$status_filter'";
}

$query = "SELECT r.*, u.nama as unit_nama FROM reservasi r 
          LEFT JOIN unit_usaha u ON r.unit_usaha_id = u.id";

if ($where) {
    $query .= " WHERE $where";
}

$query .= " ORDER BY r.created_at DESC";

$result = $conn->query($query);
$reservasi = [];

if ($result->num_rows > 0) {
    $reservasi = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Reservasi - BUMDes Admin</title>
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
            --danger-color: #f44336;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
        }

        .admin-layout {
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

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .page-header h1 {
            color: var(--primary-color);
            font-size: 1.5rem;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: #1e3a0f;
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: #da190b;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
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

        .list-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
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

        .filter-buttons {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .filter-buttons a {
            padding: 0.5rem 1rem;
            background-color: #e0e0e0;
            color: #333;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .filter-buttons a.active {
            background-color: var(--primary-color);
            color: white;
        }

        .filter-buttons a:hover {
            background-color: var(--secondary-color);
            color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .admin-layout {
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

            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="index.php">📊 Dashboard</a></li>
                <li><a href="manage_pimpinan.php">👥 Pimpinan</a></li>
                <li><a href="manage_unit.php">🏢 Unit Usaha</a></li>
                <li><a href="manage_reservasi.php" class="active">📅 Reservasi</a></li>
                <li><a href="manage_kontak.php">📞 Kontak</a></li>
                <li style="margin-top: 2rem;"><a href="logout.php">🚪 Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="page-header">
                <h1>Manajemen Reservasi</h1>
                <a href="index.php" class="btn btn-secondary">← Kembali</a>
            </div>

            <?php if ($flash): ?>
                <div class="flash-message <?php echo $flash['type']; ?>">
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>

            <div class="list-card">
                <div class="filter-buttons">
                    <a href="manage_reservasi.php" class="<?php echo empty($status_filter) ? 'active' : ''; ?>">Semua</a>
                    <a href="?status=pending" class="<?php echo ($status_filter === 'pending') ? 'active' : ''; ?>">Pending</a>
                    <a href="?status=confirmed" class="<?php echo ($status_filter === 'confirmed') ? 'active' : ''; ?>">Confirmed</a>
                    <a href="?status=completed" class="<?php echo ($status_filter === 'completed') ? 'active' : ''; ?>">Completed</a>
                    <a href="?status=cancelled" class="<?php echo ($status_filter === 'cancelled') ? 'active' : ''; ?>">Cancelled</a>
                </div>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>No. HP</th>
                                <th>Tanggal</th>
                                <th>Unit Usaha</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (count($reservasi) > 0) {
                                foreach ($reservasi as $item) {
                                    $status_class = 'status-' . strtolower($item['status']);
                                    echo "<tr>";
                                    echo "<td>#" . htmlspecialchars($item['id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($item['nama']) . "</td>";
                                    echo "<td><a href='tel:" . htmlspecialchars($item['no_hp']) . "'>" . htmlspecialchars($item['no_hp']) . "</a></td>";
                                    echo "<td>" . date('d M Y', strtotime($item['tanggal'])) . "</td>";
                                    echo "<td>" . htmlspecialchars($item['unit_nama']) . "</td>";
                                    echo "<td><span class='status-badge $status_class'>" . ucfirst($item['status']) . "</span></td>";
                                    echo "<td>";
                                    echo "<div class='action-buttons'>";
                                    
                                    // Status update form
                                    echo "<form method='POST' style='display:inline;'>";
                                    echo "<input type='hidden' name='action' value='update_status'>";
                                    echo "<input type='hidden' name='id' value='" . $item['id'] . "'>";
                                    echo "<select name='status' onchange='this.form.submit();' style='padding:0.4rem; font-size:0.9rem;'>";
                                    echo "<option value='pending' " . ($item['status'] === 'pending' ? 'selected' : '') . ">Pending</option>";
                                    echo "<option value='confirmed' " . ($item['status'] === 'confirmed' ? 'selected' : '') . ">Confirmed</option>";
                                    echo "<option value='completed' " . ($item['status'] === 'completed' ? 'selected' : '') . ">Completed</option>";
                                    echo "<option value='cancelled' " . ($item['status'] === 'cancelled' ? 'selected' : '') . ">Cancelled</option>";
                                    echo "</select>";
                                    echo "</form>";
                                    
                                    // Delete form
                                    echo "<form method='POST' style='display:inline;' onsubmit='return confirm(\"Yakin hapus?\");'>";
                                    echo "<input type='hidden' name='action' value='delete'>";
                                    echo "<input type='hidden' name='id' value='" . $item['id'] . "'>";
                                    echo "<button type='submit' class='btn btn-danger btn-sm'>Hapus</button>";
                                    echo "</form>";
                                    
                                    echo "</div>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' style='text-align: center; padding: 2rem;'>Belum ada reservasi</td></tr>";
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

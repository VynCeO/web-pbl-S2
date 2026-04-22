<?php
require_once '../../app/config/database.php';
require_once '../../app/includes/functions.php';

start_session();

if (!is_logged_in()) {
    redirect('/admin/login.php');
}

// Handle actions
$action = isset($_GET['action']) ? sanitize($_GET['action']) : '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$flash = get_flash();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $post_action = sanitize($_POST['action']);
    
    if ($post_action === 'add' || $post_action === 'edit') {
        $nama = sanitize($_POST['nama'] ?? '');
        $deskripsi = sanitize($_POST['deskripsi'] ?? '');
        $urutan = (int)($_POST['urutan'] ?? 1);
        $status = sanitize($_POST['status'] ?? 'aktif');
        
        if (empty($nama)) {
            set_flash('error', 'Nama unit usaha harus diisi');
        } else {
            $data = [
                'nama' => $nama,
                'deskripsi' => $deskripsi,
                'urutan' => $urutan,
                'status' => $status
            ];
            
            if ($post_action === 'add') {
                if (insert_data($conn, 'unit_usaha', $data)) {
                    set_flash('success', 'Unit usaha berhasil ditambahkan');
                    redirect('/admin/manage_unit.php');
                }
            } else {
                $edit_id = (int)($_POST['id'] ?? 0);
                if ($edit_id > 0 && update_data($conn, 'unit_usaha', $data, $edit_id)) {
                    set_flash('success', 'Unit usaha berhasil diperbarui');
                    redirect('/admin/manage_unit.php');
                }
            }
        }
    } elseif ($post_action === 'delete') {
        $delete_id = (int)($_POST['id'] ?? 0);
        if ($delete_id > 0 && delete_data($conn, 'unit_usaha', $delete_id)) {
            set_flash('success', 'Unit usaha berhasil dihapus');
            redirect('/admin/manage_unit.php');
        }
    }
}

$unit = null;
$edit_mode = false;

if ($action === 'edit' && $id > 0) {
    $unit = get_data_by_id($conn, 'unit_usaha', $id);
    if ($unit) {
        $edit_mode = true;
    }
}

$all_units = get_all_data($conn, 'unit_usaha', '', 'urutan ASC');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Unit Usaha - BUMDes Admin</title>
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
            --success-color: #4caf50;
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

        .content-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 2rem;
        }

        .form-card,
        .list-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .form-card h2 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
            font-size: 1rem;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(45, 80, 22, 0.1);
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
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
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-aktif {
            background-color: #d4edda;
            color: #155724;
        }

        .status-nonaktif {
            background-color: #f8d7da;
            color: #721c24;
        }

        @media (max-width: 1024px) {
            .content-grid {
                grid-template-columns: 1fr;
            }

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
                <li><a href="manage_unit.php" class="active">🏢 Unit Usaha</a></li>
                <li><a href="manage_reservasi.php">📅 Reservasi</a></li>
                <li><a href="manage_kontak.php">📞 Kontak</a></li>
                <li style="margin-top: 2rem;"><a href="logout.php">🚪 Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="page-header">
                <h1>Manajemen Unit Usaha</h1>
                <a href="index.php" class="btn btn-secondary">← Kembali</a>
            </div>

            <?php if ($flash): ?>
                <div class="flash-message <?php echo $flash['type']; ?>">
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>

            <div class="content-grid">
                <!-- Form -->
                <div class="form-card">
                    <h2><?php echo $edit_mode ? 'Edit Unit Usaha' : 'Tambah Unit Usaha'; ?></h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="<?php echo $edit_mode ? 'edit' : 'add'; ?>">
                        <?php if ($edit_mode): ?>
                            <input type="hidden" name="id" value="<?php echo $unit['id']; ?>">
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="nama">Nama Unit Usaha</label>
                            <input type="text" id="nama" name="nama" value="<?php echo $edit_mode ? htmlspecialchars($unit['nama']) : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea id="deskripsi" name="deskripsi" rows="4"><?php echo $edit_mode ? htmlspecialchars($unit['deskripsi']) : ''; ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="urutan">Urutan</label>
                            <input type="number" id="urutan" name="urutan" value="<?php echo $edit_mode ? $unit['urutan'] : '1'; ?>" min="1">
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="aktif" <?php echo ($edit_mode && $unit['status'] === 'aktif') ? 'selected' : ''; ?>>Aktif</option>
                                <option value="nonaktif" <?php echo ($edit_mode && $unit['status'] === 'nonaktif') ? 'selected' : ''; ?>>Nonaktif</option>
                            </select>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <?php if ($edit_mode): ?>
                                <a href="manage_unit.php" class="btn btn-secondary">Batal</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- List -->
                <div class="list-card">
                    <h2>Daftar Unit Usaha</h2>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama</th>
                                    <th>Status</th>
                                    <th>Urutan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                foreach ($all_units as $item) {
                                    $status_class = 'status-' . strtolower($item['status']);
                                    echo "<tr>";
                                    echo "<td>" . $no++ . "</td>";
                                    echo "<td>" . htmlspecialchars($item['nama']) . "</td>";
                                    echo "<td><span class='status-badge $status_class'>" . ucfirst($item['status']) . "</span></td>";
                                    echo "<td>" . $item['urutan'] . "</td>";
                                    echo "<td>";
                                    echo "<div class='action-buttons'>";
                                    echo "<a href='?action=edit&id=" . $item['id'] . "' class='btn btn-secondary btn-sm'>Edit</a>";
                                    echo "<form method='POST' style='display:inline;' onsubmit='return confirm(\"Yakin hapus?\");'>";
                                    echo "<input type='hidden' name='action' value='delete'>";
                                    echo "<input type='hidden' name='id' value='" . $item['id'] . "'>";
                                    echo "<button type='submit' class='btn btn-danger btn-sm'>Hapus</button>";
                                    echo "</form>";
                                    echo "</div>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

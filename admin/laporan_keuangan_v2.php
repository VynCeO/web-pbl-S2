<?php
/**
 * Laporan Keuangan v2.0
 * - Upload dan import dari Excel
 * - Kelola laporan keuangan
 */

require_once '../app/config/database.php';
require_once '../app/includes/functions_v2.php';

start_session();

if (!is_logged_in()) {
    redirect('/admin/login.php');
}

define('UPLOAD_DIR', '../assets/uploads/laporan/');

// Variables
$page_title = 'Laporan Keuangan v2';
$flash = get_flash();
$tab = isset($_GET['tab']) ? sanitize($_GET['tab']) : 'dashboard';

// Statistics for dashboard
$total_debit = 0;
$total_kredit = 0;
$total_saldo = 0;

$stat_query = "SELECT 
    SUM(COALESCE(debit, 0)) as total_debit,
    SUM(COALESCE(kredit, 0)) as total_kredit,
    SUM(COALESCE(saldo, 0)) as total_saldo
    FROM laporan_keuangan";
$stat_result = $conn->query($stat_query);
if ($stat_result) {
    $stat = $stat_result->fetch_assoc();
    $total_debit = $stat['total_debit'] ?? 0;
    $total_kredit = $stat['total_kredit'] ?? 0;
    $total_saldo = $stat['total_saldo'] ?? 0;
}

// Get all laporan keuangan data
$laporan_query = "SELECT l.*, u.nama as unit_nama, a.nama_lengkap 
                  FROM laporan_keuangan l
                  LEFT JOIN unit_usaha u ON l.unit_usaha_id = u.id
                  LEFT JOIN admin_user a ON l.created_by = a.id
                  ORDER BY l.tanggal DESC, l.id DESC
                  LIMIT 100";
$laporan_result = $conn->query($laporan_query);
$laporan_data = [];
if ($laporan_result && $laporan_result->num_rows > 0) {
    while ($row = $laporan_result->fetch_assoc()) {
        $laporan_data[] = $row;
    }
}

// Get upload history
$upload_query = "SELECT u.*, a.nama_lengkap 
                 FROM upload_laporan u
                 LEFT JOIN admin_user a ON u.uploaded_by = a.id
                 ORDER BY u.created_at DESC
                 LIMIT 20";
$upload_result = $conn->query($upload_query);
$upload_history = [];
if ($upload_result && $upload_result->num_rows > 0) {
    while ($row = $upload_result->fetch_assoc()) {
        $upload_history[] = $row;
    }
}

// Get kategori untuk filter
$kategori_query = "SELECT DISTINCT kategori FROM laporan_keuangan ORDER BY kategori";
$kategori_result = $conn->query($kategori_query);
$kategori_list = [];
if ($kategori_result && $kategori_result->num_rows > 0) {
    while ($row = $kategori_result->fetch_assoc()) {
        $kategori_list[] = $row['kategori'];
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = sanitize($_POST['action']);
    
    // Add laporan keuangan manually
    if ($action === 'add_laporan') {
        $tanggal = sanitize($_POST['tanggal'] ?? '');
        $kategori = sanitize($_POST['kategori'] ?? '');
        $deskripsi = sanitize($_POST['deskripsi'] ?? '');
        $debit = (float)($_POST['debit'] ?? 0);
        $kredit = (float)($_POST['kredit'] ?? 0);
        $unit_usaha_id = (int)($_POST['unit_usaha_id'] ?? 0);
        $keterangan = sanitize($_POST['keterangan'] ?? '');
        
        if (empty($tanggal) || empty($kategori) || ($debit <= 0 && $kredit <= 0)) {
            set_flash('error', 'Tanggal, kategori, dan minimal satu dari debit/kredit harus diisi');
        } else {
            // Calculate saldo (debit - kredit)
            $saldo = $debit - $kredit;
            
            $data = [
                'tanggal' => $tanggal,
                'kategori' => $kategori,
                'deskripsi' => $deskripsi,
                'debit' => $debit,
                'kredit' => $kredit,
                'saldo' => $saldo,
                'unit_usaha_id' => $unit_usaha_id > 0 ? $unit_usaha_id : null,
                'keterangan' => $keterangan,
                'created_by' => $_SESSION['admin_id']
            ];
            
            if (insert_data($conn, 'laporan_keuangan', $data)) {
                set_flash('success', 'Laporan keuangan berhasil ditambahkan');
            } else {
                set_flash('error', 'Gagal menambahkan laporan keuangan');
            }
            redirect('/admin/laporan_keuangan_v2.php');
        }
    }
    
    // Upload Excel file
    elseif ($action === 'upload_excel') {
        if (!empty($_FILES['excel_file']['name'])) {
            $file = $_FILES['excel_file'];
            $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            // Validasi tipe file
            if (!in_array($file_ext, ['xlsx', 'xls', 'csv'])) {
                set_flash('error', 'Format file hanya mendukung Excel (.xlsx, .xls) atau CSV (.csv)');
                redirect('/admin/laporan_keuangan_v2.php');
            }
            
            // Create upload directory
            if (!is_dir(UPLOAD_DIR)) {
                mkdir(UPLOAD_DIR, 0755, true);
            }
            
            $filename = time() . '_' . bin2hex(random_bytes(8)) . '.' . $file_ext;
            $filepath = UPLOAD_DIR . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                // Process file
                $imported_rows = 0;
                $errors = [];
                
                if ($file_ext === 'csv') {
                    // Process CSV
                    $handle = fopen($filepath, 'r');
                    $header = fgetcsv($handle); // Skip header
                    
                    while (($row = fgetcsv($handle)) !== false) {
                        // Expected format: Tanggal, Kategori, Deskripsi, Debit, Kredit, Unit, Keterangan
                        if (count($row) >= 4) {
                            $tanggal = trim($row[0]);
                            $kategori = trim($row[1]);
                            $deskripsi = trim($row[2]);
                            $debit = (float)str_replace(',', '.', $row[3]);
                            $kredit = (float)(isset($row[4]) ? str_replace(',', '.', $row[4]) : 0);
                            
                            if (!empty($tanggal) && !empty($kategori)) {
                                $saldo = $debit - $kredit;
                                
                                $data = [
                                    'tanggal' => $tanggal,
                                    'kategori' => $kategori,
                                    'deskripsi' => $deskripsi,
                                    'debit' => $debit,
                                    'kredit' => $kredit,
                                    'saldo' => $saldo,
                                    'unit_usaha_id' => null,
                                    'keterangan' => isset($row[6]) ? trim($row[6]) : '',
                                    'created_by' => $_SESSION['admin_id']
                                ];
                                
                                if (insert_data($conn, 'laporan_keuangan', $data)) {
                                    $imported_rows++;
                                }
                            }
                        }
                    }
                    fclose($handle);
                } else {
                    // For XLSX, we'll need a library - for now, show message
                    $imported_rows = 0;
                    $errors[] = 'Format XLSX memerlukan library tambahan. Gunakan CSV untuk sekarang.';
                }
                
                // Record upload history
                $upload_data = [
                    'nama_file' => $file['name'],
                    'file_path' => $filepath,
                    'jumlah_baris' => $imported_rows,
                    'status' => $imported_rows > 0 ? 'processed' : 'error',
                    'error_message' => !empty($errors) ? implode(', ', $errors) : null,
                    'uploaded_by' => $_SESSION['admin_id']
                ];
                insert_data($conn, 'upload_laporan', $upload_data);
                
                if ($imported_rows > 0) {
                    set_flash('success', "Berhasil import {$imported_rows} baris data dari file Excel");
                } else {
                    set_flash('error', 'Tidak ada data yang berhasil diimport');
                }
            } else {
                set_flash('error', 'Gagal mengupload file');
            }
            redirect('/admin/laporan_keuangan_v2.php');
        }
    }
    
    // Delete laporan
    elseif ($action === 'delete_laporan') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0 && delete_data($conn, 'laporan_keuangan', $id)) {
            set_flash('success', 'Laporan berhasil dihapus');
        } else {
            set_flash('error', 'Gagal menghapus laporan');
        }
        redirect('/admin/laporan_keuangan_v2.php');
    }
}

// Get all units for dropdown
$units = get_all_data($conn, 'unit_usaha', '', 'urutan ASC');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - BUMDes Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #2d5016;
            --secondary-color: #ff9500;
            --success-color: #4caf50;
            --danger-color: #f44336;
            --warning-color: #ff9800;
            --info-color: #2196F3;
            --light-bg: #f5f5f5;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
            color: #333;
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
            z-index: 1000;
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
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
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

        .tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid #ddd;
            background: white;
            padding: 1rem;
            border-radius: 8px 8px 0 0;
        }

        .tab-btn {
            background: none;
            border: none;
            padding: 0.75rem 1.5rem;
            cursor: pointer;
            font-weight: 600;
            color: #666;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .tab-btn.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .stats-grid {
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
        }

        .stat-card h3 {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .stat-card .value {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .card h2 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--secondary-color);
        }

        .form-group {
            margin-bottom: 1.5rem;
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
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 5px rgba(45, 80, 22, 0.2);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
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

        table th {
            padding: 0.75rem;
            text-align: left;
            font-weight: 600;
            color: var(--primary-color);
            border-bottom: 2px solid #ddd;
        }

        table td {
            padding: 0.75rem;
            border-bottom: 1px solid #ddd;
        }

        table tr:hover {
            background-color: #f9f9f9;
        }

        .action-btns {
            display: flex;
            gap: 0.5rem;
        }

        .btn-delete {
            background-color: var(--danger-color);
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .btn-delete:hover {
            background-color: #da190b;
        }

        .rupiah {
            color: var(--success-color);
            font-weight: 600;
        }

        .file-upload-area {
            border: 2px dashed var(--primary-color);
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            background-color: rgba(45, 80, 22, 0.05);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .file-upload-area:hover {
            background-color: rgba(45, 80, 22, 0.1);
            border-color: var(--secondary-color);
        }

        .file-upload-area input[type="file"] {
            display: none;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-processed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-error {
            background-color: #f8d7da;
            color: #721c24;
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

            .form-row {
                grid-template-columns: 1fr;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
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
                <li><a href="manage_reservasi.php">📅 Reservasi</a></li>
                <li><a href="manage_kontak.php">📞 Kontak</a></li>
                <li><a href="laporan_keuangan_v2.php" class="active">💰 Laporan Keuangan</a></li>
                <li style="margin-top: 2rem;"><a href="logout.php">🚪 Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Page Header -->
            <div class="page-header">
                <h1><?php echo $page_title; ?></h1>
                <a href="index.php" class="btn btn-secondary">← Kembali</a>
            </div>

            <!-- Flash Messages -->
            <?php if ($flash): ?>
                <div class="flash-message flash-<?php echo $flash['type']; ?>">
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>

            <!-- Tabs -->
            <div class="tabs">
                <button class="tab-btn<?php echo $tab === 'dashboard' ? ' active' : ''; ?>" onclick="switchTab('dashboard')">
                    📊 Dashboard
                </button>
                <button class="tab-btn<?php echo $tab === 'input' ? ' active' : ''; ?>" onclick="switchTab('input')">
                    ➕ Input Manual
                </button>
                <button class="tab-btn<?php echo $tab === 'upload' ? ' active' : ''; ?>" onclick="switchTab('upload')">
                    📤 Upload Excel
                </button>
                <button class="tab-btn<?php echo $tab === 'history' ? ' active' : ''; ?>" onclick="switchTab('history')">
                    📜 Riwayat Upload
                </button>
            </div>

            <!-- Dashboard Tab -->
            <div id="dashboard-tab" class="tab-content<?php echo $tab === 'dashboard' ? ' active' : ''; ?>">
                <!-- Statistics -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Total Debit</h3>
                        <div class="value rupiah">Rp <?php echo number_format($total_debit, 0, ',', '.'); ?></div>
                    </div>
                    <div class="stat-card">
                        <h3>Total Kredit</h3>
                        <div class="value rupiah">Rp <?php echo number_format($total_kredit, 0, ',', '.'); ?></div>
                    </div>
                    <div class="stat-card">
                        <h3>Total Saldo</h3>
                        <div class="value rupiah">Rp <?php echo number_format($total_saldo, 0, ',', '.'); ?></div>
                    </div>
                    <div class="stat-card">
                        <h3>Jumlah Transaksi</h3>
                        <div class="value"><?php echo count($laporan_data); ?></div>
                    </div>
                </div>

                <!-- Laporan Data Table -->
                <div class="card">
                    <h2>Data Laporan Keuangan</h2>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kategori</th>
                                    <th>Deskripsi</th>
                                    <th style="text-align: right;">Debit</th>
                                    <th style="text-align: right;">Kredit</th>
                                    <th style="text-align: right;">Saldo</th>
                                    <th>Unit Usaha</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($laporan_data)): ?>
                                    <?php foreach ($laporan_data as $lap): ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y', strtotime($lap['tanggal'])); ?></td>
                                            <td><?php echo htmlspecialchars($lap['kategori']); ?></td>
                                            <td><?php echo htmlspecialchars(substr($lap['deskripsi'], 0, 30)); ?></td>
                                            <td style="text-align: right;" class="rupiah">Rp <?php echo number_format($lap['debit'], 0, ',', '.'); ?></td>
                                            <td style="text-align: right;" class="rupiah">Rp <?php echo number_format($lap['kredit'], 0, ',', '.'); ?></td>
                                            <td style="text-align: right;" class="rupiah">Rp <?php echo number_format($lap['saldo'], 0, ',', '.'); ?></td>
                                            <td><?php echo $lap['unit_nama'] ? htmlspecialchars($lap['unit_nama']) : '-'; ?></td>
                                            <td>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="action" value="delete_laporan">
                                                    <input type="hidden" name="id" value="<?php echo $lap['id']; ?>">
                                                    <button type="submit" class="btn-delete btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" style="text-align: center; color: #999;">Belum ada data laporan keuangan</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Input Manual Tab -->
            <div id="input-tab" class="tab-content<?php echo $tab === 'input' ? ' active' : ''; ?>">
                <div class="card">
                    <h2>Input Laporan Keuangan Manual</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="add_laporan">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Tanggal *</label>
                                <input type="date" name="tanggal" required>
                            </div>
                            <div class="form-group">
                                <label>Kategori *</label>
                                <input type="text" name="kategori" placeholder="cth: Pendapatan, Biaya Operasional" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi" placeholder="Deskripsi transaksi..."></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Debit (Rp)</label>
                                <input type="number" name="debit" step="0.01" min="0" value="0">
                            </div>
                            <div class="form-group">
                                <label>Kredit (Rp)</label>
                                <input type="number" name="kredit" step="0.01" min="0" value="0">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Unit Usaha</label>
                                <select name="unit_usaha_id">
                                    <option value="0">-- Pilih Unit (Opsional) --</option>
                                    <?php foreach ($units as $unit): ?>
                                        <option value="<?php echo $unit['id']; ?>"><?php echo htmlspecialchars($unit['nama']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Keterangan</label>
                                <input type="text" name="keterangan" placeholder="Catatan tambahan...">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">💾 Simpan</button>
                    </form>
                </div>
            </div>

            <!-- Upload Excel Tab -->
            <div id="upload-tab" class="tab-content<?php echo $tab === 'upload' ? ' active' : ''; ?>">
                <div class="card">
                    <h2>Upload File Excel/CSV</h2>
                    <p style="color: #666; margin-bottom: 1.5rem;">
                        Format file: CSV dengan kolom: Tanggal, Kategori, Deskripsi, Debit, Kredit, (Unit), Keterangan
                    </p>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="upload_excel">
                        
                        <div class="file-upload-area" onclick="document.getElementById('excel_file').click()">
                            <input type="file" id="excel_file" name="excel_file" accept=".csv,.xlsx,.xls" required>
                            <p style="font-size: 1.1rem; margin-bottom: 0.5rem;">📁 Klik untuk memilih file</p>
                            <p style="color: #666; font-size: 0.9rem;">atau drag and drop file di sini</p>
                            <p style="color: #999; font-size: 0.85rem; margin-top: 0.5rem;">Mendukung: CSV, XLSX, XLS (Max 10MB)</p>
                        </div>

                        <div id="file-name" style="margin-top: 1rem; color: #666; display: none;">
                            File dipilih: <strong id="selected-file"></strong>
                        </div>

                        <button type="submit" class="btn btn-primary" style="margin-top: 1.5rem;">📤 Upload</button>
                    </form>

                    <div style="background-color: #f0f8ff; padding: 1.5rem; border-radius: 8px; margin-top: 2rem;">
                        <h3 style="color: var(--info-color); margin-bottom: 1rem;">📋 Contoh Format CSV</h3>
                        <pre style="background: white; padding: 1rem; border-radius: 4px; overflow-x: auto; font-size: 0.85rem;">Tanggal,Kategori,Deskripsi,Debit,Kredit,Unit,Keterangan
2024-01-01,Pendapatan,Sewa GOR,500000,0,GOR Sugihwaras,Booking ID: 123
2024-01-02,Biaya Operasional,Gaji Karyawan,0,200000,,Gaji Januari
2024-01-03,Pendapatan,Air Minum,300000,0,Air Minum Kemasan,Penjualan Botol</pre>
                    </div>
                </div>
            </div>

            <!-- Upload History Tab -->
            <div id="history-tab" class="tab-content<?php echo $tab === 'history' ? ' active' : ''; ?>">
                <div class="card">
                    <h2>Riwayat Upload</h2>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nama File</th>
                                    <th>Tanggal Upload</th>
                                    <th>Jumlah Baris</th>
                                    <th>Status</th>
                                    <th>Pesan Error</th>
                                    <th>Upload oleh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($upload_history)): ?>
                                    <?php foreach ($upload_history as $hist): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($hist['nama_file']); ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($hist['created_at'])); ?></td>
                                            <td><?php echo $hist['jumlah_baris']; ?></td>
                                            <td><span class="status-badge status-<?php echo $hist['status']; ?>"><?php echo ucfirst($hist['status']); ?></span></td>
                                            <td><?php echo $hist['error_message'] ? htmlspecialchars($hist['error_message']) : '-'; ?></td>
                                            <td><?php echo $hist['nama_lengkap'] ? htmlspecialchars($hist['nama_lengkap']) : 'Unknown'; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; color: #999;">Belum ada riwayat upload</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });

            // Show selected tab
            document.getElementById(tabName + '-tab').classList.add('active');
            event.target.classList.add('active');

            // Update URL
            window.history.pushState(null, '', '?tab=' + tabName);
        }

        // Handle file input
        document.getElementById('excel_file')?.addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            if (fileName) {
                document.getElementById('selected-file').textContent = fileName;
                document.getElementById('file-name').style.display = 'block';
            }
        });

        // Drag and drop
        const fileUploadArea = document.querySelector('.file-upload-area');
        if (fileUploadArea) {
            fileUploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                fileUploadArea.style.backgroundColor = 'rgba(45, 80, 22, 0.15)';
            });

            fileUploadArea.addEventListener('dragleave', () => {
                fileUploadArea.style.backgroundColor = 'rgba(45, 80, 22, 0.05)';
            });

            fileUploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                fileUploadArea.style.backgroundColor = 'rgba(45, 80, 22, 0.05)';
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    document.getElementById('excel_file').files = files;
                    const event = new Event('change', { bubbles: true });
                    document.getElementById('excel_file').dispatchEvent(event);
                }
            });
        }
    </script>
</body>
</html>

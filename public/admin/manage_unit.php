<?php
/**
 * Manajemen Unit Usaha v2.0
 * - Support upload foto
 * - Fitur variasi produk dengan harga
 * - Code lebih terstruktur dan efisien
 */

require_once '../app/config/database.php';
require_once '../app/includes/functions_v2.php';

start_session();

if (!is_logged_in()) {
    redirect('/admin/login.php');
}

// Define page info
$page_title = 'Manajemen Unit Usaha';
$page_header_title = 'Manajemen Unit Usaha';
$page_header_action = '<a href="index.php" class="btn btn-secondary">← Kembali</a>';

// Constants
define('UPLOAD_DIR', '../../assets/images/');

// Handle actions
$action = isset($_GET['action']) ? sanitize($_GET['action']) : '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$tab = isset($_GET['tab']) ? sanitize($_GET['tab']) : 'unit'; // unit atau variasi

$flash = get_flash();
$unit = null;
$edit_mode = false;

// ============================================
// HANDLE FORM SUBMISSIONS
// ============================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $post_action = sanitize($_POST['action']);
    
    // ===== UNIT USAHA OPERATIONS =====
    if ($post_action === 'add_unit' || $post_action === 'edit_unit') {
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
            
            // Handle photo upload
            if (!empty($_FILES['foto']['name'])) {
                $old_foto = '';
                if ($post_action === 'edit_unit') {
                    $edit_id = (int)($_POST['id'] ?? 0);
                    $unit_data = get_data_by_id($conn, 'unit_usaha', $edit_id);
                    $old_foto = $unit_data['foto'] ?? '';
                }
                
                $foto = replace_file($old_foto, $_FILES['foto'], ['jpg', 'jpeg', 'png', 'gif'], UPLOAD_DIR);
                if ($foto) {
                    $data['foto'] = $foto;
                }
            }
            
            if ($post_action === 'add_unit') {
                $unit_id = insert_data($conn, 'unit_usaha', $data);
                if ($unit_id) {
                    set_flash('success', 'Unit usaha berhasil ditambahkan');
                    redirect('/admin/manage_unit.php');
                } else {
                    set_flash('error', 'Gagal menambahkan unit usaha');
                }
            } else {
                $edit_id = (int)($_POST['id'] ?? 0);
                if ($edit_id > 0 && update_data($conn, 'unit_usaha', $data, $edit_id)) {
                    set_flash('success', 'Unit usaha berhasil diperbarui');
                    redirect('/admin/manage_unit.php');
                } else {
                    set_flash('error', 'Gagal memperbarui unit usaha');
                }
            }
        }
    }
    
    // ===== DELETE UNIT USAHA =====
    elseif ($post_action === 'delete_unit') {
        $delete_id = (int)($_POST['id'] ?? 0);
        if ($delete_id > 0) {
            // Delete related files
            $unit_data = get_data_by_id($conn, 'unit_usaha', $delete_id);
            if ($unit_data && !empty($unit_data['foto'])) {
                delete_file($unit_data['foto'], UPLOAD_DIR);
            }
            
            // Delete variasi produk terkait (cascade)
            $conn->query("DELETE FROM variasi_produk WHERE unit_usaha_id = {$delete_id}");
            
            // Delete unit
            if (delete_data($conn, 'unit_usaha', $delete_id)) {
                set_flash('success', 'Unit usaha berhasil dihapus');
            } else {
                set_flash('error', 'Gagal menghapus unit usaha');
            }
            redirect('/admin/manage_unit.php');
        }
    }
    
    // ===== VARIASI PRODUK OPERATIONS =====
    elseif ($post_action === 'add_variasi' || $post_action === 'edit_variasi') {
        $unit_usaha_id = (int)($_POST['unit_usaha_id'] ?? 0);
        $nama_variasi = sanitize($_POST['nama_variasi'] ?? '');
        $harga = sanitize($_POST['harga'] ?? '');
        $keterangan = sanitize($_POST['keterangan'] ?? '');
        $urutan_variasi = (int)($_POST['urutan_variasi'] ?? 1);
        
        if (empty($nama_variasi) || !validate_currency($harga)) {
            set_flash('error', 'Nama dan harga variasi harus diisi dengan benar');
        } elseif ($unit_usaha_id <= 0) {
            set_flash('error', 'Unit usaha tidak valid');
        } else {
            $variasi_data = [
                'unit_usaha_id' => $unit_usaha_id,
                'nama' => $nama_variasi,
                'harga' => $harga,
                'keterangan' => $keterangan,
                'urutan' => $urutan_variasi,
                'status' => 'aktif'
            ];
            
            if ($post_action === 'add_variasi') {
                if (insert_data($conn, 'variasi_produk', $variasi_data)) {
                    set_flash('success', 'Variasi produk berhasil ditambahkan');
                } else {
                    set_flash('error', 'Gagal menambahkan variasi produk');
                }
            } else {
                $variasi_id = (int)($_POST['variasi_id'] ?? 0);
                if ($variasi_id > 0 && update_data($conn, 'variasi_produk', $variasi_data, $variasi_id)) {
                    set_flash('success', 'Variasi produk berhasil diperbarui');
                } else {
                    set_flash('error', 'Gagal memperbarui variasi produk');
                }
            }
            redirect("/admin/manage_unit.php?id={$unit_usaha_id}&tab=variasi");
        }
    }
    
    // ===== DELETE VARIASI =====
    elseif ($post_action === 'delete_variasi') {
        $variasi_id = (int)($_POST['variasi_id'] ?? 0);
        $unit_id = (int)($_POST['unit_id'] ?? 0);
        
        if ($variasi_id > 0 && delete_data($conn, 'variasi_produk', $variasi_id)) {
            set_flash('success', 'Variasi produk berhasil dihapus');
        } else {
            set_flash('error', 'Gagal menghapus variasi produk');
        }
        
        if ($unit_id > 0) {
            redirect("/admin/manage_unit.php?id={$unit_id}&tab=variasi");
        } else {
            redirect('/admin/manage_unit.php');
        }
    }
}

// ============================================
// LOAD DATA
// ============================================

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
            --light-bg: #f5f5f5;
            --danger-color: #f44336;
            --success-color: #4caf50;
            --warning-color: #ff9800;
            --info-color: #2196F3;
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
            font-size: 1rem;
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

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: #da190b;
        }

        .btn-success {
            background-color: var(--success-color);
            color: white;
        }

        .btn-success:hover {
            background-color: #45a049;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        .flash-message {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            border-left: 4px solid;
        }

        .flash-message.success {
            background-color: #d4edda;
            color: #155724;
            border-left-color: var(--success-color);
        }

        .flash-message.error {
            background-color: #f8d7da;
            color: #721c24;
            border-left-color: var(--danger-color);
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 2rem;
        }

        .form-card,
        .list-card,
        .detail-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .form-card h2,
        .list-card h2,
        .detail-card h2 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
            border-bottom: 2px solid var(--light-bg);
            padding-bottom: 1rem;
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

        .form-group small {
            display: block;
            margin-top: 0.25rem;
            color: #666;
            font-size: 0.9rem;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .file-preview {
            margin-top: 1rem;
        }

        .file-preview img {
            max-width: 150px;
            max-height: 150px;
            border-radius: 4px;
            border: 2px solid #ddd;
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

        table th,
        table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            font-weight: 600;
            color: var(--primary-color);
        }

        table tbody tr:hover {
            background-color: rgba(45, 80, 22, 0.05);
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

        .status-aktif {
            background-color: #d4edda;
            color: #155724;
        }

        .status-nonaktif {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* ===== TABS ===== */
        .tabs {
            display: flex;
            gap: 0;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid #ddd;
        }

        .tab-btn {
            background: none;
            border: none;
            padding: 1rem;
            cursor: pointer;
            font-weight: 600;
            color: #666;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .tab-btn:hover {
            color: var(--primary-color);
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

        /* ===== VARIASI PRODUCTS DISPLAY ===== */
        .variasi-item {
            background: #f9f9f9;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            border-left: 4px solid var(--secondary-color);
        }

        .variasi-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .variasi-item-name {
            font-weight: 600;
            color: var(--primary-color);
            font-size: 1.1rem;
        }

        .variasi-item-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--secondary-color);
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

            .page-header {
                flex-direction: column;
                gap: 1rem;
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
                <li><a href="manage_unit.php" class="active">🏢 Unit Usaha</a></li>
                <li><a href="manage_reservasi.php">📅 Reservasi</a></li>
                <li><a href="manage_kontak.php">📞 Kontak</a></li>
                <li style="margin-top: 2rem;"><a href="logout.php">🚪 Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="page-header">
                <h1><?php echo $page_title; ?></h1>
                <a href="index.php" class="btn btn-secondary">← Kembali</a>
            </div>

            <?php if ($flash): ?>
                <div class="flash-message <?php echo htmlspecialchars($flash['type']); ?>">
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>

            <?php if ($edit_mode && $unit): ?>
                <!-- EDIT MODE - Show Unit Detail with Tabs -->
                <div class="detail-card">
                    <h2>📝 Edit Unit Usaha: <?php echo htmlspecialchars($unit['nama']); ?></h2>
                    <a href="manage_unit.php" class="btn btn-secondary" style="margin-bottom: 1rem;">← Kembali ke Daftar</a>

                    <!-- TABS -->
                    <div class="tabs">
                        <button class="tab-btn active" onclick="switchTab('info')">ℹ️ Informasi Unit</button>
                        <button class="tab-btn" onclick="switchTab('variasi')">🏷️ Variasi Produk & Harga</button>
                    </div>

                    <!-- TAB 1: INFO UNIT -->
                    <div id="info" class="tab-content active">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="edit_unit">
                            <input type="hidden" name="id" value="<?php echo $unit['id']; ?>">

                            <div class="form-group">
                                <label for="nama">Nama Unit Usaha</label>
                                <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($unit['nama']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="deskripsi">Deskripsi</label>
                                <textarea id="deskripsi" name="deskripsi" rows="4"><?php echo htmlspecialchars($unit['deskripsi']); ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="foto">Foto Unit</label>
                                <input type="file" id="foto" name="foto" accept="image/*" onchange="previewImage(this)">
                                <small>Format: JPG, JPEG, PNG, GIF. Ukuran maksimal: 5MB</small>
                                <?php if (!empty($unit['foto']) && file_exists(UPLOAD_DIR . $unit['foto'])): ?>
                                    <div class="file-preview">
                                        <p style="margin-bottom: 0.5rem;"><strong>Foto saat ini:</strong></p>
                                        <img src="<?php echo BASE_URL; ?>/assets/images/<?php echo htmlspecialchars($unit['foto']); ?>" alt="<?php echo htmlspecialchars($unit['nama']); ?>">
                                    </div>
                                <?php endif; ?>
                                <div id="preview-container"></div>
                            </div>

                            <div class="form-group">
                                <label for="urutan">Urutan</label>
                                <input type="number" id="urutan" name="urutan" value="<?php echo $unit['urutan']; ?>" min="1">
                            </div>

                            <div class="form-group">
                                <label for="status">Status</label>
                                <select id="status" name="status">
                                    <option value="aktif" <?php echo ($unit['status'] === 'aktif') ? 'selected' : ''; ?>>Aktif</option>
                                    <option value="nonaktif" <?php echo ($unit['status'] === 'nonaktif') ? 'selected' : ''; ?>>Nonaktif</option>
                                </select>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
                                <a href="manage_unit.php" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>

                    <!-- TAB 2: VARIASI PRODUK -->
                    <div id="variasi" class="tab-content">
                        <h3 style="margin-bottom: 1.5rem; color: var(--primary-color);">Tambah Variasi Produk Baru</h3>
                        
                        <form method="POST" style="background: #f9f9f9; padding: 1.5rem; border-radius: 4px; margin-bottom: 2rem;">
                            <input type="hidden" name="action" value="add_variasi">
                            <input type="hidden" name="unit_usaha_id" value="<?php echo $unit['id']; ?>">

                            <div class="form-group">
                                <label for="nama_variasi">Nama Produk/Variasi <span style="color: red;">*</span></label>
                                <input type="text" id="nama_variasi" name="nama_variasi" placeholder="Contoh: 1 Dus, 2 Dus, Galon, dll" required>
                                <small>Contoh: Air Minum 1 Dus, Kopi Premium 1kg, dll</small>
                            </div>

                            <div class="form-group">
                                <label for="harga">Harga (Rp) <span style="color: red;">*</span></label>
                                <input type="number" id="harga" name="harga" placeholder="45000" min="0" step="1000" required>
                            </div>

                            <div class="form-group">
                                <label for="keterangan">Keterangan</label>
                                <textarea id="keterangan" name="keterangan" rows="2" placeholder="Deskripsi tambahan produk ini (opsional)"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="urutan_variasi">Urutan</label>
                                <input type="number" id="urutan_variasi" name="urutan_variasi" value="1" min="1">
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-success">➕ Tambah Variasi</button>
                            </div>
                        </form>

                        <h3 style="margin-bottom: 1.5rem; color: var(--primary-color);">Daftar Variasi Produk</h3>
                        
                        <?php
                        $variasi_list = get_all_data($conn, 'variasi_produk', "unit_usaha_id = {$unit['id']}", 'urutan ASC');
                        
                        if (!empty($variasi_list)):
                            foreach ($variasi_list as $variasi):
                        ?>
                            <div class="variasi-item">
                                <div class="variasi-item-header">
                                    <div>
                                        <div class="variasi-item-name"><?php echo htmlspecialchars($variasi['nama']); ?></div>
                                        <?php if (!empty($variasi['keterangan'])): ?>
                                            <small style="color: #666;"><?php echo htmlspecialchars($variasi['keterangan']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <div style="text-align: right;">
                                        <div class="variasi-item-price"><?php echo format_rupiah($variasi['harga']); ?></div>
                                        <div style="margin-top: 0.5rem;">
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="delete_variasi">
                                                <input type="hidden" name="variasi_id" value="<?php echo $variasi['id']; ?>">
                                                <input type="hidden" name="unit_id" value="<?php echo $unit['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?');">🗑️ Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                            endforeach;
                        else:
                        ?>
                            <p style="color: #999; text-align: center; padding: 2rem;">Belum ada variasi produk. Tambahkan variasi baru di atas.</p>
                        <?php
                        endif;
                        ?>
                    </div>
                </div>

            <?php else: ?>
                <!-- LIST MODE -->
                <div class="content-grid">
                    <!-- Form Add -->
                    <div class="form-card">
                        <h2>➕ Tambah Unit Usaha Baru</h2>
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="add_unit">

                            <div class="form-group">
                                <label for="nama">Nama Unit Usaha</label>
                                <input type="text" id="nama" name="nama" required>
                            </div>

                            <div class="form-group">
                                <label for="deskripsi">Deskripsi</label>
                                <textarea id="deskripsi" name="deskripsi" rows="4"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="foto">Foto Unit</label>
                                <input type="file" id="foto" name="foto" accept="image/*" onchange="previewImage(this)">
                                <small>Format: JPG, JPEG, PNG, GIF. Ukuran maksimal: 5MB</small>
                                <div id="preview-container"></div>
                            </div>

                            <div class="form-group">
                                <label for="urutan">Urutan</label>
                                <input type="number" id="urutan" name="urutan" value="1" min="1">
                            </div>

                            <div class="form-group">
                                <label for="status">Status</label>
                                <select id="status" name="status">
                                    <option value="aktif" selected>Aktif</option>
                                    <option value="nonaktif">Nonaktif</option>
                                </select>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">💾 Simpan</button>
                            </div>
                        </form>
                    </div>

                    <!-- List -->
                    <div class="list-card">
                        <h2>📋 Daftar Unit Usaha</h2>
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
                                        echo "<a href='?action=edit&id=" . $item['id'] . "' class='btn btn-secondary btn-sm'>✏️ Edit</a>";
                                        echo "<form method='POST' style='display:inline;' onsubmit='return confirm(\"Yakin hapus?\");'>";
                                        echo "<input type='hidden' name='action' value='delete_unit'>";
                                        echo "<input type='hidden' name='id' value='" . $item['id'] . "'>";
                                        echo "<button type='submit' class='btn btn-danger btn-sm'>🗑️ Hapus</button>";
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
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Tab switching
        function switchTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
            
            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }

        // Image preview
        function previewImage(input) {
            const container = input.parentElement.querySelector('#preview-container') || document.getElementById('preview-container');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    container.innerHTML = '<div style="margin-top: 1rem;"><strong>Preview:</strong><br><img src="' + e.target.result + '" style="max-width: 150px; max-height: 150px; border-radius: 4px; border: 2px solid #ddd; margin-top: 0.5rem;"></div>';
                };
                
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>

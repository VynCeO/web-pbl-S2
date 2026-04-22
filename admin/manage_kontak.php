<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

start_session();

if (!is_logged_in()) {
    redirect('/admin/login.php');
}

$flash = get_flash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alamat = sanitize($_POST['alamat'] ?? '');
    $telepon = sanitize($_POST['telepon'] ?? '');
    $whatsapp = sanitize($_POST['whatsapp'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $facebook = sanitize($_POST['facebook'] ?? '');
    $instagram = sanitize($_POST['instagram'] ?? '');
    $instagram_url = sanitize($_POST['instagram_url'] ?? '');

    if (empty($alamat) || empty($telepon)) {
        set_flash('error', 'Alamat dan telepon harus diisi');
    } else {
        $data = [
            'alamat' => $alamat,
            'telepon' => $telepon,
            'whatsapp' => $whatsapp,
            'email' => $email,
            'facebook' => $facebook,
            'instagram' => $instagram,
            'instagram_url' => $instagram_url
        ];

        // Update or insert
        $result = $conn->query("SELECT id FROM kontak LIMIT 1");
        if ($result->num_rows > 0) {
            $kontak = $result->fetch_assoc();
            update_data($conn, 'kontak', $data, $kontak['id']);
        } else {
            insert_data($conn, 'kontak', $data);
        }

        set_flash('success', 'Data kontak berhasil disimpan');
        redirect('/admin/manage_kontak.php');
    }
}

// Get current kontak
$query = "SELECT * FROM kontak LIMIT 1";
$result = $conn->query($query);
$kontak = $result->num_rows > 0 ? $result->fetch_assoc() : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kontak - BUMDes Admin</title>
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

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
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

        .form-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
        }

        .form-card h2 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
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
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
            font-size: 1rem;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(45, 80, 22, 0.1);
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
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
                <li><a href="manage_kontak.php" class="active">📞 Kontak</a></li>
                <li style="margin-top: 2rem;"><a href="logout.php">🚪 Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="page-header">
                <h1>Manajemen Kontak</h1>
                <a href="index.php" class="btn btn-secondary">← Kembali</a>
            </div>

            <?php if ($flash): ?>
                <div class="flash-message <?php echo $flash['type']; ?>">
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>

            <div class="form-card">
                <h2>Edit Data Kontak</h2>
                <form method="POST">
                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <textarea id="alamat" name="alamat" rows="3" required><?php echo $kontak ? htmlspecialchars($kontak['alamat']) : ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="telepon">Telepon</label>
                        <input type="tel" id="telepon" name="telepon" value="<?php echo $kontak ? htmlspecialchars($kontak['telepon']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="whatsapp">WhatsApp</label>
                        <input type="tel" id="whatsapp" name="whatsapp" value="<?php echo $kontak ? htmlspecialchars($kontak['whatsapp']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo $kontak ? htmlspecialchars($kontak['email']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="facebook">Facebook URL</label>
                        <input type="url" id="facebook" name="facebook" value="<?php echo $kontak ? htmlspecialchars($kontak['facebook']) : ''; ?>" placeholder="https://facebook.com/...">
                    </div>

                    <div class="form-group">
                        <label for="instagram">Instagram</label>
                        <input type="text" id="instagram" name="instagram" value="<?php echo $kontak ? htmlspecialchars($kontak['instagram']) : ''; ?>" placeholder="@username">
                    </div>

                    <div class="form-group">
                        <label for="instagram_url">Instagram URL</label>
                        <input type="url" id="instagram_url" name="instagram_url" value="<?php echo $kontak ? htmlspecialchars($kontak['instagram_url']) : ''; ?>" placeholder="https://instagram.com/...">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="index.php" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

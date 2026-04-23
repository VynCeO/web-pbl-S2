<?php
/**
 * Admin Layout Template
 * Menghilangkan code duplication di semua halaman admin
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - BUMDes Admin' : 'BUMDes Admin'; ?></title>
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

        /* ===== SIDEBAR ===== */
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

        /* ===== MAIN CONTENT ===== */
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

        /* ===== BUTTONS ===== */
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

        /* ===== ALERTS/FLASH MESSAGES ===== */
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

        .flash-message.info {
            background-color: #d1ecf1;
            color: #0c5460;
            border-left-color: var(--info-color);
        }

        /* ===== FORMS & CARDS ===== */
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

        .form-card h2,
        .list-card h2 {
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

        /* ===== FILE UPLOAD PREVIEW ===== */
        .file-input-group {
            position: relative;
        }

        .file-input-wrapper {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
        }

        .file-input-wrapper input[type="file"] {
            flex: 1;
        }

        .file-preview {
            width: 100px;
            height: 100px;
            border-radius: 4px;
            object-fit: cover;
            border: 2px solid #ddd;
        }

        .file-preview-container {
            margin-top: 1rem;
        }

        /* ===== TABLES ===== */
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

        /* ===== RESPONSIVE ===== */
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
                padding: 1.5rem 1rem;
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            .page-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .page-header h1 {
                width: 100%;
            }
        }

        @media (max-width: 600px) {
            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                text-align: center;
            }

            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="index.php" <?php echo (basename($_SERVER['PHP_SELF']) === 'index.php') ? 'class="active"' : ''; ?>>📊 Dashboard</a></li>
                <li><a href="manage_pimpinan.php" <?php echo (basename($_SERVER['PHP_SELF']) === 'manage_pimpinan.php') ? 'class="active"' : ''; ?>>👥 Pimpinan</a></li>
                <li><a href="manage_unit.php" <?php echo (basename($_SERVER['PHP_SELF']) === 'manage_unit.php') ? 'class="active"' : ''; ?>>🏢 Unit Usaha</a></li>
                <li><a href="manage_reservasi.php" <?php echo (basename($_SERVER['PHP_SELF']) === 'manage_reservasi.php') ? 'class="active"' : ''; ?>>📅 Reservasi</a></li>
                <li><a href="manage_kontak.php" <?php echo (basename($_SERVER['PHP_SELF']) === 'manage_kontak.php') ? 'class="active"' : ''; ?>>📞 Kontak</a></li>
                <li style="margin-top: 2rem;"><a href="logout.php">🚪 Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <?php if (isset($page_header_title)): ?>
                <div class="page-header">
                    <h1><?php echo htmlspecialchars($page_header_title); ?></h1>
                    <?php if (isset($page_header_action)): ?>
                        <?php echo $page_header_action; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($flash = get_flash()): ?>
                <div class="flash-message <?php echo htmlspecialchars($flash['type']); ?>">
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>

            <!-- Page content will be inserted here -->
            <?php include $content_file; ?>
        </div>
    </div>
</body>
</html>

<?php
/**
 * BUMDes Helper Functions v2.0
 * Refactored and optimized for better performance
 */

// ============================================
// SANITIZATION & VALIDATION
// ============================================

/**
 * Sanitize user input
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

/**
 * Validate email
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validate phone (Indonesia format)
 */
function validate_phone($phone) {
    return preg_match('/^(\+62|0)[0-9]{9,12}$/', str_replace('-', '', $phone));
}

/**
 * Validate currency/number
 */
function validate_currency($value) {
    return is_numeric($value) && $value >= 0;
}

// ============================================
// FORMATTING
// ============================================

/**
 * Format to Indonesian Rupiah
 */
function format_rupiah($value) {
    return 'Rp ' . number_format((int)$value, 0, ',', '.');
}

/**
 * Format Indonesian date
 */
function format_tanggal($date) {
    $bulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    
    $split = explode('-', $date);
    if (count($split) !== 3) return $date;
    
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}

// ============================================
// DATABASE OPERATIONS (Optimized)
// ============================================

/**
 * Get all data with optimized query
 */
function get_all_data($conn, $table, $where = '', $order = '', $limit = '') {
    $query = "SELECT * FROM {$table}";
    
    if (!empty($where)) {
        $query .= " WHERE {$where}";
    }
    
    if (!empty($order)) {
        $query .= " ORDER BY {$order}";
    } else {
        $query .= " ORDER BY urutan ASC, id ASC";
    }
    
    if (!empty($limit)) {
        $query .= " LIMIT {$limit}";
    }
    
    $result = $conn->query($query);
    
    if (!$result) {
        error_log("Database error: " . $conn->error);
        return [];
    }
    
    if ($result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    return [];
}

/**
 * Get single data by ID
 */
function get_data_by_id($conn, $table, $id) {
    $id = (int)$id;
    $query = "SELECT * FROM {$table} WHERE id = {$id} LIMIT 1";
    $result = $conn->query($query);
    
    if (!$result) {
        error_log("Database error: " . $conn->error);
        return null;
    }
    
    return $result->num_rows > 0 ? $result->fetch_assoc() : null;
}

/**
 * Get count of records
 */
function get_count($conn, $table, $where = '') {
    $query = "SELECT COUNT(*) as total FROM {$table}";
    
    if (!empty($where)) {
        $query .= " WHERE {$where}";
    }
    
    $result = $conn->query($query);
    
    if (!$result) {
        error_log("Database error: " . $conn->error);
        return 0;
    }
    
    $row = $result->fetch_assoc();
    return (int)$row['total'];
}

/**
 * Insert data (returns ID on success)
 */
function insert_data($conn, $table, $data) {
    if (empty($data)) return false;
    
    $columns = array_keys($data);
    $values = array_values($data);
    
    $columns_str = '`' . implode('`, `', $columns) . '`';
    $placeholders = implode(', ', array_fill(0, count($values), '?'));
    
    $query = "INSERT INTO {$table} ({$columns_str}) VALUES ({$placeholders})";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        error_log("Prepare error: " . $conn->error);
        return false;
    }
    
    // Create type string
    $types = '';
    foreach ($values as $value) {
        if (is_int($value)) {
            $types .= 'i';
        } elseif (is_float($value)) {
            $types .= 'd';
        } else {
            $types .= 's';
        }
    }
    
    $stmt->bind_param($types, ...$values);
    
    if (!$stmt->execute()) {
        error_log("Execute error: " . $stmt->error);
        return false;
    }
    
    return $conn->insert_id;
}

/**
 * Update data (returns success boolean)
 */
function update_data($conn, $table, $data, $id) {
    if (empty($data)) return false;
    
    $id = (int)$id;
    
    $set_parts = [];
    $values = [];
    $types = '';
    
    foreach ($data as $key => $value) {
        $set_parts[] = "`{$key}` = ?";
        $values[] = $value;
        
        if (is_int($value)) {
            $types .= 'i';
        } elseif (is_float($value)) {
            $types .= 'd';
        } else {
            $types .= 's';
        }
    }
    
    $values[] = $id;
    $types .= 'i';
    
    $set_str = implode(', ', $set_parts);
    $query = "UPDATE {$table} SET {$set_str} WHERE id = ?";
    
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        error_log("Prepare error: " . $conn->error);
        return false;
    }
    
    $stmt->bind_param($types, ...$values);
    
    if (!$stmt->execute()) {
        error_log("Execute error: " . $stmt->error);
        return false;
    }
    
    return true;
}

/**
 * Delete data
 */
function delete_data($conn, $table, $id) {
    $id = (int)$id;
    $query = "DELETE FROM {$table} WHERE id = {$id} LIMIT 1";
    
    if (!$conn->query($query)) {
        error_log("Database error: " . $conn->error);
        return false;
    }
    
    return $conn->affected_rows > 0;
}

// ============================================
// FILE UPLOAD & MANAGEMENT
// ============================================

/**
 * Upload file dengan validasi ketat
 * @param array $file - $_FILES item
 * @param array $allowed_types - ext yang diperbolehkan
 * @param string $upload_dir - direktori tujuan (relative)
 * @param int $max_size - ukuran max dalam bytes (default 5MB)
 * @return string|false - filename jika berhasil, false jika gagal
 */
function upload_file($file, $allowed_types = ['jpg', 'jpeg', 'png', 'gif'], $upload_dir = '../assets/images/', $max_size = 5242880) {
    // Validasi file ada
    if (!isset($file['tmp_name']) || empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return false;
    }
    
    // Validasi ukuran
    if ($file['size'] > $max_size) {
        return false;
    }
    
    // Validasi ekstensi
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_ext, $allowed_types)) {
        return false;
    }
    
    // Create directory if not exists
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Generate unique filename
    $new_filename = time() . '_' . bin2hex(random_bytes(8)) . '.' . $file_ext;
    $upload_path = $upload_dir . $new_filename;
    
    // Move file
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        return $new_filename;
    }
    
    return false;
}

/**
 * Delete file dari server
 */
function delete_file($filename, $upload_dir = '../assets/images/') {
    $filepath = $upload_dir . $filename;
    
    if (file_exists($filepath) && is_file($filepath)) {
        return unlink($filepath);
    }
    
    return false;
}

/**
 * Replace file (delete old, upload new)
 */
function replace_file($old_filename, $new_file, $allowed_types = ['jpg', 'jpeg', 'png', 'gif'], $upload_dir = '../assets/images/') {
    // Delete old file if exists
    if (!empty($old_filename)) {
        delete_file($old_filename, $upload_dir);
    }
    
    // Upload new file
    if (!empty($new_file['name'])) {
        return upload_file($new_file, $allowed_types, $upload_dir);
    }
    
    return false;
}

// ============================================
// USER AUTHENTICATION
// ============================================

/**
 * Start session
 */
function start_session() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Check if user logged in
 */
function is_logged_in() {
    start_session();
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Check if username exists
 */
function username_exists($conn, $username, $exclude_id = null) {
    $username = $conn->real_escape_string($username);
    $query = "SELECT id FROM admin_user WHERE username = '{$username}'";
    
    if ($exclude_id) {
        $query .= " AND id != " . (int)$exclude_id;
    }
    
    $result = $conn->query($query);
    return $result->num_rows > 0;
}

/**
 * Check if email exists
 */
function email_exists($conn, $email, $exclude_id = null) {
    $email = $conn->real_escape_string($email);
    $query = "SELECT id FROM admin_user WHERE email = '{$email}'";
    
    if ($exclude_id) {
        $query .= " AND id != " . (int)$exclude_id;
    }
    
    $result = $conn->query($query);
    return $result->num_rows > 0;
}

// ============================================
// FLASH MESSAGES
// ============================================

/**
 * Set flash message
 */
function set_flash($type, $message) {
    start_session();
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get flash message
 */
function get_flash() {
    start_session();
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// ============================================
// REDIRECTS & RESPONSES
// ============================================

/**
 * Redirect to URL
 */
function redirect($url) {
    // Absolute redirects
    if (strpos($url, 'http') === 0 || strpos($url, '/') === 0) {
        header("Location: " . $url);
    } else {
        // Relative redirects
        header("Location: " . ltrim($url, './'));
    }
    exit;
}

/**
 * JSON response
 */
function json_response($success, $message, $data = null) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

?>

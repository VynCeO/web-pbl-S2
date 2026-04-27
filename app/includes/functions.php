<?php
// Fungsi-fungsi helper untuk BUMDes

// Sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Validasi email
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Validasi nomor telepon (Indonesia)
function validate_phone($phone) {
    return preg_match('/^(\+62|0)[0-9]{9,12}$/', str_replace('-', '', $phone));
}

// Format harga Rupiah
function format_rupiah($value) {
    return 'Rp ' . number_format($value, 0, ',', '.');
}

// Format tanggal Indonesia
function format_tanggal($date) {
    $bulan = array(
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    );
    
    $split = explode('-', $date);
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}

// Get all data dari tabel
function get_all_data($conn, $table, $where = '', $order = '', $limit = '') {
    $query = "SELECT * FROM $table";
    
    if (!empty($where)) {
        $query .= " WHERE $where";
    }
    
    if (!empty($order)) {
        $query .= " ORDER BY $order";
    } else {
        $query .= " ORDER BY urutan ASC, id ASC";
    }
    
    if (!empty($limit)) {
        $query .= " LIMIT $limit";
    }
    
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    return [];
}

// Get data by ID
function get_data_by_id($conn, $table, $id) {
    $query = "SELECT * FROM $table WHERE id = $id";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

// Insert data
function insert_data($conn, $table, $data) {
    $columns = implode(', ', array_keys($data));
    $values = implode(', ', array_map(function($value) use ($conn) {
        return "'" . $conn->real_escape_string($value) . "'";
    }, array_values($data)));
    
    $query = "INSERT INTO $table ($columns) VALUES ($values)";
    
    if ($conn->query($query)) {
        return $conn->insert_id;
    }
    
    return false;
}

// Update data
function update_data($conn, $table, $data, $id) {
    $set = '';
    foreach ($data as $key => $value) {
        $set .= "$key = '" . $conn->real_escape_string($value) . "', ";
    }
    $set = rtrim($set, ', ');
    
    $query = "UPDATE $table SET $set WHERE id = $id";
    
    return $conn->query($query);
}

// Delete data
function delete_data($conn, $table, $id) {
    $query = "DELETE FROM $table WHERE id = $id";
    return $conn->query($query);
}

// Check if email exists
function email_exists($conn, $email, $exclude_id = null) {
    $email = $conn->real_escape_string($email);
    $query = "SELECT id FROM admin_user WHERE email = '$email'";
    
    if ($exclude_id) {
        $query .= " AND id != $exclude_id";
    }
    
    $result = $conn->query($query);
    return $result->num_rows > 0;
}

// Check if username exists
function username_exists($conn, $username, $exclude_id = null) {
    $username = $conn->real_escape_string($username);
    $query = "SELECT id FROM admin_user WHERE username = '$username'";
    
    if ($exclude_id) {
        $query .= " AND id != $exclude_id";
    }
    
    $result = $conn->query($query);
    return $result->num_rows > 0;
}

// Upload file
function upload_file($file, $allowed_types = ['jpg', 'jpeg', 'png', 'gif'], $upload_dir = '../assets/images/') {
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return false;
    }
    
    $file_name = $file['name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    if (!in_array($file_ext, $allowed_types)) {
        return false;
    }
    
    $new_filename = time() . '_' . md5($file_name) . '.' . $file_ext;
    $upload_path = $upload_dir . $new_filename;
    
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        return $new_filename;
    }
    
    return false;
}

// Session handling
function start_session() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Check if user logged in
function is_logged_in() {
    start_session();
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

// Set flash message
function set_flash($type, $message) {
    start_session();
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

// Get flash message
function get_flash() {
    start_session();
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Redirect
function redirect($url) {
    // Handle different redirect scenarios
    if (strpos($url, 'http') === 0) {
        // Absolute URL
        header("Location: " . $url);
    } elseif (strpos($url, '/') === 0) {
        // Absolute path from web root - no modification needed
        header("Location: " . $url);
    } else {
        // Relative path - remove leading ./
        $url = ltrim($url, './');
        header("Location: " . $url);
    }
    exit;
}

// Get JSON response
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

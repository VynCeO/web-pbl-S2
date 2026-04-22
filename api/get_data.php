<?php
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../includes/functions.php';

$action = isset($_GET['action']) ? sanitize($_GET['action']) : '';
$response = ['success' => false, 'message' => 'Invalid request', 'data' => null];

switch ($action) {
    case 'get_pimpinan':
        $data = get_all_data($conn, 'pimpinan', 'urutan', 'urutan ASC');
        $response = [
            'success' => true,
            'message' => 'Data pimpinan berhasil diambil',
            'data' => $data
        ];
        break;

    case 'get_unit_usaha':
        $data = get_all_data($conn, 'unit_usaha', "status = 'aktif'", 'urutan ASC');
        $response = [
            'success' => true,
            'message' => 'Data unit usaha berhasil diambil',
            'data' => $data
        ];
        break;

    case 'get_layanan':
        $unit_id = isset($_GET['unit_id']) ? (int)$_GET['unit_id'] : 0;
        if ($unit_id > 0) {
            $data = get_all_data($conn, 'layanan', "unit_usaha_id = $unit_id AND status = 'aktif'", 'id ASC');
            $response = [
                'success' => true,
                'message' => 'Data layanan berhasil diambil',
                'data' => $data
            ];
        }
        break;

    case 'get_kontak':
        $query = "SELECT * FROM kontak LIMIT 1";
        $result = $conn->query($query);
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            $response = [
                'success' => true,
                'message' => 'Data kontak berhasil diambil',
                'data' => $data
            ];
        }
        break;

    case 'create_reservasi':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = sanitize($_POST['nama'] ?? '');
            $no_hp = sanitize($_POST['no_hp'] ?? '');
            $tanggal = sanitize($_POST['tanggal'] ?? '');
            $unit_id = (int)($_POST['unit_usaha_id'] ?? 0);
            $keterangan = sanitize($_POST['keterangan'] ?? '');

            if (empty($nama) || empty($no_hp) || empty($tanggal) || $unit_id <= 0) {
                $response = ['success' => false, 'message' => 'Data tidak lengkap'];
                break;
            }

            if (!validate_phone($no_hp)) {
                $response = ['success' => false, 'message' => 'Nomor telepon tidak valid'];
                break;
            }

            // Check if unit usaha exists
            $unit = get_data_by_id($conn, 'unit_usaha', $unit_id);
            if (!$unit) {
                $response = ['success' => false, 'message' => 'Unit usaha tidak ditemukan'];
                break;
            }

            $data = [
                'nama' => $nama,
                'no_hp' => $no_hp,
                'tanggal' => $tanggal,
                'unit_usaha_id' => $unit_id,
                'keterangan' => $keterangan
            ];

            $insert_id = insert_data($conn, 'reservasi', $data);
            if ($insert_id) {
                $response = [
                    'success' => true,
                    'message' => 'Reservasi berhasil dibuat. Kami akan segera menghubungi Anda.',
                    'data' => ['id' => $insert_id]
                ];
            } else {
                $response = ['success' => false, 'message' => 'Gagal membuat reservasi'];
            }
        }
        break;

    case 'get_reservasi':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0) {
            $data = get_data_by_id($conn, 'reservasi', $id);
            if ($data) {
                $response = [
                    'success' => true,
                    'message' => 'Data reservasi berhasil diambil',
                    'data' => $data
                ];
            } else {
                $response = ['success' => false, 'message' => 'Reservasi tidak ditemukan'];
            }
        }
        break;

    default:
        $response = ['success' => false, 'message' => 'Action tidak dikenali'];
}

echo json_encode($response);

?>

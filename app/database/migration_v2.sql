-- Migration v2.0 - Tambah fitur foto dan variasi produk
-- Script ini menambahkan kolom foto ke tabel pimpinan dan unit_usaha
-- Serta membuat tabel baru untuk variasi produk

USE bumdes_db;

-- 1. Tambah kolom foto ke tabel pimpinan (jika belum ada)
ALTER TABLE pimpinan 
ADD COLUMN foto VARCHAR(255) NULL AFTER keterangan;

-- 2. Tambah kolom foto ke tabel unit_usaha (jika belum ada) 
ALTER TABLE unit_usaha 
ADD COLUMN foto VARCHAR(255) NULL AFTER gambar;

-- 3. Buat tabel baru untuk variasi produk/harga
CREATE TABLE IF NOT EXISTS variasi_produk (
    id INT PRIMARY KEY AUTO_INCREMENT,
    unit_usaha_id INT NOT NULL,
    nama VARCHAR(100) NOT NULL,
    harga DECIMAL(10, 2) NOT NULL,
    keterangan TEXT,
    urutan INT DEFAULT 1,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (unit_usaha_id) REFERENCES unit_usaha(id) ON DELETE CASCADE,
    INDEX idx_unit (unit_usaha_id),
    INDEX idx_status (status)
);

-- 4. Hapus kolom yang redundan (icon dari unit_usaha - tidak digunakan)
-- ALTER TABLE unit_usaha DROP COLUMN icon; -- Uncomment jika perlu

-- 5. Tambah kolom untuk tracking file (opsional tapi berguna)
ALTER TABLE pimpinan ADD COLUMN foto_file_type VARCHAR(10) NULL DEFAULT 'jpg';
ALTER TABLE unit_usaha ADD COLUMN foto_file_type VARCHAR(10) NULL DEFAULT 'jpg';

-- 6. Buat tabel untuk laporan keuangan v2.1
CREATE TABLE IF NOT EXISTS laporan_keuangan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tanggal DATE NOT NULL,
    kategori VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    debit DECIMAL(15, 2) DEFAULT 0,
    kredit DECIMAL(15, 2) DEFAULT 0,
    saldo DECIMAL(15, 2) DEFAULT 0,
    unit_usaha_id INT,
    keterangan TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (unit_usaha_id) REFERENCES unit_usaha(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES admin_user(id) ON DELETE SET NULL,
    INDEX idx_tanggal (tanggal),
    INDEX idx_kategori (kategori),
    INDEX idx_unit (unit_usaha_id)
);

-- 7. Buat tabel untuk upload laporan keuangan (tracking file)
CREATE TABLE IF NOT EXISTS upload_laporan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_file VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    tanggal_mulai DATE,
    tanggal_akhir DATE,
    jumlah_baris INT,
    status ENUM('pending', 'processed', 'error') DEFAULT 'pending',
    error_message TEXT,
    uploaded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES admin_user(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

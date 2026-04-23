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

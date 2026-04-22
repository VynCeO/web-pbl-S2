-- Create Database
CREATE DATABASE IF NOT EXISTS bumdes_db;
USE bumdes_db;

-- Tabel Pimpinan
CREATE TABLE IF NOT EXISTS pimpinan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    posisi VARCHAR(100) NOT NULL,
    keterangan TEXT,
    foto VARCHAR(255),
    urutan INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Unit Usaha
CREATE TABLE IF NOT EXISTS unit_usaha (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    icon VARCHAR(255),
    gambar VARCHAR(255),
    urutan INT DEFAULT 1,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Reservasi
CREATE TABLE IF NOT EXISTS reservasi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    no_hp VARCHAR(15) NOT NULL,
    tanggal DATE NOT NULL,
    unit_usaha_id INT NOT NULL,
    keterangan TEXT,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (unit_usaha_id) REFERENCES unit_usaha(id) ON DELETE CASCADE
);

-- Tabel Layanan (untuk reservasi yang bisa di-customize)
CREATE TABLE IF NOT EXISTS layanan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    unit_usaha_id INT NOT NULL,
    nama VARCHAR(100) NOT NULL,
    harga DECIMAL(10, 2),
    deskripsi TEXT,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (unit_usaha_id) REFERENCES unit_usaha(id) ON DELETE CASCADE
);

-- Tabel Kontak
CREATE TABLE IF NOT EXISTS kontak (
    id INT PRIMARY KEY AUTO_INCREMENT,
    alamat TEXT NOT NULL,
    telepon VARCHAR(15) NOT NULL,
    whatsapp VARCHAR(15),
    email VARCHAR(100),
    facebook VARCHAR(100),
    instagram VARCHAR(100),
    instagram_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Admin User
CREATE TABLE IF NOT EXISTS admin_user (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    nama_lengkap VARCHAR(100),
    role ENUM('admin', 'moderator') DEFAULT 'admin',
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert data default
INSERT INTO kontak (alamat, telepon, whatsapp, email) VALUES 
('Jl. H. Nur Sugihwaras, RT 11 / RW 03, Rejo, Candi, Sidoarjo, Jawa Timur 61271', '0877-5813-5806', '0877-5813-5806', 'bumdes@sugihwaras.id');

INSERT INTO admin_user (username, password, email, nama_lengkap) VALUES 
('admin', SHA2('admin123', 256), 'admin@bumdes.id', 'Administrator');

-- Insert sample data Unit Usaha
INSERT INTO unit_usaha (nama, deskripsi, urutan, status) VALUES 
('GOR Sugihwaras', 'Gedung Olahraga untuk acara dan kegiatan olahraga', 1, 'aktif'),
('Rental Tenda', 'Sewa tenda untuk berbagai acara dan kegiatan', 2, 'aktif'),
('Air Minum Kemasan', 'Penjualan air minum dalam kemasan', 3, 'aktif'),
('Kopi Melek', 'Warung kopi dengan berbagai varian minuman', 4, 'aktif'),
('Peternakan Sapi & Kambing', 'Peternakan dan penjualan sapi serta kambing berkualitas', 5, 'aktif'),
('Pembayaran PBB', 'Layanan pembayaran pajak bumi dan bangunan', 6, 'aktif');

-- Insert sample data Pimpinan
INSERT INTO pimpinan (nama, posisi, keterangan, urutan) VALUES 
('Syaiful', 'Komisaris / Kepala Desa', 'Pimpinan tertinggi BUMDes', 1),
('Marsudi, S.Pd, M.M', 'Direktur', 'Mengelola operasional BUMDes', 2),
('Agus Indra Prasetyo', 'Sekretaris', 'Mengelola administrasi dan dokumentasi', 3),
('Mohammad Murti Sudiyo', 'Bendahara', 'Mengelola keuangan BUMDes', 4);


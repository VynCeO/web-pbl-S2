# 🔄 SISTEM REFACTOR v2.0 - PANDUAN IMPLEMENTASI

## 📋 Daftar Perubahan

### ✅ Sudah Selesai:
1. **Optimasi Functions** - Refactor `functions_v2.php` dengan:
   - Prepared statements untuk keamanan SQL injection
   - Better error handling & logging
   - File upload management yang lebih robust
   - Type hints validation

2. **Photo Upload Feature** - Di manage_unit.php:
   - Upload foto untuk setiap unit usaha
   - Preview sebelum upload
   - Auto-delete foto lama saat edit
   - Validasi ukuran & tipe file

3. **Product Variations** - Fitur variasi produk & harga:
   - Tambah/edit/hapus variasi produk
   - Display harga dengan format Rupiah
   - Terstruktur dengan baik di tab terpisah

4. **Database Schema** - Tabel baru & kolom:
   - Kolom `foto` di tabel `pimpinan` dan `unit_usaha`
   - Tabel baru `variasi_produk` dengan struktur lengkap

---

## 🚀 INSTALASI & IMPLEMENTASI

### Step 1: Update Database

Jalankan migration script:

```sql
-- Copy semua query dari file app/database/migration_v2.sql
-- Atau jalankan di phpMyAdmin:

USE bumdes_db;

-- Tambah kolom foto (jika belum ada)
ALTER TABLE pimpinan 
ADD COLUMN foto VARCHAR(255) NULL AFTER keterangan;

ALTER TABLE unit_usaha 
ADD COLUMN foto VARCHAR(255) NULL AFTER gambar;

-- Buat tabel variasi produk
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
```

### Step 2: Update Config

**PENTING**: Ganti reference dari `functions.php` ke `functions_v2.php`:

Di `public/admin/manage_unit.php` (baris ke-7):
```php
// Sebelum:
// require_once '../../app/includes/functions.php';

// Sesudah:
require_once '../../app/includes/functions_v2.php';
```

### Step 3: Verifikasi Struktur File

Pastikan struktur folder sudah benar:
```
app/
├── includes/
│   ├── functions.php (LAMA - optional)
│   └── functions_v2.php (BARU - gunakan ini)
├── database/
│   ├── init.sql
│   └── migration_v2.sql (BARU)
└── config/
    └── database.php

assets/
└── images/ (folder untuk upload foto)

public/admin/
├── manage_unit.php (SUDAH DIUPDATE)
├── manage_pimpinan.php (AKAN DIUPDATE)
├── manage_reservasi.php
├── manage_kontak.php
└── layouts/
    └── admin_layout.php (OPTIONAL - template shared)
```

---

## 🎯 FITUR BARU: CARA PENGGUNAAN

### 1️⃣ Upload Foto Unit Usaha

**Lokasi**: Admin → Unit Usaha → Edit Unit

**Fitur**:
- Input file dengan preview otomatis
- Validasi: JPG, JPEG, PNG, GIF
- Max size: 5MB
- File lama otomatis dihapus saat edit

**Cara Pakai**:
1. Klik "Edit" pada unit yang ingin diupdate
2. Scroll ke section "Foto Unit"
3. Pilih file gambar
4. Preview akan tampil otomatis
5. Klik "Simpan Perubahan"

### 2️⃣ Variasi Produk & Harga

**Lokasi**: Admin → Unit Usaha → Edit Unit → Tab "Variasi Produk & Harga"

**Contoh Data**:
```
Unit Usaha: Air Minum Kemasan
├── 1 Dus = Rp 45.000
├── 2 Dus = Rp 89.000
├── 3 Dus = Rp 130.000
└── Galon = Rp 25.000

Unit Usaha: Kopi Melek
├── Kopi Biasa = Rp 8.000
├── Kopi Premium = Rp 12.000
└── Kopi Campur = Rp 10.000
```

**Cara Pakai**:
1. Edit Unit Usaha
2. Klik Tab "Variasi Produk & Harga"
3. Isi form:
   - Nama: "1 Dus" atau "Air Minum Galon"
   - Harga: 45000 (angka saja)
   - Keterangan: (opsional)
   - Urutan: nomor urutan tampilan
4. Klik "Tambah Variasi"
5. Variasi akan tampil di bawah dengan harga otomatis format Rupiah

---

## 📊 SKEMA DATABASE

### Tabel `variasi_produk`:
```
id              INT (Primary Key)
unit_usaha_id   INT (Foreign Key → unit_usaha)
nama            VARCHAR(100)  - Nama produk/variasi
harga           DECIMAL(10,2) - Harga produk
keterangan      TEXT          - Deskripsi tambahan
urutan          INT           - Urutan tampilan
status          ENUM('aktif', 'nonaktif')
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### Kolom Baru:
```
pimpinan.foto           VARCHAR(255) - Path foto pimpinan
unit_usaha.foto         VARCHAR(255) - Path foto unit
unit_usaha.foto_file_type VARCHAR(10) - Tipe file (jpg, png, dll)
```

---

## 🔧 REFACTORING IMPROVEMENTS

### Fungsi Baru di functions_v2.php:

1. **`validate_currency($value)`** - Validasi nilai uang
   ```php
   if (validate_currency($harga)) { // OK
   ```

2. **`replace_file($old, $new, $types, $dir)`** - Replace file dengan auto-delete old
   ```php
   $foto = replace_file($old_foto, $_FILES['foto'], ['jpg','png'], UPLOAD_DIR);
   ```

3. **`get_count($conn, $table, $where)`** - Get count records
   ```php
   $total = get_count($conn, 'unit_usaha');
   ```

4. **Prepared Statements** - Keamanan lebih baik
   ```php
   // LAMA (rentan SQL injection):
   update_data($conn, 'unit_usaha', $data, $id);
   
   // BARU (aman dengan prepared statements):
   update_data($conn, 'unit_usaha', $data, $id);
   ```

### Performance:
- ✅ Prepared statements (anti SQL injection)
- ✅ Better error handling
- ✅ Index di tabel variasi_produk
- ✅ Cascade delete otomatis
- ✅ Efficient file operations

---

## ⚠️ MIGRASI DATA EXISTING

Jika sudah punya data unit usaha sebelumnya:

```sql
-- Tidak perlu migrate data karena struktur sama
-- Hanya tambah kolom foto yang baru
-- Variasi produk bisa ditambah belakangan
```

---

## 🔐 BACKUP SEBELUM UPDATE

```bash
# Backup database:
mysqldump -u root bumdes_db > backup_bumdes_$(date +%Y%m%d).sql

# Backup files (jika perlu):
# Ganti /path/to/bumdes ke lokasi project Anda
```

---

## ✨ NEXT STEPS (Optional)

1. **Tambah foto upload di manage_pimpinan.php** - Follow pattern di manage_unit.php
2. **API endpoint untuk variasi produk** - Di `public/api/get_data.php`
3. **Frontend display variasi** - Di halaman publik (src/index.php)
4. **Export/Import data** - Feature untuk backup/restore
5. **Image optimization** - Compress foto otomatis saat upload

---

## 🐛 TROUBLESHOOTING

### Error: "Table 'variasi_produk' doesn't exist"
**Solusi**: Run migration script di atas

### Error: "Function functions_v2 not found"
**Solusi**: Check path di require_once, pastikan nama file benar

### Foto tidak upload
**Solusi**: 
- Check folder permissions: `chmod 755 assets/images/`
- Check file size < 5MB
- Check format: JPG, JPEG, PNG, GIF only

### Harga tidak ter-format Rupiah
**Solusi**: Pastikan menggunakan `format_rupiah()` saat display

---

## 📞 CATATAN PENTING

- **Compatibility**: Kompatibel dengan PHP 7.4+
- **Database**: MySQL 5.7+
- **Backup**: Selalu backup sebelum update
- **Testing**: Test di localhost dulu sebelum production

---

✅ **Refactor Selesai!**

Sistem sudah lebih optimal dengan:
- ✅ Prepared statements (security)
- ✅ Photo upload management
- ✅ Product variations tracking
- ✅ Better error handling
- ✅ Cleaner code structure

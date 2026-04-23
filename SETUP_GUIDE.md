# Setup & Dummy Data - BUMDes Sugihwaras

Panduan lengkap untuk setup project dan load dummy data untuk testing & development.

---

## 📋 Prerequisites

Sebelum memulai, pastikan sudah install:
- **PHP 7.4+** dengan MySQL extension
- **MySQL Server** (MariaDB)
- **Browser** (Chrome, Firefox, Safari, Edge)
- **Text Editor** (VS Code, Sublime Text, dll)

---

## 🚀 Quick Start

### 1. Persiapan Database

```bash
# Akses MySQL
mysql -u root -p

# Buat database baru (atau gunakan yang sudah ada)
CREATE DATABASE bumdes_db;
USE bumdes_db;

# Import schema
source app/database/init.sql;
```

Atau langsung import melalui GUI (phpMyAdmin, MySQL Workbench, dll).

### 2. Update Database Config

Edit file `app/config/database.php`:

```php
define('DB_HOST', 'localhost');      // Host database
define('DB_USER', 'root');           // Username MySQL
define('DB_PASS', '');               // Password MySQL (kosong jika default)
define('DB_NAME', 'bumdes_db');      // Nama database
```

### 3. Load Dummy Data

Ada 2 cara untuk load dummy data:

#### Cara A: GUI Setup Page (Recommended)

1. Buka project di browser: `http://localhost/path-to-project/`
2. Buka setup page: `http://localhost/path-to-project/setup_dummy_data.php`
3. Klik tombol **"✅ Load Dummy Data"**
4. Tunggu hingga selesai ✓

#### Cara B: Import SQL File Langsung

```bash
# Via MySQL CLI
mysql -u root bumdes_db < app/database/dummy_data.sql

# Atau gunakan GUI tools
```

---

## 📊 Data yang Di-Load

Setelah load dummy data, database akan berisi:

### Pimpinan (4 records)
- Syaiful - Komisaris / Kepala Desa
- Marsudi, S.Pd, M.M - Direktur Utama
- Agus Indra Prasetyo - Sekretaris
- Mohammad Murti Sudiyo - Bendahara

**Foto:** Diambil dari internet (UI Avatars API)

### Unit Usaha (6 records)
1. GOR Sugihwaras
2. Rental Tenda
3. Air Minum Kemasan
4. Kopi Melek
5. Peternakan Sapi & Kambing
6. Pembayaran PBB Online

### Layanan (21 records)
- GOR: 3 layanan (paket harian, 2 hari, malam)
- Rental Tenda: 4 layanan (berbagai ukuran + paket pernikahan)
- Air Minum: 2 layanan (19L, 1.5L)
- Kopi Melek: 3 layanan
- Peternakan: 2 layanan
- PBB: 1 layanan

### Reservasi (8 records)
- **Pending** (3): Menunggu konfirmasi
- **Confirmed** (3): Sudah dikonfirmasi
- **Completed** (2): Sudah selesai
- **Cancelled** (2): Dibatalkan

Setiap reservasi sudah include:
- Nama customer
- No HP
- Tanggal Mulai & Tanggal Kembali
- Unit Usaha
- Status

### Admin Users (3 records)

| Username | Password   | Role      |
|----------|-----------|-----------|
| admin    | admin123   | Admin     |
| marsudi  | marsudi123 | Moderator |
| agus     | agus123    | Moderator |

### Kontak (1 record)
- Alamat: Desa Sugihwaras, Sidoarjo
- Telepon: 0877-5813-5806
- WhatsApp: 0877-5813-5806
- Email: bumdes.sugihwaras@gmail.com
- Instagram: @bumdes.sugihwaras19
- Facebook: BUMDes Sugihwaras

---

## 🎨 Assets - Logo dari Internet

Project menggunakan logo dan foto dari internet (external CDN):

### Logo Navbar
- **URL:** `https://ui-avatars.com/api/`
- **Source:** UI Avatars (Free CDN)
- **Keuntungan:**
  - Tidak perlu upload file
  - Auto-generate avatar
  - Responsive & profesional
  - Gratis unlimited

### Foto Pimpinan
- **Source:** UI Avatars API
- **Format:** Generated avatar dengan nama
- **Color:** Sesuai dengan brand color (#1f5b3a)

### Custom Logo

Jika ingin menggunakan logo custom:

1. **Upload file ke `public/assets/images/`**
   ```
   public/assets/images/
   ├── logo.png
   ├── sidarjo.jpeg
   └── ...
   ```

2. **Update reference di public/index.php**
   ```html
   <img src="./assets/images/logo.png" alt="Logo" width="40">
   ```

3. **Update reference di database**
   - Foto pimpinan: tabel `pimpinan` kolom `foto`
   - Gambar unit: tabel `unit_usaha` kolom `gambar`

---

## 🧪 Testing

### 1. Homepage Testing
- ✅ Buka: `http://localhost/path-to-project/public/index.php`
- ✅ Lihat semua pimpinan, unit usaha, reservasi form
- ✅ Coba submit reservasi
- ✅ Lihat booked dates

### 2. Admin Testing
- ✅ Buka: `http://localhost/path-to-project/public/admin/login.php`
- ✅ Login dengan: `admin` / `admin123`
- ✅ Explore dashboard & laporan keuangan
- ✅ Lihat semua data ter-load dengan benar

### 3. Mobile Responsive Testing
- ✅ Test di mobile device atau DevTools (F12)
- ✅ Hamburger menu berfungsi
- ✅ Layout responsive di 480px, 768px, 1024px

### 4. Reservasi Testing
- ✅ Lihat booked dates di form
- ✅ Coba booking tanggal yang available
- ✅ Coba submit & lihat di admin panel

---

## 🔄 Reset Data

Jika perlu reset/clear semua data:

### Cara 1: Gunakan GUI
1. Buka: `http://localhost/path-to-project/setup_dummy_data.php`
2. Klik tombol **"⚠️ Clear All Data"**

### Cara 2: SQL Command
```sql
TRUNCATE TABLE reservasi;
TRUNCATE TABLE layanan;
TRUNCATE TABLE unit_usaha;
TRUNCATE TABLE pimpinan;
TRUNCATE TABLE kontak;
TRUNCATE TABLE admin_user;
```

Setelah itu, load ulang dummy data.

---

## 📁 Struktur Folder

```
web-pbl-S2/
├── public/                  # Browser-accessible files
│   ├── index.php           # Homepage
│   ├── assets/
│   │   ├── css/style.css
│   │   ├── js/script.js
│   │   └── images/         # Local images (optional)
│   ├── admin/              # Admin panel
│   │   ├── login.php
│   │   ├── index.php
│   │   ├── laporan_keuangan.php
│   │   └── ...
│   └── api/
│       └── get_data.php
│
├── app/                    # Private files
│   ├── config/
│   │   └── database.php    # ⚙️ EDIT INI
│   ├── database/
│   │   ├── init.sql        # Schema
│   │   └── dummy_data.sql  # Data dummy
│   └── includes/
│       └── functions.php
│
├── setup_dummy_data.php    # ✅ Setup page (buka ini)
├── index.php               # Redirect ke public/index.php
└── ...
```

---

## 🐛 Troubleshooting

### Error: "Database connection failed"
**Solusi:**
1. Cek MySQL server sudah running
2. Verify DB config di `app/config/database.php`
3. Cek username/password MySQL

### Error: "Table already exists"
**Solusi:**
1. Database sudah ada, skip `CREATE DATABASE` step
2. Pastikan schema sudah ter-import dari `init.sql`

### Logo tidak tampil
**Solusi:**
1. Check internet connection (CDN perlu internet)
2. Atau gunakan logo local: simpan ke `public/assets/images/`

### Reservasi tidak muncul di booked dates
**Solusi:**
1. Pastikan dummy data sudah ter-load
2. Check koneksi API di browser console (F12)
3. Verify `public/api/get_data.php` accessible

### Admin login gagal
**Solusi:**
1. Password default: `admin123` (jangan lupa 123)
2. Clear data & load ulang dummy
3. Check database sudah ter-import

---

## 📚 API Endpoints

Setelah setup, API sudah siap digunakan:

- `GET /api/get_data.php?action=get_pimpinan` - List pimpinan
- `GET /api/get_data.php?action=get_unit_usaha` - List unit usaha
- `GET /api/get_data.php?action=get_reservasi` - List reservasi
- `POST /api/get_data.php?action=create_reservasi` - Buat reservasi baru

---

## ✅ Checklist Setup

- [ ] MySQL server running
- [ ] Database created & imported schema
- [ ] Config file updated (app/config/database.php)
- [ ] Dummy data loaded via setup page
- [ ] Homepage accessible: http://localhost/.../public/index.php
- [ ] Admin accessible: http://localhost/.../public/admin/login.php
- [ ] Can login dengan admin/admin123
- [ ] Pimpinan, unit usaha, reservasi muncul di homepage
- [ ] Booked dates display di form reservasi
- [ ] Laporan keuangan accessible di admin
- [ ] Mobile responsive working

---

## 🎓 Next Steps

Setelah setup berhasil:

1. **Explore Homepage**
   - Lihat struktur HTML
   - Test form reservasi
   - Check responsive design

2. **Explore Admin Panel**
   - Manage pimpinan
   - Manage unit usaha
   - Manage reservasi & status
   - Lihat laporan keuangan

3. **Test Development**
   - Edit CSS di `public/assets/css/style.js`
   - Edit JS di `public/assets/js/script.js`
   - Test dengan data dummy

4. **Production Ready**
   - Ganti dummy data dengan data real
   - Update config untuk production
   - Setup backup database
   - Deploy ke server

---

## 📞 Support

Jika ada pertanyaan atau issue:
1. Check dokumentasi di `README.md`
2. Check file `GETTING_STARTED.md`
3. Check `IMPLEMENTATION.md`

---

**Selamat! Setup project selesai! 🎉**

Homepage: `http://localhost/.../public/index.php`  
Admin: `http://localhost/.../public/admin/login.php`  
Setup: `http://localhost/.../setup_dummy_data.php`

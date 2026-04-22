# BUMDes Sukses Bersama - Website

Website promosi dan reservasi untuk **BUMDes Sukses Bersama Desa Sugihwaras**, dibangun dengan **PHP Native** tanpa framework.

## 🎯 Fitur Utama

### Frontend
- **Halaman Utama**: Hero section dengan informasi BUMDes
- **Profil Pimpinan**: Menampilkan struktur kepemimpinan
- **Unit Usaha**: Daftar semua unit bisnis yang tersedia
- **Reservasi Online**: Form pemesanan untuk GOR, Tenda, dan layanan lainnya
- **Laporan Keuangan**: Transparansi keuangan BUMDes
- **Kontak & Lokasi**: Informasi lengkap dan social media
- **Responsive Design**: Optimal di semua perangkat

### Backend & Admin
- **Admin Dashboard**: Monitoring data dan statistics
- **Management Pimpinan**: CRUD untuk data pimpinan
- **Management Unit Usaha**: CRUD untuk unit usaha  
- **Management Reservasi**: Tracking dan update status reservasi
- **Management Kontak**: Edit informasi kontak
- **Sistem Autentikasi**: Login security dengan session

## 📋 Persyaratan Sistem

- PHP 7.4 atau lebih tinggi
- MySQL/MariaDB
- Browser modern (Chrome, Firefox, Safari, Edge)

## 🚀 Quick Start

### 1. Setup Database

```bash
# Navigasi ke folder project
cd web-pbl-S2

# Jalankan server PHP
php -S localhost:8000
```

Buka: `http://localhost:8000/setup.php` dan klik "Setup Database Sekarang"

### 2. Akses Website

```
Frontend: http://localhost:8000/src/index.php
Admin:    http://localhost:8000/admin/login.php
```

**Login Admin Default:**
- Username: `admin`
- Password: `admin123`

⚠️ **PENTING**: Ubah password default setelah instalasi!

## 📁 Struktur Proyek

```
web-pbl-S2/
├── config/              # Konfigurasi
│   └── database.php    # Database config
├── database/           # SQL files
│   └── init.sql       # Database initialization
├── includes/          # Helper functions
│   └── functions.php  # Fungsi-fungsi helper
├── api/              # API endpoints
│   └── get_data.php  # REST API untuk frontend
├── admin/            # Admin panel
│   ├── login.php
│   ├── index.php
│   ├── manage_pimpinan.php
│   ├── manage_unit.php
│   ├── manage_reservasi.php
│   ├── manage_kontak.php
│   └── logout.php
├── src/              # Frontend
│   └── index.php     # Halaman utama
├── assets/
│   ├── css/style.css
│   ├── js/script.js
│   └── images/       # Asset images
├── setup.php         # Database setup wizard
├── README.md
└── IMPLEMENTATION.md
```

## 🔧 Konfigurasi

Edit `config/database.php`:

```php
define('DB_HOST', 'localhost');      // Host database
define('DB_USER', 'root');           // Username
define('DB_PASS', '');               // Password
define('DB_NAME', 'bumdes_db');      // Database name
define('BASE_URL', 'http://localhost:8000'); // Base URL
```

## 💾 Database Schema

**8 Tables:**
- `pimpinan` - Data pimpinan BUMDes
- `unit_usaha` - Unit bisnis yang tersedia
- `reservasi` - Data reservasi/booking
- `layanan` - Layanan per unit usaha
- `kontak` - Informasi kontak BUMDes
- `admin_user` - Admin accounts

## 📱 API Endpoints

| Endpoint | Method | Deskripsi |
|----------|--------|-----------|
| `/api/get_data.php?action=get_pimpinan` | GET | Get semua pimpinan |
| `/api/get_data.php?action=get_unit_usaha` | GET | Get semua unit usaha |
| `/api/get_data.php?action=get_kontak` | GET | Get informasi kontak |
| `/api/get_data.php?action=create_reservasi` | POST | Buat reservasi baru |

## ✅ Validasi Form

- **Nama**: Required, string
- **No. HP**: Required, format Indonesia (08xx atau +62xx)
- **Tanggal**: Required, minimum hari ini
- **Unit Usaha**: Required, harus exist di database

## 📊 Status Reservasi

- `pending` - Menunggu konfirmasi
- `confirmed` - Dikonfirmasi
- `completed` - Selesai
- `cancelled` - Dibatalkan

## 🎨 Customization

### Menambah Unit Usaha Baru
1. Admin Panel → Unit Usaha → Tambah
2. Isi form dan simpan
3. Unit akan langsung muncul di website

### Mengubah Informasi Kontak
1. Admin Panel → Kontak
2. Edit informasi
3. Simpan

### Upload Asset
- Foto pimpinan: `assets/images/`
- Gambar unit: `assets/images/`
- Accepted: JPG, PNG, GIF (max 5MB)

## 🔐 Keamanan

- ✓ Password SHA256 hashing
- ✓ Input sanitization on all forms
- ✓ Session-based authentication
- ✓ CSRF protection ready
- ✓ SQL injection prevention

## 🚨 Troubleshooting

**Database Connection Error?**
- Pastikan MySQL running
- Check credentials di `config/database.php`

**Function not found?**
- Pastikan `includes/functions.php` exist
- Clear browser cache

**Form tidak submit?**
- Enable JavaScript di browser
- Check console untuk error messages

**Foto tidak muncul?**
- Check folder `assets/images/` exist
- Set permission: 755 atau 777

## 📞 Kontak

- **Telepon**: 0877-5813-5806
- **WhatsApp**: 0877-5813-5806
- **Email**: bumdes@sugihwaras.id
- **Alamat**: Jl. H. Nur Sugihwaras, RT 11 / RW 03, Rejo, Candi, Sidoarjo, Jawa Timur 61271

## 📝 Teknologi

- **Backend**: PHP 7.4+ (Native)
- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **Database**: MySQL/MariaDB
- **Responsive**: Mobile-first, Flexbox/Grid

## 📄 License

Proprietary - BUMDes Sukses Bersama Desa Sugihwaras

---

**Last Updated**: April 2026 | **Version**: 1.0

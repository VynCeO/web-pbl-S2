# Getting Started - BUMDes Website

## 📖 Panduan Memulai

### Step 1: Persiapan Awal

1. Pastikan PHP 7.4+ terinstall
2. Pastikan MySQL/MariaDB running
3. Navigasi ke folder project:
   ```bash
   cd c:\Users\Admin\Desktop\web-pbl-S2
   ```

### Step 2: Konfigurasi Database

Edit file `config/database.php` sesuaikan dengan pengaturan MySQL Anda:

```php
define('DB_HOST', 'localhost');      // Host
define('DB_USER', 'root');           // Username  
define('DB_PASS', '');               // Password (kosong jika no password)
define('DB_NAME', 'bumdes_db');      // Database name
```

### Step 3: Jalankan Server

```bash
# Dari folder project
php -S localhost:8000
```

Output akan menampilkan:
```
Development Server is running at http://localhost:8000
Press Ctrl-C to quit.
```

### Step 4: Setup Database

1. Buka browser: `http://localhost:8000/setup.php`
2. Centang checkbox agreement
3. Klik "Setup Database Sekarang"
4. Tunggu hingga berhasil (melihat pesan hijau ✓)

### Step 5: Akses Website

- **Website Utama**: `http://localhost:8000/src/index.php`
- **Admin Panel**: `http://localhost:8000/admin/login.php`

**Login dengan:**
- Username: `admin`
- Password: `admin123`

⚠️ **SEGERA UBAH PASSWORD ADMIN!**

## 🎯 Yang Dapat Dilakukan

### Di Frontend (Website Utama)

1. **Lihat Informasi BUMDes**
   - Hero section dengan deskripsi
   - Profil pimpinan
   - Unit usaha yang tersedia

2. **Buat Reservasi**
   - Isi form dengan nama, nomor HP, tanggal
   - Pilih unit usaha
   - Tambahkan keterangan (opsional)
   - Submit

3. **Hubungi BUMDes**
   - Call/WhatsApp langsung
   - Email
   - Ikuti social media

### Di Admin Panel

1. **Dashboard**
   - Lihat statistik: Total pimpinan, unit usaha, reservasi
   - Monitor reservasi pending

2. **Manajemen Pimpinan**
   - Tambah pimpinan baru
   - Edit data pimpinan
   - Hapus pimpinan
   - Atur urutan tampilan

3. **Manajemen Unit Usaha**
   - Tambah unit usaha baru
   - Edit informasi unit
   - Ubah status (aktif/nonaktif)
   - Hapus unit

4. **Manajemen Reservasi**
   - Lihat semua reservasi
   - Filter by status (pending, confirmed, completed, cancelled)
   - Update status reservasi
   - Hapus reservasi

5. **Manajemen Kontak**
   - Edit alamat
   - Edit nomor telepon/WhatsApp
   - Edit email
   - Update social media links

## 📸 Menambah Assets (Foto/Gambar)

### Upload Foto Pimpinan

1. Buka Admin Panel → Pimpinan
2. Buat pimpinan baru atau edit existing
3. Simpan
4. Upload foto ke folder: `assets/images/`
5. Update data pimpinan dengan nama foto

**Nama File Format**: `pimpinan_nama.jpg`

### Upload Gambar Unit Usaha

1. Buka Admin Panel → Unit Usaha
2. Simpan unit
3. Upload gambar ke folder: `assets/images/`

**Accepted Formats**: JPG, PNG, GIF (Max 5MB)

## 🔍 Troubleshooting

### Error 1: "Connection failed"
```
Solusi:
1. Pastikan MySQL/MariaDB running
2. Check username/password di config/database.php
3. Restart MySQL service
```

### Error 2: "Database doesn't exist"
```
Solusi:
1. Akses http://localhost:8000/setup.php
2. Jalankan setup database
3. Tunggu hingga berhasil
```

### Error 3: "Table doesn't exist"
```
Solusi:
1. Setup ulang database via setup.php
2. Pastikan permission folder
3. Check syntax SQL di database/init.sql
```

### Error 4: "File not found / 404"
```
Solusi:
1. Pastikan URL benar
2. Check folder structure
3. Restart server PHP
```

### Foto tidak muncul
```
Solusi:
1. Pastikan folder assets/images/ exist
2. Set permission: chmod 755 assets/images/
3. Nama file di database harus sesuai
```

## 🔐 Security Tips

1. **Ubah Password Admin**
   - Login dengan admin/admin123
   - Ganti password di profile/settings

2. **Hapus setup.php**
   - Setelah setup berhasil, delete file setup.php
   - File ini hanya untuk initial setup

3. **Set File Permission**
   ```bash
   chmod 755 config/database.php
   chmod 777 assets/images/
   ```

4. **Backup Database**
   ```bash
   mysqldump -u root -p bumdes_db > backup.sql
   ```

## 📞 API Usage (For Developers)

### Get All Leadership
```javascript
fetch('http://localhost:8000/api/get_data.php?action=get_pimpinan')
  .then(res => res.json())
  .then(data => console.log(data))
```

### Get All Business Units
```javascript
fetch('http://localhost:8000/api/get_data.php?action=get_unit_usaha')
  .then(res => res.json())
  .then(data => console.log(data))
```

### Create Reservation
```javascript
const formData = new FormData();
formData.append('nama', 'John Doe');
formData.append('no_hp', '0877123456');
formData.append('tanggal', '2026-05-01');
formData.append('unit_usaha_id', 1);
formData.append('keterangan', 'Event pribadi');

fetch('http://localhost:8000/api/get_data.php?action=create_reservasi', {
  method: 'POST',
  body: formData
})
.then(res => res.json())
.then(data => console.log(data))
```

## 📝 File Penting

| File | Fungsi |
|------|--------|
| `config/database.php` | Database connection config |
| `includes/functions.php` | Helper functions library |
| `api/get_data.php` | Backend API endpoints |
| `src/index.php` | Frontend utama |
| `assets/css/style.css` | Styling dan responsive |
| `assets/js/script.js` | Frontend interactivity |
| `admin/index.php` | Admin dashboard |
| `database/init.sql` | Database schema |

## 🎓 Learning Resources

### Untuk Memahami Kode

1. **Database Schema**: Lihat `database/init.sql`
2. **Helper Functions**: Lihat `includes/functions.php`
3. **API Endpoints**: Lihat `api/get_data.php`
4. **HTML Structure**: Lihat `src/index.php`
5. **CSS Styling**: Lihat `assets/css/style.css`
6. **JavaScript**: Lihat `assets/js/script.js`

### Next Steps

1. Customize dengan assets Anda (foto, logo, gambar)
2. Update informasi kontak yang benar
3. Tambahkan pimpinan, unit usaha, dan layanan
4. Test reservasi functionality
5. Deploy ke server

## 🚀 Deployment

Untuk production, gunakan:

- **Apache/Nginx** instead of PHP built-in server
- **SSL Certificate** for HTTPS
- **Database Backup** regular schedule
- **Monitoring** tools
- **CDN** untuk assets static

## 📞 Support

Jika ada pertanyaan atau error, silakan:

1. Check `README.md` untuk dokumentasi lengkap
2. Check `IMPLEMENTATION.md` untuk technical details
3. Baca error messages di browser console
4. Check file `database/init.sql` untuk schema

---

**Happy Coding! 🎉**

BUMDes Sukses Bersama Desa Sugihwaras

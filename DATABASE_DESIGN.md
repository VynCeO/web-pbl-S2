📊 DATABASE DESIGN DOCUMENTATION
═════════════════════════════════════════════════════════

## Database Schema Overview

```
bumdes_db
│
├── TABLE: pimpinan
│   ├── id (PK, INT, AI)
│   ├── nama (VARCHAR 100)
│   ├── posisi (VARCHAR 100)
│   ├── keterangan (TEXT)
│   ├── foto (VARCHAR 255)
│   ├── urutan (INT)
│   ├── created_at (TIMESTAMP)
│   └── updated_at (TIMESTAMP)
│
├── TABLE: unit_usaha
│   ├── id (PK, INT, AI)
│   ├── nama (VARCHAR 100)
│   ├── deskripsi (TEXT)
│   ├── icon (VARCHAR 255)
│   ├── gambar (VARCHAR 255)
│   ├── urutan (INT)
│   ├── status (ENUM: aktif, nonaktif)
│   ├── created_at (TIMESTAMP)
│   └── updated_at (TIMESTAMP)
│
├── TABLE: reservasi
│   ├── id (PK, INT, AI)
│   ├── nama (VARCHAR 100)
│   ├── no_hp (VARCHAR 15)
│   ├── tanggal (DATE)
│   ├── unit_usaha_id (FK → unit_usaha.id)
│   ├── keterangan (TEXT)
│   ├── status (ENUM: pending, confirmed, completed, cancelled)
│   ├── created_at (TIMESTAMP)
│   └── updated_at (TIMESTAMP)
│
├── TABLE: layanan
│   ├── id (PK, INT, AI)
│   ├── unit_usaha_id (FK → unit_usaha.id)
│   ├── nama (VARCHAR 100)
│   ├── harga (DECIMAL 10,2)
│   ├── deskripsi (TEXT)
│   ├── status (ENUM: aktif, nonaktif)
│   ├── created_at (TIMESTAMP)
│   └── updated_at (TIMESTAMP)
│
├── TABLE: kontak
│   ├── id (PK, INT, AI)
│   ├── alamat (TEXT)
│   ├── telepon (VARCHAR 15)
│   ├── whatsapp (VARCHAR 15)
│   ├── email (VARCHAR 100)
│   ├── facebook (VARCHAR 100)
│   ├── instagram (VARCHAR 100)
│   ├── instagram_url (VARCHAR 255)
│   ├── created_at (TIMESTAMP)
│   └── updated_at (TIMESTAMP)
│
└── TABLE: admin_user
    ├── id (PK, INT, AI)
    ├── username (VARCHAR 50, UNIQUE)
    ├── password (VARCHAR 255) - SHA256 Hash
    ├── email (VARCHAR 100)
    ├── nama_lengkap (VARCHAR 100)
    ├── role (ENUM: admin, moderator)
    ├── status (ENUM: aktif, nonaktif)
    ├── created_at (TIMESTAMP)
    └── updated_at (TIMESTAMP)
```

## Entity Relationship Diagram (ERD)

```
┌──────────────────────┐
│     admin_user       │
├──────────────────────┤
│ id (PK)              │
│ username (UNIQUE)    │
│ password (SHA256)    │
│ email                │
│ nama_lengkap         │
│ role                 │
│ status               │
└──────────────────────┘

┌──────────────────────┐        ┌──────────────────────┐
│     pimpinan         │        │   unit_usaha         │
├──────────────────────┤        ├──────────────────────┤
│ id (PK)              │        │ id (PK)              │
│ nama                 │        │ nama                 │
│ posisi               │        │ deskripsi            │
│ keterangan           │        │ icon                 │
│ foto                 │        │ gambar               │
│ urutan               │        │ urutan               │
└──────────────────────┘        │ status (aktif/*)     │
                                └──────────────────────┘
                                    ▲
                                    │ 1 : N
                    ┌───────────────┴───────────────┐
                    │                               │
            ┌───────────────────┐        ┌──────────────────────┐
            │    reservasi      │        │     layanan          │
            ├───────────────────┤        ├──────────────────────┤
            │ id (PK)           │        │ id (PK)              │
            │ nama              │        │ unit_usaha_id (FK)   │
            │ no_hp             │        │ nama                 │
            │ tanggal           │        │ harga                │
            │ unit_usaha_id(FK) │        │ deskripsi            │
            │ keterangan        │        │ status               │
            │ status            │        └──────────────────────┘
            └───────────────────┘

┌──────────────────────┐
│     kontak           │
├──────────────────────┤
│ id (PK)              │
│ alamat               │
│ telepon              │
│ whatsapp             │
│ email                │
│ facebook             │
│ instagram            │
│ instagram_url        │
└──────────────────────┘
```

## Data Relationships

### 1. unit_usaha ← → reservasi (One-to-Many)
- 1 Unit Usaha dapat memiliki banyak reservasi
- Setiap reservasi mereferensi 1 Unit Usaha
- Foreign Key: reservasi.unit_usaha_id → unit_usaha.id

### 2. unit_usaha ← → layanan (One-to-Many)
- 1 Unit Usaha dapat memiliki banyak layanan
- Setiap layanan mereferensi 1 Unit Usaha
- Foreign Key: layanan.unit_usaha_id → unit_usaha.id

### 3. admin_user (Independent)
- Standalone table untuk authentication
- Tidak ada relasi dengan tabel lain

### 4. pimpinan (Independent)
- Standalone table
- Tidak ada relasi foreign key

### 5. kontak (Singleton)
- Hanya 1 record
- Informasi kontak global BUMDes

## Table Details

### PIMPINAN
**Fungsi**: Menyimpan data struktur kepemimpinan BUMDes

| Field | Type | Nullable | Default | Description |
|-------|------|----------|---------|-------------|
| id | INT | NO | AUTO_INCREMENT | Primary Key |
| nama | VARCHAR(100) | NO | - | Nama pimpinan |
| posisi | VARCHAR(100) | NO | - | Posisi/jabatan |
| keterangan | TEXT | YES | NULL | Deskripsi tambahan |
| foto | VARCHAR(255) | YES | NULL | Filename foto |
| urutan | INT | YES | 1 | Urutan tampilan |
| created_at | TIMESTAMP | NO | CURRENT_TIMESTAMP | Record creation |
| updated_at | TIMESTAMP | NO | CURRENT_TIMESTAMP ON UPDATE | Last modification |

**Sample Data**:
```sql
INSERT INTO pimpinan VALUES 
(1, 'Syaiful', 'Komisaris / Kepala Desa', 'Pimpinan tertinggi BUMDes', NULL, 1, NOW(), NOW()),
(2, 'Marsudi, S.Pd, M.M', 'Direktur', 'Mengelola operasional BUMDes', NULL, 2, NOW(), NOW()),
(3, 'Agus Indra Prasetyo', 'Sekretaris', 'Mengelola administrasi dan dokumentasi', NULL, 3, NOW(), NOW()),
(4, 'Mohammad Murti Sudiyo', 'Bendahara', 'Mengelola keuangan BUMDes', NULL, 4, NOW(), NOW());
```

### UNIT_USAHA
**Fungsi**: Menyimpan data unit bisnis/usaha BUMDes

| Field | Type | Nullable | Default | Description |
|-------|------|----------|---------|-------------|
| id | INT | NO | AUTO_INCREMENT | Primary Key |
| nama | VARCHAR(100) | NO | - | Nama unit usaha |
| deskripsi | TEXT | YES | NULL | Deskripsi unit |
| icon | VARCHAR(255) | YES | NULL | Icon/emoji filename |
| gambar | VARCHAR(255) | YES | NULL | Gambar filename |
| urutan | INT | YES | 1 | Urutan tampilan |
| status | ENUM | YES | aktif | Status (aktif/nonaktif) |
| created_at | TIMESTAMP | NO | CURRENT_TIMESTAMP | Record creation |
| updated_at | TIMESTAMP | NO | CURRENT_TIMESTAMP ON UPDATE | Last modification |

**Sample Data**:
```sql
INSERT INTO unit_usaha VALUES 
(1, 'GOR Sugihwaras', 'Gedung Olahraga...', NULL, NULL, 1, 'aktif', NOW(), NOW()),
(2, 'Rental Tenda', 'Sewa tenda untuk acara...', NULL, NULL, 2, 'aktif', NOW(), NOW()),
-- ... dll
```

### RESERVASI
**Fungsi**: Menyimpan data reservasi/booking dari customer

| Field | Type | Nullable | Default | Description |
|-------|------|----------|---------|-------------|
| id | INT | NO | AUTO_INCREMENT | Primary Key |
| nama | VARCHAR(100) | NO | - | Nama customer |
| no_hp | VARCHAR(15) | NO | - | Nomor telepon |
| tanggal | DATE | NO | - | Tanggal reservasi |
| unit_usaha_id | INT | NO | - | FK ke unit_usaha |
| keterangan | TEXT | YES | NULL | Catatan tambahan |
| status | ENUM | YES | pending | Status (pending/confirmed/completed/cancelled) |
| created_at | TIMESTAMP | NO | CURRENT_TIMESTAMP | Record creation |
| updated_at | TIMESTAMP | NO | CURRENT_TIMESTAMP ON UPDATE | Last modification |

**Status Values**:
- `pending` - Menunggu konfirmasi
- `confirmed` - Sudah dikonfirmasi
- `completed` - Sudah selesai/berakhir
- `cancelled` - Dibatalkan

### LAYANAN
**Fungsi**: Menyimpan data layanan/paket per unit usaha

| Field | Type | Nullable | Default | Description |
|-------|------|----------|---------|-------------|
| id | INT | NO | AUTO_INCREMENT | Primary Key |
| unit_usaha_id | INT | NO | - | FK ke unit_usaha |
| nama | VARCHAR(100) | NO | - | Nama layanan |
| harga | DECIMAL(10,2) | YES | NULL | Harga layanan |
| deskripsi | TEXT | YES | NULL | Deskripsi layanan |
| status | ENUM | YES | aktif | Status (aktif/nonaktif) |
| created_at | TIMESTAMP | NO | CURRENT_TIMESTAMP | Record creation |
| updated_at | TIMESTAMP | NO | CURRENT_TIMESTAMP ON UPDATE | Last modification |

### KONTAK
**Fungsi**: Menyimpan informasi kontak BUMDes (singleton table)

| Field | Type | Nullable | Default | Description |
|-------|------|----------|---------|-------------|
| id | INT | NO | AUTO_INCREMENT | Primary Key |
| alamat | TEXT | NO | - | Alamat lengkap |
| telepon | VARCHAR(15) | NO | - | Nomor telepon |
| whatsapp | VARCHAR(15) | YES | NULL | Nomor WhatsApp |
| email | VARCHAR(100) | YES | NULL | Email address |
| facebook | VARCHAR(100) | YES | NULL | Facebook username |
| instagram | VARCHAR(100) | YES | NULL | Instagram username |
| instagram_url | VARCHAR(255) | YES | NULL | Instagram URL |
| created_at | TIMESTAMP | NO | CURRENT_TIMESTAMP | Record creation |
| updated_at | TIMESTAMP | NO | CURRENT_TIMESTAMP ON UPDATE | Last modification |

### ADMIN_USER
**Fungsi**: Menyimpan data admin untuk authentication

| Field | Type | Nullable | Default | Description |
|-------|------|----------|---------|-------------|
| id | INT | NO | AUTO_INCREMENT | Primary Key |
| username | VARCHAR(50) | NO | - | Username (UNIQUE) |
| password | VARCHAR(255) | NO | - | Password (SHA256) |
| email | VARCHAR(100) | YES | NULL | Email admin |
| nama_lengkap | VARCHAR(100) | YES | NULL | Nama lengkap |
| role | ENUM | YES | admin | Role (admin/moderator) |
| status | ENUM | YES | aktif | Status (aktif/nonaktif) |
| created_at | TIMESTAMP | NO | CURRENT_TIMESTAMP | Record creation |
| updated_at | TIMESTAMP | NO | CURRENT_TIMESTAMP ON UPDATE | Last modification |

**Default User**:
```sql
INSERT INTO admin_user VALUES 
(1, 'admin', SHA2('admin123', 256), 'admin@bumdes.id', 'Administrator', 'admin', 'aktif', NOW(), NOW());
```

## Query Examples

### Get all leadership ordered by position
```sql
SELECT * FROM pimpinan ORDER BY urutan ASC;
```

### Get all active business units
```sql
SELECT * FROM unit_usaha WHERE status = 'aktif' ORDER BY urutan;
```

### Get pending reservations
```sql
SELECT r.*, u.nama as unit_name 
FROM reservasi r 
LEFT JOIN unit_usaha u ON r.unit_usaha_id = u.id 
WHERE r.status = 'pending' 
ORDER BY r.created_at DESC;
```

### Get services for a specific unit
```sql
SELECT * FROM layanan 
WHERE unit_usaha_id = 1 AND status = 'aktif';
```

### Count reservations by status
```sql
SELECT status, COUNT(*) as total 
FROM reservasi 
GROUP BY status;
```

### Update reservation status
```sql
UPDATE reservasi 
SET status = 'confirmed', updated_at = NOW() 
WHERE id = 1;
```

## Indexes

**Recommended Indexes** untuk performance:

```sql
CREATE INDEX idx_pimpinan_urutan ON pimpinan(urutan);
CREATE INDEX idx_unit_usaha_status ON unit_usaha(status);
CREATE INDEX idx_reservasi_status ON reservasi(status);
CREATE INDEX idx_reservasi_unit ON reservasi(unit_usaha_id);
CREATE INDEX idx_reservasi_tanggal ON reservasi(tanggal);
CREATE INDEX idx_layanan_unit ON layanan(unit_usaha_id);
CREATE INDEX idx_admin_username ON admin_user(username);
```

## Data Validation Rules

### PIMPINAN
- nama: NOT NULL, MIN 3, MAX 100
- posisi: NOT NULL, MIN 3, MAX 100
- urutan: INT MIN 1

### UNIT_USAHA
- nama: NOT NULL, MIN 3, MAX 100
- status: ENUM ('aktif', 'nonaktif')
- urutan: INT MIN 1

### RESERVASI
- nama: NOT NULL, MIN 3, MAX 100
- no_hp: NOT NULL, Format Indonesia (08xx/+62xx)
- tanggal: NOT NULL, MIN today
- unit_usaha_id: NOT NULL, Must exist
- status: ENUM ('pending', 'confirmed', 'completed', 'cancelled')

### ADMIN_USER
- username: NOT NULL, UNIQUE, MIN 3, MAX 50
- password: NOT NULL, MIN 6 (stored as SHA256 hash)
- email: Valid email format
- role: ENUM ('admin', 'moderator')

## Backup & Recovery

### Backup Database
```bash
mysqldump -u root -p bumdes_db > bumdes_backup.sql
```

### Restore Database
```bash
mysql -u root -p bumdes_db < bumdes_backup.sql
```

### Export Data
```bash
mysqldump -u root -p bumdes_db --no-create-info > bumdes_data.sql
```

═════════════════════════════════════════════════════════

For more information, see:
- database/init.sql (Complete SQL schema)
- includes/functions.php (Database functions)
- api/get_data.php (Query examples)

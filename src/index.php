<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BUMDes Sukses Bersama - Desa Sugihwaras</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Header & Navigation -->
    <header class="header">
        <nav class="navbar">
            <div class="container">
                <div class="navbar-brand">
                    <div class="logo">
                        <h1>BUMDes Sukses Bersama</h1>
                        <p class="tagline-small">Desa Sugihwaras</p>
                    </div>
                </div>

                <button class="menu-toggle" id="menuToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

                <ul class="nav-menu" id="navMenu">
                    <li><a href="#home" class="nav-link">Home</a></li>
                    <li><a href="#unit-usaha" class="nav-link">Unit Usaha</a></li>
                    <li><a href="#service" class="nav-link">Service</a></li>
                    <li><a href="#laporan" class="nav-link">Laporan Keuangan</a></li>
                    <li><a href="#kontak" class="nav-link">Kontak</a></li>
                </ul>

                <div class="nav-icons">
                    <a href="tel:0877-5813-5806" class="icon-btn">☎</a>
                    <a href="https://wa.me/6287758135806" class="icon-btn">💬</a>
                    <a href="mailto:bumdes@sugihwaras.id" class="icon-btn">✉</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-content">
            <h1>Selamat Datang di</h1>
            <h2>BUMDes Sukses Bersama</h2>
            <p class="hero-subtitle">Desa Sugihwaras</p>
            <p class="hero-tagline">Membangun Ekonomi Desa, Memberdayakan Masyarakat</p>
            <button class="btn btn-primary" onclick="document.getElementById('pimpinan').scrollIntoView({behavior: 'smooth'})">Profil Selengkapnya</button>
        </div>
        <div class="hero-image">
            <div class="placeholder-image">BG Image</div>
        </div>
    </section>

    <!-- Profil Pimpinan -->
    <section id="pimpinan" class="profil-pimpinan">
        <div class="container">
            <h2 class="section-title">Profil Pimpinan</h2>
            <div class="pimpinan-grid" id="pimpinantGrid">
                <!-- Data will be loaded by JavaScript -->
                <div class="loading">Memuat data...</div>
            </div>
        </div>
    </section>

    <!-- Unit Usaha -->
    <section id="unit-usaha" class="unit-usaha">
        <div class="container">
            <h2 class="section-title">Unit Usaha Kami</h2>
            <div class="unit-grid" id="unitGrid">
                <!-- Data will be loaded by JavaScript -->
                <div class="loading">Memuat data...</div>
            </div>
        </div>
    </section>

    <!-- Reservasi Online -->
    <section id="service" class="reservasi">
        <div class="container">
            <h2 class="section-title">Reservasi Online</h2>
            <p class="section-subtitle">Pesan GOR & Tenda Pasar Dengan Mudah</p>

            <div class="reservasi-content">
                <div class="reservasi-form-wrapper">
                    <form id="formReservasi" class="reservasi-form">
                        <div class="form-group">
                            <label for="nama">Nama</label>
                            <input type="text" id="nama" name="nama" placeholder="Masukkan nama Anda" required>
                        </div>

                        <div class="form-group">
                            <label for="no_hp">No. HP</label>
                            <input type="tel" id="no_hp" name="no_hp" placeholder="Masukkan nomor telepon" required>
                        </div>

                        <div class="form-group">
                            <label for="tanggal">Tanggal</label>
                            <input type="date" id="tanggal" name="tanggal" required>
                        </div>

                        <div class="form-group">
                            <label for="unit_usaha_id">Pilih Layanan</label>
                            <select id="unit_usaha_id" name="unit_usaha_id" required>
                                <option value="">-- Pilih Unit Usaha --</option>
                                <!-- Options will be loaded by JavaScript -->
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="keterangan">Keterangan (Opsional)</label>
                            <textarea id="keterangan" name="keterangan" placeholder="Masukkan keterangan tambahan" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Pesan Sekarang</button>
                    </form>

                    <div id="responseMessage" class="response-message" style="display: none;"></div>
                </div>

                <div class="reservasi-image">
                    <div class="placeholder-image">Gambar Reservasi</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Laporan Keuangan -->
    <section id="laporan" class="laporan">
        <div class="container">
            <h2 class="section-title">Laporan Keuangan</h2>
            <p class="section-subtitle">Transparansi Keuangan BUMDes Sukses Bersama</p>

            <div class="laporan-content">
                <div class="laporan-item">
                    <h3>Tahun 2025</h3>
                    <p>Total Pendapatan: <strong>Rp 0,00</strong></p>
                    <p>Total Pengeluaran: <strong>Rp 0,00</strong></p>
                    <a href="#" class="btn btn-outline">Lihat Detail</a>
                </div>

                <div class="laporan-item">
                    <h3>Tahun 2024</h3>
                    <p>Total Pendapatan: <strong>Rp 0,00</strong></p>
                    <p>Total Pengeluaran: <strong>Rp 0,00</strong></p>
                    <a href="#" class="btn btn-outline">Lihat Detail</a>
                </div>

                <div class="laporan-item">
                    <h3>Tahun 2023</h3>
                    <p>Total Pendapatan: <strong>Rp 0,00</strong></p>
                    <p>Total Pengeluaran: <strong>Rp 0,00</strong></p>
                    <a href="#" class="btn btn-outline">Lihat Detail</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Kontak -->
    <section id="kontak" class="kontak">
        <div class="container">
            <h2 class="section-title">Kontak Kami</h2>

            <div class="kontak-content">
                <div class="kontak-info">
                    <div class="info-item">
                        <h3>📍 Alamat</h3>
                        <p id="alamatText">Jl. H. Nur Sugihwaras, RT 11 / RW 03, Rejo, Candi, Sidoarjo, Jawa Timur 61271</p>
                    </div>

                    <div class="info-item">
                        <h3>📞 Telepon</h3>
                        <p><a href="tel:0877-5813-5806" id="teleponText">0877-5813-5806</a></p>
                    </div>

                    <div class="info-item">
                        <h3>💬 WhatsApp</h3>
                        <p><a href="https://wa.me/6287758135806" id="whatsappText" target="_blank">0877-5813-5806</a></p>
                    </div>

                    <div class="info-item">
                        <h3>📧 Email</h3>
                        <p><a href="mailto:bumdes@sugihwaras.id" id="emailText">bumdes@sugihwaras.id</a></p>
                    </div>

                    <div class="info-item">
                        <h3>Ikuti Kami</h3>
                        <div class="social-links">
                            <a href="https://facebook.com" target="_blank" class="social-icon">f</a>
                            <a href="https://wa.me/6287758135806" target="_blank" class="social-icon">w</a>
                            <a href="https://instagram.com" target="_blank" class="social-icon">📷</a>
                        </div>
                    </div>
                </div>

                <div class="kontak-map">
                    <div class="placeholder-image">Google Maps / Lokasi</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 BUMDes Sukses Bersama. Semua hak dilindungi.</p>
            <p class="footer-credit">Desa Sugihwaras, Candi, Sidoarjo, Jawa Timur</p>
        </div>
    </footer>

    <script src="../assets/js/script.js"></script>
</body>
</html></content>
<parameter name="filePath">/workspaces/web-pbl-S2/src/index.php
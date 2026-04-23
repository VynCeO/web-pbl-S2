<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BUMDes Sukses Bersama Desa Sugihwaras</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
  </head>

  <body>
    <!-- Loader -->
    <div id="loader">
      <div class="spinner"></div>
    </div>

    <!-- Navbar -->
    <div class="navbar">
      <div class="nav-left">
        <img src="https://ui-avatars.com/api/?name=BUMDES+Sugihwaras&background=1f5b3a&color=fff&size=40" alt="Logo" width="40" style="border-radius: 50%;">
        <b>BUMDES</b>
      </div>

      <div class="hamburger" onclick="toggleMenu()">
        ☰
      </div>

      <div class="nav-right" id="navMenu">
        <div class="menu"><a href="#home">Home</a></div>

        <div class="menu dropdown">
          Unit Usaha
          <div class="dropdown-content">
            <a href="#unit-usaha">Semua Layanan</a>
            <a href="#" onclick="filterUnit('GOR')">GOR Sugihwaras</a>
            <a href="#" onclick="filterUnit('Tenda')">Rental Tenda</a>
            <a href="#" onclick="filterUnit('Air')">Air Minum</a>
            <a href="#" onclick="filterUnit('Kopi')">Kopi Melek</a>
            <a href="#" onclick="filterUnit('Ternak')">Peternakan</a>
            <a href="#" onclick="filterUnit('PBB')">Pembayaran PBB</a>
          </div>
        </div>

        <div class="menu"><a href="#service">Service</a></div>
        <div class="menu"><a href="#kontak">Kontak</a></div>

        <div class="btn-masuk" onclick="goToLogin()">Masuk</div>
      </div>
    </div>

    <!-- Hero Section -->
    <div class="hero">
      <div class="hero-text">
        <p class="hero-kecil">Selamat Datang di</p>
        <h1>BUMDes Sukses Bersama</h1>
        <p class="hero-kecil">Desa Sugihwaras</p>
        <a href="#unit-usaha" class="hero-btn">Jelajahi Layanan</a>
      </div>
    </div>

    <!-- Profil Pimpinan -->
    <section class="pimpinan reveal">
      <h2>Profil Pimpinan</h2>
      <div class="pimpinan-container" id="pimpinanContainer">
        <!-- Data akan dimuat dari API -->
        <div style="text-align: center; padding: 20px;">Memuat data...</div>
      </div>
    </section>

    <!-- Unit Usaha -->
    <section class="unit-usaha reveal" id="unit-usaha">
      <h2>Unit Usaha Kami</h2>
      <div class="usaha-container" id="unitContainer">
        <!-- Data akan dimuat dari API -->
        <div style="text-align: center; padding: 20px; width: 100%;">Memuat data...</div>
      </div>
    </section>

    <!-- Reservasi -->
    <section class="reservasi reveal" id="service">
      <div class="reservasi-container">
        <!-- FORM -->
        <div class="reservasi-form">
          <h2>Reservasi Online</h2>
          <p>Pesan GOR & Tenda Dengan Mudah</p>

          <form id="reservasiForm">
            <input type="text" id="nama" placeholder="Nama" required />
            <input type="text" id="phone" placeholder="No. HP" required />
            <label>Tanggal Mulai Sewa</label>
            <input type="date" id="tanggal" required />
            <label>Tanggal Pengembalian</label>
            <input type="date" id="tanggal_kembali" required />
            <div id="bookedDates" style="font-size: 12px; color: #666; margin-top: 8px; padding: 10px; background: #f0f0f0; border-radius: 4px;">
              <strong>Tanggal yang sudah dipesan:</strong>
              <div id="bookedDatesList">Memuat data...</div>
            </div>
            <select id="layanan" required>
              <option>Pilih Layanan</option>
              <option>Sewa GOR</option>
              <option>Rental Tenda</option>
            </select>
            <button type="submit" class="btn-reserve">Pesan Sekarang</button>
            <div id="reservasiResponse"></div>
          </form>
        </div>

        <!-- GAMBAR -->
        <div class="reservasi-gambar">
          <img src="../assets/images/reservasi.jpg" alt="Reservasi" />
        </div>
      </div>
    </section>

    <!-- Kontak -->
    <section class="kontak reveal" id="kontak">
      <div class="kontak-container">
        <div class="kontak-kiri">
          <h2>Kontak Kami</h2>
          <p>📍 Jl. H. Nur Sugihwaras, RT 11 / RW 03, Rejo, Candi, Sidoarjo</p>
          <p>📞 Telp: 0877-5813-5806</p>
          <p>🟢 WhatsApp: 0877-5813-5806</p>
        </div>

        <div class="kontak-kanan">
          <a href="https://www.facebook.com/login" target="_blank">
            <img src="../assets/images/facebook.png" alt="Facebook" />
          </a>
          <a href="https://wa.me/6287758135806" target="_blank">
            <img src="../assets/images/whatsapp.png" alt="Whatsapp" />
          </a>
          <a href="https://www.instagram.com/bumdes.sugihwaras19" target="_blank">
            <img src="../assets/images/instagram.png" alt="Instagram" />
          </a>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
      <p>© 2026 BUMDes Sukses Bersama - Desa Sugihwaras</p>
    </footer>

    <!-- Scripts -->
    <script>
      function toggleMenu() {
        var menu = document.getElementById("navMenu");
        menu.classList.toggle("active");
      }

      function reveal() {
        var reveals = document.querySelectorAll(".reveal");
        for(var i = 0; i < reveals.length; i++){
          var windowHeight = window.innerHeight;
          var elementTop = reveals[i].getBoundingClientRect().top;
          var elementVisible = 150;
          if(elementTop < windowHeight - elementVisible){
            reveals[i].classList.add("active");
          }
        }
      }

      window.addEventListener("scroll", reveal);

      window.addEventListener("load", function(){
        document.getElementById("loader").style.display = "none";
        loadPimpinan();
        loadUnitUsaha();
        loadLaporan();
        setupFormSubmission();
      });

      function goToLogin() {
        window.location.href = '../admin/login.php';
      }

      function filterUnit(type) {
        // Filter unit berdasarkan tipe (akan diimplementasikan)
        loadUnitUsaha(type);
      }
    </script>

    <!-- Load data from API -->
    <script src="../assets/js/script.js"></script>
  </body>
</html>

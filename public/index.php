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
          Produk
          <div class="dropdown-content">
            <a href="produk.php">Semua Produk</a>
            <a href="produk.php#GOR" onclick="filterUnit('GOR')">GOR Sugihwaras</a>
            <a href="produk.php#Tenda" onclick="filterUnit('Tenda')">Rental Tenda</a>
            <a href="produk.php#Air" onclick="filterUnit('Air')">Air Minum</a>
            <a href="produk.php#Kopi" onclick="filterUnit('Kopi')">Kopi Melek</a>
            <a href="produk.php#Ternak" onclick="filterUnit('Ternak')">Peternakan</a>
            <a href="produk.php#PBB" onclick="filterUnit('PBB')">Pembayaran PBB</a>
          </div>
        </div>

        <div class="menu"><a href="reservasi.php">Reservasi</a></div>
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

    <!-- Unit Usaha Section Removed - Moved to produk.php -->

    <!-- Reservasi Section Removed - Moved to reservasi.php -->

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
      });

      function goToLogin() {
        window.location.href = '/admin/login.php';
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

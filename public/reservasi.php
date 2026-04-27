<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservasi | BUMDes Sukses Bersama</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <style>
      .reservasi-page {
        min-height: 100vh;
        padding: 2rem;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      }

      .reservasi-page-container {
        max-width: 1200px;
        margin: 0 auto;
        background: white;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      }

      .reservasi-page h1 {
        text-align: center;
        color: #2d5016;
        margin-bottom: 1rem;
        font-size: 2.5rem;
      }

      .reservasi-page-subtitle {
        text-align: center;
        color: #666;
        margin-bottom: 2rem;
        font-size: 1.1rem;
      }

      .reservasi-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-top: 2rem;
      }

      .reservasi-form {
        padding: 1.5rem;
        background: #f9f9f9;
        border-radius: 8px;
      }

      .reservasi-form h2 {
        color: #2d5016;
        margin-bottom: 1rem;
      }

      .reservasi-form input,
      .reservasi-form select,
      .reservasi-form label {
        width: 100%;
        padding: 0.75rem;
        margin: 0.5rem 0;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-family: inherit;
      }

      .reservasi-form button {
        width: 100%;
        padding: 0.75rem;
        background: #2d5016;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1rem;
        margin-top: 1rem;
        transition: background 0.3s;
      }

      .reservasi-form button:hover {
        background: #1e3a0f;
      }

      .reservasi-gambar {
        display: flex;
        align-items: center;
        justify-content: center;
      }

      .reservasi-gambar img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      }

      .booking-list {
        margin-top: 2rem;
        padding: 1.5rem;
        background: white;
        border-radius: 8px;
        border-left: 4px solid #ff9500;
      }

      .booking-list h2 {
        color: #2d5016;
        margin-bottom: 1rem;
      }

      .booking-item {
        padding: 1rem;
        background: #f5f5f5;
        border-radius: 4px;
        margin-bottom: 1rem;
        border-left: 3px solid #ff9500;
      }

      .booking-item .booking-date {
        color: #666;
        font-size: 0.9rem;
      }

      .booking-item .booking-layanan {
        color: #2d5016;
        font-weight: bold;
        margin-top: 0.5rem;
      }

      #bookedDates {
        font-size: 12px;
        color: #666;
        margin-top: 8px;
        padding: 10px;
        background: #f0f0f0;
        border-radius: 4px;
      }

      @media (max-width: 768px) {
        .reservasi-content {
          grid-template-columns: 1fr;
        }

        .reservasi-page h1 {
          font-size: 1.8rem;
        }

        .reservasi-page-container {
          padding: 1rem;
        }

        .reservasi-page {
          padding: 1rem;
        }
      }
    </style>
  </head>

  <body>
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
        <div class="menu"><a href="index.php#home">Home</a></div>

        <div class="menu dropdown">
          Produk
          <div class="dropdown-content">
            <a href="produk.php">Semua Produk</a>
            <a href="produk.php#GOR">GOR Sugihwaras</a>
            <a href="produk.php#Tenda">Rental Tenda</a>
            <a href="produk.php#Air">Air Minum</a>
            <a href="produk.php#Kopi">Kopi Melek</a>
            <a href="produk.php#Ternak">Peternakan</a>
            <a href="produk.php#PBB">Pembayaran PBB</a>
          </div>
        </div>

        <div class="menu"><a href="reservasi.php">Reservasi</a></div>
        <div class="menu"><a href="index.php#kontak">Kontak</a></div>

        <div class="btn-masuk" onclick="goToLogin()">Masuk</div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="reservasi-page">
      <div class="reservasi-page-container">
        <h1>🎫 Reservasi Online</h1>
        <p class="reservasi-page-subtitle">Pesan GOR & Tenda Dengan Mudah</p>

        <div class="reservasi-content">
          <!-- FORM -->
          <div class="reservasi-form">
            <h2>Form Pemesanan</h2>

            <form id="reservasiForm">
              <input type="text" id="nama" placeholder="Nama Lengkap" required />
              <input type="email" id="email" placeholder="Email" required />
              <input type="text" id="phone" placeholder="No. HP" required />
              
              <label for="layanan">Pilih Layanan</label>
              <select id="layanan" required>
                <option value="">-- Pilih Layanan --</option>
                <option value="Sewa GOR">Sewa GOR</option>
                <option value="Rental Tenda">Rental Tenda</option>
              </select>

              <label for="tanggal">Tanggal Mulai Sewa</label>
              <input type="date" id="tanggal" required />
              
              <label for="tanggal_kembali">Tanggal Pengembalian</label>
              <input type="date" id="tanggal_kembali" required />

              <div id="bookedDates">
                <strong>⚠️ Tanggal yang sudah dipesan:</strong>
                <div id="bookedDatesList" style="margin-top: 8px;">Memuat data...</div>
              </div>

              <button type="submit" class="btn-reserve">Pesan Sekarang</button>
              <div id="reservasiResponse"></div>
            </form>
          </div>

          <!-- GAMBAR -->
          <div class="reservasi-gambar">
            <img src="../assets/images/reservasi.jpg" alt="Reservasi Facilities" />
          </div>
        </div>

        <!-- Daftar Pemesanan -->
        <div class="booking-list">
          <h2>📋 Pemesanan Terbaru</h2>
          <div id="bookingListContainer">
            <p style="text-align: center; color: #999;">Memuat data...</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
      <p>© 2026 BUMDes Sukses Bersama - Desa Sugihwaras</p>
    </footer>

    <script>
      function toggleMenu() {
        var menu = document.getElementById("navMenu");
        menu.classList.toggle("active");
      }

      function goToLogin() {
        window.location.href = '/admin/login.php';
      }

      // Load booked dates
      function loadBookedDates() {
        fetch('../api/get_data.php?type=booked-dates')
          .then(response => response.json())
          .then(data => {
            const datesList = document.getElementById('bookedDatesList');
            if (data.dates && data.dates.length > 0) {
              datesList.innerHTML = data.dates.map(d => `<span style="display: inline-block; margin-right: 5px; padding: 3px 8px; background: #ffcccc; border-radius: 3px;">${d}</span>`).join('');
            } else {
              datesList.innerHTML = '<span style="color: #999;">Semua tanggal tersedia</span>';
            }
          })
          .catch(err => console.error('Error loading booked dates:', err));
      }

      // Load booking list
      function loadBookingList() {
        fetch('../api/get_data.php?type=reservasi')
          .then(response => response.json())
          .then(data => {
            const container = document.getElementById('bookingListContainer');
            if (data.length > 0) {
              container.innerHTML = data.slice(0, 10).map(booking => `
                <div class="booking-item">
                  <div class="booking-date">📅 ${booking.tanggal} s/d ${booking.tanggal_kembali}</div>
                  <div class="booking-layanan">${booking.layanan}</div>
                  <div style="color: #999; font-size: 0.85rem;">Atas nama: ${booking.nama}</div>
                </div>
              `).join('');
            } else {
              container.innerHTML = '<p style="text-align: center; color: #999;">Belum ada pemesanan</p>';
            }
          })
          .catch(err => console.error('Error loading booking list:', err));
      }

      // Form submission
      function setupFormSubmission() {
        const form = document.getElementById('reservasiForm');
        if (form) {
          form.addEventListener('submit', function(e) {
            e.preventDefault();

            const data = {
              nama: document.getElementById('nama').value,
              email: document.getElementById('email').value,
              phone: document.getElementById('phone').value,
              layanan: document.getElementById('layanan').value,
              tanggal: document.getElementById('tanggal').value,
              tanggal_kembali: document.getElementById('tanggal_kembali').value
            };

            fetch('../api/get_data.php?type=create-reservasi', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
              const responseDiv = document.getElementById('reservasiResponse');
              if (result.success) {
                responseDiv.innerHTML = '<p style="color: green; font-weight: bold;">✅ Pemesanan berhasil! Kami akan menghubungi Anda segera.</p>';
                form.reset();
                loadBookedDates();
                loadBookingList();
                setTimeout(() => responseDiv.innerHTML = '', 5000);
              } else {
                responseDiv.innerHTML = `<p style="color: red; font-weight: bold;">❌ ${result.message || 'Pemesanan gagal'}</p>`;
              }
            })
            .catch(err => {
              document.getElementById('reservasiResponse').innerHTML = '<p style="color: red;">Terjadi kesalahan. Coba lagi.</p>';
              console.error('Error:', err);
            });
          });
        }
      }

      window.addEventListener("load", function(){
        loadBookedDates();
        loadBookingList();
        setupFormSubmission();
      });
    </script>
  </body>
</html>

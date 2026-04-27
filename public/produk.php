<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk | BUMDes Sukses Bersama</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <style>
      .produk-page {
        min-height: 100vh;
        padding: 2rem 1rem;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      }

      .produk-container {
        max-width: 1200px;
        margin: 0 auto;
      }

      .produk-header {
        text-align: center;
        margin-bottom: 3rem;
      }

      .produk-header h1 {
        color: #2d5016;
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
      }

      .produk-header p {
        color: #666;
        font-size: 1.1rem;
      }

      .produk-filter {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
      }

      .filter-btn {
        padding: 0.5rem 1rem;
        border: 2px solid #2d5016;
        background: white;
        color: #2d5016;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.3s;
        font-weight: bold;
      }

      .filter-btn:hover,
      .filter-btn.active {
        background: #2d5016;
        color: white;
      }

      .produk-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 2rem;
        margin-bottom: 3rem;
      }

      .produk-card {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s, box-shadow 0.3s;
      }

      .produk-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
      }

      .produk-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        background: #f0f0f0;
      }

      .produk-content {
        padding: 1.5rem;
      }

      .produk-category {
        display: inline-block;
        background: #ff9500;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        margin-bottom: 0.5rem;
      }

      .produk-name {
        font-size: 1.3rem;
        color: #2d5016;
        margin: 0.5rem 0;
        font-weight: bold;
      }

      .produk-desc {
        color: #666;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 1rem;
      }

      .produk-variasi {
        background: #f9f9f9;
        padding: 1rem;
        border-radius: 4px;
        margin-bottom: 1rem;
      }

      .variasi-title {
        font-weight: bold;
        color: #2d5016;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
      }

      .variasi-item {
        padding: 0.5rem 0;
        font-size: 0.85rem;
        color: #555;
        display: flex;
        justify-content: space-between;
        border-bottom: 1px solid #eee;
      }

      .variasi-item:last-child {
        border-bottom: none;
      }

      .produk-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
      }

      .produk-price {
        font-size: 1.2rem;
        color: #ff9500;
        font-weight: bold;
      }

      .produk-btn {
        padding: 0.5rem 1rem;
        background: #2d5016;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background 0.3s;
      }

      .produk-btn:hover {
        background: #1e3a0f;
      }

      .detail-section {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        margin-bottom: 2rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      }

      .detail-title {
        font-size: 1.8rem;
        color: #2d5016;
        margin-bottom: 1rem;
        border-bottom: 3px solid #ff9500;
        padding-bottom: 0.5rem;
      }

      .detail-content {
        color: #555;
        line-height: 1.8;
      }

      .detail-list {
        list-style: none;
        padding: 0;
      }

      .detail-list li {
        padding: 0.75rem 0;
        padding-left: 1.5rem;
        position: relative;
      }

      .detail-list li:before {
        content: "✓";
        position: absolute;
        left: 0;
        color: #ff9500;
        font-weight: bold;
      }

      @media (max-width: 768px) {
        .produk-grid {
          grid-template-columns: 1fr;
        }

        .produk-header h1 {
          font-size: 1.8rem;
        }

        .produk-page {
          padding: 1rem;
        }

        .detail-section {
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
            <a href="#GOR" onclick="filterUnit('GOR')">GOR Sugihwaras</a>
            <a href="#Tenda" onclick="filterUnit('Tenda')">Rental Tenda</a>
            <a href="#Air" onclick="filterUnit('Air')">Air Minum</a>
            <a href="#Kopi" onclick="filterUnit('Kopi')">Kopi Melek</a>
            <a href="#Ternak" onclick="filterUnit('Ternak')">Peternakan</a>
            <a href="#PBB" onclick="filterUnit('PBB')">Pembayaran PBB</a>
          </div>
        </div>

        <div class="menu"><a href="reservasi.php">Reservasi</a></div>
        <div class="menu"><a href="index.php#kontak">Kontak</a></div>

        <div class="btn-masuk" onclick="goToLogin()">Masuk</div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="produk-page">
      <div class="produk-container">
        <div class="produk-header">
          <h1>🏪 Produk & Layanan Kami</h1>
          <p>Berbagai pilihan berkualitas untuk kebutuhan Anda</p>
        </div>

        <!-- Filter -->
        <div class="produk-filter">
          <button class="filter-btn active" onclick="filterUnit('ALL')">Semua Produk</button>
          <button class="filter-btn" onclick="filterUnit('GOR')">GOR Sugihwaras</button>
          <button class="filter-btn" onclick="filterUnit('Tenda')">Rental Tenda</button>
          <button class="filter-btn" onclick="filterUnit('Air')">Air Minum</button>
          <button class="filter-btn" onclick="filterUnit('Kopi')">Kopi Melek</button>
          <button class="filter-btn" onclick="filterUnit('Ternak')">Peternakan</button>
          <button class="filter-btn" onclick="filterUnit('PBB')">Pembayaran PBB</button>
        </div>

        <!-- Produk Grid -->
        <div class="produk-grid" id="produkContainer">
          <div style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: #999;">Memuat data produk...</div>
        </div>

        <!-- Detail Sections -->
        <div id="detailSections"></div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
      <p>© 2026 BUMDes Sukses Bersama - Desa Sugihwaras</p>
    </footer>

    <script>
      let allProducts = [];
      let currentFilter = 'ALL';

      function toggleMenu() {
        var menu = document.getElementById("navMenu");
        menu.classList.toggle("active");
      }

      function goToLogin() {
        window.location.href = '/admin/login.php';
      }

      // Load unit usaha dengan detail
      function loadUnitUsaha(filter = 'ALL') {
        currentFilter = filter;
        const filterBtns = document.querySelectorAll('.filter-btn');
        filterBtns.forEach(btn => {
          btn.classList.remove('active');
          if (btn.textContent.includes(filter === 'ALL' ? 'Semua' : filter)) {
            btn.classList.add('active');
          }
        });

        fetch('../api/get_data.php?type=unit-usaha')
          .then(response => response.json())
          .then(data => {
            allProducts = data;
            displayProducts(filter);
            displayDetailSections(data, filter);
          })
          .catch(err => {
            console.error('Error loading products:', err);
            document.getElementById('produkContainer').innerHTML = 
              '<div style="grid-column: 1 / -1; text-align: center; color: red;">Gagal memuat data produk</div>';
          });
      }

      // Display produk
      function displayProducts(filter) {
        const container = document.getElementById('produkContainer');
        
        const filtered = filter === 'ALL' 
          ? allProducts 
          : allProducts.filter(p => p.nama.includes(filter));

        if (filtered.length === 0) {
          container.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: #999;">Tidak ada produk untuk filter ini</div>';
          return;
        }

        container.innerHTML = filtered.map(product => {
          const variasi = product.variasi && product.variasi.length > 0;
          const minPrice = variasi 
            ? Math.min(...product.variasi.map(v => parseFloat(v.harga)))
            : 'Hubungi Kami';

          return `
            <div class="produk-card">
              <img src="../assets/images/produk/${product.foto || 'default.jpg'}" 
                   alt="${product.nama}" class="produk-image" 
                   onerror="this.src='../assets/images/default.jpg'">
              <div class="produk-content">
                <span class="produk-category">${product.nama.split(' ')[0]}</span>
                <h3 class="produk-name">${product.nama}</h3>
                <p class="produk-desc">${product.deskripsi || 'Produk berkualitas dari BUMDes kami'}</p>
                
                ${variasi ? `
                  <div class="produk-variasi">
                    <div class="variasi-title">Pilihan Tersedia:</div>
                    ${product.variasi.slice(0, 3).map(v => `
                      <div class="variasi-item">
                        <span>${v.nama}</span>
                        <span style="color: #ff9500; font-weight: bold;">Rp ${parseInt(v.harga).toLocaleString('id-ID')}</span>
                      </div>
                    `).join('')}
                    ${product.variasi.length > 3 ? `<div style="text-align: center; color: #999; font-size: 0.8rem; padding-top: 0.5rem;">+${product.variasi.length - 3} lainnya</div>` : ''}
                  </div>
                ` : ''}

                <div class="produk-footer">
                  <span class="produk-price">${typeof minPrice === 'number' ? 'Rp ' + minPrice.toLocaleString('id-ID') : minPrice}</span>
                  <button class="produk-btn" onclick="goToReservasi()">Pesan</button>
                </div>
              </div>
            </div>
          `;
        }).join('');
      }

      // Display detail sections
      function displayDetailSections(data, filter) {
        const container = document.getElementById('detailSections');
        const filtered = filter === 'ALL' 
          ? data 
          : data.filter(p => p.nama.includes(filter));

        if (filtered.length === 0) return;

        container.innerHTML = filtered.slice(0, 3).map(product => `
          <div class="detail-section" id="${product.nama.replace(/\s+/g, '')}">
            <h2 class="detail-title">📌 ${product.nama}</h2>
            <div class="detail-content">
              <p><strong>Deskripsi:</strong></p>
              <p>${product.deskripsi || 'Produk unggulan dari BUMDes Sukses Bersama'}</p>
              
              ${product.variasi && product.variasi.length > 0 ? `
                <p style="margin-top: 1rem;"><strong>Pilihan Harga:</strong></p>
                <ul class="detail-list">
                  ${product.variasi.map(v => `
                    <li>${v.nama} - Rp ${parseInt(v.harga).toLocaleString('id-ID')}</li>
                  `).join('')}
                </ul>
              ` : ''}

              <p style="margin-top: 1.5rem;">
                <button class="produk-btn" onclick="goToReservasi()" style="padding: 0.75rem 1.5rem; font-size: 1rem;">
                  Pesan Sekarang
                </button>
              </p>
            </div>
          </div>
        `).join('');
      }

      function filterUnit(type) {
        loadUnitUsaha(type);
      }

      function goToReservasi() {
        window.location.href = 'reservasi.php';
      }

      window.addEventListener("load", function(){
        loadUnitUsaha();
      });
    </script>
  </body>
</html>

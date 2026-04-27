// ================================
// Global Configuration
// ================================

const API_BASE = '../api/get_data.php';
const BASE_URL = '../';

// ================================
// Utility Functions
// ================================

function showMessage(elementId, message, type = 'info') {
    const messageEl = document.getElementById(elementId);
    if (messageEl) {
        messageEl.className = `response-message ${type}`;
        messageEl.textContent = message;
        messageEl.style.display = 'block';

        if (type === 'success') {
            setTimeout(() => {
                messageEl.style.display = 'none';
            }, 5000);
        }
    }
}

function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('id-ID', options);
}

function formatRupiah(value) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(value);
}

// ================================
// API Calls with Error Handling
// ================================

async function fetchData(action, params = {}) {
    try {
        let url = `${API_BASE}?action=${action}`;
        for (let key in params) {
            url += `&${key}=${encodeURIComponent(params[key])}`;
        }

        const response = await fetch(url);
        if (!response.ok) throw new Error(`HTTP Error: ${response.status}`);
        return await response.json();
    } catch (error) {
        console.error('Fetch error:', error);
        return { success: false, message: 'Terjadi kesalahan saat memuat data' };
    }
}

// ================================
// Load Pimpinan Data
// ================================

async function loadPimpinan() {
    try {
        const result = await fetchData('get_pimpinan');
        const container = document.getElementById('pimpinanContainer');
        if (!container) return;

        if (result.success && result.data && result.data.length > 0) {
            container.innerHTML = result.data.map(pimpinan => `
                <div class="card">
                    <img 
                        src="${BASE_URL}assets/images/${pimpinan.foto || 'default-person.jpg'}" 
                        alt="${pimpinan.nama}" 
                        onerror="this.src='${BASE_URL}assets/images/default-person.jpg'"
                    />
                    <div class="card-nama">
                        <h3>${pimpinan.nama}</h3>
                        <p>${pimpinan.posisi || 'Staff'}</p>
                    </div>
                </div>
            `).join('');
        } else {
            container.innerHTML = '<p style="text-align: center; padding: 20px;">Belum ada data pimpinan</p>';
        }
    } catch (error) {
        console.error('Error loading pimpinan:', error);
    }
}

// ================================
// Load Unit Usaha (For produk.php)
// ================================

async function loadUnitUsaha(filterType = null) {
    try {
        const result = await fetchData('get_unit_usaha');
        const container = document.getElementById('unitContainer');
        if (!container) return;

        if (result.success && result.data && result.data.length > 0) {
            let data = result.data;
            
            if (filterType && filterType !== 'ALL') {
                data = data.filter(unit => unit.nama.includes(filterType));
            }

            container.innerHTML = data.length > 0 ? data.map(unit => `
                <div class="usaha-card-wrapper" data-unit-id="${unit.id}">
                    <div class="usaha-card" onclick="toggleVariasi(${unit.id})">
                        <img 
                            src="${BASE_URL}assets/images/${unit.gambar || 'default-unit.png'}" 
                            alt="${unit.nama}"
                            onerror="this.src='${BASE_URL}assets/images/default-unit.png'"
                        />
                        <div class="usaha-card-overlay">
                            <div class="usaha-card-content">
                                <p class="usaha-card-nama">${unit.nama}</p>
                                <p class="usaha-card-hint">Lihat Variasi & Harga</p>
                            </div>
                        </div>
                    </div>
                    <div class="variasi-panel" id="variasi-${unit.id}" style="display: none;">
                        <div class="variasi-header">
                            <h4>${unit.nama}</h4>
                            <button class="variasi-close" onclick="toggleVariasi(${unit.id})">&times;</button>
                        </div>
                        <div class="variasi-list" id="variasi-list-${unit.id}">
                            <p class="variasi-loading">Memuat variasi produk...</p>
                        </div>
                    </div>
                </div>
            `).join('') : '<p style="text-align: center; padding: 20px; width: 100%;">Tidak ada unit usaha untuk kategori ini</p>';
            
            data.forEach(unit => loadVariasiProduk(unit.id));
        } else {
            container.innerHTML = '<p style="text-align: center; padding: 20px; width: 100%;">Belum ada unit usaha</p>';
        }
    } catch (error) {
        console.error('Error loading unit usaha:', error);
    }
}

// ================================
// Load Variasi Produk
// ================================

async function loadVariasiProduk(unitId) {
    try {
        const result = await fetchData('get_variasi_produk', { unit_id: unitId });
        const variasisList = document.getElementById(`variasi-list-${unitId}`);
        if (!variasisList) return;

        if (result.success && result.data && result.data.length > 0) {
            variasisList.innerHTML = result.data.map(variasi => `
                <div class="variasi-item">
                    <div class="variasi-nama">${variasi.nama}</div>
                    <div class="variasi-harga">${formatRupiah(variasi.harga)}</div>
                    ${variasi.keterangan ? `<div class="variasi-keterangan">${variasi.keterangan}</div>` : ''}
                </div>
            `).join('');
        } else {
            variasisList.innerHTML = '<p style="text-align: center; padding: 10px;">Belum ada variasi produk</p>';
        }
    } catch (error) {
        console.error('Error loading variasi:', error);
        const variasisList = document.getElementById(`variasi-list-${unitId}`);
        if (variasisList) {
            variasisList.innerHTML = '<p style="text-align: center; padding: 10px; color: red;">Gagal memuat data</p>';
        }
    }
}

// ================================
// Toggle Variasi Panel
// ================================

function toggleVariasi(unitId) {
    const panel = document.getElementById(`variasi-${unitId}`);
    if (!panel) return;
    
    const isHidden = panel.style.display === 'none';
    document.querySelectorAll('.variasi-panel').forEach(p => {
        p.style.display = 'none';
    });
    
    if (isHidden) {
        panel.style.display = 'block';
    }
}

// ================================
// Mobile Menu Toggle
// ================================

function toggleMenu() {
    const menu = document.getElementById('navMenu');
    if (menu) {
        menu.classList.toggle('active');
    }
}

// Close menu when clicking on a link
document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('.nav-right a');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            const menu = document.getElementById('navMenu');
            if (menu) {
                menu.classList.remove('active');
            }
        });
    });
});

// ================================
// Reveal Animation on Scroll
// ================================

function reveal() {
    const reveals = document.querySelectorAll('.reveal');
    reveals.forEach(element => {
        const windowHeight = window.innerHeight;
        const elementTop = element.getBoundingClientRect().top;
        const elementVisible = 150;

        if (elementTop < windowHeight - elementVisible) {
            element.classList.add('active');
        }
    });
}

window.addEventListener('scroll', reveal);

// ================================
// Initialize on Page Load
// ================================

window.addEventListener('load', function() {
    const loader = document.getElementById('loader');
    if (loader) {
        loader.style.display = 'none';
    }

    // Load pimpinan data (for landing page)
    loadPimpinan();

    // Set minimum date for date input (for reservasi page)
    const dateInput = document.getElementById('tanggal');
    if (dateInput) {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        dateInput.min = `${year}-${month}-${day}`;
    }
});

// ================================
// Global Configuration
// ================================

const API_BASE = './api/get_data.php';
const BASE_URL = './';
let bookedDates = [];

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

// ================================
// API Calls
// ================================

async function fetchData(action, params = {}) {
    try {
        let url = `${API_BASE}?action=${action}`;
        for (let key in params) {
            url += `&${key}=${encodeURIComponent(params[key])}`;
        }

        const response = await fetch(url);
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

        if (result.success && result.data && result.data.length > 0) {
            container.innerHTML = result.data.map(pimpinan => `
                <div class="card">
                    <img src="${BASE_URL}assets/images/${pimpinan.foto || 'default-person.jpg'}" alt="${pimpinan.nama}" />
                    <div class="card-nama">
                        <h3>${pimpinan.nama}</h3>
                        <p>${pimpinan.posisi}</p>
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
// Load Unit Usaha Data
// ================================

async function loadUnitUsaha(filterType = null) {
    try {
        const result = await fetchData('get_unit_usaha');
        const container = document.getElementById('unitContainer');

        if (result.success && result.data && result.data.length > 0) {
            let data = result.data;
            
            // Filter jika ada parameter
            if (filterType) {
                data = data.filter(unit => unit.nama.includes(filterType));
            }

            container.innerHTML = data.map(unit => `
                <div class="usaha-card">
                    <img src="${BASE_URL}assets/images/${unit.gambar || 'default-unit.png'}" alt="${unit.nama}" />
                    <p>${unit.nama}</p>
                </div>
            `).join('');
        } else {
            container.innerHTML = '<p style="text-align: center; padding: 20px; width: 100%;">Belum ada unit usaha</p>';
        }
    } catch (error) {
        console.error('Error loading unit usaha:', error);
    }
}

// ================================
// Load Booked Dates
// ================================

async function loadBookedDates() {
    try {
        const result = await fetchData('get_reservasi');
        const container = document.getElementById('bookedDatesList');

        if (result.success && result.data && result.data.length > 0) {
            bookedDates = [];
            const datesList = result.data.map(reservasi => {
                const startDate = new Date(reservasi.tanggal);
                const endDate = reservasi.tanggal_kembali ? new Date(reservasi.tanggal_kembali) : startDate;
                
                // Add all dates between start and end to booked list
                for (let d = new Date(startDate); d <= endDate; d.setDate(d.getDate() + 1)) {
                    bookedDates.push(d.toISOString().split('T')[0]);
                }
                
                return `<span style="display: block; padding: 4px 0;">
                    📅 ${formatDate(reservasi.tanggal)} ${reservasi.tanggal_kembali ? `- ${formatDate(reservasi.tanggal_kembali)}` : ''} (${reservasi.nama})
                </span>`;
            }).join('');

            container.innerHTML = datesList || 'Tidak ada tanggal yang dipesan';
        } else {
            container.innerHTML = 'Semua tanggal tersedia';
            bookedDates = [];
        }

        // Update date input attributes
        updateDateInputs();
    } catch (error) {
        console.error('Error loading booked dates:', error);
    }
}

// ================================
// Update Date Input Constraints
// ================================

function updateDateInputs() {
    const tanggalInput = document.getElementById('tanggal');
    const tanggalKembaliInput = document.getElementById('tanggal_kembali');

    if (tanggalInput) {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        tanggalInput.min = `${year}-${month}-${day}`;

        // Disable booked dates
        tanggalInput.addEventListener('change', function() {
            const selectedDate = this.value;
            if (bookedDates.includes(selectedDate)) {
                showMessage('reservasiResponse', 'Tanggal ini sudah dipesan', 'error');
                this.value = '';
            }
            
            // Auto-set minimum return date
            if (tanggalKembaliInput) {
                const nextDay = new Date(selectedDate);
                nextDay.setDate(nextDay.getDate() + 1);
                const nextDayStr = nextDay.toISOString().split('T')[0];
                tanggalKembaliInput.min = nextDayStr;
                tanggalKembaliInput.value = nextDayStr;
            }
        });
    }

    if (tanggalKembaliInput) {
        tanggalKembaliInput.addEventListener('change', function() {
            const selectedDate = this.value;
            const tanggalMulai = document.getElementById('tanggal').value;

            if (selectedDate <= tanggalMulai) {
                showMessage('reservasiResponse', 'Tanggal pengembalian harus lebih besar dari tanggal mulai', 'error');
                this.value = '';
                return;
            }

            // Check if any date in range is booked
            const startDate = new Date(tanggalMulai);
            const endDate = new Date(selectedDate);
            let hasConflict = false;

            for (let d = new Date(startDate); d <= endDate; d.setDate(d.getDate() + 1)) {
                const dateStr = d.toISOString().split('T')[0];
                if (bookedDates.includes(dateStr)) {
                    hasConflict = true;
                    break;
                }
            }

            if (hasConflict) {
                showMessage('reservasiResponse', 'Beberapa tanggal dalam rentang ini sudah dipesan', 'error');
                this.value = '';
            }
        });
    }
}

// ================================
// Form Submission
// ================================

function setupFormSubmission() {
    const form = document.getElementById('reservasiForm');

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const data = {
                nama: document.getElementById('nama').value,
                no_hp: document.getElementById('phone').value,
                tanggal: document.getElementById('tanggal').value,
                tanggal_kembali: document.getElementById('tanggal_kembali').value,
                layanan: document.getElementById('layanan').value,
                keterangan: ''
            };

            // Validation
            if (!data.nama || !data.no_hp || !data.tanggal || !data.tanggal_kembali || !data.layanan || data.layanan === 'Pilih Layanan') {
                showMessage('reservasiResponse', 'Mohon isi semua field yang diperlukan', 'error');
                return;
            }

            // Validate phone number
            const phoneRegex = /^(\+62|0)[0-9]{9,12}$/;
            if (!phoneRegex.test(data.no_hp.replace(/-/g, ''))) {
                showMessage('reservasiResponse', 'Nomor telepon tidak valid', 'error');
                return;
            }

            // Validate date range
            const startDate = new Date(data.tanggal);
            const endDate = new Date(data.tanggal_kembali);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (startDate < today) {
                showMessage('reservasiResponse', 'Tanggal mulai tidak boleh di masa lalu', 'error');
                return;
            }

            if (endDate <= startDate) {
                showMessage('reservasiResponse', 'Tanggal pengembalian harus lebih besar dari tanggal mulai', 'error');
                return;
            }

            try {
                const formData = new FormData();
                formData.append('nama', data.nama);
                formData.append('no_hp', data.no_hp);
                formData.append('tanggal', data.tanggal);
                formData.append('tanggal_kembali', data.tanggal_kembali);
                
                // Map layanan name to unit_usaha_id
                let unit_usaha_id = 1; // Default GOR
                if (data.layanan.includes('Tenda')) {
                    unit_usaha_id = 2;
                }
                formData.append('unit_usaha_id', unit_usaha_id);
                formData.append('keterangan', data.keterangan);

                const response = await fetch(`${API_BASE}?action=create_reservasi`, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showMessage('reservasiResponse', result.message, 'success');
                    form.reset();
                    // Reload booked dates
                    loadBookedDates();
                } else {
                    showMessage('reservasiResponse', result.message || 'Terjadi kesalahan', 'error');
                }
            } catch (error) {
                console.error('Form submission error:', error);
                showMessage('reservasiResponse', 'Terjadi kesalahan saat mengirim data', 'error');
            }
        });
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
// Initialization
// ================================

window.addEventListener('load', function() {
    const loader = document.getElementById('loader');
    if (loader) {
        loader.style.display = 'none';
    }

    loadPimpinan();
    loadUnitUsaha();
    loadBookedDates();
    setupFormSubmission();
});

// Reveal animation on scroll
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

// Filter unit by type
function filterUnit(type) {
    loadUnitUsaha(type);
}

// Go to login page
function goToLogin() {
    window.location.href = './admin/login.php';
}

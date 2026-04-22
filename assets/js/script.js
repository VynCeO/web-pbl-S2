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

// ================================
// API Calls
// ================================

async function fetchData(action, params = {}) {
    try {
        let url = `${API_BASE}?action=${action}`;
        for (let key in params) {
            url += `&${key}=${params[key]}`;
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
        const container = document.getElementById('pimpinantGrid');

        if (result.success && result.data && result.data.length > 0) {
            container.innerHTML = result.data.map(pimpinan => `
                <div class="pimpinan-card">
                    <div class="pimpinan-image">
                        ${pimpinan.foto ? `<img src="${BASE_URL}assets/images/${pimpinan.foto}" alt="${pimpinan.nama}">` : '<div style="width:100%; height:100%; background:#ddd; display:flex; align-items:center; justify-content:center; color:#999;">Foto</div>'}
                    </div>
                    <div class="pimpinan-info">
                        <h3>${pimpinan.nama}</h3>
                        <p>${pimpinan.posisi}</p>
                        <p style="font-size: 0.85rem; margin-top: 0.5rem; opacity: 0.9;">${pimpinan.keterangan || ''}</p>
                    </div>
                </div>
            `).join('');
        } else {
            container.innerHTML = '<p>Belum ada data pimpinan</p>';
        }
    } catch (error) {
        console.error('Error loading pimpinan:', error);
    }
}

// ================================
// Load Unit Usaha Data
// ================================

async function loadUnitUsaha() {
    try {
        const result = await fetchData('get_unit_usaha');
        const container = document.getElementById('unitGrid');
        const selectElement = document.getElementById('unit_usaha_id');

        if (result.success && result.data && result.data.length > 0) {
            container.innerHTML = result.data.map(unit => `
                <div class="unit-card">
                    <div class="unit-image">
                        ${unit.gambar ? `<img src="${BASE_URL}assets/images/${unit.gambar}" alt="${unit.nama}">` : '🏢'}
                    </div>
                    <div class="unit-info">
                        <h3>${unit.nama}</h3>
                        <p>${unit.deskripsi || 'Unit usaha BUMDes'}</p>
                    </div>
                </div>
            `).join('');

            // Populate select options
            if (selectElement) {
                const options = result.data.map(unit => 
                    `<option value="${unit.id}">${unit.nama}</option>`
                ).join('');
                selectElement.innerHTML = '<option value="">-- Pilih Unit Usaha --</option>' + options;
            }
        } else {
            container.innerHTML = '<p>Belum ada unit usaha</p>';
        }
    } catch (error) {
        console.error('Error loading unit usaha:', error);
    }
}

// ================================
// Load Kontak Data
// ================================

async function loadKontak() {
    try {
        const result = await fetchData('get_kontak');

        if (result.success && result.data) {
            const data = result.data;

            // Update contact information
            if (document.getElementById('alamatText')) {
                document.getElementById('alamatText').textContent = data.alamat || '';
            }

            if (document.getElementById('teleponText')) {
                const telEl = document.getElementById('teleponText');
                telEl.textContent = data.telepon || '';
                telEl.href = `tel:${data.telepon}`;
            }

            if (document.getElementById('whatsappText')) {
                const waEl = document.getElementById('whatsappText');
                waEl.textContent = data.whatsapp || '';
                const waNumber = data.whatsapp.replace(/\D/g, '');
                if (!waNumber.startsWith('62')) {
                    waEl.href = `https://wa.me/62${waNumber.substring(1)}`;
                } else {
                    waEl.href = `https://wa.me/${waNumber}`;
                }
            }

            if (document.getElementById('emailText')) {
                const emailEl = document.getElementById('emailText');
                emailEl.textContent = data.email || '';
                emailEl.href = `mailto:${data.email}`;
            }

            // Update social media links
            const socialLinks = document.querySelectorAll('.social-links a');
            if (socialLinks[0] && data.facebook) {
                socialLinks[0].href = data.facebook;
            }
            if (socialLinks[2] && data.instagram) {
                socialLinks[2].href = data.instagram;
            }
        }
    } catch (error) {
        console.error('Error loading kontak:', error);
    }
}

// ================================
// Form Submission
// ================================

function setupFormSubmission() {
    const form = document.getElementById('formReservasi');

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(form);
            const data = {
                nama: formData.get('nama'),
                no_hp: formData.get('no_hp'),
                tanggal: formData.get('tanggal'),
                unit_usaha_id: formData.get('unit_usaha_id'),
                keterangan: formData.get('keterangan')
            };

            // Validation
            if (!data.nama || !data.no_hp || !data.tanggal || !data.unit_usaha_id) {
                showMessage('responseMessage', 'Mohon isi semua field yang diperlukan', 'error');
                return;
            }

            // Validate phone number format
            const phoneRegex = /^(\+62|0)[0-9]{9,12}$/;
            if (!phoneRegex.test(data.no_hp.replace(/-/g, ''))) {
                showMessage('responseMessage', 'Nomor telepon tidak valid. Gunakan format 08XX atau +62XX', 'error');
                return;
            }

            // Validate date (minimum today)
            const selectedDate = new Date(data.tanggal);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (selectedDate < today) {
                showMessage('responseMessage', 'Tanggal reservasi tidak boleh di masa lalu', 'error');
                return;
            }

            try {
                const formDataForSubmit = new FormData();
                formDataForSubmit.append('nama', data.nama);
                formDataForSubmit.append('no_hp', data.no_hp);
                formDataForSubmit.append('tanggal', data.tanggal);
                formDataForSubmit.append('unit_usaha_id', data.unit_usaha_id);
                formDataForSubmit.append('keterangan', data.keterangan);

                const submitUrl = `${API_BASE}?action=create_reservasi`;
                const response = await fetch(submitUrl, {
                    method: 'POST',
                    body: formDataForSubmit
                });

                const result = await response.json();

                if (result.success) {
                    showMessage('responseMessage', result.message, 'success');
                    form.reset();
                } else {
                    showMessage('responseMessage', result.message || 'Terjadi kesalahan', 'error');
                }
            } catch (error) {
                console.error('Form submission error:', error);
                showMessage('responseMessage', 'Terjadi kesalahan saat mengirim data', 'error');
            }
        });
    }
}

// ================================
// Mobile Menu Toggle
// ================================

function setupMobileMenu() {
    const menuToggle = document.getElementById('menuToggle');
    const navMenu = document.getElementById('navMenu');

    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
        });

        // Close menu when clicking on a link
        const navLinks = navMenu.querySelectorAll('a');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('active');
            });
        });
    }
}

// ================================
// Smooth Scroll Active Link
// ================================

function setupActiveLink() {
    const sections = document.querySelectorAll('section');
    const navLinks = document.querySelectorAll('.nav-menu a');

    window.addEventListener('scroll', () => {
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            if (pageYOffset >= sectionTop - 60) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href').slice(1) === current) {
                link.classList.add('active');
            }
        });
    });
}

// ================================
// Minimum Date Setting
// ================================

function setupDateMinimum() {
    const dateInput = document.getElementById('tanggal');
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('min', today);
    }
}

// ================================
// Initialize
// ================================

document.addEventListener('DOMContentLoaded', () => {
    // Load data
    loadPimpinan();
    loadUnitUsaha();
    loadKontak();

    // Setup interactions
    setupFormSubmission();
    setupMobileMenu();
    setupActiveLink();
    setupDateMinimum();

    console.log('Website BUMDes Sukses Bersama loaded successfully');
});

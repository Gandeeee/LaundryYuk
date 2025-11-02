document.addEventListener('DOMContentLoaded', function() {
    
    // =========================================================================
    // DATABASE SIMULATION 
    // =========================================================================
    let trenOrderChartInstance = null;
    let pendapatanChartInstance = null;

    const OrderStatus = [
        'MENUNGGU_DIJEMPUT', 'DRIVER_OTW', 'CUCIAN_DIAMBIL',
        'MENUNGGU_PEMBAYARAN', 'PROSES_PENCUCIAN', 'SELESAI_DICUCI',
        'DIKIRIM', 'TIBA'
    ];

    // Data Driver Awal
    let db_drivers = [
        { driver_id: 1, name: 'Driver A (Budi)', phone: '0851111111', isAvailable: true },
        { driver_id: 2, name: 'Driver B (Susi)', phone: '0852222222', isAvailable: true },
        { driver_id: 3, name: 'Driver C (Eka)', phone: '0853333333', isAvailable: false } 
    ];
    console.log('[Init] Initial drivers data:', JSON.stringify(db_drivers)); // Konfirmasi data awal

    // Data Order Awal
    let db_orders = [
        {
            order_id: 1, customer_name: 'Kadek Gandhi', phone_number: '08123', pickup_schedule: '27 Okt 2025, 09:00',
            status: 'SELESAI_DICUCI', total_weight: 4.0, total_price: 40000, 
            is_paid: true, is_verified: true, payment_proof_url: null, 
            pickup_driver_id: 2, delivery_driver_id: null 
        },
        {
            order_id: 2, customer_name: 'Revi', phone_number: '08124', pickup_schedule: '27 Okt 2025, 11:00',
            status: 'MENUNGGU_PEMBAYARAN', total_weight: 5.5, total_price: 55000,
            is_paid: false, is_verified: false, payment_proof_url: 'https://i.imgur.com/g1f2s3j.png', 
            pickup_driver_id: 1, delivery_driver_id: null 
        },
        {
            order_id: 3, customer_name: 'Pelanggan Tiga', phone_number: '08125', pickup_schedule: '26 Okt 2025, 14:00',
            status: 'PROSES_PENCUCIAN', total_weight: 7.0, total_price: 70000,
            is_paid: true, is_verified: true, payment_proof_url: 'https://i.imgur.com/g1f2s3j.png', 
            pickup_driver_id: 1, delivery_driver_id: null 
        }
    ];

    // =========================================================================
    // SELEKTOR DOM UTAMA
    // =========================================================================
    const alertContainer = document.getElementById('alert-container');
    const pageTitle = document.getElementById('page-title');
    const sidebarNav = document.getElementById('sidebar-nav');
    const pages = document.querySelectorAll('.page-content');
    const notificationCountEl = document.getElementById('notification-count');
    const notificationList = document.getElementById('notification-list');
    const dashboardStatsCards = document.getElementById('dashboard-stats-cards');
    const dashboardOrderRingkasan = document.getElementById('dashboard-order-ringkasan');
    const orderTableBody = document.getElementById('order-table-body');
    const detailOrderModalEl = document.getElementById('detailOrderModal'); 
    const detailOrderModal = detailOrderModalEl ? new bootstrap.Modal(detailOrderModalEl) : null;
    const verifikasiModalEl = document.getElementById('verifikasiModal'); 
    const verifikasiModal = verifikasiModalEl ? new bootstrap.Modal(verifikasiModalEl) : null;
    const inputTagihanModalEl = document.getElementById('inputTagihanModal'); 
    const inputTagihanModal = inputTagihanModalEl ? new bootstrap.Modal(inputTagihanModalEl) : null;
    const assignDriverModalEl = document.getElementById('assignDriverModal'); 
    const assignDriverModal = assignDriverModalEl ? new bootstrap.Modal(assignDriverModalEl) : null;
    const manualOrderModalEl = document.getElementById('manualOrderModal'); 
    const manualOrderModal = manualOrderModalEl ? new bootstrap.Modal(manualOrderModalEl) : null;
    const driverTableBody = document.getElementById('driver-table-body');
    const driverModalEl = document.getElementById('driverModal'); 
    const driverModal = driverModalEl ? new bootstrap.Modal(driverModalEl) : null;
    const formDriver = document.getElementById('formDriver');
    const driverModalTitle = document.getElementById('driverModalTitle');
    const laporanChartContainer = document.getElementById('laporan-chart-container');
    const exportCsvBtn = document.getElementById('export-csv-btn');

    // Error Check Awal
    if (!detailOrderModal || !verifikasiModal || !inputTagihanModal || !assignDriverModal || !manualOrderModal || !driverModal) {
        console.error("KRITIS: Satu atau lebih Bootstrap Modals gagal diinisialisasi. Periksa ID HTML.");
        showAlert("Error: Komponen halaman tidak lengkap.", "danger");
    }
    // Lanjutkan pengecekan elemen lain...
    if (!alertContainer) console.error("Elemen #alert-container tidak ditemukan.");
    if (!pageTitle) console.error("Elemen #page-title tidak ditemukan.");
    if (!sidebarNav) console.error("Elemen #sidebar-nav tidak ditemukan.");
    if (!pages || pages.length === 0) console.error("Elemen .page-content tidak ditemukan.");
    if (!notificationCountEl) console.error("Elemen #notification-count tidak ditemukan.");
    if (!notificationList) console.error("Elemen #notification-list tidak ditemukan.");
    if (!dashboardStatsCards) console.error("Elemen #dashboard-stats-cards tidak ditemukan.");
    if (!dashboardOrderRingkasan) console.error("Elemen #dashboard-order-ringkasan tidak ditemukan.");
    if (!orderTableBody) console.error("Elemen #order-table-body tidak ditemukan.");
    if (!driverTableBody) console.error("Elemen #driver-table-body tidak ditemukan.");
    if (!formDriver) console.error("Form #formDriver tidak ditemukan.");
    if (!driverModalTitle) console.error("Elemen #driverModalTitle tidak ditemukan.");
    if (!laporanChartContainer) console.error("Elemen #laporan-chart-container tidak ditemukan.");
    if (!exportCsvBtn) console.error("Elemen #export-csv-btn tidak ditemukan.");


    // =========================================================================
    // FUNGSI UTAMA: RENDER SEMUA KOMPONEN
    // =========================================================================
    function renderAll() {
        try {
            renderDashboard();
            renderOrderTable();
            renderDriverTable();
            renderLaporanPage();
            renderNotifications();
        } catch (error) {
            console.error('[Render Error] Terjadi kesalahan saat me-render komponen:', error); 
            showAlert("Terjadi kesalahan internal saat menampilkan data.", "danger");
        }
    }

    // =========================================================================
    // FUNGSI HELPER (UI)
    // =========================================================================
    function showAlert(message, type = 'success') { 
        if (!alertContainer) return; // Jangan jalankan jika container tidak ada
        const wrapper = document.createElement('div');
        wrapper.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        alertContainer.append(wrapper);
        setTimeout(() => { 
            // Tambah check sebelum remove, antisipasi user close manual
            if (wrapper && wrapper.parentNode === alertContainer) {
                 bootstrap.Alert.getOrCreateInstance(wrapper.querySelector('.alert')).close();
            }
        }, 3000);
    }

    function getStatusBadge(status) { 
        switch (status) {
            case 'MENUNGGU_DIJEMPUT': return '<span class="status-badge bg-primary text-white">MENUNGGU JEMPUT</span>';
            case 'DRIVER_OTW': return '<span class="status-badge bg-info text-dark">DRIVER OTW</span>';
            case 'CUCIAN_DIAMBIL': return '<span class="status-badge bg-secondary text-white">CUCIAN DIAMBIL</span>';
            case 'MENUNGGU_PEMBAYARAN': return '<span class="status-badge bg-warning text-dark">MENUNGGU BAYAR</span>';
            case 'PROSES_PENCUCIAN': return '<span class="status-badge bg-success text-white">PROSES CUCI</span>';
            case 'SELESAI_DICUCI': return '<span class="status-badge bg-dark text-white">SELESAI CUCI</span>';
            case 'DIKIRIM': return '<span class="status-badge bg-info text-dark">DIKIRIM</span>';
            case 'TIBA': return '<span class="status-badge bg-light text-dark border">TIBA</span>';
            default: return '<span class="status-badge bg-light text-dark border">N/A</span>';
        }
    }

    function getPaymentBadge(order) { 
        if (!order) return '<span class="payment-badge bg-secondary text-white">Error</span>';
        if (order.is_paid) {
            return '<span class="payment-badge bg-success text-white">LUNAS</span>';
        }
        if (order.status === 'MENUNGGU_PEMBAYARAN' && !order.is_verified) {
            return '<span class="payment-badge bg-info text-dark">VERIF</span>';
        }
        if (typeof order.total_price !== 'undefined' && order.total_price === 0 && order.status === 'CUCIAN_DIAMBIL') {
            return '<span class="payment-badge bg-light text-dark border">BELUM TAGIH</span>';
        }
        return '<span class="payment-badge bg-secondary text-white">BELUM LUNAS</span>';
    }

    // =========================================================================
    // IMPLEMENTASI FITUR: ROUTING, NOTIFIKASI, LAPORAN, DRIVER
    // =========================================================================

    /** 1. LOGIKA ROUTING SPA */
    if (sidebarNav) {
        sidebarNav.addEventListener('click', (e) => { 
            const navLink = e.target.closest('a');
            if (!navLink) return;
            e.preventDefault();
            const pageId = navLink.getAttribute('data-page');
            if (!pageId) return;
            sidebarNav.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
            if(pages) pages.forEach(page => page.classList.remove('active'));
            navLink.classList.add('active');
            const targetPage = document.getElementById(`page-${pageId}`);
            if(targetPage) {
                 targetPage.classList.add('active');
                 if (pageTitle) pageTitle.textContent = navLink.textContent.trim(); 
            } else {
                console.error(`Routing Error: Halaman 'page-${pageId}' tidak ditemukan.`);
                showAlert(`Error: Halaman ${pageId} tidak ditemukan.`, "danger");
            }
        });
    }

    /** 2. LOGIKA NOTIFIKASI */
    function renderNotifications() { 
        if (!notificationList || !notificationCountEl) return;
        notificationList.innerHTML = '';
        const notifs = db_orders.filter(o => o.status === 'MENUNGGU_PEMBAYARAN' && !o.is_verified);
        notificationCountEl.textContent = notifs.length;
        if (notifs.length === 0) {
            notificationList.innerHTML = '<li><span class="dropdown-item text-muted small">Tidak ada notifikasi baru</span></li>';
        } else {
            notifs.forEach(order => {
                const li = document.createElement('li');
                li.innerHTML = `<a class="dropdown-item" href="#" data-order-id="${order.order_id}"><div class="fw-bold">Order #${order.order_id}</div><small>Perlu verifikasi dari ${order.customer_name}</small></a>`;
                li.querySelector('a').addEventListener('click', (e) => {
                    e.preventDefault();
                    populateVerifikasiModal(order.order_id);
                    if(verifikasiModal) verifikasiModal.show();
                    else console.error("Verifikasi modal instance not found when clicking notification.");
                });
                notificationList.appendChild(li);
            });
        }
    }

    /** 3. LOGIKA HALAMAN DASHBOARD */
    function renderDashboard() { 
        if (!dashboardStatsCards || !dashboardOrderRingkasan) return;
        const totalOrders = db_orders.length;
        const totalPendapatan = db_orders.filter(o => o.is_paid).reduce((sum, o) => sum + (o.total_price || 0), 0);
        const perluProses = db_orders.filter(o => o.status && o.status !== 'MENUNGGU_DIJEMPUT' && o.status !== 'TIBA').length;
        
        dashboardStatsCards.innerHTML = `
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="stat-content">
                    <h3>${totalOrders}</h3>
                    <p>Total Pesanan</p>
                </div>
            </div>
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="stat-content">
                    <h3>Rp ${totalPendapatan.toLocaleString('id-ID')}</h3>
                    <p>Total Pendapatan (Lunas)</p>
                </div>
            </div>
            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
                <div class="stat-content">
                    <h3>${perluProses}</h3>
                    <p>Pesanan Perlu Diproses</p>
                </div>
            </div>`;
        
        dashboardOrderRingkasan.innerHTML = '';
        const recentOrders = [...db_orders].sort((a, b) => b.order_id - a.order_id).slice(0, 5);
        if (recentOrders.length === 0) { 
            dashboardOrderRingkasan.innerHTML = '<tr><td colspan="4" class="text-center p-4 text-muted">Belum ada pesanan.</td></tr>'; 
        } else { 
            recentOrders.forEach(order => { 
                dashboardOrderRingkasan.innerHTML += `
                    <tr>
                        <td><strong>#LD-${order.order_id}</strong></td>
                        <td>${order.customer_name || 'N/A'}</td>
                        <td>${getStatusBadge(order.status || 'N/A')}</td>
                        <td class="text-end fw-bold">Rp ${(order.total_price || 0).toLocaleString('id-ID')}</td>
                    </tr>`; 
            }); 
        }
    }

    /** 4. LOGIKA HALAMAN LAPORAN */
    function renderLaporanPage() { 
        if (!laporanChartContainer) return;
        
        const statusCounts = {};
        OrderStatus.forEach(status => statusCounts[status] = 0);
        db_orders.forEach(order => { 
            if (order.status && statusCounts.hasOwnProperty(order.status)) { 
                statusCounts[order.status]++; 
            } 
        });
        
        const maxCount = Math.max(...Object.values(statusCounts), 1);
        
        laporanChartContainer.innerHTML = `
            <div class="chart-bars" id="laporan-chart-bars">
                ${OrderStatus.map(status => {
                    const count = statusCounts[status];
                    const heightPercent = maxCount > 0 ? (count / maxCount) * 80 + 10 : 10; // Min 10% height
                    const displayName = status.replace(/_/g, ' ');
                    const shortName = displayName.length > 12 ? displayName.substring(0, 10) + '...' : displayName;
                    
                    return `
                        <div class="chart-bar-item">
                            <div class="bar-value">${count}</div>
                            <div class="bar-fill" style="height: ${heightPercent}%;" title="${displayName}: ${count} pesanan"></div>
                            <div class="bar-label">${shortName}</div>
                        </div>
                    `;
                }).join('')}
            </div>
        `;
        renderMonthlyCharts();
        
    }

    /** 5. LOGIKA HALAMAN ORDER */
    function renderOrderTable() { 
        if (!orderTableBody) return;
        orderTableBody.innerHTML = '';
        
        // Update total orders count
        const totalOrdersCount = document.getElementById('total-orders-count');
        if (totalOrdersCount) {
            totalOrdersCount.textContent = db_orders.length;
        }
        
        if (db_orders.length === 0) { 
            orderTableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <h5>Belum Ada Order</h5>
                        <p class="mb-0">Mulai dengan membuat order manual atau tunggu order dari pelanggan</p>
                    </td>
                </tr>
            `; 
            return; 
        }
        
        db_orders.forEach(order => {
            const tr = document.createElement('tr');
            let actionButtons = '';
            
            // Customer Info
            const customerInfo = `
                <div class="customer-info">
                    <div class="customer-name">${order.customer_name || 'N/A'}</div>
                    <div class="customer-phone">${order.phone_number || 'No Telepon'}</div>
                </div>
            `;
            
            // Schedule Info
            const scheduleInfo = order.pickup_schedule ? `
                <div class="schedule-info">
                    <div class="schedule-date">${order.pickup_schedule.split(',')[0]}</div>
                    <div class="schedule-time">${order.pickup_schedule.split(',')[1]}</div>
                </div>
            ` : '<div class="walk-in-indicator"><i class="bi bi-shop"></i> WALK-IN</div>';
            
            // Tombol Input/Edit Tagihan
            if ((order.status === 'CUCIAN_DIAMBIL') || (order.status === 'MENUNGGU_PEMBAYARAN' && !order.is_verified)) { 
                actionButtons += `<button class="btn-action btn-tagihan" title="Input/Edit Tagihan" data-bs-toggle="modal" data-bs-target="#inputTagihanModal" data-order-id="${order.order_id}"><i class="bi bi-receipt"></i><span>Tagihan</span></button>`; 
            }
            
            // Tombol Verifikasi
            if (order.status === 'MENUNGGU_PEMBAYARAN' && !order.is_verified) { 
                actionButtons += `<button class="btn-action btn-verifikasi" title="Verifikasi Pembayaran" data-bs-toggle="modal" data-bs-target="#verifikasiModal" data-order-id="${order.order_id}"><i class="bi bi-patch-check"></i><span>Verifikasi</span></button>`; 
            }
            
            // Tombol Update Status
            if (order.status !== 'TIBA') { 
                actionButtons += `<button class="btn-action btn-status" title="Update Status" data-bs-toggle="modal" data-bs-target="#detailOrderModal" data-order-id="${order.order_id}"><i class="bi bi-list-check"></i><span>Status</span></button>`; 
            }
            
            // Tombol Assign Driver Jemput
            if (order.status === 'MENUNGGU_DIJEMPUT') { 
                actionButtons += `<button class="btn-action btn-jemput" title="Assign Driver Penjemputan" data-bs-toggle="modal" data-bs-target="#assignDriverModal" data-order-id="${order.order_id}" data-assignment-type="pickup"><i class="bi bi-truck"></i><span>Jemput</span></button>`; 
            }
            
            // Tombol Assign Driver Antar
            if (order.status === 'SELESAI_DICUCI') { 
                actionButtons += `<button class="btn-action btn-antar" title="Assign Driver Pengantaran" data-bs-toggle="modal" data-bs-target="#assignDriverModal" data-order-id="${order.order_id}" data-assignment-type="delivery"><i class="bi bi-send"></i><span>Antar</span></button>`; 
            }
            
            // Tombol Hapus Order
            actionButtons += `<button class="btn-action btn-delete" title="Hapus Entry" data-action="delete-order" data-order-id="${order.order_id}"><i class="bi bi-trash-fill"></i><span>Hapus</span></button>`;
            
            tr.innerHTML = `
                <td><div class="order-id">#LD-${order.order_id}</div></td>
                <td>${customerInfo}</td>
                <td>${scheduleInfo}</td>
                <td>${getStatusBadge(order.status || 'N/A')}</td>
                <td>${getPaymentBadge(order)}</td>
                <td class="text-end action-buttons-order">${actionButtons || '<span class="text-muted fst-italic">Selesai</span>'}</td>
            `;
            orderTableBody.appendChild(tr);
        });
    }

    /** 6. LOGIKA HALAMAN DRIVER */
    function renderDriverTable() { 
        if (!driverTableBody) return;
        driverTableBody.innerHTML = '';
        if (db_drivers.length === 0) { driverTableBody.innerHTML = '<tr><td colspan="5" class="text-center p-4">Tidak ada data driver.</td></tr>'; return; }
        db_drivers.forEach(driver => {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td>${driver.driver_id}</td><td><strong>${driver.name || 'N/A'}</strong></td><td>${driver.phone || 'N/A'}</td><td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="driverAvailable-${driver.driver_id}" data-driver-id="${driver.driver_id}" ${driver.isAvailable ? 'checked' : ''}><label class="form-check-label" for="driverAvailable-${driver.driver_id}">${driver.isAvailable ? 'Tersedia' : 'Non-Aktif'}</label></div></td><td class="text-end action-buttons"><button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#driverModal" data-action="update" data-driver-id="${driver.driver_id}"><i class="bi bi-pencil-fill"></i> Edit</button><button class="btn btn-outline-danger btn-sm" data-action="delete" data-driver-id="${driver.driver_id}"><i class="bi bi-trash-fill"></i> Hapus</button></td>`;
            driverTableBody.appendChild(tr);
        });
    }
    /** 7. LOGIKA CHART.JS UNTUK LAPORAN BULANAN */
    function renderMonthlyCharts() {
        try {
            const trenOrderCtx = document.getElementById('trenOrderChart');
            const pendapatanCtx = document.getElementById('pendapatanChart');

            if (!trenOrderCtx || !pendapatanCtx) {
                console.error("Satu atau kedua elemen <canvas> chart tidak ditemukan.");
                return; 
            }
            
            // Pengecekan library Chart.js
            if (typeof Chart === 'undefined') {
                console.error("Library Chart.js tidak ter-load!");
                const errorMessage = '<div class="alert alert-warning"><b>Gagal memuat library chart.</b><br><small>Pastikan link CDN Chart.js ada di file HTML Anda.</small></div>';
                trenOrderCtx.parentElement.innerHTML = errorMessage;
                pendapatanCtx.parentElement.innerHTML = errorMessage;
                return;
            }

            // Data statis berdasarkan screenshot Anda
            const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const trenOrderData = [25, 30, 22, 35, 40, 45, 50, 55, 48, 60, 68, 75];
            const pendapatanData = [1500000, 1700000, 1650000, 1850000, 2000000, 2500000, 2700000, 2900000, 3100000, 3300000, 3500000, 4000000];

            // Hancurkan chart lama jika ada (untuk mencegah duplikasi saat re-render)
            if (trenOrderChartInstance) {
                trenOrderChartInstance.destroy();
            }
            if (pendapatanChartInstance) {
                pendapatanChartInstance.destroy();
            }

            // Chart 1: Tren Order Bulanan (Line Chart)
            trenOrderChartInstance = new Chart(trenOrderCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Order',
                        data: trenOrderData,
                        fill: true,
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        borderColor: 'rgb(13, 110, 253)',
                        tension: 0.3,
                        pointRadius: 4,
                        pointBackgroundColor: 'rgb(13, 110, 253)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ` Orders: ${context.raw}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#eef2f6' } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // Chart 2: Pendapatan per Bulan (Bar Chart)
            pendapatanChartInstance = new Chart(pendapatanCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Pendapatan',
                        data: pendapatanData,
                        backgroundColor: '#198754',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ` Pendapatan: Rp ${context.raw.toLocaleString('id-ID')}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value, index, values) {
                                    if (value >= 1000000) {
                                        return `Rp ${value / 1000000} Jt`;
                                    }
                                    return `Rp ${value}`;
                                }
                            },
                            grid: { color: '#eef2f6' }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });

        } catch (error) {
            // BLOK INI TELAH DIPERBARUI UNTUK MENAMPILKAN ERROR
            console.error("Error saat me-render chart bulanan:", error);
            
            // Buat pesan error yang lebih detail
            const errorMessage = `<div class="alert alert-danger" style="text-align: left;">
                                    <b>Gagal menggambar chart.</b>
                                    <br>
                                    <small><b>Error:</b> ${error.message}</small>
                                </div>`;
            
            // Tampilkan error di KEDUA card
            const trenOrderContainer = document.getElementById('trenOrderChart')?.parentElement;
            if (trenOrderContainer) trenOrderContainer.innerHTML = errorMessage;
            
            const pendapatanContainer = document.getElementById('pendapatanChart')?.parentElement;
            if (pendapatanContainer) pendapatanContainer.innerHTML = errorMessage;
        }
    }
    
    // =========================================================================
    // EVENT LISTENER: MODAL POPULATOR (Mengisi data ke modal)
    // =========================================================================

    function populateVerifikasiModal(orderId) { 
         const order = db_orders.find(o => o.order_id == orderId); 
         if (!order) { console.error(`[Populate Error] Order ${orderId} not found for verification modal.`); return; }
         // Dapatkan elemen di dalam fungsi untuk kepastian
         const orderIdEl = document.getElementById('verif_order_id');
         const orderIdInputEl = document.getElementById('verif_order_id_input');
         const customerNameEl = document.getElementById('verif_customer_name');
         const proofImageEl = document.getElementById('verif_proof_image');
         const amountEl = document.getElementById('verif_amount');

         if (orderIdEl) orderIdEl.textContent = `#LD-${orderId}`;
         if (orderIdInputEl) orderIdInputEl.value = orderId;
         if (customerNameEl) customerNameEl.textContent = order.customer_name || 'N/A';
         if (proofImageEl) proofImageEl.src = order.payment_proof_url || ''; // Beri default kosong jika null
         if (amountEl) amountEl.textContent = `Rp ${(order.total_price || 0).toLocaleString('id-ID')}`;
    }

    if (detailOrderModalEl) {
        detailOrderModalEl.addEventListener('show.bs.modal', function(event) { 
            const button = event.relatedTarget; if (!button) return;
            const orderId = button.getAttribute('data-order-id');
            const order = db_orders.find(o => o.order_id == orderId);
            if (!order) { console.error(`[Populate Error] Order ${orderId} not found for status modal.`); return; } 
            
            const modalOrderIdEl = document.getElementById('modalOrderId');
            const statusOrderIdInputEl = document.getElementById('status_order_id');
            if (modalOrderIdEl) modalOrderIdEl.textContent = `#LD-${orderId}`;
            if(statusOrderIdInputEl) statusOrderIdInputEl.value = orderId;

            const checklistGroup = document.getElementById('statusChecklistGroup');
            if(!checklistGroup) { console.error("Element #statusChecklistGroup not found!"); return; }

            checklistGroup.innerHTML = '';
            const currentStatusIndex = OrderStatus.indexOf(order.status);
            OrderStatus.forEach((status, index) => {
                const isCurrent = (index === currentStatusIndex); const isNext = (index === (currentStatusIndex + 1)); let isDisabled = true, labelText = status, labelClass = '';
                if (isCurrent || isNext) isDisabled = false; if (status === 'PROSES_PENCUCIAN' && !order.is_paid && isNext) { isDisabled = true; labelText = "PROSES_PENCUCIAN (Menunggu Lunas)"; } if (isCurrent) labelClass = 'fw-bold'; else if (index < currentStatusIndex) labelClass = 'text-decoration-line-through';
                checklistGroup.innerHTML += `<div class="form-check"><input class="form-check-input" type="radio" name="new_status" value="${status}" id="status_${index}" ${isCurrent ? 'checked' : ''} ${isDisabled ? 'disabled' : ''}><label class="form-check-label ${labelClass}" for="status_${index}">${labelText}</label></div>`;
            });
        });
    }

    if (verifikasiModalEl) {
        verifikasiModalEl.addEventListener('show.bs.modal', function(event) { 
            const button = event.relatedTarget; if (!button) return;
            populateVerifikasiModal(button.getAttribute('data-order-id'));
        });
    }

    if (inputTagihanModalEl) {
        inputTagihanModalEl.addEventListener('show.bs.modal', function(event) { 
            const button = event.relatedTarget; if (!button) return;
            const orderId = button.getAttribute('data-order-id');
            const order = db_orders.find(o => o.order_id == orderId);
             if (!order) { console.error(`[Populate Error] Order ${orderId} not found for tagihan modal.`); return; } 
            
            const tagihanOrderIdEl = document.getElementById('tagihan_order_id');
            const tagihanOrderIdInputEl = document.getElementById('tagihan_order_id_input');
            const tagihanCustomerNameEl = document.getElementById('tagihan_customer_name');
            const totalWeightEl = document.getElementById('totalWeight');
            const totalPriceEl = document.getElementById('totalPrice');

            if (tagihanOrderIdEl) tagihanOrderIdEl.textContent = `#LD-${orderId}`;
            if (tagihanOrderIdInputEl) tagihanOrderIdInputEl.value = orderId;
            if (tagihanCustomerNameEl) tagihanCustomerNameEl.textContent = order.customer_name || 'N/A';
            if (totalWeightEl) totalWeightEl.value = order.total_weight > 0 ? order.total_weight : '';
            if (totalPriceEl) totalPriceEl.value = order.total_price > 0 ? order.total_price : '';
        });
    }

    // %% BAGIAN INI DIPASTIKAN BENAR & DITAMBAH LOGGING %%
    if (assignDriverModalEl) {
        assignDriverModalEl.addEventListener('show.bs.modal', function(event) {
            console.log('[Assign Driver Modal] Event: show.bs.modal triggered.'); 
            const button = event.relatedTarget;
            if (!button) { 
                console.error("[Assign Driver Modal] ERROR: Modal triggered without a button."); 
                return; 
            } 
            
            const orderId = button.getAttribute('data-order-id');
            if (!orderId) { 
                console.error("[Assign Driver Modal] ERROR: Button missing data-order-id."); 
                return; 
            }
            console.log('[Assign Driver Modal] Order ID from button:', orderId); 

            const assignmentType = button.getAttribute('data-assignment-type'); 
            if (!assignmentType) { 
                console.error("[Assign Driver Modal] ERROR: Button missing data-assignment-type."); 
                return; 
            }
            console.log('[Assign Driver Modal] Assignment Type from button:', assignmentType); 

            // Set hidden inputs
            const orderIdInput = document.getElementById('driver_order_id_input');
            const assignmentTypeInput = document.getElementById('driver_assignment_type');
            
            if(orderIdInput) {
                orderIdInput.value = orderId; 
            } else {
                console.error("[Assign Driver Modal] ERROR: Input #driver_order_id_input not found!");
            }
            
            if(assignmentTypeInput) {
                assignmentTypeInput.value = assignmentType; 
            } else {
                console.error("[Assign Driver Modal] ERROR: Input #driver_assignment_type not found!");
            }

            // Set title and description - PERBAIKI SELECTOR INI
            const modalTitle = document.getElementById('driver_order_id'); // Diperbaiki selector
            const description = document.getElementById('assign-driver-description'); // Diperbaiki selector
            
            if(modalTitle && description) {
                if (assignmentType === 'pickup') { 
                    modalTitle.textContent = `Assign Driver Jemput: #LD-${orderId}`; 
                    description.textContent = 'Pilih driver untuk mengambil pesanan ini.'; 
                } else { 
                    modalTitle.textContent = `Assign Driver Antar: #LD-${orderId}`; 
                    description.textContent = 'Pilih driver untuk mengantar pesanan ini ke pelanggan.'; 
                }
            } else { 
                console.error("[Assign Driver Modal] ERROR: Title or Description element not found!"); 
            }
            
            // Populate Dropdown
            const driverSelect = document.getElementById('driverSelect');
            if (!driverSelect) { 
                console.error("[Assign Driver Modal] ERROR: Dropdown #driverSelect not found!"); 
                return; 
            } 

            driverSelect.innerHTML = '<option value="" selected disabled>Memuat...</option>';
            
            try {
                const availableDrivers = db_drivers.filter(d => d.isAvailable === true); 
                console.log('[Assign Driver Modal] Filtered available drivers:', JSON.stringify(availableDrivers)); 

                driverSelect.innerHTML = '<option value="" selected disabled>Pilih Driver...</option>';

                if (availableDrivers.length === 0) { 
                    driverSelect.innerHTML = '<option value="" disabled>Tidak ada driver tersedia</option>';
                    console.log('[Assign Driver Modal] No available drivers found after filter.'); 
                } else { 
                    availableDrivers.forEach(driver => {
                        const option = document.createElement('option'); 
                        option.value = driver.driver_id;
                        option.textContent = driver.name || `Driver ID ${driver.driver_id}`;
                        driverSelect.appendChild(option);
                    });
                    console.log('[Assign Driver Modal] Dropdown populated with options.'); 
                }
            } catch (filterError) {
                console.error("[Assign Driver Modal] Error during driver filtering or dropdown population:", filterError);
                driverSelect.innerHTML = '<option value="" disabled>Error memuat driver</option>';
            }
        });
    }

    if (driverModalEl) {
        driverModalEl.addEventListener('show.bs.modal', function(e) { 
            const button = e.relatedTarget; if (!button) return;
            const action = button.getAttribute('data-action');
            if (formDriver) formDriver.reset(); // Hanya reset jika form ada
            else { console.error("Form #formDriver not found during modal show!"); return; }

            const driverIdInput = formDriver.querySelector('#driver_id_input');
            const driverNameInput = formDriver.querySelector('#driverName');
            const driverPhoneInput = formDriver.querySelector('#driverPhone');
            const driverAvailableSwitch = formDriver.querySelector('#driverAvailable');

            if (!driverIdInput || !driverNameInput || !driverPhoneInput || !driverAvailableSwitch || !driverModalTitle) {
                 console.error("One or more elements inside driverModal not found!");
                 return;
            }

            if (action === 'update') {
                const driverId = button.getAttribute('data-driver-id');
                const driver = db_drivers.find(d => d.driver_id == driverId);
                 if (!driver) { console.error(`[Populate Error] Driver ${driverId} not found for edit modal.`); return; } 
                driverModalTitle.textContent = `Edit Driver: ${driver.name}`;
                driverIdInput.value = driver.driver_id;
                driverNameInput.value = driver.name || '';
                driverPhoneInput.value = driver.phone || '';
                driverAvailableSwitch.checked = driver.isAvailable === true; // Eksplisit boolean
            } else {
                driverModalTitle.textContent = 'Tambah Driver Baru';
                driverIdInput.value = '';
                driverAvailableSwitch.checked = true; // Default true
            }
        });
    }

    // =========================================================================
    // EVENT LISTENER: FORM SUBMISSION (Logika C/U/D)
    // =========================================================================

    const formManualOrder = document.getElementById('formManualOrder');
    if(formManualOrder) {
        formManualOrder.addEventListener('submit', function(e) { 
            e.preventDefault(); const formData = new FormData(this); const newTotalPrice = parseFloat(formData.get('total_price')) || 0; const newTotalWeight = parseFloat(formData.get('total_weight')) || 0;
            const newOrder = { order_id: db_orders.length ? Math.max(...db_orders.map(o => o.order_id)) + 1 : 1, customer_name: formData.get('customer_name'), phone_number: formData.get('phone_number'), pickup_schedule: null, status: newTotalPrice > 0 ? 'MENUNGGU_PEMBAYARAN' : 'CUCIAN_DIAMBIL', total_weight: newTotalWeight, total_price: newTotalPrice, is_paid: false, is_verified: false, payment_proof_url: null, pickup_driver_id: null, delivery_driver_id: null };
            db_orders.push(newOrder); showAlert(`Order walk-in #${newOrder.order_id} untuk ${newOrder.customer_name} berhasil dibuat.`, 'success'); renderAll(); if(manualOrderModal) manualOrderModal.hide(); this.reset();
        });
    } else { console.error("Form #formManualOrder not found!"); }

    const formUpdateStatus = document.getElementById('formUpdateStatus');
    if(formUpdateStatus) {
        formUpdateStatus.addEventListener('submit', function(e) { 
            e.preventDefault(); const formData = new FormData(this); const orderId = formData.get('order_id'); const newStatus = formData.get('new_status');
            if (!newStatus) { showAlert('Anda harus memilih status baru.', 'warning'); return; }
            const order = db_orders.find(o => o.order_id == orderId); if (order) { order.status = newStatus; showAlert(`Status Order #${orderId} diupdate ke: ${newStatus}`, 'success'); renderAll(); if(detailOrderModal) detailOrderModal.hide(); } 
            else { console.error(`[Submit Error] Order ${orderId} not found on status update.`); } 
        });
    } else { console.error("Form #formUpdateStatus not found!"); }
    
    const formVerifikasi = document.getElementById('formVerifikasi');
    if(formVerifikasi) {
        formVerifikasi.addEventListener('submit', function(e) { 
            e.preventDefault(); const orderId = new FormData(this).get('order_id');
            const order = db_orders.find(o => o.order_id == orderId); if (order) { order.is_paid = true; order.is_verified = true; order.status = 'PROSES_PENCUCIAN'; showAlert(`Pembayaran Order #${orderId} diverifikasi!`, 'success'); renderAll(); if(verifikasiModal) verifikasiModal.hide(); }
             else { console.error(`[Submit Error] Order ${orderId} not found on verification.`); } 
        });
    } else { console.error("Form #formVerifikasi not found!"); }

    const formInputTagihan = document.getElementById('formInputTagihan');
    if (formInputTagihan) {
        formInputTagihan.addEventListener('submit', function(e) { 
            e.preventDefault(); const formData = new FormData(this); const orderId = formData.get('order_id');
            const order = db_orders.find(o => o.order_id == orderId); if (order) { order.total_weight = parseFloat(formData.get('total_weight')) || 0; order.total_price = parseFloat(formData.get('total_price')) || 0; if (order.status === 'CUCIAN_DIAMBIL') { order.status = 'MENUNGGU_PEMBAYARAN'; } showAlert(`Tagihan untuk Order #${orderId} disimpan/diperbarui.`, 'success'); renderAll(); if(inputTagihanModal) inputTagihanModal.hide(); }
             else { console.error(`[Submit Error] Order ${orderId} not found on tagihan submit.`); } 
        });
    } else { console.error("Form #formInputTagihan not found!"); }
    
    const formAssignDriver = document.getElementById('formAssignDriver');
    if(formAssignDriver) {
        formAssignDriver.addEventListener('submit', function(e) { 
            e.preventDefault(); 
            const formData = new FormData(this); 
            const orderId = formData.get('order_id'); 
            const driverId = formData.get('driver_id'); 
            const assignmentType = formData.get('assignment_type'); // Sekarang harusnya bisa mendapatkan nilai
            
            console.log('[Assign Driver Submit] Data:', { orderId, driverId, assignmentType });
            
            const order = db_orders.find(o => o.order_id == orderId); 
            if (order && driverId) { 
                if (assignmentType === 'pickup') { 
                    order.pickup_driver_id = parseInt(driverId, 10); 
                    order.status = 'DRIVER_OTW'; 
                    showAlert(`Driver penjemput ditugaskan ke Order #${orderId}.`, 'success'); 
                } else if (assignmentType === 'delivery') { 
                    order.delivery_driver_id = parseInt(driverId, 10); 
                    order.status = 'DIKIRIM'; 
                    showAlert(`Driver pengantar ditugaskan ke Order #${orderId}.`, 'success'); 
                } else { 
                    console.error(`[Submit Error] Invalid assignmentType: ${assignmentType}`); 
                    showAlert("Error: Tipe penugasan driver tidak valid.", "danger"); 
                    return; 
                }
                renderAll(); 
                if(assignDriverModal) assignDriverModal.hide(); 
            } else if (!driverId) { 
                showAlert('Anda harus memilih driver.', 'warning'); 
            } else { 
                console.error(`[Submit Error] Order ${orderId} not found on assign driver submit.`); 
                showAlert("Error: Order tidak ditemukan.", "danger");
            } 
        });
    } else { 
        console.error("Form #formAssignDriver not found!"); 
    }
    
    // formDriver sudah dicek di atas
    if(formDriver) {
        formDriver.addEventListener('submit', function(e) { 
            e.preventDefault(); const formData = new FormData(this); const driverId = formData.get('driver_id'); 
            // Ambil nilai checkbox dengan benar
            const isAvailableChecked = formData.get('isAvailable') === 'on'; 
            const driverData = { name: formData.get('name'), phone: formData.get('phone'), isAvailable: isAvailableChecked };
            console.log('[Driver Submit] Data:', { driverId, driverData }); // <-- DEBUGGING DRIVER SUBMIT

            if (driverId) { 
                const index = db_drivers.findIndex(d => d.driver_id == driverId); 
                if (index > -1) { db_drivers[index] = { ...db_drivers[index], ...driverData }; showAlert(`Driver ${driverData.name} berhasil diupdate.`, 'success'); } 
                else { console.error(`[Submit Error] Driver ${driverId} not found for update.`); showAlert(`Error: Driver ${driverId} tidak ditemukan.`, "danger"); } 
            } else { 
                driverData.driver_id = db_drivers.length ? Math.max(...db_drivers.map(d => d.driver_id)) + 1 : 1; 
                db_drivers.push(driverData); showAlert(`Driver ${driverData.name} berhasil ditambahkan.`, 'success'); 
            }
            renderAll(); 
            if(driverModal) driverModal.hide();
        });
    }

    // =========================================================================
    // EVENT LISTENER: AKSI TABEL (Delete, Toggle)
    // =========================================================================
    
    if (driverTableBody) {
        driverTableBody.addEventListener('click', function(e) { 
            const target = e.target;
            // Hapus Driver
            if (target.closest('[data-action="delete"]')) { 
                const deleteBtn = target.closest('[data-action="delete"]'); 
                const driverId = deleteBtn.getAttribute('data-driver-id'); 
                const driver = db_drivers.find(d => d.driver_id == driverId); 
                if (driver && confirm(`Apakah Anda yakin ingin menghapus driver ${driver.name}?`)) { 
                    const isAssignedPickup = db_orders.some(o => o.pickup_driver_id == driverId && o.status && o.status !== 'TIBA'); 
                    const isAssignedDelivery = db_orders.some(o => o.delivery_driver_id == driverId && o.status && o.status !== 'TIBA'); 
                    if (isAssignedPickup || isAssignedDelivery) { showAlert(`Driver ${driver.name} tidak bisa dihapus karena sedang bertugas.`, 'warning'); } 
                    else { db_drivers = db_drivers.filter(d => d.driver_id != driverId); showAlert(`Driver ${driver.name} berhasil dihapus.`, 'success'); renderAll(); } 
                } else if (!driver) { console.warn(`[Delete Driver] Driver ${driverId} not found.`); }
            }
            // Toggle Available
            if (target.matches('input[type="checkbox"]')) { 
                const driverId = target.getAttribute('data-driver-id'); 
                const driver = db_drivers.find(d => d.driver_id == driverId); 
                if (driver) { driver.isAvailable = target.checked; showAlert(`Status ketersediaan ${driver.name} diupdate.`, 'info'); renderAll(); }
                else { console.warn(`[Toggle Driver] Driver ${driverId} not found.`); }
            }
        });
    }

    if (orderTableBody) {
        orderTableBody.addEventListener('click', function(e) { 
            // Hapus Order
            const deleteBtn = e.target.closest('[data-action="delete-order"]'); 
            if (deleteBtn) { 
                const orderId = deleteBtn.getAttribute('data-order-id'); 
                const order = db_orders.find(o => o.order_id == orderId); 
                if (order && confirm(`Apakah Anda yakin ingin menghapus Order #LD-${orderId} (${order.customer_name})?`)) { 
                    db_orders = db_orders.filter(o => o.order_id != orderId); 
                    showAlert(`Order #LD-${orderId} berhasil dihapus.`, 'success'); 
                    renderAll(); 
                } else if (!order) { console.warn(`[Delete Order] Order ${orderId} not found.`); }
            }
        });
    }

    if (exportCsvBtn) {
        exportCsvBtn.addEventListener('click', function() { 
            let csvContent = "data:text/csv;charset=utf-8,"; csvContent += "OrderID,Customer,Phone,Jadwal,Status,Berat(kg),Total(Rp),Lunas\r\n"; // Tambah Berat
            db_orders.forEach(order => { 
                // Handle potensi koma dalam nama pelanggan
                const customerNameCsv = `"${(order.customer_name || '').replace(/"/g, '""')}"`; 
                const row = [
                    `LD-${order.order_id}`, customerNameCsv, order.phone_number || '',
                    order.pickup_schedule || 'WALK-IN', order.status || '', 
                    order.total_weight || 0, order.total_price || 0,
                    order.is_paid ? 'Ya' : 'Belum'
                ]; 
                csvContent += row.join(",") + "\r\n"; 
            });
            const encodedUri = encodeURI(csvContent); const link = document.createElement("a"); link.setAttribute("href", encodedUri); link.setAttribute("download", "laporan_laundry_full.csv"); document.body.appendChild(link); link.click(); document.body.removeChild(link); showAlert('Laporan CSV sedang di-download.', 'info');
        });
    }

    // =========================================================================
    // INISIALISASI
    // =========================================================================
    console.log('[Init] Document loaded. Starting initial render...');
    renderAll(); // Jalankan render awal

});
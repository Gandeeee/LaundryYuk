// Pastikan kode berjalan setelah dokumen HTML siap
$(document).ready(function() {
    
    // --- FUNGSI UTAMA ---

    // Fungsi untuk ganti halaman (div)
    function showPage(pageId) {
        // Sembunyikan semua halaman
        $('.page-section').hide();
        // Tampilkan halaman yang diminta
        $(pageId).show();
    }

    // Fungsi untuk ganti link 'active' di navbar
    function setActiveNav(navId) {
        // Hapus 'active' dari semua link
        $('.navbar-nav .nav-link').removeClass('active');
        // Tambahkan 'active' ke link yang diklik
        $(navId).addClass('active');
    }

    // --- EVENT LISTENERS (Saat tombol diklik) ---

    // 1. Klik "Dashboard" di navbar
    $('#nav-dashboard').on('click', function(e) {
        e.preventDefault(); // Hentikan link href="#" agar halaman tidak loncat
        setActiveNav('#nav-dashboard');
        showPage('#dashboard-page');
    });

    // 2. Klik "Buat Pesanan" di navbar
    $('#nav-order').on('click', function(e) {
        e.preventDefault();
        setActiveNav('#nav-order');
        showPage('#order-page');
    });

    // 3. Klik "Riwayat" di navbar
    $('#nav-history').on('click', function(e) {
        e.preventDefault();
        setActiveNav('#nav-history');
        showPage('#history-page');
    });

    // 4. Klik tombol "Batal" di form pesanan
    $('#cancelOrder').on('click', function(e) {
        e.preventDefault();
        // Kembalikan ke dashboard
        setActiveNav('#nav-dashboard');
        showPage('#dashboard-page');
        $('#orderForm')[0].reset(); // Reset isi form
    });

    // 5. Saat form "Kirim Pesanan" disubmit
    $('#orderForm').on('submit', function(e) {
        e.preventDefault();
        // (Nanti di sini bisa ditambahkan logika kirim data ke database)
        alert('Pesanan berhasil dikirim! (Ini masih data dummy)');
        
        // Kembalikan ke dashboard setelah submit
        setActiveNav('#nav-dashboard');
        showPage('#dashboard-page');
        $('#orderForm')[0].reset(); // Reset isi form
    });


    // --- LOGIKA BARU UNTUK MODAL DETAIL STATUS ---

    // 6. Saat Modal Detail Status akan Tampil
    var statusDetailModal = document.getElementById('statusDetailModal');
    if(statusDetailModal) { // Pastikan modal ada sebelum menambah listener
        statusDetailModal.addEventListener('show.bs.modal', function(event) {
            // Dapatkan tombol (badge) yang diklik
            var button = $(event.relatedTarget);
            
            // Ekstrak data dari atribut data-*
            var orderId = button.data('order-id');
            var status = button.data('order-status');
            var total = button.data('order-total');
            var tanggal = button.data('order-date');
            var layanan = button.data('order-layanan');

            // Dapatkan elemen modal
            var modalTitle = $(statusDetailModal).find('.modal-title');
            var modalBody = $(statusDetailModal).find('.modal-body');

            // Update title modal
            modalTitle.text('Detail Pesanan: ' + orderId);

            // Buat HTML untuk body modal (dengan timeline)
            var timelineHtml = buildTimeline(status, tanggal, layanan, total);
            
            // Update body modal
            modalBody.html(timelineHtml);
        });
    }

    // --- FUNGSI HELPER BARU UNTUK MEMBUAT TIMELINE ---
    function buildTimeline(status, tanggal, layanan, total) {
        // Definisikan semua tahapan
        // (Ini disesuaikan dengan alur logis yang kita bahas sebelumnya)
        // Tahapan ini harus URUT
        var simplifiedStages = [
            { id: 'MENUNGGU PENJEMPUTAN', text: 'Pesanan Dibuat' },
            { id: 'MENUNGGU PEMBAYARAN', text: 'Menunggu Pembayaran' },
            { id: 'PROSES PENCUCIAN', text: 'Proses Pencucian' },
            { id: 'SELESAI DICUCI', text: 'Selesai Dicuci (Siap Diantar)' }
        ];

        // Tentukan tahapan aktif saat ini
        var currentStageIndex = simplifiedStages.findIndex(stage => stage.id === status);
        
        // HTML untuk info dasar
        var timelineHtml = '<div class="mb-4">' +
            '<p class="mb-1"><strong>Layanan:</strong> ' + layanan + '</p>' +
            '<p class="mb-1"><strong>Tanggal Pesan:</strong> ' + tanggal + '</p>' +
            '<p class="mb-1"><strong>Total:</strong> ' + total + '</p>' +
            '</div>' +
            '<div class="status-timeline">'; // Pembungkus timeline

        // Jika status dibatalkan, tampilkan pesan khusus
        if (status === 'DIBATALKAN') {
            timelineHtml += '<div class="timeline-step active">' + // 'active' akan membuatnya merah
                                '<div class="timeline-dot"></div>' +
                                '<div class="timeline-content fw-bold">Pesanan Dibatalkan</div>' +
                            '</div>';
        } else {
            // Buat timeline normal
            simplifiedStages.forEach(function(stage, index) {
                
                var stageStatus = 'pending'; // Status default (abu-abu)
                if (index < currentStageIndex) {
                    stageStatus = 'completed'; // Sudah lewat (hijau)
                } else if (index === currentStageIndex) {
                    stageStatus = 'active'; // Status saat ini (merah)
                }
                
                timelineHtml += '<div class="timeline-step ' + stageStatus + '">' +
                                  '<div class="timeline-dot"></div>' +
                                  '<div class="timeline-content">' + stage.text + '</div>' +
                                '</div>';
            });
        }

        timelineHtml += '</div>'; // Penutup .status-timeline
        return timelineHtml;
    }
});
// Pastikan kode berjalan setelah dokumen HTML siap
$(document).ready(function() {
            
    // Fungsi untuk menangani klik navigasi
    function navigate(pageId) {
        // Sembunyikan semua halaman
        $('.page-section').hide();
        // Tampilkan halaman yang diklik
        $('#' + pageId + '-page').show();
        
        // Atur status 'active' di navbar
        $('.nav-link').removeClass('active');
        $('.nav-link[data-page="' + pageId + '"]').addClass('active');
    }

    // Event listener untuk link di navbar utama
    $('.nav-link').on('click', function(e) {
        e.preventDefault(); // Mencegah link pindah halaman
        var pageId = $(this).data('page');
        if (pageId) {
            navigate(pageId);
        }
    });

    // Event listener untuk tombol "Pesan Sekarang" di dashboard
    $('#btnPesanSekarang').on('click', function() {
        navigate('order');
    });
});

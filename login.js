// Pastikan kode berjalan setelah dokumen HTML siap
$(document).ready(function() {
    
    // Event listener untuk form login
    $('#loginForm').on('submit', function(e) {
        // Mencegah form mengirim data secara tradisional
        e.preventDefault(); 

        // 1. Ambil nilai dari role yang dipilih
        var role = $('#role').val();

        // 2. Ambil nilai email & password
        var email = $('#email').val();
        var password = $('#password').val();

        // 3. Validasi sederhana (Nantinya ini akan diganti oleh PHP/Laravel)
        if (!role) {
            alert('Silakan pilih role Anda!');
            return;
        }

        if (!email || !password) {
            alert('Email dan Password tidak boleh kosong!');
            return;
        }

        // 4. Logika Pengalihan (Redirect)
        if (role === 'admin') {
            // Jika pilih Admin, arahkan ke halaman admin
            alert('Login sebagai Admin berhasil!');
            // Path relative ke folder admin
            window.location.href = 'admin/index.html'; 
        
        } else if (role === 'customer') {
            // Jika pilih Customer, arahkan ke halaman customer
            alert('Login sebagai Customer berhasil!');
            
            // --- PERUBAHAN DI SINI ---
            // Kita tambahkan './' untuk membuatnya lebih jelas
            // Ini berarti "dari folder saat ini, cari folder customer, lalu buka customer.html"
            window.location.href = './customer/customer.html'; 
        }
    });
});


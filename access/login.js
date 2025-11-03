// login.js (REVISI)

// Pastikan kode berjalan setelah dokumen HTML siap
$(document).ready(function() {
    
    // Event listener untuk form login
    $('#loginForm').on('submit', function(e) {
        // Mencegah form mengirim data secara tradisional
        e.preventDefault(); 

        // 1. Ambil nilai email & password
        var email = $('#email').val();
        var password = $('#password').val();

        // 2. Validasi sederhana (REVISI: Validasi role dihapus)
        if (!email || !password) {
            alert('Email dan Password tidak boleh kosong!');
            return;
        }

        // 3. REVISI 1: Daftar email yang dianggap sebagai Admin
        var adminEmails = [
            'adminlaundry1@gmail.com',
            'adminutama@gmail.com',
            'adminlaundry2@gmail.com',
            'admin@gmail.com'
        ];

        // 4. Logika Pengalihan (Redirect)
        
        // Cek apakah email yang dimasukkan ada di dalam daftar admin
        if (adminEmails.includes(email)) {
            
            // Jika email ada di daftar Admin
            alert('Login sebagai Admin berhasil!');
            // Path relative ke folder admin
            window.location.href = '../admin/index.html'; 
        
        } else {
            
            // Jika email lain, otomatis dianggap Customer
            alert('Login sebagai Customer berhasil!');
            window.location.href = '../customer/customer.html'; 
        }
    });
});
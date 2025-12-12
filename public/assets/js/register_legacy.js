$(document).ready(function() {
    
    const form = $('#registerForm');
    const passwordInput = $('#password');
    const konfirmasiPasswordInput = $('#konfirmasiPassword');
    const passwordMismatchError = $('#passwordMismatchError');

    form.on('submit', function(e) {
        
        // Cek dulu apakah password cocok
        if (passwordInput.val() !== konfirmasiPasswordInput.val()) {
            e.preventDefault(); // Hentikan submit
            e.stopPropagation(); // Hentikan submit
            
            // Tampilkan error manual di konfirmasi password
            konfirmasiPasswordInput.addClass('is-invalid');
            passwordMismatchError.show(); // Tampilkan pesan error spesifik
        } else {
            // Jika cocok, sembunyikan error
            konfirmasiPasswordInput.removeClass('is-invalid');
            passwordMismatchError.hide();
        }

        // Jalankan validasi standar Bootstrap
        if (!form[0].checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        } else if (passwordInput.val() === konfirmasiPasswordInput.val()) {
            // JIKA SEMUA VALIDASI LOLOS DAN PASSWORD COCOK
            e.preventDefault(); // Hentikan pengiriman form
            
            alert('Registrasi berhasil! Anda akan diarahkan ke halaman Login.');
            
            // Arahkan ke halaman login
            window.location.href = 'login.html';
        }

        form.addClass('was-validated'); // Tampilkan hasil validasi Bootstrap
    });

    // Tambahan: Cek kecocokan password saat user mengetik
    konfirmasiPasswordInput.on('keyup', function() {
        if (passwordInput.val() === $(this).val()) {
            // Jika cocok
            $(this).removeClass('is-invalid');
            passwordMismatchError.hide();
        } else {
            // Jika tidak cocok
            $(this).addClass('is-invalid');
            passwordMismatchError.show();
        }
    });
});

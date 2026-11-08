// Konfirmasi hapus dengan SweetAlert2 -> submit form POST milik tombol terkait
document.addEventListener('click', function (e) {
    const tombol = e.target.closest('.btn-hapus');
    if (!tombol) return;

    e.preventDefault();
    Swal.fire({
        title: 'Hapus buku ini?',
        html: 'Data <b>' + tombol.dataset.judul + '</b> beserta file sampulnya akan dihapus permanen.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal'
    }).then(function (hasil) {
        if (hasil.isConfirmed) {
            tombol.closest('form').submit();
        }
    });
});

// Pratinjau gambar sampul sebelum diupload + validasi sisi klien (2MB, jpg/png)
const inputSampul = document.getElementById('sampul');
if (inputSampul) {
    inputSampul.addEventListener('change', function () {
        const file = this.files[0];
        const box = document.getElementById('previewBox');
        const pesan = document.getElementById('pesanFile');
        pesan.textContent = '';

        if (!file) return;

        const tipeOk = ['image/jpeg', 'image/png'].includes(file.type);
        if (!tipeOk) {
            pesan.textContent = 'Hanya file JPG/PNG yang diperbolehkan.';
            this.value = '';
            return;
        }
        if (file.size > 2 * 1024 * 1024) {
            pesan.textContent = 'Ukuran file maksimal 2 MB.';
            this.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function (ev) {
            box.innerHTML = '<img src="' + ev.target.result + '" alt="Pratinjau sampul">';
        };
        reader.readAsDataURL(file);
    });
}

// Tampilkan / sembunyikan password
const toggle = document.getElementById('togglePassword');
if (toggle) {
    toggle.addEventListener('click', function () {
        const input = document.getElementById('password');
        const ikon = this.querySelector('i');
        const tampil = input.type === 'password';
        input.type = tampil ? 'text' : 'password';
        ikon.className = tampil ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
}

# E-Posyandu Bina Cita

## Instalasi awal

1. Buat database baru dan impor [posyandu_db.sql](posyandu_db.sql).
2. Salin `config.example.php` menjadi `config.local.php`, lalu isi kredensial MySQL. File lokal ini diabaikan Git.
3. Daftarkan akun orang tua melalui `register.php`.
4. Buat akun kader dengan akses database, lalu ubah role akun yang sudah terdaftar:

   ```sql
   UPDATE users SET role = 'admin' WHERE email = 'kader@example.org';
   ```

Tidak ada jalur pendaftaran admin melalui aplikasi.

## Memperbarui instalasi lama

1. Cadangkan database terlebih dahulu.
2. Jalankan [migrasi_001_keamanan_dan_skema.sql](migrasi_001_keamanan_dan_skema.sql).
3. Lengkapi `jenis_kelamin` dan pilih akun orang tua untuk setiap data anak lama lewat halaman **Manajemen Data Anak**.
4. Ganti kredensial database lama karena sebelumnya pernah tersimpan di Git. Hapus data pribadi dari riwayat Git sebelum repositori dibagikan ke pihak lain.

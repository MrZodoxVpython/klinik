-- Jalankan pada database lama setelah membuat cadangan database.
-- Migrasi ini tidak menghapus data.

ALTER TABLE users
    ADD COLUMN IF NOT EXISTS failed_login_attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
    ADD COLUMN IF NOT EXISTS locked_until DATETIME NULL DEFAULT NULL;

ALTER TABLE data_anak
    ADD COLUMN IF NOT EXISTS jenis_kelamin ENUM('Laki-laki', 'Perempuan') NULL AFTER tgl_lahir;

-- Lengkapi jenis_kelamin dan hubungkan setiap data_anak lama ke akun pengguna
-- sebelum menerapkan NOT NULL pada kedua kolom tersebut.
-- Periksa NIK duplikat terlebih dahulu sebelum menjalankan indeks unik di bawah:
-- SELECT nik_anak, COUNT(*) FROM data_anak GROUP BY nik_anak HAVING COUNT(*) > 1;
-- ALTER TABLE data_anak ADD UNIQUE KEY uq_data_anak_nik (nik_anak);

ALTER TABLE jadwal_posyandu
    ADD INDEX IF NOT EXISTS idx_jadwal_tanggal_waktu (tanggal, waktu);

ALTER TABLE imunisasi
    ADD INDEX IF NOT EXISTS idx_imunisasi_anak_tanggal (anak_id, tanggal);

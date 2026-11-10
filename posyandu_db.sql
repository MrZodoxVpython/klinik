-- Skema awal E-Posyandu Bina Cita.
-- File ini sengaja tidak berisi akun, NIK, alamat, atau data kesehatan nyata.

CREATE DATABASE IF NOT EXISTS posyandu_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE posyandu_db;

CREATE TABLE users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'pengguna') NOT NULL DEFAULT 'pengguna',
    failed_login_attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
    locked_until DATETIME NULL DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE data_anak (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nama_anak VARCHAR(100) NOT NULL,
    nik_anak CHAR(16) NOT NULL,
    tgl_lahir DATE NOT NULL,
    jenis_kelamin ENUM('Laki-laki', 'Perempuan') NOT NULL,
    nama_ibu VARCHAR(100) NOT NULL,
    nama_ayah VARCHAR(100) NULL,
    alamat TEXT NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_data_anak_nik (nik_anak),
    KEY idx_data_anak_user_id (user_id),
    CONSTRAINT fk_data_anak_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE imunisasi (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    anak_id INT UNSIGNED NOT NULL,
    tanggal DATE NOT NULL,
    jenis_imunisasi VARCHAR(100) NOT NULL,
    keterangan TEXT NULL,
    berat_badan DECIMAL(5,2) NULL,
    tinggi_badan DECIMAL(5,2) NULL,
    diagnosa VARCHAR(255) NULL,
    PRIMARY KEY (id),
    KEY idx_imunisasi_anak_tanggal (anak_id, tanggal),
    CONSTRAINT fk_imunisasi_anak FOREIGN KEY (anak_id) REFERENCES data_anak(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE jadwal_posyandu (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    tanggal DATE NOT NULL,
    waktu TIME NOT NULL,
    tempat VARCHAR(100) NOT NULL,
    keterangan TEXT NULL,
    PRIMARY KEY (id),
    KEY idx_jadwal_tanggal_waktu (tanggal, waktu)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE petugas (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    jabatan VARCHAR(100) NOT NULL,
    kontak VARCHAR(50) NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 13 Jun 2025 pada 05.20
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `posyandu_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `data_anak`
--

CREATE TABLE `data_anak` (
  `id` int(11) NOT NULL,
  `nama_anak` varchar(100) DEFAULT NULL,
  `nik_anak` varchar(20) DEFAULT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `nama_ibu` varchar(100) DEFAULT NULL,
  `nama_ayah` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `data_anak`
--

INSERT INTO `data_anak` (`id`, `nama_anak`, `nik_anak`, `tgl_lahir`, `nama_ibu`, `nama_ayah`, `alamat`, `user_id`) VALUES
(1, 'Lorean wildansyah', '3516056310050006', '2030-06-12', 'Siti Nur Hidayah', 'Muhammad Nafik', 'Modopuro', 1),
(2, 'Alvaro Putra Wibowo', '3516056310050001', '2021-11-29', 'Anis Mufridah', 'Bibit Wibowo', 'Modopuro', 2),
(4, 'Gio putra Zafrayno Dermawan', '3516054800003544', '2024-10-23', 'Naira Ranaima', 'Akbar Dermawan', 'Modopuro', 9),
(5, 'Andrian adi pratama', '3516055311000455', '2022-05-27', 'Icha Mariana dewi', 'Yusril mahbubi kemal', 'mojosari', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `imunisasi`
--

CREATE TABLE `imunisasi` (
  `id` int(11) NOT NULL,
  `anak_id` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `jenis_imunisasi` varchar(100) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `berat_badan` decimal(5,2) DEFAULT NULL,
  `tinggi_badan` decimal(5,2) DEFAULT NULL,
  `diagnosa` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `imunisasi`
--

INSERT INTO `imunisasi` (`id`, `anak_id`, `tanggal`, `jenis_imunisasi`, `keterangan`, `berat_badan`, `tinggi_badan`, `diagnosa`) VALUES
(1, 2, '2024-01-09', 'Polio', 'gizi cukup baik', 10.00, 96.00, 'normal'),
(4, 1, '2024-01-09', 'Polio', 'gizi baik', 11.00, 106.00, 'normal'),
(5, 4, '2024-06-13', 'Campak', 'Gizi cukup baik', 10.00, 98.00, '-');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal_posyandu`
--

CREATE TABLE `jadwal_posyandu` (
  `id` int(11) NOT NULL,
  `tanggal` date DEFAULT NULL,
  `waktu` varchar(50) DEFAULT NULL,
  `tempat` varchar(100) DEFAULT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jadwal_posyandu`
--

INSERT INTO `jadwal_posyandu` (`id`, `tanggal`, `waktu`, `tempat`, `keterangan`) VALUES
(3, '2025-06-20', '08:00', 'Puskesmas Bina Cita', 'Jangan lupa datang tepat waktu!');

-- --------------------------------------------------------

--
-- Struktur dari tabel `petugas`
--

CREATE TABLE `petugas` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `kontak` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','pengguna') DEFAULT 'pengguna'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`) VALUES
(1, 'Siti Nur Hidayah', 'Hida@gmail.com', '$2y$10$FgIWW4vZLbnSP4tSQduDKep.FubXVEzw0jCXrqbbS2dmla16JsaeK', 'pengguna'),
(2, 'Anis Mufridah', 'Anis@gmail.com', '$2y$10$onnDgFPnBSP4IAo36zoIAuIJsBCF2aaGWjgEiczxYrIgkCv9egp9W', 'pengguna'),
(5, 'Lilis Syafira', 'Lilis@gmail.com', '$2y$10$0XGu9Od1H1V0.yDi782QqeRuEaRusjgnUfphuCV3hpX.Z8zPpar/W', 'pengguna'),
(8, 'Reviyona Heaven Windya', '01.kader.Yona@gmail.com', '$2y$10$WROpc8WQ8E0ARiYnrs.qs.ELRIQJRA5jHIa4696wa2L9cqw63n22e', 'admin'),
(9, 'Naira Ranaima', 'Naira@gmail.com', '$2y$10$v86nGWxmHVBpeKZTjdJfh.GhvNeOhhynVDcLcMLDYMz4U5281YN.a', 'pengguna');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `data_anak`
--
ALTER TABLE `data_anak`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Indeks untuk tabel `imunisasi`
--
ALTER TABLE `imunisasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `anak_id` (`anak_id`);

--
-- Indeks untuk tabel `jadwal_posyandu`
--
ALTER TABLE `jadwal_posyandu`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `petugas`
--
ALTER TABLE `petugas`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `data_anak`
--
ALTER TABLE `data_anak`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `imunisasi`
--
ALTER TABLE `imunisasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `jadwal_posyandu`
--
ALTER TABLE `jadwal_posyandu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `petugas`
--
ALTER TABLE `petugas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `data_anak`
--
ALTER TABLE `data_anak`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `imunisasi`
--
ALTER TABLE `imunisasi`
  ADD CONSTRAINT `imunisasi_ibfk_1` FOREIGN KEY (`anak_id`) REFERENCES `data_anak` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

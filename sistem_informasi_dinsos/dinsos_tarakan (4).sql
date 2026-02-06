-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 05, 2026 at 02:47 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dinsos_tarakan`
--

-- --------------------------------------------------------

--
-- Table structure for table `indikator`
--

CREATE TABLE `indikator` (
  `id` int(11) NOT NULL,
  `sasaran_strategis` text NOT NULL,
  `indikator_kinerja` text NOT NULL,
  `satuan` varchar(50) DEFAULT NULL,
  `target_tahunan` decimal(15,2) DEFAULT NULL,
  `tahun` int(11) NOT NULL,
  `bidang` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `program` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `indikator`
--

INSERT INTO `indikator` (`id`, `sasaran_strategis`, `indikator_kinerja`, `satuan`, `target_tahunan`, `tahun`, `bidang`, `created_at`, `program`) VALUES
(1, 'Meningkatkan kesejahteraan sosial', 'Persentase penanganan fakir miskin', '%', 95.00, 2024, 'Perencanaan dan Keuangan', '2026-01-05 12:53:46', 'Program Penanganan Fakir Miskin'),
(2, 'Meningkatkan pelayanan sosial', 'Jumlah PMKS tertangani', 'Orang', 1200.00, 2024, 'Umum dan Kepegawaian', '2026-01-05 12:53:46', 'Program Pelayanan PMKS'),
(3, 'Peningkatan perlindungan sosial', 'Cakupan bantuan sosial', '%', 90.00, 2024, 'Rehabilitasi Sosial', '2026-01-05 12:53:46', 'Program Bantuan Sosial Terpadu'),
(4, 'Penguatan kelembagaan sosial', 'Jumlah lembaga sosial aktif', 'Lembaga', 75.00, 2024, 'Perlindungan dan Jaminan Sosial', '2026-01-05 12:53:46', 'Program Penguatan Lembaga Sosial'),
(5, 'Peningkatan kualitas SDM sosial', 'Jumlah pendamping sosial tersertifikasi', 'Orang', 50.00, 2024, 'Pemberdayaan Sosial', '2026-01-05 12:53:46', 'Program Peningkatan SDM Sosial'),
(6, 'Meningkatnya Tata Kelola Linerja Dinsos PM', 'Jumlah Laporan Evaluasi Kinerja', 'laporan', 3.00, 2025, 'Perencanaan dan Keuangan', '2026-01-05 13:41:38', 'Program Tata Kelola Kinerja Dinsos'),
(7, 'Meningkatnya Tata Kelola Linerja Dinsos PM	', 'Jumlah Laporan', 'laporan', 3.00, 2025, 'Perencanaan dan Keuangan', '2026-01-05 13:42:58', 'Program Pelaporan dan Evaluasi Kinerja'),
(8, 'Peningkatan kualitas SDM sosial', 'Jumlah pendamping sosial tersertifikasi', 'Orang', 50.00, 2025, 'Rehabilitasi Sosial', '2026-01-05 13:44:00', 'Program Rehabilitasi Sosial'),
(9, 'Meningkatkan pelayanan sosial', 'Jumlah PMKS tertangani', 'Orang', 1200.00, 2025, 'Perencanaan dan Keuangan', '2026-01-05 13:47:54', 'Program Pelayanan Sosial Lanjutan'),
(10, 'test', 'yesy', '%', 12.00, 2025, 'Perencanaan dan Keuangan', '2026-01-05 14:18:31', 'Program Uji Coba Sistem'),
(11, '121', '1212', '21', 21.00, 2024, 'Umum dan Kepegawaian', '2026-01-05 14:18:41', 'Program Administrasi Umum'),
(12, 'tes', 'tes', 'laporan', 12.00, 2025, 'Perencanaan dan Keuangan', '2026-01-05 14:39:56', 'Program Pengembangan Aplikasi'),
(13, 'bhabbss', 'wwwewew', '21', 212.00, 2025, 'Perencanaan dan Keuangan', '2026-01-06 00:58:09', 'Program Percobaan Data'),
(14, 'dddwdwdwdw', 'wddwdw', '2', 22.00, 2025, 'Pemberdayaan Sosial', '2026-01-06 00:58:58', 'Program Pemberdayaan Sosial'),
(15, 'tes1', 'tes1', '%', 90.00, 2025, 'Perencanaan dan Keuangan', '2026-01-07 00:17:13', NULL),
(16, 'tes2', 'tes2', 'laporan', 3.00, 2025, 'Perencanaan dan Keuangan', '2026-01-07 00:24:48', 'tes2');

-- --------------------------------------------------------

--
-- Table structure for table `kegiatan`
--

CREATE TABLE `kegiatan` (
  `id` int(11) NOT NULL,
  `sasaran_strategis` varchar(255) DEFAULT NULL,
  `indikator_kinerja` varchar(255) DEFAULT NULL,
  `satuan` varchar(50) DEFAULT NULL,
  `target` decimal(10,2) DEFAULT NULL,
  `tahun` year(4) DEFAULT NULL,
  `bidang` varchar(100) DEFAULT NULL,
  `program` varchar(255) DEFAULT NULL,
  `kegiatan` varchar(255) DEFAULT NULL,
  `sub_kegiatan` varchar(255) DEFAULT NULL,
  `pagu_anggaran` decimal(15,2) DEFAULT NULL,
  `realisasi_bulan1` decimal(15,2) DEFAULT NULL,
  `realisasi_bulan2` decimal(15,2) DEFAULT NULL,
  `realisasi_bulan3` decimal(15,2) DEFAULT NULL,
  `realisasi_bulan4` decimal(15,2) DEFAULT NULL,
  `realisasi_bulan5` decimal(15,2) DEFAULT NULL,
  `realisasi_bulan6` decimal(15,2) DEFAULT NULL,
  `realisasi_bulan7` decimal(15,2) DEFAULT NULL,
  `realisasi_bulan8` decimal(15,2) DEFAULT NULL,
  `realisasi_bulan9` decimal(15,2) DEFAULT NULL,
  `realisasi_bulan10` decimal(15,2) DEFAULT NULL,
  `realisasi_bulan11` decimal(15,2) DEFAULT NULL,
  `realisasi_bulan12` decimal(15,2) DEFAULT NULL,
  `realisasi_anggaran_bulan1` decimal(15,2) DEFAULT NULL,
  `realisasi_anggaran_bulan2` decimal(15,2) DEFAULT NULL,
  `realisasi_anggaran_bulan3` decimal(15,2) DEFAULT NULL,
  `realisasi_anggaran_bulan4` decimal(15,2) DEFAULT NULL,
  `realisasi_anggaran_bulan5` decimal(15,2) DEFAULT NULL,
  `realisasi_anggaran_bulan6` decimal(15,2) DEFAULT NULL,
  `realisasi_anggaran_bulan7` decimal(15,2) DEFAULT NULL,
  `realisasi_anggaran_bulan8` decimal(15,2) DEFAULT NULL,
  `realisasi_anggaran_bulan9` decimal(15,2) DEFAULT NULL,
  `realisasi_anggaran_bulan10` decimal(15,2) DEFAULT NULL,
  `realisasi_anggaran_bulan11` decimal(15,2) DEFAULT NULL,
  `realisasi_anggaran_bulan12` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `bukti1` varchar(255) DEFAULT NULL,
  `bukti2` varchar(255) DEFAULT NULL,
  `bukti3` varchar(255) DEFAULT NULL,
  `bukti4` varchar(255) DEFAULT NULL,
  `bukti5` varchar(255) DEFAULT NULL,
  `bukti6` varchar(255) DEFAULT NULL,
  `bukti7` varchar(255) DEFAULT NULL,
  `bukti8` varchar(255) DEFAULT NULL,
  `bukti9` varchar(255) DEFAULT NULL,
  `bukti10` varchar(255) DEFAULT NULL,
  `bukti11` varchar(255) DEFAULT NULL,
  `bukti12` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `realisasi_triwulan`
--

CREATE TABLE `realisasi_triwulan` (
  `id` int(11) NOT NULL,
  `indikator_id` int(11) NOT NULL,
  `triwulan` tinyint(4) NOT NULL CHECK (`triwulan` between 1 and 4),
  `target` decimal(15,2) DEFAULT NULL,
  `realisasi` decimal(15,2) DEFAULT NULL,
  `persentase` decimal(6,2) DEFAULT NULL,
  `pagu_anggaran` decimal(20,2) DEFAULT NULL,
  `realisasi_anggaran` decimal(20,2) DEFAULT NULL,
  `persentase_anggaran` decimal(6,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `realisasi_triwulan`
--

INSERT INTO `realisasi_triwulan` (`id`, `indikator_id`, `triwulan`, `target`, `realisasi`, `persentase`, `pagu_anggaran`, `realisasi_anggaran`, `persentase_anggaran`) VALUES
(1, 1, 1, 20.00, 18.50, 92.50, 50000000.00, 45000000.00, 90.00),
(2, 1, 2, 25.00, 23.00, 92.00, 60000000.00, 56000000.00, 93.33),
(3, 2, 1, 300.00, 280.00, 93.33, 80000000.00, 76000000.00, 95.00),
(4, 2, 2, 300.00, 290.00, 96.67, 90000000.00, 85000000.00, 94.44),
(5, 3, 1, 20.00, 19.00, 95.00, 70000000.00, 65000000.00, 92.86),
(6, 3, 2, 25.00, 24.00, 96.00, 80000000.00, 78000000.00, 97.50),
(7, 4, 1, 15.00, 14.00, 93.33, 40000000.00, 37000000.00, 92.50),
(8, 4, 2, 20.00, 19.00, 95.00, 45000000.00, 42000000.00, 93.33),
(9, 5, 1, 10.00, 9.00, 90.00, 30000000.00, 27000000.00, 90.00),
(10, 5, 2, 15.00, 14.00, 93.33, 35000000.00, 33000000.00, 94.29),
(14, 13, 3, NULL, 123.00, NULL, 2000000.00, 30000000.00, NULL),
(15, 12, 1, NULL, 123.00, NULL, 50000.00, 50000.00, NULL),
(16, 10, 1, NULL, 23.00, NULL, 45000.00, 50000.00, NULL),
(17, 9, 2, NULL, 1100.00, NULL, 45000.00, 50000.00, NULL),
(18, 9, 3, NULL, 1100.00, NULL, 45000.00, 50000.00, NULL),
(19, 10, 2, NULL, 23.00, NULL, 45000.00, 50000.00, NULL),
(20, 7, 1, NULL, 123.00, NULL, 45000.00, 50000.00, NULL),
(21, 6, 1, NULL, 155.00, NULL, 45000.00, 50000.00, NULL),
(22, 1, 3, NULL, 18.50, NULL, 45000.00, 50000.00, NULL),
(23, 13, 2, NULL, 123.00, NULL, 240000.00, 200000.00, NULL),
(24, 16, 1, NULL, 800.00, NULL, 240000.00, 200000.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `undangan`
--

CREATE TABLE `undangan` (
  `id` int(11) NOT NULL,
  `judul_kegiatan` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` time NOT NULL,
  `tempat` varchar(255) NOT NULL,
  `pihak_mengundang` varchar(255) NOT NULL,
  `bidang_terkait` varchar(150) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status_kegiatan` varchar(50) DEFAULT 'Belum Terlaksana',
  `menghadiri` varchar(50) DEFAULT NULL,
  `bukti` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', 'dinsos123', 'Admin'),
(2, 'kadis', 'dinsos123', 'Kepala Dinas'),
(3, 'staff perencanaan', 'dinsos123', 'Perencanaan dan Keuangan'),
(4, 'staff umum', 'dinsos123', 'Umum dan Kepegawaian'),
(5, 'staff resos', 'dinsos123', 'Rehabilitasi Sosial'),
(6, 'staff linjamsos', 'dinsos123', 'Perlindungan dan Jaminan Sosial'),
(7, 'staff dayasos', 'dinsos123', 'Pemberdayaan Sosial'),
(8, 'staff PM', 'dinsos123', 'Pemberdayaan Masyarakat'),
(9, 'kabid sosial', 'dinsos123', 'Kepala Bidang Sosial'),
(10, 'kasubbag perencanaan', 'dinsos123', 'Kepala Sub Bagian Perencanaan'),
(11, 'kabid pm', 'dinsos123', 'Kepala Bidang Pemberdayaan Masyarakat'),
(12, 'kasubbag kepegawaian', 'dinsos123', 'Kepala Sub Bagian Kepegawaian'),
(13, 'Sekretaris', 'dinsos123', 'Sekretaris');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `indikator`
--
ALTER TABLE `indikator`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kegiatan`
--
ALTER TABLE `kegiatan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `realisasi_triwulan`
--
ALTER TABLE `realisasi_triwulan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `indikator_id` (`indikator_id`);

--
-- Indexes for table `undangan`
--
ALTER TABLE `undangan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `indikator`
--
ALTER TABLE `indikator`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `kegiatan`
--
ALTER TABLE `kegiatan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `realisasi_triwulan`
--
ALTER TABLE `realisasi_triwulan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `undangan`
--
ALTER TABLE `undangan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `realisasi_triwulan`
--
ALTER TABLE `realisasi_triwulan`
  ADD CONSTRAINT `realisasi_triwulan_ibfk_1` FOREIGN KEY (`indikator_id`) REFERENCES `indikator` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

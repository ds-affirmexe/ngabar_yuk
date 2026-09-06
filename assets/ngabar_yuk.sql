-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 06, 2026 at 03:59 AM
-- Server version: 8.4.7
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ngabar_yuk`
--

-- --------------------------------------------------------

--
-- Table structure for table `berita`
--

DROP TABLE IF EXISTS `berita`;
CREATE TABLE IF NOT EXISTS `berita` (
  `id` int NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `penulis` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `konten` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `gambar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `read_time` int NOT NULL DEFAULT '1',
  `tanggal` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `berita`
--

INSERT INTO `berita` (`id`, `judul`, `kategori`, `penulis`, `konten`, `gambar`, `read_time`, `tanggal`) VALUES
(20, 'Morfem Hadiri Acara PKKMB PENS 2026 Sebagai Bintang Tamu', 'Lokal', 'admin3232', '<h2><b>MORFEM MENGHADIRI ACARA PKKMB PENS 2026 SEBAGAI BINTANG TAMU</b></h2><div style=\"text-align: justify;\">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta.&nbsp;</div><div><br></div><div style=\"text-align: justify;\">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta.&nbsp;</div><div style=\"text-align: justify;\"><br></div><div style=\"text-align: justify;\">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta.&nbsp;</div><div style=\"text-align: justify;\"><br></div><div style=\"text-align: justify;\">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta.&nbsp;</div><div style=\"text-align: justify;\"><br></div><div style=\"text-align: justify;\">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta.&nbsp;</div>', '6a9cde7071350.jpg', 4, '2026-09-06 03:30:56'),
(21, 'The Jeblogs Berkunjung ke PENS', 'Insight', 'admin3232', '<h2>THE JEBLOGS BERKUNJUNG KE PENS</h2><div>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta.&nbsp;</div><div><br></div><div>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta.&nbsp;</div><div><br></div>', '6a9cdff59aecb.jpeg', 1, '2026-09-06 03:37:25'),
(18, 'filosofi kopi', 'Insight', 'admin3232', 'ddd<div><b>bold</b></div><div><i>italic</i></div><div><u>underline</u></div><div><br></div><p>paragraph</p><h2>heading 2</h2><h3>heading 3</h3><h4>heading 4</h4><p></p><ul><li>bullet</li></ul><ol><li>number</li></ol><blockquote><span style=\"font-size: 0.875rem;\">quote</span></blockquote><br><p></p><p>left</p><p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta.</p><p style=\"text-align: center;\">center</p><p style=\"text-align: center;\">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta.</p><p style=\"text-align: right;\">right</p><p style=\"text-align: right;\">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta.</p><p style=\"text-align: justify;\">justify&nbsp;</p><p style=\"text-align: justify;\">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos ipsam quia rerum sit. Quas, optio error consectetur pariatur ipsam quae explicabo a impedit eos dicta.</p><p style=\"text-align: justify;\"><br></p><p style=\"text-align: justify;\">link&nbsp;<a href=\"https://github.com/ds-affirmexe/ngabar_yuk\">https://github.com/ds-affirmexe/ngabar_yuk</a></p>', '6a9ce111849f9.jpg', 1, '2026-09-06 00:17:13');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','super_admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `status` enum('aktif','nonaktif') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `password`, `role`, `status`, `created_at`) VALUES
(1, 'admin3232', 'Admin_Keren', '$2y$10$e1U5I2R9Ay1P9VhrhGHfQOZjhN599ZaLVZltM.4yl7Gvra0lfnz5u', 'super_admin', 'aktif', '2026-09-05 23:40:43'),
(3, 'denny caknan', 'dddnan', '$2y$10$lS.7HlyCHDbKlxdAHe2h7uDKqoWtgusjgp0ATrcz8TqOBztui6QvG', 'admin', 'aktif', '2026-09-06 00:16:24');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

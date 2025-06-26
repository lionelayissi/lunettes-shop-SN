-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 09, 2025 at 06:40 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lunettes_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `commande_details`
--

CREATE TABLE `commande_details` (
  `id` int(11) NOT NULL,
  `commande_id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL,
  `quantite` int(11) NOT NULL,
  `prix_unitaire` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `commande_details`
--

INSERT INTO `commande_details` (`id`, `commande_id`, `produit_id`, `quantite`, `prix_unitaire`) VALUES
(1, 1, 2, 2, '18000.00'),
(2, 1, 3, 2, '10000.00'),
(3, 1, 1, 1, '45000.00'),
(4, 2, 2, 1, '18000.00'),
(5, 2, 3, 1, '10000.00'),
(6, 3, 2, 2, '18000.00'),
(7, 3, 3, 4, '10000.00'),
(8, 3, 1, 2, '45000.00'),
(9, 4, 1, 1, '45000.00'),
(10, 4, 2, 1, '18000.00'),
(11, 4, 3, 1, '10000.00'),
(12, 5, 2, 1, '18000.00'),
(13, 6, 3, 2, '10000.00'),
(14, 6, 2, 2, '18000.00'),
(15, 7, 2, 1, '18000.00'),
(16, 8, 1, 1, '45000.00'),
(17, 8, 2, 1, '18000.00'),
(18, 8, 3, 1, '10000.00'),
(19, 9, 1, 1, '45000.00'),
(20, 9, 2, 1, '18000.00'),
(21, 9, 3, 1, '10000.00'),
(22, 10, 1, 1, '45000.00'),
(23, 11, 1, 1, '45000.00'),
(24, 11, 2, 2, '18000.00'),
(25, 12, 1, 1, '45000.00'),
(26, 13, 1, 1, '45000.00'),
(27, 13, 2, 1, '18000.00'),
(28, 14, 2, 1, '18000.00'),
(29, 15, 1, 1, '45000.00'),
(30, 16, 3, 1, '10000.00'),
(31, 17, 3, 1, '10000.00'),
(32, 18, 3, 1, '10000.00'),
(33, 19, 2, 1, '18000.00'),
(34, 20, 1, 1, '45000.00'),
(35, 20, 2, 1, '18000.00'),
(36, 20, 3, 1, '10000.00'),
(37, 21, 1, 1, '45000.00'),
(38, 22, 1, 2, '45000.00'),
(39, 22, 2, 2, '18000.00'),
(40, 22, 3, 1, '10000.00'),
(41, 23, 3, 4, '10000.00'),
(42, 23, 2, 4, '18000.00'),
(43, 23, 1, 4, '45000.00'),
(44, 24, 1, 3, '45000.00'),
(45, 24, 3, 3, '10000.00'),
(46, 24, 2, 1, '18000.00'),
(47, 25, 1, 3, '45000.00'),
(48, 25, 2, 3, '18000.00'),
(49, 25, 3, 12, '10000.00'),
(50, 26, 2, 2, '18000.00'),
(51, 26, 1, 1, '45000.00'),
(52, 26, 3, 1, '10000.00'),
(53, 27, 1, 1, '45000.00'),
(54, 27, 2, 1, '18000.00'),
(55, 28, 2, 2, '18000.00'),
(56, 28, 6, 2, '15000.00'),
(57, 29, 7, 3, '5000.00'),
(58, 29, 1, 1, '45000.00'),
(59, 29, 2, 2, '18000.00'),
(60, 29, 3, 3, '10000.00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `commande_details`
--
ALTER TABLE `commande_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `commande_id` (`commande_id`),
  ADD KEY `produit_id` (`produit_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `commande_details`
--
ALTER TABLE `commande_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `commande_details`
--
ALTER TABLE `commande_details`
  ADD CONSTRAINT `commande_details_ibfk_1` FOREIGN KEY (`commande_id`) REFERENCES `commandes` (`id`),
  ADD CONSTRAINT `commande_details_ibfk_2` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 14, 2026 at 10:07 PM
-- Server version: 11.8.8-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u152620751_kingsleykhord`
--

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE `plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tier` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `price_ngn` decimal(10,2) DEFAULT NULL,
  `price_usd` decimal(10,2) DEFAULT NULL,
  `price_eur` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `background` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `paystack_product_id` varchar(255) NOT NULL,
  `stripe_product_id` varchar(255) DEFAULT NULL,
  `paypal_product_id` varchar(255) DEFAULT NULL,
  `paypal_plan_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`paypal_plan_ids`)),
  `product_id` varchar(255) DEFAULT NULL,
  `price_id` varchar(255) DEFAULT NULL,
  `agent` enum('paystack','stripe','paypal') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`id`, `tier`, `type`, `price_ngn`, `price_usd`, `price_eur`, `image`, `background`, `created_at`, `updated_at`, `paystack_product_id`, `stripe_product_id`, `paypal_product_id`, `paypal_plan_ids`, `product_id`, `price_id`, `agent`) VALUES
(1, 'standard', 'monthly', 38000.00, 28.00, 25.00, '/icons/icon.png', '', '2026-06-26 11:11:36', '2026-08-05 19:20:53', 'PLN_brzt8hexwspqu9p', 'price_1TosNAB0pqpbXiCi02nYWMfZ', 'PROD-3PK11385EB4507523', '{\"EUR\":\"P-6DN4031633408434XNJ3DBAA\",\"USD\":\"P-8ND1915612609040FNJ3C7NI\"}', 'price_1TmEw0B0pqpbXiCi4WJifhGG', NULL, NULL),
(2, 'premium', 'monthly', 78000.00, 57.00, 50.00, '/icons/price2.png', '/images/Background.jpg', '2026-06-26 11:11:36', '2026-08-04 21:15:13', 'PLN_mb88lum57cm9dyy', 'price_1TmF19B0pqpbXiCiG7cPdcU6', 'PROD-9GY71630L44257120', '{\"EUR\":\"P-4NU94981AR335624PNJ7ZI3Y\",\"USD\":\"P-4PT51499LD5653846NJ7ZI3A\"}', NULL, NULL, NULL),
(3, 'Standard 3-months', 'quarterly', 105000.00, 75.00, 68.00, '/icons/icon.png', '', '2026-06-26 11:11:37', '2026-06-26 11:11:37', 'PLN_zo5n17vwoeom6is', 'price_1TmFAaB0pqpbXiCikJDqSE8T', 'PROD-54R39287UT532922V', '{\"EUR\":\"P-4DG0560861569861WNJ7ZI5Q\",\"USD\":\"P-34A21887C3695471TNJ7ZI4Y\"}', NULL, NULL, NULL),
(4, 'Premium 3-months', 'quarterly', 210000.00, 154.00, 135.00, '/icons/price2.png', '/images/Background.jpg', '2026-06-26 11:11:37', '2026-06-26 11:11:37', 'PLN_6x7ktvd81doif9t', 'price_1TmFHFB0pqpbXiCiKso7J8tY', 'PROD-7CB464156J138843V', '{\"EUR\":\"P-2EP0740883223181DNJ7ZI7A\",\"USD\":\"P-9UA919582U8783601NJ7ZI6I\"}', NULL, NULL, NULL),
(5, 'standard', 'yearly', 330000.00, 235.00, 210.00, '/icons/icon.png', '', '2026-06-26 11:11:37', '2026-06-26 11:11:37', 'PLN_l4u5qel3amq3ukh', 'price_1TmFUaB0pqpbXiCiQS6SALlX', 'PROD-9KA514689H476474Y', '{\"EUR\":\"P-6S718815GF0623509NJ7ZJAY\",\"USD\":\"P-32629278E85476021NJ7ZJAA\"}', NULL, NULL, NULL),
(6, 'premium', 'yearly', 650000.00, 480.00, 420.00, '/icons/price2.png', '/images/Background.jpg', '2026-06-26 11:11:37', '2026-06-26 11:11:37', 'PLN_x7f11lzl66061dg', 'price_1TmFapB0pqpbXiCiWwZpbUDc', 'PROD-37M234696R653872D', '{\"EUR\":\"P-8DL36764PT466270CNJ7ZJCQ\",\"USD\":\"P-4362833433754532DNJ7ZJBY\"}', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

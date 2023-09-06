-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: sql6.webzdarma.cz:3306
-- Generation Time: Sep 07, 2023 at 12:27 AM
-- Server version: 8.0.33-25
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `whitefoxbank7108`
--

-- --------------------------------------------------------

--
-- Table structure for table `cards`
--

CREATE TABLE `cards` (
  `card_id` int NOT NULL,
  `user_id` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `info` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `color` smallint NOT NULL,
  `currency` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cards`
--

INSERT INTO `cards` (`card_id`, `user_id`, `name`, `info`, `color`, `currency`) VALUES
(15, '1', 'Maťa', 'Prachy na cestu', 2, 'EUR'),
(16, '8', 'ondrej je pica', 'penize co me dluzi ondrej\r\n', 1, 'EUR'),
(17, '8', 'ondrej je pica', 'penize co me dluzi ondrej\r\n', 1, 'EUR'),
(18, '1', 'Přijmy', 'Od domacností', 5, 'EUR'),
(32, '11', 'Bani pentru Dăni', '$', 4, 'MDL'),
(20, '9', 'kartička', 'mám moc rád jídlo a kafe, koláčky jsou fajn', 4, 'EUR'),
(44, '14', 'Banka', 'Přijem z práce', 3, 'CZK'),
(38, '13', 'ČSOB', 'Banka', 4, 'CZK'),
(47, '1', 'Raiffeisen', 'Main account', 2, 'CZK'),
(46, '1', 'Revolut', 'Second account - for travel', 4, 'CZK'),
(37, '12', 'Prijem ze Svycarska', 'sjdhf', 1, 'CHF'),
(26, '10', 'Swiss ❤️', 'MONEY!!', 1, 'CHF'),
(28, '10', 'Brva', '', 3, 'EUR'),
(29, '', 'Brva', '', 3, 'EUR'),
(31, '9', 'Neblíkej kámo', 'HEj hou', 3, 'CHF');

-- --------------------------------------------------------

--
-- Table structure for table `money`
--

CREATE TABLE `money` (
  `money_id` int NOT NULL,
  `card_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `money` float(10,2) DEFAULT NULL,
  `date` date DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

--
-- Dumping data for table `money`
--

INSERT INTO `money` (`money_id`, `card_id`, `user_id`, `money`, `date`) VALUES
(31, 44, 14, 179.24, '2023-05-16'),
(34, 46, 1, 4.63, '2023-05-24'),
(12, 17, 8, -500.00, '2023-03-22'),
(22, 32, 11, 4344.67, '2023-03-30'),
(28, 38, 13, 400.00, '2023-05-06'),
(25, 18, 1, -22.00, '2023-04-05'),
(27, 37, 12, 88.00, '2023-04-12'),
(33, 47, 1, 4986.31, '2023-06-02'),
(18, 26, 10, -314.45, '2023-03-29'),
(20, 15, 1, 409.85, '2023-05-16'),
(21, 31, 9, 45.84, '2023-03-30');

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `transaction_id` int NOT NULL,
  `money_id` int DEFAULT NULL,
  `card_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `suma` float(20,2) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `money` float(20,2) DEFAULT NULL,
  `description` varchar(50) COLLATE utf8mb3_czech_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

--
-- Dumping data for table `transaction`
--

INSERT INTO `transaction` (`transaction_id`, `money_id`, `card_id`, `user_id`, `suma`, `date`, `money`, `description`) VALUES
(61, NULL, 15, 1, -89.60, '2023-04-05', 116.76, 'Penize na tábor'),
(60, NULL, 15, 1, 76.00, '2023-04-05', 40.76, 'Výdělek'),
(127, NULL, 47, 1, -60.00, '2023-05-28', 318.76, 'PID - jizdenky'),
(126, NULL, 47, 1, -58.00, '2023-05-28', 376.76, 'Letní bar - FNV'),
(125, NULL, 47, 1, -152.00, '2023-05-28', 528.76, 'Štěpanka Pizza'),
(41, NULL, 32, 11, -200.00, '2023-03-30', 2000.00, 'Schuze'),
(40, NULL, 32, 11, 2000.00, '2023-03-30', 0.00, 'Bicicletă'),
(39, NULL, 31, 9, 33.50, '2023-03-30', 12.34, 'Peníze na rohlík'),
(38, NULL, 31, 9, 12.34, '2023-03-30', 0.00, 'Příspěvek od taťky '),
(37, NULL, 15, 1, 786.98, '2023-03-30', 0.00, 'Penize od tatky'),
(29, NULL, 26, 10, 198.00, '2023-03-29', 0.00, 'Jenea'),
(30, NULL, 26, 10, 347.65, '2023-03-29', 198.00, 'Bunica'),
(31, NULL, 26, 10, -100.00, '2023-03-29', 545.65, 'Mincare'),
(32, NULL, 26, 10, 237.90, '2023-03-29', 445.65, 'Ulala'),
(33, NULL, 26, 10, -398.00, '2023-03-29', 683.55, 'Kytara - kapodastr'),
(34, NULL, 26, 10, -600.00, '2023-03-29', 285.55, 'Ounou'),
(42, NULL, 32, 11, -2000.00, '2023-03-30', 1800.00, 'Dluhy'),
(43, NULL, 32, 11, 219.67, '2023-03-30', -200.00, 'Ulala'),
(44, NULL, 32, 11, -675.00, '2023-03-30', 19.67, 'Jenea'),
(45, NULL, 32, 11, 5000.00, '2023-03-30', -655.33, 'Bancă'),
(46, NULL, 15, 1, 178.23, '2023-04-05', 786.98, 'Ou shit'),
(47, NULL, 15, 1, -0.15, '2023-04-05', 965.21, 'Rohlik'),
(48, NULL, 15, 1, -15.00, '2023-04-05', 965.06, 'Oběd'),
(49, NULL, 15, 1, 50.00, '2023-04-05', 950.06, 'Brigadka'),
(50, NULL, 15, 1, 80.00, '2023-04-05', 1000.06, 'Brigadka'),
(51, NULL, 15, 1, -20.00, '2023-04-05', 1080.06, 'Večeře'),
(52, NULL, 15, 1, -120.00, '2023-04-05', 1060.06, 'Dárek pro Barču'),
(53, NULL, 15, 1, 5.00, '2023-04-05', 940.06, 'Tak lidi by neměli psat do toho slohovky no'),
(54, NULL, 15, 1, 5.00, '2023-04-05', 945.06, 'Tak lidi by neměli psat do toho slohovky no'),
(55, NULL, 15, 1, -211.30, '2023-04-05', 950.06, 'Daně'),
(56, NULL, 15, 1, -698.00, '2023-04-05', 738.76, 'lol broke'),
(62, NULL, 15, 1, 40.00, '2023-04-05', 27.16, 'Brigadka kávarna'),
(63, NULL, 15, 1, 215.00, '2023-04-05', 67.16, 'Měsiční kapesny'),
(64, NULL, 15, 1, -2.00, '2023-04-05', 282.16, 'Zmrzka'),
(65, NULL, 15, 1, -60.00, '2023-04-05', 280.16, 'Gameska - Hogwards Legacy'),
(66, NULL, 15, 1, -4.99, '2023-04-05', 220.16, 'Gameska - Euro Truck Simulator'),
(67, NULL, 15, 1, -20.99, '2023-04-05', 215.17, 'Gameska - Fifa 23'),
(68, NULL, 15, 1, -59.99, '2023-04-05', 194.18, 'Gameska - Resident Evil 4'),
(69, NULL, 15, 1, -29.99, '2023-04-05', 134.19, 'Gameska - Kingdome Come: Deliverance'),
(70, NULL, 15, 1, -9.89, '2023-04-05', 104.20, 'Gameska - Mafia II'),
(71, NULL, 15, 1, -8.99, '2023-04-05', 94.31, 'Gameska - Borderlands 3'),
(72, NULL, 15, 1, -29.99, '2023-04-05', 85.32, 'Gameska - Far Cry 4'),
(73, NULL, 15, 1, -9.99, '2023-04-05', 55.33, 'R6S - Body'),
(74, NULL, 15, 1, -12.00, '2023-04-05', 45.34, 'Cesta do Aše'),
(75, NULL, 15, 1, -12.00, '2023-04-05', 33.34, 'Cesta z Aše'),
(76, NULL, 15, 1, -7.50, '2023-04-05', 21.34, 'Na zelený pivo'),
(77, NULL, 15, 1, 40.00, '2023-04-05', 13.84, 'Kapesny'),
(78, NULL, 15, 1, -19.99, '2023-04-05', 53.84, 'DLC - Hogwarts Legacy: Dark Arts Pack'),
(79, NULL, 15, 1, -4.00, '2023-04-05', 33.85, 'Třidní fotky'),
(80, NULL, 15, 1, 190.00, '2023-04-05', 29.85, 'RedBull'),
(81, NULL, 15, 1, 90.00, '2023-04-05', 219.85, 'Monster'),
(82, NULL, 18, 1, 23.00, '2023-04-05', 0.00, 'Prachy!!'),
(83, NULL, 18, 1, -45.00, '2023-04-05', 23.00, 'Oka'),
(124, NULL, 47, 1, -60.00, '2023-05-28', 588.76, 'PID - jizdenky'),
(123, NULL, 47, 1, 359.00, '2023-05-28', 229.76, 'Jizdenky na vlak od Davky'),
(122, NULL, 47, 1, -538.00, '2023-05-28', 767.76, 'Jizdenky na vlak + Davka'),
(121, NULL, 47, 1, -35.00, '2023-05-28', 802.76, 'Automat - Brno hl.n'),
(120, NULL, 47, 1, -50.00, '2023-05-28', 852.76, 'Penize spolužakovy'),
(119, NULL, 47, 1, -150.00, '2023-05-28', 1002.76, 'Bar u veslá'),
(117, NULL, 46, 1, 4.63, '2023-05-24', 0.00, 'Zůstatek'),
(118, NULL, 47, 1, 1000.00, '2023-05-28', 2.76, 'Penize od tatky'),
(116, NULL, 47, 1, 2.76, '2023-05-24', 0.00, 'Zůstatek'),
(99, NULL, 37, 12, 100.00, '2023-04-12', 0.00, 'Papiry'),
(100, NULL, 37, 12, -12.00, '2023-04-12', 100.00, 'Cesta'),
(101, NULL, 38, 13, 500.00, '2023-05-06', 0.00, 'Dar od Dana'),
(102, NULL, 38, 13, -100.00, '2023-05-06', 500.00, 'Bubbletea'),
(103, NULL, 15, 1, 100.00, '2023-05-16', 309.85, 'Mateo'),
(108, NULL, 44, 14, -120.76, '2023-05-16', 300.00, 'Lidl svačina'),
(107, NULL, 44, 14, 300.00, '2023-05-16', 0.00, 'Penize od babičky'),
(128, NULL, 47, 1, 60.00, '2023-05-28', 258.76, 'PID - Davka'),
(129, NULL, 47, 1, -209.00, '2023-05-28', 318.76, 'Jizdenky do Prahy - Adam Bušina'),
(130, NULL, 47, 1, 5.00, '2023-05-28', 109.76, 'Přispěvek Davka'),
(131, NULL, 47, 1, -113.00, '2023-05-28', 114.76, 'Uber - Marek Kocmanek'),
(132, NULL, 47, 1, 500.00, '2023-05-29', 1.76, 'Penize od tatky'),
(133, NULL, 47, 1, -200.00, '2023-05-29', 501.76, 'Bar - Zelný trh'),
(134, NULL, 47, 1, -94.75, '2023-05-29', 301.76, 'Cesta do Aše - FlixBus'),
(135, NULL, 47, 1, -205.00, '2023-05-29', 207.01, 'Cesta do Aše vlák'),
(136, NULL, 47, 1, 1000.00, '2023-05-29', 2.01, 'Penize od tatky'),
(137, NULL, 47, 1, -650.00, '2023-05-29', 1002.01, 'Doktor - řidičský průkaz'),
(138, NULL, 47, 1, -80.00, '2023-05-29', 352.01, 'Zmrzka pro Barču'),
(139, NULL, 47, 1, -139.00, '2023-05-29', 272.01, 'Bubbletea - Brno'),
(140, NULL, 47, 1, -30.00, '2023-05-29', 133.01, 'PID - jizdenky'),
(141, NULL, 47, 1, -93.00, '2023-05-29', 103.01, 'Svačina - Billa'),
(142, NULL, 47, 1, -0.80, '2023-05-29', 10.01, 'Dorovnání'),
(143, NULL, 47, 1, 6000.00, '2023-06-02', 9.21, 'Kapesny od tatky'),
(144, NULL, 47, 1, -400.00, '2023-06-02', 6009.21, 'ATM'),
(145, NULL, 47, 1, -160.00, '2023-06-02', 5609.21, 'Kebab - Aš'),
(146, NULL, 47, 1, -358.00, '2023-06-02', 5449.21, 'Jizdenky Brno - vlák'),
(147, NULL, 47, 1, -14.90, '2023-06-02', 5091.21, 'Tesco'),
(148, NULL, 47, 1, -90.00, '2023-06-02', 5076.31, 'Nanda - penize');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int NOT NULL,
  `user_key` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `user_name` char(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `user_surname` char(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `nickname` char(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `phone` int DEFAULT NULL,
  `password` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `admin` tinyint DEFAULT NULL,
  `ban_status` tinyint DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `user_key`, `user_name`, `user_surname`, `nickname`, `email`, `phone`, `password`, `admin`, `ban_status`) VALUES
(1, 'jhfd6%$', 'Daniel', 'Tarelunga', 'MDfun', 'danilegos.t@gmail.com', 775696129, 'pepega', NULL, NULL),
(2, 'dkjf&875', 'Roman', 'Tarelunga', 'Peacemaker', 'r.tarelunga@yahoo.com', 775698236, '12345', NULL, NULL),
(3, 'lkf87@', 'LeoÅ¡', 'Gjumija', 'lTechnik', 'leos.gjumija@gmail.com', 778425325, 'technik22', NULL, NULL),
(4, '4od461$', 'admin', 'admin', 'admin', 'admin@admin.com', 123456789, 'admin', 1, NULL),
(5, '5#hh5', 'Šimon', 'Krejčí', 'šimišimi', 'sima@educanet.cz', 775345896, 'čičo', NULL, NULL),
(6, 'ey7*n#3tse', 'Borec', 'Největší', 'Kalwich', 'kalwich@gmail.com', 111111111, '123', NULL, NULL),
(7, 'tvrgfw', 'Petr', 'Pavel', 'General', 'daniel.tarelunga@', 775696129, 'pepega', NULL, 1),
(8, '7x4@k3*', 'Leos', 'Gjumija', 'gjumle', 'gjumle@proton.me', 606510144, 'gjumle1', NULL, NULL),
(9, '9v$ophhj', 'Jakub', 'Halík', 'Kubys', 'jakubhalik1@gmail.com', 777274108, 'Jakubin', NULL, NULL),
(10, 'k%d2b*g&i9', 'Real', 'User', 'Best vlk', 'da', 775696129, 'pepega', NULL, 1),
(12, 'rw76s', 'Daniel', 'Tarelunga', 'Frajersdsdsd', 'daniel.tarlunga@educanet.cz', 123456789, '1234', NULL, NULL),
(11, 'ps#119', 'Roman', 'Tarelunga', 'Peacemaker v.2', 'romantare@outlook.com', 123456789, 'c1o88*5u', NULL, 0),
(13, '52$c5vr%', 'Barbora', 'Nemejc', 'barosaurus', 'nemejcova.b@seznam.cz', 777820931, '14861486', NULL, NULL),
(14, 'ytzf#**8!1a', 'Nelepší', 'Typek', 'Frajer', 'frajer.jenej@educanet.cz', 775696129, '1234', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cards`
--
ALTER TABLE `cards`
  ADD PRIMARY KEY (`card_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `money`
--
ALTER TABLE `money`
  ADD PRIMARY KEY (`money_id`),
  ADD KEY `card_id` (`card_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`transaction_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cards`
--
ALTER TABLE `cards`
  MODIFY `card_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `money`
--
ALTER TABLE `money`
  MODIFY `money_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `transaction_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=149;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

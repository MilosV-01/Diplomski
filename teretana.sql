-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 12, 2024 at 05:07 AM
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
-- Database: `teretana`
--

-- --------------------------------------------------------

--
-- Table structure for table `clanarina`
--

CREATE TABLE `clanarina` (
  `id` int(11) NOT NULL,
  `naziv` varchar(100) NOT NULL,
  `cena` decimal(10,2) DEFAULT NULL,
  `trajanje` varchar(20) DEFAULT NULL,
  `opis` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clanarina`
--

INSERT INTO `clanarina` (`id`, `naziv`, `cena`, `trajanje`, `opis`) VALUES
(8, 'Mesečna članarina', 2500.00, '1 mesec', 'Savršena za one koji žele da isprobaju naše usluge ili imaju kratkoročne ciljeve. Neograničen pristup svim sadržajima tokom meseca.'),
(9, ' Kvartalna članarina', 5500.00, '3 meseca', 'Idealna za one koji planiraju da se posvete teretani na srednjem roku. Uključuje sve pogodnosti i popust na mesečnu cenu.'),
(10, 'Polugodišnja članarina', 13000.00, '6 meseci', ' Za ozbiljnije posvećene članove koji žele dugoročnu posvećenost. Pruža dodatne beneficije i značajan popust na mesečne rate.'),
(11, 'Godišnja članarina', 23000.00, '12 meseci', 'Najbolja opcija za posvećene članove koji žele maksimalne uštede. Uključuje neograničen pristup svim sadržajima i dodatne privilegije.');

-- --------------------------------------------------------

--
-- Table structure for table `klijent`
--

CREATE TABLE `klijent` (
  `id` int(11) NOT NULL,
  `ime` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefon` varchar(20) DEFAULT NULL,
  `adresa` varchar(255) DEFAULT NULL,
  `lozinka` varchar(255) DEFAULT NULL,
  `role` enum('admin','klijent') NOT NULL DEFAULT 'klijent',
  `datum_pocetka` date DEFAULT NULL,
  `datum_kraja` date DEFAULT NULL,
  `clanarina_id` int(11) DEFAULT NULL,
  `kontakt_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `klijent`
--

INSERT INTO `klijent` (`id`, `ime`, `email`, `telefon`, `adresa`, `lozinka`, `role`, `datum_pocetka`, `datum_kraja`, `clanarina_id`, `kontakt_id`) VALUES
(42, 'admin', 'admin@admin.com', '0', '0', '$2y$10$u6V3/fdIwU53J/xJ4QRZ9OTh.7ZHGD/GgMuZQbO.W2SA1Bn97Qv0i', 'admin', NULL, NULL, NULL, NULL),
(44, 'Nikola Vukmirovic', 'nikola@gmail.com', '0101011', 'V.K', '$2y$10$2ZnHkQxFVUeCBwlM.gyfR..3L7c/tEFSpbg3k8sZSNGeNiZcxQLei', 'klijent', '2024-08-31', '2024-12-01', NULL, NULL),
(46, 'Aleksa Blagic', 'aleksa03@gmail.com', '0120312', 'V.K.', '$2y$10$6uo8MzjBjNMyclB8SjQWzelJnnHCyy8JVDdAr9QNN3BZ6v9aJQpxi', 'klijent', '2024-09-06', '2025-09-06', NULL, NULL),
(52, 'Milos', 'vukmirovicai@gmail.com', '0123124123', 'V.K. 61', 'Milos', 'klijent', '2024-08-21', '2024-09-21', NULL, NULL),
(53, 'Milos Vukmirovic', 'vukmirovicmilos@gmail.com', '061232131', 'V.K. 1212', '$2y$10$td4m9Ql5rUpcD1k.W972au2NP7Ik439fimJKEqL46p8DrJ0gO8Ioe', 'klijent', NULL, NULL, NULL, NULL),
(54, 'Nikola Vukmirovic', 'nvukmirovic0@gmail.com', '101011', 'V.K', '$2y$10$udEw3yfrPTF4HoBOQSRvIutaqt2P/BpsJ0/OFCBNPI9z6XHOipwCm', 'klijent', '2024-08-24', '2025-08-24', NULL, NULL),
(55, 'Mirko Mirkovic', 'mirko@gmail.com', '912312312', 'M.M. 21', '$2y$10$gYXEYU1gA0T46iLjWyqRAeukLtJF6GmoZ/sLwCe52OI.xr10mMgjO', 'klijent', NULL, NULL, NULL, NULL),
(56, 'Petar Petrovic', 'petar@gmail.com', '0123124123', 'V.K. 1212', '$2y$10$LXvEWOwHk8csRcTmR9Vj4uKPYxTobqw9o9/nCcUUKqjqhKkE.CNUu', 'klijent', '2024-09-06', '2024-12-06', NULL, NULL),
(58, 'Milos', 'milossomi200116@gmail.com', '0123124123', 'Vuka Karadzica ', '$2y$10$Vgejfa8lN8V7HMIjqotztO9aLqt6Dt0EJinJZztwSBq7DFHR4Zu1i', 'klijent', '2024-09-07', '2025-03-07', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `klijent_plan`
--

CREATE TABLE `klijent_plan` (
  `id` int(11) NOT NULL,
  `klijent_id` int(11) DEFAULT NULL,
  `plan_id` int(11) DEFAULT NULL,
  `datum_pocetka` date DEFAULT NULL,
  `trajanje` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `klijent_plan`
--

INSERT INTO `klijent_plan` (`id`, `klijent_id`, `plan_id`, `datum_pocetka`, `trajanje`) VALUES
(54, 56, 8, '2024-09-04', 3),
(55, 44, 4, '2024-09-04', 4),
(56, 44, 5, '2024-09-04', 4),
(57, 58, 5, '2024-09-08', 3);

-- --------------------------------------------------------

--
-- Table structure for table `kontakt`
--

CREATE TABLE `kontakt` (
  `id` int(11) NOT NULL,
  `ime` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `tekst` text NOT NULL,
  `procitana` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kontakt`
--

INSERT INTO `kontakt` (`id`, `ime`, `subject`, `email`, `tekst`, `procitana`) VALUES
(6, 'Petar Petrovic', '', 'aleksa03@gmail.com', 'kada mogu da zakazem trening', 1),
(7, 'Milos', '', 'vukmirovicai@gmail.com', 'Kada mogu zakazati trening ', 0),
(9, 'Nikola Vukmirovic', '', 'nvukmirovic@gmail.com', 'Kada mogu zakazati svoj prvi termin?', 0),
(10, 'Nikola Vukmirovic', '', 'nvukmirovic0@gmail.com', 'Pozdrav Milose...\r\nKada mogu da se upisem?', 0);

-- --------------------------------------------------------

--
-- Table structure for table `plan`
--

CREATE TABLE `plan` (
  `id` int(11) NOT NULL,
  `naziv` varchar(100) NOT NULL,
  `opis` text DEFAULT NULL,
  `trajanje` varchar(50) DEFAULT NULL,
  `cena` decimal(10,2) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0,
  `prikazi_na_pocetnoj` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `plan`
--

INSERT INTO `plan` (`id`, `naziv`, `opis`, `trajanje`, `cena`, `status`, `prikazi_na_pocetnoj`) VALUES
(4, 'Osnovni Plan', 'Idealno za početnike koji žele da se upoznaju sa osnovama vežbanja. Uključuje osnovne vežbe za celo telo.', '4 meseca', 3000.00, 0, 1),
(5, 'Napredni Plan', 'Pogodan za one koji imaju prethodno iskustvo i žele da unaprede svoju fizičku kondiciju. Uključuje intenzivne treninge sa fokusom na različite mišićne grupe.', '3 meseca', 8000.00, 0, 1),
(8, 'Ekspert Plan', 'Za iskusne vežbače koji žele maksimalne rezultate. Uključuje personalizovane treninge i napredne tehnike.', '6 meseca', 20000.00, 1, 1),
(9, 'Kombinovani Plan', 'Kombinuje treninge snage i kardio vežbi. Pogodan za one koji žele sveobuhvatan pristup vežbanju.', '2 meseca', 5000.00, 0, 0),
(10, 'Plan za Mršavljenje', 'Fokusiran na sagorevanje masti i poboljšanje metabolizma. Uključuje vežbe visokog intenziteta i nutritivne savete.', '1 meseci', 3500.00, 0, 0),
(11, 'Plan za Povećanje Snage', 'Dizajniran za one koji žele da povećaju mišićnu masu i snagu. Uključuje teške treninge sa fokusom na podizanje težine.', '3 meseca', 10000.00, 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `clanarina`
--
ALTER TABLE `clanarina`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `klijent`
--
ALTER TABLE `klijent`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_clanarina` (`clanarina_id`),
  ADD KEY `fk_kontakt` (`kontakt_id`);

--
-- Indexes for table `klijent_plan`
--
ALTER TABLE `klijent_plan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `klijent_id` (`klijent_id`),
  ADD KEY `plan_id` (`plan_id`);

--
-- Indexes for table `kontakt`
--
ALTER TABLE `kontakt`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `plan`
--
ALTER TABLE `plan`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `clanarina`
--
ALTER TABLE `clanarina`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `klijent`
--
ALTER TABLE `klijent`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `klijent_plan`
--
ALTER TABLE `klijent_plan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `kontakt`
--
ALTER TABLE `kontakt`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `plan`
--
ALTER TABLE `plan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `klijent`
--
ALTER TABLE `klijent`
  ADD CONSTRAINT `fk_clanarina` FOREIGN KEY (`clanarina_id`) REFERENCES `clanarina` (`id`),
  ADD CONSTRAINT `fk_kontakt` FOREIGN KEY (`kontakt_id`) REFERENCES `kontakt` (`id`);

--
-- Constraints for table `klijent_plan`
--
ALTER TABLE `klijent_plan`
  ADD CONSTRAINT `klijent_plan_ibfk_1` FOREIGN KEY (`klijent_id`) REFERENCES `klijent` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `klijent_plan_ibfk_2` FOREIGN KEY (`plan_id`) REFERENCES `plan` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

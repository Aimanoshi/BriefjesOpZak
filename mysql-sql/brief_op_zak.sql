-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: mysqldb
-- Gegenereerd op: 05 dec 2020 om 02:03
-- Serverversie: 5.7.32
-- PHP-versie: 7.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `brief_op_zak`
--
CREATE DATABASE IF NOT EXISTS `brief_op_zak` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `brief_op_zak`;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `channels`
--

CREATE TABLE `channels` (
  `Id` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `description` varchar(500) NOT NULL,
  `organisations_Id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden geëxporteerd voor tabel `channels`
--

INSERT INTO `channels` (`Id`, `name`, `description`, `organisations_Id`) VALUES
(1, '1ICT2', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 1),
(2, '2ICT2', 'Sed ullamcorper morbi tincidunt ornare massa. Lectus vestibulum mattis ullamcorper velit sed ullamcorper morbi. Morbi tristique senectus et netus et malesuada fames ac turpis. Elit eget gravida cum sociis. Augue eget arcu dictum varius. Aenean pharetra magna ac placerat vestibulum lectus mauris. Nulla porttitor massa id neque aliquam. Blandit cursus risus at ultrices. At elementum eu facilisis sed odio morbi quis commodo. Diam vel quam elementum pulvinar.', 1),
(3, 'Zwemklas', 'Donec pharetra justo vel ex interdum, vel commodo sapien tempor. Sed consequat tristique velit, non venenatis purus sollicitudin nec. Pellentesque posuere lorem in felis ultricies porta. Sed nec euismod orci. Nullam nulla nisl, semper id dui ut, dignissim condimentum mi. Nulla in eros vel eros tempus cursus sit amet sit amet ligula. Cras at euismod odio.', 2);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `messages`
--

CREATE TABLE `messages` (
  `Id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `added_date` date NOT NULL,
  `channels_Id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden geëxporteerd voor tabel `messages`
--

INSERT INTO `messages` (`Id`, `title`, `description`, `added_date`, `channels_Id`) VALUES
(1, 'Eerste les', 'Sed ullamcorper morbi tincidunt ornare massa. Lectus vestibulum mattis ullamcorper velit sed ullamcorper morbi. Morbi tristique senectus et netus et malesuada fames ac turpis. Elit eget gravida cum sociis. Augue eget arcu dictum varius. Aenean pharetra magna ac placerat vestibulum lectus mauris. Nulla porttitor massa id neque aliquam. Blandit cursus risus at ultrices. At elementum eu facilisis sed odio morbi quis commodo. Diam vel quam elementum pulvinar.', '2020-11-15', 1),
(2, 'Tweede les', 'Commodo viverra maecenas accumsan lacus vel facilisis volutpat est. Vulputate mi sit amet mauris commodo. Leo in vitae turpis massa sed elementum tempus. Facilisi nullam vehicula ipsum a. Nunc sed velit dignissim sodales. Congue eu consequat ac felis donec et. Dictumst vestibulum rhoncus est pellentesque elit. Leo urna molestie at elementum. Eget sit amet tellus cras adipiscing enim. Eget arcu dictum varius duis at. Nunc congue nisi vitae suscipit tellus mauris. Risus nullam eget felis eget nunc lobortis mattis aliquam faucibus.\n\n', '2020-12-01', 1);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `organisations`
--

CREATE TABLE `organisations` (
  `Id` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `address` varchar(50) NOT NULL,
  `postcode` varchar(4) NOT NULL,
  `description` text,
  `users_Id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden geëxporteerd voor tabel `organisations`
--

INSERT INTO `organisations` (`Id`, `name`, `address`, `postcode`, `description`, `users_Id`) VALUES
(1, 'Odisee', 'Gebroeders de smetstraat 1', '9000', 'Technology is the passion that flows through the veins of Odisee\'s Ghent campus. We would love to share this passion with you!\n\nThat is wy Odisee invested in modern laboratories & infrasturctures on its Technology campus. You can also find a student restaurant with a large offer of hot meals, sandwiches & snacks.', 1),
(2, 'Stadhuid eeklo', 'Markt 12 Eeklo', '9900', 'Aenean lectus nibh, tempor id lacus et, tincidunt maximus urna. Aliquam laoreet mollis finibus. In ac nisl volutpat, faucibus mi ac, vehicula sem. Donec convallis hendrerit pharetra. Mauris nisl leo, maximus vel porttitor ornare, accumsan ut erat. Integer eu ante egestas, vehicula justo et, finibus ante. Vestibulum cursus lorem ex, et ullamcorper mauris molestie rhoncus.', 1);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `subscriptions`
--

CREATE TABLE `subscriptions` (
  `accepted` tinyint(1) DEFAULT NULL,
  `users_Id` int(11) NOT NULL,
  `channels_Id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden geëxporteerd voor tabel `subscriptions`
--

INSERT INTO `subscriptions` (`accepted`, `users_Id`, `channels_Id`) VALUES
(1, 2, 1),
(1, 2, 2),
(0, 3, 2);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `users`
--

CREATE TABLE `users` (
  `Id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `rol` enum('admin','user') NOT NULL,
  `wie` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden geëxporteerd voor tabel `users`
--

INSERT INTO `users` (`Id`, `email`, `password`, `rol`, `wie`) VALUES
(1, 'abasin@yahoo.com', '$2y$10$AOs.8MdBqaNq1GZM6DpTTONtjmYOchXHvFOCc9KBvRkzbpDtbNzdS', 'admin','Admin van Odisee'),
(2, 'aima@gmial.com', '$2y$10$AOs.8MdBqaNq1GZM6DpTTONtjmYOchXHvFOCc9KBvRkzbpDtbNzdS', 'user','Student van 1ICT2'),
(3, 'atal@yahoo.com', '$2y$10$u8D3GtHymhredjL9kqHeUOwWoZMDgQqEOXc44ZQVk3ZJohIbCjkze', 'user', 'Inwoner van Eeklo');

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `channels`
--
ALTER TABLE `channels`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `fk_channels_organisations1_idx` (`organisations_Id`);

--
-- Indexen voor tabel `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `fk_messages_channels1_idx` (`channels_Id`);

--
-- Indexen voor tabel `organisations`
--
ALTER TABLE `organisations`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `fk_organisations_users1_idx` (`users_Id`);

--
-- Indexen voor tabel `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`users_Id`,`channels_Id`),
  ADD KEY `fk_subscriptions_channels1_idx` (`channels_Id`);

--
-- Indexen voor tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--
ALTER TABLE `organisations`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT voor een tabel `messages`
--
--
-- AUTO_INCREMENT voor een tabel `channels`
--
ALTER TABLE `channels`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT voor een tabel `messages`
--
ALTER TABLE `messages`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT voor een tabel `users`
--
ALTER TABLE `users`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `channels`
--
ALTER TABLE `channels`
  ADD CONSTRAINT `fk_channels_organisations1` FOREIGN KEY (`organisations_Id`) REFERENCES `organisations` (`Id`);

--
-- Beperkingen voor tabel `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_messages_channels1` FOREIGN KEY (`channels_Id`) REFERENCES `channels` (`Id`);

--
-- Beperkingen voor tabel `organisations`
--
ALTER TABLE `organisations`
  ADD CONSTRAINT `fk_organisations_users1` FOREIGN KEY (`users_Id`) REFERENCES `users` (`Id`);

--
-- Beperkingen voor tabel `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `fk_subscriptions_channels1` FOREIGN KEY (`channels_Id`) REFERENCES `channels` (`Id`),
  ADD CONSTRAINT `fk_subscriptions_users1` FOREIGN KEY (`users_Id`) REFERENCES `users` (`Id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

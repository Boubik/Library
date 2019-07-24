-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Počítač: localhost
-- Vytvořeno: Stř 24. čec 2019, 16:57
-- Verze serveru: 10.1.37-MariaDB
-- Verze PHP: 7.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `knihovna`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `author`
--

CREATE TABLE `author` (
  `id` bigint(19) UNSIGNED NOT NULL,
  `f_name` varchar(45) NOT NULL,
  `l_name` varchar(45) NOT NULL,
  `bday` date NOT NULL,
  `country` char(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `author`
--

INSERT INTO `author` (`id`, `f_name`, `l_name`, `bday`, `country`) VALUES
(1, 'Jana', 'Hollanová', '1991-01-01', 'CZ');

-- --------------------------------------------------------

--
-- Struktura tabulky `book`
--

CREATE TABLE `book` (
  `id` bigint(19) UNSIGNED NOT NULL,
  `name` varchar(45) NOT NULL,
  `relase` year(4) NOT NULL,
  `language` char(2) NOT NULL,
  `ISBN` varchar(20) DEFAULT NULL,
  `pages` smallint(6) DEFAULT NULL,
  `img` varchar(200) NOT NULL,
  `room_name` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `book`
--

INSERT INTO `book` (`id`, `name`, `relase`, `language`, `ISBN`, `pages`, `img`, `room_name`) VALUES
(1, 'Stopařův průvodce po Galaxii', 1991, 'CZ', '80-207-0229-6', 304, 'https://www.databazeknih.cz/images_books/49_/49330/big_stoparuv-pruvodce-galaxii-stoparuv--0jS-49330.jpg', '14');

-- --------------------------------------------------------

--
-- Struktura tabulky `book_has_author`
--

CREATE TABLE `book_has_author` (
  `book_id` bigint(19) UNSIGNED NOT NULL,
  `author_id` bigint(19) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `book_has_author`
--

INSERT INTO `book_has_author` (`book_id`, `author_id`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `book_has_genres`
--

CREATE TABLE `book_has_genres` (
  `book_id` bigint(19) UNSIGNED NOT NULL,
  `genres_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `book_has_genres`
--

INSERT INTO `book_has_genres` (`book_id`, `genres_id`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `book_has_reservation`
--

CREATE TABLE `book_has_reservation` (
  `book_id` bigint(19) UNSIGNED NOT NULL,
  `reservation_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `genres`
--

CREATE TABLE `genres` (
  `id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `genres`
--

INSERT INTO `genres` (`id`, `name`) VALUES
(1, 'Sci-fi');

-- --------------------------------------------------------

--
-- Struktura tabulky `reservation`
--

CREATE TABLE `reservation` (
  `id` bigint(20) NOT NULL,
  `s-reservation` datetime NOT NULL,
  `e-reservation` datetime NOT NULL,
  `user_id` bigint(19) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `reservation`
--

INSERT INTO `reservation` (`id`, `s-reservation`, `e-reservation`, `user_id`) VALUES
(1, '2019-07-01 00:00:00', '2019-07-31 00:00:00', 1),
(2, '2019-07-01 00:00:00', '2019-07-22 00:00:00', 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `room`
--

CREATE TABLE `room` (
  `name` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `room`
--

INSERT INTO `room` (`name`) VALUES
('14');

-- --------------------------------------------------------

--
-- Struktura tabulky `user`
--

CREATE TABLE `user` (
  `id` bigint(19) UNSIGNED NOT NULL,
  `f_name` varchar(45) NOT NULL,
  `l_name` varchar(45) NOT NULL,
  `username` varchar(45) NOT NULL,
  `password` varchar(512) NOT NULL,
  `last_login` datetime NOT NULL,
  `ceated` datetime NOT NULL,
  `role` varchar(45) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `user`
--

INSERT INTO `user` (`id`, `f_name`, `l_name`, `username`, `password`, `last_login`, `ceated`, `role`) VALUES
(1, 'Jan', 'Chlouba', 'Boubik', 'ebe177c42eebd3046ccd71a97d77603e2da7d1a5a81964cffd7a2305f6b605633a1f1a73f8923d287f67bc14748edde70414a3bde6d87ec86b30fbe00e1853f4', '2019-07-22 15:48:44', '2019-07-22 15:48:44', 'admin'),
(3, 'kek', 'kek', 'kek', 'c9c81052a10ba0cf2449120994c4be5ab02ce588cff2f90be6a9fa31c262be8f8e7e0945d6c69d6a5f0f8005e3a7e7e14807843ceb4da92195b09e7830d53bc9', '2019-07-24 16:42:55', '2019-07-24 16:42:55', 'user');

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `author`
--
ALTER TABLE `author`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_book_room1_idx` (`room_name`);

--
-- Klíče pro tabulku `book_has_author`
--
ALTER TABLE `book_has_author`
  ADD PRIMARY KEY (`book_id`,`author_id`),
  ADD KEY `fk_book_has_author_author1_idx` (`author_id`),
  ADD KEY `fk_book_has_author_book1_idx` (`book_id`);

--
-- Klíče pro tabulku `book_has_genres`
--
ALTER TABLE `book_has_genres`
  ADD PRIMARY KEY (`book_id`,`genres_id`),
  ADD KEY `fk_book_has_genres_genres1_idx` (`genres_id`),
  ADD KEY `fk_book_has_genres_book1_idx` (`book_id`);

--
-- Klíče pro tabulku `book_has_reservation`
--
ALTER TABLE `book_has_reservation`
  ADD PRIMARY KEY (`book_id`,`reservation_id`),
  ADD KEY `fk_book_has_reservation_reservation1_idx` (`reservation_id`),
  ADD KEY `fk_book_has_reservation_book1_idx` (`book_id`);

--
-- Klíče pro tabulku `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reservation_user1_idx` (`user_id`);

--
-- Klíče pro tabulku `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`name`);

--
-- Klíče pro tabulku `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `author`
--
ALTER TABLE `author`
  MODIFY `id` bigint(19) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pro tabulku `book`
--
ALTER TABLE `book`
  MODIFY `id` bigint(19) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pro tabulku `genres`
--
ALTER TABLE `genres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pro tabulku `reservation`
--
ALTER TABLE `reservation`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pro tabulku `user`
--
ALTER TABLE `user`
  MODIFY `id` bigint(19) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `book`
--
ALTER TABLE `book`
  ADD CONSTRAINT `fk_book_room1` FOREIGN KEY (`room_name`) REFERENCES `room` (`name`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `book_has_author`
--
ALTER TABLE `book_has_author`
  ADD CONSTRAINT `fk_book_has_author_author1` FOREIGN KEY (`author_id`) REFERENCES `author` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_book_has_author_book1` FOREIGN KEY (`book_id`) REFERENCES `book` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `book_has_genres`
--
ALTER TABLE `book_has_genres`
  ADD CONSTRAINT `fk_book_has_genres_book1` FOREIGN KEY (`book_id`) REFERENCES `book` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_book_has_genres_genres1` FOREIGN KEY (`genres_id`) REFERENCES `genres` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `book_has_reservation`
--
ALTER TABLE `book_has_reservation`
  ADD CONSTRAINT `fk_book_has_reservation_book1` FOREIGN KEY (`book_id`) REFERENCES `book` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_book_has_reservation_reservation1` FOREIGN KEY (`reservation_id`) REFERENCES `reservation` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `fk_reservation_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

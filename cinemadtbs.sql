-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Φιλοξενητής: 127.0.0.1:3306
-- Χρόνος δημιουργίας: 30 Μάη 2021 στις 17:05:45
-- Έκδοση διακομιστή: 5.7.31
-- Έκδοση PHP: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Βάση δεδομένων: `cinemadtbs`
--

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `cancelledseats`
--

DROP TABLE IF EXISTS `cancelledseats`;
CREATE TABLE IF NOT EXISTS `cancelledseats` (
  `cs_ID` int(11) NOT NULL AUTO_INCREMENT,
  `seat` varchar(55) COLLATE utf8_unicode_ci NOT NULL,
  `hall_ID` int(11) NOT NULL,
  PRIMARY KEY (`cs_ID`),
  KEY `hall_ID` (`hall_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Άδειασμα δεδομένων του πίνακα `cancelledseats`
--

INSERT INTO `cancelledseats` (`cs_ID`, `seat`, `hall_ID`) VALUES
(1, 'seatG9', 6),
(2, 'seatF9', 6),
(3, 'seatG4', 6),
(4, 'seatF4', 6),
(5, 'seatD4', 6),
(6, 'seatE4', 6),
(7, 'seatC4', 6),
(8, 'seatB4', 6),
(9, 'seatA4', 6),
(10, 'seatE9', 6),
(11, 'seatD9', 6),
(12, 'seatC9', 6),
(13, 'seatB9', 6),
(14, 'seatA9', 6),
(15, 'seatG9', 7),
(16, 'seatF9', 7),
(17, 'seatG4', 7),
(18, 'seatF4', 7),
(19, 'seatD4', 7),
(20, 'seatE4', 7),
(21, 'seatC4', 7),
(22, 'seatB4', 7),
(23, 'seatA4', 7),
(24, 'seatE9', 7),
(25, 'seatD9', 7),
(26, 'seatC9', 7),
(27, 'seatB9', 7),
(28, 'seatA9', 7),
(29, 'seatG9', 8),
(30, 'seatF9', 8),
(31, 'seatG4', 8),
(32, 'seatF4', 8),
(33, 'seatD4', 8),
(34, 'seatE4', 8),
(35, 'seatC4', 8),
(36, 'seatB4', 8),
(37, 'seatA4', 8),
(38, 'seatE9', 8),
(39, 'seatD9', 8),
(40, 'seatC9', 8),
(41, 'seatB9', 8),
(42, 'seatA9', 8),
(75, 'seatH1', 1),
(76, 'seatG1', 1),
(77, 'seatF1', 1),
(78, 'seatE1', 1),
(79, 'seatD1', 1),
(80, 'seatC1', 1),
(81, 'seatB1', 1),
(82, 'seatA1', 1),
(83, 'seatH8', 1),
(84, 'seatG8', 1),
(85, 'seatF8', 1),
(86, 'seatE8', 1),
(87, 'seatD8', 1),
(88, 'seatC8', 1),
(89, 'seatB8', 1),
(90, 'seatA8', 1),
(91, 'seatI3', 9),
(92, 'seatH3', 9),
(93, 'seatG3', 9),
(94, 'seatF3', 9),
(95, 'seatE3', 9),
(96, 'seatD3', 9),
(97, 'seatC3', 9),
(98, 'seatB3', 9),
(99, 'seatA3', 9),
(100, 'seatI7', 9),
(101, 'seatH7', 9),
(102, 'seatG7', 9),
(103, 'seatF7', 9),
(104, 'seatE7', 9),
(105, 'seatD7', 9),
(106, 'seatC7', 9),
(107, 'seatB7', 9),
(108, 'seatA7', 9);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `cinemas`
--

DROP TABLE IF EXISTS `cinemas`;
CREATE TABLE IF NOT EXISTS `cinemas` (
  `cinema_ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(55) COLLATE utf8_unicode_ci NOT NULL,
  `isOpen` tinyint(1) NOT NULL,
  PRIMARY KEY (`cinema_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Άδειασμα δεδομένων του πίνακα `cinemas`
--

INSERT INTO `cinemas` (`cinema_ID`, `name`, `isOpen`) VALUES
(1, 'Athens', 1),
(2, 'Thessaloniki', 1);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `customer`
--

DROP TABLE IF EXISTS `customer`;
CREATE TABLE IF NOT EXISTS `customer` (
  `customer_ID` int(11) NOT NULL AUTO_INCREMENT,
  `user_ID` int(11) NOT NULL,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `surname` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `telephone` varchar(13) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`customer_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Άδειασμα δεδομένων του πίνακα `customer`
--

INSERT INTO `customer` (`customer_ID`, `user_ID`, `name`, `surname`, `email`, `telephone`) VALUES
(2, 3, 'Nick', 'Jimmy', 'ngk-23@hotmail.com', '6990001234');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `disabledseats`
--

DROP TABLE IF EXISTS `disabledseats`;
CREATE TABLE IF NOT EXISTS `disabledseats` (
  `ds_ID` int(11) NOT NULL AUTO_INCREMENT,
  `seat` varchar(55) COLLATE utf8_unicode_ci NOT NULL,
  `hall_ID` int(11) NOT NULL,
  PRIMARY KEY (`ds_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Άδειασμα δεδομένων του πίνακα `disabledseats`
--

INSERT INTO `disabledseats` (`ds_ID`, `seat`, `hall_ID`) VALUES
(1, 'seatG6', 6),
(2, 'seatG7', 6),
(3, 'seatG2', 7),
(4, 'seatF2', 7),
(5, 'seatE2', 7),
(6, 'seatD2', 7),
(7, 'seatC2', 7),
(8, 'seatB2', 7),
(9, 'seatA2', 7),
(10, 'seatA6', 7),
(11, 'seatB6', 7),
(12, 'seatC6', 7),
(13, 'seatD6', 7),
(14, 'seatG6', 7),
(15, 'seatF6', 7),
(16, 'seatE6', 7),
(17, 'seatG8', 7),
(18, 'seatF8', 7),
(19, 'seatE8', 7),
(20, 'seatD8', 7),
(21, 'seatC8', 7),
(22, 'seatB8', 7),
(23, 'seatA8', 7),
(24, 'seatA11', 7),
(25, 'seatB11', 7),
(26, 'seatC11', 7),
(27, 'seatD11', 7),
(28, 'seatG11', 7),
(29, 'seatF11', 7),
(30, 'seatE11', 7),
(31, 'seatG2', 8),
(32, 'seatF2', 8),
(33, 'seatE2', 8),
(34, 'seatD2', 8),
(35, 'seatC2', 8),
(36, 'seatB2', 8),
(37, 'seatA2', 8),
(38, 'seatA6', 8),
(39, 'seatB6', 8),
(40, 'seatC6', 8),
(41, 'seatD6', 8),
(42, 'seatG6', 8),
(43, 'seatF6', 8),
(44, 'seatE6', 8),
(45, 'seatG8', 8),
(46, 'seatF8', 8),
(47, 'seatE8', 8),
(48, 'seatD8', 8),
(49, 'seatC8', 8),
(50, 'seatB8', 8),
(51, 'seatA8', 8),
(52, 'seatA11', 8),
(53, 'seatB11', 8),
(54, 'seatC11', 8),
(55, 'seatD11', 8),
(56, 'seatG11', 8),
(57, 'seatF11', 8),
(58, 'seatE11', 8),
(107, 'seatH2', 1),
(108, 'seatH4', 1),
(109, 'seatH6', 1),
(110, 'seatG7', 1),
(111, 'seatG5', 1),
(112, 'seatG3', 1),
(113, 'seatF2', 1),
(114, 'seatF4', 1),
(115, 'seatF6', 1),
(116, 'seatE7', 1),
(117, 'seatE5', 1),
(118, 'seatE3', 1),
(119, 'seatD2', 1),
(120, 'seatD4', 1),
(121, 'seatD6', 1),
(122, 'seatC7', 1),
(123, 'seatC5', 1),
(124, 'seatC3', 1),
(125, 'seatB2', 1),
(126, 'seatB4', 1),
(127, 'seatB6', 1),
(128, 'seatA7', 1),
(129, 'seatA5', 1),
(130, 'seatA3', 1);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `employee`
--

DROP TABLE IF EXISTS `employee`;
CREATE TABLE IF NOT EXISTS `employee` (
  `employee_ID` int(11) NOT NULL AUTO_INCREMENT,
  `user_ID` int(11) NOT NULL,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `surname` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `telephone` varchar(13) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`employee_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Άδειασμα δεδομένων του πίνακα `employee`
--

INSERT INTO `employee` (`employee_ID`, `user_ID`, `name`, `surname`, `email`, `telephone`) VALUES
(1, 1, 'neofytos', 'konstantinidis', 'ngka-23@hotmail.com', '6940000002');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `free_days`
--

DROP TABLE IF EXISTS `free_days`;
CREATE TABLE IF NOT EXISTS `free_days` (
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `genre`
--

DROP TABLE IF EXISTS `genre`;
CREATE TABLE IF NOT EXISTS `genre` (
  `Gen_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Gen_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Άδειασμα δεδομένων του πίνακα `genre`
--

INSERT INTO `genre` (`Gen_ID`, `Title`) VALUES
(1, 'Action'),
(2, 'Adventure'),
(3, 'Horror'),
(4, 'Short'),
(5, 'Drama'),
(6, 'Mystery'),
(7, 'Comedy'),
(8, 'Fantasy'),
(9, 'Sci-Fi'),
(10, 'Romance'),
(11, 'Animation'),
(12, 'Crime'),
(13, 'Talk-Show'),
(14, 'Family'),
(15, 'Documentary'),
(16, 'Reality-TV'),
(17, 'Music'),
(18, 'History'),
(19, 'Western'),
(20, 'News'),
(21, 'Biography'),
(22, 'War'),
(23, 'Musical'),
(24, 'Game-Show'),
(25, 'Sport'),
(26, 'Film-Noir'),
(27, 'Adult');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `halls`
--

DROP TABLE IF EXISTS `halls`;
CREATE TABLE IF NOT EXISTS `halls` (
  `hall_ID` int(11) NOT NULL AUTO_INCREMENT,
  `cinema_ID` int(11) NOT NULL,
  `hall_Name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `rows` int(11) NOT NULL,
  `columns` int(11) NOT NULL,
  `availableSeats` int(11) NOT NULL,
  PRIMARY KEY (`hall_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Άδειασμα δεδομένων του πίνακα `halls`
--

INSERT INTO `halls` (`hall_ID`, `cinema_ID`, `hall_Name`, `rows`, `columns`, `availableSeats`) VALUES
(1, 1, 'AS1', 8, 8, 24),
(2, 1, 'AS2', 8, 8, 0),
(6, 1, 'AM1', 7, 12, 68),
(7, 1, 'AM2', 7, 12, 42),
(8, 1, 'AM3', 7, 12, 42),
(9, 2, 'TS1', 9, 9, 63);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `moviegenres`
--

DROP TABLE IF EXISTS `moviegenres`;
CREATE TABLE IF NOT EXISTS `moviegenres` (
  `mgenre_ID` int(11) NOT NULL AUTO_INCREMENT,
  `movie_ID` int(11) NOT NULL,
  `genre_ID` int(11) NOT NULL,
  PRIMARY KEY (`mgenre_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Άδειασμα δεδομένων του πίνακα `moviegenres`
--

INSERT INTO `moviegenres` (`mgenre_ID`, `movie_ID`, `genre_ID`) VALUES
(4, 2, 1),
(5, 2, 5),
(6, 3, 11),
(7, 3, 2),
(8, 3, 7),
(9, 4, 1),
(10, 4, 2),
(11, 4, 9),
(12, 5, 3),
(67, 1, 12),
(68, 1, 2),
(69, 1, 1),
(81, 13, 2);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `movies`
--

DROP TABLE IF EXISTS `movies`;
CREATE TABLE IF NOT EXISTS `movies` (
  `Movie_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `Description` text COLLATE utf8_unicode_ci NOT NULL,
  `Year` year(4) NOT NULL,
  `ReleaseDate` date NOT NULL,
  `Duration` int(11) NOT NULL,
  `preview` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `Trailers` text COLLATE utf8_unicode_ci NOT NULL,
  `Photos` text COLLATE utf8_unicode_ci NOT NULL,
  `isPlaying` tinyint(1) NOT NULL,
  PRIMARY KEY (`Movie_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Άδειασμα δεδομένων του πίνακα `movies`
--

INSERT INTO `movies` (`Movie_ID`, `Title`, `Description`, `Year`, `ReleaseDate`, `Duration`, `preview`, `Trailers`, `Photos`, `isPlaying`) VALUES
(1, 'Fast & Furious 9 (F9)', 'Vin Diesel\'s Dom Toretto is leading a quiet life off the grid with Letty and his son, little Brian, but they know that danger always lurks just over their peaceful horizon. This time, that threat will force Dom to confront the sins of his past if he\'s going to save those he loves most. His crew joins together to stop a world-shattering plot led by the most skilled assassin and high-performance driver they\'ve ever encountered: a man who also happens to be Dom\'s forsaken brother, Jakob (John Cena, the upcoming The Suicide Squad).', 2021, '2021-06-25', 145, 'F9preview.jpg', 'FUK2kdPsBws', 'fastnfurious9_p1.jpg,fastnfurious9_p2.jpg,fastnfurious9_p3.jpg,fastnfurious9_p4.jpg,fastnfurious9_p5.jpg', 1),
(2, 'Top Gun: Maverick', 'After more than thirty years of service as one of the Navy\'s top aviators, Pete Mitchell is where he belongs, pushing the envelope as a courageous test pilot and dodging the advancement in rank that would ground him. ', 2021, '2021-07-02', 110, 'maverickPreview.jpg', 'qSqVVswa420,g4U4BQW9OEk', 'topgunmaverick_p1.jpg,topgunmaverick_p2.jpg', 1),
(3, 'Peter Rabbit 2: The Runaway', 'Thomas and Bea are now married and living with Peter and his rabbit family. Bored of life in the garden, Peter goes to the big city, where he meets shady characters and ends up creating chaos for the whole family.', 2021, '2021-07-02', 93, 'peterrabbit2Preview.jpg', 'PWBcqCz7l_c', 'peterrabbit2_p1.jpg,peterrabbit2_p2.jpg', 1),
(4, 'Black Widow', 'Following the events of Captain America: Civil War (2016), Natasha Romanoff finds herself alone and forced to confront a dangerous conspiracy with ties to her past. Pursued by a force that will stop at nothing to bring her down, Romanoff must deal with her history as a spy and the broken relationships left in her wake long before she became an Avenger.', 2021, '2021-07-09', 133, 'blackwidowPreview.jpg', 'ybji16u608U', 'blackwidow_p1.jpg', 1),
(5, 'The Unholy', 'A hearing-impaired girl is visited by the Virgin Mary and can suddenly hear, speak, and heal the sick. As people flock to witness her miracles, terrifying events unfold. Are they the work of the Virgin Mary or something much more sinister?', 2021, '2021-04-02', 99, 'theunholypreview.jpg', 'NmQiJPLYzPI', 'theunholy_p1.jpg,theunholy_p2.jpg', 1);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `moviesrating`
--

DROP TABLE IF EXISTS `moviesrating`;
CREATE TABLE IF NOT EXISTS `moviesrating` (
  `mr_ID` int(11) NOT NULL AUTO_INCREMENT,
  `movie_ID` int(11) NOT NULL,
  `user_ID` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  PRIMARY KEY (`mr_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Άδειασμα δεδομένων του πίνακα `moviesrating`
--

INSERT INTO `moviesrating` (`mr_ID`, `movie_ID`, `user_ID`, `rating`) VALUES
(1, 1, 1, 4),
(2, 1, 2, 3),
(3, 2, 1, 3),
(4, 3, 1, 4);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `permission_ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`permission_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Άδειασμα δεδομένων του πίνακα `permissions`
--

INSERT INTO `permissions` (`permission_ID`, `name`) VALUES
(0, 'user'),
(1, 'admin');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `projection`
--

DROP TABLE IF EXISTS `projection`;
CREATE TABLE IF NOT EXISTS `projection` (
  `projection_ID` int(11) NOT NULL AUTO_INCREMENT,
  `hall_ID` int(11) NOT NULL,
  `movie_ID` int(11) NOT NULL,
  `time_ID` int(11) NOT NULL,
  `projDate` date NOT NULL,
  PRIMARY KEY (`projection_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Άδειασμα δεδομένων του πίνακα `projection`
--

INSERT INTO `projection` (`projection_ID`, `hall_ID`, `movie_ID`, `time_ID`, `projDate`) VALUES
(19, 1, 1, 11, '2021-04-22'),
(22, 1, 1, 11, '2021-04-15'),
(23, 1, 1, 12, '2021-04-16'),
(24, 1, 1, 10, '2021-04-14'),
(25, 1, 1, 12, '2021-04-23'),
(26, 1, 1, 8, '2021-04-19'),
(27, 2, 2, 11, '2021-04-22'),
(28, 2, 2, 9, '2021-04-20'),
(29, 2, 2, 9, '2021-04-27'),
(30, 6, 1, 18, '2021-04-22'),
(31, 6, 1, 11, '2021-04-22'),
(32, 6, 2, 22, '2021-05-26'),
(33, 6, 2, 24, '2021-05-28'),
(34, 6, 2, 21, '2021-06-01'),
(35, 6, 2, 23, '2021-06-03'),
(38, 6, 1, 8, '2021-06-07'),
(57, 6, 1, 8, '2021-06-28'),
(58, 6, 1, 11, '2021-07-01'),
(59, 6, 1, 11, '2021-07-08'),
(60, 6, 1, 8, '2021-07-05'),
(61, 6, 2, 10, '2021-07-07'),
(62, 6, 2, 9, '2021-07-06'),
(63, 6, 2, 12, '2021-07-02'),
(64, 6, 1, 12, '2021-06-25'),
(65, 7, 3, 12, '2021-07-02'),
(66, 7, 3, 8, '2021-07-05'),
(67, 7, 3, 12, '2021-07-09'),
(68, 7, 4, 8, '2021-07-12'),
(69, 7, 4, 9, '2021-07-13'),
(70, 7, 4, 10, '2021-07-14'),
(71, 7, 4, 10, '2021-07-21'),
(72, 7, 4, 9, '2021-07-20'),
(73, 7, 4, 8, '2021-07-19'),
(74, 6, 4, 12, '2021-07-09'),
(75, 6, 1, 33, '2021-06-25'),
(76, 6, 1, 25, '2021-06-26'),
(77, 6, 1, 26, '2021-06-27'),
(78, 6, 1, 29, '2021-06-28'),
(79, 6, 1, 30, '2021-06-29'),
(81, 6, 2, 33, '2021-07-02'),
(82, 2, 2, 8, '2021-06-21'),
(83, 2, 2, 9, '2021-06-22'),
(84, 2, 2, 10, '2021-06-23'),
(85, 1, 5, 8, '2021-06-21'),
(86, 1, 5, 9, '2021-06-22'),
(87, 1, 5, 10, '2021-06-23'),
(88, 9, 1, 12, '2021-06-25'),
(89, 9, 1, 8, '2021-06-28'),
(90, 9, 1, 9, '2021-06-29'),
(91, 9, 1, 10, '2021-06-30'),
(92, 9, 2, 12, '2021-07-02'),
(93, 9, 3, 8, '2021-07-05'),
(94, 9, 3, 9, '2021-07-06'),
(95, 9, 4, 12, '2021-07-09');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `projtime`
--

DROP TABLE IF EXISTS `projtime`;
CREATE TABLE IF NOT EXISTS `projtime` (
  `time_ID` int(11) NOT NULL AUTO_INCREMENT,
  `dayP` enum('monday','tuesday','wednesday','thursday','friday','saturday','sunday') COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` time NOT NULL,
  PRIMARY KEY (`time_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Άδειασμα δεδομένων του πίνακα `projtime`
--

INSERT INTO `projtime` (`time_ID`, `dayP`, `timestamp`) VALUES
(8, 'monday', '09:15:00'),
(9, 'tuesday', '09:15:00'),
(10, 'wednesday', '09:15:00'),
(11, 'thursday', '09:15:00'),
(12, 'friday', '09:15:00'),
(13, 'saturday', '10:15:00'),
(14, 'sunday', '10:15:00'),
(15, 'monday', '11:15:00'),
(16, 'tuesday', '11:15:00'),
(17, 'wednesday', '11:15:00'),
(18, 'thursday', '11:15:00'),
(19, 'friday', '11:15:00'),
(20, 'monday', '11:45:00'),
(21, 'tuesday', '11:45:00'),
(22, 'wednesday', '11:45:00'),
(23, 'thursday', '11:45:00'),
(24, 'friday', '11:45:00'),
(25, 'saturday', '12:15:00'),
(26, 'sunday', '12:15:00'),
(27, 'saturday', '12:45:00'),
(28, 'sunday', '12:45:00'),
(29, 'monday', '12:15:00'),
(30, 'tuesday', '12:15:00'),
(31, 'wednesday', '12:15:00'),
(32, 'thursday', '12:15:00'),
(33, 'friday', '12:15:00'),
(34, 'tuesday', '14:15:00'),
(35, 'thursday', '14:15:00'),
(36, 'friday', '14:15:00');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `ticket`
--

DROP TABLE IF EXISTS `ticket`;
CREATE TABLE IF NOT EXISTS `ticket` (
  `ticket_ID` int(11) NOT NULL AUTO_INCREMENT,
  `seatName` varchar(55) COLLATE utf8_unicode_ci NOT NULL,
  `projection_ID` int(11) NOT NULL,
  `user_ID` varchar(55) COLLATE utf8_unicode_ci NOT NULL,
  `timeclosed` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ticket_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Άδειασμα δεδομένων του πίνακα `ticket`
--

INSERT INTO `ticket` (`ticket_ID`, `seatName`, `projection_ID`, `user_ID`, `timeclosed`) VALUES
(8, 'seatG6', 2, '1', '2021-05-05 03:50:31'),
(9, 'seatH7', 2, '1', '2021-05-05 03:50:31'),
(10, 'seatH3', 2, '1', '2021-05-05 03:50:31'),
(13, 'seatD7', 2, '1', '2021-05-05 20:02:27'),
(14, 'seatD5', 2, '1', '2021-05-05 20:02:27'),
(15, 'seatE2', 2, '1', '2021-05-05 20:04:08'),
(16, 'seatF3', 2, '1', '2021-05-05 20:04:08'),
(17, 'seatB5', 2, '1', '2021-05-05 20:08:08'),
(18, 'seatB7', 2, '1', '2021-05-05 20:08:08'),
(19, 'seatH5', 2, '1', '2021-05-05 20:11:24'),
(20, 'seatG4', 2, '1', '2021-05-05 20:11:24'),
(21, 'seatG2', 2, '1', '2021-05-05 20:11:24'),
(29, 'seatB3', 2, '1', '2021-05-25 20:01:20'),
(30, 'seatC4', 2, '1', '2021-05-25 20:01:20'),
(35, 'seatC6', 2, '1', '2021-05-26 06:07:21');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `user_ID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `role` int(11) NOT NULL,
  `isActivated` tinyint(1) NOT NULL,
  PRIMARY KEY (`user_ID`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Άδειασμα δεδομένων του πίνακα `user`
--

INSERT INTO `user` (`user_ID`, `username`, `password`, `role`, `isActivated`) VALUES
(1, 'neofytos', 'eb8830213cd6e188656d00e7328547f97a892a11', 1, 1),
(3, 'user1', 'b3daa77b4c04a9551b8781d03191fe098f325e67', 0, 1);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `userconfirmation`
--

DROP TABLE IF EXISTS `userconfirmation`;
CREATE TABLE IF NOT EXISTS `userconfirmation` (
  `username` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `confirmationcode` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Άδειασμα δεδομένων του πίνακα `userconfirmation`
--

INSERT INTO `userconfirmation` (`username`, `confirmationcode`) VALUES
('neofytos', 'YL5azGpAqv');

--
-- Περιορισμοί για άχρηστους πίνακες
--

--
-- Περιορισμοί για πίνακα `cancelledseats`
--
ALTER TABLE `cancelledseats`
  ADD CONSTRAINT `hall_cancelled` FOREIGN KEY (`hall_ID`) REFERENCES `halls` (`hall_ID`) ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

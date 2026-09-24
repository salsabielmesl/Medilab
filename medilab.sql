-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3308
-- Generation Time: Aug 22, 2025 at 12:15 PM
-- Server version: 5.7.31
-- PHP Version: 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `medilab`
--

-- --------------------------------------------------------

--
-- Table structure for table `aboutus`
--

DROP TABLE IF EXISTS `aboutus`;
CREATE TABLE IF NOT EXISTS `aboutus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text,
  `image` varchar(255) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `aboutus`
--

INSERT INTO `aboutus` (`id`, `content`, `image`, `active`) VALUES
(1, 'hi', 'default_about.jpg', 0),
(2, 'hi', 'aboutus_1753343918.jpeg', 0),
(3, 'hi', 'aboutus_1753344012.jpeg', 0),
(4, 'hi', 'aboutus_1753344013.jpeg', 0),
(5, 'hi', 'aboutus_1753344021.jpeg', 0),
(6, 'hi', 'aboutus_1753344490.jpeg', 0),
(7, 'hi', 'aboutus_1753344503.jpeg', 0),
(8, 'hi', 'aboutus_1753344522.jpeg', 0),
(9, 'hello', 'default_about.jpg', 0),
(10, 'hello', 'aboutus_1753349255.jpeg', 0),
(11, 'hello', 'aboutus_1753349739.jpeg', 0),
(12, 'Welcome to MediLab, where compassionate care meets cutting-edge technology. Our clinic is dedicated to providing comprehensive medical services tailored to the needs of every patient. With a team of highly qualified doctors, nurses, and healthcare professionals, we offer a wide range of diagnostic, preventive, and treatment services in a welcoming and patient-centered environment. At MediLab, we believe in transparency, trust, and excellence â€” ensuring that your health and well-being are always our top priorities.\r\n\r\n', 'aboutus_1753381816.jpeg', 0),
(13, 'Welcome to MediLab, where compassionate care meets cutting-edge technology. Our clinic is dedicated to providing comprehensive medical services tailored to the needs of every patient. With a team of highly qualified doctors, nurses, and healthcare professionals, we offer a wide range of diagnostic, preventive, and treatment services in a welcoming and patient-centered environment. At MediLab, we believe in transparency, trust, and excellence â€” ensuring that your health and well-being are always our top priorities.', 'aboutus_1753382729.png', 1);

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

DROP TABLE IF EXISTS `appointments`;
CREATE TABLE IF NOT EXISTS `appointments` (
  `AppID` int(11) NOT NULL AUTO_INCREMENT,
  `PatientID` int(11) DEFAULT NULL,
  `RecepID` int(11) DEFAULT NULL,
  `DepID` int(11) DEFAULT NULL,
  `DocID` int(11) DEFAULT NULL,
  `FullName` varchar(150) NOT NULL,
  `AppDate` date NOT NULL,
  `StartTime` time NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `Discount` decimal(10,2) DEFAULT '0.00',
  `Total` decimal(10,2) GENERATED ALWAYS AS ((`Price` - `Discount`)) STORED,
  `Status` enum('Paid','Cancel','Appointed') NOT NULL DEFAULT 'Appointed',
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `Approved` tinyint(1) DEFAULT '0',
  `message` text,
  `PaymentAmount` decimal(10,2) DEFAULT '0.00',
  `RemainingAmount` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`AppID`),
  KEY `PatientID` (`PatientID`),
  KEY `RecepID` (`RecepID`),
  KEY `DepID` (`DepID`),
  KEY `DocID` (`DocID`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`AppID`, `PatientID`, `RecepID`, `DepID`, `DocID`, `FullName`, `AppDate`, `StartTime`, `Price`, `Discount`, `Status`, `CreatedAt`, `Approved`, `message`, `PaymentAmount`, `RemainingAmount`) VALUES
(1, 1, 1, 1, 1, 'ali', '2025-07-01', '17:32:00', '23.00', '3.00', 'Paid', '2025-07-23 11:32:46', 0, '', '20.00', '0.00'),
(2, 1, 1, 1, 1, 'ali', '2025-07-01', '17:38:00', '34.00', '3.00', 'Cancel', '2025-07-23 11:35:51', 0, '', '0.00', '0.00'),
(3, 1, 1, 1, 1, 'ali', '2025-07-02', '17:10:00', '350.00', '50.00', 'Cancel', '2025-07-24 12:10:31', 0, '', '0.00', '0.00'),
(4, 1, 1, 1, 1, 'ali', '2025-07-03', '17:10:00', '350.00', '60.00', 'Paid', '2025-07-24 12:11:06', 0, '', '290.00', '0.00'),
(5, 4, 1, 6, 9, 'faten', '2025-06-30', '12:49:00', '47.00', '3.00', 'Paid', '2025-07-24 18:47:29', 1, '', '44.00', '0.00'),
(8, 6, NULL, 2, 5, 'alii', '2025-07-25', '06:59:00', '0.00', '0.00', 'Paid', '2025-07-24 23:56:39', 1, '', '40.00', '0.00'),
(7, 4, 2, 4, 7, 'faten', '2025-07-29', '00:20:00', '50.00', '1.00', 'Paid', '2025-07-24 21:17:53', 1, '', '49.00', '0.00'),
(14, 5, 2, 3, 18, 'mohamad', '2025-07-24', '02:44:00', '34.00', '1.00', 'Cancel', '2025-07-25 07:44:58', 0, '', '0.00', '0.00'),
(13, 6, NULL, 3, 6, 'alii', '2025-07-26', '03:23:00', '0.00', '0.00', 'Appointed', '2025-07-25 00:20:11', 0, '', '0.00', '0.00'),
(15, 6, NULL, 3, 6, 'alii', '2025-07-08', '13:04:00', '400.00', '50.00', 'Paid', '2025-07-25 08:04:24', 0, '', '350.00', '0.00'),
(16, 5, NULL, 1, 14, 'mohamad', '2025-07-31', '14:54:00', '4544.00', '0.00', 'Cancel', '2025-08-04 20:54:41', 0, '', '0.00', '0.00');

-- --------------------------------------------------------

--
-- Table structure for table `carousel`
--

DROP TABLE IF EXISTS `carousel`;
CREATE TABLE IF NOT EXISTS `carousel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `carousel`
--

INSERT INTO `carousel` (`id`, `image`, `title`, `description`) VALUES
(1, 'carousel_1753381543_68827aa7bea71.jpeg', 'Welcome to Medilabgg', 'Trusted care for your familyâ€™s healthâ€”where compassion meets advanced medical expertisgge.'),
(2, 'carousel_1753381543_68827aa7bf36a.jpeg', 'All-In-One Medical Servicesff', 'Comprehensive medical services under one roofâ€”from diagnostics to personalized treatment plans.ggg'),
(3, 'carousel_1753381543_68827aa7bfb61.jpeg', 'Compassionate, Patient-Focused Caregg', 'Experience compassionate care with miniggmal wait times and a focus on your comfort and wellbeing.');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

DROP TABLE IF EXISTS `department`;
CREATE TABLE IF NOT EXISTS `department` (
  `DepID` int(11) NOT NULL AUTO_INCREMENT,
  `DepName` varchar(100) NOT NULL,
  `Description` text,
  `Image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`DepID`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`DepID`, `DepName`, `Description`, `Image`) VALUES
(1, 'Psychiatry', 'Focused on the diagnosis, treatment, and prevention of mental, emotional, and behavioral disorders.', 'dep_688275bc104ba9.37137761.jpeg'),
(2, 'Gynecology', 'Focused on the health of the female reproductive system. ', 'dep_6882760dd7b088.03715585.jpeg'),
(3, 'Surgery', 'Surgery is a medical procedure involving manual or instrumental techniques to diagnose, treat, or alter a body part, function, or tissue.', 'dep_688276cec378e1.92656132.jpeg'),
(4, 'Neurology', 'Neurology is the branch of medicine dealing with the diagnosis and treatment of all categories of conditions and disease involving the nervous system, which comprises the brain, the spinal cord and the peripheral nerves.', 'dep_6882770854a323.55660342.jpeg'),
(5, 'Internal medicine', 'Internal medicine is a medical specialty focused on the prevention, diagnosis, and treatment of diseases in adults.', 'dep_6882773faae677.82968597.jpeg'),
(6, 'Dermatology', 'Dermatology is the branch of medicine focused on the diagnosis and treatment of diseases and conditions related to the skin, hair, and nails.', 'dep_688277d09c08e8.62903701.jpeg'),
(7, 'Anesthesiology', 'The branch of medicine concerned with anaesthesia and anaesthetics.', 'dep_68827830c71c55.34801378.jpeg'),
(8, 'Oncology', 'Oncology is the branch of medicine focused on the study, diagnosis, treatment, and prevention of cancer.', 'dep_688278581b7da6.93846845.webp'),
(9, 'Gastroenterology', 'Gastroenterology is the branch of medicine focused on the digestive system and its disorders. ', 'dep_68827880ba35b5.49853711.jpeg'),
(10, 'Otolaryngology', 'Otolaryngology, also known as ENT (ear, nose, and throat) or head and neck surgery, is a medical specialty focused on the diagnosis and treatment of conditions affecting the ears, nose, throat, head, and neck.', 'dep_688278b23db737.31166625.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `docschedule`
--

DROP TABLE IF EXISTS `docschedule`;
CREATE TABLE IF NOT EXISTS `docschedule` (
  `ScheduleID` int(11) NOT NULL AUTO_INCREMENT,
  `RecepID` int(11) DEFAULT NULL,
  `DocID` int(11) DEFAULT NULL,
  `DepID` int(11) DEFAULT NULL,
  `DayOfWeek` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `StartTime` time NOT NULL,
  `EndTime` time NOT NULL,
  PRIMARY KEY (`ScheduleID`),
  KEY `RecepID` (`RecepID`),
  KEY `DocID` (`DocID`),
  KEY `DepID` (`DepID`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `docschedule`
--

INSERT INTO `docschedule` (`ScheduleID`, `RecepID`, `DocID`, `DepID`, `DayOfWeek`, `StartTime`, `EndTime`) VALUES
(1, NULL, 1, 1, 'Tuesday', '17:10:00', '16:10:00'),
(2, NULL, 1, 1, 'Wednesday', '16:17:00', '16:17:00'),
(4, NULL, 7, 4, 'Monday', '04:45:00', '06:45:00'),
(5, NULL, 7, 4, 'Wednesday', '15:45:00', '18:45:00'),
(6, NULL, 17, 2, 'Monday', '06:40:00', '07:40:00'),
(7, NULL, 5, 2, 'Monday', '02:01:00', '03:01:00');

-- --------------------------------------------------------

--
-- Table structure for table `doctor`
--

DROP TABLE IF EXISTS `doctor`;
CREATE TABLE IF NOT EXISTS `doctor` (
  `DocID` int(11) NOT NULL AUTO_INCREMENT,
  `DepID` int(11) DEFAULT NULL,
  `Name` varchar(100) NOT NULL,
  `DOB` date DEFAULT NULL,
  `ProfilePic` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`DocID`),
  KEY `DepID` (`DepID`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `doctor`
--

INSERT INTO `doctor` (`DocID`, `DepID`, `Name`, `DOB`, `ProfilePic`) VALUES
(4, 1, 'Hana', '2025-07-24', 'doc_68827483c185a9.76929679.jpg'),
(5, 2, 'Adam', '2025-07-25', 'doc_688278dbd42fc2.62506211.jpg'),
(6, 3, 'Zeinab', '2025-07-08', 'doc_688279003609f1.12727060.jpg'),
(7, 4, 'Abbas', '2025-07-22', 'doc_688279116d9d03.66760717.jpg'),
(8, 5, 'Hala', '2025-07-31', 'doc_688279276253c5.24518565.jpg'),
(9, 6, 'Ahmad', '2025-07-14', 'doc_68827945c37bc6.24814811.jpg'),
(10, 7, 'Hasan', '2025-07-30', 'doc_6882795562bc60.64002572.jpg'),
(11, 8, 'Hussein', '2025-07-27', 'doc_688279a8dddbf7.94865232.jpg'),
(12, 9, 'Maya', '2025-07-26', 'doc_688279fe1241d5.59040034.jpg'),
(13, 10, 'Alaa', '2025-07-13', 'doc_68827a0c4f0050.10593265.jpg'),
(14, 1, 'malak', '2025-06-29', 'doc_6882c56ebe7a44.98960292.jpeg'),
(16, 1, 'samar', '2025-07-16', 'doc_6882c5ef7a4c71.12491611.jpeg'),
(17, 2, 'bachar', '2025-07-16', 'doc_6882c63fbbdd45.13562910.jpeg'),
(18, 3, 'hadi', '2025-07-14', 'doc_6882c64f232546.62674385.jpeg'),
(19, 4, 'zeina', '2025-07-15', 'doc_6882c67978c096.67337349.jpeg'),
(20, 5, 'zein', '2025-08-04', 'doc_6882c6974ac927.08007924.jpeg'),
(21, 6, 'malek', '2025-07-13', 'doc_6882c6b1ba9c38.40861201.jpeg'),
(22, 7, 'monir', '2025-07-21', 'doc_6882c6d591eae0.67958810.jpeg'),
(23, 8, 'raef', '2025-07-15', 'doc_6882c6ec6edca0.56476854.jpeg'),
(24, 9, 'salim', '2025-07-06', 'doc_6882c704644421.01021426.jpeg'),
(25, 10, 'sana', '2025-07-08', 'doc_6882c741ec8b44.75466107.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

DROP TABLE IF EXISTS `gallery`;
CREATE TABLE IF NOT EXISTS `gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image` varchar(255) NOT NULL,
  `uploaded_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `image`, `uploaded_at`) VALUES
(18, 'medicio/assets/img/gallery/1753382465_gal8.jpeg', '2025-07-24 21:41:05'),
(16, 'medicio/assets/img/gallery/1753382465_gal6.jpeg', '2025-07-24 21:41:05'),
(17, 'medicio/assets/img/gallery/1753382465_gal7.jpeg', '2025-07-24 21:41:05'),
(15, 'medicio/assets/img/gallery/1753382465_gal5.jpeg', '2025-07-24 21:41:05'),
(14, 'medicio/assets/img/gallery/1753382465_gal4.jpeg', '2025-07-24 21:41:05'),
(13, 'medicio/assets/img/gallery/1753382465_gal2.jpeg', '2025-07-24 21:41:05');

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

DROP TABLE IF EXISTS `patient`;
CREATE TABLE IF NOT EXISTS `patient` (
  `PatientID` int(11) NOT NULL AUTO_INCREMENT,
  `FullName` varchar(150) NOT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `Gender` enum('Male','Female') NOT NULL,
  `DOB` date NOT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`PatientID`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`PatientID`, `FullName`, `Email`, `Password`, `Gender`, `DOB`, `Phone`) VALUES
(2, 'zahraa', 'zahraa@gmail.com', '$2y$10$cMJPLlkaFBOGOTnKEB.h/ecy7yg0G7G39QgYIhEZYccVZYhau3wtO', 'Female', '2025-07-23', '67454'),
(4, 'faten', 'faten@gmail.com', '$2y$10$dL8wbYvTsJG1pqskKFP3nOvHp.fZDJ9CYBGBXas37Wh3jhSrpGNtu', 'Female', '2025-07-24', '81897765'),
(5, 'mohamad', 'mohamad@gmail.com', '$2y$10$fpb6adOk.MpqerbXUlVJOOE.DTH4qo1pzsWVRgH3bdpnzxSKnQgZ6', 'Male', '2025-07-25', '345678888'),
(6, 'alii', 'ali1@gmail.com', '$2y$10$1qaaikR0uwfvGUPYIegXH.wvBeQ9.tQq.3Y/s95TudS67QpEIJprC', 'Male', '2025-07-25', '81897765');

-- --------------------------------------------------------

--
-- Table structure for table `receptionist`
--

DROP TABLE IF EXISTS `receptionist`;
CREATE TABLE IF NOT EXISTS `receptionist` (
  `RecepID` int(11) NOT NULL AUTO_INCREMENT,
  `FullName` varchar(150) NOT NULL,
  `PhoneNum` varchar(20) NOT NULL,
  `DOB` date NOT NULL,
  PRIMARY KEY (`RecepID`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `receptionist`
--

INSERT INTO `receptionist` (`RecepID`, `FullName`, `PhoneNum`, `DOB`) VALUES
(2, 'samir', '3454647', '2025-07-24');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
CREATE TABLE IF NOT EXISTS `reports` (
  `ReportID` int(11) NOT NULL AUTO_INCREMENT,
  `AppID` int(11) NOT NULL,
  `Assessment` text NOT NULL,
  `Diagnosis` text NOT NULL,
  `Prescription` text NOT NULL,
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ReportID`),
  KEY `AppID` (`AppID`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`ReportID`, `AppID`, `Assessment`, `Diagnosis`, `Prescription`, `CreatedAt`) VALUES
(1, 5, 'test', 'test', 'test', '2025-07-24 20:15:37'),
(2, 7, 'test', 'test', 'test', '2025-07-24 21:28:58'),
(3, 8, 'test', 'test', 'test', '2025-07-24 23:58:03');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','doctor','receptionist','patient') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(18, 'bachar', '$2y$10$ElB/4bNmLA1zUeOPiZOPjumAV.tglQdfKl0lNyiVsyz1oucdwIDOi', 'doctor'),
(3, 'Adam', '$2y$10$llfOvw5F/qi6LHdgdGLDL.eK/de9XIYLqo4sNVt07IyvGd6faGH6S', 'doctor'),
(14, 'ahmad', '$2y$10$KtoQfIcwguzrW8ARvbqIEuunXwbY2/KA.xQ2GlrGNbX8FrfBe427a', 'doctor'),
(5, 'Zeinab', '$2y$10$DTyHQ8pGgzg4EY.XKu93POQDHBJmsWjsNXWLOhL72t9n0ZQkeyso2', 'doctor'),
(16, 'abbas', '$2y$10$tSvQMw33RqIdK1NvX3wmU.PtuD6BgXv3Tj2FjDrVp0wA0cnl2qosG', 'doctor'),
(17, 'salsabiel', '$2y$10$RQX/ORK5Q7onvwbEa3/62eWRTjf/c8c7lGSgqBYHVRctevCKgNjEq', 'admin'),
(13, 'samir', '$2y$10$/HXR5U1F5wlw583ZdaUin.SDLMAfNtvPejrS.CG2S3F08cbw4gA3C', 'receptionist');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

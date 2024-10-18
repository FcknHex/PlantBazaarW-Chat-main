-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 13, 2024 at 08:24 PM
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
-- Database: `gabplant`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `adminId` int(11) NOT NULL,
  `adminEmail` varchar(100) NOT NULL,
  `password` varchar(50) NOT NULL,
  `loginTime` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chats`
--

CREATE TABLE `chats` (
  `messageId` int(11) NOT NULL,
  `senderId` int(11) NOT NULL,
  `receiverId` int(11) NOT NULL,
  `messageContent` text NOT NULL,
  `messageDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `reply_to` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `file_path`, `reply_to`, `timestamp`, `status`) VALUES
(564, 24, 18, 'Uyy', NULL, NULL, '2024-10-11 02:03:42', 1),
(565, 24, 18, 'Vakit?', NULL, NULL, '2024-10-11 02:04:56', 1),
(566, 18, 24, 'utot', NULL, NULL, '2024-10-11 02:05:03', 1),
(567, 18, 24, 'bakit ganun sayang ang tatoo', NULL, 566, '2024-10-11 02:59:14', 0),
(568, 59, 1, 'uy', NULL, NULL, '2024-10-11 04:27:20', 1),
(569, 59, 1, 'boss?', NULL, 568, '2024-10-11 04:27:27', 1),
(570, 59, 41, 'bossing', NULL, NULL, '2024-10-11 04:27:45', 1),
(571, 59, 41, 'oy', NULL, 570, '2024-10-11 04:28:18', 1),
(572, 41, 1, 'Hello Boss', NULL, NULL, '2024-10-11 17:40:11', 1),
(573, 41, 1, 'Ey wats app', NULL, NULL, '2024-10-11 17:40:21', 1),
(574, 1, 41, 'Ok lang naman', NULL, NULL, '2024-10-11 17:41:38', 1),
(575, 41, 1, 'Buti naman', NULL, NULL, '2024-10-11 17:41:47', 1),
(576, 41, 41, 'Hey po', NULL, NULL, '2024-10-12 06:03:07', 0),
(577, 41, 1, 'Hey po', NULL, NULL, '2024-10-12 06:04:21', 0),
(578, 41, 59, ' asd', NULL, NULL, '2024-10-12 06:04:46', 0),
(579, 41, 1, 'asd', NULL, NULL, '2024-10-12 06:04:50', 0),
(580, 41, 41, 'asd', NULL, NULL, '2024-10-12 07:47:36', 0),
(581, 41, 41, 'pogi', NULL, NULL, '2024-10-12 07:47:39', 0),
(582, 41, 41, 'ey', NULL, NULL, '2024-10-12 07:47:44', 0),
(583, 41, 1, 'psst', NULL, 575, '2024-10-12 08:44:13', 0),
(584, 41, 1, 'oy', NULL, NULL, '2024-10-12 08:44:18', 0),
(585, 41, 1, 'idol', NULL, NULL, '2024-10-12 08:44:22', 0),
(586, 41, 1, 'asd', NULL, NULL, '2024-10-12 08:44:26', 0),
(587, 41, 1, 'ano balita', NULL, 584, '2024-10-12 08:47:50', 0),
(588, 41, 1, 'asd', NULL, 585, '2024-10-12 09:27:07', 0),
(589, 41, 1, 'bossing', NULL, 575, '2024-10-12 09:27:25', 0),
(590, 41, 1, 'asd', NULL, NULL, '2024-10-12 09:27:35', 0);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `plantid` int(10) NOT NULL,
  `added_by` int(11) NOT NULL,
  `plantname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `img1` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `img2` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `img3` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `plantColor` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `plantSize` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `plantcategories` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `location` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `region` varchar(128) NOT NULL,
  `province` varchar(128) NOT NULL,
  `city` varchar(50) NOT NULL,
  `barangay` varchar(128) NOT NULL,
  `street` varchar(128) DEFAULT NULL,
  `price` float(11,2) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`plantid`, `added_by`, `plantname`, `img1`, `img2`, `img3`, `plantColor`, `plantSize`, `plantcategories`, `details`, `location`, `region`, `province`, `city`, `barangay`, `street`, `price`, `createdAt`, `updatedAt`) VALUES
(39, 2, 'Succulent', 'succulent.jpg', '', '', 'Green', '6cm', 'Succulent', 'Easy to maintain plant', 'Gapan', '', '', '', '', NULL, 200.00, '2024-09-18 15:01:52', '2024-09-18 15:01:52'),
(47, 3, 'Uebelmannia pectinifera', '461944679_901099365454261_1511506421590284050_n.jpg', '461953559_901099362120928_417283808325182436_n.jpg', '461934857_901099372120927_4382470582676685201_n.jpg', '', 'Adult', 'Indoor', 'Uebelmannia pectinifera is a solitary cactus 10-50(-100) cm tall. It is a multiform species and very variable in habitat, comprising a complex of numerous local forms, where each form is linked to others by populations of plants with intermediate characteristics.', '', 'Region III (Central Luzon)', 'Nueva Ecija', 'Palayan City (Capital)', 'Sapang Buho', 'Malapit sa 7/11', 1600.00, '2024-10-12 18:34:03', '2024-10-12 18:34:03'),
(48, 3, 'Astrophytum asterias cv. Goryo Vtype', '461868218_900322995531898_8690682879674124111_n.jpg', '461946027_900322992198565_8126774815550641083_n.jpg', '461859863_900323002198564_5675261350630500625_n.jpg', '', 'Juvenile', 'Indoor', 'Normally Astrophytum asterias seedling have the standard 8 ribs, while only a small number of them have few or more ribs. The form with only five ribs, is particularly rare because many of the 5 ribbed seedlings that occasionally appear will usually develop additional ribs in a few years as they age. So it is quite exceptional to see an old A. asterias with five stable ribs. This form however looks suspiciously hybridized with myriostigma or coahuilense and probably is not a pure 100% asterias.', '', 'Region III (Central Luzon)', 'Nueva Ecija', 'City Of Gapan', 'San Vicente (Pob.)', 'Kangkong street', 1000.21, '2024-10-12 18:32:50', '2024-10-12 18:32:50');

-- --------------------------------------------------------

--
-- Table structure for table `product_archive`
--

CREATE TABLE `product_archive` (
  `archiveID` int(11) NOT NULL,
  `postedBy` varchar(50) NOT NULL,
  `postPlantName` varchar(50) NOT NULL,
  `img1` varchar(128) NOT NULL,
  `img2` varchar(128) NOT NULL,
  `img3` varchar(128) NOT NULL,
  `plantSize` varchar(20) NOT NULL,
  `plantCategories` varchar(40) NOT NULL,
  `details` varchar(128) NOT NULL,
  `location` int(70) NOT NULL,
  `price` int(10) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_archive`
--

INSERT INTO `product_archive` (`archiveID`, `postedBy`, `postPlantName`, `img1`, `img2`, `img3`, `plantSize`, `plantCategories`, `details`, `location`, `price`, `createdAt`, `updatedAt`) VALUES
(40, 'maranathabarredo@gmail.com', 'Cactus', 'cactus.jpg', 'Screenshot 2024-10-01 200917.png', 'Screenshot 2024-10-01 222606.png', 'Seedling', 'Cacti', '', 0, 100, '2024-10-12 16:28:58', '2024-10-12 16:28:58'),
(41, 'maranathabarredo@gmail.com', 'Kangkong', 'Screenshot 2024-10-05 193948.png', '', '', '1 meter', 'Veggies', '', 0, 200, '2024-10-12 16:29:00', '2024-10-12 16:29:00'),
(42, 'maranathabarredo@gmail.com', 'Munggo', 'sold.jpg', 'default-image.jpg', 'default-image.jpg', 'Adult', 'Outdoor', '', 0, 100, '2024-10-12 16:29:02', '2024-10-12 16:29:02'),
(43, 'maranathabarredo@gmail.com', 'asd', 'halam.jpg', 'default-image.jpg', 'default-image.jpg', 'Malaki', 'Tinola', '', 0, 123, '2024-10-12 16:29:03', '2024-10-12 16:29:03'),
(44, 'maranathabarredo@gmail.com', 'Wala lang', 'Screenshot 2024-10-06 180127.png', 'default-image.jpg', 'default-image.jpg', 'Adult', 'Climbers', '', 0, 123, '2024-10-12 16:29:05', '2024-10-12 16:29:05'),
(45, 'maranathabarredo@gmail.com', 'Malunggay', '3.png', 'default-image.jpg', 'default-image.jpg', 'Seedling', 'Indoor', '', 0, 1232, '2024-10-12 16:29:06', '2024-10-12 16:29:06'),
(46, 'maranathabarredo@gmail.com', 'Euphorbia obesa', '461861999_900319622198902_4781648371440441819_n.jpg', '461795555_900319615532236_6193450879555062943_n.jpg', '461875136_900319612198903_6003683218236318191_n.jpg', 'Juvenile', 'Outdoor', '', 0, 800, '2024-10-12 18:25:35', '2024-10-12 18:25:35');

-- --------------------------------------------------------

--
-- Table structure for table `sellers`
--

CREATE TABLE `sellers` (
  `seller_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ratings` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sellers`
--

INSERT INTO `sellers` (`seller_id`, `user_id`, `ratings`) VALUES
(2, 1, 4.8),
(3, 41, 5);

-- --------------------------------------------------------

--
-- Table structure for table `seller_applicant`
--

CREATE TABLE `seller_applicant` (
  `applicantID` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `validId` varchar(128) NOT NULL,
  `selfieValidId` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seller_applicant`
--

INSERT INTO `seller_applicant` (`applicantID`, `user_id`, `validId`, `selfieValidId`) VALUES
(2, 1, 'pogiako.png', 'pogiako.png\r\n'),
(4, 41, 'pogiKami.png', 'pogiKami.png'),
(13, 48, 'mbarredo2n.neust@gmail.com_validId.png', 'mbarredo2n.neust@gmail.com_selfieValidId.png');

-- --------------------------------------------------------

--
-- Table structure for table `seller_applicant_archive`
--

CREATE TABLE `seller_applicant_archive` (
  `archiveID` int(11) NOT NULL,
  `applicantID` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `validId` varchar(128) NOT NULL,
  `selfieValidId` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `proflePicture` varchar(128) DEFAULT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `gender` varchar(20) NOT NULL,
  `phoneNumber` bigint(12) NOT NULL,
  `address` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `proflePicture`, `firstname`, `lastname`, `email`, `gender`, `phoneNumber`, `address`, `password`, `status`) VALUES
(1, 'eugenevanlinsangan1204@gmail.com.jpg', 'Juan', 'DelaCruz', 'eugenevanlinsangan1204@gmail.com', 'male', 91234351, 'Gapan', 'Test123@', 1),
(41, 'maranathabarredo@gmail.com.png', 'Maranatha', 'Barredo', 'maranathabarredo@gmail.com', 'male', 974236516, 'Gen Tinio', 'Test123@', 1),
(48, '2.png', 'qwe', 'qwe', 'mbarredo2n.neust@gmail.com', 'Male', 123, 'qwe', 'qwe', 0),
(49, 'wadom93936_daypey_com.png', 'Wadow', 'Doe', 'wadom93936@daypey.com', 'Male', 123, 'qwe', '$2y$10$u/5.tT1u86/PS7aFWhDVz.L1CZ7ZuueVul0TPmtCjeOCF32ITx9Rm', 0),
(50, 'dejesusjoel731_gmail_com.png', 'Joel', 'De jesus', 'dejesusjoel731@gmail.com', 'Male', 9392925229, 'Poblacion East 2, Aliaga', '$2y$10$skYEmTatLJb9dqbs4u7QKek1.ce8o3E0OXfP4/YrnwW2NFWexDGHS', 0),
(51, 'olpottado205_gmail_com.png', 'Joel', 'De jesus', 'olpottado205@gmail.com', 'Male', 9882215277, 'Poblacion East 2, Aliaga', '$2y$10$fhav0V3QxWCllQWM7HDNNuF31esnXaAWx23LBMNR/FQqLDSLFJOq.', 0),
(52, '', 'Joel', 'De jesus', 'hatdog@gmail.com', 'gg', 9392925229, 'Poblacion East 2, Aliaga', '$2y$10$kqhldvFsXVcFkstDraB84..raG0t2twk9Lczs7pdcjR2Lg4bi7EY6', 0),
(53, 'gab_gmail_com.jpg', 'Gab', 'Gab', 'gab@gmail.com', 'Male', 9392925229, 'Gen Tinio', '$2y$10$ewu4rqhwpjPEdZMjfWpqBe1FfWs1yYeddkuk6uLvyTOnMDZpy4wdq', 0),
(54, '', 'Gab', 'Gab', 'gab123@gmail.com', 'gg', 9392925229, 'Poblacion East 2, Aliaga', 'GG', 0),
(55, 'GB_GMAIL_com.jpg', 'Gab', 'Gab', 'GB@GMAIL.com', 'Male', 9392925229, 'Gen Tinio', '$2y$10$qPFrPtmYYlFObD8TIErFseJt7GoHDwQ0GoXLnkVDaI70u/nbj5FDi', 0),
(56, 'Mark_olpot_yahoo_com.jpg', 'Gab', 'Gab', 'Mark_olpot@yahoo.com', 'Male', 9392925229, 'Gen Tinio', '$2y$10$QWDrYW.Ky2qSusj56hkfhOwoQoRobnTdGu5PHnrv73hvbjyb0oNoW', 0),
(57, '', 'Gab', 'Gab', 'gg@gmail.com', 'gg', 9882215277, 'Poblacion East 2, Aliaga', '$2y$10$QPUzjsuvxqIqMAoShvS9yOlgBlLxK0DRz2I89vTbmf938QIRgybXK', 0),
(58, NULL, '', '', 'gh@gmail.com', '', 0, '', 'gg', 0),
(59, '', 'Gab', 'God', 'azarik@gmail.com', 'gab', 45454, 'gab', '$2y$10$7j4V1EEXorFOloQtFklkZeLaiaFpIEfWHKL/AHwgOYcjJ0xxUOSY6', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`adminId`);

--
-- Indexes for table `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`messageId`),
  ADD KEY `senderId` (`senderId`,`receiverId`),
  ADD KEY `receiverId` (`receiverId`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`plantid`),
  ADD KEY `seller_id` (`added_by`);

--
-- Indexes for table `product_archive`
--
ALTER TABLE `product_archive`
  ADD PRIMARY KEY (`archiveID`);

--
-- Indexes for table `sellers`
--
ALTER TABLE `sellers`
  ADD PRIMARY KEY (`seller_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `seller_applicant`
--
ALTER TABLE `seller_applicant`
  ADD PRIMARY KEY (`applicantID`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `seller_applicant_archive`
--
ALTER TABLE `seller_applicant_archive`
  ADD PRIMARY KEY (`archiveID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `email` (`email`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `adminId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chats`
--
ALTER TABLE `chats`
  MODIFY `messageId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=591;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `plantid` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `product_archive`
--
ALTER TABLE `product_archive`
  MODIFY `archiveID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `sellers`
--
ALTER TABLE `sellers`
  MODIFY `seller_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `seller_applicant`
--
ALTER TABLE `seller_applicant`
  MODIFY `applicantID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `seller_applicant_archive`
--
ALTER TABLE `seller_applicant_archive`
  MODIFY `archiveID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chats`
--
ALTER TABLE `chats`
  ADD CONSTRAINT `chats_ibfk_1` FOREIGN KEY (`senderId`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `chats_ibfk_2` FOREIGN KEY (`receiverId`) REFERENCES `users` (`id`);

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `sellers` (`seller_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sellers`
--
ALTER TABLE `sellers`
  ADD CONSTRAINT `sellers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `seller_applicant`
--
ALTER TABLE `seller_applicant`
  ADD CONSTRAINT `seller_applicant_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

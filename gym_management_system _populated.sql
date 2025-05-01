-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 29, 2025 at 01:55 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gym_management_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `Admin_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`Admin_id`) VALUES
(1);

-- --------------------------------------------------------

--
-- Table structure for table `body_measurement`
--

CREATE TABLE `body_measurement` (
  `Measurement_ID` int(11) NOT NULL,
  `Member_ID` int(11) DEFAULT NULL,
  `Trainer_ID` int(11) DEFAULT NULL,
  `Height` decimal(5,2) DEFAULT NULL,
  `Weight` decimal(5,2) DEFAULT NULL,
  `Date_of_measurement` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `body_measurement`
--

INSERT INTO `body_measurement` (`Measurement_ID`, `Member_ID`, `Trainer_ID`, `Height`, `Weight`, `Date_of_measurement`) VALUES
(1, 7, 2, 170.00, 65.00, '2025-04-27'),
(2, 11, 2, 163.00, 62.00, '2025-04-27'),
(3, 8, 2, 165.00, 66.00, '2025-04-27'),
(4, 9, 2, 165.00, 63.00, '2025-04-27'),
(5, 10, 2, 172.00, 63.00, '2025-04-27'),
(6, 12, 3, 180.00, 80.00, '2025-04-27'),
(7, 13, 4, 172.00, 78.00, '2025-04-27'),
(8, 14, 2, 169.00, 68.00, '2025-04-27'),
(9, 15, 3, 173.00, 70.00, '2025-04-27'),
(10, 16, 4, 165.00, 72.00, '2025-04-27'),
(11, 17, 2, 178.00, 82.00, '2025-04-27'),
(12, 18, 3, 174.00, 76.00, '2025-04-27'),
(13, 19, 5, 160.00, 64.00, '2025-04-27'),
(14, 20, 2, 177.00, 79.00, '2025-04-27'),
(15, 21, 3, 168.00, 66.00, '2025-04-27'),
(16, 22, 5, 162.00, 60.00, '2025-04-27'),
(17, 23, 2, 170.00, 71.00, '2025-04-27'),
(18, 24, 3, 165.00, 69.00, '2025-04-27'),
(19, 25, 4, 180.00, 85.00, '2025-04-27'),
(20, 26, 2, 174.00, 77.00, '2025-04-27');

-- --------------------------------------------------------

--
-- Table structure for table `gym_equipment`
--

CREATE TABLE `gym_equipment` (
  `Equipment_ID` int(11) NOT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `Type` varchar(50) DEFAULT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `Condition` varchar(50) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `Admin_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gym_equipment`
--

INSERT INTO `gym_equipment` (`Equipment_ID`, `Name`, `Type`, `Quantity`, `Condition`, `Description`, `Admin_id`) VALUES
(301, 'Treadmill', 'Cardio', 5, 'Good', 'Motorized running machine', 1),
(302, 'Dumbbell Set', 'Weights', 10, 'Excellent', 'Various weight dumbbells', 1),
(303, 'Bench Press', 'Strength', 3, 'Good', 'Flat bench with barbell rack', 1),
(304, 'Elliptical Trainer', 'Cardio', 4, 'Fair', 'Low-impact cardio machine', 1),
(305, 'Pull Up Bar', 'Strength', 5, 'New', 'Upper Body Strength', 1);

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE `member` (
  `Member_ID` int(11) NOT NULL,
  `Date_of_joining` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `member`
--

INSERT INTO `member` (`Member_ID`, `Date_of_joining`) VALUES
(7, '2025-04-27'),
(8, '2025-04-27'),
(9, '2025-04-27'),
(10, '2025-04-27'),
(11, '2025-04-27'),
(12, '2025-04-27'),
(13, '2025-04-27'),
(14, '2025-04-27'),
(15, '2025-04-27'),
(16, '2025-04-27'),
(17, '2025-04-27'),
(18, '2025-04-27'),
(19, '2025-04-27'),
(20, '2025-04-27'),
(21, '2025-04-27'),
(22, '2025-04-27'),
(23, '2025-04-27'),
(24, '2025-04-27'),
(25, '2025-04-27'),
(26, '2025-04-27');

-- --------------------------------------------------------

--
-- Table structure for table `membership_plan`
--

CREATE TABLE `membership_plan` (
  `Plan_id` int(11) NOT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `Duration` int(11) DEFAULT NULL,
  `Amount` decimal(10,2) DEFAULT NULL,
  `Created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `membership_plan`
--

INSERT INTO `membership_plan` (`Plan_id`, `Name`, `Duration`, `Amount`, `Created_by`) VALUES
(101, '1 Month Plan', 30, 1500.00, 1),
(102, '3 Month Plan', 90, 4000.00, 1),
(103, '6 Month Plan', 180, 7500.00, 1),
(104, '12 Month Plan', 365, 12000.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `receipt`
--

CREATE TABLE `receipt` (
  `Receipt_no` int(11) NOT NULL,
  `Member_id` int(11) DEFAULT NULL,
  `Generated_by` int(11) DEFAULT NULL,
  `Plan_id` int(11) DEFAULT NULL,
  `Amount_paid` decimal(10,2) DEFAULT NULL,
  `Date_of_creation` date DEFAULT NULL,
  `Date_of_purchase` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receipt`
--

INSERT INTO `receipt` (`Receipt_no`, `Member_id`, `Generated_by`, `Plan_id`, `Amount_paid`, `Date_of_creation`, `Date_of_purchase`) VALUES
(1, 7, 27, 101, 1500.00, '2025-04-27', '2025-04-27'),
(2, 8, 27, 101, 1500.00, '2025-04-27', '2025-04-27'),
(3, 9, 27, 101, 1500.00, '2025-04-27', '2025-04-27'),
(4, 10, 27, 101, 1500.00, '2025-04-27', '2025-04-27'),
(5, 11, 27, 101, 1500.00, '2025-04-27', '2025-04-27'),
(6, 12, 27, 101, 1500.00, '2025-04-27', '2025-04-27'),
(7, 13, 27, 102, 4000.00, '2025-04-27', '2025-04-27'),
(8, 14, 27, 102, 4000.00, '2025-04-27', '2025-04-27'),
(9, 15, 27, 102, 4000.00, '2025-04-27', '2025-04-27'),
(10, 16, 27, 102, 4000.00, '2025-04-27', '2025-04-27'),
(11, 17, 27, 103, 7500.00, '2025-04-27', '2025-04-27'),
(12, 18, 27, 103, 7500.00, '2025-04-27', '2025-04-27'),
(13, 19, 28, 103, 7500.00, '2025-04-27', '2025-04-27'),
(14, 20, 28, 103, 7500.00, '2025-04-27', '2025-04-27'),
(15, 21, 28, 104, 12000.00, '2025-04-27', '2025-04-27'),
(16, 22, 28, 104, 12000.00, '2025-04-27', '2025-04-27'),
(17, 23, 28, 104, 12000.00, '2025-04-27', '2025-04-27'),
(18, 24, 28, 104, 12000.00, '2025-04-27', '2025-04-27'),
(19, 25, 27, 101, 1500.00, '2025-04-27', '2025-04-27'),
(20, 26, 27, 101, 1500.00, '2025-04-27', '2025-04-27');

-- --------------------------------------------------------

--
-- Table structure for table `receptionist`
--

CREATE TABLE `receptionist` (
  `Receptionist_ID` int(11) NOT NULL,
  `Salary` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receptionist`
--

INSERT INTO `receptionist` (`Receptionist_ID`, `Salary`) VALUES
(27, 18000.00),
(28, 18000.00);

-- --------------------------------------------------------

--
-- Table structure for table `trainer`
--

CREATE TABLE `trainer` (
  `Trainer_ID` int(11) NOT NULL,
  `Hourly_wage` decimal(10,2) DEFAULT NULL,
  `Shift_start` time DEFAULT NULL,
  `Shift_end` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainer`
--

INSERT INTO `trainer` (`Trainer_ID`, `Hourly_wage`, `Shift_start`, `Shift_end`) VALUES
(2, 500.00, '08:00:00', '12:00:00'),
(3, 550.00, '12:00:00', '16:00:00'),
(4, 600.00, '16:00:00', '20:00:00'),
(5, 650.00, '06:00:00', '10:00:00'),
(6, 700.00, '10:00:00', '14:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `trains`
--

CREATE TABLE `trains` (
  `Member_ID` int(11) NOT NULL,
  `Trainer_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trains`
--

INSERT INTO `trains` (`Member_ID`, `Trainer_ID`) VALUES
(7, 2),
(8, 3),
(9, 4),
(10, 5),
(11, 6),
(12, 2),
(13, 3),
(14, 4),
(15, 5),
(16, 6),
(17, 2),
(18, 3),
(19, 4),
(20, 5),
(21, 6),
(22, 2),
(23, 3),
(24, 4),
(25, 5),
(26, 6);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `ID` int(11) NOT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `Email` varchar(255) NOT NULL,
  `Age` int(11) DEFAULT NULL,
  `Gender` enum('Male','Female','Other') DEFAULT NULL,
  `Address` text DEFAULT NULL,
  `Password` varchar(100) DEFAULT NULL,
  `User_type` enum('Admin','Trainer','Receptionist','Member') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`ID`, `Name`, `Email`, `Age`, `Gender`, `Address`, `Password`, `User_type`) VALUES
(1, 'Lutfur Rahman Sayem', 'lutfursayem@gmail.com', 23, 'Male', 'Dhanmondi, Dhaka', 'admin123', 'Admin'),
(2, 'MD Jahan', 'mdjahan@gmail.com', 29, 'Male', 'Narayanganj, Dhaka', 'trainer123', 'Trainer'),
(3, 'Sumon Hossain', 'sumonhossain@gmail.com', 32, 'Male', 'Mohakhali, Dhaka', 'trainer321', 'Trainer'),
(4, 'Sabbir Ahmed', 'sabbirahmed@gmail.com', 30, 'Male', 'Uttara, Dhaka', 'trainer1234', 'Trainer'),
(5, 'Shamim Hossain', 'shaminhossain@gmail.com', 28, 'Male', 'Gulshan, Dhaka', 'trainer5678', 'Trainer'),
(6, 'Sonia Nasrin', 'sonianasrin@gmail.com', 27, 'Female', 'Banani, Dhaka', 'trainer9876', 'Trainer'),
(7, 'Tanvir Ahmed', 'tanvirahmed@gmail.com', 22, 'Male', 'Uttara, Dhaka', 'mem1', 'Member'),
(8, 'Sharmin Akter', 'sharminakter@gmail.com', 25, 'Female', 'Badda, Dhaka', 'mem2', 'Member'),
(9, 'Mehedi Hasan', 'mehedihasan@gmail.com', 28, 'Male', 'Khilgaon, Dhaka', 'mem3', 'Member'),
(10, 'Afsana Mimi', 'afsanamimi@gmail.com', 21, 'Female', 'Shantinagar, Dhaka', 'mem4', 'Member'),
(11, 'Kamrul Hasan', 'kamrulhasan@gmail.com', 23, 'Male', 'Merul Badda, Dhaka', 'mem5', 'Member'),
(12, 'Shadik Ullah', 'shadikullah@gmail.com', 23, 'Male', 'Merul Badda, Dhaka', 'mem6', 'Member'),
(13, 'Rina Begum', 'rinabegum@gmail.com', 26, 'Female', 'Moghbazar, Dhaka', 'mem7', 'Member'),
(14, 'Mahrin Loba', 'mahrinloba@gmail.com', 22, 'Female', 'Farmgate, Dhaka', 'mem8', 'Member'),
(15, 'Hina Tasnim', 'hinatasnim@gmail.com', 27, 'Female', 'Rampura, Dhaka', 'mem9', 'Member'),
(16, 'Fahim Sayed', 'fahimsayed@gmail.com', 29, 'Male', 'Khilgaon, Dhaka', 'mem10', 'Member'),
(17, 'Shamim Akter', 'shamimakter@gmail.com', 0, 'Female', 'Bashundhara, Dhaka', 'mem11', 'Member'),
(18, 'Mohammad Ali', 'mohammadali@gmail.com', 30, 'Male', 'Mirpur, Dhaka', 'mem12', 'Member'),
(19, 'Mimi Akter', 'mimiakter@gmail.com', 20, 'Female', 'Gulshan, Dhaka', 'mem13', 'Member'),
(20, 'Shaila Sultana', 'shailasultana@gmail.com', 22, 'Female', 'Motijheel, Dhaka', 'mem14', 'Member'),
(21, 'Rasel Chowdhury', 'raselchowdhury@gmail.com', 32, 'Male', 'Mohakhali, Dhaka', 'mem15', 'Member'),
(22, 'Sohail Riaz', 'sohailriaz@gmail.com', 26, 'Male', 'Khilgaon, Dhaka', 'mem16', 'Member'),
(23, 'Tanjina Akter', 'tanjinaakter@gmail.com', 28, 'Female', 'Banani, Dhaka', 'mem17', 'Member'),
(24, 'Rupa Sultana', 'rupasultana@gmail.com', 30, 'Female', 'Mirpur, Dhaka', 'mem18', 'Member'),
(25, 'Samiul Islam', 'samiulislam@gmail.com', 23, 'Male', 'Uttara, Dhaka', 'mem19', 'Member'),
(26, 'Moushumi Tasnim', 'moushumitasnim@gmail.com', 22, 'Female', 'Shantinagar, Dhaka', 'mem20', 'Member'),
(27, 'Nusrat Jahan', 'nusratjahan@gmail.com', 26, 'Female', 'Banani, Dhaka', 'rec123', 'Receptionist'),
(28, 'Zara Rahman', 'zararahman@gmail.com', 24, 'Female', 'Gulshan, Dhaka', 'rec456', 'Receptionist');

-- --------------------------------------------------------

--
-- Table structure for table `uses`
--

CREATE TABLE `uses` (
  `Plan_ID` int(11) NOT NULL,
  `Member_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uses`
--

INSERT INTO `uses` (`Plan_ID`, `Member_ID`) VALUES
(101, 7),
(101, 11),
(101, 15),
(101, 19),
(101, 23),
(102, 8),
(102, 12),
(102, 16),
(102, 20),
(102, 24),
(103, 9),
(103, 13),
(103, 17),
(103, 21),
(103, 25),
(104, 10),
(104, 14),
(104, 18),
(104, 22),
(104, 26);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`Admin_id`);

--
-- Indexes for table `body_measurement`
--
ALTER TABLE `body_measurement`
  ADD PRIMARY KEY (`Measurement_ID`),
  ADD KEY `Member_ID` (`Member_ID`),
  ADD KEY `Trainer_ID` (`Trainer_ID`);

--
-- Indexes for table `gym_equipment`
--
ALTER TABLE `gym_equipment`
  ADD PRIMARY KEY (`Equipment_ID`),
  ADD KEY `Admin_id` (`Admin_id`);

--
-- Indexes for table `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`Member_ID`);

--
-- Indexes for table `membership_plan`
--
ALTER TABLE `membership_plan`
  ADD PRIMARY KEY (`Plan_id`),
  ADD KEY `Created_by` (`Created_by`);

--
-- Indexes for table `receipt`
--
ALTER TABLE `receipt`
  ADD PRIMARY KEY (`Receipt_no`),
  ADD KEY `Member_id` (`Member_id`),
  ADD KEY `Generated_by` (`Generated_by`),
  ADD KEY `Plan_id` (`Plan_id`);

--
-- Indexes for table `receptionist`
--
ALTER TABLE `receptionist`
  ADD PRIMARY KEY (`Receptionist_ID`);

--
-- Indexes for table `trainer`
--
ALTER TABLE `trainer`
  ADD PRIMARY KEY (`Trainer_ID`);

--
-- Indexes for table `trains`
--
ALTER TABLE `trains`
  ADD PRIMARY KEY (`Member_ID`,`Trainer_ID`),
  ADD KEY `Trainer_ID` (`Trainer_ID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `uses`
--
ALTER TABLE `uses`
  ADD PRIMARY KEY (`Plan_ID`,`Member_ID`),
  ADD KEY `Member_ID` (`Member_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `body_measurement`
--
ALTER TABLE `body_measurement`
  MODIFY `Measurement_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`Admin_id`) REFERENCES `user` (`ID`);

--
-- Constraints for table `body_measurement`
--
ALTER TABLE `body_measurement`
  ADD CONSTRAINT `body_measurement_ibfk_1` FOREIGN KEY (`Member_ID`) REFERENCES `member` (`Member_ID`),
  ADD CONSTRAINT `body_measurement_ibfk_2` FOREIGN KEY (`Trainer_ID`) REFERENCES `trainer` (`Trainer_ID`);

--
-- Constraints for table `gym_equipment`
--
ALTER TABLE `gym_equipment`
  ADD CONSTRAINT `gym_equipment_ibfk_1` FOREIGN KEY (`Admin_id`) REFERENCES `admin` (`Admin_id`);

--
-- Constraints for table `member`
--
ALTER TABLE `member`
  ADD CONSTRAINT `member_ibfk_1` FOREIGN KEY (`Member_ID`) REFERENCES `user` (`ID`);

--
-- Constraints for table `membership_plan`
--
ALTER TABLE `membership_plan`
  ADD CONSTRAINT `membership_plan_ibfk_1` FOREIGN KEY (`Created_by`) REFERENCES `admin` (`Admin_id`);

--
-- Constraints for table `receipt`
--
ALTER TABLE `receipt`
  ADD CONSTRAINT `receipt_ibfk_1` FOREIGN KEY (`Member_id`) REFERENCES `member` (`Member_ID`),
  ADD CONSTRAINT `receipt_ibfk_2` FOREIGN KEY (`Generated_by`) REFERENCES `receptionist` (`Receptionist_ID`),
  ADD CONSTRAINT `receipt_ibfk_3` FOREIGN KEY (`Plan_id`) REFERENCES `membership_plan` (`Plan_id`);

--
-- Constraints for table `receptionist`
--
ALTER TABLE `receptionist`
  ADD CONSTRAINT `receptionist_ibfk_1` FOREIGN KEY (`Receptionist_ID`) REFERENCES `user` (`ID`);

--
-- Constraints for table `trainer`
--
ALTER TABLE `trainer`
  ADD CONSTRAINT `trainer_ibfk_1` FOREIGN KEY (`Trainer_ID`) REFERENCES `user` (`ID`);

--
-- Constraints for table `trains`
--
ALTER TABLE `trains`
  ADD CONSTRAINT `trains_ibfk_1` FOREIGN KEY (`Member_ID`) REFERENCES `member` (`Member_ID`),
  ADD CONSTRAINT `trains_ibfk_2` FOREIGN KEY (`Trainer_ID`) REFERENCES `trainer` (`Trainer_ID`);

--
-- Constraints for table `uses`
--
ALTER TABLE `uses`
  ADD CONSTRAINT `uses_ibfk_1` FOREIGN KEY (`Plan_ID`) REFERENCES `membership_plan` (`Plan_id`),
  ADD CONSTRAINT `uses_ibfk_2` FOREIGN KEY (`Member_ID`) REFERENCES `member` (`Member_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

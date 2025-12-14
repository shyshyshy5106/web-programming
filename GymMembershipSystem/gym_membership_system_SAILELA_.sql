-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 14, 2025 at 04:33 PM
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
-- Database: `gym membership system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `firstname` varchar(225) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `id` int(11) NOT NULL,
  `profile_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`firstname`, `lastname`, `role`, `email`, `password`, `is_active`, `id`, `profile_id`) VALUES
('Princess Shaira Mae', 'Sailela', 'Admin', 'sailelaprincessshairamae@gmail.com', '$2y$10$WBNCN8RcSQb0I9o7Vjz8Suz342cw8MA2znNBOst10Ry2yxwyaAzni', 1, 1, 0),
('Jeoff Nikko', 'Ricafort', 'Staff', 'ricafort@gmail.com', '$2y$10$r4./zSXOSePYIqf45yIUTO7Gnanl94mUBm8TbaOtdz1Sb3QjP0OnK', 1, 2, 0),
('Dixon', 'Trumata', 'Staff', 'dixontrumatajr@gmail.com', '$2y$10$kjnx91I2qjV9q8qGHZA6K.ypbHhk3tM6fk5vC2hpSrjfkLOwTF0Ee', 1, 3, 0),
('Shaira', 'Sailela', 'Admin', 'sailelaprincess.rutherford2023@gmail.com', '$2y$10$H.LACaxODkwZv3pigxNRYe5pY2.yjcNJRzi7YTDIf/CGkj1ilpExi', 1, 4, 0),
('Shaira', 'Sailela', 'Admin', 'sailelaprincess.rutherford2023@gmail.com', '$2y$10$Qvix1r7GnHS/.b9iHWFcmORl4mlL9Z/.PvGE3ioMH8NVohc76O9/.', 1, 5, 0);

-- --------------------------------------------------------

--
-- Table structure for table `email_logs`
--

CREATE TABLE `email_logs` (
  `email_log_id` int(11) NOT NULL,
  `recipient` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message_preview` longtext DEFAULT NULL,
  `sender_email` varchar(255) NOT NULL,
  `sender_name` varchar(255) DEFAULT NULL,
  `status` enum('PENDING','SENT_SUCCESS','SENT_FAILED','TEST_MODE_SUCCESS') DEFAULT 'PENDING',
  `notes` longtext DEFAULT NULL,
  `sent_by_user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_logs`
--

INSERT INTO `email_logs` (`email_log_id`, `recipient`, `subject`, `message_preview`, `sender_email`, `sender_name`, `status`, `notes`, `sent_by_user_id`, `created_at`, `updated_at`) VALUES
(1, 'sailelaprincess.rutherford2023@gmail.com', 'Payment Due Notification', 'test', 'noreply@gymmembership.local', 'Gym Membership System', 'SENT_FAILED', 'Failed after 3 attempts: PHP mail() function failed. Ensure mail server is configured or use PHPMailer.', NULL, '2025-11-19 16:47:08', '2025-11-19 16:47:18'),
(2, 'sailelaprincess.rutherford2023@gmail.com', 'Special Promotion', 'test', 'noreply@gymmembership.local', 'Gym Membership System', 'PENDING', NULL, NULL, '2025-11-19 16:54:58', '2025-11-19 16:54:58'),
(3, 'sailelaprincess.rutherford2023@gmail.com', 'Payment Due Notification', 'test', 'noreply@gymmembership.local', 'Gym Membership System', 'SENT_FAILED', 'Failed after 3 attempts: PHP mail() function failed. Ensure mail server is configured or use PHPMailer.', NULL, '2025-11-19 16:57:10', '2025-11-19 16:57:20'),
(4, 'test@example.com', 'Test Subject', 'Test Message Body', 'noreply@gymmembership.local', 'System Admin', 'TEST_MODE_SUCCESS', 'Email logged in test mode (not sent)', NULL, '2025-11-19 16:59:01', '2025-11-19 16:59:01'),
(5, 'sailelaprincess.rutherford2023@gmail.com', 'Payment Due Notification', 'test', 'noreply@gymmembership.local', 'Gym Membership System', 'TEST_MODE_SUCCESS', 'Email logged in test mode (not sent)', NULL, '2025-11-19 17:01:50', '2025-11-19 17:01:50'),
(6, 'test@example.com', 'Test Subject', 'Test Message Body', 'noreply@gymmembership.local', 'System Admin', 'SENT_FAILED', 'Failed after 3 attempts: PHP mail() function failed. Ensure mail server is configured or use PHPMailer.', NULL, '2025-11-19 17:08:04', '2025-11-19 17:08:23'),
(7, 'test@example.com', 'Test Subject', 'Test Message Body', 'noreply@gymmembership.local', 'System Admin', 'SENT_FAILED', 'Failed after 3 attempts: PHP mail() function failed. Ensure mail server is configured or use PHPMailer.', NULL, '2025-11-19 17:13:24', '2025-11-19 17:13:37'),
(8, 'sailelaprincess.rutherford2023@gmail.com', 'Test Email', 'This is a test email from the Gym Membership System', 'noreply@gymmembership.local', 'Gym System', 'SENT_FAILED', 'Failed after 3 attempts: PHP mail() function failed. Ensure mail server is configured or use PHPMailer.', NULL, '2025-11-19 17:14:23', '2025-11-19 17:14:36'),
(9, 'sailelaprincess.rutherford2023@gmail.com', 'Test Email', 'This is a test email from the Gym Membership System', 'noreply@gymmembership.local', 'Gym System', 'TEST_MODE_SUCCESS', 'Email logged in test mode (not sent)', NULL, '2025-11-19 17:15:39', '2025-11-19 17:15:39'),
(10, 'sailelaprincess.rutherford2023@gmail.com', 'Payment Due Notification', 'test 2', 'noreply@gymmembership.local', 'Gym Membership System', 'TEST_MODE_SUCCESS', 'Email logged in test mode (not sent)', NULL, '2025-11-19 17:16:33', '2025-11-19 17:16:33'),
(11, 'sailelaprincess.rutherford2023@gmail.com', 'Test Email', 'This is a test email from the Gym Membership System', 'noreply@gymmembership.local', 'Gym System', 'TEST_MODE_SUCCESS', 'Email logged in test mode (not sent)', NULL, '2025-11-19 17:38:32', '2025-11-19 17:38:32'),
(12, 'sailelaprincess.rutherford2023@gmail.com', 'Membership Expiry Warning', 'test 3', 'noreply@gymmembership.local', 'Gym Membership System', 'TEST_MODE_SUCCESS', 'Email logged in test mode (not sent)', NULL, '2025-11-19 17:40:14', '2025-11-19 17:40:14'),
(13, 'sailelaprincess.rutherford2023@gmail.com', 'Membership Expiry Warning', 'test 3', 'noreply@gymmembership.local', 'Gym Membership System', 'TEST_MODE_SUCCESS', 'Email logged in test mode (not sent)', NULL, '2025-11-19 17:40:25', '2025-11-19 17:40:25'),
(14, 'sailelaprincess.rutherford2023@gmail.com', 'Test Email', 'This is a test email from the Gym Membership System', 'noreply@gymmembership.local', 'Gym System', 'SENT_FAILED', 'Failed after 3 attempts: PHPMailer Error: SMTP Error: Could not authenticate.', NULL, '2025-11-19 17:45:23', '2025-11-19 17:45:39'),
(15, 'sailelaprincess.rutherford2023@gmail.com', 'Welcome to Our Gym', 'test 4', 'sailelaprincess.rutherford2023@gmail.com', 'Gym Membership System', 'SENT_FAILED', 'Failed after 3 attempts: PHPMailer Error: SMTP Error: Could not authenticate.', NULL, '2025-11-19 17:47:23', '2025-11-19 17:47:40'),
(16, 'sailelaprincess.rutherford2023@gmail.com', 'Welcome to Our Gym', 'test 4', 'sailelaprincess.rutherford2023@gmail.com', 'Gym Membership System', 'SENT_FAILED', 'Failed after 3 attempts: PHPMailer Error: SMTP Error: Could not authenticate.', NULL, '2025-11-19 17:47:40', '2025-11-19 17:47:57'),
(17, 'sailelaprincess.rutherford2023@gmail.com', 'Test Email', 'This is a test email from the Gym Membership System', 'sailelaprincess.rutherford2023@gmail.com', 'Gym System', 'SENT_FAILED', 'Failed after 3 attempts: PHPMailer Error: SMTP Error: Could not authenticate.', NULL, '2025-11-19 17:57:40', '2025-11-19 17:57:54'),
(18, 'sailelaprincess.rutherford2023@gmail.com', 'Test Email', 'This is a test email from the Gym Membership System', 'sailelaprincess.rutherford2023@gmail.com', 'Gym System', 'SENT_FAILED', 'Failed after 3 attempts: PHPMailer Error: SMTP Error: Could not authenticate.', NULL, '2025-11-19 17:58:38', '2025-11-19 17:58:51'),
(19, 'sailelaprincess.rutherford2023@gmail.com', 'Membership Expiry Warning', 'test 5', 'sailelaprincess.rutherford2023@gmail.com', 'Gym Membership System', 'TEST_MODE_SUCCESS', 'Email logged in test mode (not sent)', NULL, '2025-11-19 18:01:32', '2025-11-19 18:01:32'),
(20, 'sailelaprincess.rutherford2023@gmail.com', 'Payment Due Notification', '6', 'sailelaprincess.rutherford2023@gmail.com', 'Gym Membership System', 'SENT_FAILED', 'Failed after 3 attempts: PHPMailer Error: SMTP Error: Could not authenticate.', NULL, '2025-11-19 18:16:14', '2025-11-19 18:16:28'),
(21, 'sailelaprincess.rutherford2023@gmail.com', 'Membership Expiry Warning', '6', 'sailelaprincess.rutherford2023@gmail.com', 'Gym Membership System', 'SENT_FAILED', 'Failed after 3 attempts: PHPMailer Error: SMTP Error: Could not authenticate.', NULL, '2025-11-19 18:35:03', '2025-11-19 18:35:16'),
(22, 'admin@gym.local', 'Test Email - Gym Membership System', 'This is a test email from your Gym Membership System.\n\nIf you received this, your email configuration is working!\n\nSent: 2025-11-19 19:52:01\nSystem: Gym Membership System', 'noreply@gymmembership.local', 'Gym System Tester', 'PENDING', NULL, NULL, '2025-11-19 18:52:01', '2025-11-19 18:52:01'),
(23, 'sailelaprincess.rutherford2023@gmail.com', 'GymMembershipSystem Test Email - 2025-11-19 20:30:15', 'This is a test email from GymMembershipSystem sent at 2025-11-19T20:30:15+01:00\n\nIf you received this message, SMTP is working.\n', 'noreply@gymmembership.local', 'System Test', 'SENT_FAILED', 'Failed after 3 attempts: PHPMailer Exception: SMTP Error: Could not authenticate. | ErrorInfo: SMTP Error: Could not authenticate.', NULL, '2025-11-19 19:30:15', '2025-11-19 19:30:28'),
(24, 'sailelaprincess.rutherford2023@gmail.com', 'GymMembershipSystem Test Email - 2025-11-19 20:30:55', 'This is a test email from GymMembershipSystem sent at 2025-11-19T20:30:55+01:00\n\nIf you received this message, SMTP is working.\n', 'noreply@gymmembership.local', 'System Test', 'SENT_FAILED', 'Failed after 3 attempts: PHPMailer Exception: SMTP Error: Could not authenticate. | ErrorInfo: SMTP Error: Could not authenticate.', NULL, '2025-11-19 19:30:55', '2025-11-19 19:31:08'),
(25, 'sailelaprincess.rutherford2023@gmail.com', 'GymMembershipSystem Test Email - 2025-11-19 20:32:09', 'This is a test email from GymMembershipSystem sent at 2025-11-19T20:32:09+01:00\n\nIf you received this message, SMTP is working.\n', 'noreply@gymmembership.local', 'System Test', 'SENT_FAILED', 'Failed after 3 attempts: PHPMailer Exception: SMTP Error: Could not authenticate. | ErrorInfo: SMTP Error: Could not authenticate.', NULL, '2025-11-19 19:32:09', '2025-11-19 19:32:22'),
(26, 'sailelaprincess.rutherford2023@gmail.com', 'GymMembershipSystem Test Email - 2025-11-19 20:49:05', 'This is a test email from GymMembershipSystem sent at 2025-11-19T20:49:05+01:00\n\nIf you received this message, SMTP is working.\n', 'your-email@gmail.com', 'System Test', 'SENT_FAILED', 'Failed after 3 attempts: PHPMailer Exception: SMTP Error: Could not authenticate. | ErrorInfo: SMTP Error: Could not authenticate.', NULL, '2025-11-19 19:49:05', '2025-11-19 19:49:18'),
(27, 'sailelaprincess.rutherford2023@gmail.com', 'GymMembershipSystem Test Email - 2025-11-19 20:49:32', 'This is a test email from GymMembershipSystem sent at 2025-11-19T20:49:32+01:00\n\nIf you received this message, SMTP is working.\n', 'your-email@gmail.com', 'System Test', 'SENT_FAILED', 'Failed after 3 attempts: PHPMailer Exception: SMTP Error: Could not authenticate. | ErrorInfo: SMTP Error: Could not authenticate.', NULL, '2025-11-19 19:49:32', '2025-11-19 19:49:45'),
(28, 'sailelaprincess.rutherford2023@gmail.com', 'GymMembershipSystem Test Email - 2025-11-19 20:54:03', 'This is a test email from GymMembershipSystem sent at 2025-11-19T20:54:03+01:00\n\nIf you received this message, SMTP is working.\n', 'noreply@gymmembership.local', 'System Test', 'SENT_SUCCESS', 'Email sent successfully on attempt 1', NULL, '2025-11-19 19:54:03', '2025-11-19 19:54:07'),
(29, 'sailelaprincess.rutherford2023@gmail.com', 'Special Promotion', '77', 'noreply@gymmembership.local', 'Gym Membership System', 'SENT_FAILED', 'Failed after 3 attempts: PHPMailer Exception: The following From address failed: noreply@gymmembership.local : MAIL FROM command failed,Authentication Required. For more information, go to\r\n https://support.google.com/accounts/troubleshooter/2402620. d9443c01a7336-29b5b29b799sm2557985ad.83 - gsmtp\r\n,530,5.7.0 SMTP server error: MAIL FROM command failed Detail: Authentication Required. For more information, go to\r\n https://support.google.com/accounts/troubleshooter/2402620. d9443c01a7336-29b5b29b799sm2557985ad.83 - gsmtp\r\n SMTP code: 530 Additional SMTP info: 5.7.0 | ErrorInfo: The following From address failed: noreply@gymmembership.local : MAIL FROM command failed,Authentication Required. For more information, go to\r\n https://support.google.com/accounts/troubleshooter/2402620. d9443c01a7336-29b5b29b799sm2557985ad.83 - gsmtp\r\n,530,5.7.0 SMTP server error: MAIL FROM command failed Detail: Authentication Required. For more information, go to\r\n https://support.google.com/accounts/troubleshooter/2402620. d9443c01a7336-29b5b29b799sm2557985ad.83 - gsmtp\r\n SMTP code: 530 Additional SMTP info: 5.7.0 SMTP server error: MAIL FROM command failed Detail: Authentication Required. For more information, go to\r\n https://support.google.com/accounts/troubleshooter/2402620. d9443c01a7336-29b5b29b799sm2557985ad.83 - gsmtp\r\n SMTP code: 530 Additional SMTP info: 5.7.0', NULL, '2025-11-19 19:55:21', '2025-11-19 19:55:31'),
(30, 'sailelaprincess.rutherford2023@gmail.com', 'Membership Renewal Reminder', '8', 'noreply@gymmembership.local', 'Gym Membership System', 'SENT_FAILED', 'Failed after 3 attempts: PHPMailer Exception: The following From address failed: noreply@gymmembership.local : MAIL FROM command failed,Authentication Required. For more information, go to\r\n https://support.google.com/accounts/troubleshooter/2402620. 98e67ed59e1d1-3472692e5d3sm346742a91.9 - gsmtp\r\n,530,5.7.0 SMTP server error: MAIL FROM command failed Detail: Authentication Required. For more information, go to\r\n https://support.google.com/accounts/troubleshooter/2402620. 98e67ed59e1d1-3472692e5d3sm346742a91.9 - gsmtp\r\n SMTP code: 530 Additional SMTP info: 5.7.0 | ErrorInfo: The following From address failed: noreply@gymmembership.local : MAIL FROM command failed,Authentication Required. For more information, go to\r\n https://support.google.com/accounts/troubleshooter/2402620. 98e67ed59e1d1-3472692e5d3sm346742a91.9 - gsmtp\r\n,530,5.7.0 SMTP server error: MAIL FROM command failed Detail: Authentication Required. For more information, go to\r\n https://support.google.com/accounts/troubleshooter/2402620. 98e67ed59e1d1-3472692e5d3sm346742a91.9 - gsmtp\r\n SMTP code: 530 Additional SMTP info: 5.7.0 SMTP server error: MAIL FROM command failed Detail: Authentication Required. For more information, go to\r\n https://support.google.com/accounts/troubleshooter/2402620. 98e67ed59e1d1-3472692e5d3sm346742a91.9 - gsmtp\r\n SMTP code: 530 Additional SMTP info: 5.7.0', NULL, '2025-11-19 19:58:21', '2025-11-19 19:58:31'),
(31, 'sailelaprincess.rutherford2023@gmail.com', 'Payment Due Notification', 'test', 'noreply@gymmembership.local', 'Gym Membership System', 'SENT_FAILED', 'Failed after 3 attempts: PHPMailer Exception: The following From address failed: noreply@gymmembership.local : MAIL FROM command failed,Authentication Required. For more information, go to\r\n https://support.google.com/accounts/troubleshooter/2402620. d2e1a72fcca58-7c3f155f6e7sm173127b3a.63 - gsmtp\r\n,530,5.7.0 SMTP server error: MAIL FROM command failed Detail: Authentication Required. For more information, go to\r\n https://support.google.com/accounts/troubleshooter/2402620. d2e1a72fcca58-7c3f155f6e7sm173127b3a.63 - gsmtp\r\n SMTP code: 530 Additional SMTP info: 5.7.0 | ErrorInfo: The following From address failed: noreply@gymmembership.local : MAIL FROM command failed,Authentication Required. For more information, go to\r\n https://support.google.com/accounts/troubleshooter/2402620. d2e1a72fcca58-7c3f155f6e7sm173127b3a.63 - gsmtp\r\n,530,5.7.0 SMTP server error: MAIL FROM command failed Detail: Authentication Required. For more information, go to\r\n https://support.google.com/accounts/troubleshooter/2402620. d2e1a72fcca58-7c3f155f6e7sm173127b3a.63 - gsmtp\r\n SMTP code: 530 Additional SMTP info: 5.7.0 SMTP server error: MAIL FROM command failed Detail: Authentication Required. For more information, go to\r\n https://support.google.com/accounts/troubleshooter/2402620. d2e1a72fcca58-7c3f155f6e7sm173127b3a.63 - gsmtp\r\n SMTP code: 530 Additional SMTP info: 5.7.0', NULL, '2025-11-19 20:00:19', '2025-11-19 20:00:30'),
(32, 'sailelaprincess.rutherford2023@gmail.com', 'Payment Due Notification', 'mmmm', 'noreply@gymmembership.local', 'Gym Membership System', 'SENT_FAILED', 'Failed after 3 attempts: PHPMailer Exception: The following From address failed: noreply@gymmembership.local : MAIL FROM command failed,Authentication Required. For more information, go to\r\n https://support.google.com/accounts/troubleshooter/2402620. 98e67ed59e1d1-345b053038fsm2821138a91.14 - gsmtp\r\n,530,5.7.0 SMTP server error: MAIL FROM command failed Detail: Authentication Required. For more information, go to\r\n https://support.google.com/accounts/troubleshooter/2402620. 98e67ed59e1d1-345b053038fsm2821138a91.14 - gsmtp\r\n SMTP code: 530 Additional SMTP info: 5.7.0 | ErrorInfo: The following From address failed: noreply@gymmembership.local : MAIL FROM command failed,Authentication Required. For more information, go to\r\n https://support.google.com/accounts/troubleshooter/2402620. 98e67ed59e1d1-345b053038fsm2821138a91.14 - gsmtp\r\n,530,5.7.0 SMTP server error: MAIL FROM command failed Detail: Authentication Required. For more information, go to\r\n https://support.google.com/accounts/troubleshooter/2402620. 98e67ed59e1d1-345b053038fsm2821138a91.14 - gsmtp\r\n SMTP code: 530 Additional SMTP info: 5.7.0 SMTP server error: MAIL FROM command failed Detail: Authentication Required. For more information, go to\r\n https://support.google.com/accounts/troubleshooter/2402620. 98e67ed59e1d1-345b053038fsm2821138a91.14 - gsmtp\r\n SMTP code: 530 Additional SMTP info: 5.7.0', NULL, '2025-11-19 20:04:15', '2025-11-19 20:04:26'),
(33, 'sailelaprincess.rutherford2023@gmail.com', 'Membership Expiry Warning', '     ccccccccccccccccc', 'noreply@gymmembership.local', 'Gym Membership System', 'SENT_FAILED', 'Failed after 3 attempts: PHPMailer Exception: The following From address failed: noreply@gymmembership.local : MAIL FROM command failed,Authentication Required. For more information, go to\r\n https://support.google.com/accounts/troubleshooter/2402620. 41be03b00d2f7-bd760ac4ba2sm281893a12.28 - gsmtp\r\n,530,5.7.0 SMTP server error: MAIL FROM command failed Detail: Authentication Required. For more information, go to\r\n https://support.google.com/accounts/troubleshooter/2402620. 41be03b00d2f7-bd760ac4ba2sm281893a12.28 - gsmtp\r\n SMTP code: 530 Additional SMTP info: 5.7.0 | ErrorInfo: The following From address failed: noreply@gymmembership.local : MAIL FROM command failed,Authentication Required. For more information, go to\r\n https://support.google.com/accounts/troubleshooter/2402620. 41be03b00d2f7-bd760ac4ba2sm281893a12.28 - gsmtp\r\n,530,5.7.0 SMTP server error: MAIL FROM command failed Detail: Authentication Required. For more information, go to\r\n https://support.google.com/accounts/troubleshooter/2402620. 41be03b00d2f7-bd760ac4ba2sm281893a12.28 - gsmtp\r\n SMTP code: 530 Additional SMTP info: 5.7.0 SMTP server error: MAIL FROM command failed Detail: Authentication Required. For more information, go to\r\n https://support.google.com/accounts/troubleshooter/2402620. 41be03b00d2f7-bd760ac4ba2sm281893a12.28 - gsmtp\r\n SMTP code: 530 Additional SMTP info: 5.7.0', NULL, '2025-11-19 20:07:34', '2025-11-19 20:07:45'),
(34, 'sailelaprincess.rutherford2023@gmail.com', 'GymMembershipSystem Test Email - 2025-11-19 21:09:41', 'This is a test email from GymMembershipSystem sent at 2025-11-19T21:09:41+01:00\n\nIf you received this message, SMTP is working.\n', 'noreply@gymmembership.local', 'System Test', 'SENT_FAILED', 'Failed after 3 attempts: PHPMailer Exception: The following From address failed: noreply@gymmembership.local : MAIL FROM command failed,Authentication Required. For more information, go to\r\n https://support.google.com/accounts/troubleshooter/2402620. d2e1a72fcca58-7c3f0b69d16sm193204b3a.53 - gsmtp\r\n,530,5.7.0 SMTP server error: MAIL FROM command failed Detail: Authentication Required. For more information, go to\r\n https://support.google.com/accounts/troubleshooter/2402620. d2e1a72fcca58-7c3f0b69d16sm193204b3a.53 - gsmtp\r\n SMTP code: 530 Additional SMTP info: 5.7.0 | ErrorInfo: The following From address failed: noreply@gymmembership.local : MAIL FROM command failed,Authentication Required. For more information, go to\r\n https://support.google.com/accounts/troubleshooter/2402620. d2e1a72fcca58-7c3f0b69d16sm193204b3a.53 - gsmtp\r\n,530,5.7.0 SMTP server error: MAIL FROM command failed Detail: Authentication Required. For more information, go to\r\n https://support.google.com/accounts/troubleshooter/2402620. d2e1a72fcca58-7c3f0b69d16sm193204b3a.53 - gsmtp\r\n SMTP code: 530 Additional SMTP info: 5.7.0 SMTP server error: MAIL FROM command failed Detail: Authentication Required. For more information, go to\r\n https://support.google.com/accounts/troubleshooter/2402620. d2e1a72fcca58-7c3f0b69d16sm193204b3a.53 - gsmtp\r\n SMTP code: 530 Additional SMTP info: 5.7.0', NULL, '2025-11-19 20:09:42', '2025-11-19 20:09:59'),
(35, 'sailelaprincess.rutherford2023@gmail.com', 'Payment Due Notification', 's', 'sailelaprincess.rutherford2023@gmail.com', 'Gym Membership System', 'SENT_SUCCESS', 'Email sent successfully on attempt 1', NULL, '2025-11-19 20:29:57', '2025-11-19 20:30:02'),
(36, 'sailelaprincess.rutherford2023@gmail.com', 'Payment Due Notification', 's', 'sailelaprincess.rutherford2023@gmail.com', 'Gym Membership System', 'SENT_SUCCESS', 'Email sent successfully on attempt 1', NULL, '2025-11-19 20:30:02', '2025-11-19 20:30:06'),
(37, 'sailelaprincess.rutherford2023@gmail.com', 'Payment Due Notification', 's', 'sailelaprincess.rutherford2023@gmail.com', 'Gym Membership System', 'SENT_SUCCESS', 'Email sent successfully on attempt 1', NULL, '2025-11-19 20:30:06', '2025-11-19 20:30:10'),
(38, 'sailelaprincess.rutherford2023@gmail.com', 'Membership Renewal Reminder', 'ccccc', 'sailelaprincessshairamae@gmail.com', 'Gym Membership System', 'SENT_SUCCESS', 'Email sent successfully on attempt 1', NULL, '2025-11-19 20:37:15', '2025-11-19 20:37:19'),
(39, 'sailelaprincess.rutherford2023@gmail.com', 'Payment Due Notification', 'hahays\r\n', 'sailelaprincessshairamae@gmail.com', 'Gym Membership System', 'SENT_SUCCESS', 'Email sent successfully on attempt 1', NULL, '2025-11-19 20:44:50', '2025-11-19 20:44:58'),
(40, 'sailelaprincessshairamae@gmail.com', 'Welcome to Our Gym', 'sample email', 'sailelaprincess.rutherford2023@gmail.com', 'Gym Membership System', 'SENT_SUCCESS', 'Email sent successfully on attempt 1', NULL, '2025-11-19 20:54:25', '2025-11-19 20:54:32'),
(41, 'sailelaprincessshairamae@gmail.com', 'Welcome to Our Gym', 'sample email', 'sailelaprincess.rutherford2023@gmail.com', 'Gym Membership System', 'SENT_SUCCESS', 'Email sent successfully on attempt 1', NULL, '2025-11-19 20:54:32', '2025-11-19 20:54:41'),
(42, 'sailelaprincessshairamae@gmail.com', 'rndom', 'rndom', 'sailelaprincess.rutherford2023@gmail.com', 'Gym Membership System', 'SENT_SUCCESS', 'Email sent successfully on attempt 1', NULL, '2025-11-19 21:06:18', '2025-11-19 21:06:23'),
(43, 'ricafort.rutherford2023@gmail.com', 'HI', 'ujmmmmmmmmmmm', 'sailelaprincess.rutherford2023@gmail.com', 'Gym Membership System', 'SENT_SUCCESS', 'Email sent successfully on attempt 1', NULL, '2025-12-14 07:41:37', '2025-12-14 07:41:41'),
(44, 'ricafort.rutherford2023@gmail.com', 'hi baby', '---', 'sailelaprincess.rutherford2023@gmail.com', 'Gym Membership System', 'SENT_SUCCESS', 'Email sent successfully on attempt 1', NULL, '2025-12-14 07:47:11', '2025-12-14 07:47:16'),
(45, 'sailelaprincess.rutherford2023@gmail.com', 'Payment Due Notification', '.', 'sailelaprincess.rutherford2023@gmail.com', 'Gym Membership System', 'SENT_SUCCESS', 'Email sent successfully on attempt 1', NULL, '2025-12-14 08:49:34', '2025-12-14 08:49:38');

-- --------------------------------------------------------

--
-- Table structure for table `email_queue`
--

CREATE TABLE `email_queue` (
  `queue_id` int(11) NOT NULL,
  `to_email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` longtext NOT NULL,
  `status` enum('PENDING','PROCESSING','SENT','FAILED') DEFAULT 'PENDING',
  `attempts` int(11) DEFAULT 0,
  `last_error` longtext DEFAULT NULL,
  `last_attempt_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sent_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `membership`
--

CREATE TABLE `membership` (
  `membership_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `expiry_date` date NOT NULL,
  `membership_status` enum('Active','Expired','Freeze','Suspended','Cancelled') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `employee_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `membership`
--

INSERT INTO `membership` (`membership_id`, `member_id`, `plan_id`, `start_date`, `expiry_date`, `membership_status`, `created_at`, `updated_at`, `employee_id`) VALUES
(8, 1, 2, '2025-11-02', '2025-11-09', 'Active', '2025-12-14 08:01:56', '2025-12-14 08:01:56', 5),
(9, 6, 2, '2025-11-02', '2025-11-16', 'Active', '2025-12-14 08:01:56', '2025-12-14 08:01:56', 5),
(10, 7, 4, '2025-11-02', '2025-11-16', 'Active', '2025-12-14 08:01:56', '2025-12-14 08:01:56', 2),
(12, 10, 5, '2025-11-06', '2025-11-13', 'Active', '2025-12-14 08:01:56', '2025-12-14 08:01:56', 4),
(13, 11, 4, '2025-11-06', '2025-11-05', 'Active', '2025-12-14 08:01:56', '2025-12-14 08:01:56', 5),
(14, 10, 3, '2025-11-16', '2025-11-19', 'Active', '2025-12-14 08:01:56', '2025-12-14 08:01:56', 4),
(15, 10, 4, '2025-11-18', '2025-11-21', 'Active', '2025-12-14 08:01:56', '2025-12-14 08:01:56', 5);

-- --------------------------------------------------------

--
-- Table structure for table `membership_plans`
--

CREATE TABLE `membership_plans` (
  `plan_id` int(11) NOT NULL,
  `plan_name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `duration` int(11) NOT NULL,
  `price` decimal(11,0) NOT NULL,
  `plan_type` enum('Individual','Family','Student','Corporate') NOT NULL,
  `status` enum('active','not_active','','') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `membership_plans`
--

INSERT INTO `membership_plans` (`plan_id`, `plan_name`, `description`, `duration`, `price`, `plan_type`, `status`, `created_at`, `updated_at`) VALUES
(2, 'jskda', 'asd', 22, 50, 'Individual', 'active', '2025-12-14 08:13:12', '2025-12-14 08:13:12'),
(3, 'ZXC ZX', 'ASC', 3, 50, 'Family', 'active', '2025-12-14 08:13:12', '2025-12-14 08:13:12'),
(4, 'Plan 1', 'First Plan', 22, 2500, 'Family', 'active', '2025-12-14 08:13:12', '2025-12-14 08:13:12'),
(5, 'Jop Plan', 'random', 7, 2500, 'Individual', 'active', '2025-12-14 08:13:12', '2025-12-14 08:13:12');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `membership_id` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `amount` int(11) NOT NULL,
  `payment_mode` enum('Cash','Card','Bank Transfer','E-wallet') NOT NULL,
  `payment_status` enum('Completed','Pending','','') NOT NULL,
  `employee_id` int(11) NOT NULL,
  `transaction_ref` varchar(15) NOT NULL,
  `notes` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`payment_id`, `membership_id`, `payment_date`, `amount`, `payment_mode`, `payment_status`, `employee_id`, `transaction_ref`, `notes`, `created_at`, `updated_at`) VALUES
(6, 9, '2025-11-02', 200, 'Bank Transfer', 'Completed', 4, 'PAY000006', '', '2025-12-14 08:54:40', '2025-12-14 08:21:17'),
(7, 8, '2025-11-02', 300, 'E-wallet', 'Completed', 5, 'PAY000007', '', '2025-12-14 08:54:40', '2025-12-14 08:21:17'),
(8, 10, '2025-11-02', 500, 'Card', 'Completed', 2, 'PAY000008', '', '2025-12-14 08:54:40', '2025-12-14 08:21:17'),
(10, 12, '2025-11-06', 2500, 'Cash', 'Completed', 5, 'PAY000010', '', '2025-12-14 08:54:40', '2025-12-14 08:21:17'),
(11, 13, '2025-11-06', 2500, 'Cash', 'Completed', 2, 'PAY000011', '', '2025-12-14 08:54:40', '2025-12-14 08:21:17');

-- --------------------------------------------------------

--
-- Table structure for table `profile`
--

CREATE TABLE `profile` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `mname` varchar(1) DEFAULT NULL,
  `phone_num` varchar(20) NOT NULL,
  `address` varchar(255) NOT NULL,
  `sex` enum('Male','Female') NOT NULL,
  `dob` date NOT NULL,
  `join_date` date NOT NULL,
  `status` enum('Active','Inactive','','') NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `email` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profile`
--

INSERT INTO `profile` (`id`, `role_id`, `fname`, `lname`, `mname`, `phone_num`, `address`, `sex`, `dob`, `join_date`, `status`, `created_at`, `updated_at`, `email`) VALUES
(1, 1, 'Dixon', 'Trumata', '', '09888888888', 'Catalina', 'Male', '2006-01-13', '2025-11-01', 'Active', '2025-11-19 17:46:46', '2025-11-19 17:46:46', 'sailelaprincess.rutherford2023@gmail.com'),
(2, 2, 'Jeoff Nikko', 'Ricafort', '', '09777777777', 'Boalan', 'Male', '2005-06-12', '2025-11-01', 'Active', '2025-11-01 20:45:45', '2025-11-01 20:45:45', ''),
(4, 2, 'Haris', 'Sailela', 'B', '09561636694', 'Tugbungan,ZC', 'Male', '2006-05-01', '2025-11-01', 'Active', '2025-11-01 21:00:11', '2025-11-01 21:00:11', ''),
(5, 2, 'Dixon', 'Trumata', 'C', '09555555555', 'Catalina', 'Male', '2025-10-22', '2025-11-02', 'Active', '2025-11-02 05:11:33', '2025-11-02 05:11:33', ''),
(6, 1, 'Evelyn', 'Bayabos', '', '09445455454', 'Tugbungan', 'Female', '2025-10-26', '2025-11-02', 'Active', '2025-11-02 05:31:26', '2025-11-02 05:31:26', ''),
(7, 1, 'Rafi', 'Sailela', 'B', '09223232323', 'Tugbungan', 'Male', '2025-10-06', '2025-10-25', 'Active', '2025-11-02 05:57:02', '2025-11-02 05:57:02', ''),
(9, 1, 'Sample', 'Sample', 'A', '09777777777', 'Boalan', 'Male', '2025-09-28', '2025-11-04', 'Active', '2025-11-04 12:49:41', '2025-11-04 12:49:41', ''),
(10, 1, 'JOP', 'Ricafort', '', '09777777777', 'Boalan', 'Male', '2005-06-12', '2025-11-06', 'Active', '2025-11-06 04:06:27', '2025-11-06 04:06:27', ''),
(11, 1, 'rndom', 'rndom', '', '08888777777', 'rndom', 'Female', '2025-10-28', '2025-11-06', 'Active', '2025-11-06 04:43:48', '2025-11-06 04:43:48', ''),
(12, 3, 'Shai', 'Sailela', 'B', '09561636694', 'sailelaprincessshairamae@gmail.com', 'Female', '2006-05-01', '2025-11-19', 'Active', '2025-11-19 21:54:06', '2025-11-19 21:54:06', 'sailelaprincessshairamae@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `renewal_record`
--

CREATE TABLE `renewal_record` (
  `renewal_id` int(11) NOT NULL,
  `membership_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `renewal_date` date NOT NULL,
  `previous_start_date` date NOT NULL,
  `previous_expiry_date` date NOT NULL,
  `new_start_date` date NOT NULL,
  `new_expiry_date` date NOT NULL,
  `payment_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `renewal_record`
--

INSERT INTO `renewal_record` (`renewal_id`, `membership_id`, `plan_id`, `renewal_date`, `previous_start_date`, `previous_expiry_date`, `new_start_date`, `new_expiry_date`, `payment_id`, `employee_id`) VALUES
(3, 10, 4, '2025-11-02', '2025-10-31', '2025-11-01', '2025-11-02', '2025-11-23', 8, 2);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `description`) VALUES
(1, 'Member', 'Regular Gym-goer'),
(2, 'Staff', 'Works in the gym.'),
(3, 'Trainer', 'Conducts fitness sessions.'),
(7, 'Admin', 'supervise');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_logs`
--
ALTER TABLE `email_logs`
  ADD PRIMARY KEY (`email_log_id`),
  ADD KEY `idx_recipient` (`recipient`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_sender_email` (`sender_email`);

--
-- Indexes for table `email_queue`
--
ALTER TABLE `email_queue`
  ADD PRIMARY KEY (`queue_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `membership`
--
ALTER TABLE `membership`
  ADD PRIMARY KEY (`membership_id`),
  ADD KEY `membership_ibfk_2` (`plan_id`),
  ADD KEY `membership_ibfk_3` (`employee_id`),
  ADD KEY `membership_ibfk_4` (`member_id`);

--
-- Indexes for table `membership_plans`
--
ALTER TABLE `membership_plans`
  ADD PRIMARY KEY (`plan_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `payment_ibfk_1` (`membership_id`),
  ADD KEY `payment_ibfk_2` (`employee_id`);

--
-- Indexes for table `profile`
--
ALTER TABLE `profile`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profile_ibfk_1` (`role_id`);

--
-- Indexes for table `renewal_record`
--
ALTER TABLE `renewal_record`
  ADD PRIMARY KEY (`renewal_id`),
  ADD KEY `renewal_record_ibfk_1` (`membership_id`),
  ADD KEY `renewal_record_ibfk_3` (`plan_id`),
  ADD KEY `renewal_record_ibfk_4` (`payment_id`),
  ADD KEY `renewal_record_ibfk_5` (`employee_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `email_logs`
--
ALTER TABLE `email_logs`
  MODIFY `email_log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `email_queue`
--
ALTER TABLE `email_queue`
  MODIFY `queue_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `membership`
--
ALTER TABLE `membership`
  MODIFY `membership_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `membership_plans`
--
ALTER TABLE `membership_plans`
  MODIFY `plan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `profile`
--
ALTER TABLE `profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `renewal_record`
--
ALTER TABLE `renewal_record`
  MODIFY `renewal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `membership`
--
ALTER TABLE `membership`
  ADD CONSTRAINT `membership_ibfk_2` FOREIGN KEY (`plan_id`) REFERENCES `membership_plans` (`plan_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `membership_ibfk_3` FOREIGN KEY (`employee_id`) REFERENCES `profile` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `membership_ibfk_4` FOREIGN KEY (`member_id`) REFERENCES `profile` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`membership_id`) REFERENCES `membership` (`membership_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payment_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `profile` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `profile`
--
ALTER TABLE `profile`
  ADD CONSTRAINT `profile_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE;

--
-- Constraints for table `renewal_record`
--
ALTER TABLE `renewal_record`
  ADD CONSTRAINT `renewal_record_ibfk_1` FOREIGN KEY (`membership_id`) REFERENCES `membership` (`membership_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `renewal_record_ibfk_3` FOREIGN KEY (`plan_id`) REFERENCES `membership_plans` (`plan_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `renewal_record_ibfk_4` FOREIGN KEY (`payment_id`) REFERENCES `payment` (`payment_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `renewal_record_ibfk_5` FOREIGN KEY (`employee_id`) REFERENCES `profile` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

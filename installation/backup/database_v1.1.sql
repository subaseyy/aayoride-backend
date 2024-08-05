-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 20, 2023 at 08:02 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `drive_mnd`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `logable_id` char(36) NOT NULL,
  `logable_type` varchar(255) NOT NULL,
  `edited_by` varchar(255) NOT NULL,
  `before` longtext DEFAULT NULL,
  `after` longtext DEFAULT NULL,
  `user_type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_notifications`
--

CREATE TABLE `admin_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `model` varchar(255) NOT NULL,
  `model_id` char(36) NOT NULL,
  `message` varchar(255) NOT NULL,
  `is_seen` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `app_notifications`
--

CREATE TABLE `app_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` char(36) NOT NULL,
  `ride_request_id` char(36) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `areas`
--

CREATE TABLE `areas` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `radius` double NOT NULL,
  `zone_id` char(36) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `area_bonus_setup`
--

CREATE TABLE `area_bonus_setup` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `area_id` char(36) NOT NULL,
  `bonus_setup_id` char(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `area_coupon_setup`
--

CREATE TABLE `area_coupon_setup` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `area_id` char(36) NOT NULL,
  `coupon_setup_id` char(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `area_discount_setup`
--

CREATE TABLE `area_discount_setup` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `area_id` char(36) NOT NULL,
  `discount_setup_id` char(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `area_pick_hour`
--

CREATE TABLE `area_pick_hour` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `area_id` char(36) NOT NULL,
  `pick_hour_id` char(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `banner_setups`
--

CREATE TABLE `banner_setups` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `time_period` varchar(255) DEFAULT NULL,
  `display_position` varchar(255) DEFAULT NULL,
  `redirect_link` varchar(255) DEFAULT NULL,
  `banner_group` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `total_redirection` decimal(8,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bonus_setups`
--

CREATE TABLE `bonus_setups` (
  `id` char(36) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `user_id` char(36) DEFAULT NULL,
  `user_level_id` char(36) DEFAULT NULL,
  `min_trip_amount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `max_bonus` decimal(8,2) NOT NULL DEFAULT 0.00,
  `bonus` decimal(8,2) NOT NULL DEFAULT 0.00,
  `amount_type` varchar(15) NOT NULL DEFAULT 'percentage',
  `bonus_type` varchar(15) NOT NULL DEFAULT 'default',
  `limit` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `rules` varchar(255) DEFAULT NULL,
  `total_used` decimal(8,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bonus_setup_vehicle_category`
--

CREATE TABLE `bonus_setup_vehicle_category` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bonus_setup_id` char(36) NOT NULL,
  `vehicle_category_id` char(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `business_settings`
--

CREATE TABLE `business_settings` (
  `id` char(36) NOT NULL,
  `key_name` varchar(191) NOT NULL,
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`value`)),
  `settings_type` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `channel_conversations`
--

CREATE TABLE `channel_conversations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `channel_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `message` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `channel_lists`
--

CREATE TABLE `channel_lists` (
  `id` char(36) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `channel_users`
--

CREATE TABLE `channel_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `channel_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `conversation_files`
--

CREATE TABLE `conversation_files` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `conversation_id` char(36) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_type` varchar(255) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupon_setups`
--

CREATE TABLE `coupon_setups` (
  `id` char(36) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `user_id` char(36) DEFAULT NULL,
  `user_level_id` char(36) DEFAULT NULL,
  `min_trip_amount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `max_coupon_amount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `coupon` decimal(8,2) NOT NULL DEFAULT 0.00,
  `amount_type` varchar(15) NOT NULL DEFAULT 'percentage',
  `coupon_type` varchar(15) NOT NULL DEFAULT 'default',
  `coupon_code` varchar(255) DEFAULT NULL,
  `limit` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `rules` varchar(255) DEFAULT NULL,
  `total_used` decimal(8,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupon_setup_vehicle_category`
--

CREATE TABLE `coupon_setup_vehicle_category` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `coupon_setup_id` char(36) NOT NULL,
  `vehicle_category_id` char(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discount_setups`
--

CREATE TABLE `discount_setups` (
  `id` char(36) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `user_id` char(36) DEFAULT NULL,
  `user_level_id` char(36) DEFAULT NULL,
  `min_trip_amount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `max_discount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `discount_type` varchar(15) NOT NULL DEFAULT 'percentage',
  `limit` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `rules` varchar(255) DEFAULT NULL,
  `total_used` decimal(8,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discount_setup_vehicle_category`
--

CREATE TABLE `discount_setup_vehicle_category` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `discount_setup_id` char(36) NOT NULL,
  `vehicle_category_id` char(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `driver_details`
--

CREATE TABLE `driver_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` char(36) NOT NULL,
  `is_online` varchar(255) NOT NULL DEFAULT '0',
  `availability_status` varchar(255) NOT NULL DEFAULT 'unavailable',
  `online` time DEFAULT NULL,
  `offline` time DEFAULT NULL,
  `online_time` double(23,2) NOT NULL DEFAULT 0.00,
  `accepted` time DEFAULT NULL,
  `completed` time DEFAULT NULL,
  `start_driving` time DEFAULT NULL,
  `on_driving_time` double(23,2) NOT NULL DEFAULT 0.00,
  `idle_time` double(23,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `driver_time_logs`
--

CREATE TABLE `driver_time_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `driver_id` char(36) NOT NULL,
  `date` date NOT NULL,
  `online` time DEFAULT NULL,
  `offline` time DEFAULT NULL,
  `online_time` double(23,2) NOT NULL DEFAULT 0.00,
  `accepted` time DEFAULT NULL,
  `completed` time DEFAULT NULL,
  `start_driving` time DEFAULT NULL,
  `on_driving_time` double(23,2) NOT NULL DEFAULT 0.00,
  `idle_time` double(23,2) NOT NULL DEFAULT 0.00,
  `on_time_completed` varchar(255) DEFAULT '0',
  `late_completed` varchar(255) DEFAULT '0',
  `late_pickup` varchar(255) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fare_biddings`
--

CREATE TABLE `fare_biddings` (
  `id` char(36) NOT NULL,
  `trip_request_id` char(36) NOT NULL,
  `driver_id` char(36) NOT NULL,
  `customer_id` char(36) NOT NULL,
  `bid_fare` decimal(8,2) NOT NULL,
  `is_ignored` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fare_bidding_logs`
--

CREATE TABLE `fare_bidding_logs` (
  `id` char(36) NOT NULL,
  `trip_request_id` char(36) DEFAULT NULL,
  `driver_id` char(36) DEFAULT NULL,
  `customer_id` char(36) DEFAULT NULL,
  `bid_fare` decimal(8,2) DEFAULT NULL,
  `is_ignored` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` varchar(255) DEFAULT NULL,
  `updated_at` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `firebase_push_notifications`
--

CREATE TABLE `firebase_push_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `value` varchar(191) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `firebase_push_notifications`
--

INSERT INTO `firebase_push_notifications` (`id`, `name`, `value`, `status`, `created_at`, `updated_at`) VALUES
(1, 'new_ride_request_notification', 'you_have_a_new_request', 0, '2023-03-18 20:34:23', '2023-05-07 23:49:50'),
(2, 'new_parcel_request_notification', 'you_have_a_new_parcel_request', 0, '2023-03-18 20:34:23', '2023-05-07 23:49:51'),
(3, 'ride_is_started', 'another_driver_is_assigned', 0, '2023-03-18 20:34:23', '2023-05-07 23:49:51'),
(4, 'parcel_delivery_is_started', 'another_driver_is_assigned', 0, '2023-03-18 20:34:23', '2023-05-07 23:49:51'),
(5, 'customer_cancelled_ride_request', 'user_just_cancelled_this_ride_request', 0, '2023-03-18 20:34:23', '2023-05-07 23:49:51'),
(6, 'customer_cancelled_parcel_request', 'user_just_cancelled_this_parcel_request', 0, '2023-03-18 20:34:23', '2023-05-07 23:49:51'),
(7, 'ride_accepted', 'customer_confirmed_your_request', 0, NULL, '2023-05-07 23:49:51'),
(8, 'parcel_request_accepted', 'customer_confirmed_your_request', 0, NULL, NULL),
(9, 'trip_completed_message', 'trip_completed_message_values', 0, NULL, NULL),
(10, 'trip_cancelled_message', 'trip_cancelled_message_value', 0, NULL, NULL),
(11, 'new_message', 'You have a new message from', 1, NULL, '2023-07-16 12:05:57'),
(12, 'payment_successful', 'is paid on this trip', 1, NULL, '2023-07-09 04:53:57'),
(13, 'registration_approved', 'Admin approved your registration. you can login now', 1, NULL, '2023-07-09 04:53:57'),
(14, 'trip_pause', 'Trip request is paused', 1, NULL, '2023-07-09 04:53:57'),
(15, 'trip_resume', 'Trip request is resumed', 1, NULL, '2023-07-09 04:53:57'),
(16, 'trip_started', 'Your trip is started', 1, NULL, '2023-07-09 04:53:57'),
(17, 'received_new_bid', 'Received  a new bid request', 1, NULL, '2023-09-12 13:30:35'),
(18, 'driver_is_on_the_way', 'Driver accepted your trip request', 1, NULL, '2023-07-09 04:53:57'),
(19, 'ride_completed', 'Your trip is completed', 1, NULL, '2023-07-09 04:53:57'),
(20, 'ride_cancelled', 'Your trip is cancelled', 1, NULL, '2023-07-09 04:53:57'),
(21, 'bid_accepted', 'Customer confirmed your bid', 1, NULL, '2023-07-09 04:53:57'),
(22, 'coupon_applied', 'Customer got discount of', 1, NULL, '2023-07-09 04:53:57'),
(23, 'coupon_removed', 'Customer removed previously applied coupon', 1, NULL, '2023-07-09 04:53:57'),
(24, 'terms_and_conditions_updated', 'Admin just updated our terms and conditions updated', 1, NULL, '2023-09-12 13:30:35'),
(25, 'privacy_policy_updated', 'Admin  just updated our privacy policy', 1, NULL, '2023-09-12 13:30:35'),
(26, 'new_ride_request', 'You have a new ride request', 1, NULL, '2023-07-09 04:53:57'),
(28, 'new_parcel', 'You have a new parcel request', 1, NULL, '2023-07-09 04:53:57'),
(29, 'driver_assigned', 'Another driver is assigned', 1, NULL, '2023-07-09 04:53:57'),
(30, 'customer_cancelled_trip', 'Customer just declined a request', 1, NULL, '2023-07-09 04:53:57'),
(31, 'this_ride_is_started', 'Another driver is assigned', 1, NULL, '2023-07-09 04:53:57'),
(32, 'vehicle_approved', 'Your vehicle is approved by admin', 1, NULL, '2023-07-09 04:53:57'),
(33, 'trip_request_cancelled', 'A trip request is cancelled', 1, NULL, '2023-07-09 04:53:57'),
(34, 'parcel_completed', 'Parcel delivered successfully ', 0, '2023-11-05 17:42:41', '2023-11-05 17:42:41');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `level_accesses`
--

CREATE TABLE `level_accesses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `level_id` char(36) NOT NULL,
  `user_type` varchar(50) NOT NULL,
  `bid` tinyint(1) NOT NULL DEFAULT 0,
  `see_destination` tinyint(1) NOT NULL DEFAULT 0,
  `see_subtotal` tinyint(1) NOT NULL DEFAULT 0,
  `see_level` tinyint(1) NOT NULL DEFAULT 0,
  `create_hire_request` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loyalty_points_histories`
--

CREATE TABLE `loyalty_points_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` char(36) DEFAULT NULL,
  `model` varchar(255) NOT NULL,
  `model_id` char(36) DEFAULT NULL,
  `points` double NOT NULL,
  `type` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2016_06_01_000001_create_oauth_auth_codes_table', 1),
(2, '2016_06_01_000002_create_oauth_access_tokens_table', 1),
(3, '2016_06_01_000003_create_oauth_refresh_tokens_table', 1),
(4, '2016_06_01_000004_create_oauth_clients_table', 1),
(5, '2016_06_01_000005_create_oauth_personal_access_clients_table', 1),
(6, '2019_08_19_000000_create_failed_jobs_table', 1),
(7, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(8, '2022_11_21_045555_create_payments_table', 1),
(9, '2022_11_21_085924_create_payment_settings_table', 1),
(10, '2023_01_10_114636_create_users_table', 1),
(11, '2023_01_10_115750_create_vehicles_table', 1),
(12, '2023_01_11_073558_create_vehicle_brands_table', 1),
(13, '2023_01_11_113737_create_vehicle_models_table', 1),
(14, '2023_01_12_062420_create_vehicle_categories_table', 1),
(15, '2023_01_16_043100_create_zones_table', 1),
(16, '2023_01_16_052732_create_vehicle_category_zone_table', 1),
(17, '2023_01_16_121122_create_user_levels_table', 1),
(18, '2023_01_17_034948_create_areas_table', 1),
(19, '2023_01_22_121648_create_business_settings_table', 1),
(20, '2023_01_24_070220_create_pick_hours_table', 1),
(21, '2023_01_24_102512_create_area_pick_hour_table', 1),
(22, '2023_01_26_091327_create_banner_setups_table', 1),
(23, '2023_01_26_110443_create_notification_settings_table', 1),
(24, '2023_01_26_111922_create_firebase_push_notifications_table', 1),
(25, '2023_01_28_041320_create_discount_setups_table', 1),
(26, '2023_01_28_103231_create_level_accesses_table', 1),
(27, '2023_01_29_115233_create_social_links_table', 1),
(28, '2023_01_30_063201_create_area_discount_setup_table', 1),
(29, '2023_01_30_114525_create_discount_setup_vehicle_category_table', 1),
(30, '2023_02_01_035306_create_milestone_setups_table', 1),
(31, '2023_02_01_042116_create_bonus_setups_table', 1),
(32, '2023_02_01_060559_create_area_bonus_setup_table', 1),
(33, '2023_02_01_060650_create_bonus_setup_vehicle_category_table', 1),
(34, '2023_02_05_035750_create_coupon_setups_table', 1),
(35, '2023_02_05_051702_create_area_coupon_setup_table', 1),
(36, '2023_02_05_052020_create_coupon_setup_vehicle_category_table', 1),
(37, '2023_02_08_065339_create_roles_table', 1),
(38, '2023_02_09_065343_create_role_user_table', 1),
(39, '2023_02_12_054054_create_trip_fares_table', 1),
(40, '2023_02_12_070009_create_parcel_categories_table', 1),
(41, '2023_02_12_092239_create_parcel_weights_table', 1),
(42, '2023_02_13_091841_create_parcel_fares_table', 1),
(43, '2023_02_15_101259_create_module_accesses_table', 1),
(44, '2023_02_16_093144_create_user_address_table', 1),
(45, '2023_02_19_043220_create_trip_requests_table', 1),
(46, '2023_02_19_070337_create_trip_status_table', 1),
(47, '2023_02_19_071606_create_trip_routes_table', 1),
(48, '2023_02_19_102134_create_fare_biddings_table', 1),
(49, '2023_02_20_114458_create_parcel_fares_parcel_weights_table', 1),
(50, '2023_02_22_063650_create_parcels_table', 1),
(51, '2023_02_22_085634_create_channel_conversations_table', 1),
(52, '2023_02_22_085659_create_channel_lists_table', 1),
(53, '2023_02_22_085727_create_channel_users_table', 1),
(54, '2023_02_22_085752_create_conversation_files_table', 1),
(55, '2023_02_25_035752_create_reviews_table', 1),
(56, '2023_02_27_042506_create_user_last_locations_table', 1),
(57, '2023_03_02_032942_create_activity_logs_table', 1),
(58, '2023_03_06_052511_create_recent_addresses_table', 1),
(59, '2023_03_14_121257_create_fare_bidding_logs_table', 1),
(60, '2023_03_16_074055_add_payer_information_to_payment_requests_table', 1),
(61, '2023_03_18_042902_add_external_redirect_link_to_payment_requests_table', 1),
(62, '2023_03_19_113319_change_column_in_payment_settings_table', 1),
(63, '2023_03_21_072752_add_receiver_information_to_payment_requests_table', 1),
(64, '2023_03_22_040654_create_jobs_table', 1),
(65, '2023_03_22_053625_create_driver_details_table', 1),
(66, '2023_03_22_072803_create_driver_time_logs_table', 1),
(67, '2023_03_23_055542_create_user_level_histories_table', 1),
(68, '2023_03_28_041451_add_column_to_payment_requests', 1),
(69, '2023_03_28_061810_add_payment_platform_column_to_payment_requests', 1),
(70, '2023_03_28_064934_create_rejected_driver_requests_table', 1),
(71, '2023_04_03_075904_create_temp_trip_notifications_table', 1),
(72, '2023_04_10_064449_rename_payment_settings_to_settings_table', 1),
(73, '2023_04_12_071813_aad_additional_data_column_to_settings_table', 1),
(74, '2023_04_29_061951_create_trip_request_fees_table', 1),
(75, '2023_04_29_062028_create_trip_request_coordinates_table', 1),
(76, '2023_04_30_060033_create_trip_request_times_table', 1),
(77, '2023_04_30_094812_create_transactions_table', 1),
(78, '2023_04_30_110147_create_user_accounts_table', 1),
(79, '2023_05_02_112219_create_parcel_user_infomations_table', 1),
(80, '2023_05_02_112241_create_parcel_information_table', 1),
(81, '2023_05_13_102728_create_admin_notifications_table', 1),
(82, '2023_05_13_123323_create_app_notifications_table', 1),
(83, '2023_05_17_091349_create_loyalty_points_histories_table', 1),
(84, '2023_05_18_045035_create_withdraw_methods_table', 1),
(85, '2023_05_18_102011_create_withdraw_requests_table', 1),
(86, '2023_05_25_084737_create_otp_verifications_table', 1),
(87, '2023_05_29_100521_create_time_tracks_table', 1),
(88, '2023_05_29_100531_create_time_logs_table', 1),
(89, '2023_06_08_065011_add_failed_attempt_col_to_users_table', 1),
(90, '2023_06_08_101119_add_more_cols_to_otp_verifications_table', 1),
(91, '2023_07_05_055628_add_is_paused_to_trip_requests_table', 1),
(92, '2023_07_09_060537_add_screenshot_column_to_trip_requests_table', 1),
(93, '2023_07_12_062801_add_is_ignred_column_to_fare_biddings_table', 1),
(94, '2023_07_12_100856_add_is_ignred_column_to_fare_bidding_logs_table', 1),
(95, '2023_11_12_105624_add_base_fare_column_to_parcel_fares_parcel_weights_table', 2),
(96, '2023_11_13_040038_create_zone_wise_default_trip_fares_table', 2),
(97, '2023_11_13_041656_add_zone_wise_default_trip_fare_id_column_to_trip_fares_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `milestone_setups`
--

CREATE TABLE `milestone_setups` (
  `id` char(36) NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` text NOT NULL,
  `customer_id` char(36) DEFAULT NULL,
  `customer_level_id` char(36) DEFAULT NULL,
  `driver_id` char(36) DEFAULT NULL,
  `driver_level_id` char(36) DEFAULT NULL,
  `thumbnail` varchar(50) NOT NULL,
  `banner` varchar(50) NOT NULL,
  `reward_type` varchar(15) NOT NULL,
  `reward_amount` decimal(30,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `challenge_type` varchar(15) NOT NULL,
  `target_count` decimal(5,2) NOT NULL,
  `referral_code` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `module_accesses`
--

CREATE TABLE `module_accesses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` char(36) NOT NULL,
  `role_id` char(36) NOT NULL,
  `module_name` varchar(255) NOT NULL,
  `view` tinyint(1) NOT NULL DEFAULT 0,
  `add` tinyint(1) NOT NULL DEFAULT 0,
  `update` tinyint(1) NOT NULL DEFAULT 0,
  `delete` tinyint(1) NOT NULL DEFAULT 0,
  `log` tinyint(1) NOT NULL DEFAULT 0,
  `export` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_settings`
--

CREATE TABLE `notification_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `push` tinyint(1) NOT NULL DEFAULT 0,
  `email` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notification_settings`
--

INSERT INTO `notification_settings` (`id`, `name`, `push`, `email`, `created_at`, `updated_at`) VALUES
(1, 'trip', 1, 0, '2023-11-04 10:00:41', '2023-11-04 10:00:49'),
(3, 'rating_and_review', 1, 0, '2023-11-04 10:01:08', '2023-11-04 14:11:03'),
(4, 'privacy_policy', 1, 0, '2023-11-09 17:09:10', '2023-11-09 17:09:16'),
(5, 'terms_and_conditions', 1, 0, '2023-11-09 17:09:20', '2023-11-12 11:16:39');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_access_tokens`
--

CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) NOT NULL,
  `user_id` char(36) DEFAULT NULL,
  `client_id` char(36) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `scopes` text DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_auth_codes`
--

CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` char(36) NOT NULL,
  `scopes` text DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_clients`
--

CREATE TABLE `oauth_clients` (
  `id` char(36) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `secret` varchar(100) DEFAULT NULL,
  `provider` varchar(255) DEFAULT NULL,
  `redirect` text NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_clients`
--

INSERT INTO `oauth_clients` (`id`, `user_id`, `name`, `secret`, `provider`, `redirect`, `personal_access_client`, `password_client`, `revoked`, `created_at`, `updated_at`) VALUES
('9a878b41-fc9e-4789-a835-0b3ebe060778', NULL, 'Laravel Personal Access Client', 'CId0ouRzX08kOyn8IWdbjegiUofmzJKLvOTGXzqU', NULL, 'http://localhost', 1, 0, 0, '2023-11-04 09:44:36', '2023-11-04 09:44:36'),
('9a878b42-03fd-4e56-923f-3f098de9188a', NULL, 'Laravel Password Grant Client', 'c8BTROa9IggqR1cG60e4ckfiBiXqQyJ3WU9AXxEo', 'users', 'http://localhost', 0, 1, 0, '2023-11-04 09:44:36', '2023-11-04 09:44:36');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_personal_access_clients`
--

CREATE TABLE `oauth_personal_access_clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` char(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_personal_access_clients`
--

INSERT INTO `oauth_personal_access_clients` (`id`, `client_id`, `created_at`, `updated_at`) VALUES
(1, '9a878b41-fc9e-4789-a835-0b3ebe060778', '2023-11-04 09:44:36', '2023-11-04 09:44:36');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_refresh_tokens`
--

CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) NOT NULL,
  `access_token_id` varchar(100) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `otp_verifications`
--

CREATE TABLE `otp_verifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `phone_or_email` varchar(255) NOT NULL,
  `otp` varchar(255) NOT NULL,
  `is_temp_blocked` tinyint(1) NOT NULL DEFAULT 0,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `failed_attempt` int(11) NOT NULL DEFAULT 0,
  `blocked_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parcels`
--

CREATE TABLE `parcels` (
  `id` char(36) NOT NULL,
  `trip_request_id` char(36) DEFAULT NULL,
  `sender_person_name` varchar(255) DEFAULT NULL,
  `sender_person_phone` varchar(255) DEFAULT NULL,
  `sender_address` varchar(255) DEFAULT NULL,
  `receiver_person_name` varchar(255) DEFAULT NULL,
  `receiver_person_phone` varchar(255) DEFAULT NULL,
  `receiver_address` varchar(255) DEFAULT NULL,
  `parcel_category_id` char(36) DEFAULT NULL,
  `parcel_weight_id` char(36) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parcel_categories`
--

CREATE TABLE `parcel_categories` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parcel_fares`
--

CREATE TABLE `parcel_fares` (
  `id` char(36) NOT NULL,
  `zone_id` char(36) DEFAULT NULL,
  `base_fare` decimal(8,2) NOT NULL,
  `base_fare_per_km` decimal(8,2) NOT NULL,
  `cancellation_fee_percent` decimal(8,2) NOT NULL,
  `min_cancellation_fee` decimal(8,2) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parcel_fares_parcel_weights`
--

CREATE TABLE `parcel_fares_parcel_weights` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parcel_fare_id` char(36) NOT NULL,
  `parcel_weight_id` char(36) NOT NULL,
  `parcel_category_id` char(36) NOT NULL,
  `base_fare` double NOT NULL DEFAULT 0,
  `fare_per_km` decimal(8,2) NOT NULL,
  `zone_id` char(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parcel_information`
--

CREATE TABLE `parcel_information` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parcel_category_id` char(36) NOT NULL,
  `trip_request_id` char(36) NOT NULL,
  `payer` varchar(255) DEFAULT NULL,
  `weight` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parcel_user_infomations`
--

CREATE TABLE `parcel_user_infomations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `trip_request_id` char(36) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `user_type` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parcel_weights`
--

CREATE TABLE `parcel_weights` (
  `id` char(36) NOT NULL,
  `min_weight` decimal(10,2) NOT NULL DEFAULT 0.00,
  `max_weight` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_requests`
--

CREATE TABLE `payment_requests` (
  `id` char(36) NOT NULL,
  `payer_id` varchar(64) DEFAULT NULL,
  `receiver_id` varchar(64) DEFAULT NULL,
  `payment_amount` decimal(24,2) NOT NULL DEFAULT 0.00,
  `gateway_callback_url` varchar(191) DEFAULT NULL,
  `hook` varchar(100) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `currency_code` varchar(20) NOT NULL DEFAULT 'USD',
  `payment_method` varchar(50) DEFAULT NULL,
  `additional_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`additional_data`)),
  `is_paid` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `payer_information` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payer_information`)),
  `external_redirect_link` varchar(255) DEFAULT NULL,
  `receiver_information` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`receiver_information`)),
  `attribute_id` varchar(64) DEFAULT NULL,
  `attribute` varchar(255) DEFAULT NULL,
  `payment_platform` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pick_hours`
--

CREATE TABLE `pick_hours` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `duration_type` varchar(255) DEFAULT NULL,
  `extra_charge` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `week_days` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`week_days`)),
  `zone_id` char(36) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recent_addresses`
--

CREATE TABLE `recent_addresses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` char(36) DEFAULT NULL,
  `zone_id` char(36) DEFAULT NULL,
  `pickup_coordinates` point DEFAULT NULL,
  `pickup_address` varchar(255) DEFAULT NULL,
  `destination_coordinates` point DEFAULT NULL,
  `destination_address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rejected_driver_requests`
--

CREATE TABLE `rejected_driver_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `trip_request_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `trip_request_id` char(36) DEFAULT NULL,
  `given_by` char(36) DEFAULT NULL,
  `received_by` char(36) DEFAULT NULL,
  `trip_type` varchar(255) DEFAULT NULL,
  `rating` int(11) NOT NULL DEFAULT 1,
  `feedback` text DEFAULT NULL,
  `images` varchar(255) DEFAULT NULL,
  `is_saved` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `modules` text NOT NULL,
  `is_active` varchar(255) NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_user`
--

CREATE TABLE `role_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` char(36) NOT NULL,
  `key_name` varchar(191) DEFAULT NULL,
  `live_values` longtext DEFAULT NULL,
  `test_values` longtext DEFAULT NULL,
  `settings_type` varchar(255) DEFAULT NULL,
  `mode` varchar(20) NOT NULL DEFAULT 'live',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `additional_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key_name`, `live_values`, `test_values`, `settings_type`, `mode`, `is_active`, `created_at`, `updated_at`, `additional_data`) VALUES
('070c6bbd-d777-11ed-96f4-0c7a158e4469', 'twilio', '{\"gateway\":\"twilio\",\"mode\":\"live\",\"status\":\"0\",\"sid\":null,\"messaging_service_sid\":null,\"token\":null,\"from\":null,\"otp_template\":null}', '{\"gateway\":\"twilio\",\"mode\":\"live\",\"status\":\"0\",\"sid\":null,\"messaging_service_sid\":null,\"token\":null,\"from\":null,\"otp_template\":null}', 'sms_config', 'live', 0, NULL, '2023-11-20 00:22:10', NULL),
('070c766c-d777-11ed-96f4-0c7a158e4469', '2factor', '{\"gateway\":\"2factor\",\"mode\":\"live\",\"status\":\"0\",\"api_key\":null}', '{\"gateway\":\"2factor\",\"mode\":\"live\",\"status\":\"0\",\"api_key\":null}', 'sms_config', 'live', 0, NULL, '2023-11-20 00:21:59', NULL),
('0d8a9308-d6a5-11ed-962c-0c7a158e4469', 'mercadopago', '{\"gateway\":\"mercadopago\",\"mode\":\"test\",\"status\":\"0\",\"access_token\":null,\"public_key\":null}', '{\"gateway\":\"mercadopago\",\"mode\":\"test\",\"status\":\"0\",\"access_token\":null,\"public_key\":null}', 'payment_config', 'test', 0, NULL, '2023-11-20 00:14:31', '{\"gateway_title\":null,\"gateway_image\":\"2023-11-20-655ae66916a34.png\"}'),
('0d8a9e49-d6a5-11ed-962c-0c7a158e4469', 'liqpay', '{\"gateway\":\"liqpay\",\"mode\":\"test\",\"status\":\"0\",\"private_key\":null,\"public_key\":null}', '{\"gateway\":\"liqpay\",\"mode\":\"test\",\"status\":\"0\",\"private_key\":null,\"public_key\":null}', 'payment_config', 'test', 0, NULL, '2023-11-20 00:14:42', '{\"gateway_title\":null,\"gateway_image\":\"2023-11-20-655ae64c304b9.png\"}'),
('101befdf-d44b-11ed-8564-0c7a158e4469', 'paypal', '{\"gateway\":\"paypal\",\"mode\":\"test\",\"status\":\"0\",\"client_id\":null,\"client_secret\":null}', '{\"gateway\":\"paypal\",\"mode\":\"test\",\"status\":\"0\",\"client_id\":null,\"client_secret\":null}', 'payment_config', 'test', 0, NULL, '2023-11-20 00:15:14', '{\"gateway_title\":null,\"gateway_image\":\"2023-11-20-655ae611d7c91.png\"}'),
('1821029f-d776-11ed-96f4-0c7a158e4469', 'msg91', '{\"gateway\":\"msg91\",\"mode\":\"live\",\"status\":\"0\",\"template_id\":null,\"auth_key\":null}', '{\"gateway\":\"msg91\",\"mode\":\"live\",\"status\":\"0\",\"template_id\":null,\"auth_key\":null}', 'sms_config', 'live', 0, NULL, '2023-11-20 00:22:17', NULL),
('18210f2b-d776-11ed-96f4-0c7a158e4469', 'nexmo', '{\"gateway\":\"nexmo\",\"mode\":\"live\",\"status\":\"0\",\"api_key\":\"\",\"api_secret\":\"\",\"token\":\"\",\"from\":\"\",\"otp_template\":\"\"}', '{\"gateway\":\"nexmo\",\"mode\":\"live\",\"status\":\"0\",\"api_key\":\"\",\"api_secret\":\"\",\"token\":\"\",\"from\":\"\",\"otp_template\":\"\"}', 'sms_config', 'live', 0, NULL, '2023-04-10 02:14:44', NULL),
('2767d142-d6a1-11ed-962c-0c7a158e4469', 'paytm', '{\"gateway\":\"paytm\",\"mode\":\"test\",\"status\":\"0\",\"merchant_key\":null,\"merchant_id\":null,\"merchant_website_link\":null}', '{\"gateway\":\"paytm\",\"mode\":\"test\",\"status\":\"0\",\"merchant_key\":null,\"merchant_id\":null,\"merchant_website_link\":null}', 'payment_config', 'test', 0, NULL, '2023-11-20 00:15:26', '{\"gateway_title\":null,\"gateway_image\":\"2023-11-20-655ae718cd837.png\"}'),
('4593b25c-d6a1-11ed-962c-0c7a158e4469', 'paytabs', '{\"gateway\":\"paytabs\",\"mode\":\"test\",\"status\":\"0\",\"profile_id\":null,\"server_key\":null,\"base_url\":null}', '{\"gateway\":\"paytabs\",\"mode\":\"test\",\"status\":\"0\",\"profile_id\":null,\"server_key\":null,\"base_url\":null}', 'payment_config', 'test', 0, NULL, '2023-11-20 00:15:41', '{\"gateway_title\":null,\"gateway_image\":\"2023-11-20-655ae7325e9b7.png\"}'),
('4e9b8dfb-e7d1-11ed-a559-0c7a158e4469', 'bkash', '{\"gateway\":\"bkash\",\"mode\":\"test\",\"status\":\"0\",\"app_key\":null,\"app_secret\":null,\"username\":null,\"password\":null}', '{\"gateway\":\"bkash\",\"mode\":\"test\",\"status\":\"0\",\"app_key\":null,\"app_secret\":null,\"username\":null,\"password\":null}', 'payment_config', 'test', 0, NULL, '2023-11-20 00:15:56', '{\"gateway_title\":null,\"gateway_image\":\"2023-11-20-655ae74591a98.png\"}'),
('998ccc62-d6a0-11ed-962c-0c7a158e4469', 'stripe', '{\"gateway\":\"stripe\",\"mode\":\"test\",\"status\":\"0\",\"api_key\":null,\"published_key\":null}', '{\"gateway\":\"stripe\",\"mode\":\"test\",\"status\":\"0\",\"api_key\":null,\"published_key\":null}', 'payment_config', 'test', 0, NULL, '2023-11-20 00:16:11', '{\"gateway_title\":null,\"gateway_image\":\"2023-11-20-655ae761a1905.png\"}'),
('ad5af1c1-d6a2-11ed-962c-0c7a158e4469', 'razor_pay', '{\"gateway\":\"razor_pay\",\"mode\":\"test\",\"status\":\"0\",\"api_key\":null,\"api_secret\":null}', '{\"gateway\":\"razor_pay\",\"mode\":\"test\",\"status\":\"0\",\"api_key\":null,\"api_secret\":null}', 'payment_config', 'test', 0, NULL, '2023-11-20 00:16:26', '{\"gateway_title\":null,\"gateway_image\":\"2023-11-20-655ae7733cc68.png\"}'),
('ad5b02a0-d6a2-11ed-962c-0c7a158e4469', 'senang_pay', '{\"gateway\":\"senang_pay\",\"mode\":\"test\",\"status\":\"0\",\"callback_url\":null,\"secret_key\":null,\"merchant_id\":null}', '{\"gateway\":\"senang_pay\",\"mode\":\"test\",\"status\":\"0\",\"callback_url\":null,\"secret_key\":null,\"merchant_id\":null}', 'payment_config', 'test', 0, NULL, '2023-11-20 00:17:04', '{\"gateway_title\":null,\"gateway_image\":\"2023-11-20-655ae78baeb8d.png\"}'),
('b8992bd4-d6a0-11ed-962c-0c7a158e4469', 'paymob_accept', '{\"gateway\":\"paymob_accept\",\"mode\":\"test\",\"status\":\"0\",\"callback_url\":null,\"api_key\":null,\"iframe_id\":null,\"integration_id\":null,\"hmac\":null}', '{\"gateway\":\"paymob_accept\",\"mode\":\"test\",\"status\":\"0\",\"callback_url\":null,\"api_key\":null,\"iframe_id\":null,\"integration_id\":null,\"hmac\":null}', 'payment_config', 'test', 0, NULL, '2023-11-20 00:16:49', '{\"gateway_title\":null,\"gateway_image\":\"2023-11-20-655ae7c0c7bd2.png\"}'),
('cb0081ce-d775-11ed-96f4-0c7a158e4469', 'releans', '{\"gateway\":\"releans\",\"mode\":\"live\",\"status\":0,\"api_key\":\"\",\"from\":\"\",\"otp_template\":\"\"}', '{\"gateway\":\"releans\",\"mode\":\"live\",\"status\":0,\"api_key\":\"\",\"from\":\"\",\"otp_template\":\"\"}', 'sms_config', 'live', 0, NULL, '2023-04-10 02:14:44', NULL),
('d4f3f5f1-d6a0-11ed-962c-0c7a158e4469', 'flutterwave', '{\"gateway\":\"flutterwave\",\"mode\":\"test\",\"status\":\"0\",\"secret_key\":null,\"public_key\":null,\"hash\":null}', '{\"gateway\":\"flutterwave\",\"mode\":\"test\",\"status\":\"0\",\"secret_key\":null,\"public_key\":null,\"hash\":null}', 'payment_config', 'test', 0, NULL, '2023-11-20 00:17:19', '{\"gateway_title\":null,\"gateway_image\":\"2023-11-20-655ae81c421b7.png\"}'),
('d822f1a5-c864-11ed-ac7a-0c7a158e4469', 'paystack', '{\"gateway\":\"paystack\",\"mode\":\"test\",\"status\":\"0\",\"public_key\":null,\"secret_key\":null,\"merchant_email\":null}', '{\"gateway\":\"paystack\",\"mode\":\"test\",\"status\":\"0\",\"public_key\":null,\"secret_key\":null,\"merchant_email\":null}', 'payment_config', 'test', 0, NULL, '2023-11-20 00:14:18', '{\"gateway_title\":null,\"gateway_image\":\"2023-11-20-655ae7d9bec0f.png\"}'),
('ea346efe-cdda-11ed-affe-0c7a158e4469', 'ssl_commerz', '{\"gateway\":\"ssl_commerz\",\"mode\":\"test\",\"status\":\"0\",\"store_id\":null,\"store_password\":null}', '{\"gateway\":\"ssl_commerz\",\"mode\":\"test\",\"status\":\"0\",\"store_id\":null,\"store_password\":null}', 'payment_config', 'test', 0, NULL, '2023-11-20 00:13:58', '{\"gateway_title\":null,\"gateway_image\":\"2023-11-20-655ae7e7231f5.png\"}');

-- --------------------------------------------------------

--
-- Table structure for table `social_links`
--

CREATE TABLE `social_links` (
  `id` char(36) NOT NULL,
  `name` varchar(191) NOT NULL,
  `link` varchar(191) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `temp_trip_notifications`
--

CREATE TABLE `temp_trip_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `trip_request_id` char(36) DEFAULT NULL,
  `user_id` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `time_logs`
--

CREATE TABLE `time_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `time_track_id` bigint(20) UNSIGNED NOT NULL,
  `online_at` time NOT NULL,
  `offline_at` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `time_tracks`
--

CREATE TABLE `time_tracks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` char(36) NOT NULL,
  `date` date NOT NULL,
  `total_online` int(11) NOT NULL DEFAULT 0,
  `total_offline` int(11) NOT NULL DEFAULT 0,
  `total_idle` int(11) NOT NULL DEFAULT 0,
  `total_driving` int(11) NOT NULL DEFAULT 0,
  `last_ride_started_at` time DEFAULT NULL,
  `last_ride_completed_at` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` char(36) NOT NULL,
  `attribute_id` char(36) DEFAULT NULL,
  `attribute` varchar(255) DEFAULT NULL,
  `debit` decimal(24,2) NOT NULL DEFAULT 0.00,
  `credit` decimal(24,2) NOT NULL DEFAULT 0.00,
  `balance` decimal(24,2) NOT NULL DEFAULT 0.00,
  `user_id` char(36) DEFAULT NULL,
  `account` varchar(255) DEFAULT NULL,
  `trx_ref_id` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trip_fares`
--

CREATE TABLE `trip_fares` (
  `id` char(36) NOT NULL,
  `zone_wise_default_trip_fare_id` char(36) NOT NULL,
  `zone_id` char(36) NOT NULL,
  `vehicle_category_id` char(36) NOT NULL,
  `base_fare` decimal(8,2) NOT NULL,
  `base_fare_per_km` decimal(8,2) NOT NULL,
  `waiting_fee_per_min` decimal(8,2) NOT NULL,
  `cancellation_fee_percent` decimal(8,2) NOT NULL,
  `min_cancellation_fee` decimal(8,2) NOT NULL,
  `idle_fee_per_min` decimal(8,2) NOT NULL,
  `trip_delay_fee_per_min` decimal(8,2) NOT NULL,
  `penalty_fee_for_cancel` decimal(8,2) NOT NULL,
  `fee_add_to_next` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trip_requests`
--

CREATE TABLE `trip_requests` (
  `id` char(36) NOT NULL,
  `ref_id` varchar(20) NOT NULL,
  `customer_id` char(36) DEFAULT NULL,
  `driver_id` char(36) DEFAULT NULL,
  `vehicle_category_id` char(36) DEFAULT NULL,
  `vehicle_id` char(36) DEFAULT NULL,
  `zone_id` char(36) DEFAULT NULL,
  `area_id` char(36) DEFAULT NULL,
  `estimated_fare` decimal(23,3) NOT NULL,
  `actual_fare` decimal(23,3) NOT NULL DEFAULT 0.000,
  `estimated_distance` double(8,2) NOT NULL,
  `paid_fare` decimal(23,3) NOT NULL DEFAULT 0.000,
  `actual_distance` double(8,2) DEFAULT NULL,
  `encoded_polyline` text DEFAULT NULL,
  `accepted_by` varchar(255) DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `payment_status` varchar(255) DEFAULT 'unpaid',
  `coupon_id` char(36) DEFAULT NULL,
  `coupon_amount` decimal(23,3) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `entrance` varchar(255) DEFAULT NULL,
  `otp` varchar(255) DEFAULT NULL,
  `rise_request_count` int(11) NOT NULL DEFAULT 0,
  `type` varchar(255) DEFAULT NULL,
  `current_status` varchar(20) NOT NULL DEFAULT 'pending',
  `checked` tinyint(1) NOT NULL DEFAULT 0,
  `tips` double NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_paused` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'trip_pause_status',
  `map_screenshot` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trip_request_coordinates`
--

CREATE TABLE `trip_request_coordinates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `trip_request_id` char(36) NOT NULL,
  `pickup_coordinates` point DEFAULT NULL,
  `pickup_address` varchar(255) DEFAULT NULL,
  `destination_coordinates` point DEFAULT NULL,
  `is_reached_destination` tinyint(1) NOT NULL DEFAULT 0,
  `destination_address` varchar(255) DEFAULT NULL,
  `intermediate_coordinates` text DEFAULT NULL,
  `int_coordinate_1` point DEFAULT NULL,
  `is_reached_1` tinyint(1) NOT NULL DEFAULT 0,
  `int_coordinate_2` point DEFAULT NULL,
  `is_reached_2` tinyint(1) NOT NULL DEFAULT 0,
  `intermediate_addresses` text DEFAULT NULL,
  `start_coordinates` point DEFAULT NULL,
  `drop_coordinates` point DEFAULT NULL,
  `driver_accept_coordinates` point DEFAULT NULL,
  `customer_request_coordinates` point DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trip_request_fees`
--

CREATE TABLE `trip_request_fees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `trip_request_id` char(36) NOT NULL,
  `cancellation_fee` decimal(23,3) NOT NULL DEFAULT 0.000,
  `cancelled_by` varchar(20) DEFAULT NULL,
  `waiting_fee` decimal(23,3) NOT NULL DEFAULT 0.000,
  `waited_by` varchar(20) DEFAULT NULL,
  `idle_fee` decimal(23,3) NOT NULL DEFAULT 0.000,
  `delay_fee` decimal(23,3) NOT NULL DEFAULT 0.000,
  `delayed_by` varchar(20) DEFAULT NULL,
  `vat_tax` decimal(23,3) NOT NULL DEFAULT 0.000,
  `tips` decimal(23,3) NOT NULL DEFAULT 0.000,
  `admin_commission` decimal(23,3) NOT NULL DEFAULT 0.000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trip_request_times`
--

CREATE TABLE `trip_request_times` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `trip_request_id` char(36) NOT NULL,
  `estimated_time` double(10,4) NOT NULL,
  `actual_time` double(8,2) DEFAULT NULL,
  `waiting_time` double(8,2) DEFAULT NULL,
  `delay_time` double(8,2) DEFAULT NULL,
  `idle_timestamp` timestamp NULL DEFAULT NULL,
  `idle_time` double(8,2) DEFAULT NULL,
  `driver_arrival_time` double(8,2) DEFAULT NULL,
  `driver_arrival_timestamp` timestamp NULL DEFAULT NULL,
  `driver_arrives_at` timestamp NULL DEFAULT NULL,
  `customer_arrives_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trip_routes`
--

CREATE TABLE `trip_routes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `trip_request_id` char(36) NOT NULL,
  `coordinates` point NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trip_status`
--

CREATE TABLE `trip_status` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `trip_request_id` char(36) NOT NULL,
  `customer_id` char(36) NOT NULL,
  `driver_id` char(36) DEFAULT NULL,
  `pending` timestamp NULL DEFAULT NULL,
  `accepted` timestamp NULL DEFAULT NULL,
  `out_for_pickup` timestamp NULL DEFAULT NULL,
  `picked_up` timestamp NULL DEFAULT NULL,
  `ongoing` timestamp NULL DEFAULT NULL,
  `completed` timestamp NULL DEFAULT NULL,
  `cancelled` timestamp NULL DEFAULT NULL,
  `failed` timestamp NULL DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` char(36) NOT NULL,
  `user_level_id` char(36) DEFAULT NULL,
  `first_name` varchar(191) DEFAULT NULL,
  `last_name` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `identification_number` varchar(191) DEFAULT NULL,
  `identification_type` varchar(25) DEFAULT NULL,
  `identification_image` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`identification_image`)),
  `other_documents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`other_documents`)),
  `profile_image` varchar(191) DEFAULT NULL,
  `fcm_token` varchar(191) DEFAULT NULL,
  `phone_verified_at` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `loyalty_points` double NOT NULL DEFAULT 0,
  `password` varchar(191) DEFAULT NULL,
  `user_type` varchar(25) NOT NULL DEFAULT 'customer',
  `role_id` char(36) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `failed_attempt` int(11) NOT NULL DEFAULT 0,
  `is_temp_blocked` tinyint(1) NOT NULL DEFAULT 0,
  `blocked_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_accounts`
--

CREATE TABLE `user_accounts` (
  `id` char(36) NOT NULL,
  `user_id` char(36) DEFAULT NULL,
  `payable_balance` decimal(24,2) NOT NULL DEFAULT 0.00,
  `receivable_balance` decimal(24,2) NOT NULL DEFAULT 0.00,
  `received_balance` decimal(24,2) NOT NULL DEFAULT 0.00,
  `pending_balance` decimal(24,2) NOT NULL DEFAULT 0.00,
  `wallet_balance` decimal(24,2) NOT NULL DEFAULT 0.00,
  `total_withdrawn` decimal(24,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_address`
--

CREATE TABLE `user_address` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` char(36) DEFAULT NULL,
  `zone_id` char(36) DEFAULT NULL,
  `latitude` varchar(191) DEFAULT NULL,
  `longitude` varchar(191) DEFAULT NULL,
  `city` varchar(191) DEFAULT NULL,
  `street` varchar(191) DEFAULT NULL,
  `house` varchar(191) DEFAULT NULL,
  `zip_code` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `contact_person_name` varchar(255) DEFAULT NULL,
  `contact_person_phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `address_label` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_last_locations`
--

CREATE TABLE `user_last_locations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` char(36) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `latitude` varchar(191) DEFAULT NULL,
  `longitude` varchar(191) DEFAULT NULL,
  `zone_id` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_levels`
--

CREATE TABLE `user_levels` (
  `id` char(36) NOT NULL,
  `sequence` int(11) NOT NULL,
  `name` varchar(191) NOT NULL,
  `reward_type` varchar(20) NOT NULL,
  `reward_amount` decimal(8,2) DEFAULT NULL,
  `image` varchar(191) DEFAULT NULL,
  `targeted_ride` int(11) NOT NULL,
  `targeted_ride_point` int(11) NOT NULL,
  `targeted_amount` double NOT NULL,
  `targeted_amount_point` int(11) NOT NULL,
  `targeted_cancel` int(11) NOT NULL,
  `targeted_cancel_point` int(11) NOT NULL,
  `targeted_review` int(11) NOT NULL,
  `targeted_review_point` int(11) NOT NULL,
  `user_type` varchar(20) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_level_histories`
--

CREATE TABLE `user_level_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_level_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `user_type` varchar(255) NOT NULL,
  `completed_ride` int(11) NOT NULL DEFAULT 0,
  `ride_reward_status` tinyint(1) NOT NULL DEFAULT 0,
  `total_amount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `amount_reward_status` tinyint(1) NOT NULL DEFAULT 0,
  `cancellation_rate` decimal(8,2) NOT NULL DEFAULT 0.00,
  `cancellation_reward_status` tinyint(1) NOT NULL DEFAULT 0,
  `reviews` int(11) NOT NULL DEFAULT 0,
  `reviews_reward_status` tinyint(1) NOT NULL DEFAULT 0,
  `is_level_reward_granted` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` char(36) NOT NULL,
  `ref_id` varchar(20) NOT NULL,
  `brand_id` char(36) NOT NULL,
  `model_id` char(36) NOT NULL,
  `category_id` char(36) NOT NULL,
  `licence_plate_number` varchar(255) NOT NULL,
  `licence_expire_date` date NOT NULL,
  `vin_number` varchar(255) NOT NULL,
  `transmission` varchar(255) NOT NULL,
  `fuel_type` varchar(255) NOT NULL,
  `ownership` varchar(255) NOT NULL,
  `driver_id` char(36) NOT NULL,
  `documents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`documents`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_brands`
--

CREATE TABLE `vehicle_brands` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_categories`
--

CREATE TABLE `vehicle_categories` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_category_zone`
--

CREATE TABLE `vehicle_category_zone` (
  `id` char(36) NOT NULL,
  `zone_id` char(36) DEFAULT NULL,
  `vehicle_category_id` char(36) DEFAULT NULL,
  `base_fare` decimal(8,2) NOT NULL,
  `base_fare_per_km` decimal(8,2) NOT NULL,
  `waiting_fee_per_min` decimal(8,2) NOT NULL,
  `cancellation_fee_percent` decimal(8,2) NOT NULL,
  `min_cancellation_fee` decimal(8,2) NOT NULL,
  `idle_fee_per_min` decimal(8,2) NOT NULL,
  `trip_delay_fee_per_min` decimal(8,2) NOT NULL,
  `penalty_fee_for_cancel` decimal(8,2) NOT NULL,
  `fee_add_to_next` decimal(8,2) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_models`
--

CREATE TABLE `vehicle_models` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `brand_id` char(36) NOT NULL,
  `seat_capacity` int(11) NOT NULL,
  `maximum_weight` decimal(8,2) NOT NULL,
  `hatch_bag_capacity` int(11) NOT NULL,
  `engine` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `withdraw_methods`
--

CREATE TABLE `withdraw_methods` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `method_name` varchar(255) NOT NULL,
  `method_fields` text NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `withdraw_requests`
--

CREATE TABLE `withdraw_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` char(36) NOT NULL,
  `amount` double NOT NULL DEFAULT 0,
  `method_id` bigint(20) UNSIGNED NOT NULL,
  `method_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`method_fields`)),
  `note` text DEFAULT NULL,
  `rejection_cause` text DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `zones`
--

CREATE TABLE `zones` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `coordinates` polygon DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `zone_wise_default_trip_fares`
--

CREATE TABLE `zone_wise_default_trip_fares` (
  `id` char(36) NOT NULL,
  `zone_id` char(36) NOT NULL,
  `base_fare` double NOT NULL,
  `base_fare_per_km` double NOT NULL,
  `waiting_fee_per_min` double NOT NULL,
  `cancellation_fee_percent` double NOT NULL,
  `min_cancellation_fee` double NOT NULL,
  `idle_fee_per_min` double NOT NULL,
  `trip_delay_fee_per_min` double NOT NULL,
  `penalty_fee_for_cancel` double NOT NULL,
  `fee_add_to_next` double NOT NULL,
  `category_wise_different_fare` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app_notifications`
--
ALTER TABLE `app_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `areas`
--
ALTER TABLE `areas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `areas_name_unique` (`name`);

--
-- Indexes for table `area_bonus_setup`
--
ALTER TABLE `area_bonus_setup`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `area_coupon_setup`
--
ALTER TABLE `area_coupon_setup`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `area_discount_setup`
--
ALTER TABLE `area_discount_setup`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `area_pick_hour`
--
ALTER TABLE `area_pick_hour`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `banner_setups`
--
ALTER TABLE `banner_setups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bonus_setups`
--
ALTER TABLE `bonus_setups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bonus_setup_vehicle_category`
--
ALTER TABLE `bonus_setup_vehicle_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `business_settings`
--
ALTER TABLE `business_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `channel_conversations`
--
ALTER TABLE `channel_conversations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `channel_lists`
--
ALTER TABLE `channel_lists`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `channel_users`
--
ALTER TABLE `channel_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `conversation_files`
--
ALTER TABLE `conversation_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coupon_setups`
--
ALTER TABLE `coupon_setups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `coupon_setups_coupon_code_unique` (`coupon_code`);

--
-- Indexes for table `coupon_setup_vehicle_category`
--
ALTER TABLE `coupon_setup_vehicle_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `discount_setups`
--
ALTER TABLE `discount_setups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `discount_setup_vehicle_category`
--
ALTER TABLE `discount_setup_vehicle_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `driver_details`
--
ALTER TABLE `driver_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `driver_time_logs`
--
ALTER TABLE `driver_time_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `fare_biddings`
--
ALTER TABLE `fare_biddings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fare_bidding_logs`
--
ALTER TABLE `fare_bidding_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `firebase_push_notifications`
--
ALTER TABLE `firebase_push_notifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `firebase_push_notifications_name_unique` (`name`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `level_accesses`
--
ALTER TABLE `level_accesses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loyalty_points_histories`
--
ALTER TABLE `loyalty_points_histories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `milestone_setups`
--
ALTER TABLE `milestone_setups`
  ADD UNIQUE KEY `milestone_setups_id_unique` (`id`);

--
-- Indexes for table `module_accesses`
--
ALTER TABLE `module_accesses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification_settings`
--
ALTER TABLE `notification_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_access_tokens`
--
ALTER TABLE `oauth_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_access_tokens_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_auth_codes`
--
ALTER TABLE `oauth_auth_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_auth_codes_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_clients_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_refresh_tokens`
--
ALTER TABLE `oauth_refresh_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`);

--
-- Indexes for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `parcels`
--
ALTER TABLE `parcels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `parcel_categories`
--
ALTER TABLE `parcel_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `parcel_categories_name_unique` (`name`);

--
-- Indexes for table `parcel_fares`
--
ALTER TABLE `parcel_fares`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `parcel_fares_parcel_weights`
--
ALTER TABLE `parcel_fares_parcel_weights`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `parcel_information`
--
ALTER TABLE `parcel_information`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `parcel_user_infomations`
--
ALTER TABLE `parcel_user_infomations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `parcel_weights`
--
ALTER TABLE `parcel_weights`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_requests`
--
ALTER TABLE `payment_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `pick_hours`
--
ALTER TABLE `pick_hours`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recent_addresses`
--
ALTER TABLE `recent_addresses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rejected_driver_requests`
--
ALTER TABLE `rejected_driver_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_user`
--
ALTER TABLE `role_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_settings_id_index` (`id`);

--
-- Indexes for table `social_links`
--
ALTER TABLE `social_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `temp_trip_notifications`
--
ALTER TABLE `temp_trip_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `time_logs`
--
ALTER TABLE `time_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `time_tracks`
--
ALTER TABLE `time_tracks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trip_fares`
--
ALTER TABLE `trip_fares`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trip_requests`
--
ALTER TABLE `trip_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trip_request_coordinates`
--
ALTER TABLE `trip_request_coordinates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trip_request_fees`
--
ALTER TABLE `trip_request_fees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trip_request_times`
--
ALTER TABLE `trip_request_times`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trip_routes`
--
ALTER TABLE `trip_routes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trip_status`
--
ALTER TABLE `trip_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_phone_unique` (`phone`);

--
-- Indexes for table `user_accounts`
--
ALTER TABLE `user_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_address`
--
ALTER TABLE `user_address`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_last_locations`
--
ALTER TABLE `user_last_locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_levels`
--
ALTER TABLE `user_levels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_level_histories`
--
ALTER TABLE `user_level_histories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vehicle_brands`
--
ALTER TABLE `vehicle_brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicle_brands_name_unique` (`name`);

--
-- Indexes for table `vehicle_categories`
--
ALTER TABLE `vehicle_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicle_categories_name_unique` (`name`);

--
-- Indexes for table `vehicle_category_zone`
--
ALTER TABLE `vehicle_category_zone`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vehicle_models`
--
ALTER TABLE `vehicle_models`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `withdraw_methods`
--
ALTER TABLE `withdraw_methods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `withdraw_requests`
--
ALTER TABLE `withdraw_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `zones`
--
ALTER TABLE `zones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `zones_name_unique` (`name`);

--
-- Indexes for table `zone_wise_default_trip_fares`
--
ALTER TABLE `zone_wise_default_trip_fares`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `app_notifications`
--
ALTER TABLE `app_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `area_bonus_setup`
--
ALTER TABLE `area_bonus_setup`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `area_coupon_setup`
--
ALTER TABLE `area_coupon_setup`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `area_discount_setup`
--
ALTER TABLE `area_discount_setup`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `area_pick_hour`
--
ALTER TABLE `area_pick_hour`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bonus_setup_vehicle_category`
--
ALTER TABLE `bonus_setup_vehicle_category`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `channel_conversations`
--
ALTER TABLE `channel_conversations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `channel_users`
--
ALTER TABLE `channel_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `conversation_files`
--
ALTER TABLE `conversation_files`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coupon_setup_vehicle_category`
--
ALTER TABLE `coupon_setup_vehicle_category`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `discount_setup_vehicle_category`
--
ALTER TABLE `discount_setup_vehicle_category`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `driver_details`
--
ALTER TABLE `driver_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `driver_time_logs`
--
ALTER TABLE `driver_time_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `firebase_push_notifications`
--
ALTER TABLE `firebase_push_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `level_accesses`
--
ALTER TABLE `level_accesses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loyalty_points_histories`
--
ALTER TABLE `loyalty_points_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `module_accesses`
--
ALTER TABLE `module_accesses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_settings`
--
ALTER TABLE `notification_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `parcel_fares_parcel_weights`
--
ALTER TABLE `parcel_fares_parcel_weights`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parcel_information`
--
ALTER TABLE `parcel_information`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parcel_user_infomations`
--
ALTER TABLE `parcel_user_infomations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recent_addresses`
--
ALTER TABLE `recent_addresses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rejected_driver_requests`
--
ALTER TABLE `rejected_driver_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `role_user`
--
ALTER TABLE `role_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `temp_trip_notifications`
--
ALTER TABLE `temp_trip_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=326;

--
-- AUTO_INCREMENT for table `time_logs`
--
ALTER TABLE `time_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `time_tracks`
--
ALTER TABLE `time_tracks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trip_request_coordinates`
--
ALTER TABLE `trip_request_coordinates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trip_request_fees`
--
ALTER TABLE `trip_request_fees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trip_request_times`
--
ALTER TABLE `trip_request_times`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trip_routes`
--
ALTER TABLE `trip_routes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trip_status`
--
ALTER TABLE `trip_status`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_address`
--
ALTER TABLE `user_address`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_last_locations`
--
ALTER TABLE `user_last_locations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_level_histories`
--
ALTER TABLE `user_level_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `withdraw_methods`
--
ALTER TABLE `withdraw_methods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `withdraw_requests`
--
ALTER TABLE `withdraw_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

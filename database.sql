-- CSX16-SERVER STATS Database Structure
-- Target Installation: csx16.ro/servers
-- Version: 1.0.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 1. Table structure for `settings`
-- Stores global configuration like the installation URL
--

CREATE TABLE IF NOT EXISTS `settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_name', 'CSX16-SERVER STATS'),
('site_url', 'https://csx16.ro/servers'),
('admin_email', 'contact@csx16.ro'),
('allow_registration', '1'),
('votes_cooldown_hours', '24');

--
-- 2. Table structure for `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL COMMENT 'Store using password_hash()',
  `role` enum('user','admin') DEFAULT 'user',
  `avatar_url` varchar(255) DEFAULT NULL,
  `api_key` varchar(64) DEFAULT NULL COMMENT 'For external API access',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 3. Table structure for `servers`
--

CREATE TABLE IF NOT EXISTS `servers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `ip` varchar(100) NOT NULL,
  `port` int(11) NOT NULL,
  `query_port` int(11) DEFAULT NULL COMMENT 'If different from join port',
  `game` varchar(50) NOT NULL COMMENT 'cs16, cs2, minecraft, etc.',
  `map` varchar(100) DEFAULT NULL,
  `players` int(11) DEFAULT 0,
  `max_players` int(11) DEFAULT 0,
  `status` enum('ONLINE','OFFLINE','MAINTENANCE') DEFAULT 'OFFLINE',
  `votes` int(11) DEFAULT 0,
  `rank` int(11) DEFAULT 0,
  `description` text,
  `banner_url` varchar(255) DEFAULT NULL,
  `version` varchar(50) DEFAULT NULL,
  `country` char(2) DEFAULT 'RO',
  `last_checked` timestamp NULL DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `owner_id` (`owner_id`),
  KEY `votes_ranking` (`votes` DESC),
  KEY `status_check` (`status`),
  CONSTRAINT `fk_server_owner` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 4. Table structure for `votes`
-- Logic: Enforces the 24h limit per IP
--

CREATE TABLE IF NOT EXISTS `votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `server_id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `server_id` (`server_id`),
  KEY `ip_check` (`ip_address`, `server_id`, `created_at`),
  CONSTRAINT `fk_vote_server` FOREIGN KEY (`server_id`) REFERENCES `servers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 5. Table structure for `server_history`
-- Logic: Stores player counts for the last 24h charts
--

CREATE TABLE IF NOT EXISTS `server_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `server_id` int(11) NOT NULL,
  `players` int(11) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `server_history_idx` (`server_id`, `created_at`),
  CONSTRAINT `fk_history_server` FOREIGN KEY (`server_id`) REFERENCES `servers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Default Admin User
-- User: admin / Pass: admin123 (HASH THIS BEFORE PRODUCTION USE)
--

INSERT INTO `users` (`username`, `email`, `password`, `role`) VALUES
('admin', 'admin@csx16.ro', '$2y$10$8.D./.H./.H./.H./.H./.H./.H./.H./.H./.H./.H./.H./.H.', 'admin');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

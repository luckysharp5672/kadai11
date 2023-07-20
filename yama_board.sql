-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2023-07-21 01:12:25
-- サーバのバージョン： 10.4.28-MariaDB
-- PHP のバージョン: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `yama_board`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `boards`
--

CREATE TABLE `boards` (
  `id` int(6) UNSIGNED NOT NULL,
  `board_id` int(6) UNSIGNED NOT NULL,
  `boardTitle` text NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `username` varchar(30) NOT NULL,
  `indate` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `boards`
--

INSERT INTO `boards` (`id`, `board_id`, `boardTitle`, `latitude`, `longitude`, `username`, `indate`) VALUES
(56, 0, 'みなとみらい', 35.45595150, 139.63316930, 'よっしー', NULL),
(60, 0, '掲示板_希望ヶ丘駅', 35.46042783, 139.51402782, 'よっしー', NULL),
(62, 0, '掲示板_三浦半島', 35.18484110, 139.65082150, 'よっしー', NULL),
(67, 0, '掲示板_希望ヶ丘さんぽコース', 35.46190000, 139.49692960, 'よっしー', NULL);

-- --------------------------------------------------------

--
-- テーブルの構造 `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `board_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `messages`
--

INSERT INTO `messages` (`id`, `board_id`, `user_id`, `message`, `created_at`) VALUES
(12, 0, 1, '', '2023-07-16 02:37:27'),
(13, 0, 1, '', '2023-07-16 02:37:52'),
(14, 0, 1, '', '2023-07-16 02:38:16'),
(15, 0, 1, '', '2023-07-16 02:50:12'),
(16, 0, 1, '', '2023-07-16 02:56:11'),
(17, 0, 1, '', '2023-07-16 02:56:27'),
(18, 0, 1, 'hello', '2023-07-16 02:59:29'),
(19, NULL, 1, 'おはよう', '2023-07-16 03:04:01'),
(20, 0, 1, 'hello', '2023-07-16 22:28:26'),
(21, NULL, 1, 'こんばんは', '2023-07-16 22:49:50'),
(22, 92, 1, 'これでどうだ', '2023-07-16 22:51:18'),
(23, 92, 1, '行きますよー', '2023-07-16 22:53:55'),
(24, 93, 1, 'こんにちは', '2023-07-16 23:04:49'),
(25, 94, 1, 'ははは', '2023-07-16 23:20:13'),
(26, 102, 1, 'Good afternoon', '2023-07-17 02:37:54'),
(27, 104, 5, 'こんばんは', '2023-07-17 03:21:08'),
(28, 104, 5, 'こんにちは', '2023-07-17 03:45:08'),
(29, 104, 5, NULL, '2023-07-17 03:45:15'),
(30, 104, 5, 'こんにちは', '2023-07-17 04:11:50'),
(40, 56, 2, 'おはよう', '2023-07-17 06:48:15'),
(41, 60, 2, 'こんばんは', '2023-07-17 07:03:34'),
(42, 56, 1, 'ここは私の会社がある場所です', '2023-07-20 20:37:17');

-- --------------------------------------------------------

--
-- テーブルの構造 `users`
--

CREATE TABLE `users` (
  `id` int(6) UNSIGNED NOT NULL,
  `username` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `kanri_flg` int(1) NOT NULL,
  `tracker_flg` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `kanri_flg`, `tracker_flg`) VALUES
(1, 'よっしー', 'luckysharp5672@gmail.com', '$2y$10$ge40aywoKuqwqjMcfs/AzuRoPqVSdfXVhqfrJLbDyZFj/Z4Ov0gEK', 1, 0),
(2, 'ラッキー', 'lucky@sharp.com', '$2y$10$4s.gumX9ajeUFJKHKckncOZa0WAtvH9upoJ9b7o930IQs0pfMiKHu', 1, 0),
(5, 'テスト', 'test@test.com', '$2y$10$PHw1DUawsH8romyfS0KlUurEW9yKPM/7ilQk2idt8V9WDibNeCnKG', 0, 0);

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `boards`
--
ALTER TABLE `boards`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `boardTitle` (`boardTitle`) USING HASH,
  ADD KEY `username` (`username`);

--
-- テーブルのインデックス `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- テーブルのインデックス `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `boards`
--
ALTER TABLE `boards`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- テーブルの AUTO_INCREMENT `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- テーブルの AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

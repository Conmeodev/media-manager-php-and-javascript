-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th8 12, 2025 lúc 06:50 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `upload_install`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `alert`
--

CREATE TABLE `alert` (
  `id` int(11) NOT NULL,
  `_name` text DEFAULT NULL,
  `_stt` int(11) DEFAULT NULL,
  `_show` int(11) DEFAULT NULL,
  `_content` text DEFAULT NULL,
  `_time` text DEFAULT NULL,
  `_token` text DEFAULT NULL,
  `_uid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `content`
--

CREATE TABLE `content` (
  `id` int(11) NOT NULL,
  `_uid` int(11) DEFAULT NULL,
  `_thumuc` int(11) DEFAULT NULL,
  `_tieude` text DEFAULT NULL,
  `_noidung` text DEFAULT NULL,
  `_loai` text DEFAULT NULL,
  `_anh` text DEFAULT NULL,
  `_time` int(11) DEFAULT NULL,
  `_tinhtrang` text DEFAULT NULL,
  `_chiase` text DEFAULT NULL,
  `_tukhoa` text DEFAULT NULL,
  `_token` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `files`
--

CREATE TABLE `files` (
  `id` int(11) NOT NULL,
  `_name` text DEFAULT NULL,
  `_thumb` text DEFAULT NULL,
  `_uid` text DEFAULT NULL,
  `_byid` text DEFAULT NULL,
  `_time` text DEFAULT NULL,
  `_share` text DEFAULT NULL,
  `_list_share` text DEFAULT NULL,
  `_token` text DEFAULT NULL,
  `old_name` text DEFAULT NULL,
  `_type` text DEFAULT NULL,
  `_dir` text DEFAULT NULL,
  `_tinhtrang` text DEFAULT NULL,
  `_size` text DEFAULT NULL,
  `keysearch` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `folders`
--

CREATE TABLE `folders` (
  `id` int(11) NOT NULL,
  `_uid` int(11) NOT NULL,
  `_byid` int(11) NOT NULL,
  `_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `_thumb` text DEFAULT NULL,
  `_content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `_type` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `_tinhtrang` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `_token` text NOT NULL,
  `_time` int(11) NOT NULL,
  `keysearch` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `login`
--

CREATE TABLE `login` (
  `id` int(11) NOT NULL,
  `_uid` text DEFAULT NULL,
  `_matkhau` text DEFAULT NULL,
  `_time` text DEFAULT NULL,
  `_tinhtrang` text DEFAULT NULL,
  `_ip` text DEFAULT NULL,
  `_device` text DEFAULT NULL,
  `_token` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `login`
--

INSERT INTO `login` (`id`, `_uid`, `_matkhau`, `_time`, `_tinhtrang`, `_ip`, `_device`, `_token`) VALUES
(1, '1', '12345', '1754974051', 'dangnhap', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '0a26e3b0fe44758ccdac4124fa19dd56fc05e33fbcf6cd4eae6338b668411743');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `ip` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `mylove`
--

CREATE TABLE `mylove` (
  `id` int(11) NOT NULL,
  `_uid` int(11) NOT NULL,
  `_byid` int(11) NOT NULL,
  `_type` text CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `process`
--

CREATE TABLE `process` (
  `id` int(11) NOT NULL,
  `_act` text DEFAULT NULL,
  `_tinhtrang` text DEFAULT NULL,
  `value2` text DEFAULT NULL,
  `value3` text DEFAULT NULL,
  `value4` text DEFAULT NULL,
  `value5` text DEFAULT NULL,
  `value1` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `search`
--

CREATE TABLE `search` (
  `id` int(11) NOT NULL,
  `_key` text NOT NULL,
  `_count` int(11) NOT NULL,
  `_ip` text NOT NULL,
  `_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `_taikhoan` text DEFAULT NULL,
  `_matkhau` text DEFAULT NULL,
  `_capdo` int(11) DEFAULT NULL,
  `_mod` text DEFAULT NULL,
  `_email` text DEFAULT NULL,
  `_ip` text DEFAULT NULL,
  `_tinhtrang` text DEFAULT NULL,
  `_time` text DEFAULT NULL,
  `_mkh` text DEFAULT NULL,
  `_cap` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `visit`
--

CREATE TABLE `visit` (
  `id` int(11) NOT NULL,
  `_ip` text NOT NULL,
  `_url` text NOT NULL,
  `_type` text NOT NULL,
  `_time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `alert`
--
ALTER TABLE `alert`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `content`
--
ALTER TABLE `content`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `folders`
--
ALTER TABLE `folders`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `mylove`
--
ALTER TABLE `mylove`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `process`
--
ALTER TABLE `process`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `search`
--
ALTER TABLE `search`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `visit`
--
ALTER TABLE `visit`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `alert`
--
ALTER TABLE `alert`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `content`
--
ALTER TABLE `content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `folders`
--
ALTER TABLE `folders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `login`
--
ALTER TABLE `login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `mylove`
--
ALTER TABLE `mylove`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `process`
--
ALTER TABLE `process`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `search`
--
ALTER TABLE `search`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `visit`
--
ALTER TABLE `visit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

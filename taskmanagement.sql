-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 03, 2025 lúc 07:48 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `taskmanagement`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tasks`
--

CREATE TABLE `tasks1` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `priority` enum('Low','Medium','High') NOT NULL,
  `status` enum('Pending','Completed') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tasks`
--

INSERT INTO `tasks1` (`id`, `title`, `description`, `due_date`, `priority`, `status`) VALUES
(1, 'Học PHP', 'Hoàn thành bài tập PHP Task Management', '2025-04-10', 'High', 'Pending'),
(2, 'Thiết kế giao diện', 'Tạo giao diện trang quản lý công việc', '2025-04-12', 'Medium', 'Pending'),
(3, 'Kiểm tra lỗi', 'Test toàn bộ chức năng của hệ thống', '2025-04-15', 'High', 'Pending'),
(4, 'Viết tài liệu', 'Soạn hướng dẫn sử dụng hệ thống', '2025-04-18', 'Low', 'Pending'),
(5, 'Tối ưu database', 'Cải thiện hiệu suất truy vấn SQL', '2025-04-20', 'Medium', 'Pending'),
(6, 'Xây dựng API', 'Tạo API để giao tiếp với frontend', '2025-04-22', 'High', 'Pending'),
(7, 'Thiết lập môi trường', 'Cấu hình XAMPP và kết nối MySQL', '2025-04-08', 'Medium', 'Completed'),
(8, 'Nghiên cứu AJAX', 'Tìm hiểu cách dùng AJAX trong PHP', '2025-04-14', 'Low', 'Pending'),
(9, 'Tích hợp Bootstrap', 'Thêm Bootstrap để cải thiện UI', '2025-04-16', 'Medium', 'Pending'),
(10, 'Fix bug giao diện', 'Sửa lỗi hiển thị trên các trình duyệt', '2025-04-19', 'High', 'Pending'),
(11, 'Viết chức năng tìm kiếm', 'Tạo form tìm kiếm công việc', '2025-04-21', 'Medium', 'Pending'),
(12, 'Tạo báo cáo', 'Xuất dữ liệu ra file Excel/PDF', '2025-04-23', 'High', 'Pending'),
(13, 'Làm slide thuyết trình', 'Chuẩn bị nội dung cho bài thuyết trình', '2025-04-25', 'Low', 'Pending'),
(14, 'Đánh giá kết quả', 'Kiểm tra lại toàn bộ hệ thống', '2025-04-27', 'High', 'Pending'),
(15, 'Triển khai hệ thống', 'Đưa hệ thống lên hosting', '2025-04-30', 'High', 'Pending');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `tasks`
--
ALTER TABLE `tasks1`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `tasks`
--
ALTER TABLE `tasks1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;
ALTER TABLE tasks1 ADD COLUMN position INT DEFAULT 0;
ALTER TABLE tasks1 ADD COLUMN completed TINYINT(1) DEFAULT 0;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
-- Tạo bảng registered_emails
CREATE TABLE `registered_emails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL UNIQUE,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tạo bảng `user` để lưu thông tin người dùng
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
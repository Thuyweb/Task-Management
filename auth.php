<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id']) || !isset($_SESSION['task_table'])) {
    header("Location: dangnhap.php");
    exit;
}
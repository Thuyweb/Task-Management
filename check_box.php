<?php
require 'auth.php';
require 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id']) && isset($data['completed'])) {
    // Lấy tên bảng công việc cá nhân từ session
    if (!isset($_SESSION['task_table'])) {
        echo json_encode(['success' => false, 'error' => 'Không tìm thấy bảng công việc cá nhân.']);
        exit;
    }
    $taskTable = $_SESSION['task_table'];

    // Lấy dữ liệu từ request
    $id = $data['id'];
    $completed = $data['completed'];

    // Cập nhật trạng thái "completed" trong cơ sở dữ liệu
    $sql = "UPDATE $taskTable SET completed = :completed WHERE id = :id";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([':completed' => $completed, ':id' => $id])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Không thể cập nhật trạng thái.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Dữ liệu không hợp lệ.']);
}

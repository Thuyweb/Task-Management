<?php
require 'auth.php';
require 'config.php'; // Kết nối cơ sở dữ liệu

// Kiểm tra xem người dùng đã đăng nhập chưa
// if (!isset($_SESSION['task_table'])) {
//     die(json_encode(['success' => false, 'error' => 'Không tìm thấy bảng công việc cá nhân. Vui lòng đăng nhập lại.']));
// }

// Lấy tên bảng công việc cá nhân từ session
$taskTable = $_SESSION['task_table'];

// Lấy dữ liệu thứ tự từ POST request (dưới dạng JSON)
$data = json_decode(file_get_contents('php://input'), true);

// Kiểm tra xem có dữ liệu thứ tự không
if (isset($data['order'])) {
    $order = $data['order'];

    // Cập nhật lại thứ tự cho từng công việc
    foreach ($order as $task) {
        $task_id = $task['id'];
        $position = $task['position'];

        // Cập nhật trường "position" trong cơ sở dữ liệu
        $sql_update = "UPDATE $taskTable SET position = :position WHERE id = :id";
        $stmt = $pdo->prepare($sql_update);
        $stmt->execute([
            ':position' => $position,
            ':id' => $task_id
        ]);
    }

    // Trả về kết quả
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Dữ liệu không hợp lệ.']);
}

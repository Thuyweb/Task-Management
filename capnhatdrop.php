<?php
require 'auth.php';
require 'config.php'; // Kết nối cơ sở dữ liệu

// Lấy dữ liệu thứ tự từ POST request (dưới dạng JSON)
$data = json_decode(file_get_contents('php://input'), true);

// Kiểm tra xem có dữ liệu thứ tự không
if (isset($data['order'])) {
    $order = $data['order'];

    // Cập nhật lại thứ tự cho từng công việc
    foreach ($order as $index => $task) {
        $task_id = $task['id'];
        $position = $task['position'];

        // Cập nhật trường "position" trong cơ sở dữ liệu (bạn có thể thay thế bằng thứ tự mới nếu cần)
        $sql_update = "UPDATE tasks SET position = $position WHERE id = $task_id";
        $conn->query($sql_update);
    }

    // Trả về kết quả
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
<?php
require 'auth.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Kết nối cơ sở dữ liệu
require '../config.php';

try {
    // Lấy danh sách người dùng và email của họ
    $userQuery = "SELECT id, email FROM user";
    $userStmt = $pdo->query($userQuery);
    $users = $userStmt->fetchAll();


    if (count($users) > 0) {
        foreach ($users as $user) {
            $userId = $user['id'];
            $email = $user['email'];

            // Lấy công việc sắp đến hạn chót của người dùng
            $taskTable = "tasks" . $userId; // Giả sử mỗi người dùng có bảng công việc riêng
            $taskQuery = "SELECT title, description, due_date FROM $taskTable WHERE due_date BETWEEN CURDATE() AND CURDATE() + INTERVAL 3 DAY";
            $taskStmt = $pdo->query($taskQuery);
            $tasks = $taskStmt->fetchAll();

            if (count($tasks) > 0) {
                // Tạo danh sách công việc
                $taskList = "Danh sách công việc sắp đến hạn chót:\n\n";
                foreach ($tasks as $task) {
                    $taskList .= "- Tiêu đề: " . htmlspecialchars($task['title']) . "\n";
                    $taskList .= "  Hạn chót: " . $task['due_date'] . "\n";
                    $taskList .= "  Mô tả: " . htmlspecialchars($task['description']) . "\n\n";
                }

                // Tạo nội dung email
                $emailBody = "Xin chào,\n\n$taskList";
                $emailBody .= "Hãy đảm bảo hoàn thành các công việc này đúng hạn!\n\nTrân trọng,\nTask Manager";

                $mail = new PHPMailer(true);


                try {
                    // Cấu hình SMTP với Gmail
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'phpkt2muc5@gmail.com'; // Thay bằng email Gmail của bạn
                    $mail->Password = 'dsng cdbe cktc ctux'; // Thay bằng mật khẩu ứng dụng Gmail
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Cấu hình email
                    $mail->CharSet = 'UTF-8';
                    $mail->setFrom('phpkt2muc5@gmail.com', 'Task Manager');
                    $mail->addAddress($email);
                    $mail->Subject = 'Nhắc nhở: Danh sách công việc sắp đến hạn chót';
                    $mail->Body = $emailBody;

                    // Gửi email
                    if ($mail->send()) {
                        echo "Email đã được gửi đến: $email\n";
                    } else {
                        echo "Không thể gửi email đến $email: " . $mail->ErrorInfo . "\n";
                    }
                } catch (Exception $e) {
                    echo "Đã xảy ra lỗi khi gửi email đến $email: " . $mail->ErrorInfo . "\n";
                }
            } else {
                echo "Không có công việc nào sắp đến hạn chót cho người dùng có email: $email\n";
            }
        }
    } else {
        echo "Không có người dùng nào trong hệ thống.\n";
    }
} catch (Exception $e) {
    echo "Đã xảy ra lỗi: " . $e->getMessage();
}
ini_set('display_errors', 1);
error_reporting(E_ALL);

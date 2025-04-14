<?php
require 'auth.php';
require 'vendor/autoload.php'; // Đảm bảo bạn đã cài đặt PHPMailer qua Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Kết nối cơ sở dữ liệu
require 'config.php'; // Kết nối cơ sở dữ liệu bằng PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['notify']) && $_POST['notify'] === 'yes') {
        $email = $_POST['email'] ?? '';
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Lưu email vào bảng registered_emails
            try {
                $stmt = $pdo->prepare("INSERT IGNORE INTO registered_emails (email) VALUES (:email)");
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->execute();
            } catch (PDOException $e) {
                echo "<script>
                    alert('Đã xảy ra lỗi khi lưu email: " . $e->getMessage() . "');
                    window.location.href = 'sendgmail.php';
                </script>";
                exit();
            }

            // Lấy tên bảng công việc cá nhân từ session
            if (!isset($_SESSION['task_table'])) {
                die("Không tìm thấy bảng công việc cá nhân. Vui lòng đăng nhập lại.");
            }
            $taskTable = $_SESSION['task_table'];

            // Truy vấn các công việc có hạn chót trong 3 ngày tới
            $stmt = $pdo->prepare("SELECT * FROM $taskTable WHERE due_date BETWEEN CURDATE() AND CURDATE() + INTERVAL 3 DAY");
            $stmt->execute();
            $tasks = $stmt->fetchAll();

            if (!empty($tasks)) {
                // Tạo nội dung email
                $emailBody = "Xin chào,\n\nDưới đây là danh sách các công việc sắp đến hạn chót của bạn (trong 3 ngày tới):\n\n";
                foreach ($tasks as $task) {
                    $emailBody .= "- " . htmlspecialchars($task['title']) . " (Hạn chót: " . $task['due_date'] . ")\n";
                }
                $emailBody .= "\nHãy đảm bảo hoàn thành các công việc này đúng hạn!\n\nTrân trọng,\nĐội ngũ Task Manager";

                $mail = new PHPMailer(true);

                try {
                    // Cấu hình SMTP với Gmail
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com'; // Máy chủ SMTP của Gmail
                    $mail->SMTPAuth = true;
                    $mail->Username = 'phpkt2muc5@gmail.com'; // Thay bằng email Gmail của bạn
                    $mail->Password = 'dsng cdbe cktc ctux'; // Thay bằng mật khẩu ứng dụng Gmail
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Sử dụng mã hóa TLS
                    $mail->Port = 587; // Cổng SMTP của Gmail

                    // Cấu hình email
                    $mail->CharSet = 'UTF-8'; // Thiết lập mã hóa UTF-8
                    $mail->setFrom('phpkt2muc5@gmail.com', 'Task Manager'); // Email gửi đi
                    $mail->addAddress($email); // Email người nhận
                    $mail->Subject = 'Thông báo công việc sắp đến hạn chót'; // Tiêu đề email
                    $mail->Body = $emailBody;

                    // Gửi email
                    $mail->send();

                    // Hiển thị thông báo thành công và chuyển hướng bằng JavaScript
                    echo "<script>
                        alert('Thông báo đã được gửi thành công!');
                        window.location.href = 'index.php';
                    </script>";
                    exit(); // Dừng thực thi mã sau khi chuyển hướng
                } catch (Exception $e) {
                    echo "Đã xảy ra lỗi khi gửi email: " . $mail->ErrorInfo;
                }
            } else {
                echo "<script>
                    alert('Hiện tại không có công việc nào sắp đến hạn chót.');
                    window.location.href = 'index.php';
                </script>";
            }
        } else {
            echo "<script>
                alert('Email không hợp lệ. Vui lòng nhập lại.');
                window.location.href = 'sendgmail.php';
            </script>";
        }
    } else {
        echo "<script>
            alert('Bạn đã chọn không nhận thông báo.');
            window.location.href = 'index.php';
        </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo nhắc hẹn - Task Management</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f9;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .container {
        background: #ffffff;
        padding: 20px 30px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        max-width: 500px;
        width: 100%;
        text-align: center;
    }

    h1 {
        color: #333333;
        font-size: 24px;
        margin-bottom: 10px;
    }

    p.description {
        color: #555555;
        font-size: 16px;
        margin-bottom: 20px;
        line-height: 1.5;
    }

    .spaced-text {
        line-height: 1.5;
    }

    form {
        margin-top: 20px;
    }

    label {
        font-size: 16px;
        color: #333333;
        margin-right: 10px;
    }

    input[type="radio"] {
        margin-right: 5px;
    }

    #emailInput {
        margin-top: 15px;
    }

    input[type="email"] {
        width: 100%;
        padding: 10px;
        margin-top: 10px;
        border: 1px solid #cccccc;
        border-radius: 4px;
        font-size: 14px;
    }

    button[type="submit"] {
        background-color: #007bff;
        color: #ffffff;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 4px;
        cursor: pointer;
        margin-top: 20px;
    }

    button[type="submit"]:hover {
        background-color: #0056b3;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1>Chào mừng đến với Task Management</h1>
        <p class="description">
            Đừng lo nếu bạn sợ bỏ lỡ công việc quan trọng, chúng tôi đồng hành cùng bạn.
            Chúng tôi sẽ lọc ra và nhắc nhở công việc đến hạn chót.
            Thông báo sẽ hiển thị đầy đủ, giúp bạn không bỏ sót công việc nào!
        </p>
        <form method="POST">
            <p class="spaced-text">Bạn có muốn chúng tôi giúp bạn kiểm tra và thông báo<br> những công việc gần đến hạn
                ngay
                bây giờ không?</p>
            <label>
                <input type="radio" name="notify" value="yes" required> Có
            </label>
            <label>
                <input type="radio" name="notify" value="no" required> Không
            </label>
            <div id="emailInput" style="display: none;">
                <label for="email">Nhập email của bạn:</label>
                <input type="email" name="email" id="email" placeholder="example@domain.com" required>
            </div>
            <button type="submit">Xác nhận</button>
        </form>
    </div>

    <script>
    // Hiển thị ô nhập email nếu chọn "Có"
    document.querySelectorAll('input[name="notify"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const emailInput = document.getElementById('emailInput');
            const emailField = emailInput.querySelector('input[name="email"]');

            if (this.value === 'yes') {
                emailInput.style.display = 'block'; // Hiển thị form nhập email
                emailField.required = true; // Bắt buộc nhập email nếu chọn "Có"
            } else {
                emailInput.style.display = 'none'; // Ẩn form nhập email
                emailField.required = false; // Không bắt buộc nhập email nếu chọn "Không"
            }
        });
    });
    </script>
</body>

</html>
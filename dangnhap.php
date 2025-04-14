<?php
require 'config.php';
session_start();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    if (isset($_POST['register'])) {
        // Xử lý đăng ký
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        if (empty($name) || empty($email) || empty($password)) {
            $error = "Vui lòng nhập đầy đủ thông tin!";
        } else {
            // Kiểm tra xem email đã tồn tại chưa
            $sql = "SELECT * FROM user WHERE email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user) {
                $error = "Email đã tồn tại!";
            } else {
                // Thêm người dùng mới vào bảng `user`
                $sql = "INSERT INTO user (name, email, password) VALUES (:name, :email, :password)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'name' => $name,
                    'email' => $email,
                    'password' => $password
                ]);

                // Lấy ID của người dùng vừa được thêm
                $userId = $pdo->lastInsertId();

                $taskTableName = "tasks" . $userId;

                // Tạo bảng công việc cá nhân
                $createTableSQL = "
    CREATE TABLE $taskTableName (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        due_date DATE,
        priority ENUM('Low', 'Medium', 'High') NOT NULL,
        status ENUM('Pending', 'Completed') NOT NULL DEFAULT 'Pending',
        position INT DEFAULT 0,
        completed TINYINT(1) NOT NULL DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
";
                $pdo->exec($createTableSQL);

                // Thêm công việc mẫu
                $defaultTask = "Công việc đầu tiên của bạn";
                $defaultDescription = "Đây là công việc mặc định được tạo khi bạn đăng ký.";
                $dueDate = date('Y-m-d', strtotime('+7 days')); // Hạn chót sau 7 ngày
                $priority = 'Medium';

                $insertTaskSQL = "
    INSERT INTO $taskTableName (title, description, due_date, priority, status, position, completed)
    VALUES (:title, :description, :due_date, :priority, 'Pending', 1, 0)
";
                $stmt = $pdo->prepare($insertTaskSQL);
                $stmt->execute([
                    'title' => $defaultTask,
                    'description' => $defaultDescription,
                    'due_date' => $dueDate,
                    'priority' => $priority
                ]);

                $success = "Đăng ký thành công! Bạn có thể đăng nhập.";
            }
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logins'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Vui lòng nhập đầy đủ email và mật khẩu!";
    } else {
        try {
            // Kiểm tra email trong cơ sở dữ liệu
            $sql = "SELECT * FROM user WHERE email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();
            if ($user) {
                // So sánh mật khẩu trực tiếp (không mã hóa)
                if ($password === $user['password']) {
                    // Lưu thông tin người dùng vào session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['task_table'] = "tasks" . $user['id']; // Lưu tên bảng công việc cá nhân

                    header("Location: index.php");
                    exit;
                } else {
                    $error = "Mật khẩu không đúng!";
                }
            } else {
                $error = "Email không tồn tại!";
            }
        } catch (PDOException $e) {
            $error = "Lỗi hệ thống: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="icon" href="images/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="css/dangnhap.css">

</head>

<body>
    <div class="body">
        <div class="container" id="container">
            <div class="form-container register-container">
                <form action="dangnhap.php" method="POST">
                    <h1>Đăng ký</h1>
                    <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>
                    <?php if (!empty($success)) echo "<p style='color: green;'>$success</p>"; ?>
                    <input type="text" name="name" placeholder="Tên đầy đủ" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Mật khẩu" required>
                    <button type="submit" name="register">Đăng ký</button>
                    <span>Hoặc sử dụng tài khoản của bạn</span>
                    <div class="social-container">
                        <a href="" class="social"><svg class="register" xmlns="http://www.w3.org/2000/svg" height="25"
                                width="25" viewBox="0 0 512 512">
                                <!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                <path fill="#005eff"
                                    d="M512 256C512 114.6 397.4 0 256 0S0 114.6 0 256C0 376 82.7 476.8 194.2 504.5V334.2H141.4V256h52.8V222.3c0-87.1 39.4-127.5 125-127.5c16.2 0 44.2 3.2 55.7 6.4V172c-6-.6-16.5-1-29.6-1c-42 0-58.2 15.9-58.2 57.2V256h83.6l-14.4 78.2H287V510.1C413.8 494.8 512 386.9 512 256h0z" />
                            </svg></a>
                        <a href="" class="social"><svg class="register" xmlns="http://www.w3.org/2000/svg" height="25"
                                width="24.34375" viewBox="0 0 488 512">
                                <!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                <path fill="#e88617"
                                    d="M488 261.8C488 403.3 391.1 504 248 504 110.8 504 0 393.2 0 256S110.8 8 248 8c66.8 0 123 24.5 166.3 64.9l-67.5 64.9C258.5 52.6 94.3 116.6 94.3 256c0 86.5 69.1 156.6 153.7 156.6 98.2 0 135-70.4 140.8-106.9H248v-85.3h236.1c2.3 12.7 3.9 24.9 3.9 41.4z" />
                            </svg></a>
                    </div>
                </form>
            </div>
            <div class="form-container login-container">
                <form action="dangnhap.php" method="POST">
                    <h1>Đăng nhập</h1>
                    <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>
                    <?php if (!empty($success)) echo "<p style='color: green;'>$success</p>"; ?>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <div class="content">
                        <div class="checkbox">
                            <input type="checkbox" name="checkbox" id="checkbox">
                            <label>Remember me</label>
                        </div>
                        <div class="pass-link">
                            <a href="">Quên mật khẩu</a>
                        </div>
                    </div>
                    <button type="submit" name="logins">Đăng nhập</button>
                    <span>Hoặc sử dụng tài khoản của bạn</span>
                    <div class="social-container">
                        <a href="" class="social"><svg class="login" xmlns="http://www.w3.org/2000/svg" height="25"
                                width="25" viewBox="0 0 512 512">
                                <!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                <path fill="#005eff"
                                    d="M512 256C512 114.6 397.4 0 256 0S0 114.6 0 256C0 376 82.7 476.8 194.2 504.5V334.2H141.4V256h52.8V222.3c0-87.1 39.4-127.5 125-127.5c16.2 0 44.2 3.2 55.7 6.4V172c-6-.6-16.5-1-29.6-1c-42 0-58.2 15.9-58.2 57.2V256h83.6l-14.4 78.2H287V510.1C413.8 494.8 512 386.9 512 256h0z" />
                            </svg></a>
                        <a href="" class="social"><svg class="login" xmlns="http://www.w3.org/2000/svg" height="25"
                                width="24.34375" viewBox="0 0 488 512">
                                <!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                <path fill="#e88617"
                                    d="M488 261.8C488 403.3 391.1 504 248 504 110.8 504 0 393.2 0 256S110.8 8 248 8c66.8 0 123 24.5 166.3 64.9l-67.5 64.9C258.5 52.6 94.3 116.6 94.3 256c0 86.5 69.1 156.6 153.7 156.6 98.2 0 135-70.4 140.8-106.9H248v-85.3h236.1c2.3 12.7 3.9 24.9 3.9 41.4z" />
                            </svg></a>

                    </div>
                </form>

            </div>
            <div class="overlay-container">
                <div class="overlay">
                    <div class="overlay-panel overlay-left">
                        <h1 class="title">Hello <br>Friends</h1>
                        <p>Nếu bạn đã có tài khoản, đăng nhập tại đây để quản lý công việc</p>
                        <button class="ghost" id="login">Đăng nhập
                            <svg class="login" xmlns="http://www.w3.org/2000/svg" height="14" width="12.25"
                                viewBox="0 0 448 512">
                                <!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                <path
                                    d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z" />
                            </svg>
                        </button>
                    </div>
                    <div class="overlay-panel overlay-right">
                        <h1 class="title">Quản lý<br>công việc cá nhân<br>ngay</h1>
                        <p>Nếu bạn chưa có tài khoản, hãy đăng kí</p>
                        <button class="ghost" id="register">Đăng kí
                            <svg class="register" xmlns="http://www.w3.org/2000/svg" height="14" width="12.25"
                                viewBox="0 0 448 512">
                                <!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                <path
                                    d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z" />
                            </svg> </button>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/dangnhap.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
<?php
require 'auth.php';
require 'config.php';

$messageText = ''; // Biến để lưu thông báo

// Lấy ID người dùng từ session
$user_id = $_SESSION['user_id'];
$taskTable = "tasks" . $user_id; // Tên bảng công việc của người dùng

// Kiểm tra nếu có id trong URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Lấy thông tin công việc từ bảng công việc của người dùng
    $sql = "SELECT * FROM $taskTable WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $task = $stmt->fetch();

    // Nếu không tìm thấy công việc
    if (!$task) {
        echo "<script>
            alert('Công việc không tồn tại!');
            window.location.href = 'index.php';
        </script>";
        exit;
    }
}

// Xử lý khi người dùng gửi form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $priority = $_POST['priority'];

    // Cập nhật dữ liệu công việc vào bảng công việc của người dùng
    $sql_update = "UPDATE $taskTable 
                   SET title = :title, description = :description, due_date = :due_date, priority = :priority 
                   WHERE id = :id";

    try {
        $stmt = $pdo->prepare($sql_update);
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':due_date' => $due_date,
            ':priority' => $priority,
            ':id' => $id
        ]);

        if ($stmt->rowCount() > 0) {
            $messageText = "Công việc đã được cập nhật thành công!";
        } else {
            $messageText = "Không có thay đổi nào được thực hiện.";
        }
    } catch (PDOException $e) {
        $messageText = "Có lỗi xảy ra: " . $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sửa Công Việc</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
        <link rel="stylesheet" href="css/sua_style.css">
    <style>
        /* CSS tùy chỉnh */
        body {
            background: linear-gradient(135deg, #ff9a9e, #fad0c4);
            /* Hiệu ứng nền gradient */
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .container {
            max-width: 600px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            /* Hiệu ứng bóng */
            overflow: hidden;
            animation: fadeIn 1s ease-in-out;
            /* Hiệu ứng mượt */
        }

        .card-header {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            /* Gradient cho header */
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 1.8rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-label {
            font-weight: bold;
            color: #333;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            border: 1px solid #ced4da;
            transition: all 0.3s ease-in-out;
            padding: 10px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #6a11cb;
            box-shadow: 0 0 10px rgba(106, 17, 203, 0.5);
        }

        .btn-primary {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            border: none;
            border-radius: 10px;
            padding: 12px 20px;
            font-size: 1rem;
            font-weight: bold;
            text-transform: uppercase;
            transition: all 0.3s ease-in-out;
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #2575fc, #6a11cb);
            transform: scale(1.05);
            /* Hiệu ứng phóng to nhẹ */
            box-shadow: 0 5px 15px rgba(106, 17, 203, 0.4);
        }

        .mb-3 {
            margin-bottom: 20px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                Sửa Công việc
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="title" class="form-label">Tiêu đề :</label>
                        <input type="text" name="title" id="title" required class="form-control"
                            value="<?= htmlspecialchars($task['title']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả công việc :</label>
                        <input type="text" name="description" id="description" required class="form-control"
                            value="<?= htmlspecialchars($task['description']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Hạn chót :</label>
                        <input type="date" name="due_date" id="due_date" required class="form-control"
                            value="<?= htmlspecialchars($task['due_date']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="priority" class="form-label">Ưu tiên:</label>
                        <select id="priority" name="priority" required class="form-select">
                            <option value="High" <?= $task['priority'] === 'High' ? 'selected' : '' ?>>Cao</option>
                            <option value="Medium" <?= $task['priority'] === 'Medium' ? 'selected' : '' ?>>Trung bình
                            </option>
                            <option value="Low" <?= $task['priority'] === 'Low' ? 'selected' : '' ?>>Thấp</option>
                        </select>
                    </div>
                    <button type="submit" name="update_task" class="btn btn-primary w-100">Lưu công việc</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        var messageText = "<?php echo $messageText; ?>"; // Lấy thông báo từ PHP
        if (messageText.trim() !== '') { // Kiểm tra nếu thông báo không rỗng
            Swal.fire({
                title: messageText.includes("lỗi") ? "Lỗi" : "Thành Công",
                text: messageText,
                icon: messageText.includes("lỗi") ? "error" : "success",
                confirmButtonText: "OK"
            }).then((result) => {
                if (result.isConfirmed && !messageText.includes("lỗi")) {
                    // Chuyển hướng về trang chủ nếu thành công
                    window.location.href = "index.php";
                }
            });
        }
    </script>
</body>

</html>
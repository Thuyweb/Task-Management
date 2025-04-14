<?php
require 'auth.php';
require 'config.php';

$messageText = ''; // Biến để lưu thông báo

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    // Lấy dữ liệu từ form
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $priority = $_POST['priority'];

    // Lấy tên bảng công việc cá nhân từ session
    if (!isset($_SESSION['task_table'])) {
        die("Không tìm thấy bảng công việc cá nhân. Vui lòng đăng nhập lại.");
    }
    $taskTable = $_SESSION['task_table'];

    // Câu lệnh SQL để thêm công việc vào bảng công việc cá nhân
    try {
        $sql = "INSERT INTO $taskTable (title, description, due_date, priority) 
                VALUES (:title, :description, :due_date, :priority)";
        $stmt = $pdo->prepare($sql);

        // Gán giá trị cho các tham số
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':due_date', $due_date);
        $stmt->bindParam(':priority', $priority);

        // Thực thi câu lệnh SQL
        if ($stmt->execute()) {
            $messageText = "Bạn đã thêm công việc thành công!"; // Gửi thông báo thành công
        } else {
            $messageText = "Có lỗi xảy ra khi thêm công việc."; // Gửi thông báo lỗi
        }
    } catch (PDOException $e) {
        $messageText = "Lỗi: " . $e->getMessage(); // Xử lý lỗi PDO
    }
}
?>

<!Doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gửi Email Nhắc Nhở</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
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
                Thêm công việc mới
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="title" class="form-label">Tiêu đề :</label>
                        <input type="text" name="title" id="title" required class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả công việc :</label>
                        <input type="text" name="description" id="description" required class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Hạn chót :</label>
                        <input type="date" name="due_date" id="due_date" required class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="priority" class="form-label">Ưu tiên:</label>
                        <select id="priority" name="priority" required class="form-select">
                            <option value="High">Cao</option>
                            <option value="Medium">Trung bình</option>
                            <option value="Low">Thấp</option>
                        </select>
                    </div>
                    <button type="submit" name="submitContact" class="btn btn-primary w-100">Lưu công việc</button>
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
    if (messageText !== '') {
        Swal.fire({
            title: messageText.includes("lỗi") ? "Error" : "Thành Công",
            text: messageText,
            icon: messageText.includes("lỗi") ? "error" : "success",
        }).then((result) => {
            if (result.isConfirmed && !messageText.includes("lỗi")) {
                window.location.href = "index.php"; // Chuyển hướng về trang chủ nếu thành công
            }
        });
    }
    </script>
</body>

</html>
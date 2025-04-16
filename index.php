<?php
require 'auth.php';
require 'config.php';

// Kiểm tra xem tên bảng công việc có tồn tại trong session không
if (!isset($_SESSION['task_table'])) {
    die("Không tìm thấy bảng công việc cá nhân. Vui lòng đăng nhập lại.");
}

// Lấy tên bảng công việc cá nhân từ session
$taskTable = $_SESSION['task_table'];

// Truy vấn dữ liệu từ bảng công việc cá nhân
try {
    $sql = "SELECT id, title, description, due_date, priority, completed, position FROM $taskTable ORDER BY position ASC";
    $stmt = $pdo->query($sql);
    $tasks = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Lỗi truy vấn SQL: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Công việc</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
</head>

<body>
    <!-- Phần header -->
    <header class="main-header">
        <div class="header-left">
            <h2>Xin chào, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h2>
        </div>
        <div class="header-right">
            <a href="logout.php" class="logout-button">Đăng xuất</a>
        </div>
    </header>

    <div class="title-container">
        <h1 class="task-list-title">Danh sách Công việc của <?= htmlspecialchars($_SESSION['user_name']) ?></h1>
    </div>

    <div style="text-align: center; margin-bottom: 10px;">
        <!-- Nút Thêm Công việc -->
        <a href="add_task.php" style="
            background-color: rgb(0, 255, 55);
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        ">+ Thêm Công việc</a>

        <!-- Nút Thông báo với icon chuông -->
        <a href="sendgmail.php" style="
            background-color:rgb(0, 255, 55);
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin-left: 10px; /* Khoảng cách giữa 2 nút */
        ">
            <i class="fas fa-bell"></i> Bật thông báo
        </a>
    </div>

    <table id="task-table">
        <thead>
            <tr>
                <th>Tiêu đề</th>
                <th>Mô tả</th>
                <th>Hạn chót</th>
                <th>Ưu tiên</th>
                <th>Hoàn thành</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody id="task-list">
            <?php if (empty($tasks)): ?>
                <tr>
                    <td colspan="6" style="text-align: center;">Không có công việc nào!</td>
                </tr>
            <?php else: ?>
                <?php foreach ($tasks as $row): ?>
                    <tr data-deadline="<?= $row['due_date'] ?>" id="row_<?= $row['id'] ?>" data-id="<?= $row['id'] ?>"style=
                    "color:
                    <?php
                    $todayDate = date("Y-m-d");
                    if ($row['completed']) {
                    echo 'green';
                    } elseif ($row['due_date'] < $todayDate) {
                    echo 'red';
                    } else {
                    echo 'black';
                    }
                    ?>;">
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['description']) ?></td>
                        <td><?= htmlspecialchars($row['due_date']) ?></td>
                        <td><?= htmlspecialchars($row['priority']) ?></td>
                        <td>
                            <input type="checkbox" class="task-completed" data-id="<?= $row['id'] ?>"
                                <?= $row['completed'] ? 'checked' : '' ?>>
                        </td>
                        <td>
                            <a href="sua.php?id=<?= $row['id'] ?>">Sửa</a> |
                            <a href="xoa.php?id=<?= $row['id'] ?>"
                                onclick="return confirm('Bạn có chắc chắn muốn xóa công việc này không?')">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- JavaScript để xử lý kéo và thả -->
    <script>
        const taskList = document.getElementById('task-list');

        new Sortable(taskList, {
            animation: 150,
            onEnd(evt) {
                const order = [];
                const rows = taskList.querySelectorAll('tr');

                rows.forEach((row, index) => {
                    order.push({
                        id: row.getAttribute('data-id'),
                        position: index + 1
                    });
                });

                fetch('capnhatdrop.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            order
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Thứ tự đã được cập nhật thành công!');
                        } else {
                            console.error('Lỗi khi cập nhật thứ tự:', data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Lỗi:', error);
                    });
            }
        });
    </script>

    <script>
        document.querySelectorAll('.task-completed').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const id = this.getAttribute('data-id');
                const completed = this.checked ? 1 : 0;
                const row = document.getElementById('row_' + id);
                const deadline = row.getAttribute('data-deadline');
                const todayDate = new Date().toISOString().split('T')[0];
                if (completed === 1) {
                 row.style.color = 'green';
                } else if (deadline < todayDate) {
                row.style.color = 'red';
                } else {
                 row.style.color = 'black';
                }

                fetch('check_box.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            id,
                            completed
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Trạng thái hoàn thành đã được cập nhật!');
                        } else {
                            console.error('Lỗi khi cập nhật trạng thái:', data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Lỗi:', error);
                    });
            });
        });
    </script>
</body>

</html>
<style>
    /* Định dạng chung cho form */
    form {
        background-color: #f9f9f9;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
        max-width: 500px;
        margin: 20px auto;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        font-family: Arial, sans-serif;
    }

    /* Định dạng cho các nhãn (label) */
    form label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
        color: #333;
    }

    /* Định dạng cho các ô nhập liệu (input, textarea, select) */
    form input[type="text"],
    form input[type="date"],
    form textarea,
    form select {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 14px;
        box-sizing: border-box;
    }

    /* Định dạng cho textarea để đồng bộ với các ô input */
    form textarea {
        resize: vertical;
        min-height: 100px;
    }

    /* Định dạng cho nút bấm */
    form button {
        background-color: #4CAF50;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        transition: background-color 0.3s ease;
        width: 100%;
    }

    form button:hover {
        background-color: #45a049;
    }

    /* Định dạng tiêu đề danh sách công việc */
    .task-list-title {
        text-align: center;
        /* Căn giữa tiêu đề */
        margin: 20px auto;
        /* Tạo khoảng cách trên dưới và căn giữa */
        font-size: 20px;
        /* Kích thước chữ nhỏ hơn */
        font-weight: bold;
        /* Chữ đậm */
        color: #333;
        /* Màu chữ tối */
        text-transform: capitalize;
        /* Chữ cái đầu in hoa */
        letter-spacing: 0.5px;
        /* Khoảng cách giữa các chữ */
        padding: 8px 0;
        /* Khoảng cách trên dưới */
        display: inline-block;
        /* Để căn giữa đường gạch chân */
        animation: fadeIn 1s ease-in-out;
        /* Hiệu ứng xuất hiện */
        width: auto;
        /* Đảm bảo không chiếm toàn bộ chiều ngang */
    }

    /* Hiệu ứng xuất hiện */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
            /* Trượt từ trên xuống */
        }

        to {
            opacity: 1;
            transform: translateY(0);
            /* Về vị trí ban đầu */
        }
    }

    /* Định dạng header chính */
    .main-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        background-color: #f5f5f5;
        /* Màu nền xám nhạt */
        color: #333;
        /* Màu chữ tối */
        border: 1px solid #ddd;
        /* Đường viền nhẹ */
        border-radius: 8px;
        /* Bo góc header */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        /* Hiệu ứng đổ bóng nhẹ */
        margin-bottom: 20px;
        font-family: 'Arial', sans-serif;
    }

    /* Định dạng phần bên trái của header */
    .header-left h2 {
        margin: 0;
        font-size: 20px;
        font-weight: bold;
        color: #333;
        /* Màu chữ tối */
    }

    /* Định dạng phần bên phải của header */
    .header-right {
        display: flex;
        align-items: center;
    }

    /* Định dạng nút đăng xuất */
    .logout-button {
        background-color: #e74c3c;
        /* Màu đỏ tinh tế */
        color: white;
        padding: 8px 15px;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        font-size: 14px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        /* Hiệu ứng đổ bóng nhẹ */
    }

    /* Hiệu ứng hover cho nút đăng xuất */
    .logout-button:hover {
        background-color: #c0392b;
        /* Màu đỏ đậm hơn khi hover */
        transform: scale(1.05);
        /* Phóng to nhẹ khi hover */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        /* Đổ bóng mạnh hơn khi hover */
    }

    /* Hiệu ứng xuất hiện cho header */
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
            /* Trượt từ trên xuống */
        }

        to {
            opacity: 1;
            transform: translateY(0);
            /* Về vị trí ban đầu */
        }
    }

    /* Áp dụng hiệu ứng cho header */
    .main-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        background-color: #f5f5f5;
        /* Màu nền xám nhạt */
        color: #333;
        /* Màu chữ tối */
        border: 1px solid #ddd;
        /* Đường viền nhẹ */
        border-radius: 8px;
        /* Bo góc header */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        /* Hiệu ứng đổ bóng nhẹ */
        margin-bottom: 20px;
        font-family: 'Arial', sans-serif;
        animation: slideIn 0.8s ease-in-out;
        /* Thêm hiệu ứng xuất hiện */
    }

    .title-container {
        text-align: center;
        margin-bottom: 20px;
    }
</style>
<?php
session_start();

if (!isset($_SESSION["data"])) {
    header("Location: index.php");
    exit();
}

$data = $_SESSION["data"];
$showResult = isset($_POST['show_result']) ? true : false;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kết Quả Nộp Phiếu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef2f3;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .form-container {
            background: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 400px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .header {
            background: #f5a623;
            color: #fff;
            padding: 10px;
            text-align: center;
            margin: -20px -20px 20px -20px;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }
        h2 {
            text-align: center;
            color: #333;
            margin: 0 0 20px 0;
        }
        .result-item {
            margin-bottom: 15px;
            display: none;
        }
        .result-item label {
            font-weight: bold;
            color: #555;
            display: block;
            margin-bottom: 5px;
        }
        .result-item p {
            margin: 0;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background: #f9f9f9;
            color: #333;
        }
        .file-upload-result {
            border: 2px dashed #ccc;
            padding: 15px;
            text-align: center;
            color: #888;
            margin-bottom: 15px;
            display: none;
        }
        .button {
            background: #333;
            color: white;
            padding: 10px;
            border: none;
            width: 100%;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 10px;
        }
        .button:hover {
            background: #555;
        }
        .back-button {
            background: #333;
            color: white;
            padding: 10px;
            border: none;
            width: 100%;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }
        .back-button:hover {
            background: #555;
        }
        .instructions {
            font-size: 12px;
            color: #777;
            margin-top: 10px;
        }
        .show {
            display: block;
        }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Kết Quả Nộp Phiếu</h2>

    <form method="post">
        <input type="submit" name="show_result" value="Hiển Thị Kết Quả" class="button">
    </form>

    <?php if ($showResult): ?>
        <div class="result-item show">
            <label>Tên:</label>
            <p><?= htmlspecialchars($data['first_name'] . ' ' . $data['last_name']) ?></p>
        </div>

        <div class="result-item show">
            <label>Email:</label>
            <p><?= htmlspecialchars($data['email']) ?></p>
        </div>

        <div class="result-item show">
            <label>Mã Hóa Đơn:</label>
            <p><?= htmlspecialchars($data['invoice_id']) ?></p>
        </div>

        <div class="result-item show">
            <label>Thanh Toán Cho:</label>
            <p><?= implode(", ", array_map('htmlspecialchars', $data['pay_for'])) ?: 'Không có' ?></p>
        </div>

        <div class="file-upload-result show">
            <label>Chứng Từ Thanh Toán:</label>
            <p><?= htmlspecialchars(basename($data['receipt'])) ?: 'Chưa tải lên' ?></p>
        </div>

        <div class="result-item show">
            <label>Thông Tin Thêm:</label>
            <p><?= htmlspecialchars($data['additional']) ?: 'Không có' ?></p>
        </div>
    <?php endif; ?>

    <a href="index.php" class="back-button">Quay Lại</a>
    <div class="instructions">
        Khi nhận được thông báo, vui lòng validate form, sau khi nhận được thông báo đã thông tin sẽ được submit, kiểm tra thông tin trong session và lưu thông tin vào database.
        Sử dụng session và cookies để lưu thông tin do nguời dùng nhập vào trên form , sau đó hiển thị lại.
    </div>
</div>
</body>
</html>
<?php
// Không hủy session ngay, chỉ hủy khi nhấn Quay Lại và quay về index.php
?>
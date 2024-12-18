<?php
session_start();

// // Kiểm tra xem người dùng đã đăng nhập chưa
// if (!isset($_SESSION['MaNguoiDung'])) {
//     header('Location: login.php');
//     exit();
// }

// Kết nối cơ sở dữ liệu
include '../../database/db.php';

// Khởi tạo biến cho các trường
$maLoaiXe = '';
$tenLoaiXe = '';
$sucChua = '';
$errorMessage = '';
$successMessage = '';

// Kiểm tra xem có mã câu hỏi để chỉnh sửa không
if (isset($_GET['id'])) {
    $maLoaiXe = $_GET['id'];

    // Lấy thông tin bến xe từ cơ sở dữ liệu
    $stmt = $conn->prepare("SELECT MaLoaiXe, TenLoaiXe, SucChua FROM loaixe WHERE MaLoaiXe = ?");
    $stmt->bind_param("i", $maLoaiXe);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $loaiXe = $result->fetch_assoc();
        $tenLoaiXe = $loaiXe['TenLoaiXe'];
        $sucChua = $loaiXe['SucChua'];
    } else {
        $errorMessage = 'Không tìm thấy loại xe!';
    }
}

// Xử lý thêm hoặc cập nhật câu hỏi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tenLoaiXe = $_POST['tenLoaiXe'];
    $sucChua = $_POST['sucChua'];

    if (empty($tenLoaiXe) || empty($sucChua)) {
        $errorMessage = 'Vui lòng điền tất cả các trường.';
    } else {
        if ($maLoaiXe) {
            // Cập nhật câu hỏi
            $stmt = $conn->prepare("UPDATE loaixe SET TenLoaiXe = ?, SucChua = ? WHERE MaLoaiXe = ?");
            if ($stmt->execute([$tenLoaiXe, $sucChua, $maCLoaiXe])) {
                $successMessage = 'Cập nhật loại xe thành công!';
            } else {
                $errorMessage = 'Có lỗi xảy ra khi cập nhật loại xe!';
            }
        } else {
            // Thêm câu hỏi mới
            $stmt = $conn->prepare("INSERT INTO loaixe (TenLoaiXe, SucChua) VALUES (?, ?)");
            if ($stmt->execute([$tenLoaiXe, $sucChua])) {
                $successMessage = 'Thêm loại xe thành công!';
            } else {
                $errorMessage = 'Có lỗi xảy ra khi thêm loại xe!';
            }
        }
    }
}
?>

<!doctype html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template-free" data-style="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Quản lý loại xe</title>
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="../assets/vendor/fonts/remixicon/remixicon.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/node-waves/node-waves.css" />
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <script src="../assets/vendor/js/helpers.js"></script>
    <script src="../assets/js/config.js"></script>
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include 'sidebar.php'; ?>
            <div class="layout-page">
                <?php include 'navbar.php'; ?>
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="card">
                            <h5 class="card-header"><?php echo $maLoaiXe ? 'Chỉnh sửa loại xe' : 'Thêm loại xe'; ?></h5>
                            <div class="card-body">
                                <?php if ($errorMessage): ?>
                                <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                                <?php endif; ?>
                                <?php if ($successMessage): ?>
                                <div class="alert alert-success"><?php echo $successMessage; ?></div>
                                <?php endif; ?>
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="tenLoaiXe" class="form-label">Tên Loại Xe</label>
                                        <input type="text" class="form-control" id="tenLoaiXe" name="tenLoaiXe"
                                            value="<?php echo htmlspecialchars($tenLoaiXe); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="sucChua" class="form-label">Sức Chứa</label>
                                        <input type="text" class="form-control" id="sucChua" name="sucChua"
                                            value="<?php echo htmlspecialchars($sucChua); ?>" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary"><?php echo $maLoaiXe ? 'Cập nhật' : 'Thêm mới'; ?></button>
                                    <a href="typebus_manager.php" class="btn btn-secondary">Quay lại</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include 'footer.php'; ?>
            </div>
        </div>
    </div>
</body>

</html>

<?php
include 'db_connect.php';

// Biến lưu thông báo lỗi hoặc thành công
$message = '';

// Kiểm tra nếu id_chi_nhanh đã được truyền vào
if (isset($_GET['id_chi_nhanh'])) {
    $id_chi_nhanh = $_GET['id_chi_nhanh'];

    // Gọi stored procedure sp_xoa_chi_nhanh
    $sql = "EXEC sp_xoa_chi_nhanh @id_chi_nhanh = ?";
    $params = array($id_chi_nhanh);
    $stmt = sqlsrv_query($conn, $sql, $params);

    // Kiểm tra kết quả
    if ($stmt === false) {
        // Lấy thông báo lỗi
        $message = "Lỗi khi xóa chi nhánh: " . print_r(sqlsrv_errors(), true);
    } else {
        $message = "Chi nhánh đã được xóa thành công!";
    }

    // Giải phóng tài nguyên
    sqlsrv_free_stmt($stmt);
} else {
    $message = "Không có mã chi nhánh được cung cấp.";
}

// Đóng kết nối
sqlsrv_close($conn);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xóa Chi Nhánh</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Xóa Chi Nhánh</h1>
            <nav>
            <a href="index.php">Trang chủ</a>
            <a href="quanlychinhanh.php">Quản lý chi nhánh</a>
                <a href="quanlynhanvien.php">Quản lý nhân viên</a>
                <a href="quanlysanpham.php">Quản lý sản phẩm</a>
                <a href="quanlykhachhang.php">Quản lý khách hàng</a>
                <a href="quanlyhoahon.php">Quản lý hóa đơn</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="message">
            <p><?= htmlspecialchars($message) ?></p>
        </div>
    </main>
</body>
</html>

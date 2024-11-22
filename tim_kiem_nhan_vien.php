<?php
// Kết nối cơ sở dữ liệu
include 'db_connect.php';

// Khởi tạo biến $nhan_vien là null để tránh lỗi khi chưa thực hiện tìm kiếm
$nhan_vien = null;
$message = '';

// Lấy số điện thoại từ form tìm kiếm
$so_dien_thoai = isset($_GET['so_dien_thoai']) ? $_GET['so_dien_thoai'] : '';

// Nếu người dùng nhập số điện thoại để tìm kiếm
if ($so_dien_thoai) {
    // Gọi procedure tìm kiếm nhân viên
    $sql = "EXEC sp_tim_kiem_nhan_vien_theo_sdt ?";
    $params = array($so_dien_thoai);
    $stmt = sqlsrv_query($conn, $sql, $params);

    // Kiểm tra nếu có lỗi
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Lấy kết quả trả về
    $nhan_vien = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Nếu không tìm thấy nhân viên
    if (!$nhan_vien) {
        $message = "Không tìm thấy nhân viên với số điện thoại: $so_dien_thoai";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm kiếm nhân viên</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Tìm kiếm nhân viên</h1>
            <nav>
            <a href="index.php">Trang chủ</a>
            <a href="quanlychinhanh.php">Quản lý chi nhánh</a>
                <a href="quanlynhanvien.php">Quản lý nhân viên</a>
                <a href="quanlysanpham.php">Quản lý sản phẩm</a>
                <a href="quanlykhachhang.php">Quản lý khách hàng</a>
                <a href="quanlyhoadon.php">Quản lý hóa đơn</a>
            </nav>
        </div>
    </header>
    <main>
        <div class="form-container">
            <h2>Nhập số điện thoại để tìm kiếm</h2>
            <form action="tim_kiem_nhan_vien.php" method="GET">
                <input type="text" name="so_dien_thoai" placeholder="Nhập số điện thoại" required>
                <button type="submit">Tìm kiếm</button>
            </form>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php elseif ($nhan_vien): ?>
            <div class="detail-container">
                <h2>Thông tin chi tiết nhân viên</h2>
                <p><strong>ID:</strong> <?= $nhan_vien['id_nhan_vien'] ?></p>
                <p><strong>Tên:</strong> <?= $nhan_vien['ten_nhan_vien'] ?></p>
                <p><strong>Chức vụ:</strong> <?= $nhan_vien['chuc_vu'] ?></p>
                <p><strong>Chi nhánh:</strong> <?= $nhan_vien['id_chi_nhanh'] ?></p>
                <p><strong>Số điện thoại:</strong> <?= $nhan_vien['so_dien_thoai'] ?></p>
                <p><strong>Email:</strong> <?= $nhan_vien['email'] ?></p>
                <p><strong>Ngày bắt đầu làm:</strong> <?= $nhan_vien['ngay_bat_dau_lam']->format('Y-m-d') ?></p>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>

<?php
// Giải phóng tài nguyên
if (isset($stmt)) {
    sqlsrv_free_stmt($stmt);
}
sqlsrv_close($conn);
?>

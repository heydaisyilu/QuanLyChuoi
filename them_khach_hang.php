<?php
include 'db_connect.php';  // Kết nối cơ sở dữ liệu

// Xử lý khi form được submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $ten_khach_hang = $_POST['ten_khach_hang'];
    $so_dien_thoai = $_POST['so_dien_thoai'];

    // Kiểm tra định dạng số điện thoại (10 chữ số và bắt đầu bằng 0)
    if (!preg_match('/^0\d{9}$/', $so_dien_thoai)) {
        $message = "Số điện thoại không hợp lệ. Số điện thoại phải có 10 chữ số và bắt đầu bằng số 0.";
        $alert_class = "alert-error";
    } else {
        // Kiểm tra xem số điện thoại đã tồn tại trong cơ sở dữ liệu không
        $sql_check_so_dien_thoai = "SELECT COUNT(*) AS phone_count FROM KhachHang WHERE so_dien_thoai = ?";
        $stmt_check_so_dien_thoai = sqlsrv_query($conn, $sql_check_so_dien_thoai, array($so_dien_thoai));

        // Kiểm tra nếu câu truy vấn thực thi thành công
        if ($stmt_check_so_dien_thoai === false) {
            $errors = sqlsrv_errors();
            $message = "Lỗi khi kiểm tra số điện thoại: " . print_r($errors, true);
            $alert_class = "alert-error";
        } else {
            // Lấy kết quả từ câu truy vấn
            $row = sqlsrv_fetch_array($stmt_check_so_dien_thoai, SQLSRV_FETCH_ASSOC);

            // Kiểm tra nếu số điện thoại đã tồn tại
            if ($row['phone_count'] > 0) {
                $message = "Số điện thoại này đã tồn tại trong cơ sở dữ liệu!";
                $alert_class = "alert-error";
            } else {
                // Nếu không có trùng, thực thi stored procedure để thêm khách hàng
                $sql = "EXEC sp_them_khach_hang ?, ?";
                $params = [$ten_khach_hang, $so_dien_thoai];

                // Thực thi câu lệnh
                $stmt = sqlsrv_query($conn, $sql, $params);

                // Kiểm tra xem câu lệnh có thực thi thành công không
                if ($stmt === false) {
                    $errors = sqlsrv_errors();
                    $message = "Lỗi khi thêm khách hàng: " . print_r($errors, true);
                    $alert_class = "alert-error";
                } else {
                    $message = "Thêm khách hàng thành công!";
                    $alert_class = "alert-success";
                }
            }

            // Giải phóng bộ nhớ sau khi sử dụng
            sqlsrv_free_stmt($stmt_check_so_dien_thoai);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Khách Hàng</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Quản Lý Khách Hàng</h1>
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
            <h2>Thêm Khách Hàng Mới</h2>

            <?php if (!empty($message)): ?>
                <div class="alert <?= $alert_class ?>"><?= $message ?></div>
            <?php endif; ?>

            <!-- Form thêm khách hàng -->
            <form action="them_khach_hang.php" method="POST">
                <div class="form-group">
                    <label for="ten_khach_hang">Tên Khách Hàng:</label>
                    <input type="text" id="ten_khach_hang" name="ten_khach_hang" required>
                </div>

                <div class="form-group">
                    <label for="so_dien_thoai">Số Điện Thoại:</label>
                    <input type="text" id="so_dien_thoai" name="so_dien_thoai" required>
                </div>

                <div class="form-group">
                    <input type="submit" value="Thêm Khách Hàng">
                </div>
            </form>
        </div>
    </main>
</body>
</html>

<?php
// Giải phóng bộ nhớ và đóng kết nối
sqlsrv_close($conn);
?>

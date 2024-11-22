<?php
include 'db_connect.php'; // Kết nối cơ sở dữ liệu

// Lấy danh sách chi nhánh
$sql = "SELECT * FROM ChiNhanh";
$stmt_chi_nhanh = sqlsrv_query($conn, $sql);

// Xử lý khi form được submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $so_dien_thoai = $_POST['so_dien_thoai'];
    $ten_khach_hang = $_POST['ten_khach_hang'];
    $so_luong = $_POST['so_luong'];
    $ngay_dat_hang = $_POST['ngay_dat_hang'];
    $trang_thai = $_POST['trang_thai'];
    $id_chi_nhanh = $_POST['id_chi_nhanh'];
    $ten_san_pham = $_POST['ten_san_pham'];

    // Kiểm tra và chuyển đổi giá trị ngày giờ
    $dateTime = DateTime::createFromFormat('Y-m-d\TH:i', $ngay_dat_hang);
    if (!$dateTime) {
        die('Ngày giờ không hợp lệ.');
    }

    // Chuyển sang định dạng SQL Server datetime (yyyy-MM-dd hh:mm:ss)
    $ngay_dat_hang_sql = $dateTime->format('Y-m-d H:i:s');

    // Lấy thông tin sản phẩm từ cơ sở dữ liệu cho chi nhánh đã chọn
    $sql_san_pham = "SELECT * FROM SanPham WHERE ten_san_pham = ? AND id_chi_nhanh = ?";
    $stmt_san_pham = sqlsrv_query($conn, $sql_san_pham, array($ten_san_pham, $id_chi_nhanh));

    if ($stmt_san_pham === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $san_pham = sqlsrv_fetch_array($stmt_san_pham, SQLSRV_FETCH_ASSOC);
    if (!$san_pham) {
        die('Sản phẩm không tồn tại trong chi nhánh này.');
    }

    // Lấy ID sản phẩm từ cơ sở dữ liệu
    $id_san_pham = $san_pham['id_san_pham'];
    $gia = $san_pham['gia'];

    // Tính tổng tiền hóa đơn
    $tong_tien = $gia * $so_luong;
    

    // Kiểm tra số lượng tồn kho có đủ hay không
    if ($san_pham['so_luong_ton_kho'] < $so_luong) {
        die('Số lượng tồn kho không đủ.');
    }

    // Giảm số lượng tồn kho sau khi đặt hàng
    $new_so_luong_ton_kho = $san_pham['so_luong_ton_kho'] - $so_luong;
    $sql_update_ton_kho = "UPDATE SanPham SET so_luong_ton_kho = ? WHERE id_san_pham = ?";
    $stmt_update_ton_kho = sqlsrv_query($conn, $sql_update_ton_kho, array($new_so_luong_ton_kho, $san_pham['id_san_pham']));

    if ($stmt_update_ton_kho === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Xử lý thông tin khách hàng
    $sql_khach_hang = "SELECT * FROM KhachHang WHERE so_dien_thoai = ?";
    $stmt_khach_hang = sqlsrv_query($conn, $sql_khach_hang, array($so_dien_thoai));

    if ($stmt_khach_hang === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $khach_hang = sqlsrv_fetch_array($stmt_khach_hang, SQLSRV_FETCH_ASSOC);
    if (!$khach_hang) {
        // Khách hàng không tồn tại, gọi stored procedure để thêm khách hàng mới
        $sql_sp_them_khach_hang = "EXEC sp_them_khach_hang ?, ?";
        $stmt_sp_them_khach_hang = sqlsrv_query($conn, $sql_sp_them_khach_hang, array($ten_khach_hang, $so_dien_thoai));

        if ($stmt_sp_them_khach_hang === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        // Lấy lại thông tin khách hàng sau khi thêm vào
        $stmt_khach_hang = sqlsrv_query($conn, $sql_khach_hang, array($so_dien_thoai));
        $khach_hang = sqlsrv_fetch_array($stmt_khach_hang, SQLSRV_FETCH_ASSOC);
    }

    $id_khach_hang = $khach_hang['id_khach_hang']; // Lấy id khách hàng

    // Thêm hóa đơn vào bảng HoaDon
    $sql_hoa_don = "INSERT INTO HoaDon (id_khach_hang, id_chi_nhanh, ngay_dat_hang, trang_thai, id_san_pham, so_luong, gia, tong_tien) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $params_hoa_don = array($id_khach_hang, $id_chi_nhanh, $ngay_dat_hang_sql, $trang_thai, $id_san_pham, $so_luong, $gia, $tong_tien);
    $stmt_hoa_don = sqlsrv_query($conn, $sql_hoa_don, $params_hoa_don);

    if ($stmt_hoa_don === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $message = "Thêm hóa đơn thành công! Tổng tiền: " . number_format($tong_tien, 0, ',', '.') . " VNĐ.";
    $alert_class = "alert-success";
} else {
    $stmt_san_pham = null;
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Hóa Đơn</title>
    <link rel="stylesheet" href="style.css">
    <script>
        // Hàm lấy sản phẩm từ chi nhánh đã chọn
        function hienthisanpham() {
            var idChiNhanh = document.getElementById('id_chi_nhanh').value;
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "hienthisanpham.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function() {
                if (xhr.status == 200) {
                    document.getElementById('ten_san_pham').innerHTML = xhr.responseText;
                }
            };
            xhr.send("id_chi_nhanh=" + idChiNhanh);
        }
    </script>
</head>
<body>
    <header>
        <div class="container">
            <h1>Quản Lý Hóa Đơn</h1>
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
            <h2>Thêm Hóa Đơn Mới</h2>
            <?php if (!empty($message)): ?>
                <div class="alert <?= $alert_class ?>"><?= $message ?></div>
            <?php endif; ?>
            <form action="them_hoa_don.php" method="POST">
                <div class="form-group">
                    <label for="so_dien_thoai">Số Điện Thoại Khách Hàng:</label>
                    <input type="text" id="so_dien_thoai" name="so_dien_thoai" required>
                </div>

                <div class="form-group">
                    <label for="ten_khach_hang">Tên Khách Hàng:</label>
                    <input type="text" id="ten_khach_hang" name="ten_khach_hang" required>
                </div>

                <div class="form-group">
                    <label for="id_chi_nhanh">Chi Nhánh:</label>
                    <select name="id_chi_nhanh" id="id_chi_nhanh" onchange="hienthisanpham()" required>
                        <option value="">Chọn chi nhánh</option>
                        <?php while ($row = sqlsrv_fetch_array($stmt_chi_nhanh, SQLSRV_FETCH_ASSOC)): ?>
                            <option value="<?= $row['id_chi_nhanh'] ?>"><?= $row['ten_chi_nhanh'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="ten_san_pham">Sản Phẩm:</label>
                    <select name="ten_san_pham" id="ten_san_pham" required>
                        <option value="">Chọn sản phẩm</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="so_luong">Số Lượng:</label>
                    <input type="number" id="so_luong" name="so_luong" min="1" required>
                </div>

                <div class="form-group">
                    <label for="ngay_dat_hang">Ngày Đặt Hàng:</label>
                    <input type="datetime-local" id="ngay_dat_hang" name="ngay_dat_hang" required>
                </div>

                <div class="form-group">
                    <label for="trang_thai">Trạng Thái:</label>
                    <select name="trang_thai" required>
                        <option value="Đang xử lý">Đang xử lý</option>
                        <option value="Đã hoàn thành">Đã hoàn thành</option>
                        <option value="Đã giao">Đã giao</option>
                    </select>
                </div>

                <div class="form-group">
                    <input type="submit" value="Thêm Hóa Đơn">
                </div>
            </form>
        </div>
    </main>
</body>
</html>

<?php
include 'db_connect.php';  // Kết nối cơ sở dữ liệu

// Truy vấn lấy tất cả hóa đơn
$sql = "SELECT id_hoa_don, id_khach_hang, id_chi_nhanh, ngay_dat_hang, trang_thai, tong_tien FROM HoaDon";
$stmt = sqlsrv_query($conn, $sql);

// Kiểm tra nếu truy vấn thất bại
if ($stmt === false) {
    die("Lỗi truy vấn: " . print_r(sqlsrv_errors(), true));
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Hóa Đơn</title>
    <link rel="stylesheet" href="style.css">
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
                <a href="them_hoa_don.php">Thêm hóa đơn mới</a> <!-- Liên kết tới trang thêm hóa đơn -->
            </nav>
        </div>
    </header>
    <main>
        <div class="table-container">
            <h2>Danh sách hóa đơn</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Hóa Đơn</th>
                        <th>ID Khách Hàng</th>
                        <th>ID Chi Nhánh</th>
                        <th>Ngày Đặt Hàng</th>
                        <th>Trạng Thái</th>
                        <th>Tổng Tiền</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= $row['id_hoa_don'] ?></td>
                            <td><?= $row['id_khach_hang'] ?></td>
                            <td><?= $row['id_chi_nhanh'] ?></td>
                            <td><?= $row['ngay_dat_hang']->format('Y-m-d H:i:s') ?></td>
                            <td><?= $row['trang_thai'] ?></td>
                            <td><?= number_format($row['tong_tien'])?> VNĐ</td>
                            <td>
                                <a href="xoa_hoa_don.php?id_hoa_don=<?= $row['id_hoa_don'] ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa hóa đơn này?')">Xóa</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>

<?php
// Giải phóng bộ nhớ và đóng kết nối
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>

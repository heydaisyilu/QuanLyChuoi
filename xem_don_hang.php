<?php
include 'db_connect.php';  // Kết nối cơ sở dữ liệu

// Lấy ID khách hàng từ URL
$id_khach_hang = isset($_GET['id_khach_hang']) ? $_GET['id_khach_hang'] : '';

if ($id_khach_hang) {
    // Truy vấn lấy các hóa đơn của khách hàng
    $sql = "SELECT id_hoa_don, ngay_dat_hang, trang_thai, tong_tien 
            FROM HoaDon 
            WHERE id_khach_hang = ?";
    $stmt = sqlsrv_query($conn, $sql, array($id_khach_hang));
} else {
    die("Không tìm thấy ID khách hàng!");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Đơn Hàng</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Danh Sách Đơn Hàng</h1>
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
        <div class="table-container">
            <h2>Danh sách đơn hàng của khách hàng ID<?= htmlspecialchars($id_khach_hang) ?></h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Hóa Đơn</th>
                        <th>Ngày Đặt Hàng</th>
                        <th>Trạng Thái</th>
                        <th>Tổng Tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= $row['id_hoa_don'] ?></td>
                            <td><?= $row['ngay_dat_hang']->format('Y-m-d H:i:s') ?></td>
                            <td><?= $row['trang_thai'] ?></td>
                            <td><?= number_format($row['tong_tien'])?>VNĐ</td>
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

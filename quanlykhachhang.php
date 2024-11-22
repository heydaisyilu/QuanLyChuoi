<?php
include 'db_connect.php';  // Kết nối cơ sở dữ liệu

// Kiểm tra xem có tìm kiếm hay không
$so_dien_thoai = isset($_GET['so_dien_thoai']) ? $_GET['so_dien_thoai'] : '';

// Truy vấn tìm kiếm khách hàng nếu có số điện thoại
if ($so_dien_thoai) {
    $sql = "EXEC sp_tim_kiem_khach_hang @so_dien_thoai = ?";
    $stmt = sqlsrv_query($conn, $sql, array($so_dien_thoai));
} else {
    // Truy vấn lấy tất cả khách hàng nếu không tìm kiếm
    $sql = "SELECT id_khach_hang, ten_khach_hang, so_dien_thoai FROM KhachHang";
    $stmt = sqlsrv_query($conn, $sql);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Khách Hàng</title>
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
                <a href="quanlyhoadon.php">Quản lý hóa đơn</a>
                <a href="them_khach_hang.php">Thêm khách hàng mới</a>
            </nav>
        </div>
    </header>
    <main>
        <!-- Form tìm kiếm -->
        <div class="search-form">
            <form action="quanlykhachhang.php" method="GET">
                <input type="text" name="so_dien_thoai" placeholder="Tìm kiếm theo số điện thoại" value="<?= htmlspecialchars($so_dien_thoai) ?>">
                <button type="submit">Tìm kiếm</button>
            </form>
        </div>

        <div class="table-container">
            <h2>Danh sách khách hàng</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên khách hàng</th>
                        <th>Số điện thoại</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= $row['id_khach_hang'] ?></td>
                            <td><?= $row['ten_khach_hang'] ?></td>
                            <td><?= $row['so_dien_thoai'] ?></td>
                            <td>
                                <a href="sua_khach_hang.php?id_khach_hang=<?= $row['id_khach_hang'] ?>">Sửa</a> |
                                <a href="xoa_khach_hang.php?id_khach_hang=<?= $row['id_khach_hang'] ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa khách hàng này?')">Xóa</a> |
                                <a href="xem_don_hang.php?id_khach_hang=<?= $row['id_khach_hang'] ?>">Xem đơn hàng</a>
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

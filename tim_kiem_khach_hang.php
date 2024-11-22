<?php
include 'db_connect.php';  // Kết nối cơ sở dữ liệu

// Lấy giá trị tìm kiếm từ GET request
$so_dien_thoai = isset($_GET['so_dien_thoai']) ? $_GET['so_dien_thoai'] : '';

// Nếu có số điện thoại, gọi procedure
if ($so_dien_thoai) {
    // Gọi stored procedure
    $sql = "EXEC sp_tim_kiem_khach_hang @so_dien_thoai = ?";
    $stmt = sqlsrv_query($conn, $sql, array($so_dien_thoai));

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm Kiếm Khách Hàng</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Tìm Kiếm Khách Hàng</h1>
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
        <!-- Form tìm kiếm -->
        <div class="search-form">
            <form action="tim_kiem_khach_hang.php" method="GET">
                <input type="text" name="so_dien_thoai" placeholder="Nhập số điện thoại để tìm kiếm" value="<?= htmlspecialchars($so_dien_thoai) ?>">
                <button type="submit">Tìm kiếm</button>
            </form>
        </div>

        <div class="table-container">
            <h2>Kết quả tìm kiếm</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên khách hàng</th>
                        <th>Số điện thoại</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($stmt)): ?>
                        <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
                            <tr>
                                <td><?= $row['id_khach_hang'] ?></td>
                                <td><?= $row['ten_khach_hang'] ?></td>
                                <td><?= $row['so_dien_thoai'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>

<?php
// Giải phóng bộ nhớ và đóng kết nối
if (isset($stmt)) {
    sqlsrv_free_stmt($stmt);
}
sqlsrv_close($conn);
?>

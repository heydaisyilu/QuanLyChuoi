<?php
include 'db_connect.php';

// Biến lưu thông báo lỗi hoặc thành công
$message = '';

// Khởi tạo biến tìm kiếm
$ten_san_pham = isset($_GET['ten_san_pham']) ? $_GET['ten_san_pham'] : '';
$id_chi_nhanh = isset($_GET['id_chi_nhanh']) ? $_GET['id_chi_nhanh'] : '';

// Truy vấn cơ sở dữ liệu sử dụng stored procedure
$sql = "EXEC sp_tim_kiem_san_pham ?, ?";
$params = array($ten_san_pham, $id_chi_nhanh);
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    $message = "Lỗi khi tìm kiếm sản phẩm: " . print_r(sqlsrv_errors(), true);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm kiếm sản phẩm</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Tìm kiếm sản phẩm</h1>
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
        <div class="search-form">
            <form action="tim_kiem_san_pham.php" method="GET">
                <input type="text" name="ten_san_pham" placeholder="Nhập tên sản phẩm" value="<?= htmlspecialchars($ten_san_pham) ?>">
                <select name="id_chi_nhanh">
                    <option value="">Chọn chi nhánh</option>
                    <?php
                    // Lấy danh sách chi nhánh
                    $sql_chi_nhanh = "SELECT id_chi_nhanh, ten_chi_nhanh FROM ChiNhanh";
                    $stmt_chi_nhanh = sqlsrv_query($conn, $sql_chi_nhanh);
                    while ($row_chi_nhanh = sqlsrv_fetch_array($stmt_chi_nhanh, SQLSRV_FETCH_ASSOC)) {
                        $selected = ($row_chi_nhanh['id_chi_nhanh'] == $id_chi_nhanh) ? 'selected' : '';
                        echo "<option value='{$row_chi_nhanh['id_chi_nhanh']}' $selected>{$row_chi_nhanh['ten_chi_nhanh']}</option>";
                    }
                    ?>
                </select>
                <button type="submit">Tìm kiếm</button>
            </form>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-error"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="table-container">
            <h2>Danh sách sản phẩm</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Sản phẩm</th>
                        <th>Tên sản phẩm</th>
                        <th>Mô tả</th>
                        <th>Giá</th>
                        <th>Số lượng tồn kho</th>
                        <th>Chi nhánh</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($stmt): ?>
                        <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
                            <tr>
                                <td><?= $row['id_san_pham'] ?></td>
                                <td><?= $row['ten_san_pham'] ?></td>
                                <td><?= $row['mo_ta'] ?></td>
                                <td><?= number_format($row['gia'], 2) ?></td>
                                <td><?= $row['so_luong_ton_kho'] ?></td>
                                <td><?= $row['ten_chi_nhanh'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6">Không có sản phẩm nào được tìm thấy.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>

<?php
// Giải phóng tài nguyên
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>

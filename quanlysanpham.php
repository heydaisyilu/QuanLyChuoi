<?php
include 'db_connect.php';

// Khởi tạo biến tìm kiếm
$ten_san_pham = isset($_GET['ten_san_pham']) ? $_GET['ten_san_pham'] : '';
$id_chi_nhanh = isset($_GET['id_chi_nhanh']) ? $_GET['id_chi_nhanh'] : '';

// Câu lệnh SQL để lọc sản phẩm theo tên và chi nhánh, kết nối với bảng ChiNhanh
$sql = "SELECT SanPham.*, ChiNhanh.ten_chi_nhanh 
        FROM SanPham 
        LEFT JOIN ChiNhanh ON SanPham.id_chi_nhanh = ChiNhanh.id_chi_nhanh
        WHERE SanPham.ten_san_pham LIKE ? 
        AND (SanPham.id_chi_nhanh LIKE ? OR ? = '')";
$params = array("%$ten_san_pham%", "%$id_chi_nhanh%", $id_chi_nhanh);
$stmt = sqlsrv_query($conn, $sql, $params);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Sản Phẩm</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Quản Lý Sản Phẩm</h1>
            <nav>
                <a href="index.php">Trang chủ</a>
                <a href="quanlychinhanh.php">Quản lý chi nhánh</a>
                <a href="quanlynhanvien.php">Quản lý nhân viên</a>
                <a href="quanlysanpham.php">Quản lý sản phẩm</a>
                <a href="quanlykhachhang.php">Quản lý khách hàng</a>
                <a href="quanlyhoadon.php">Quản lý hóa đơn</a>
                <a href="them_san_pham.php">Thêm Sản Phẩm</a>
            </nav>
        </div>
    </header>

    <main>
        <!-- Form tìm kiếm sản phẩm -->
        <div class="search-form">
            <form action="quanlysanpham.php" method="GET">
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

        <!-- Bảng hiển thị sản phẩm -->
        <div class="table-container">
            <h2>Danh Sách Sản Phẩm</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên Sản Phẩm</th>
                        <th>Mô Tả</th>
                        <th>Giá</th>
                        <th>Số Lượng</th>
                        <th>Chi Nhánh</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= $row['id_san_pham'] ?></td>
                            <td><?= $row['ten_san_pham'] ?></td>
                            <td><?= $row['mo_ta'] ?></td>
                            <td><?= number_format($row['gia']) ?> VNĐ</td>
                            <td><?= $row['so_luong_ton_kho'] ?></td>
                            <td><?= $row['ten_chi_nhanh'] ?></td>
                            <td>
                                <a href="sua_san_pham.php?id=<?= $row['id_san_pham'] ?>">Sửa</a>
                                <a href="xoa_san_pham.php?id=<?= $row['id_san_pham'] ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">Xóa</a>
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
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>

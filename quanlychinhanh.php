<?php
// Kết nối cơ sở dữ liệu
include 'db_connect.php';

// Biến thông báo
$message = '';

// Xử lý khi người dùng muốn xóa chi nhánh
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $id_chi_nhanh = $_POST['id_chi_nhanh'];
    $id_chi_nhanh_moi = $_POST['id_chi_nhanh_moi'];

    // Kiểm tra nếu chi nhánh có sản phẩm
    $sql_check_san_pham = "SELECT COUNT(*) AS product_count FROM SanPham WHERE id_chi_nhanh = ?";
    $stmt_check_san_pham = sqlsrv_query($conn, $sql_check_san_pham, array($id_chi_nhanh));
    $row_check = sqlsrv_fetch_array($stmt_check_san_pham, SQLSRV_FETCH_ASSOC);

    if ($row_check['product_count'] > 0) {
        $message = "Chi nhánh này vẫn còn sản phẩm. Không thể xóa!";
    } else {
        // Chuyển nhân viên sang chi nhánh mới
        $sql_chuyen_nhan_vien = "EXEC sp_chuyen_nhan_vien_sang_chi_nhanh ?, ?";
        $params_chuyen_nhan_vien = array($id_chi_nhanh, $id_chi_nhanh_moi);
        $stmt_chuyen_nhan_vien = sqlsrv_query($conn, $sql_chuyen_nhan_vien, $params_chuyen_nhan_vien);
        
        // Kiểm tra nếu có lỗi khi chuyển nhân viên
        if ($stmt_chuyen_nhan_vien === false) {
            $message = "Lỗi khi chuyển nhân viên: " . print_r(sqlsrv_errors(), true);
        } else {
            // Tiến hành xóa chi nhánh
            $sql_delete = "EXEC sp_xoa_chi_nhanh ?";
            $params_delete = array($id_chi_nhanh);
            $stmt_delete = sqlsrv_query($conn, $sql_delete, $params_delete);
            
            // Kiểm tra nếu có lỗi khi xóa chi nhánh
            if ($stmt_delete === false) {
                $message = "Lỗi khi xóa chi nhánh: " . print_r(sqlsrv_errors(), true);
            } else {
                $message = "Chi nhánh đã được xóa thành công!";
            }
        }
    }

    // Chuyển hướng lại trang với thông báo
    header("Location: quanlychinhanh.php?message=" . urlencode($message));
    exit;
}

// Xử lý tìm kiếm chi nhánh
$ten_chi_nhanh = '';
if (isset($_GET['ten_chi_nhanh'])) {
    $ten_chi_nhanh = $_GET['ten_chi_nhanh'];
    $sql = "SELECT * FROM ChiNhanh WHERE ten_chi_nhanh LIKE ?";
    $params = array('%' . $ten_chi_nhanh . '%');
    $stmt = sqlsrv_query($conn, $sql, $params);
} else {
    // Nếu không tìm kiếm, hiển thị tất cả chi nhánh
    $sql = "SELECT * FROM ChiNhanh";
    $stmt = sqlsrv_query($conn, $sql);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Chi Nhánh</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Quản Lý Chi Nhánh</h1>
            <nav>
                <a href="index.php">Trang chủ</a>
                <a href="quanlynhanvien.php">Quản lý nhân viên</a>
                <a href="quanlysanpham.php">Quản lý sản phẩm</a>
                <a href="quanlykhachhang.php">Quản lý khách hàng</a>
                <a href="quanlyhoadon.php">Quản lý hóa đơn</a>
                <a href="them_chi_nhanh.php">Thêm chi nhánh mới</a>
            </nav>
        </div>
    </header>
    <main>
        <!-- Form tìm kiếm -->
        <form action="quanlychinhanh.php" method="GET" class="search-form">
                <input type="text" name="ten_chi_nhanh" placeholder="Nhập tên chi nhánh" value="<?= htmlspecialchars($ten_chi_nhanh) ?>" required>
                <button type="submit">Tìm kiếm</button>
        </form>
        
        <!-- Hiển thị thông báo nếu có -->
        <?php if (isset($_GET['message'])): ?>
            <div class="alert 
                <?php 
                    // Kiểm tra nếu thông báo có chứa "Lỗi" để xác định kiểu thông báo
                    if (strpos($_GET['message'], 'Lỗi') !== false) {
                        echo 'alert-error'; // Thông báo lỗi
                    } else {
                        echo 'alert-success'; // Thông báo thành công
                    }
                ?>
            ">
                <?php echo htmlspecialchars($_GET['message']); ?>
            </div>
        <?php endif; ?>
        
        <div class="table-container">
            <h2>Danh sách chi nhánh</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên chi nhánh</th>
                        <th>Địa chỉ</th>
                        <th>Số điện thoại</th>
                        <th>Email</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= $row['id_chi_nhanh'] ?></td>
                            <td><?= $row['ten_chi_nhanh'] ?></td>
                            <td><?= $row['dia_chi'] ?></td>
                            <td><?= $row['so_dien_thoai'] ?></td>
                            <td><?= $row['email'] ?></td>
                            <td>
                                <a href="sua_chi_nhanh.php?id_chi_nhanh=<?= $row['id_chi_nhanh'] ?>">Sửa</a> |
                                <form action="quanlychinhanh.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id_chi_nhanh" value="<?= $row['id_chi_nhanh'] ?>">
                                    <select name="id_chi_nhanh_moi" required>
                                        <?php
                                        // Lấy danh sách các chi nhánh còn lại để chuyển nhân viên
                                        $sql_other = "SELECT * FROM ChiNhanh WHERE id_chi_nhanh != ?";
                                        $stmt_other = sqlsrv_query($conn, $sql_other, array($row['id_chi_nhanh']));
                                        while ($other = sqlsrv_fetch_array($stmt_other, SQLSRV_FETCH_ASSOC)) {
                                            echo "<option value='{$other['id_chi_nhanh']}'>{$other['ten_chi_nhanh']}</option>";
                                        }
                                        ?>
                                    </select>
                                    <input type="submit" name="delete" value="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa chi nhánh này? Nhân viên sẽ được chuyển sang chi nhánh khác.')">
                                </form>
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
// Giải phóng tài nguyên
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>

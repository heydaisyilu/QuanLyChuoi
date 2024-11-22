<?php
include 'db_connect.php';

// Lấy thông tin chi nhánh theo id
$id_chi_nhanh = $_GET['id_chi_nhanh'] ?? null;

if ($id_chi_nhanh) {
    // Lấy thông tin chi nhánh
    $sql = "SELECT * FROM ChiNhanh WHERE id_chi_nhanh = ?";
    $stmt_chi_nhanh = sqlsrv_query($conn, $sql, array($id_chi_nhanh));
    $chi_nhanh = sqlsrv_fetch_array($stmt_chi_nhanh, SQLSRV_FETCH_ASSOC);

    // Lấy danh sách nhân viên của chi nhánh
    $sql_nv = "SELECT * FROM NhanVien WHERE id_chi_nhanh = ?";
    $stmt_nhan_vien = sqlsrv_query($conn, $sql_nv, array($id_chi_nhanh));

    // Lấy danh sách sản phẩm của chi nhánh
    $sql_sp = "SELECT * FROM SanPham WHERE id_chi_nhanh = ?";
    $stmt_san_pham = sqlsrv_query($conn, $sql_sp, array($id_chi_nhanh));
} else {
    die("Chi nhánh không tồn tại.");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Chi Nhánh</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Chi Tiết Chi Nhánh</h1>
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
        <div class="container">
            <h2>Thông Tin Chi Nhánh</h2>
            <?php if ($chi_nhanh): ?>
                <div class="chi-nhanh-info">
                    <h3>Tên Chi Nhánh: <?= $chi_nhanh['ten_chi_nhanh'] ?></h3>
                    <p>Địa Chỉ: <?= $chi_nhanh['dia_chi'] ?></p>
                    <p>Điện Thoại: <?= $chi_nhanh['so_dien_thoai'] ?></p>
                    <p>Email: <?= $chi_nhanh['email'] ?></p>
                </div>

                <h3>Danh Sách Nhân Viên</h3>
                <?php if (sqlsrv_has_rows($stmt_nhan_vien)): ?>
                    <div class="nhan-vien-list">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên Nhân Viên</th>
                                    <th>Chức Vụ</th>
                                    <th>Số Điện Thoại</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($nv = sqlsrv_fetch_array($stmt_nhan_vien, SQLSRV_FETCH_ASSOC)): ?>
                                    <tr>
                                        <td><?= $nv['id_nhan_vien'] ?></td>
                                        <td><?= $nv['ten_nhan_vien'] ?></td>
                                        <td><?= $nv['chuc_vu'] ?></td>
                                        <td><?= $nv['so_dien_thoai'] ?></td>
                                        <td><?= $nv['email'] ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>Không có nhân viên nào tại chi nhánh này.</p>
                <?php endif; ?>

                <h3>Danh Sách Sản Phẩm</h3>
                <?php if (sqlsrv_has_rows($stmt_san_pham)): ?>
                    <div class="san-pham-list">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên Sản Phẩm</th>
                                    <th>Mô Tả</th>
                                    <th>Giá</th>
                                    <th>Số Lượng Tồn Kho</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($sp = sqlsrv_fetch_array($stmt_san_pham, SQLSRV_FETCH_ASSOC)): ?>
                                    <tr>
                                        <td><?= $sp['id_san_pham'] ?></td>
                                        <td><?= $sp['ten_san_pham'] ?></td>
                                        <td><?= $sp['mo_ta'] ?></td>
                                        <td><?= number_format($sp['gia'], 0) ?> VNĐ</td>
                                        <td><?= $sp['so_luong_ton_kho'] ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>Không có sản phẩm nào tại chi nhánh này.</p>
                <?php endif; ?>

            <?php else: ?>
                <p>Chi nhánh không tồn tại.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

<?php
sqlsrv_free_stmt($stmt_chi_nhanh);
sqlsrv_free_stmt($stmt_nhan_vien);
sqlsrv_free_stmt($stmt_san_pham);
sqlsrv_close($conn);
?>

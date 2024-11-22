<?php
include 'db_connect.php'; // Kết nối cơ sở dữ liệu

// Xử lý khi người dùng submit form tìm kiếm
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ten_chi_nhanh = $_POST['ten_chi_nhanh']; // Lấy tên chi nhánh từ form

    // Gọi procedure để tìm kiếm chi nhánh
    $sql = "EXEC sp_tim_kiem_chi_nhanh ?";
    $params = array($ten_chi_nhanh);
    $stmt = sqlsrv_query($conn, $sql, $params);

    // Kiểm tra lỗi khi thực thi
    if ($stmt === false) {
        $errors = sqlsrv_errors();
        echo "Lỗi khi tìm kiếm chi nhánh: " . print_r($errors, true);
        exit;
    }

    // Lưu kết quả tìm kiếm
    $results = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $results[] = $row;
    }

    // Giải phóng bộ nhớ
    sqlsrv_free_stmt($stmt);

    // Lấy chi tiết chi nhánh (giả sử có các table cho nhân viên và sản phẩm)
    if (!empty($results)) {
        $id_chi_nhanh = $results[0]['id_chi_nhanh']; // Lấy ID chi nhánh từ kết quả tìm kiếm

        // Lấy thông tin chi nhánh chi tiết
        $sql_chi_nhanh = "SELECT * FROM ChiNhanh WHERE id_chi_nhanh = ?";
        $stmt_chi_nhanh = sqlsrv_query($conn, $sql_chi_nhanh, array($id_chi_nhanh));
        $chi_nhanh = sqlsrv_fetch_array($stmt_chi_nhanh, SQLSRV_FETCH_ASSOC);
        sqlsrv_free_stmt($stmt_chi_nhanh);

        // Lấy danh sách nhân viên của chi nhánh
        $sql_nhan_vien = "SELECT * FROM NhanVien WHERE id_chi_nhanh = ?";
        $stmt_nhan_vien = sqlsrv_query($conn, $sql_nhan_vien, array($id_chi_nhanh));
        $nhan_vien_list = [];
        while ($nv = sqlsrv_fetch_array($stmt_nhan_vien, SQLSRV_FETCH_ASSOC)) {
            $nhan_vien_list[] = $nv;
        }
        sqlsrv_free_stmt($stmt_nhan_vien);

        // Lấy danh sách sản phẩm của chi nhánh
        $sql_san_pham = "SELECT * FROM SanPham WHERE id_chi_nhanh = ?";
        $stmt_san_pham = sqlsrv_query($conn, $sql_san_pham, array($id_chi_nhanh));
        $san_pham_list = [];
        while ($sp = sqlsrv_fetch_array($stmt_san_pham, SQLSRV_FETCH_ASSOC)) {
            $san_pham_list[] = $sp;
        }
        sqlsrv_free_stmt($stmt_san_pham);
    }
}

// Đóng kết nối cơ sở dữ liệu
sqlsrv_close($conn);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm kiếm chi nhánh</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Tìm kiếm chi nhánh</h1>
        <nav>
        <a href="index.php">Trang chủ</a>
            <a href="quanlychinhanh.php">Quản lý chi nhánh</a>
                <a href="quanlynhanvien.php">Quản lý nhân viên</a>
                <a href="quanlysanpham.php">Quản lý sản phẩm</a>
                <a href="quanlykhachhang.php">Quản lý khách hàng</a>
                <a href="quanlyhoadon.php">Quản lý hóa đơn</a>
        </nav>
    </header>
    <main>
        <form method="POST">
            <label for="ten_chi_nhanh">Tên chi nhánh:</label>
            <input type="text" id="ten_chi_nhanh" name="ten_chi_nhanh" required>
            <button type="submit">Tìm kiếm</button>
        </form>

        <?php if (!empty($results)): ?>
            <h2>Kết quả tìm kiếm</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên chi nhánh</th>
                        <th>Địa chỉ</th>
                        <th>Số điện thoại</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td><?= $row['id_chi_nhanh'] ?></td>
                            <td><?= htmlspecialchars($row['ten_chi_nhanh']) ?></td>
                            <td><?= htmlspecialchars($row['dia_chi']) ?></td>
                            <td><?= htmlspecialchars($row['so_dien_thoai']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Hiển thị chi tiết chi nhánh -->
            <h2>Chi tiết chi nhánh</h2>
            <div>
                <h3>Thông tin chi nhánh</h3>
                <p><strong>Tên chi nhánh:</strong> <?= htmlspecialchars($chi_nhanh['ten_chi_nhanh']) ?></p>
                <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($chi_nhanh['dia_chi']) ?></p>
                <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($chi_nhanh['so_dien_thoai']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($chi_nhanh['email']) ?></p>
            </div>

            <!-- Hiển thị danh sách nhân viên -->
            <h3>Danh sách nhân viên</h3>
            <?php if (!empty($nhan_vien_list)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên nhân viên</th>
                            <th>Chức vụ</th>
                            <th>Số điện thoại</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($nhan_vien_list as $nv): ?>
                            <tr>
                                <td><?= $nv['id_nhan_vien'] ?></td>
                                <td><?= htmlspecialchars($nv['ten_nhan_vien']) ?></td>
                                <td><?= htmlspecialchars($nv['chuc_vu']) ?></td>
                                <td><?= htmlspecialchars($nv['so_dien_thoai']) ?></td>
                                <td><?= htmlspecialchars($nv['email']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Không có nhân viên nào.</p>
            <?php endif; ?>

            <!-- Hiển thị danh sách sản phẩm -->
            <h3>Danh sách sản phẩm</h3>
            <?php if (!empty($san_pham_list)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên sản phẩm</th>
                            <th>Mô tả</th>
                            <th>Giá</th>
                            <th>Số lượng tồn kho</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($san_pham_list as $sp): ?>
                            <tr>
                                <td><?= $sp['id_san_pham'] ?></td>
                                <td><?= htmlspecialchars($sp['ten_san_pham']) ?></td>
                                <td><?= htmlspecialchars($sp['mo_ta']) ?></td>
                                <td><?= number_format($sp['gia'], 0) ?> VNĐ</td>
                                <td><?= $sp['so_luong_ton_kho'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Không có sản phẩm nào.</p>
            <?php endif; ?>
        <?php elseif ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
            <p>Không tìm thấy chi nhánh nào với tên được cung cấp.</p>
        <?php endif; ?>
    </main>
</body>
</html>

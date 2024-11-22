<?php
include 'db_connect.php'; // Kết nối cơ sở dữ liệu

// Kiểm tra nếu có chi nhánh được chọn
if (isset($_POST['id_chi_nhanh'])) {
    $id_chi_nhanh = $_POST['id_chi_nhanh'];

    // Truy vấn sản phẩm từ chi nhánh đã chọn
    $sql = "SELECT * FROM SanPham WHERE id_chi_nhanh = ?";
    $stmt = sqlsrv_query($conn, $sql, array($id_chi_nhanh));

    // Kiểm tra lỗi
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Xuất ra các option sản phẩm
    if (sqlsrv_has_rows($stmt)) {
        echo '<option value="">Chọn sản phẩm</option>';
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            echo '<option value="' . $row['ten_san_pham'] . '">' . $row['ten_san_pham'] . '</option>';
        }
    } else {
        echo '<option value="">Không có sản phẩm nào trong chi nhánh này</option>';
    }

    // Giải phóng tài nguyên
    sqlsrv_free_stmt($stmt);
}
?>

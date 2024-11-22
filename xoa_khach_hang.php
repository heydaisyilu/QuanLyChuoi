<?php
include 'db_connect.php';  // Kết nối cơ sở dữ liệu

if (isset($_GET['id_khach_hang'])) {
    $id_khach_hang = $_GET['id_khach_hang'];

    // Xóa khách hàng khỏi cơ sở dữ liệu
    $sql = "DELETE FROM KhachHang WHERE id_khach_hang = ?";
    $params = array($id_khach_hang);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        $message = "Lỗi khi xóa khách hàng!";
    } else {
        $message = "Xóa khách hàng thành công!";
    }

    // Giải phóng tài nguyên
    if ($stmt !== false) {
        sqlsrv_free_stmt($stmt);
    }

    // Đóng kết nối
    sqlsrv_close($conn);

    // Chuyển hướng về trang quản lý khách hàng
    header("Location: quanlykhachhang.php?message=" . urlencode($message));
    exit;
}
?>

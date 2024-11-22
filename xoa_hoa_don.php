<?php
include 'db_connect.php';  // Kết nối cơ sở dữ liệu

if (isset($_GET['id_hoa_don'])) {
    $id_hoa_don = $_GET['id_hoa_don'];

    // Xóa hóa đơn khỏi cơ sở dữ liệu
    $sql = "DELETE FROM HoaDon WHERE id_hoa_don = ?";
    $params = array($id_hoa_don);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        $errors = sqlsrv_errors(); // Lấy chi tiết lỗi nếu xảy ra
        $message = "Lỗi khi xóa hóa đơn: " . print_r($errors, true);
        $alert_class = "alert-error";
    } else {
        $message = "Xóa hóa đơn thành công!";
        $alert_class = "alert-success";
    }

    // Giải phóng tài nguyên
    if ($stmt !== false) {
        sqlsrv_free_stmt($stmt);
    }

    // Đóng kết nối
    sqlsrv_close($conn);

    // Chuyển hướng về trang quản lý hóa đơn với thông báo
    header("Location: quanlyhoadon.php?message=" . urlencode($message) . "&alert_class=" . urlencode($alert_class));
    exit;
} else {
    // Không có ID hóa đơn được truyền vào
    $message = "Không tìm thấy ID hóa đơn!";
    $alert_class = "alert-error";

    // Đóng kết nối
    sqlsrv_close($conn);

    // Chuyển hướng về trang quản lý hóa đơn với thông báo
    header("Location: quanlyhoadon.php?message=" . urlencode($message) . "&alert_class=" . urlencode($alert_class));
    exit;
}
?>

<?php
include 'db_connect.php';

// Biến thông báo
$message = '';

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
    <title>Hệ thống quản lý cửa hàng nhiều chi nhánh</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Thêm CSS cho giao diện */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #333;
            color: white;
            padding: 10px 0;
            text-align: center;
        }

        header nav a {
            color: white;
            margin: 0 15px;
            text-decoration: none;
            font-weight: bold;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px 0;
        }

        h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 1.8em;
            margin-bottom: 20px;
        }

        .chi-nhanh-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between;
        }

        .chi-nhanh-item {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: calc(33.33% - 20px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .chi-nhanh-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .chi-nhanh-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chi-nhanh-item h3 {
            font-size: 1.3em;
            color: #333;
            margin-right: 15px; /* Thêm khoảng cách giữa tên và địa chỉ */
        }

        .chi-nhanh-item p {
            font-size: 1em;
            color: #555;
            margin: 0;
        }

        .chi-nhanh-link {
            text-decoration: none;
            color: inherit;
        }

        .chi-nhanh-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .chi-nhanh-item {
                width: calc(50% - 20px);
            }
        }

        @media (max-width: 480px) {
            .chi-nhanh-item {
                width: 100%;
            }
        }

        .search-form {
            margin-bottom: 20px;
        }

        .search-form input {
            padding: 10px;
            width: 70%;
            font-size: 1em;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .search-form button {
            padding: 10px 15px;
            background-color: #333;
            color: white;
            font-size: 1em;
            border-radius: 5px;
            border: none;
        }

        .search-form button:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Hệ thống quản lý cửa hàng nhiều chi nhánh</h1>
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
            <h2>Danh Sách Chi Nhánh</h2>

            <!-- Form tìm kiếm -->
            <form action="index.php" method="GET" class="search-form">
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
            
            <!-- Hiển thị danh sách chi nhánh -->
            <div class="chi-nhanh-list">
                <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
                    <div class="chi-nhanh-item">
                        <a href="chitietchinhanh.php?id_chi_nhanh=<?= $row['id_chi_nhanh'] ?>" class="chi-nhanh-link">
                            <div class="chi-nhanh-info">
                                <h3>Tên chi nhánh: <?= $row['ten_chi_nhanh'] ?></h3>
                                <p>Địa chỉ: <?= $row['dia_chi'] ?></p>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>

        </div>
    </main>
</body>
</html>

<?php
// Giải phóng tài nguyên
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>

<?php
require "/buivananh_duan1/admin/inc/essential.php";
require "/buivananh_duan1/admin/inc/db_config.php";
adminLogin();

// truy vấn xuất la loai phòng để thêm 
// Truy vấn lấy danh sách loại phòng
$loaiPhongQuery = "SELECT * FROM `loai_phong`";
$loaiPhongResult = mysqli_query($conn, $loaiPhongQuery);

// theem phong vào csdl
// 
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['them'])) {
    $tenPhong = loc($_POST['name']);
    $loaiPhong = (int) loc($_POST['loaiphong_id']); // Chuyển sang kiểu số nguyên
    $gia = (float) loc($_POST['gia']); // Chuyển sang kiểu số thực
    $dichVu = loc($_POST['dichvu']);
    $moTa = loc($_POST['mota']);
    $trangThai = loc($_POST['trangthai']);

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $folderUpload = 'C:/buivananh_duan1/upload/';
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = time() . '-' . $_FILES['image']['name'];
        $destPath = $folderUpload . $fileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $imagePath = $destPath;

            //$sql = "INSERT INTO `phong` (`name`, `loaiphong_id`, `image`, `gia`, `dichvu`, `mota`, `trangthai`) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $sql = "INSERT INTO `phong` (`name`, `loaiphong_id`, `image`, `gia`, `dichvu`, `mota`, `trangthai`) VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($conn, $sql);
            //mysqli_stmt_bind_param($stmt, 'sisssss', $tenPhong, $loaiPhong, $imagePath, $gia, $dichVu, $moTa, $trangThai);
            mysqli_stmt_bind_param($stmt, 'sisdsss', $tenPhong, $loaiPhong, $imagePath, $gia, $dichVu, $moTa, $trangThai);

            $result = mysqli_stmt_execute($stmt);

            if (!$result) {
                echo "Lỗi SQL: " . mysqli_error($conn);
            } else {
                echo "Phòng đã được thêm thành công.";
                //echo "<script>alert'Phòng đã được thêm thành công'</script>";
                //echo "<script>alert('Phòng đã được thêm thành công')</script>";

            }

            mysqli_stmt_close($stmt);
        } else {
            //echo "<script>alert('Thêm thành công'); window.location='phong.php';</script>";
            echo "Phòng đã được thêm thành công.";
        }
    } else {
        //cho "Lỗi: Không có file được tải lên hoặc có lỗi xảy ra trong quá trình tải lên.";
        echo "<script>alert'Lỗi: Không có file được tải lên hoặc có lỗi xảy ra trong quá trình tải lên.'</script>";
    }
}

// xuat phong 
$phongQuery = "SELECT * FROM `phong`";
$phongResult = mysqli_query($conn, $phongQuery);

///
// chuc nang xoa 
if (isset($_GET['delete'])) {
    // xoa theo id
    $id = $_GET['delete'];
    // truy van xoa 
    $q = "DELETE FROM `phong` WHERE `id`=?";
    $result = xoa($q, [$id], 'i');

    // kiểm tra va xóa
    if ($result) {
        echo "<script>alert('Xóa phong thành công'); window.location='phong.php';</script>";
    } else {
        echo "<script>alert('Lỗi khi xóa phòng'); window.location='phong.php';</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Phòng </title>
    <link rel="stylesheet" href="/admin/css/style.css">
    <?php
    require "/buivananh_duan1/admin/inc/link_admin.php";
    ?>
</head>

<body class="bg-light">
    <?php require "/buivananh_duan1/admin/inc/header.php"; ?>
    <div class="container-fluid" id="main-content">
        <div class="row">

            <div class="col-lg-10 ms-auto p-4 overflow-hidden">
                <h3 class="mb-4">Admin - Phòng </h3>
                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-body">
                        <div class="text-end mb-4">
                            <button type="button" class="btn btn-darks shadow-none btn-sm" data-bs-toggle="modal" data-bs-target="#add-room"><i class="bi bi-plus-square"></i>Thêm</button>
                        </div>

                        <div class="table-responsive-lg" style="height: 450px; overflow-y: scroll;">
                            <table class="table table-hover border">
                                <tr class="bg-dark text-light">
                                    <th scope="col">id</th>
                                    <th scope="col">Tên Phòng</th>
                                    <th scope="col">Loại Phòng</th>
                                    <th scope="col">Ảnh</th>
                                    <th scope="col">Giá</th>
                                    <th scope="col">Dich vụ</th>
                                    <th scope="col">Mô tả</th>
                                    <th scope="col">Trạng Thái</th>
                                    <th scope="col">Hành Động</th>
                                </tr>
                                <?php while ($row = mysqli_fetch_assoc($phongResult)) : ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo $row['name']; ?></td>
                                        <td><?php echo $row['loaiphong_id']; // Bạn có thể muốn join bảng để lấy tên loại phòng 
                                            ?></td>
                                        <td><img src="<?php echo $row['image']; ?>" alt="Ảnh phòng đang bị lỗi sử lí sau" style="width: 100px; height: auto;"></td>
                                        <td><?php echo $row['gia']; ?></td>
                                        <td><?php echo $row['dichvu']; ?></td>
                                        <td><?php echo $row['mota']; ?></td>
                                        <td><?php echo $row['trangthai']; ?></td>
                                        <!-- Các hành động như chỉnh sửa/xóa -->
                                        <td><a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</a></td>


                                    </tr>
                                <?php endwhile; ?>
                            </table>

                            <tbody id="room=-data"> </tbody>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <!--modal them phong-->
    <div class="modal fade" id="add-room" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form id="add_room" enctype="multipart/form-data" method="POST" autocomplete="off">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Them Phong</h5>
                    </div>
                    <div class="row">

                        <div class="modal-body">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Ten</label>
                                <input type="text" name="name" class="form-control shadow" required>
                            </div>
                        </div>

                        <!-- xuat loai phong de them phong vs loai phong  do-->
                        <div class="modal-body">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Loại Phòng</label>
                                <select name="loaiphong_id" class="form-control shadow" required>
                                    <option value="">Chọn loại phòng</option>
                                    <?php while ($row = mysqli_fetch_assoc($loaiPhongResult)) : ?>
                                        <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>



                        <div class="modal-body">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Ảnh Phòng</label>
                                <input type="file" name="image" class="form-control shadow" required>
                            </div>
                        </div>

                        <div class="modal-body">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Giá Phòng</label>
                                <input type="number" name="gia" class="form-control shadow" required>
                            </div>
                        </div>

                        <div class="modal-body">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Dịch vụ</label>
                                <input type="text" name="dichvu" class="form-control shadow" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mô tả: </label>
                            <textarea name="mota" required class="form-control shadow-none" rows="5" style="resize: none;"></textarea>
                        </div>

                        <div class="modal-body">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Trạng Thái</label>
                                <input type="text" name="trangthai" class="form-control shadow" required>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="reset" class="btn text-secondary shadow-none" data-bs-dismiss="modal">Thoat</button>
                        <button type="submit" name="them" class="btn bg-custom text-dark shadow-none">Them</button>
                    </div>
                </div>

        </div>
        </form>
    </div>
    <!--end modal them phong-->

    <?php require "/buivananh_duan1/admin/inc/scripts.php"; ?>
</body>

</html>
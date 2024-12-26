<?php
session_start();
require_once 'dbConnect.php'; // เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับข้อมูลจากฟอร์ม
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $gender = $_POST['gender'];
    $userlevel = 4; // กำหนดระดับผู้ใช้เป็น 4 (ผู้ใช้ทั่วไป)
    $recorddate = date('Y-m-d');
    $status = 3; // กำหนดสถานะเป็น 3 (ไม่ได้เป็นสมาชิก)
    $type = $_POST['type']; // รับประเภทผู้ใช้จากฟอร์ม ('personnel' หรือ 'general')

    // กำหนดเลขเริ่มต้นและรูปแบบ user_id ตามประเภท
    if ($type == 'personnel') {
        $prefix = '41'; // บุคลากร
        $start_id = 41001;
    } elseif ($type == 'general') {
        $prefix = '42'; // บุคคลทั่วไป
        $start_id = 42001;
    } else {
        die("ประเภทผู้ใช้ไม่ถูกต้อง");
    }

    // ค้นหา user_id ล่าสุดที่ตรงกับประเภทในฐานข้อมูล
    $sql_last_id = "SELECT user_id FROM users WHERE user_id LIKE '{$prefix}%' ORDER BY user_id DESC LIMIT 1";
    $result = mysqli_query($conn, $sql_last_id);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $last_id = $row['user_id'];
        $new_id = intval($last_id) + 1;
    } else {
        // ถ้ายังไม่มี user_id ที่ตรงกับประเภทในระบบ กำหนดค่าเริ่มต้น
        $new_id = $start_id;
    }

    // กำหนด user_id ใหม่
    $user_id = str_pad($new_id, 5, "0", STR_PAD_LEFT);

    // เพิ่มข้อมูลผู้ใช้ใหม่ในฐานข้อมูล
    $sql = "INSERT INTO users (user_id, name, surname, gender, userlevel, recorddate, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'sssssss', $user_id, $name, $surname, $gender, $userlevel, $recorddate, $status);

    if (mysqli_stmt_execute($stmt)) {
        echo "เพิ่มผู้ใช้ใหม่สำเร็จ";
        header("Location: users.php"); // เปลี่ยนเส้นทางหลังจากการเพิ่มข้อมูลสำเร็จ
        exit();
    } else {
        echo "เกิดข้อผิดพลาดในการเพิ่มผู้ใช้: " . mysqli_error($conn);
    }

    // ปิดการเชื่อมต่อฐานข้อมูล
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มผู้ใช้ใหม่</title>
    <!-- Basic meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Mobile metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1">
    <!-- Site metas -->
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Site icon -->
    <link rel="icon" href="images/fevicon.png" type="image/png" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <!-- Site CSS -->
    <link rel="stylesheet" href="style.css" />
    <!-- Responsive CSS -->
    <link rel="stylesheet" href="css/responsive.css" />
    <!-- Color CSS -->
    <link rel="stylesheet" href="css/color_2.css" />
    <!-- Select bootstrap -->
    <link rel="stylesheet" href="css/bootstrap-select.css" />
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="css/perfect-scrollbar.css" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/custom.css" />
    <!-- Calendar file CSS -->
    <link rel="stylesheet" href="js/semantic.min.css" />
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mali:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;1,200;1,300;1,400;1,500;1,600;1,700&family=Mitr:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body,
        h3,
        h4,
        p,
        div {
            font-family: 'Mali', sans-serif;
        }

        .container {
            display: flex;
            justify-content: center;
            max-width: 80%;
            margin-top: 80px;
            /* เพิ่ม margin-top เพื่อไม่ให้ topbar บัง */
        }

        .form-row {
            width: 80%;
            max-width: 1000px;
            /* กำหนดความกว้างสูงสุดของฟอร์ม */
            margin: auto;
            /* จัดกึ่งกลาง */
            padding: 15px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            background-color: #f9f9f9;

            display: flex;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .form-row .form-group {
            flex: 1;
            display: flex;
            align-items: center;
            margin-right: 1rem;
        }

        .form-row .form-group label {
            width: 100px;
            margin-right: 1rem;
            text-align: right;
        }

        .form-row .form-group input,
        .form-row .form-group select {
            flex: 1;
            margin-left: 1rem;
            /* เพิ่ม margin ซ้ายให้กับ input และ select */
        }

        .form-row .form-group:last-child {
            margin-right: 0;
            /* ลบ margin ขวาของกลุ่มฟอร์มสุดท้าย */
        }

        .content {
            padding-top: 20px;
            /* เพิ่มระยะห่างจากด้านบน */
        }

        .button-container {
            display: flex;
            justify-content: flex-end;
            /* ทำให้เนื้อหาภายในคอนเทนเนอร์จัดชิดขวา */
        }

        .button-container .btn {
            font-size: 1.0rem;
            /* เพิ่มขนาดตัวอักษรของปุ่ม */
            padding: 10px 20px;
            /* เพิ่มขนาด padding ของปุ่ม */
            margin-right: 80px;
            /* ลบ margin ขวา */
        }
    </style>

</head>

<body>
    <div class="inner_container">
        <!-- Sidebar  -->
        <nav id="sidebar">
            <div class="sidebar_blog_1">
                <div class="sidebar-header"></div>
                <div class="sidebar_user_info">
                    <div class="user_info">
                        <a href="#" class="brand-link">
                            <img src="https://www.bru.ac.th/wp-content/uploads/2019/08/bru-web-logo-en.png" alt="BRU Logo" class="brand-image img-circle elevation-3" style="opacity: .8; max-width: 150px;">
                        </a>
                    </div>
                </div>
            </div>
            <div class="sidebar_blog_2">
                <h4>ผู้ดูแลระบบ</h4>
                <ul class="list-unstyled components">
                    <li>
                        <a href="javascript:history.go(-1)"><i class="far fa-arrow-alt-circle-left"></i><span>ย้อนกลับ</span></a>
                    </li>
                </ul>
                </ul>
            </div>
        </nav>

        <div id="content">
            <div class="topbar">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <div class="full">
                        <div class="right_topbar">
                            <div class="icon_info">
                                <ul class="user_profile_dd">
                                    <li>
                                        <a class="dropdown-toggle" data-bs-toggle="dropdown" href="#">
                                            <span class="name_user"><?php echo $_SESSION['user']; ?></span>
                                        </a>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="profile_member.php"><i class="fa-regular fa-id-badge"></i> <span>ข้อมูลส่วนตัว</span></a>
                                            <a class="dropdown-item" href="logout.php"><span>ออกจากระบบ</span> <i class="fa fa-sign-out"></i></a>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>
        </div>

        <div class="container">
            <div class="form-container">
                <h3 class="text-center">เพิ่มผู้ใช้ทั่วไป</h3>
                <form id="generalForm" action="insert_general.php" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name" class="form-label">ชื่อ</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="surname" class="form-label">นามสกุล</label>
                            <input type="text" class="form-control" id="surname" name="surname" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="gender" class="form-label">เพศ</label>
                            <select class="form-control" id="gender" name="gender" required>
                                <option value="">เลือกเพศ</option>
                                <option value="0">ชาย</option>
                                <option value="1">หญิง</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <label for="type">ประเภทผู้ใช้:</label>
                        <select name="type" id="type" required>
                            <option value="personnel">บุคลากร</option>
                            <option value="general">บุคคลทั่วไป</option>
                        </select>
                    </div>
                    <div class="button-container">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                    </div>
                </form>
            </div>
        </div>

        <script src="js/jquery.min.js"></script>
        <!-- Popper.js -->
        <script src="js/popper.min.js"></script>
        <!-- Bootstrap JS -->
        <script src="js/bootstrap.min.js"></script>
        <!-- Wow animation -->
        <script src="js/animate.js"></script>
        <!-- Select country -->
        <script src="js/bootstrap-select.js"></script>
        <!-- Owl carousel -->
        <script src="js/owl.carousel.js"></script>
        <!-- Chart JS -->
        <script src="js/Chart.min.js"></script>
        <script src="js/Chart.bundle.min.js"></script>
        <script src="js/utils.js"></script>
        <script src="js/analyser.js"></script>
        <!-- Nice scrollbar -->
        <script src="js/perfect-scrollbar.min.js"></script>
        <script>
            var ps = new PerfectScrollbar('#sidebar');
        </script>
        <!-- Custom JS -->
        <script src="js/custom.js"></script>
        <!-- Calendar file CSS -->
        <script src="js/semantic.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    </div>


</body>

</html>
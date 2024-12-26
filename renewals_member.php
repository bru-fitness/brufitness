<?php
session_start();
require_once 'dbConnect.php'; // เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $renewal_fee = $_POST['renewal_fee'];
    $renewal_date = $_POST['renewal_date'];
    $record_id = $_SESSION['userid'] ?? 1; // record ผู้บันทึก

    // Prepare and bind the SQL statement with parameters
    $insert = "INSERT INTO renewals (user_id, renewal_fee, renewal_date, record_id) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insert);
    mysqli_stmt_bind_param($stmt, "ssss", $user_id, $renewal_fee, $renewal_date, $record_id);

    // Execute the statement
    if (mysqli_stmt_execute($stmt)) {
        // Redirect to renewal page with success message
        header("Location: renewal.php?status=success&message=Renewal added successfully");
        exit();
    } else {
        // Redirect to renewal page with error message
        header("Location: renewal.php?status=error&message=Failed to add renewal");
        exit();
    }
}

// ตรวจสอบว่าได้รับ user_id มาจาก URL หรือไม่
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
} else {
    // ถ้าไม่มี user_id ให้ redirect ไปหน้าอื่น หรือแสดงข้อความแจ้งเตือน
    header("Location: notification.php?status=error&message=No user selected");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ต่ออายุสมาชิก</title>
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
        /* เปลี่ยนฟอนต์ทั้งหมดในหน้า */
        body,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        p,
        div {
            font-family: 'Mali', sans-serif;
        }
    </style>

    <style>
        .container {
            display: flex;
            justify-content: center;
            max-width: 80%;
            margin-top: 50px;
            /* เพิ่ม margin-top เพื่อไม่ให้ topbar บัง */
        }

        .form-container {
            padding: 30px;
        }

        .form-row {
            width: 90%;
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
            padding-top: 70px;
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
            margin-right: 100px;
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
                            <h3>BRU FITNESS</h3>
                        </a>
                    </div>
                </div>
            </div>
            <div class="sidebar_blog_2">
                <h4>ผู้ดูแลระบบ</h4>
                <ul class="list-unstyled components">
                    <li>
                        <a href="javascript:history.go(-1)"><i class="bi bi-caret-left-fill"></i><span>ย้อนกลับ</span></a>
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
                                            <a class="dropdown-item" href="profile_admin.php"><i class="fa-regular fa-id-badge"></i> <span>ข้อมูลส่วนตัว</span></a>
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
                <h3 class="text-center">ต่ออายุสมาชิก</h3>
                <form id="renewalForm" action="renewal_process.php" method="POST">
                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="user_id">รหัสผู้ใช้</label>
                            <input type="text" class="form-control" id="user_id" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>" readonly>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="renewal_fee">ค่าต่ออายุ</label>
                            <input type="number" class="form-control" id="renewal_fee" name="renewal_fee" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="renewal_date">วันที่ต่ออายุ</label>
                            <?php
                            date_default_timezone_set('Asia/Bangkok'); // กำหนด timezone
                            $current_datetime = date('Y-m-d\TH:i');   // รูปแบบสำหรับ datetime-local
                            ?>
                            <input type="datetime-local" class="form-control" id="renewal_date" name="renewal_date" value="<?php echo $current_datetime; ?>" required>
                        </div>
                    </div>

                    <!-- <div class="form-row">
                        <div class="form-group">
                            <label for="record_id">ผู้บันทึก</label>
                            <input type="text" class="form-control" id="record_id" name="record_id" required>
                        </div>
                    </div> -->
                    <div class="button-container">
                        <button type="submit" class="btn btn-success">ดำเนินการต่ออายุ</button>
                    </div>
                </form>
            </div>
        </div>
        <script>
            window.onload = function() {
                var userId = document.getElementById('user_id').value;
                var renewalFeeInput = document.getElementById('renewal_fee');

                if (userId.startsWith('31')) {
                    renewalFeeInput.value = 200;
                } else if (userId.startsWith('32')) {
                    renewalFeeInput.value = 500;
                } else {
                    renewalFeeInput.value = ''; // ล้างค่าถ้าไม่ตรงกับเงื่อนไข
                }
            };
        </script>

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
<?php

session_start();
require_once 'dbConnect.php'; // เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $payment_type_id = $_POST['payment_type_id'];
    $payment_type_name = '';
    $record_id = $_POST['record_id'];
    $other_payment_type = $_POST['other_payment_type'] ?? null;
    $service_fee = $_POST['service_fee'];
    $service_date = $_POST['service_date'];

    // ตรวจสอบประเภทการชำระเงินและตั้งชื่อประเภท
    if ($payment_type_id == '3' && !empty($other_payment_type)) {
        $payment_type_name = $other_payment_type;
    } else {
        switch ($payment_type_id) {
            case '1':
                $payment_type_name = 'เงินสด';
                break;
            case '2':
                $payment_type_name = 'คูปอง';
                break;
        }
    }

    // Prepare and bind the SQL statement with parameters
    $insert = "INSERT INTO payment_type (payment_type_id, payment_type_name, record_id, other_payment_type, user_id, service_fee, service_date) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insert);
    mysqli_stmt_bind_param($stmt, "sssssss", $payment_type_id, $payment_type_name, $record_id, $other_payment_type, $user_id, $service_fee, $service_date);

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
// ดึงรูปโปรไฟล์ของผู้ใช้ที่ล็อกอินอยู่
$userId = $_SESSION['userid'];
$sql = "SELECT picture FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Failed to prepare statement: " . $conn->error);
}
$stmt->bind_param('i', $userId);
$stmt->execute();
$stmt->bind_result($photo);
$stmt->fetch();
$stmt->close();

// กำหนด path รูปโปรไฟล์
$uploadDir = 'uploads/';
$defaultImage = 'cards/carderror.jpg';
$profileImage = !empty($photo) && file_exists($uploadDir . $photo) ? $uploadDir . $photo : $defaultImage;

// คิวรีเพื่อดึงจำนวนผู้ใช้ที่ใกล้หมดอายุการเป็นสมาชิก
$currentDate = date('Y-m-d');
$sql = "SELECT COUNT(*) as expiringCount FROM users 
        WHERE DATE_ADD(recorddate, INTERVAL 2 YEAR) BETWEEN '$currentDate' AND DATE_ADD('$currentDate', INTERVAL 30 DAY)";

$result = $conn->query($sql);

// ตรวจสอบผลลัพธ์ของคิวรี
if ($result) {
    $row = $result->fetch_assoc();
    $expiringCount = $row['expiringCount']; // จำนวนผู้ใช้ที่ใกล้หมดอายุ
} else {
    $expiringCount = 0; // กรณีที่คิวรีไม่สำเร็จหรือไม่มีผู้ใช้ที่ใกล้หมดอายุ
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การเข้าใช้บริการ</title>
    <link rel="icon" href="images/fevicon.png" type="image/png" />
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="css/responsive.css" />
    <link rel="stylesheet" href="css/color_2.css" />
    <link rel="stylesheet" href="css/bootstrap-select.css" />
    <link rel="stylesheet" href="css/perfect-scrollbar.css" />
    <link rel="stylesheet" href="css/custom.css" />
    <link rel="stylesheet" href="js/semantic.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <style>
        body,
        h3,
        h4,
        p,
        div {
            font-family: "Chakra Petch", sans-serif;
        }

        .container {
            display: flex;
            justify-content: center;
            max-width: 80%;
            margin-top: 50px;
        }

        .form-container {
            padding: 30px;
        }

        .form-row {
            width: 90%;
            max-width: 1000px;
            margin: auto;
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
        }

        .form-row .form-group:last-child {
            margin-right: 0;
        }

        .content {
            padding-top: 70px;
        }

        .button-container {
            display: flex;
            justify-content: flex-end;
        }

        .button-container .btn {
            font-size: 1.0rem;
            padding: 10px 20px;
            margin-right: 100px;
        }

        .dropdown-item.active {
            background-color: lightblue;
            /* กำหนดสีพื้นหลัง */
            color: #ffffff;
            /* กำหนดสีข้อความ */
        }
    </style>
</head>

<body>
    <div class="inner_container">
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
                        <a href="admin_page.php"><i class="fa-solid fa-home"></i> <span>หน้าแรก</span></a>
                    </li>
                    <li>
                        <a href="sign_up.php"><i class="fa-solid fa-user-plus"></i> <span>สมัครสมาชิก</span></a>
                    </li>
                    <li>
                        <a href="service_page.php" class="dropdown-item active" aria-current="true"><i class="fa-solid fa-person-walking-arrow-right"></i> <span>เข้าใช้บริการ</span></a>
                    </li>
                    <li>
                        <a href="renewals.php"><i class="fa-solid fa-user-clock"></i> </i> <span>ต่ออายุสมาชิก</span></a>
                    </li>
                    <li class="active">
                        <a href="#users" data-toggle="collapse" aria-expanded="true" class="dropdown-toggle"><i class="fa-solid fa-house-user"></i> <span>จัดการข้อมูลหลัก</span></a>
                        <ul class="collapse list-unstyled" id="users">
                            <li>
                                <a href="users.php"><i class="fas fa-user-cog"></i><span>ข้อมูลผู้ใช้ระบบ</span></a>
                            </li>
                            <li>
                                <a href="payrate.php"><i class="fas fa-hand-holding-usd"></i> <span>อัตราการชำระเงิน</span></a>
                            </li>
                        </ul>
                    </li>
                    <li class="active">
                        <a href="#report" data-toggle="collapse" aria-expanded="true" class="dropdown-toggle"><i class="fa fa-newspaper-o"></i> <span>รายงาน</span></a>
                        <ul class="collapse list-unstyled" id="report">
                            <li>
                                <a href="report_service.php"><i class="fa fa-calendar-check-o"></i><span>การเข้าใช้บริการ</span></a>
                            </li>
                            <li>
                                <a href=".php"><i class="fas fa-user-check"></i> <span>การสมัครสมาชิก</span></a>
                            </li>
                            <li>
                                <a href=".php"><i class="fa fa-refresh"></i> <span>การต่ออายุ</span></a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <div id="content">
            <div class="topbar">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <div class="full">
                        <div class="right_topbar">
                            <div class="icon_info">
                                <ul class="notification">
                                    <li class="nav-item">
                                        <a class="nav-link" href="notifications.php">
                                            <i class="fa fa-bell"></i>
                                            <span class="badge badge-danger">
                                                <?php echo $expiringCount; ?>
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                                <ul class="user_profile_dd">
                                    <li>
                                        <a class=" dropdown-toggle" href="javascript:void(0)" id="drop2"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <img src="<?php echo $profileImage; ?>" alt="Profile Picture" width="35"
                                                height="35" class="rounded-circle">
                                            <span
                                                class="d-none d-lg-inline-flex fs-3 m-2 text-dark"><?php echo htmlspecialchars($_SESSION['user']); ?></span>
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
                <h3 class="text-center">การเข้าใช้บริการ</h3>
                <form id="serviceForm" action="insertservice.php" method="POST">
                    <div class="form-row">
                        <div class="mb-3">
                            <label for="user_id" class="form-label">รหัสผู้ใช้</label>
                            <input type="text" class="form-control" id="user_id" name="user_id" required>
                            <div id="user_id_feedback" class="invalid-feedback" style="display: none;">ไม่มีผู้ใช้นี้อยู่</div>
                        </div>

                    </div>
                    <div class="form-row">
                        <div class="mb-3">
                            <label for="service_fee" class="form-label">ค่าบริการ</label>
                            <input type="text" class="form-control" id="service_fee" name="service_fee" readonly required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="mb-3">
                            <label for="payment_type_id" class="form-label">ประเภทการชำระเงิน</label>
                            <select class="form-select" id="payment_type_id" name="payment_type_id" readonly required>
                                <option value="1">เงินสด</option>
                                <option value="2">คูปอง</option>
                                <option value="3">อื่นๆ</option>
                            </select>
                        </div>
                        <div class="mb-3" id="otherPaymentTypeContainer" style="display: none;">
                            <label for="other_payment_type">ระบุประเภทการชำระเงิน (ถ้าเลือก 'อื่นๆ')</label>
                            <input type="text" class="form-control" id="other_payment_type" name="other_payment_type">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="mb-3">
                            <label for="service_date" class="form-label">วันที่เข้าใช้บริการ</label>
                            <input type="datetime-local" class="form-control" id="service_date" name="service_date" required>
                        </div>
                    </div>


                    <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
                </form>
            </div>
        </div>
        <!-- เช็ค user_id -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const userIdInput = document.getElementById('user_id');
                const feedback = document.getElementById('user_id_feedback');

                userIdInput.addEventListener('input', function() {
                    const userId = this.value.trim();

                    if (userId === '') {
                        feedback.style.display = 'none'; // ซ่อนข้อความแจ้งเตือนถ้าไม่ได้กรอกอะไร
                        userIdInput.classList.remove('is-invalid');
                        return;
                    }

                    // ตรวจสอบ user_id ด้วย AJAX
                    fetch('check_user.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `user_id=${userId}`,
                        })
                        .then((response) => response.json())
                        .then((data) => {
                            if (data.exists) {
                                feedback.style.display = 'none'; // ซ่อนข้อความแจ้งเตือน
                                userIdInput.classList.remove('is-invalid');
                            } else {
                                feedback.style.display = 'block'; // แสดงข้อความแจ้งเตือน
                                userIdInput.classList.add('is-invalid');
                            }
                        })
                        .catch((error) => console.error('Error:', error));
                });
            });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var serviceDateInput = document.getElementById('service_date');
                var now = new Date();

                var year = now.getFullYear();
                var month = ('0' + (now.getMonth() + 1)).slice(-2);
                var day = ('0' + now.getDate()).slice(-2);
                var hours = ('0' + now.getHours()).slice(-2);
                var minutes = ('0' + now.getMinutes()).slice(-2);

                var currentDateTime = year + '-' + month + '-' + day + 'T' + hours + ':' + minutes;
                serviceDateInput.value = currentDateTime;
            });
        </script>
        <!-- ค่าเข้าใช้บริการ -->
        <script>
            document.getElementById('user_id').addEventListener('input', function() {
                const userId = this.value;
                const serviceFeeInput = document.getElementById('service_fee');

                if (userId.length === 5) { // ตรวจสอบความยาวของรหัสผู้ใช้
                    const userLevel = userId.charAt(0); // ตัวเลขแรกคือ userlevel
                    const memberType = userId.charAt(1); // ตัวเลขที่สองคือ type

                    let serviceFee = '';
                    if (userLevel === '3') { // สมาชิก
                        if (memberType === '1') {
                            serviceFee = 20; // บุคลากร
                        } else if (memberType === '2') {
                            serviceFee = 40; // บุคคลทั่วไป
                        }
                    } else if (userLevel === '4') { // ไม่เป็นสมาชิก
                        if (memberType === '1') {
                            serviceFee = 40; // บุคลากร
                        } else if (memberType === '2') {
                            serviceFee = 60; // บุคคลทั่วไป
                        }
                    }

                    serviceFeeInput.value = serviceFee; // แสดงค่าบริการ
                } else {
                    serviceFeeInput.value = ''; // ล้างค่าถ้ารหัสไม่ถูกต้อง
                }
            });
        </script>
        <script src="js/jquery.min.js"></script>
        <script src="js/popper.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/animate.js"></script>
        <script src="js/bootstrap-select.js"></script>
        <script src="js/owl.carousel.js"></script>
        <script src="js/Chart.min.js"></script>
        <script src="js/Chart.bundle.min.js"></script>
        <script src="js/utils.js"></script>
        <script src="js/analyser.js"></script>
        <script src="js/perfect-scrollbar.min.js"></script>
        <script>
            var ps = new PerfectScrollbar('#sidebar');
        </script>
        <script src="js/custom.js"></script>
        <script src="js/semantic.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

        <script>
            // JavaScript สำหรับตรวจสอบประเภทการชำระเงิน
            document.getElementById('payment_type_id').addEventListener('change', function() {
                var otherPaymentTypeContainer = document.getElementById('otherPaymentTypeContainer');
                if (this.value == '3') {
                    otherPaymentTypeContainer.style.display = 'block';
                } else {
                    otherPaymentTypeContainer.style.display = 'none';
                }
            });
        </script>
    </div>
</body>

</html>
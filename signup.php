<?php
session_start();
require_once 'dbConnect.php'; // เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล

if (!isset($_SESSION['userid'])) {
    header("Location: index.php");
    exit();
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
$defaultImage = '../assets/images/profile/user-1.jpg';
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
    <title>สมัครสมาชิก</title>
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Chakra+Petch:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet">
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
            margin-top: 40px;
            /* เพิ่ม margin-top เพื่อไม่ให้ topbar บัง */
        }
        .form-row {
            width: 90%;
            max-width: 1000px;
            /* กำหนดความกว้างสูงสุดของฟอร์ม */
            margin: auto;
            /* จัดกึ่งกลาง */
            padding: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            background-color: #f9f9f9;

            display: flex;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }
        .form-row .form-group {
            margin-right: 1rem;
        }
        .form-row .form-group label {
            width: 100px;
            margin-right: 1rem;
            text-align: right;
            display: inline-block;
        }
        .form-row .form-group small {
            display: block;
            margin-top: 0.5rem;
            color: red;
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

        .dropdown-item.active {
            background-color: lightblue;
            /* กำหนดสีพื้นหลัง */
            color: #ffffff;
            /* กำหนดสีข้อความ */
        }
    </style>
    <style>
        /* สไตล์ของเส้นแสดงความแข็งแกร่ง */
        #password-strength-bar {
            height: 4px;
            margin-top: 5px;
            background-color: #ddd;
            /* สีพื้นฐานเมื่อยังไม่ได้พิมพ์รหัสผ่าน */
            border-radius: 2px;
            transition: background-color 0.3s ease;
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
                            <img src="https://www.bru.ac.th/wp-content/uploads/2019/08/bru-web-logo-en.png"
                                alt="BRU Logo" class="brand-image img-circle elevation-3"
                                style="opacity: .8; max-width: 150px;">
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
                        <a href="signup.php" class="dropdown-item active" aria-current="true"><i
                                class="fa-solid fa-user-plus"></i> <span>สมัครสมาชิก</span></a>
                    </li>
                    <li>
                        <a href="service_page.php"><i class="fa-solid fa-person-walking-arrow-right"></i>
                            <span>เข้าใช้บริการ</span></a>
                    </li>
                    <li>
                        <a href="renewals.php"><i class="fa-solid fa-user-clock"></i> </i>
                            <span>ต่ออายุสมาชิก</span></a>
                    </li>
                    <li class="active">
                        <a href="#users" data-toggle="collapse" aria-expanded="true" class="dropdown-toggle"><i
                                class="fa-solid fa-house-user"></i> <span>จัดการข้อมูลหลัก</span></a>
                        <ul class="collapse list-unstyled" id="users">
                            <li>
                                <a href="users.php"><i class="fas fa-user-cog"></i><span>ข้อมูลผู้ใช้ระบบ</span></a>
                            </li>
                            <li>
                                <a href="payrate.php"><i class="fas fa-hand-holding-usd"></i>
                                    <span>อัตราการชำระเงิน</span></a>
                            </li>
                        </ul>
                    </li>
                    <li class="active">
                        <a href="#report" data-toggle="collapse" aria-expanded="true" class="dropdown-toggle"><i
                                class="fa fa-newspaper-o"></i> <span>รายงาน</span></a>
                        <ul class="collapse list-unstyled" id="report">
                            <li>
                                <a href="service.php"><i
                                        class="fa fa-calendar-check-o"></i><span>การเข้าใช้บริการ</span></a>
                            </li>
                            <li>
                                <a href="report_signup.php"><i class="fas fa-user-check"></i>
                                    <span>การสมัครสมาชิก</span></a>
                            </li>
                            <li>
                                <a href="report_renewal.php"><i class="fa fa-refresh"></i> <span>การต่ออายุ</span></a>
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
                                            <span class="d-none d-lg-inline-flex fs-3 m-2 text-dark">
                                                <?php echo htmlspecialchars($_SESSION['user'], ENT_QUOTES, 'UTF-8'); ?>
                                            </span>
                                        </a>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="profile_admin.php"><i
                                                    class="fa-regular fa-id-badge"></i> <span>ข้อมูลส่วนตัว</span></a>
                                            <a class="dropdown-item" href="logout.php"><span>ออกจากระบบ</span> <i
                                                    class="fa fa-sign-out"></i></a>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>
        </div>

        <div class="container mt-5 content">
            <div class="container">
                <div class="form-container">
                    <h3 class="text-center">สมัครสมาชิก</h3>
                    <form id="insertUserForm" action="insertUser.php" method="POST" enctype="multipart/form-data">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name" class="form-label">ชื่อ</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="surname" class="form-label">นามสกุล</label>
                                <input type="text" class="form-control" id="surname" name="surname" required>
                            </div>
                            <div class="form-group">
                                <label for="dob" class="form-label">วันเกิด</label>
                                <input type="date" class="form-control" id="dob" name="dob" required>
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
                            <div class="form-group">
                                <label for="userlevel" class="form-label">ประเภทผู้ใช้</label>
                                <select class="form-control" id="userlevel" name="userlevel" required readonly>
                                    <option value="3" selected>สมาชิก</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="memberType" class="form-label">ประเภทสมาชิก</label>
                                <select class="form-control" id="type" name="type" required>
                                    <option value="">เลือกประเภทสมาชิก</option>
                                    <option value="1">บุคลากร</option>
                                    <option value="2">บุคคลทั่วไป</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="address" class="form-label">ที่อยู่</label>
                                <input type="text" class="form-control" id="address" name="address" required>
                            </div>
                            <div class="form-group">
                                <label for="phone" class="form-label">เบอร์โทร</label>
                                <input type="text" class="form-control" id="phone" name="phone" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="username" class="form-label">ชื่อผู้ใช้</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                                <small class="form-text text-danger">โปรดตั้งชื่อผู้ใช้เป็นภาษาอังกฤษ</small>
                            </div>

                            <!-- ช่องรหัสผ่าน -->
                            <div class="form-group col-md-6">
                                <label for="password" class="form-label">รหัสผ่าน</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" required
                                        oninput="checkPasswordStrength()">
                                    <div class="input-group-append">
                                        <span class="input-group-text"
                                            onclick="togglePassword('password', 'password-icon')">
                                            <i class="fa fa-eye-slash" id="password-icon"></i>
                                        </span>
                                    </div>
                                </div>
                                <div id="password-strength-bar" style="height: 5px; background-color: #ddd; margin-top: 5px;"></div>
                            </div>

                            <!-- ช่องยืนยันรหัสผ่าน -->
                            <div class="form-group col-md-6">
                                <label for="c_password" class="form-label">ยืนยันรหัสผ่าน</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="c_password" name="c_password" required
                                        oninput="checkPasswordMatch()">
                                    <div class="input-group-append">
                                        <span class="input-group-text"
                                            onclick="togglePassword('c_password', 'c_password-icon')">
                                            <i class="fa fa-eye-slash" id="c_password-icon"></i>
                                        </span>
                                    </div>
                                </div>
                                <span id="password-match-msg" style="color:red;"></span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="payrate_signup" class="form-label">ค่าสมัครสมาชิก</label>
                                <input type="number" class="form-control" id="signup_fee" name="signup_fee" readonly required>
                            </div>
                        </div>
                        <div class="button-container">
                            <button type="submit" class="btn btn-success" name="insertUser">สมัครสมาชิก</button>
                        </div>
                </div>
                </form>
            </div>
        </div>
        <!-- Modal แจ้งเตือน-->
        <div class="modal fade" id="responseModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modalBody">
                        <!-- ข้อความแจ้งเตือน -->
                    </div>
                    <div class="modal-footer" id="modalFooter">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('type').addEventListener('change', function() {
            var type = this.value;
            var userlevel = 3;
            var signupFeeInput = document.getElementById('payrate_signup');

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'get_payrate.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    signupFeeInput.value = response.payrate_signup || '';
                }
            };
            xhr.send('type=' + encodeURIComponent(type) + '&userlevel=' + encodeURIComponent(userlevel));
        });
    </script>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace("fa-eye-slash", "fa-eye");
            } else {
                input.type = "password";
                icon.classList.replace("fa-eye", "fa-eye-slash");
            }
        }

        function checkPasswordStrength() {
            const password = document.getElementById("password").value;
            const strengthBar = document.getElementById("password-strength-bar");

            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-z0-9]/.test(password)) strength++;

            const colors = ["#ddd", "red", "orange", "yellow", "green"];
            strengthBar.style.width = `${(strength / 5) * 100}%`;
            strengthBar.style.backgroundColor = colors[strength];
        }


        function checkPasswordMatch() {
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("c_password").value;
            const matchMsg = document.getElementById("password-match-msg");
            if (password !== confirmPassword) {
                matchMsg.textContent = "รหัสผ่านไม่ตรงกัน";
            } else {
                matchMsg.textContent = "";
            }
        }
    </script>

    <script>
        document.getElementById("username").addEventListener("input", function() {
            const username = this.value;
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "check_username.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onload = function() {
                document.getElementById("username-msg").textContent = this.responseText;
            };
            xhr.send("username=" + username);
        });
    </script>

    <script>
        function togglePassword(fieldId, iconId) {
            const passwordField = document.getElementById(fieldId);
            const icon = document.getElementById(iconId);

            if (passwordField.type === "password") {
                passwordField.type = "text"; // แสดงรหัสผ่าน
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            } else {
                passwordField.type = "password"; // ซ่อนรหัสผ่าน
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            }
        }
    </script>
    <!-- แจ้งเตือน -->
    <!-- <script>
        document.getElementById("insertUserForm").addEventListener("submit", function(event) {
            event.preventDefault(); // ป้องกันการ reload หน้า

            const formData = new FormData(this);
            fetch("insertUser.php", {
                    method: "POST",
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        showModal("สำเร็จ", data.message, "success");
                    } else {
                        showModal("ข้อผิดพลาด", data.message, "danger");
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    showModal("ข้อผิดพลาด", "เกิดข้อผิดพลาดในระบบ", "danger");
                });
        });

        function showModal(title, message, type) {
            const modalTitle = document.getElementById("modalTitle");
            const modalBody = document.getElementById("modalBody");
            const modalFooter = document.getElementById("modalFooter");

            modalTitle.textContent = title;
            modalBody.textContent = message;

            modalFooter.className = `btn btn-${type}`;
            const modal = new bootstrap.Modal(document.getElementById('responseModal'));
            modal.show();
        }
    </script> -->
    <script>
        document.getElementById('type').addEventListener('change', function() {
            const signupFeeInput = document.getElementById('signup_fee');
            const memberType = this.value;

            if (memberType === "1") {
                signupFeeInput.value = 200; // ค่าสมัครสำหรับบุคลากร
            } else if (memberType === "2") {
                signupFeeInput.value = 500; // ค่าสมัครสำหรับบุคคลทั่วไป
            } else {
                signupFeeInput.value = ''; // รีเซ็ตค่าสมัครถ้าไม่ได้เลือกประเภท
            }
        });
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    </div>
</body>

</html>
<?php
session_start();
require_once 'dbConnect.php'; // เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับข้อมูลจากฟอร์ม
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $gender = $_POST['gender'];
    $telephone = $_POST['telephone'];
    $userlevel = 1; // กำหนดระดับผู้ใช้เป็น 2 (อาจารย์)
    $username = $_POST["username"];
    $password = $_POST["password"];
    $c_password = $_POST["c_password"];
    $recorddate = date('Y-m-d H:i:s');
    $status = 4; // กำหนดสถานะเป็น 4 (บุคลากร)

    // เก็บ record_id ของผู้ที่ทำรายการ
    $record_id = $_SESSION['user_id'];

    // ค้นหา user_id ล่าสุดของอาจารย์ (userlevel=2) ในฐานข้อมูล
    $sql_last_id = "SELECT user_id FROM users WHERE user_id LIKE '1%' AND userlevel = 1 ORDER BY user_id DESC LIMIT 1";
    $result = mysqli_query($conn, $sql_last_id);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $last_id = $row['user_id'];
        $new_id = intval($last_id) + 1;
    } else {
        // ถ้ายังไม่มี user_id ที่ขึ้นต้นด้วย '1' กำหนดค่าเริ่มต้นเป็น 10001
        $new_id = 10001;
    }

    // กำหนด user_id ใหม่เป็นเลข 5 หลัก
    $user_id = str_pad($new_id, 5, "0", STR_PAD_LEFT);

    // เพิ่มข้อมูลผู้ใช้ใหม่ในฐานข้อมูล
    $sql = "INSERT INTO users (user_id, name, surname, gender, telephone, userlevel, username, password, recorddate, status, record_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'sssssssssss', $user_id, $name, $surname, $gender, $telephone, $userlevel, $username, $password, $recorddate, $status, $record_id);

    if (mysqli_stmt_execute($stmt)) {
        echo "เพิ่มผู้ใช้อาจารย์สำเร็จ";
        header("Location: users.php");
        exit();
    } else {
        echo "เกิดข้อผิดพลาดในการเพิ่มผู้ใช้: " . mysqli_error($conn);
    }    
    // ตรวจสอบว่าชื่อผู้ใช้มีอยู่หรือไม่
    $sql_check = "SELECT * FROM users WHERE username = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, 's', $username);
    mysqli_stmt_execute($stmt_check);
    $result = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($result) > 0) {
        echo "ชื่อผู้ใช้นี้มีอยู่แล้ว กรุณาเลือกชื่อผู้ใช้อื่น";
        exit();
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
    <title>เพิ่มรายชื่ออาจารย์</title>
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
    <!-- <link rel="stylesheet" href="css/style.css" /> -->
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

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
        max-width: 85%;
        margin-top: 40px;
        /* เพิ่ม margin-top เพื่อไม่ให้ topbar บัง */
    }

    .form-container {
        padding: 20px;
        background-color: #f7f7f7;
        border-radius: 10px;
        box-sizing: border-box;
    }

    .form-row {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 15px;
    }

    .form-group {
        flex: 1;
        padding: 10px;
        box-sizing: border-box;
    }

    .button-container {
        display: flex;
        justify-content: flex-end;
        /* Align items to the right */
    }

    .button-container .btn {
        font-size: 1.0rem;
        /* Increase button text size */
        padding: 10px 20px;
        /* Increase button padding */
    }

    .content {
        padding-top: 20px;
        /* เพิ่มระยะห่างจากด้านบน */
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
                        </a>
                    </div>
                </div>
            </div>
            <div class="sidebar_blog_2">
                <h4>ผู้ดูแลระบบ</h4>
                <ul class="list-unstyled components">
                    <li>
                        <a href="javascript:history.go(-1)"><i
                                class="far fa-arrow-alt-circle-left"></i><span>ย้อนกลับ</span></a>
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
                                            <a class="dropdown-item" href="profile_member.php"><i
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
        <div class="container">
            <div class="form-container">
                <h3 class="text-center">เพิ่มรายชื่ออาจารย์</h3>
                <form id="lecturerForm" action="insert_lecturer.php" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name" class="form-label">ชื่อ</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
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
                        <div class="form-group">
                            <label for="telephone" class="form-label">เบอร์โทรศัพท์</label>
                            <input type="text" class="form-control" id="telephone" name="telephone" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="username" class="form-label">ชื่อผู้ใช้</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                            <small class="form-text text-danger">โปรดตั้งชื่อผู้ใช้เป็นภาษาอังกฤษ</small>
                        </div>
                    </div>
                    <div class="form-row">
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
                            <div id="password-strength-bar"></div>
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

                    <div class="button-container">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                    </div>

            </div>

            </form>
        </div>
    </div>

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
        if (/[A-Z]/.test(password)) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;

        // เปลี่ยนสีของเส้นตามระดับความแข็งแกร่ง
        if (password.length === 0) {
            strengthBar.style.backgroundColor = "#ddd"; // สีพื้นฐานเมื่อไม่มีอักษร
        } else if (strength <= 2) {
            strengthBar.style.backgroundColor = "red"; // ง่าย
        } else if (strength === 3) {
            strengthBar.style.backgroundColor = "orange"; // ปานกลาง
        } else {
            strengthBar.style.backgroundColor = "green"; // ยาก
        }
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
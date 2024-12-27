<?php
session_start();
require_once 'dbConnect.php'; // เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = isset($_POST["name"]) ? $_POST["name"] : "";
    $surname = isset($_POST["surname"]) ? $_POST["surname"] : "";
    $gender = isset($_POST["gender"]) ? $_POST["gender"] : "";
    $userlevel = isset($_POST["userlevel"]) ? $_POST["userlevel"] : "";
    $birthday = isset($_POST["dob"]) ? $_POST["dob"] : "";
    $address = isset($_POST["address"]) ? $_POST["address"] : "";
    $telephone = isset($_POST["phone"]) ? $_POST["phone"] : "";
    $username = isset($_POST["username"]) ? $_POST["username"] : "";
    $password = isset($_POST["password"]) ? $_POST["password"] : "";
    $recorddate = date('Y-m-d '); // ใช้วันที่ปัจจุบันเป็นวันที่สมัครสมาชิก

    $insert = "INSERT INTO users (name, surname, gender, userlevel, birthday, address, telephone, username, password, recorddate) 
               VALUES ('$name', '$surname', '$gender', '$userlevel', '$birthday', '$address', '$telephone', '$username', '$password', '$recorddate')";
    $result = mysqli_query($conn, $insert);

    if ($result) {
        $_SESSION['success'] = "เพิ่มข้อมูลผู้ใช้เรียบร้อยแล้ว";
        header("refresh:2; url=users.php");
        exit();
    } else {
        $_SESSION['error'] = "มีข้อผิดพลาดเกิดขึ้นในการเพิ่มข้อมูล";
        header("refresh:2; url=users.php");
        exit();
    }
}
// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
$user_id = 1; // สมมติว่าคุณมี user_id หรือใช้ $_GET['user_id'] หรือค่าอื่นๆ ตามที่คุณต้องการ
$sql = "SELECT recorddate FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $recorddate = $row['recorddate']; // วันที่สมัครสมาชิก
    $current_date = date('Y-m-d');
    $expire_date = date('Y-m-d', strtotime($recorddate . ' +2 years'));
    $one_month_before_expire = date('Y-m-d', strtotime($expire_date . ' -1 month'));

    $status = 0;

    if ($current_date >= $expire_date) {
        $status = 2; // สมาชิกหมดอายุแล้ว
    } elseif ($current_date >= $one_month_before_expire) {
        $status = 1; // ใกล้หมดอายุ
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
if (isset($row['recorddate']) && !empty($row['recorddate'])) {
    $date = date_create($row['recorddate']);
} else {
    // กรณีที่ไม่มี 'recorddate' หรือมีค่าเป็นค่าว่าง
    $date = null; // หรือจัดการกรณีที่ไม่มีข้อมูลตามที่ต้องการ
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลผู้ใช้ระบบ</title>
    <!-- Basic meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Mobile metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
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
    <!-- <link rel="stylesheet" href="css/sidebar.css" /> -->
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

        body {
            background-color: white;
            /* พื้นหลังสีขาว */
        }

        .dropdown-item.active {
            background-color: lightblue;
            color: #ffffff;

        }

        .dropdown-item:hover {
            background-color: lightgreen;
            color: black;
        }
    </style>

<script>
    function confirmDelete(user_id) {
        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: "การกระทำนี้ไม่สามารถย้อนกลับได้!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                // ส่งคำร้องขอ AJAX
                $.ajax({
                    url: 'deleteUser.php', // ไฟล์ PHP สำหรับลบข้อมูล
                    type: 'POST',
                    data: {
                        user_id: user_id
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.status == 'success') {
                            Swal.fire(
                                'ลบสำเร็จ!',
                                response.message,
                                'success'
                            ).then(() => location.reload()); // โหลดหน้าซ้ำ
                        } else if (response.status == 'warning') {
                            Swal.fire(
                                'คำเตือน!',
                                response.message,
                                'warning'
                            );
                        } else {
                            Swal.fire(
                                'เกิดข้อผิดพลาด!',
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'เกิดข้อผิดพลาด!',
                            'ไม่สามารถลบข้อมูลได้',
                            'error'
                        );
                    }
                });
            }
        });
    }
</script>

    <script>
        let selectedUserId;

        function viewUser(user_id) {
            selectedUserId = user_id; // เก็บ user_id ที่เลือก

            // ดึงข้อมูลผู้ใช้จาก viewUser.php และแสดงใน Modal
            fetch('viewUser.php?user_id=' + user_id)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(data => {
                    document.getElementById('userDetails').innerHTML = data;
                    var myModal = new bootstrap.Modal(document.getElementById('userModal'));
                    myModal.show();
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                });
        }

        function editUser() {
            if (selectedUserId) {
                // ส่ง user_id ที่ถูกต้องไปยังหน้า edit_user.php
                window.location.href = `edit_user.php?user_id=${selectedUserId}`;
            } else {
                alert("เกิดข้อผิดพลาด: ไม่พบ user_id ที่ถูกต้อง");
            }
        }
    </script>

    <!-- แสดงข้อมูลทั้งหมดของคนๆนั้น -->
    <script>
        function fetchUserDetails(userId) {
            // Make an AJAX request to fetch user details
            fetch(`getUserDetails.php?user_id=${userId}`)
                .then(response => response.json())
                .then(data => {
                    // Check if the response contains user details
                    if (data.success) {
                        const userDetails = data.user;

                        // Populate the modal with user details
                        document.getElementById('userDetails').innerHTML = `
                        <p><strong>รูปภาพ:</strong> ${userDetails.picture}<p>
                        <p><strong>ชื่อ:</strong> ${userDetails.name}</p>
                        <p><strong>นามสกุล:</strong> ${userDetails.surname}</p>
                        <p><strong>เพศ:</strong> ${userDetails.gender == 0 ? 'ชาย' : 'หญิง'}</p>
                        <p><strong>ประเภทผู้ใช้:</strong> ${getUserLevel(userDetails.userlevel)}</p>
                        ${userDetails.userlevel == 3 ? `<p><strong>ประเภทสมาชิก:</strong> ${getType(userDetails.type)}</p>` : ''}
                        <p><strong>ชื่อผู้ใช้:</strong> ${userDetails.username}</p>
                        <p><strong>เบอร์โทรศัพท์:</strong> ${userDetails.telephone}</p>
                        <p><strong>วันที่บันทึกเข้าระบบ:</strong> ${userDetails.recorddate}</p>
                        <img src="uploads/${userDetails.picture}" alt="Profile Picture" style="max-width: 100%;">
                    `;
                    } else {
                        document.getElementById('userDetails').innerHTML = `<p>${data.error}</p>`;
                    }
                })
                .catch(error => {
                    document.getElementById('userDetails').innerHTML = `<p>เกิดข้อผิดพลาด: ${error.message}</p>`;
                });
        }

        function getUserLevel(userlevel) {
            switch (userlevel) {
                case '0':
                    return 'ผู้ดูแลระบบ';
                case '1':
                    return 'อาจารย์';
                case '2':
                    return 'เทรนเนอร์';
                case '3':
                    return 'สมาชิก';
                case '4':
                    return 'ผู้ใช้ทั่วไป';
                default:
                    return 'ไม่ทราบ';
            }
        }

        function getType(type) {
            switch (type) {
                case '1':
                    return 'บุคลากร';
                case '2':
                    return 'บุคคลทั่วไป';
                default:
                    return 'ไม่ทราบ';
            }
        }
    </script>


    <style>
        .content {
            padding-top: 40px;
            /* เพิ่มระยะห่างจากด้านบน */
        }

        .container {
            max-width: 80%;
            /* ปรับความกว้างตามที่ต้องการ */
        }

        .table thead th {
            background-color: rgb(152, 196, 253);
            /* สีพื้นหลัง */
            color: black;
            /* สีตัวอักษร */
        }
    </style>

</head>

<body>
    <?php if (isset($_SESSION['success'])) : ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success']; ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])) : ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['error']; ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

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
                        <a href="sign_up.php"><i class="fa-solid fa-user-plus"></i> <span>สมัครสมาชิก</span></a>
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
                                <a class="dropdown-item active" href="users.php"><i class="fas fa-user-cog"></i><span>ข้อมูลผู้ใช้ระบบ</span></a>
                            </li>
                            <li>
                                <a href="service.php"><i class="fa fa-calendar-check-o"></i><span>การเข้าใช้บริการ</span></a>
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
                                            <span
                                                class="d-none d-lg-inline-flex fs-3 m-2 text-dark"><?php echo htmlspecialchars($_SESSION['user']); ?></span>
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
            <h3 class="text-center">ข้อมูลผู้ใช้ระบบ</h3>
            <!-- <div class="d-flex justify-content-end mt-5 mb-3"> -->
            <div class="hstack gap-3">                <!-- Form ค้นหา -->
                <input type="text" id="searchBox" placeholder="กรอกคำค้นหา..." onkeyup="searchUsers()">
                <!-- ตัวกรองสถานะ -->
                <div class="btn-group">
                    <button class="btn bg-light border ms-auto dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        กรองตามสถานะ
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="setStatusFilter('')">ทั้งหมด</a></li>
                        <li><a class="dropdown-item" href="#" onclick="setStatusFilter('0')">ยังเป็นสมาชิก</a></li>
                        <li><a class="dropdown-item" href="#" onclick="setStatusFilter('1')">ใกล้หมดอายุ</a></li>
                        <li><a class="dropdown-item" href="#" onclick="setStatusFilter('2')">สมาชิกหมดอายุแล้ว</a></li>
                        <li><a class="dropdown-item" href="#" onclick="setStatusFilter('3')">ไม่ได้เป็นสมาชิก</a></li>
                        <li><a class="dropdown-item" href="#" onclick="setStatusFilter('4')">บุคลากร</a></li>
                    </ul>
                </div>
                <div class="btn-group">
                    <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        data-bs-auto-close="outside" aria-expanded="false">
                        เพิ่มผู้ใช้
                    </button>
                    <ul class="dropdown-menu">
                        <li><button class="dropdown-item"
                                onclick="window.location.href='insert_lecturer.php';">เพิ่มอาจารย์</button></li>
                        <li><button class="dropdown-item"
                                onclick="window.location.href='insert_trainer.php';">เพิ่มเทรนเนอร์</button></li>
                        <li><button class="dropdown-item"
                                onclick="window.location.href='insert_general.php';">เพิ่มผู้ใช้ทั่วไป</button></li>
                    </ul>
                </div>
            </div>

            <table class="table  table-striped table-bordered mt-4">
                <thead>
                    <tr>
                        <th>รหัสผู้ใช้</th>
                        <th>ชื่อ</th>
                        <th>นามสกุล</th>
                        <th>เพศ</th>
                        <th>ประเภทผู้ใช้</th>
                        <th>เบอร์โทรศัพท์</th>
                        <th>วันที่บันทึกเข้าระบบ</th>
                        <th>สถานะของผู้ใช้</th>
                        <th>การจัดการ</th>
                    </tr>
                </thead>
                <tbody id="usersTable">
                    <?php
                    $conn = new mysqli($servername, $username, $password, $dbname);

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $sql = "SELECT * FROM users ORDER BY recorddate DESC";
                    $result = $conn->query($sql);

                    $date = isset($row['recorddate']) && !empty($row['recorddate']) ? date_create($row['recorddate']) : date_create('2024-01-01');
                    $formatted_date = date_format($date, 'd/m/Y H:i'); // แปลงรูปแบบวันที่และเวลาเป็น วัน/เดือน/ปี ชั่วโมง:นาที

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                    ?>
                            <tr>
                                <td><?php echo $row['user_id']; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['surname']; ?></td>
                                <td>
                                    <?php
                                    switch ($row['gender']) {
                                        case '0':
                                            echo "ชาย";
                                            break;
                                        case '1':
                                            echo "หญิง";
                                            break;
                                        default:
                                            echo "ไม่ระบุ";
                                            break;
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $userlevel = "";
                                    switch ($row['userlevel']) {
                                        case '0':
                                            $userlevel = "ผู้ดูแลระบบ";
                                            break;
                                        case '1':
                                            $userlevel = "อาจารย์";
                                            break;
                                        case '2':
                                            $userlevel = "เทรนเนอร์";
                                            break;
                                        case '3':
                                            $userlevel = "สมาชิก";
                                            break;
                                        case '4':
                                            $userlevel = "ผู้ใช้ทั่วไป";
                                            break;
                                        default:
                                            $userlevel = "ไม่ระบุ";
                                            break;
                                    }
                                    echo $userlevel;
                                    ?>
                                </td>
                                <td><?php echo $row['telephone']; ?></td>
                                <td><?php echo $row['recorddate']; ?></td>
                                <td>
                                    <?php
                                    switch ($row['status']) {
                                        case 0:
                                            echo "ยังเป็นสมาชิก";
                                            break;
                                        case 1:
                                            echo "ใกล้หมดอายุ";
                                            break;
                                        case 2:
                                            echo "สมาชิกหมดอายุแล้ว";
                                            break;
                                        case 3:
                                            echo "ไม่ได้เป็นสมาชิก";
                                            break;
                                        case 4:
                                            echo "บุคลากร";
                                            break;
                                        default:
                                            echo "ไม่ระบุ";
                                            break;
                                    }
                                    ?>
                                </td>

                                <td>
                                    <button class="btn btn-info"
                                        onclick="viewUser(<?php echo $row['user_id']; ?>)">ดูข้อมูลทั้งหมด</button>
                                    <button class="btn btn-danger"
                                        onclick="confirmDelete(<?php echo $row['user_id']; ?>)">ลบ</button>
                                </td>
                            </tr>
                        <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="10" class="text-center">ยังไม่มีข้อมูลผู้ใช้</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

        </div>
    </div>
    </div>
    <!-- ค้นหาผู้ใช้ -->
    <script>
        // ฟังก์ชันค้นหาและแสดงข้อมูล
        function searchUsers() {
            const searchValue = document.getElementById('searchBox').value;

            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'search_users.php?search=' + encodeURIComponent(searchValue), true);

            xhr.onload = function() {
                if (this.status === 200) {
                    document.querySelector('#usersTable').innerHTML = this.responseText;
                }
            };

            xhr.send();
        }

        // โหลดข้อมูลเริ่มต้นเมื่อเปิดหน้า
        document.addEventListener('DOMContentLoaded', searchUsers);
    </script>
    <!-- กรองตามสถานะ -->
    <script>
        let statusFilter = ''; // ตัวแปรสำหรับสถานะ

        // ฟังก์ชันตั้งค่าสถานะที่กรอง
        function setStatusFilter(status) {
            statusFilter = status; // กำหนดค่าของตัวกรองสถานะ
            filterUsers(); // เรียกฟังก์ชันกรอง
        }

        // ฟังก์ชันกรองข้อมูล
        function filterUsers() {
            const searchValue = document.getElementById('searchBox').value; // ค่าคำค้นหา

            // ส่งคำค้นหากับตัวกรองสถานะไปยัง search_users.php
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `search_users.php?search=${encodeURIComponent(searchValue)}&status=${encodeURIComponent(statusFilter)}`, true);

            xhr.onload = function() {
                if (this.status === 200) {
                    document.querySelector('#usersTable').innerHTML = this.responseText; // อัพเดตตาราง
                }
            };

            xhr.send();
        }

        // โหลดข้อมูลเริ่มต้นเมื่อเปิดหน้า
        document.addEventListener('DOMContentLoaded', filterUsers);
    </script>
    <!-- ซ่อนsidebar -->
    <script>
        document.getElementById("sidebarToggle").addEventListener("click", function() {
            const sidebar = document.getElementById("sidebar");
            const content = document.getElementById("content");
            const toggleButton = document.querySelector('.toggle-button');

            // Toggle class เพื่อเลื่อน Sidebar
            sidebar.classList.toggle("hidden");
            content.classList.toggle("full");
        });
    </script>

    <script>
        document.getElementById('userlevel').addEventListener('change', function() {
            var memberTypeContainer = document.getElementById('memberTypeContainer');
            if (this.value === '3') {
                memberTypeContainer.style.display = 'block';
            } else {
                memberTypeContainer.style.display = 'none';
            }
        });
    </script>
    <!-- Modal for Viewing User Details -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">ข้อมูลส่วนตัว</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="userDetails">
                    <!-- รูปภาพจะแสดงที่นี่ -->
                    <div id="userImageContainer"></div>
                    <!-- รายละเอียดอื่น ๆ จะแสดงที่นี่ -->
                    <div id="userInfoContainer"></div>
                </div>
                <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-info" id="cardsButton" onclick="cardUser('<?php echo $user_id; ?>')">บัตรสมาชิก</button> -->
                    <button type="button" class="btn btn-warning" id="editUserButton"
                        onclick="editUser()">แก้ไขข้อมูล</button>
                </div>

            </div>
        </div>
    </div>
    <!-- sweetalert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            // การตั้งค่าค่าเริ่มต้นของจำนวนแถวต่อหน้า
            var rowsPerPage = $('#rowsPerPage').val();

            // เรียกใช้ DataTables
            var table = $('#example').DataTable({
                "pageLength": parseInt(rowsPerPage, 10)
            });

            // เมื่อมีการเปลี่ยนค่าของจำนวนแถวต่อหน้า
            $('#rowsPerPage').on('change', function() {
                rowsPerPage = $(this).val();
                table.page.len(parseInt(rowsPerPage, 10)).draw();
            });
        });
    </script>
    <!-- js active -->
    <script src="js/active.js"></script>
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
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </div>

</body>

</html>
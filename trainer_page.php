<?php
session_start();
require_once 'dbConnect.php';

// ตรวจสอบว่ามีค่า $_SESSION['userid'] หรือไม่ก่อนที่จะเข้าถึงค่า
if (!isset($_SESSION['userid'])) {
    header("Location: index.php");
    exit(); // ออกจากการทำงานของสคริปต์
}
// เซฟผู้บันทึกอัตโนมัติ
$record_id = isset($_SESSION['userid']) ? $_SESSION['userid'] : null;

$sql = "SELECT su .*, swt.user_id, u.name 
 FROM service_usage su 
 JOIN service_with_trainer swt 
 ON su.user_id = swt.user_id
 JOIN users u 
 ON su.user_id = u.user_id
 WHERE swt.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['userid']);
$stmt->execute();
$result = $stmt->get_result();

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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ส่วนของเทรนเนอร์</title>
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
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- ฟอนต์ -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Anuphun:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet">

    <style>
        body,
        h3,
        h4,
        p,
        div {
            font-family: 'Anuphun', sans-serif;
        }

        .modal-body .form-label {
            font-family: 'Anuphun', sans-serif;

        }
    </style>
    <style>
        .content {
            padding-top: 40px;
            padding-left: 20px;
        }

        .container {
            max-width: 80%;
            /* ปรับความกว้างตามที่ต้องการ */
            margin: 0 auto;
            /* จัดกึ่งกลางในแนวนอน */
            padding-left: 20px;
            /* เพิ่ม padding ด้านซ้ายเพื่อหลีกเลี่ยงการถูกบัง */
            padding-right: 20px;
            /* เพิ่ม padding ด้านขวาเพื่อความสมดุล */
            display: flex;
            /* justify-content: center; */
            align-items: center;
            flex-direction: column;
        }

        .button-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 10px;
        }

        .button-container .btn {
            font-size: 1.0rem;
            /* เพิ่มขนาดตัวอักษรของปุ่ม */
            padding: 10px 20px;
            /* เพิ่มขนาด padding ของปุ่ม */
            margin-right: 50px;
            /* เพิ่ม margin ขวา */
        }

        .table {
            max-width: 80%;
            padding-top: 20px;

        }

        .table thead th {
            background-color: rgb(40, 91, 129);
            /* สีพื้นหลัง */
            color: white;
            /* สีตัวอักษร */
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
                <h4>เทรนเนอร์</h4>
                <ul class="list-unstyled components">
                    <li class="active">
                        <a href="service_ulist.php"><i class="fa-solid fa-users-line"></i> <span>รายชื่อผู้เข้าใช้บริการ</span></a>
                    </li>
                    <li class="active">
                        <a href="trainer_page.php" class="dropdown-item active" aria-current="true"><i class="fa-solid fa-people-arrows"></i> <span>ผู้เข้าใช้บริการกับเทรนเนอร์</span></a>
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
                                            <a class="dropdown-item" href="profile_trainer.php"><i class="fa-regular fa-id-badge"></i> <span>ข้อมูลส่วนตัว</span></a>
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
        <div class="container mt-5 content">
            <h3 class="text-center">ผู้เข้าใช้บริการกับเทรนเนอร์</h3>
            <table class="table table-bordered mt-4">
                <tbody>
                    <?php
                    require_once 'dbConnect.php';

                    // ดึง user_id จาก session
                    $user_id = $_SESSION['userid'];

                    // คำสั่ง SQL สำหรับดึงข้อมูลที่เกี่ยวข้องกับ trainer_id
                    $sql = "SELECT s.service_date, u.user_id, s.details, s.review, u.name AS user_name, t.name AS trainer_name, s.with_trainer_id 
        FROM service_with_trainer s
        JOIN users u ON s.user_id = u.user_id
        JOIN users t ON s.trainer_id = t.user_id
        WHERE s.trainer_id = ?"; // ใช้เงื่อนไข WHERE เพื่อตรวจสอบ trainer_id

                    // เตรียม statement
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $user_id); // ผูกตัวแปร user_id กับ query
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        echo '<table class="table table-bordered mt-4">';
                        echo '<thead><tr><th>วันที่เข้าใช้บริการ</th><th>ผู้เข้าใช้บริการ</th><th>ชื่อเทรนเนอร์</th><th>รายละเอียด</th><th>ความคิดเห็นจากผู้ใช้</th><th>การจัดการ</th></tr></thead>';
                        echo '<tbody>';
                        while ($row = $result->fetch_assoc()) {
                            $date = date_create($row['service_date']);
                            $formatted_date = date_format($date, 'd/m/Y H:i');

                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($formatted_date) . '</td>';
                            echo '<td>' . htmlspecialchars($row['user_id'] . ' ' . $row['user_name']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['trainer_name']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['details']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['review']) . '</td>';
                            echo '<td>';
                            echo '<button class="btn btn-warning btn-sm mr-2 editBtn" 
                            data-bs-toggle="modal" 
                            data-bs-target="#editModal" 
                            data-with-trainer-id="' . htmlspecialchars($row['with_trainer_id']) . '" 
                            data-user-id="' . htmlspecialchars($row['user_id']) . '" 
                            data-details="' . htmlspecialchars($row['details']) . '">
                            แก้ไข
                          </button>';
                            echo '<button class="btn btn-danger btn-sm deleteBtn" data-with-trainer-id="' . htmlspecialchars($row['with_trainer_id']) . '">ลบ</button>';
                            echo '</td>';

                            echo '</tr>';
                        }
                        echo '</tbody></table>';
                    } else {
                        echo '<p class="text-center mt-3">ยังไม่มีข้อมูลการเข้าใช้บริการ</p>';
                    }

                    // ปิด statement และการเชื่อมต่อฐานข้อมูล
                    $stmt->close();
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>


    </div>

    <!-- Modal แก้ไขข้อมูล -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">แก้ไขข้อมูล</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" action="update_service_with_trainer.php" method="post">
                    <input type="hidden" id="with_trainer_id" name="with_trainer_id">
                    <div class="mb-3">
                        <label for="user_id" class="form-label">ชื่อผู้ใช้</label>
                        <input type="text" class="form-control" id="user_id" name="user_id" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="details" class="form-label">รายละเอียด</label>
                        <textarea class="form-control" id="details" name="details" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                </form>

            </div>
        </div>
    </div>
    <!-- ดึงเนื้อหามาแสดง -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editButtons = document.querySelectorAll('.editBtn');

            $('.editBtn').on('click', function() {
                const withTrainerId = $(this).data('with-trainer-id');
                const userId = $(this).data('user-id'); // อ่านค่า user_id ที่เกี่ยวข้อง
                const details = $(this).data('details'); // อ่านค่ารายละเอียด

                $('#with_trainer_id').val(withTrainerId); // ตั้งค่า with_trainer_id
                $('#user_id').val(userId); // ตั้งค่า user_id
                $('#details').val(details); // ตั้งค่ารายละเอียด
            });
        });
    </script>
    <!-- แก้ไขข้อมูล -->
    <script>
        $(document).ready(function() {
            $('.editBtn').on('click', function() {
                var withTrainerId = $(this).data('with-trainer-id');
                var serviceDate = $(this).data('service-date');
                var userName = $(this).data('user-name');
                // var trainerName = $(this).data('trainer-name');

                $('#edit-with-trainer-id').val(withTrainerId);
                $('#edit-service-date').val(serviceDate);
                $('#edit-user-name').val(userName);
                // $('#edit-trainer-name').val(trainerName);
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
    <!-- ลบข้อมูล -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.deleteBtn').forEach(button => {
                button.addEventListener('click', () => {
                    const withTrainerId = button.getAttribute('data-with-trainer-id');

                    if (!withTrainerId) {
                        Swal.fire('เกิดข้อผิดพลาด', 'ไม่พบข้อมูลที่ต้องการลบ', 'error');
                        return;
                    }

                    // แสดง SweetAlert แจ้งเตือนก่อนลบ
                    Swal.fire({
                        title: 'คุณแน่ใจหรือไม่?',
                        text: "การกระทำนี้ไม่สามารถย้อนกลับได้!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'ใช่, ลบเลย!',
                        cancelButtonText: 'ยกเลิก',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch('delete_service.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                    },
                                    body: JSON.stringify({
                                        with_trainer_id: withTrainerId
                                    }),
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire(
                                            'ลบสำเร็จ!',
                                            data.message,
                                            'success'
                                        ).then(() => location.reload()); // โหลดหน้าซ้ำ
                                    } else {
                                        Swal.fire('เกิดข้อผิดพลาด', data.message, 'error');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถลบข้อมูลได้', 'error');
                                });
                        }
                    });
                });
            });
        });
    </script>
    <!-- sweetalert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- jQuery -->
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
    </div>

</body>

</html>
<?php
session_start();
require_once 'dbConnect.php';
$sql = "SELECT * FROM service_usage , payment_type";
$result = mysqli_query($conn, $sql);
// ดึง user_id จาก session
$user_id = $_SESSION['userid'];

// ดึงข้อมูลการเข้าใช้บริการทั้งหมด
$sql = "SELECT su.*, pt.payment_type_id, u.name AS recorder_name, swt.details, swt.trainer_id
        FROM service_usage su
        JOIN payment_type pt ON su.payment_type_id = pt.payment_type_id
        JOIN users u ON su.record_id = u.user_id
        LEFT JOIN service_with_trainer swt ON su.service_id = swt.with_trainer_id";

$stmt = $conn->prepare($sql);
$stmt->execute();
// ดึงผลลัพธ์
$result = $stmt->get_result();

// if ($result->num_rows > 0) {
//     while ($row = $result->fetch_assoc()) {
//         echo '<tr>';
//         echo '<td>' . htmlspecialchars($row['service_date']) . '</td>';
//         echo '<td>' . htmlspecialchars($row['user_id']) . '</td>';
//         echo '<td>' . htmlspecialchars($row['trainer_id']) . '</td>';
//         echo '<td>' . htmlspecialchars($row['details']) . '</td>';
//         echo '</tr>';
//     }
// } else {
//     echo '<p>ไม่พบข้อมูล</p>';
// }

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
                    <a href="service_ulist.php" class="dropdown-item active" aria-current="true"><i class="fa-solid fa-users-line"></i> <span>รายชื่อผู้เข้าใช้บริการ</span></a>
                    </li>
                    <li class="active">
                    <a href="trainer_page.php"><i class="fa-solid fa-people-arrows"></i> <span>ผู้เข้าใช้บริการกับเทรนเนอร์</span></a>
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
            <h3 class="text-center">รายชื่อผู้เข้าใช้บริการ</h3>
            <?php if ($result->num_rows > 0): ?>
                <table class="table table-bordered mt-2">
                    <tbody>
                        <?php
                        require_once 'dbConnect.php'; // เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล
                        date_default_timezone_set('Asia/Bangkok');

                        // Fetch all service data
                        $sql = "SELECT su.*, 
               pt.payment_type_id, 
               u.name AS recorder_name, 
               swt.details, 
               swt.trainer_id, 
               u2.name AS user_name
        FROM service_usage su
        JOIN payment_type pt ON su.payment_type_id = pt.payment_type_id
        JOIN users u ON su.record_id = u.user_id
        LEFT JOIN service_with_trainer swt ON su.service_id = swt.with_trainer_id
        LEFT JOIN users u2 ON su.user_id = u2.user_id 
        ORDER BY su.service_date DESC";
                        $result = mysqli_query($conn, $sql);

                        if ($result && $result->num_rows > 0) {
                            echo '<table class="table table-bordered mt-4">';
                            echo '<thead><tr><th>วันที่เข้าใช้บริการ</th><th>ผู้เข้าใช้บริการ</th><th>การจัดการ</th></tr></thead>';
                            echo '<tbody>';

                            while ($row = mysqli_fetch_assoc($result)) {
                                // แปลง payment_type_id เป็นข้อความ
                                $payment_type = ($row['payment_type_id'] == 1) ? 'เงินสด' : (($row['payment_type_id'] == 2) ? 'คูปอง' : 'ไม่ระบุ');

                                // แปลงวันที่ให้อยู่ในรูปแบบ วัน/เดือน/ปี ชั่วโมง:นาที
                                $date = date_create($row['service_date']);
                                $formatted_date = date_format($date, 'd/m/Y H:i');

                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($formatted_date) . '</td>'; // แสดงวันที่เข้าใช้บริการ
                                echo '<td>' . htmlspecialchars($row['user_id']) . ' - ' . htmlspecialchars($row['user_name']) . '</td>'; // แสดง user_id และชื่อ
                                echo '<td>';
                                echo '<button type="button" class="btn btn-success btn-add-service" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#addService_trainnerModal" 
                                        data-user-id="' . htmlspecialchars($row['user_id']) . '">เพิ่มข้อมูล</button>';
                                echo '</td>';
                                echo '</tr>';
                            }
                            echo '</tbody></table>';
                        } else {
                            echo '<p class="text-center mt-4">ยังไม่มีข้อมูลการเข้าใช้บริการ</p>';
                        }

                        // ปิดการเชื่อมต่อฐานข้อมูล
                        mysqli_close($conn);
                        ?>

                    </tbody>
                </table>

            <?php else: ?>
                <p class="text-center mt-4">ยังไม่มีข้อมูลการเข้าใช้บริการ</p>
            <?php endif; ?>
        </div>

        <!-- Modal เพิ่มข้อมูล -->
        <div class="modal fade" id="addService_trainnerModal" tabindex="-1" aria-labelledby="addService_trainnerModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addService_trainnerModalLabel">เพิ่มข้อมูลการเข้าใช้บริการ</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="serviceForm" action="insertservice_trainer.php" method="post">
                            <div class="mb-3">
                                <label for="user_id" class="form-label">รหัสผู้เข้าใช้บริการ</label>
                                <input type="text" class="form-control" id="user_id" name="user_id">
                            </div>
                            <div class="mb-3">
                                <label for="service_date" class="form-label">วันที่เข้าใช้บริการ</label>
                                <input type="datetime-local" class="form-control" id="service_date" name="service_date" required>
                            </div>
                            <div class="mb-3">
                                <label for="details" class="form-label">รายละเอียด</label>
                                <input type="text" class="form-control" id="details" name="details" required>
                            </div>
                            <button type="submit" id="saveButton" class="btn btn-primary">บันทึก</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // เลือกปุ่มทั้งหมดที่มี class "btn-add-service"
            const buttons = document.querySelectorAll('.btn-add-service');

            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    // ดึงค่า user_id จาก data-user-id ของปุ่มที่คลิก
                    const userId = this.getAttribute('data-user-id');

                    // ใส่ค่า user_id ลงใน input ของ Modal
                    document.getElementById('user_id').value = userId;
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[action="insertservice_trainer.php"]');

            form.addEventListener('submit', function(e) {
                e.preventDefault(); // ป้องกันการรีเฟรชหน้า (กรณีใช้ AJAX)

                const formData = new FormData(this);

                // ส่งข้อมูลด้วย AJAX
                fetch(this.action, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json()) // คาดหวังผลลัพธ์ JSON จากเซิร์ฟเวอร์
                    .then(data => {
                        if (data.success) {
                            // หาแถวที่ตรงกับ service_id
                            const serviceRow = document.querySelector(`#row-${data.service_id}`);
                            if (serviceRow) {
                                serviceRow.querySelector('.btn-add-service').textContent = 'เพิ่มรายละเอียดแล้ว!';
                                serviceRow.querySelector('.btn-add-service').classList.replace('btn-success', 'btn-secondary');
                                serviceRow.querySelector('.btn-add-service').disabled = true; // ปิดการคลิกปุ่ม
                            }
                            // ปิด Modal
                            const modal = document.querySelector('#addService_trainnerModal');
                            const modalInstance = bootstrap.Modal.getInstance(modal);
                            modalInstance.hide();
                        } else {
                            alert('เกิดข้อผิดพลาด: ' + data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        });
    </script>
    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var now = new Date();
            var currentDateTime = now.toISOString().slice(0, 16);
            var serviceDateInput = document.getElementById('service_date');
            if (serviceDateInput) {
                serviceDateInput.value = currentDateTime;
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#saveButton').click(function() {
                $.ajax({
                    url: 'insertservice_trainer.php',
                    method: 'POST',
                    data: $('#serviceForm').serialize(),
                    success: function(response) {
                        var result = JSON.parse(response);
                        if (result.success) {
                            // ปิด modal
                            $('#addService_trainnerModal').modal('hide');
                            // เปลี่ยนเส้นทางไปยัง trainer_page.php
                            window.location.href = 'trainer_page.php';
                        } else {
                            alert(result.message); // แสดงข้อความผิดพลาด
                        }
                    }
                });
            });
        });
    </script>


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
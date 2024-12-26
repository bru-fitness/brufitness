<?php
session_start();
require_once 'dbConnect.php';

// ตรวจสอบว่ามีค่า $_SESSION['userid'] หรือไม่ก่อนที่จะเข้าถึงค่า
if (!isset($_SESSION['userid'])) {
    header("Location: index.php"); // ส่งกลับไปยังหน้า index.php หรือหน้าที่เหมาะสม
    exit(); // ออกจากการทำงานของสคริปต์
}

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// ดึงข้อมูลผู้ใช้ที่ user_id ขึ้นต้นด้วย 3% และตรงกับ session user_id
$sql = "
    SELECT 
        users.user_id,
        users.picture, 
        users.name, 
        users.surname, 
        users.recorddate, 
        (SELECT MAX(renewal_date) FROM renewals WHERE renewals.user_id = users.user_id) AS renewal_date 
    FROM users 
    WHERE users.user_id = ? AND users.user_id LIKE '3%'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['userid']);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Error running query: " . mysqli_error($conn));
}

$user = $result->fetch_assoc();

function calculateMembershipStatus($recorddate, $renewaldate)
{
    $current_date = new DateTime();
    $start_date = new DateTime($recorddate);
    $end_date = $start_date->modify('+2 years');

    if ($renewaldate) {
        $renewal_date = new DateTime($renewaldate);
        $end_date = $renewal_date->modify('+2 years');
    }

    $interval = $current_date->diff($end_date);

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

$expiryDate = (new DateTime($user['recorddate']))->modify('+2 years')->format('Y-m-d');
// วันที่ปัจจุบัน
$currentDate = date('Y-m-d');
// คำนวณวันที่คงเหลือ
$remainingDays = (strtotime($expiryDate) - strtotime($currentDate)) / (60 * 60 * 24);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตรวจสอบการเป็นสมาชิก</title>
    <!-- Basic meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Mobile metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <!-- Site metas -->
    <title>BRU FITNESS</title>
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
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- ฟอนต์ -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;1,200;1,300;1,400;1,500;1,600;1,700&family=Mitr:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* เปลี่ยนฟอนต์ทั้งหมดในหน้า */
        body,
        h3,
        h4,
        h5,
        p,
        div {
            font-family: 'Chakra Petch', sans-serif;
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
                <h4>สมาชิก</h4>
                <ul class="list-unstyled components">
                    <li class="active">
                        <a href="#service" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i class="fa-solid fa-clock-rotate-left"></i> <span>ประวัติการเข้าใช้บริการ</span></a>
                        <ul class="collapse list-unstyled" id="service">
                            <li>
                                <a href="service_yourself.php"><i class="fa-solid fa-user"></i> <span>เข้าใช้บริการด้วยตนเอง</span></a>
                            </li>
                            <li>
                                <a href="service_trainer.php"><i class="fa-solid fa-user-tie"></i> </i> <span>เข้าใช้บริการกับเทรนเนอร์</span></a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="checkstatus.php"><i class="fa-solid fa-address-card"></i> <span>ตรวจสอบการเป็นสมาชิก</span></a>
                    </li>
                    <li>
                        <a href="alltrainer.php"><i class="fa-solid fa-people-group"></i><span> ข้อมูลเทรนเนอร์ทั้งหมด</span></a>
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
            <div class="midde_cont">
                <div class="container-fluid">
                    <div class="row column_title">
                        <div class="col-md-12">
                            <div class="page_title">
                                <h2>ตรวจสอบการเป็นสมาชิก</h2>
                            </div>
                        </div>
                    </div>
                    <div class="row column1">
                        <div class="col-md-3"></div>
                        <div class="col-md-7">
                            <div class="white_shd full margin_bottom_30">
                                <div class="full graph_head">
                                    <div class="heading1 margin_0">
                                        <h2>รายละเอียดสมาชิก</h2>
                                    </div>
                                    <div class="full price_table padding_infor_info">
                                        <div class="row">
                                            <!-- user profile section -->
                                            <!-- profile image -->
                                            <div class="col-lg-12">
                                                <div class="full dis_flex center_text">
                                                    <?php if ($user) : ?>
                                                        <div class="profile_img"><img width="180" class="rounded-circle" src="uploads/<?php echo htmlspecialchars($user['picture']); ?>" alt="#" /></div>
                                                        <div class="profile_contant">
                                                            <div class="contact_inner">
                                                                <h3>ชื่อผู้ใช้: <?php echo htmlspecialchars($_SESSION['user']); ?></h3>
                                                                <p><strong>วันที่สมัครสมาชิก: </strong><?php echo htmlspecialchars($user['recorddate']); ?></p>
                                                                <?php if ($user['renewal_date']) : ?>
                                                                    <p><strong>วันที่ต่ออายุ: </strong><?php echo htmlspecialchars($user['renewal_date']); ?></p>
                                                                <?php endif; ?>
                                                                <p><strong>วันหมดอายุ: </strong><?php echo htmlspecialchars((new DateTime($user['recorddate']))->modify('+2 years')->format('Y-m-d')); ?></p>
                                                                <p><strong>สถานะการเป็นสมาชิกคงเหลือ: </strong>
                                                                    <?php
                                                                    // วันที่หมดอายุ
                                                                    $expiryDate = (new DateTime($user['recorddate']))->modify('+2 years')->format('Y-m-d');
                                                                    $currentDate = date('Y-m-d');

                                                                    // คำนวณจำนวนวันที่เหลือ
                                                                    $remainingDays = (strtotime($expiryDate) - strtotime($currentDate)) / (60 * 60 * 24);

                                                                    // แสดงผล
                                                                    if ($remainingDays > 0) {
                                                                        echo htmlspecialchars($remainingDays) . " วัน";
                                                                    } else {
                                                                        echo "หมดอายุแล้ว";
                                                                    }
                                                                    ?>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    <?php else : ?>
                                                        <p>ไม่มีสมาชิกที่ขึ้นต้นด้วย user_id=3%</p>
                                                    <?php endif; ?>
                                                </div>
                                                <button type="button" class="btn btn-info mb-2" onclick="window.location.href='user_page.php';">บัตรสมาชิก</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- jQuery -->
            <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
            <!-- Popper.js -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
            <!-- Bootstrap JS -->
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
            <!-- Additional JS files -->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>
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

            <script>
                $('#cardModal').on('show.bs.modal', function(event) {
                    var button = $(event.relatedTarget);
                    var userId = button.data('userid');
                    var modal = $(this);
                    modal.find('#cardImage').attr('src', 'cards/' + userId + '.png');
                });
            </script>
</body>

</html>
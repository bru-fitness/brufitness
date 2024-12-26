<?php
session_start();
require_once 'dbConnect.php';

// ตรวจสอบว่ามีค่า $_SESSION['userid'] หรือไม่ก่อนที่จะเข้าถึงค่า
if (!isset($_SESSION['userid'])) {
    header("Location: index.php"); // ส่งกลับไปยังหน้า index.php หรือหน้าที่เหมาะสม
    exit(); // ออกจากการทำงานของสคริปต์
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

$limit = 8; // จำนวนรายการต่อหน้า
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;
// ดึงข้อมูลเทรนเนอร์และคะแนน
$sql = "SELECT u.user_id, u.name, u.surname, u.gender, u.picture, u.telephone,
       CONCAT(u.name, ' ', u.surname) AS full_name, 
       IFNULL(AVG(s.rating), 0) AS avg_rating 
        FROM users u
        LEFT JOIN service_with_trainer s 
        ON u.user_id = s.trainer_id
        WHERE u.user_id LIKE '2%'
        GROUP BY u.user_id, u.name, u.surname, u.gender, u.picture";
$result = $conn->query($sql);
// Count total records for pagination
$total_query = "SELECT COUNT(*) FROM users WHERE user_id LIKE '2%'";
$total_result = $conn->query($total_query);
$total_row = $total_result->fetch_row();
$total_records = $total_row[0];
$total_pages = ceil($total_records / $limit);

// Pagination controls
$max_display_pages = 5;
$half_display_pages = floor($max_display_pages / 2);

$start_page = max(1, $page - $half_display_pages);
$end_page = min($total_pages, $start_page + $max_display_pages - 1);

if ($end_page - $start_page + 1 < $max_display_pages) {
    $start_page = max(1, $end_page - $max_display_pages + 1);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลเทรนเนอร์ทั้งหมด</title>
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
    <!-- แสดงข้อมูลเทรนเนอร์ -->
    <link rel="stylesheet" href="css/viewtrainer.css" />
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

        .table {
            max-width: 50%;
        }
        /* ปรับขนาดของรูปใน modal */
        #modalImage {
            width: 100%;
            height: auto;
        }

        .card-img-top {
            transition: transform 0.3s ease;
        }

        .card-img-top:hover {
            transform: scale(1.05);
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
        </div>
        <!-- end topbar -->
        <div class="container mt-5 content">
            <div class="wrapper">
                <div class="content-wrapper">
                    <div class="container">
                        <div class="content-header">
                            <div class="container-fluid">
                                <div class="row mb-2 mt-4">
                                    <div class="col-sm-6">
                                        <h4 class="m-0">ข้อมูลเทรนเนอร์ทั้งหมด</h4>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-group float-right" style="width: 300px;">
                                            <input type="text" id="searchInput" class="form-control" placeholder="ค้นหา...">
                                            <div class="input-group-append">
                                                <button id="searchButton" type="button" class="btn btn-primary">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="container">
                        <div class="row" id="TrainerCards">
                            <?php while ($row = $result->fetch_assoc()) { ?>
                                <div class="col-lg-3 col-md-6 col-sm-12 mb-3 trainer"
                                    data-name="<?php echo strtolower($row['name'] . ' ' . ($row['surname'] ?? '')); ?>"
                                    data-surname="<?php echo $row['surname'] ?? ''; ?>"
                                    data-gender="<?php echo isset($row['gender']) ? ($row['gender'] == 'ชาย' ? 0 : 1) : ''; ?>">
                                    <div class="card">
                                        <img src="<?php echo !empty($row['picture']) ? './uploads/' . ($row['picture']) : 'path/to/default-picture.jpg'; ?>" alt="Image" class="card-img-top">
                                        <div class="card-body">
                                            <h5 class="card-title">
                                                <?php echo ($row['name'] . " " . $row['surname']); ?>
                                                <hr>
                                            </h5>
                                            <p><strong>เพศ:</strong> <?php echo $row['gender'] ?? 'ไม่ระบุ'; ?></p>
                                            <p><strong>เบอร์โทร:</strong> <?php echo $row['telephone'] ?? 'ไม่ระบุ'; ?></p>
                                            <p>
                                                <strong>คะแนนรีวิว:</strong>
                                                <?php
                                                $avg_rating = round($row['avg_rating'], 1);
                                                echo $avg_rating . " / 5.0";
                                                ?>
                                            </p>
                                            <div class="rating-stars">
                                                <?php
                                                $full_stars = floor($avg_rating);
                                                $half_star = ($avg_rating - $full_stars) >= 0.5 ? 1 : 0;
                                                $empty_stars = 5 - $full_stars - $half_star;

                                                for ($i = 0; $i < $full_stars; $i++) {
                                                    echo '<i class="fas fa-star text-warning"></i>';
                                                }
                                                if ($half_star) {
                                                    echo '<i class="fas fa-star-half-alt text-warning"></i>';
                                                }
                                                for ($i = 0; $i < $empty_stars; $i++) {
                                                    echo '<i class="far fa-star text-warning"></i>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>


                    <!-- Pagination -->
                    <?php if (!isset($_GET['search']) || !$_GET['search']) { ?>
                        <nav id="paginationNav" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <!-- ปุ่มย้อนกลับ -->
                                <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                                    <a class="page-link" href="?page=<?php echo ($page - 1); ?>&search=<?php echo isset($_GET['search']) ? ($_GET['search']) : ''; ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <!-- แสดงเลขหน้า -->
                                <?php for ($i = $start_page; $i <= $end_page; $i++) { ?>
                                    <li class="page-item <?php if ($page == $i) echo 'active'; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo isset($_GET['search']) ? ($_GET['search']) : ''; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php } ?>
                                <!-- ปุ่มถัดไป -->
                                <li class="page-item <?php if ($page >= $total_pages) echo 'disabled'; ?>">
                                    <a class="page-link" href="?page=<?php echo ($page + 1); ?>&search=<?php echo isset($_GET['search']) ? ($_GET['search']) : ''; ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    <?php } ?>
                </div>
            </div>
        </div>

        <script>
            // JavaScript สำหรับการกรองข้อมูลในการ์ด
            $(document).ready(function() {
                function filterCards() {
                    var value = $("#searchInput").val().toLowerCase();
                    $("#TrainerCards .trainer").each(function() {
                        var name = $(this).data('name');
                        $(this).toggle(name.indexOf(value) > -1);
                    });

                    // ซ่อน pagination เมื่อค้นหา
                    if (value.length > 0) {
                        $("#paginationNav").hide();
                    } else {
                        $("#paginationNav").show();
                    }
                }

                // ทำงานเมื่อพิมพ์ในช่องค้นหา
                $("#searchInput").on("keyup", function() {
                    filterCards();
                });

                // ทำงานเมื่อคลิกปุ่มค้นหา
                $("#searchButton").on("click", function() {
                    filterCards();
                });
            



            // JavaScript สำหรับแสดงรูปภาพและข้อมูลใน modal
            $('#TrainerCards .card-img-top').on('click', function() {
                var imgSrc = $(this).data('img-src');
                var parentCard = $(this).closest('.trainer');
                var firstname = parentCard.data('name');
                var surname = parentCard.data('surname');

                $('#modalImage').attr('src', imgSrc);
                $('#modalInfo').html("ชื่อ นามสกุล : " + firstname + " " + surname);
            });

            });

            
        </script>


        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
        <!-- Popper.js -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <!-- Bootstrap JS -->
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- Popper.js -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Bootstrap JS -->
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>
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
    </div>

</body>

</html>
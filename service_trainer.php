<?php
session_start();
require_once 'dbConnect.php';

$sql = "SELECT service_date FROM service_with_trainer";
$result = mysqli_query($conn, $sql);
// ตรวจสอบว่ามีค่า $_SESSION['userid'] หรือไม่ก่อนที่จะเข้าถึงค่า
if (!isset($_SESSION['userid'])) {
    header("Location: index.php"); // ส่งกลับไปยังหน้า index.php หรือหน้าที่เหมาะสม
    exit(); // ออกจากการทำงานของสคริปต์
}

// ต่อไปให้ทำงานต่อเมื่อมี $_SESSION['userid'] มีค่า
// รับ URL ปัจจุบัน
$current_page = basename($_SERVER['PHP_SELF']);

// ตรวจสอบวันที่จากฟอร์ม
$date_filter = "";
if (isset($_POST['filter_date']) && $_POST['filter_date'] != "") {
    $date_filter = $_POST['filter_date'];
    $sql = "SELECT * FROM service_with_trainer WHERE service_date = '$date_filter' AND user_id LIKE '2%'";
} else {
    $sql = "SELECT * FROM service_with_trainer WHERE user_id LIKE '2%'";
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


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการเข้าใช้บริการกับเทรนเนอร์</title>
    <!-- Basic meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Mobile metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
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
    <link href="https://fonts.googleapis.com/css2?family=Anuphun:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;1,200;1,300;1,400;1,500;1,600;1,700&family=Mitr:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* เปลี่ยนฟอนต์ทั้งหมดในหน้า */
        body,
        h3,
        h4,
        h5,
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

        .table {
            max-width: 80%;
        }

        .table thead th {
            background-color: #0036A9;
            /* สีพื้นหลัง */
            color: white;
            /* สีตัวอักษร */
        }
    </style>
    <style>
        .star-rating {
            direction: rtl;
            /* ทำให้ดาวเรียงจากขวาไปซ้าย */
            display: inline-block;
            font-size: 2rem;
            padding: 0.1rem;
        }

        .star-rating input[type="radio"] {
            display: none;
        }

        .star-rating label {
            color: #ccc;
            cursor: pointer;
            font-size: 2rem;
            margin: 0 0.1rem;
        }

        .star-rating input[type="radio"]:checked~label {
            color: #f0c420;
        }

        .star-rating input[type="radio"]:checked+label:hover,
        .star-rating label:hover,
        .star-rating label:hover~label {
            color: #f0c420;
        }
    </style>
    <script>
        function setWithTrainerId(withTrainerId) {
            document.getElementById('with_trainer_id').value = withTrainerId;
        }
    </script>
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
                    <li class="<?php echo ($current_page == 'checkstatus.php') ?: ''; ?>">
                        <a href="checkstatus.php"><i class="fa-solid fa-address-card"></i> <span>ตรวจสอบการเป็นสมาชิก</span></a>
                    </li>
                    <li class="<?php echo ($current_page == 'alltrainer.php') ?: ''; ?>">
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
            <div class="container mt-5 content">
                <h3>ประวัติการเข้าใช้บริการกับเทรนเนอร์</h3>

                <br>
                <table class="table">
                    <thead>
                        <tr>
                            <th>วันที่</th>
                            <th>ชื่อเทรนเนอร์</th>
                            <th>ความคิดเห็นจากเทรนเนอร์</th>
                            <th>ให้คะแนนความพึงพอใจ</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $user_id = $_SESSION['userid'];
                        // Query ที่จะดึงข้อมูลวันที่และชื่อเทรนเนอร์ สำหรับ user_id นั้นๆ
                        $sql = "SELECT u.user_id, u.name, u.surname, s.service_date, s.trainer_id, s.details , s.with_trainer_id
                        FROM service_with_trainer s
                        JOIN users u ON s.trainer_id = u.user_id
                        WHERE s.user_id = ?"; // เปลี่ยน WHERE ให้ใช้ s.user_id
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $user_id); // กำหนดประเภทของตัวแปรเป็น integer
                        $stmt->execute();
                        $result = $stmt->get_result();
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $row['service_date'] . "</td>";
                            echo "<td>" . htmlspecialchars($row['trainer_id'] . ' ' . $row['name'] . ' ' . $row['surname']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['details']) . "</td>";

                            if (!empty($row['rating'])) {
                                echo '<td><span class="text-success">ให้คะแนนแล้ว!</span></td>';
                            } else {
                                echo '<td>';
                                echo '<button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#rateTrainerModal" onclick="setWithTrainerId(' . htmlspecialchars($row['with_trainer_id']) . ')">ให้คะแนน</button>';
                                echo '</td>';
                            }
                            echo "</tr>";
                        }

                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Modal สำหรับให้คะแนน -->
        <div class="modal fade" id="rateTrainerModal" tabindex="-1" aria-labelledby="rateTrainerModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="submit_rating.php">
                        <div class="modal-header">
                            <h5 class="modal-title" id="rateTrainerModalLabel">ให้คะแนนเทรนเนอร์</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="with_trainer_id" id="with_trainer_id" />
                            <div>
                                <label>คะแนน:</label>
                                <div class="star-rating">
                                    <input type="radio" id="star5" name="rating" value="5" required /><label for="star5" title="5 stars">&#9733;</label>
                                    <input type="radio" id="star4" name="rating" value="4" required /><label for="star4" title="4 stars">&#9733;</label>
                                    <input type="radio" id="star3" name="rating" value="3" required /><label for="star3" title="3 stars">&#9733;</label>
                                    <input type="radio" id="star2" name="rating" value="2" required /><label for="star2" title="2 stars">&#9733;</label>
                                    <input type="radio" id="star1" name="rating" value="1" required /><label for="star1" title="1 star">&#9733;</label>
                                </div>
                                <div class="mb-3">
                                    <label for="review" class="form-label">ความคิดเห็น:</label>
                                    <textarea class="form-control" id="review" name="review" rows="3" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                            <button type="submit" class="btn btn-primary">ส่งคะแนน</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



        </form>
    </div>
    </div>
    </div>
    <script>
        function setWithTrainerId(id) {
            document.getElementById('with_trainer_id').value = id;
        }
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
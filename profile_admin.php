<?php
session_start();
require_once 'dbConnect.php';

// ตรวจสอบว่ามีค่า $_SESSION['userid'] หรือไม่ก่อนที่จะเข้าถึงค่า
if (!isset($_SESSION['userid'])) {
   header("Location: index.php");
   exit(); // ออกจากการทำงานของสคริปต์
}

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!$conn) {
   die("Connection failed: " . mysqli_connect_error());
}

// ดึงข้อมูลผู้ใช้ที่ตรงกับ session user_id
$sql = "
    SELECT 
        user_id,
        picture,
        name, 
        surname, 
        gender, 
        userlevel, 
        address, 
        telephone, 
        birthday, 
        username, 
        password
    FROM users 
    WHERE user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['userid']);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
   die("Error running query: " . mysqli_error($conn));
}

$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>ข้อมูลส่วนตัว</title>
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
   <link href="https://fonts.googleapis.com/css2?family=Mali:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;1,200;1,300;1,400;1,500;1,600;1,700&family=Mitr:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
   <style>
      /* เปลี่ยนฟอนต์ทั้งหมดในหน้า */
      body,
      h3,
      h4,
      h5,
      p,
      div {
         font-family: 'Mali', sans-serif;
      }
   </style>

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
         background-color: #0036A9;
         /* สีพื้นหลัง */
         color: white;
         /* สีตัวอักษร */
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
         <div class="midde_cont">
            <div class="container-fluid">
               <div class="row column_title">
                  <div class="col-md-12">
                     <div class="page_title">
                        <h2>ข้อมูลส่วนตัว</h2>
                     </div>
                  </div>
               </div>
               <div class="row column1">
                  <div class="col-md-3"></div>
                  <div class="col-md-7">
                     <div class="white_shd full margin_bottom_30">
                        <div class="full graph_head">
                           <div class="heading1 margin_0">
                              <h2>รายละเอียดข้อมูลส่วนตัว</h2>
                           </div>
                           <div class="full price_table padding_infor_info">
                              <div class="row">
                                 <div class="col-lg-12">
                                    <div class="full dis_flex center_text">
                                       <?php if ($user) : ?>
                                          <div class="profile_contant">
                                             <div class="contact_inner">
                                                <img id="Preview" src="<?php echo !empty($user['picture']) ? 'uploads/' . $user['picture'] : ''; ?>" alt="Image Preview" style="max-width: 200px; ;?>">
                                                </p>
                                                <p><strong>ชื่อ: </strong><?php echo htmlspecialchars($user['name']); ?></p>
                                                <p><strong>นามสกุล: </strong><?php echo htmlspecialchars($user['surname']); ?></p>
                                                <p><strong>เพศ: </strong>
                                                   <?php
                                                   if ($user['gender'] == 0) {
                                                      echo 'ชาย';
                                                   } elseif ($user['gender'] == 1) {
                                                      echo 'หญิง';
                                                   } else {
                                                      echo 'ไม่ระบุ';
                                                   }
                                                   ?>
                                                </p>
                                                <p><strong>ระดับผู้ใช้: </strong>
                                                   <?php
                                                   switch ($user['userlevel']) {
                                                      case 0:
                                                         echo 'ผู้ดูแลระบบ';
                                                         break;
                                                      case 1:
                                                         echo 'อาจารย์';
                                                         break;
                                                      case 2:
                                                         echo 'เทรนเนอร์';
                                                         break;
                                                      case 3:
                                                         echo 'สมาชิก';
                                                         break;
                                                      default:
                                                         echo 'ไม่ระบุ';
                                                         break;
                                                   }
                                                   ?>
                                                </p>
                                                <p><strong>ที่อยู่: </strong><?php echo htmlspecialchars($user['address']); ?></p>
                                                <p><strong>โทรศัพท์: </strong><?php echo htmlspecialchars($user['telephone']); ?></p>
                                                <p><strong>วันเกิด: </strong><?php echo htmlspecialchars($user['birthday']); ?></p>
                                                <p><strong>ชื่อผู้ใช้: </strong><?php echo htmlspecialchars($user['username']); ?></p>
                                                <p><strong>รหัสผ่าน: </strong><?php echo htmlspecialchars($user['password']); ?></p>
                                                <a href="edit_profile admin.php?user_id=<?php echo $user['user_id']; ?>" class="btn btn-primary">แก้ไขข้อมูล</a>
                                             </div>
                                          </div>
                                       <?php else : ?>
                                          <p>ไม่มีข้อมูลผู้ใช้</p>
                                       <?php endif; ?>
                                    </div>
                                 </div>
                              </div>
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
</body>

</html>
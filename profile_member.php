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

// ดึงข้อมูลผู้ใช้ที่ตรงกับ session user_id
$sql = "
    SELECT 
        user_id,picture,name, surname, gender, userlevel, address, telephone, birthday, 
        username, password, type, recorddate, cardImage,
        CONCAT(name, ' ', surname) AS fullname
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
   <link rel="stylesheet" href="css/profile.css" />
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
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
   <!-- Font Awesome CSS -->
   <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
   <!-- ฟอนต์ -->
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
                     <h3>BRU FITNESS</h3>
                  </a>
               </div>
            </div>
         </div>
         <div class="sidebar_blog_2">
            <h4>สมาชิก</h4>
            <ul class="list-unstyled components">
               <li>
                  <a href="profile_member.php" class="dropdown-item active" aria-current="true"><i class="bi bi-person-badge"></i> <span>ข้อมูลส่วนตัว</span></a>
               </li>
               <li>
                  <a href="medical_member.php"><i class="bi bi-file-earmark-medical"></i> <span>ข้อมูลทางการแพทย์</span></a>
               </li>
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

         <div class="row column1">
            <div class="col-md-2"></div>
            <div class="col-md-10">
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
                                    <div class="container-layout">
                                       <!-- Sidebar -->
                                       <div class="sidebar">
                                          <div class="user-info">
                                             <!-- <p><strong>รูปภาพ: </strong> -->
                                             <img id="Preview"
                                                src="<?php echo !empty($user['picture']) ? 'uploads/' . $user['picture'] : ''; ?>"
                                                alt="Image Preview"
                                                width="150"
                                                height="200"
                                                class="rounded-circle"
                                                style="border-radius: 50%; object-fit: cover; max-width: 200px;"> </p>
                                             </p>
                                             <p>รหัสผู้ใช้ : <?php echo htmlspecialchars($user['user_id']); ?></p>
                                             <p><?php echo htmlspecialchars($user['fullname']); ?></p>
                                             <a href="edit_profile member.php?user_id=<?php echo $user['user_id']; ?>" class="btn btn-warning">แก้ไขข้อมูล</a>
                                             <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cardModal">
                                                บัตรสมาชิก
                                             </button>
                                             <a href="logout.php" class="btn btn-danger">ออกจากระบบ</a>
                                          </div>
                                       </div>

                                       <div class="main-content">
                                          <div class="form-group">
                                             <label>วัน/เดือน/ปีเกิด</label>
                                             <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['birthday']); ?>" readonly>
                                          </div>
                                          <div class="form-group">
                                             <label>เพศ</label>
                                             <input type="text" class="form-control"
                                                value="<?php
                                                         switch ($user['gender']) {
                                                            case 0:
                                                               echo 'ชาย';
                                                               break;
                                                            case 1:
                                                               echo 'หญิง';
                                                               break;                                        
                                                               default:
                                                               echo 'ไม่ระบุ';
                                                         }
                                                         ?>" readonly>
                                          </div>
                                          <div class="form-group">
                                             <label>ประเภทผู้ใช้</label>
                                             <input type="text" class="form-control"
                                                value="<?php
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
                                                         }
                                                         ?>" readonly>
                                          </div>
                                          <div class="form-group">
                                             <label>ประเภทสมาชิก</label>
                                             <input type="text" class="form-control"
                                                value="<?php
                                                         echo ($user['type'] == 1) ? 'บุคลากร' : (($user['type'] == 2) ? 'บุคคลทั่วไป' : 'ไม่ทราบประเภท');
                                                         ?>"
                                                readonly>
                                          </div>
                                          <div class="form-group">
                                             <label>ที่อยู่</label>
                                             <textarea class="form-control" readonly><?php echo htmlspecialchars($user['address']); ?></textarea>
                                          </div>
                                          <div class="form-group">
                                             <label>เบอร์โทรศัพท์</label>
                                             <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['telephone']); ?>" readonly>
                                          </div>
                                          <div class="form-group">
                                             <label>ชื่อผู้ใช้</label>
                                             <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                                          </div>
                                          <div class="form-group">
                                             <label>รหัสผ่าน</label>
                                             <input type="password" class="form-control" value="<?php echo htmlspecialchars($user['password']); ?>" readonly>
                                          </div>
                                          <div class="form-group">
                                             <label>วันที่สมัครสมาชิก</label>
                                             <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['recorddate']); ?>" readonly>
                                          </div>
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
   <!-- Modal -->
   <div class="modal fade" id="cardModal" tabindex="-1" aria-labelledby="cardModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="cardModalLabel">บัตรสมาชิก</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
               <!-- แสดงภาพบัตรสมาชิก -->
               <img src="cards/<?php echo htmlspecialchars($cardImage); ?>" alt="บัตรสมาชิก" class="img-fluid">
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
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
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
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
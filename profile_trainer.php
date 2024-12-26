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
   <!-- Font Awesome CSS -->
   <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
   <!-- ฟอนต์ -->
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
   <style>
      /* เปลี่ยนฟอนต์ทั้งหมดในหน้า */
      body,
      h3,
      h4,
      h5,
      p,
      div {
         font-family: "Chakra Petch", sans-serif;
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
            <h4>เทรนเนอร์</h4>
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
            <div class="container">
               <div class="row">
                  <!-- Sidebar Section -->
                  <div class="col-md-3 text-center">
                     <img id="Preview" src="<?php echo !empty($user['picture']) ? 'uploads/' . $user['picture'] : 'default-avatar.png'; ?>" alt="Profile Picture" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover;">
                     <p class="mt-3"><strong><?php echo htmlspecialchars($user['name'] . ' ' . $user['surname']); ?></strong></p>
                     <p>รหัสเทรนเนอร์: <?php echo htmlspecialchars($user['user_id']); ?></p>
                     <a href="edit_profile trainer.php?user_id=<?php echo $user['user_id']; ?>" class="btn btn-primary">แก้ไขข้อมูล</a>
                     </div>

                  <!-- Details Section -->
                  <div class="col-md-9">
                     <div class="card">
                        <div class="card-header bg-warning text-dark">
                           <h4>แก้ไขข้อมูลส่วนตัว</h4>
                        </div>
                        <div class="card-body">
                           <form>
                              <!-- Gender -->
                              <div class="mb-3">
                                 <label class="form-label">เพศ</label>
                                 <div>
                                    <input type="radio" id="male" name="gender" value="0" <?php echo $user['gender'] == 0 ? 'checked' : ''; ?>>
                                    <label for="male">ชาย</label>
                                    <input type="radio" id="female" name="gender" value="1" <?php echo $user['gender'] == 1 ? 'checked' : ''; ?> class="ms-3">
                                    <label for="female">หญิง</label>
                                 </div>
                              </div>

                              <!-- Name -->
                              <div class="mb-3">
                                 <label class="form-label">ชื่อ</label>
                                 <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>">
                              </div>
                              <div class="mb-3">
                                 <label class="form-label">นามสกุล</label>
                                 <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['surname']); ?>">
                              </div>

                              <!-- Phone -->
                              <div class="mb-3">
                                 <label class="form-label">เบอร์โทรศัพท์</label>
                                 <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['telephone']); ?>">
                              </div>

                              <!-- Address -->
                              <div class="mb-3">
                                 <label class="form-label">ที่อยู่</label>
                                 <textarea class="form-control"><?php echo htmlspecialchars($user['address']); ?></textarea>
                              </div>
                           </form>
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
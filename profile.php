<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ส่วนของเทรนเนอร์</title>
    <!-- Basic meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Mobile metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1">
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

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

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

        .modal-body .form-label {
            font-family: 'Mali', sans-serif;

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
            background-color: #0036A9;
            /* สีพื้นหลัง */
            color: white;
            /* สีตัวอักษร */
        }
        
        .brand-link p{
            color:black;
            text-align: center;
            font-size: large;
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
                            <p>
                            <img src="assets\images\logo\bru-logo.png" alt="BRU Logo"  style=" width: 90%;  "><br>
                            BRU FITNESS</p>
                        </a>
                        
                    </div>
                </div>
            </div>
            <div class="sidebar_blog_2">
                <h4>เทรนเนอร์</h4>
                <ul class="list-unstyled components">
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
        </div>


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
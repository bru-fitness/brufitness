<?php
session_start();
include 'dbConnect.php'; // เรียกใช้ไฟล์ dbConnect.php เพื่อเชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าได้เข้าสู่ระบบแล้วหรือยัง
if (isset($_SESSION['username'])) {
    header("Location: home.php");
    exit();
}

// ตรวจสอบว่าเป็นการเรียกหน้าโดยการส่งข้อมูลผ่าน POST หรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    // $passwordenc = md5($password); // เข้ารหัสผ่านด้วย md5

    // เตรียมคำสั่ง SQL เพื่อลดความเสี่ยงในการถูกโจมตีด้วย SQL Injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if (isset($_SESSION['error'])) {
        echo "<script>alert('" . $_SESSION['error'] . "');</script>";
        unset($_SESSION['error']); // ลบข้อความแจ้งเตือนเมื่อแสดงเสร็จ
    }
    
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BRUFITNESS</title>
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Roboto:wght@500;700&display=swap"
        rel="stylesheet">
    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />
    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- Template Stylesheet -->
    <link href="css/login.css" rel="stylesheet">


</head>

<body>

    <?php if (isset($_SESSION['success'])) : ?>
        <div class="success">
            <?php
            echo $_SESSION['success'];
            ?>
        </div>
    <?php endif; ?>


    <?php if (isset($_SESSION['error'])) : ?>
        <div class="error">
            <?php
            echo $_SESSION['error'];
            ?>
        </div>
    <?php endif; ?>

    <!-- Sign In Start -->
    <div class="container">
        <div class="login-container">
            <h3 class="text-primary">BRU FITNESS</h3>
            <!-- ตรวจสอบว่ามีการส่งข้อความ error หรือไม่ -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?php
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>
            <form action="login.php" method="post">
                <label for="username">ชื่อผู้ใช้</label>
                <input type="text" class="form-control" id="username" name="username" required>
                <!-- <input type="password" class="form-control" id="password" name="password" required> -->
                <label for="password" class="form-label">รหัสผ่าน</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" required
                        oninput="checkPasswordStrength()">
                    <div class="input-group-append">
                        <span class="input-group-text"
                            onclick="togglePassword('password', 'password-icon')">
                            <i class="fa fa-eye-slash" id="password-icon"></i>
                        </span>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <!-- <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="exampleCheck1">
                                    <label class="form-check-label" for="exampleCheck1">Check me out</label>
                                </div> -->
                    <a href="#">ลืมรหัสผ่านใช่หรือไม่</a>
                </div>
                <button type="submit">Log in</button>
            </form>
        </div>
    </div>
    <div class="video-container">
        <video autoplay muted loop id="bg-video">
            <source src="assets/images/bg/lat-pulldown.mp4" type="video/mp4">
        </video>
    </div>
    <script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === "password") {
            input.type = "text";
            icon.classList.replace("fa-eye-slash", "fa-eye");
        } else {
            input.type = "password";
            icon.classList.replace("fa-eye", "fa-eye-slash");
        }
    }
    </script>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/chart/chart.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>
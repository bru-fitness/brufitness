<?php
session_start();
require_once 'dbConnect.php'; // เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล
// รับ payrate_id
if (isset($_GET['id'])) {
    $payrate_id = $_GET['id'];
    $sql = "SELECT * FROM payrate WHERE payrate_id = $payrate_id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
} else {
    die("Invalid ID");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลค่าบริการ</title>
    <!-- Basic meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Mobile metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <!-- Site metas -->
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
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <style>
        body,
        h3,
        h4,
        p,
        div {
            font-family: "Chakra Petch", sans-serif;
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
    <form action="update_payrate.php" method="post">
        <input type="hidden" name="payrate_id" value="<?php echo $row['payrate_id']; ?>">
        <label>ประเภท:</label>
        <input type="text" name="type" value="<?php echo $row['type']; ?>"><br>
        <label>ค่าสมัครสมาชิก:</label>
        <input type="number" name="payrate_signup" value="<?php echo $row['payrate_signup']; ?>"><br>
        <label>ค่าต่ออายุสมาชิก:</label>
        <input type="number" name="payrate_renewal" value="<?php echo $row['payrate_renewal']; ?>"><br>
        <label>ค่าเข้าใช้บริการ:</label>
        <input type="number" name="payrate_service" value="<?php echo $row['payrate_service']; ?>"><br>
        <button type="submit">บันทึก</button>
    </form>
</body>
</html>
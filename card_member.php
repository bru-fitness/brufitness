<?php
session_start();

// ตรวจสอบว่าค่า user_id ถูกตั้งค่าใน session หรือไม่
if (!isset($_SESSION['user_id'])) {
    echo "Error: user_id not set in session.";
    exit();
}

$user_id = $_SESSION['user_id'];
$card_image_path = 'cards/renewal/renewal_card_' . $user_id . '.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บัตรสมาชิก</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        .card-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .member-card {
            text-align: center;
            padding: 20px;
            border: 1px solid #ddd;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .member-card img {
            max-width: 100%;
            height: auto;
        }

        .member-card h3 {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container card-container">
    <div class="member-card">
        <?php
        if (file_exists($card_image_path)) {
            echo "<img src='$card_image_path' alt='บัตรสมาชิก'>";
            echo "<h3>บัตรสมาชิกของคุณ</h3>";
        } else {
            echo "<p>ไม่พบบัตรสมาชิก</p>";
        }
        ?>
        <a href="users.php" class="btn btn-primary mt-3">ตกลง</a>
    </div>
</div>

</body>
</html>

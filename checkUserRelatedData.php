<?php
require_once 'dbConnect.php';

$user_id = $_GET['user_id'];
$response = ['hasRelatedData' => false];

// ตัวอย่างการตรวจสอบข้อมูลที่เกี่ยวข้อง
$query = "SELECT COUNT(*) as count FROM related_table WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

if ($row['count'] > 0) {
    $response['hasRelatedData'] = true;
}

header('Content-Type: application/json');
echo json_encode($response);
?>

<?php
require_once 'dbConnect.php';

$query = "SELECT * FROM users";
$result = mysqli_query($conn, $query);

$users = [];
while ($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}

mysqli_close($conn);

echo json_encode(['status' => 'success', 'users' => $users]);
?>

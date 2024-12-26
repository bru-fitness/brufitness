<?php
include 'dbConnect.php';

$name = $_POST['name'];
$surname = $_POST['surname'];
$gender = $_POST['gender'];
$user_type = $_POST['user_type'];
$picture = $_POST['picture'];
$birthday = $_POST['brithday'];
$username = $_POST['username'];
$password = $_POST['password'];
$address = $_POST['address'];
$telephone = $_POST['telephone'];
$medical_id = $_POST['medical_id'];
$recorddate = $_POST['recorddate'];
$record_id = $_SESSION['userid'] ?? 1; // record ผู้บันทึก

$insert = "INSERT INTO users(name,surname,gender,user_type,picture,brithday,username,password,address,telephone,medical_id, recorddate,record_id) 
VALUES ('$name','$surname','$gender','$user_type','$username','$password','$address','$telephone','$medical_id','$recorddate','$record_id')";
$result = mysqli_query($conn, $insert);

if($result){
    header("refresh:2; url=users.php");
}
?>
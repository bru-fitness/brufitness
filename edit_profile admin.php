<!-- แก้ใขข้อมูลส่วนตัวจากเทรนเนอร์ -->
<?php
session_start();

// รวมไฟล์การเชื่อมต่อฐานข้อมูล
include('dbConnect.php');

// ตรวจสอบว่าได้ส่ง user_id มาในคำขอหรือไม่
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // ดึงข้อมูลผู้ใช้จากฐานข้อมูล
    $query = "SELECT * FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        echo "ไม่พบผู้ใช้ที่ระบุ";
        exit;
    }
} else {
    echo "ไม่มี user_id ระบุ";
    exit;
}
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    // ดำเนินการดึงข้อมูลจากฐานข้อมูลเพื่อแสดงในฟอร์ม
} else {
    echo "ไม่มี user_id ระบุ";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลส่วนตัว</title>
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
    <link href="https://fonts.googleapis.com/css2?family=Mali:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;1,200;1,300;1,400;1,500;1,600;1,700&family=Mitr:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body,
        h3,
        h4,
        p,
        div {
            font-family: 'Mali', sans-serif;
        }

        .container {
            display: flex;
            justify-content: center;
            max-width: 85%;
            margin-top: 40px;
            /* เพิ่ม margin-top เพื่อไม่ให้ topbar บัง */
        }

        .form-container {
            padding: 20px;
            background-color: #f7f7f7;
            border-radius: 10px;
            box-sizing: border-box;
            /* Ensure padding and border are included in the element's total width and height */
        }

        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }

        .form-group {
            flex: 1;
            padding: 10px;
            box-sizing: border-box;
            /* Ensure padding is included in the element's total width and height */
        }

        .button-container {
            display: flex;
            justify-content: flex-end;
            /* Align items to the right */
        }

        .button-container .btn {
            font-size: 1.0rem;
            /* Increase button text size */
            padding: 10px 20px;
            /* Increase button padding */
        }

        .content {
            padding-top: 20px;
            /* เพิ่มระยะห่างจากด้านบน */
        }

        .image-container {
            display: flex;
            justify-content: center;
            /* จัดกึ่งกลางแนวนอน */
            align-items: center;
            /* จัดกึ่งกลางแนวตั้ง */
            height: 250px;
            /* ตั้งค่าความสูงตามต้องการ */
            margin-bottom: 20px;
            /* ระยะห่างระหว่างรูปภาพและ input file */
        }

        .image-container img {
            max-width: 200px;
            /* ตั้งค่าความกว้างสูงสุดของรูปภาพ */
            max-height: 200px;
            /* ตั้งค่าความสูงสูงสุดของรูปภาพ */
            object-fit: contain;
            /* ทำให้รูปภาพอยู่ในขนาดของคอนเทนเนอร์ */
        }

        .file-input-container {
            display: flex;
            align-items: center;
            justify-content: center;
            /* ทำให้เนื้อหาภายในคอนเทนเนอร์จัดชิดกึ่งกลาง */
            gap: 20px;
            /* เว้นระยะห่างระหว่างปุ่ม */
            margin-top: 10px;
        }

        .file-input-container input[type="file"] {
            display: none;
            /* ซ่อน input file */
        }

        .file-input-container button {
            display: inline-block;
        }
    </style>

    <script>
        function editUser(userId) {
            $.ajax({
                url: 'viewUser.php',
                type: 'GET',
                data: {
                    user_id: userId
                },
                success: function(response) {
                    var userData = JSON.parse(response);
                    if (userData.success) {
                        $('#editUserId').val(userData.user.user_id);
                        $('#editName').val(userData.user.name);
                        $('#editSurname').val(userData.user.surname);
                        $('#editGender').val(userData.user.gender);
                        $('#editUserLevel').val(userData.user.userlevel);

                        if (userData.user.userlevel == '3') {
                            $('#editTypeContainer').show();
                            $('#editType').val(userData.user.type);
                        } else {
                            $('#editTypeContainer').hide();
                        }

                        $('#editUsername').val(userData.user.username);
                        $('#editPassword').val(userData.user.password);
                        $('#editConfirmPassword').val(userData.user.password);
                        $('#editBirthday').val(userData.user.birthday);
                        $('#editAddress').val(userData.user.address);
                        $('#editTelephone').val(userData.user.telephone);
                        $('#editStatus').val(userData.user.status);
                        $('#editRecordDate').val(userData.user.recorddate);
                        $('#editRecordId').val(userData.user.record_id);

                        // Display the image preview
                        if (userData.user.picture) {
                            $('#editPreview').attr('src', 'uploads/' + userData.user.picture).show();
                        } else {
                            $('#editPreview').hide();
                        }

                        var editUserModal = new bootstrap.Modal(document.getElementById('editUserModal'));
                        editUserModal.show();
                    } else {
                        console.error("User not found: " + userData.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }

        document.getElementById('editUserLevel').addEventListener('change', function() {
            var userLevel = this.value;
            var typeContainer = document.getElementById('editTypeContainer');
            if (userLevel == '3') {
                typeContainer.style.display = 'block';
            } else {
                typeContainer.style.display = 'none';
            }
        });

        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('editPreview');
                output.src = reader.result;
                output.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>



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
        </div>

        <div class="container mt-5 content">
            <div class="container">
                <div class="form-container">
                    <h3 class="text-center">แก้ไขข้อมูลผู้ใช้</h3>
                    <form id="editUserForm" action="updateProfile admin.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="user_id" id="editUserId" value="<?php echo $user['user_id']; ?>">
                        <div class="form-group">
                            <div class="image-container">
                                <img id="editPreview" src="<?php echo !empty($user['picture']) ? 'uploads/' . $user['picture'] : ''; ?>" alt="Image Preview" style="<?php echo empty($user['picture']) ? 'display: none;' : ''; ?>">
                            </div>
                            <div class="file-input-container">
                                <input type="file" class="form-control-file" name="picture" id="editPicture" accept="image/*" onchange="previewImage(event)" style="display: none;">
                                <button type="button" id="addPictureButton" class="btn btn-secondary" onclick="document.getElementById('editPicture').click();" style="<?php echo !empty($user['picture']) ? 'display: none;' : ''; ?>">เพิ่มรูปภาพ</button>
                                <button type="button" id="changePictureButton" class="btn btn-secondary" onclick="document.getElementById('editPicture').click();" style="<?php echo empty($user['picture']) ? 'display: none;' : ''; ?>">เปลี่ยนรูปภาพ</button>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="editUserIdDisplay" class="form-label">รหัสผู้ใช้</label>
                                <input type="text" class="form-control" id="editUserIdDisplay" value="<?php echo $user['user_id']; ?>" disabled>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="editName" class="form-label">ชื่อ</label>
                                <input type="text" class="form-control" id="editName" name="name" value="<?php echo $user['name']; ?>" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="editSurname" class="form-label">นามสกุล</label>
                                <input type="text" class="form-control" id="editSurname" name="surname" value="<?php echo $user['surname']; ?>" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="editBirthday" class="form-label">วันเกิด</label>
                                <input type="date" name="birthday" class="form-control" id="editBirthday" value="<?php echo $user['birthday']; ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="editGender" class="form-label">เพศ</label>
                                <select class="form-control" id="editGender" name="gender" required>
                                    <option value="">เลือกเพศ</option>
                                    <option value="0" <?php if ($user['gender'] == '0') echo 'selected'; ?>>ชาย</option>
                                    <option value="1" <?php if ($user['gender'] == '1') echo 'selected'; ?>>หญิง</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="editUserLevel" class="form-label">ประเภทผู้ใช้</label>
                                <select class="form-control" id="editUserLevel" name="userlevel" required disabled>
                                    <?php if ($user['userlevel'] == '0'): ?>
                                        <option value="admin" selected>ผู้ดูแลระบบ</option>
                                        <option value="lecturer">อาจารย์</option>
                                        <option value="member">สมาชิก</option>
                                        <option value="trainer">เทรนเนอร์</option>
                                    <?php elseif ($user['userlevel'] == '1'): ?>
                                        <option value="admin">ผู้ดูแลระบบ</option>
                                        <option value="lecturer" selected>อาจารย์</option>
                                        <option value="member">สมาชิก</option>
                                        <option value="trainer">เทรนเนอร์</option>
                                    <?php elseif ($user['userlevel'] == '2'): ?>
                                        <option value="admin">ผู้ดูแลระบบ</option>
                                        <option value="lecturer">อาจารย์</option>
                                        <option value="member">สมาชิก</option>
                                        <option value="trainer" selected>เทรนเนอร์</option>
                                    <?php elseif ($user['userlevel'] == '3'): ?>
                                        <option value="admin">ผู้ดูแลระบบ</option>
                                        <option value="lecturer">อาจารย์</option>
                                        <option value="member" selected>สมาชิก</option>
                                        <option value="trainer">เทรนเนอร์</option>
                                    <?php else: ?>
                                        <option value="" selected>ไม่รู้จัก</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-6" id="editTypeContainer" style="<?php if ($user['userlevel'] != '3') echo 'display: none;'; ?>">
                                <label for="editType" class="form-label">ประเภทสมาชิก</label>
                                <select name="type" class="form-control" id="editType" name="type" required disabled>
                                    <option value="">เลือกประเภทสมาชิก</option>
                                    <option value="1" <?php if ($user['type'] == '1') echo 'selected'; ?>>บุคคลทั่วไป</option>
                                    <option value="2" <?php if ($user['type'] == '2') echo 'selected'; ?>>บุคลากร</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="editAddress" class="form-label">ที่อยู่</label>
                                <input type="text" class="form-control" name="address" id="editAddress" value="<?php echo $user['address']; ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="editTelephone" class="form-label">เบอร์โทร</label>
                                <input type="text" name="telephone" class="form-control" id="editTelephone" value="<?php echo $user['telephone']; ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="editUsername" class="form-label">ชื่อผู้ใช้</label>
                                <input type="text" name="username" class="form-control" id="editUsername" value="<?php echo $user['username']; ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="editPassword" class="form-label">รหัสผ่าน</label>
                                <input type="password" name="password" class="form-control" id="editPassword" value="<?php echo $user['password']; ?>">
                                <input type="checkbox" onclick="togglePasswordVisibility()"> แสดงรหัสผ่าน
                                <div id="passwordError" style="color: red; display: none;">รหัสผ่านไม่ตรงกัน</div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="editConfirmPassword" class="form-label">ยืนยันรหัสผ่าน</label>
                                <input type="password" name="confirm_password" class="form-control" id="editConfirmPassword" value="<?php echo $user['password']; ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="editRecordDate" class="form-label">วันที่บันทึกเข้าระบบ</label>
                                <input type="datetime-local" name="recorddate" class="form-control" id="editRecordDate" value="<?php echo date('Y-m-d\TH:i', strtotime($user['recorddate'])); ?>" readonly>
                            </div>
                        </div>
                        <div class="form-row button-container">
                            <button type="submit" class="btn btn-success" name="editUser">บันทึกข้อมูล</button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                function togglePasswordVisibility() {
                    var passwordField = document.getElementById("editPassword");
                    if (passwordField.type === "password") {
                        passwordField.type = "text";
                    } else {
                        passwordField.type = "password";
                    }
                }
            </script>
            <script>
                document.getElementById('editUserForm').addEventListener('submit', function(event) {
                    var password = document.getElementById('editPassword').value;
                    var confirmPassword = document.getElementById('editConfirmPassword').value;
                    var passwordError = document.getElementById('passwordError');

                    if (password !== confirmPassword) {
                        event.preventDefault(); // ป้องกันการส่งฟอร์ม
                        passwordError.style.display = 'block';
                    } else {
                        passwordError.style.display = 'none';
                    }
                });

                function togglePasswordVisibility() {
                    var passwordField = document.getElementById('editPassword');
                    var confirmPasswordField = document.getElementById('editConfirmPassword');
                    if (passwordField.type === "password") {
                        passwordField.type = "text";
                        confirmPasswordField.type = "text";
                    } else {
                        passwordField.type = "password";
                        confirmPasswordField.type = "password";
                    }
                }
            </script>

            <script>
                function previewImage(event) {
                    var reader = new FileReader();
                    reader.onload = function() {
                        var output = document.getElementById('editPreview');
                        output.src = reader.result;
                        output.style.display = 'block';

                        // Hide the "add" button and show the "change" button
                        document.getElementById('addPictureButton').style.display = 'none';
                        document.getElementById('changePictureButton').style.display = 'block';
                    }
                    reader.readAsDataURL(event.target.files[0]);
                }

                document.getElementById('editPicture').addEventListener('change', function() {
                    const fileInput = document.getElementById('editPicture');
                    const addPictureButton = document.getElementById('addPictureButton');
                    const changePictureButton = document.getElementById('changePictureButton');

                    if (fileInput.files && fileInput.files[0]) {
                        addPictureButton.style.display = 'none';
                        changePictureButton.style.display = 'inline-block';
                    }
                });

                // Initial button display
                window.onload = function() {
                    const pictureSrc = document.getElementById('editPreview').src;
                    const addPictureButton = document.getElementById('addPictureButton');
                    const changePictureButton = document.getElementById('changePictureButton');

                    if (pictureSrc) {
                        addPictureButton.style.display = 'none';
                        changePictureButton.style.display = 'inline-block';
                    } else {
                        addPictureButton.style.display = 'inline-block';
                        changePictureButton.style.display = 'none';
                    }
                };
            </script>

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
            <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>

</html>
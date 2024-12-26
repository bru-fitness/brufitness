<?php

require_once 'dbConnect.php';

if (isset($_GET['service_id'])) {
    $service_id = $_GET['service_id'];

    // Fetch the service data from the database
    $sql = "SELECT * FROM service_usage WHERE service_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $service = $result->fetch_assoc();
    } else {
        echo "ไม่พบข้อมูลบริการ";
        exit;
    }
} else {
    echo "ไม่มีการระบุ ID ของบริการ";
    exit;
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลการเข้าใช้บริการ</title>
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
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;1,200;1,300;1,400;1,500;1,600;1,700&family=Mitr:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
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

        .modal-body .form-label {
            font-family: "Chakra Petch", sans-serif;

        }
    </style>
</head>

<body>
    
    <div class="inner_container">
        <!-- Sidebar and navigation content as before -->
        <div class="container mt-5 content">
            <div class="container">
                <div class="form-container">
                    <h3 class="text-center">แก้ไขข้อมูลการเข้าใช้บริการ</h3>
                    <form id="editUserForm" action="update_service.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="service_id" id="editServiceUsageId" value="<?php echo $service['service_id']; ?>">
                        <div class="form-group">
                            <label for="editUserIdDisplay" class="form-label">รหัสผู้ใช้</label>
                            <input type="text" class="form-control" id="editUserIdDisplay" value="<?php echo $service['user_id']; ?>" disabled required>
                        </div>
                        <div class="form-group">
                            <label for="payment_type_id" class="form-label">ประเภทการชำระเงิน</label>
                            <select class="form-select" id="payment_type_id" name="payment_type_id" required>
                                <option value="1" <?php echo $service['payment_type_id'] == 1 ? 'selected' : ''; ?>>เงินสด</option>
                                <option value="2" <?php echo $service['payment_type_id'] == 2 ? 'selected' : ''; ?>>คูปอง</option>
                                <option value="3" <?php echo $service['payment_type_id'] == 3 ? 'selected' : ''; ?>>อื่นๆ</option>
                            </select>
                        </div>
                        <!-- <div class="form-group mb-3" id="otherPaymentTypeContainer" style="display: <?php echo $service['payment_type_id'] == 3 ? 'block' : 'none'; ?>;">
                            <label for="other_payment_type" class="form-label">ระบุประเภทการชำระเงิน (ถ้าเลือก 'อื่นๆ')</label>
                            <input type="text" class="form-control" id="other_payment_type" name="other_payment_type" value="<?php echo $service['other_payment_type']; ?>" required>
                        </div> -->
                        <div class="form-group">
                            <label for="editServiceFee">ค่าบริการ</label>
                            <input type="text" class="form-control" name="service_fee" id="editServiceFee" value="<?php echo $service['service_fee']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="editServiceDate">วันที่เข้าใช้บริการ</label>
                            <input type="datetime-local" class="form-control" name="service_date" id="editServiceDate" value="<?php echo date('Y-m-d\TH:i', strtotime($service['service_date'])); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='service.php';">ยกเลิก</button>
                        </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Show/hide the 'other payment type' input field
        document.getElementById('payment_type_id').addEventListener('change', function() {
            var otherPaymentTypeContainer = document.getElementById('otherPaymentTypeContainer');
            if (this.value == '3') {
                otherPaymentTypeContainer.style.display = 'block';
            } else {
                otherPaymentTypeContainer.style.display = 'none';
            }
        });
    </script>
    <script>
        // เมื่อกดปุ่มแก้ไข ให้ดึงข้อมูลของบริการนั้นมาแสดงในฟอร์มของโมดอล
        $(document).ready(function() {
            // เมื่อกดปุ่มแก้ไข ให้ดึงข้อมูลของบริการนั้นมาแสดงในฟอร์มของโมดอล
            $(document).on('click', '.editBtn', function() {
                var serviceId = $(this).data('service-id');
                // ส่ง Ajax request เพื่อดึงข้อมูลของบริการที่ต้องการแก้ไข
                $.ajax({
                    url: 'get_service_usage.php',
                    type: 'GET',
                    data: {
                        id: serviceId
                    },
                    success: function(response) {
                        // สมมติว่า response เป็นข้อมูล JSON ของ service usage
                        var data = JSON.parse(response);
                        $('#editServiceUsageId').val(data.service_id);
                        $('#editUserId').val(data.user_id); // เพิ่มการแสดงรหัสผู้ใช้
                        $('#payment_type_id').val(data.payment_type_id).change(); // เลือกประเภทการชำระเงิน
                        $('#editServiceFee').val(data.service_fee);
                        $('#editServiceDate').val(data.service_date);

                        // ถ้าเลือกประเภทการชำระเงินเป็น 'อื่นๆ'
                        if (data.payment_type_id == '3') {
                            $('#otherPaymentTypeContainer').show();
                            $('#other_payment_type').val(data.other_payment_type); // ใส่ค่าในฟิลด์อื่นๆ
                        } else {
                            $('#otherPaymentTypeContainer').hide();
                        }
                    }
                });
            });

            // เมื่อกดปุ่มลบ ให้ส่ง request เพื่อทำการลบ
            $(document).on('click', '.deleteBtn', function() {
                var serviceId = $(this).data('service-id');
                if (confirm('คุณแน่ใจหรือว่าต้องการลบข้อมูลนี้?')) {
                    $.ajax({
                        url: 'delete_service_usage.php',
                        type: 'POST',
                        data: {
                            id: serviceId
                        },
                        success: function(response) {
                            if (response == 'success') {
                                // ลบแถวออกจากตาราง หรือโหลดหน้าซ้ำ
                                location.reload();
                            } else {
                                alert('เกิดข้อผิดพลาดในการลบข้อมูล');
                            }
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>

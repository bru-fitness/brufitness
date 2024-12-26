<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มผู้ใช้</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
    <h2>เพิ่มผู้ใช้</h2>
    <form action="save.php" method="POST">
        <div class="form-group">
            <label for="">1</label>
            <input type="text" name="name">
            <label for="">2</label>
            <input type="text" name="surname"><br>
            
        
        </div>
<br>
        <input type="submit" value="บันทึกข้อมูล">
    </form>
    </div>

    <a  href="home.php">กลับหน้าแรก</a>
</body>
</html>
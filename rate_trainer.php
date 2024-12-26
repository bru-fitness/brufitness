<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once 'dbConnect.php';

    $with_trainer_id = $_POST['with_trainer_id'];
    $review = intval($_POST['review']);

    // Insert the new review
    $sql = "UPDATE service_with_trainer SET review = ? WHERE with_trainer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $review, $with_trainer_id);

    if ($stmt->execute()) {
        // Recalculate the overall rating
        $sql = "SELECT AVG(review) as rating FROM service_with_trainer WHERE trainer_id = (SELECT trainer_id FROM service_with_trainer WHERE with_trainer_id = ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $with_trainer_id);
        $stmt->execute();
        $stmt->bind_result($new_rating);
        $stmt->fetch();

        // Update the overall rating in the service_with_trainer table
        $stmt->close();

        $sql = "UPDATE service_with_trainer SET rating = ? WHERE trainer_id = (SELECT trainer_id FROM service_with_trainer WHERE with_trainer_id = ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("di", $new_rating, $with_trainer_id);

        if ($stmt->execute()) {
            echo "การให้คะแนนสำเร็จ";
            header("Location: alltrainer.php");
            exit();
        } else {
            echo "เกิดข้อผิดพลาดในการบันทึกคะแนน: " . $stmt->error;
        }
    } else {
        echo "เกิดข้อผิดพลาดในการบันทึกรีวิว: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

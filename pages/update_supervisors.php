<?php
session_start();
include("connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['session_id'], $_POST['room_id'], $_POST['supervisors'])) {
    $session_id = (int)$_POST['session_id'];
    $room_id = (int)$_POST['room_id'];
    $new_supervisors = array_map('intval', $_POST['supervisors']);

    // Get current supervisors
    $current_supervisors = [];
    $res_current = mysqli_query($con, "
        SELECT teacher_id 
        FROM session_rooms 
        WHERE session_id = $session_id AND room_id = $room_id
    ");
    while ($row = mysqli_fetch_assoc($res_current)) {
        $current_supervisors[] = (int)$row['teacher_id'];
    }

    // إذا لا يوجد أي تغيير، لا نفعل شيئًا
    if ($new_supervisors === $current_supervisors) {
        echo "<script>alert('No changes made.'); window.location.href = 'your_page.php';</script>";
        exit;
    }

    // احسب من تم حذفه فعليًا
    $to_remove = array_diff($current_supervisors, $new_supervisors);
    foreach ($to_remove as $tid) {
        // حذف من session_rooms
        mysqli_query($con, "
            DELETE FROM session_rooms 
            WHERE session_id = $session_id AND room_id = $room_id AND teacher_id = $tid
        ");
        // طرح ساعة
        mysqli_query($con, "
            UPDATE teachers 
            SET used_hours = GREATEST(used_hours - 1, 0)
            WHERE id = $tid
        ");
    }

    // احسب من تم إضافته فعليًا
    $to_add = array_diff($new_supervisors, $current_supervisors);
    foreach ($to_add as $tid) {
        // إضافة
        mysqli_query($con, "
            INSERT INTO session_rooms (session_id, room_id, teacher_id)
            VALUES ($session_id, $room_id, $tid)
        ");
        // زيادة ساعة
        mysqli_query($con, "
            UPDATE teachers 
            SET used_hours = used_hours + 1
            WHERE id = $tid
        ");
    }

    echo "<script>alert('Supervisors updated successfully!'); window.location.href = 'all_exam_schedules.php';</script>";
} else {
    echo "Invalid request.";
}
?>

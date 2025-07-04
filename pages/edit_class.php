<?php 
session_start();
include("connection.php");
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_class'])) {
    $class_id = (int)$_POST['class_id'];
    $class_name = mysqli_real_escape_string($con, $_POST['class_name']);
    $student_count = (int)$_POST['student_count'];
    $supervisor_count = (int)$_POST['supervisor_count'];
    $id_department = (int)$_POST['id_deprtment'];
    $check_query = "SELECT id FROM rooms WHERE name = '$class_name' AND id_department = $id_department AND id != $class_id";
    $check_result = mysqli_query($con, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
        echo "<script>alert('This department already has a room with this name assigned.'); window.location.href='manager.php#classes';</script>";
        exit();
    }
    $update_query = "UPDATE rooms SET name = '$class_name', student_count = $student_count, supervisor_count = $supervisor_count, id_department = $id_department WHERE id = $class_id";
    if (mysqli_query($con, $update_query)) {
        echo "<script>alert('Class updated successfully.'); window.location.href='manager.php#classes';</script>";
        exit();
    } else {
        echo "<script>alert('Error updating class!'); window.location.href='manager.php#classes';</script>";
        exit();
    }
}
?>


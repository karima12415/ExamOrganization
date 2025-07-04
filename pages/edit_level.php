<?php 
session_start();
include("connection.php");
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_level'])) {
    $level_id = (int)$_POST['level_id'];
    $level_name = mysqli_real_escape_string($con, $_POST['level_name']);
    $student_count = (int)$_POST['student_count'];
    $specialty_id = (int)$_POST['specialty_id'];
    $check_query = "SELECT id FROM levels WHERE name = '$level_name' AND specialty_id = $specialty_id AND id != $level_id";
    $check_result = mysqli_query($con, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
        echo "<script>alert('This specialty already has a level with this name.'); window.location.href='manager.php#level';</script>";
        exit();
    }
    $update_query = "UPDATE levels SET name = '$level_name', student_count = $student_count, specialty_id = $specialty_id WHERE id = $level_id";
    if (mysqli_query($con, $update_query)) {
        echo "<script>alert('Level updated successfully.'); window.location.href='manager.php#level';</script>";
        exit();
    } else {
        echo "<script>alert('Error updating level.'); window.location.href='manager.php#level';</script>";
        exit();
    }
}
?>



<?php
session_start();
include("connection.php");
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_level'])) {
    $level_name = mysqli_real_escape_string($con, $_POST['level_name']);
    $specialty_id = (int)$_POST['specialty_id'];
    $student_count = (int)$_POST['student_count'];
    $check_query = "SELECT id FROM levels WHERE name = '$level_name' AND specialty_id = $specialty_id";
    $check_result = mysqli_query($con, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
        echo "<script>alert('This specialty already has a level assigned.'); window.location.href='manager.php#level';</script>";
        exit();
    }
    $add_level_query = "INSERT INTO levels (name, specialty_id, student_count) VALUES ('$level_name', $specialty_id, $student_count)";
    if (mysqli_query($con, $add_level_query)) {
        echo "<script>alert('Level added successfully.'); window.location.href='manager.php#level';</script>";
        exit();
    } else {
        echo "<script>alert('Error adding level!'); window.location.href='manager.php#level';</script>";
        exit();
    }
}
?>
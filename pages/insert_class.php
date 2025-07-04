<?php
session_start();
include("connection.php"); 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_class'])){
    // Retrieve the form data
    $class_name = mysqli_real_escape_string($con, $_POST['class_name']);
    $student_count = (int)$_POST['student_count'];
    $supervisor_count = (int)$_POST['supervisor_count'];
    $id_department = (int)$_POST['id_deprtment'];
    // Prepare the SQL insert statement
    $check_query = "SELECT id FROM rooms WHERE name = '$class_name' AND id_department = $id_department";
    $check_result = mysqli_query($con, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
        echo "<script>alert('This deprtment already has a room assigned.'); window.location.href='manager.php#classes';</script>";
        exit();
    }
    $insert_query = "INSERT INTO rooms (name, student_count, supervisor_count, id_department) VALUES ('$class_name', $student_count, $supervisor_count, $id_department)";
    // Execute the query
    if (mysqli_query($con, $insert_query)) {
        echo "<script>alert('Class added successfully.'); window.location.href='manager.php#classes';</script>";
        exit();
    } else {
        $_SESSION['message'] = "" . mysqli_error($con);
        echo "<script>alert('Error adding class!'); window.location.href='manager.php#classes';</script>";
        exit();
    }
}
?>

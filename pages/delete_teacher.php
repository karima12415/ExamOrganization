<?php
session_start();
include("connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_type']) && $_POST['delete_type'] === 'teacher') {
    $delete_id = (int)$_POST['delete_id'];

    // Delete from teacher_modules first to remove references
    $delete_modules_query = "DELETE FROM teacher_modules WHERE teacher_id = $delete_id";
    if (mysqli_query($con, $delete_modules_query)) {
        // If module deletion is successful, delete the teacher record
        $delete_teacher_query = "DELETE FROM teachers WHERE id = $delete_id";
        if (mysqli_query($con, $delete_teacher_query)) {
            echo "<script>alert('Teacher and associated module assignments deleted successfully.'); window.location.href='manager.php';</script>";
            exit();
        } else {
            echo "<script>alert('Error deleting teacher: " . mysqli_error($con) . "'); window.location.href='manager.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Error deleting teacher modules: " . mysqli_error($con) . "'); window.location.href='manager.php';</script>";
        exit();
    }
} else {
    // Invalid request: redirect back
    echo "<script>alert('Invalid request.'); window.location.href='manager.php';</script>";
    exit();
}
?>

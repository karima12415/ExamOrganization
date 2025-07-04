<?php
session_start();
include("connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_teacher'])) {
    $teacher_id = (int)$_POST['teacher_id'];
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $hourly_size = (int)$_POST['hourly_size'];
    $modules = $_POST['modules']; // Array of selected module IDs
    // Check if the email already exists for another teacher
    $email_check_query = "SELECT id FROM teachers WHERE email = '$email' AND id != $teacher_id";
    $email_check_result = mysqli_query($con, $email_check_query);
    if (mysqli_num_rows($email_check_result) > 0) {
        // Email already exists
        echo "<script>alert('Cannot save: The email is already in use by another teacher.'); window.location.href='manager.php';</script>";
        exit();
    }
    // Update teacher details
    $update_query = "UPDATE teachers SET name = '$name', email = '$email', hourly_size = $hourly_size WHERE id = $teacher_id";
    if (mysqli_query($con, $update_query)) {
        // Update teacher_modules table
        // First, delete existing module assignments
        $delete_modules_query = "DELETE FROM teacher_modules WHERE teacher_id = $teacher_id";
        mysqli_query($con, $delete_modules_query);

        // Then, insert new module assignments
        foreach ($modules as $module_id) {
            $module_id = (int)$module_id; // Ensure it's an integer
            $insert_module_query = "INSERT INTO teacher_modules (teacher_id, module_id) VALUES ($teacher_id, $module_id)";
            mysqli_query($con, $insert_module_query);
        }

        // Success message
        echo "<script>alert('Teacher updated successfully.'); window.location.href='manager.php';</script>";
        exit();
    } else {
        // Error message
        echo "<script>alert('Error updating teacher: " . mysqli_error($con) . "'); window.location.href='manager.php';</script>";
        exit();
    }
} else {
    // Invalid request: redirect back
    echo "<script>alert('Invalid request.'); window.location.href='manager.php';</script>";
    exit();
}
?>

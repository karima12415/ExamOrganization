<?php
session_start();
include("connection.php");
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_teacher'])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $hourly_size = (int)$_POST['hourly_size'];
    $modules = $_POST['modules']; // هذه ستكون مصفوفة من المعرفات
    $id_faculty = (int)$_POST['id_faculty'];
    // Check if the email already exists
    $check_email_query = "SELECT * FROM teachers WHERE email = '$email'";
    $result = mysqli_query($con, $check_email_query);
    if (mysqli_num_rows($result) > 0) {
        // Email already exists
        echo "<script>alert('Cannot save: The email is already in use by another teacher.'); window.location.href='manager.php';</script>";
        exit();
    } else {
        // Insert the teacher into the teachers table with isfree = 0
        $insert_teacher_query = "INSERT INTO teachers (name, email, hourly_size, id_faculty, isfree) VALUES ('$name', '$email', $hourly_size, $id_faculty, 0)";
        if (mysqli_query($con, $insert_teacher_query)) {
            $teacher_id = mysqli_insert_id($con); // Get the new teacher ID
            // Insert the associated modules into the teacher_modules table
            foreach ($modules as $module_id) {
                $module_id = (int)$module_id; // Ensure it's an integer
                $insert_module_query = "INSERT INTO teacher_modules (teacher_id, module_id) VALUES ($teacher_id, $module_id)";
                mysqli_query($con, $insert_module_query);
            }
            echo "<script>alert('Teacher added successfully.'); window.location.href='manager.php';</script>";
            exit();
        } else {
            echo "<script>alert('Error adding teacher.'); window.location.href='manager.php';</script>";
            exit();
        }
    }
}
?>

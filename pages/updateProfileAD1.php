<?php
session_start();
include("connection.php");
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['email']) && $_SESSION['role'] === 'admin1') {
    $email = $_SESSION['email'];
    $new_email = mysqli_real_escape_string($con, $_POST['email']);
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];

    // Validate new email
    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format!'); window.location.href='home.php#Profile';</script>";
        exit;
    }

    // Get current password hash from DB
    $query = "SELECT password FROM admin1 WHERE email = '$email'";
    $result = mysqli_query($con, $query);

    if (!$result || mysqli_num_rows($result) !== 1) {
        echo "<script>alert('User not found!'); window.location.href='home.php#Profile';</script>";
        exit;
    }

    $row = mysqli_fetch_assoc($result);
    $current_hashed_password = $row['password'];

    // If user wants to change password
    if (!empty($new_password)) {
        if (strlen($new_password) < 10) {
            echo "<script>alert('Password must be at least 10 characters long!'); window.location.href='home.php#Profile';</script>";
            exit;
        }

        if (empty($old_password)) {
            echo "<script>alert('Old password is required!'); window.location.href='home.php#Profile';</script>";
            exit;
        }

        // Verify old password
        if (!password_verify($old_password, $current_hashed_password)) {
            echo "<script>alert('Old password is incorrect!'); window.location.href='home.php#Profile';</script>";
            exit;
        }

        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE admin1 SET email = '$new_email', password = '$hashed_password' WHERE email = '$email'";
    } else {
        $sql = "UPDATE admin1 SET email = '$new_email' WHERE email = '$email'";
    }

    // Execute update
    if (mysqli_query($con, $sql)) {
        $_SESSION['email'] = $new_email; // Update session email
        echo "<script>alert('update successfully!'); window.location.href='home.php#Profile';</script>";
        exit;
    } else {
        echo "<script>alert('Error updating profile!'); window.location.href='home.php#Profile';</script>";
        exit;
    }

} else {
    echo "<script>alert('Unauthorized access.'); window.location.href='home.php#Profile';</script>";
    exit;
}
?>

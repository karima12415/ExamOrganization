<?php
header('Content-Type: application/json');
include("connection.php");

// Get all POST data
$id = mysqli_real_escape_string($con, $_POST['id'] ?? '');
$name = mysqli_real_escape_string($con, $_POST['name'] ?? '');
$email = mysqli_real_escape_string($con, $_POST['email'] ?? '');
$oldPassword = $_POST['old_password'] ?? '';
$newPassword = $_POST['new_password'] ?? '';
$role = mysqli_real_escape_string($con, $_POST['role'] ?? 'Organizer');
// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}
// Check if email exists (excluding current user)
$checkEmail = mysqli_query($con, "SELECT id FROM organizer WHERE email = '$email' AND id != '$id'");
if (mysqli_num_rows($checkEmail) > 0) {
    echo json_encode(['success' => false, 'message' => 'Email already exists']);
    exit;
}
// Fetch current hashed password
$PasswordResult = mysqli_query($con, "SELECT password FROM organizer WHERE id = '$id'");
if (!$PasswordResult || mysqli_num_rows($PasswordResult) === 0) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}
$PasswordHash = mysqli_fetch_assoc($PasswordResult)['password'];

// Password update logic
$passwordUpdate = '';
if (!empty($newPassword)) {
    if (strlen($newPassword) < 10) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 10 characters long']);
        exit;
    }
    if (empty($oldPassword)) {
        echo json_encode(['success' => false, 'message' => 'Old password is required']);
        exit;
    }
    if (!password_verify($oldPassword, $PasswordHash)) {
        echo json_encode(['success' => false, 'message' => 'Old password is incorrect']);
        exit;
    }

    // Check if new password already exists (among all users)
    $checkPassword = mysqli_query($con, "SELECT password FROM organizer");
    while ($row = mysqli_fetch_assoc($checkPassword)) {
        if (password_verify($newPassword, $row['password'])) {
            echo json_encode(['success' => false, 'message' => 'This password is already in use. Please choose another one.']);
            exit;
        }
    }

    // Hash and prepare password update
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $passwordUpdate = ", password = '$hashedPassword'";
}
$updateQuery = "UPDATE organizer SET 
                name = '$name',
                email = '$email'
                $passwordUpdate
                WHERE id = '$id'";

if (mysqli_query($con, $updateQuery)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Update failed: ' . mysqli_error($con)]);
}

mysqli_close($con);
?>
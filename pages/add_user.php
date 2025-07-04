<?php
header('Content-Type: application/json');
include("connection.php");

// Get all POST data with validation
$name = mysqli_real_escape_string($con, $_POST['name'] ??'');
$email = mysqli_real_escape_string($con, $_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role = mysqli_real_escape_string($con, $_POST['role'] ?? 'Admin');
$universityId = mysqli_real_escape_string($con, $_POST['univer'] ?? '');
$facultyId = mysqli_real_escape_string($con, $_POST['facul'] ?? '');
// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}
// Check if email exists
$checkEmail = mysqli_query($con, "SELECT id FROM admin2 WHERE email = '$email'");
if (mysqli_num_rows($checkEmail) > 0) {
    echo json_encode(['success' => false, 'message' => 'Email already registered']);
    exit;
}
if (strlen($password) < 10) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 10 characters long']);
    exit;
}
// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
// Insert new user with all fields
$query = "INSERT INTO admin2 (name, email, role, password, id_univ, id_faculty) 
          VALUES ('$name', '$email', '$role', '$hashedPassword', '$universityId', '$facultyId')";
if (mysqli_query($con, $query)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add user: ' . mysqli_error($con)]);
}

mysqli_close($con);
?>
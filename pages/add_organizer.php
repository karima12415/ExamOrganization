<?php
header('Content-Type: application/json');
include("connection.php");

// Get all POST data with validation
$name = mysqli_real_escape_string($con, $_POST['name'] ??'');
$email = mysqli_real_escape_string($con, $_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role = mysqli_real_escape_string($con, $_POST['role'] ?? 'Organizer');
$id_univ = mysqli_real_escape_string($con, $_POST['id_univ'] ?? '');
$id_faculty = mysqli_real_escape_string($con, $_POST['id_faculty'] ?? '');
$added_by_admin2 = mysqli_real_escape_string($con, $_POST['added_by_admin2'] ?? '');
// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}
// Check if email exists
$checkEmail = mysqli_query($con, "SELECT id FROM organizer WHERE email = '$email'");
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
$query = "INSERT INTO organizer (name, email, role, password, id_univ, id_faculty, added_by_admin2)
          VALUES ('$name', '$email', '$role', '$hashedPassword', '$id_univ', '$id_faculty', '$added_by_admin2')";

if (mysqli_query($con, $query)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add user: ' . mysqli_error($con)]);
}

mysqli_close($con);
?>
<?php
include("connection.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($con, $_POST["email"]);
    $password = $_POST["password"]; // Don't escape the password
    
    // Function to verify login with password hashing
    function verifyLogin($con, $table, $email, $password) {
        $stmt = mysqli_prepare($con, "SELECT * FROM `$table` WHERE email=?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }
        return false;
    }

    // Check admin1
    if ($user = verifyLogin($con, 'admin1', $email, $password)) {
        $_SESSION['id_admin1'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = 'admin1';
        header("Location: home.php");
        exit;
    }

    // Check admin2
    if ($user = verifyLogin($con, 'admin2', $email, $password)) {
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = 'admin2';
        $_SESSION['id_admin2'] = $user['id'];
        header("Location: homeAD2.php");
        exit;
    }

    // Check organizer
    if ($user = verifyLogin($con, 'organizer', $email, $password)) {
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = 'organizer';
        $_SESSION['id_organizer'] = $user['id'];
        header("Location: homeorgan.php");
        exit;
    }

    // Invalid credentials
    $_SESSION['login_error'] = "Invalid email or password";
    header("Location: dashboard.php");
    exit;
}
?>
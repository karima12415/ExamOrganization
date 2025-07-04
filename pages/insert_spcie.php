<?php
session_start();
include("connection.php");
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_specialty'])) {
    $specialty_name = mysqli_real_escape_string($con, $_POST['specialty_name']);
    $id_filiere = (int)$_POST['id_filiere'];
    $check_query = "SELECT id FROM specialties WHERE name = '$specialty_name' AND id_filiere = $id_filiere";
    $check_result = mysqli_query($con, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
        echo "<script>alert('This specialty already exists in the selected filiere.'); window.location.href='manager.php#dashboard-page';</script>";
        exit();
    }
    $query = "INSERT INTO specialties (name, id_filiere) VALUES ('$specialty_name', $id_filiere)";
    if (mysqli_query($con, $query)) {
        echo "<script>alert('Specialty added successfully.!'); window.location.href='manager.php#dashboard-page';</script>";
        exit();
    } else {
        echo "<script>alert('Error adding specialty.!'); window.location.href='manager.php#dashboard-page';</script>";
        exit();
    }
}
?>
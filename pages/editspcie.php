<?php 
session_start();
include("connection.php");
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_specialty'])) {
    $specialty_id = (int)$_POST['specialty_id'];
    $specialty_name = mysqli_real_escape_string($con, $_POST['specialty_name']);
    $id_filiere = (int)$_POST['id_filiere'];
    $update_query = "UPDATE specialties SET name = '$specialty_name', id_filiere = $id_filiere WHERE id = $specialty_id";
    if (mysqli_query($con, $update_query)) {
        echo "<script>alert('Specialty updated successfully.!'); window.location.href='manager.php#dashboard-page';</script>";
        exit();
    } else {
        echo "<script>alert('Error updating specialty.!'); window.location.href='manager.php#dashboard-page';</script>";
        exit();
    }
}
?>
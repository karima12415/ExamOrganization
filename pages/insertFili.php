<?php
    include("connection.php");
    if (isset($_POST['submit'])) {
        $id_department = $_POST['id_department'];
        $name_filiere = $_POST['name_filiere'];
        // Check if Department ID exists
        $check_department = mysqli_query($con, "SELECT * FROM `department` WHERE id_department = '$id_department'");
        if (mysqli_num_rows($check_department) == 0) {
            echo "<script>alert('Department ID does not exist!'); window.location.href='home.php#Filiere';</script>";
            exit();
        }
        // Check if the Filiere already exists under the same Department
        $check_query = mysqli_query($con, "SELECT * FROM `filiere` WHERE name_filiere = '$name_filiere' AND id_department = '$id_department'");
        if (mysqli_num_rows($check_query) > 0) {
            echo "<script>alert('Filiere with the same name already exists in this department!'); window.location.href='home.php#Filiere';</script>";
            exit();
        }
        // Insert Filiere into the database
        $query = mysqli_query($con, "INSERT INTO `filiere`(`name_filiere`, `id_department`) VALUES ('$name_filiere', '$id_department')");
        if ($query) {
            echo "<script>alert('Filiere added successfully!'); window.location.href='home.php#Filiere';</script>";
        } else {
            echo "<script>alert('Failed to add Filiere.'); window.location.href='home.php#Filiere';</script>";
        }
    }
?>

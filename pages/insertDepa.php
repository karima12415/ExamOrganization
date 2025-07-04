<?php
    include("connection.php");
    if (isset($_POST['submit'])) {
        $id_faculty = $_POST['id_faculty'];
        $name_department = $_POST['name_deprtment'];
        $nb_of_rooms = $_POST['nbOfRoom'];
        $floor = $_POST['floor'];
        // Check if Faculty ID exists
        $check_faculty = mysqli_query($con, "SELECT * FROM `faculty` WHERE id_faculty = '$id_faculty'");
        if (mysqli_num_rows($check_faculty) == 0) {
            echo "<script>alert('Faculty ID does not exist!'); window.location.href='home.php#Department';</script>";
            exit();
        }
        // Check if the Department already exists under the same Faculty
        $check_query = mysqli_query($con, "SELECT * FROM `department` WHERE name_deprtment = '$name_department' AND id_faculty = '$id_faculty'");
        if (mysqli_num_rows($check_query) > 0) {
            echo "<script>alert('Department with the same name already exists in this faculty!'); window.location.href='home.php#Department';</script>";
            exit();
        }
        // Insert Department into the database
        $query = mysqli_query($con, "INSERT INTO `department`(`name_deprtment`, `nbOfRoom`, `floor`, `id_faculty`) VALUES ('$name_department', '$nb_of_rooms', '$floor', '$id_faculty')");
        if ($query) {
            echo "<script>alert('Department added successfully!'); window.location.href='home.php#Department';</script>";
        } else {
            echo "<script>alert('Failed to add department.'); window.location.href='home.php#Department';</script>";
        }
    }
?>

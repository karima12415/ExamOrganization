<?php
    include("connection.php");
    if (isset($_POST['submit'])) {
        $id_univ = $_POST['id-univ'];
        $name_faculty = $_POST['name-faculty'];
        $logo_faculty = $_FILES['logo-Faculty'];
        // Check if university ID exists
        $check_univ = mysqli_query($con, "SELECT * FROM `university` WHERE id_univ = '$id_univ'");
        if (mysqli_num_rows($check_univ) == 0) {
            echo "<script>alert('University ID does not exist!'); window.location.href='home.php#Faculty';</script>";
            exit();
        }
        // Check if the faculty already exists in the same university
        $check_query = mysqli_query($con, "SELECT * FROM `faculty` WHERE name_faculty = '$name_faculty' AND id_univ = '$id_univ'");
        if (mysqli_num_rows($check_query) > 0) {
            echo "<script>alert('Faculty with the same name already exists in this university!'); window.location.href='home.php#Faculty';</script>";
            exit();
        }
        // Process file upload
        $logo_name = $_FILES['logo-faculty']['name'];
        if(!empty($logo_name)){
            $logo_loc = $_FILES['logo-faculty']['tmp_name'];
            $logo_dest = "logoFact/".$logo_name;
            move_uploaded_file($logo_loc,'logoFact/'.$logo_name);
        }
        else{
            $logo_dest = "logoFact/logoempty.PNG";
        }
        // Insert faculty into the database
        $query = mysqli_query($con, "INSERT INTO `faculty`(`name_faculty`, `image_faculty`, `id_univ`) VALUES ('$name_faculty','$logo_dest','$id_univ')");
        if ($query) {
            echo "<script>alert('Faculty added successfully!'); window.location.href='home.php#Faculty';</script>";
        } else {
            echo "<script>alert('Failed to add faculty.'); window.location.href='home.php#Faculty';</script>";
        }
    }
?>

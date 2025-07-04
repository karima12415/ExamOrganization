<?php 
session_start();
$email = $_SESSION['email'];
$id_organizer=$_SESSION['id_organizer'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Organizer Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/all.min.css">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="website icon" type="png" href="/images/logo.png">
</head>
<body>
    <?php
    include("connection.php"); 
    $recordO = mysqli_query($con, "SELECT id_univ,id_faculty,name FROM `organizer` WHERE email='$email'AND id='$id_organizer'");
    $dataO = mysqli_fetch_array($recordO);
    $id_univ = $dataO['id_univ'];
    $id_faculty = $dataO['id_faculty'];
    $recordU = mysqli_query($con, "SELECT * FROM `university` WHERE id_univ = $id_univ");
    $recordF = mysqli_query($con, "SELECT * FROM `faculty` WHERE id_faculty = $id_faculty");
    $dataU = mysqli_fetch_array($recordU);
    $dataF = mysqli_fetch_array($recordF);
    ?>
    <div class="navigationW">
        <div class="imglogo">
            <img src="<?php echo $dataU['imageU']; ?>">
        </div>
        <div class="infoselcted">
            <h3><?php echo "{$dataU['name_univ']} - {$dataU['adress_univ']}"; ?></h3>
            <h4><?php echo $dataF['name_faculty']; ?></h4>
        </div>
        <div class="imglogo">
            <img class="logo-preview" src="<?php echo $dataF['image_faculty']; ?>">
        </div>
    </div>
    <div class="container_homeAD2">
        <div class="text_cont">
            <p>Welcome <span><?php echo $dataO['name']; ?></span> to the Exam Organization Platform. You can now manage exam schedules, supervise planning, and collaborate with faculty.</p>
            <div class="btn_cont">
                <button class="btn_profile" onclick="window.location.href='updateProfileOrgan.php'"><i class="fa-solid fa-gear"></i> Settings</button>
                <button class="btn_addOrg" onclick="window.location.href='manager.php'"><i class="fa-solid fa-calendar-days"></i> Schedule Exams</button>
            </div>
        </div>
        <div class="img_cont">
            <img src="/images/shoudle.png" alt="">
        </div>
    </div>
</body>
</html>

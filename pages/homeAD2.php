<?php 
session_start();
$email =$_SESSION['email'];     
$id_admin2=$_SESSION['id_admin2'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/all.min.css">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="website icon" type="png" href="/images/logo.png">
    <title>Exam Organization</title>
</head>
<body>
    <?php
        include("connection.php");
        $recordA = mysqli_query($con,"SELECT id_univ,id_faculty,name FROM `admin2` WHERE email='$email' AND id='$id_admin2' ");
        $dataA= mysqli_fetch_array($recordA);
        $id_univ = $dataA['id_univ'];
        $id_faculty = $dataA['id_faculty'];
        $recordU = mysqli_query($con,"SELECT * FROM `university` WHERE id_univ = $id_univ");
        $recordF = mysqli_query($con,"SELECT * FROM `faculty` WHERE id_faculty = $id_faculty");
        $dataU = mysqli_fetch_array($recordU);
        $dataF = mysqli_fetch_array($recordF);
    ?>
    <div class="navigationW">
        <div class="imglogo">
            <img  src="<?php echo $dataU['imageU']?>">
        </div>
        <div class="infoselcted">
            <h3><?php echo "{$dataU['name_univ']}-{$dataU['adress_univ']}"?></h3>
            <h4><?php echo $dataF['name_faculty']?></h4>
            
        </div>
        <div class="imglogo">
            <img class="logo-preview" src="<?php echo $dataF['image_faculty']?>">
        </div> 
    </div>
    <div class="container_homeAD2">
        <div class="text_cont">
            <p>Welcome <span><?php echo $dataA['name']?></span>to exam Organization platform a smart solution designed to simplify and streamline exam planning, scheduling, and supervision.</p>
            <div class="btn_cont">
                <button class="btn_profile" onclick="window.location.href='updateProfileAD2.php'"><i class="fa-solid fa-gear"></i>   Settings</button>
                <button class="btn_addOrg" onclick="window.location.href='userOrganizer.php'"><i class="fa-solid fa-user-plus"></i>  Add Organizer</button>
            </div>
        </div>
        <div class="img_cont">
            <img src="/images/homead2.png" alt="">
        </div>
        
    </div>

</body>
</html>

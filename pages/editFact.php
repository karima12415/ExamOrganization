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
        $ID = $_GET['Id'];
        $record = mysqli_query($con,"SELECT * FROM `faculty` WHERE id_faculty= $ID");
        $data = mysqli_fetch_array($record);
    ?>
     <div class="navigationS">
        <div class="logoS">
            <img src="/images/logo.png" alt="Logo">
            <h1>Exam<span class="danger">Org</span></h1> 
        </div>
        <li><a href="/pages/home.php"><i class="fa-solid fa-circle-left"></i></a></li>
    </div>
    <div class="container" style="padding: 90px;">
        <h2>Edit Faculty</h2>
        <form action="" method="post" enctype="multipart/form-data" class="form">
            <div class="input-group">
                <input type="text" name="name-faculty" value="<?php echo $data['name_faculty']?>" required><label>Name Faculty</label>
            </div>
            <div class="input-group">
                <td><input class="input-file" type="file" value="<?php echo $data['image_faculty']?>" name="logo-faculty"><img class="logo-preview" src="<?php echo $data['image_faculty']?>" width='50' height='50'></td><!--<label>Logo University</label>-->
                    <input type="hidden" name="id_faculty" value="<?php echo $data['id_faculty']?>">
            </div> 
                <input class="btn-submitF" type="submit" value="Edit" name="submit"> 
        </form>  
        
    </div> 
    <!--update code ----------------------------------------------------------- --> 
    <?php 
         include("connection.php");
         if(isset($_POST['submit'])){
            $id=$_POST['id_faculty'];
            $Name_faculty=$_POST['name-faculty'];

            $logo_faculty = $_FILES['logo-Faculty'];
            $logo_name = $_FILES['logo-faculty']['name'];
            if (!empty($logo_name)) {
                $logo_loc = $_FILES['logo-faculty']['tmp_name'];
                $logo_dest = "logoFact/".$logo_name;
                move_uploaded_file($logo_loc,'logoFact/'.$logo_name);
            }
            else{
                $getOldLogo = mysqli_query($con, "SELECT image_faculty FROM faculty WHERE id_faculty = '$id'");
                $oldData = mysqli_fetch_assoc($getOldLogo);
                $logo_dest = $oldData['image_faculty'];
            }
            mysqli_query($con,"UPDATE `faculty` SET `name_faculty`='$Name_faculty',`image_faculty`='$logo_dest' WHERE id_faculty ='$id'");
            echo "<script>window.location.href='home.php#Faculty';</script>";   
        }
    ?>
</body>
</html>
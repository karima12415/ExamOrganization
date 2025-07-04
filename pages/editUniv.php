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
        $record = mysqli_query($con,"SELECT * FROM `university` WHERE id_univ = $ID");
        $data = mysqli_fetch_array($record);
    ?>
    <div class="navigationS">
        <div class="logoS">
            <img src="/images/logo.png" alt="Logo">
            <h1>Exam<span class="danger">Org</span></h1> 
        </div>
        <li><a href="/pages/home.php"><i class="fa-solid fa-circle-left"></i></a></li>
    </div>
    <div class="container" style="padding: 75px;">
        <h2>Edit university</h2>
        <form action="" method="post" enctype="multipart/form-data" class="form">
            <div class="input-group">
                <input type="text" value="<?php echo $data['name_univ']?>" name="name-univ" required><label>Name University</label>
            </div>
            <div class="input-group">
                <input type="text" value="<?php echo $data['state_univ']?>" name="state-univ" required><label>State University</label>
            </div>
            <div class="input-group">
                <input type="text" value="<?php echo $data['adress_univ']?>" name="adress-univ" required><label>Adress University</label>
            </div>
            <div class="input-group">
                <td><input class="input-file" type="file" value="<?php echo $data['imageU']?>" name="logo-univ"><img class="logo-preview" src="<?php echo $data['imageU']?>" width='50' height='50'></td><!--<label>Logo University</label>-->
                    <input type="hidden" name="id_univ" value="<?php echo $data['id_univ']?>">
            </div>  
                <input class="btn-submit" type="submit" value="Edit" name="submit"> 
        </form> 
    </div> 
        <!--update code ----------------------------------------------------------- --> 
        <?php 
         include("connection.php");
         if(isset($_POST['submit'])){
            $id=$_POST['id_univ'];
            $Name_univ=$_POST['name-univ'];
            $State_univ=$_POST['state-univ']; 
            $Adress_univ=$_POST['adress-univ'];

            $Logo_univ=$_FILES['logo-univ']; 
            $logo_name=$_FILES['logo-univ']['name'];
            if(!empty($logo_name)){
                $logo_loc=$_FILES['logo-univ']['tmp_name'];
                $logo_des="logoUniv/".$logo_name;
                move_uploaded_file($logo_loc,'logoUniv/'.$logo_name);
            }
            else{
                $getOldLogo = mysqli_query($con, "SELECT imageU FROM university WHERE id_univ = '$id'");
                $oldData = mysqli_fetch_assoc($getOldLogo);
                $logo_des = $oldData['imageU'];
            }
            mysqli_query($con,"UPDATE `university` SET `name_univ`='$Name_univ',`state_univ`='$State_univ',`adress_univ`='$Adress_univ',`imageU`='$logo_des' WHERE id_univ='$id'");
            echo "<script>window.location.href='home.php#University';</script>";    
        }
        ?>
</body>
</html>
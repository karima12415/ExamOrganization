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
        $record = mysqli_query($con,"SELECT * FROM `department` WHERE id_department= $ID");
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
        <h2>Edit Department</h2>
        <form action="" method="post" enctype="multipart/form-data" class="form" >
            <div class="input-group">
                <input type="text" name="name_deprtment" value="<?php echo $data['name_deprtment']?>" required><label>Name Department</label>
            </div>
            <div class="input-group">
                <input type="number" name="nbOfRoom" value="<?php echo $data['nbOfRoom']?>"  required><label>Number of rooms</label>
            </div>
            <div class="input-group">
                <input type="text" name="floor" value="<?php echo $data['floor']?>" required><label>Number of floor</label>
                <input type="hidden" name="id_department" value="<?php echo $data['id_department']?>">
            </div>
               <br><input class="btn-submit" type="submit" value="Edit" name="submit">
        </form>
    </div> 
    <?php 
         include("connection.php");
         if(isset($_POST['submit'])){
            $id=$_POST['id_department'];
            $Name_deprtment=$_POST['name_deprtment'];
            $NbOfRoom=$_POST['nbOfRoom'];
            $Floor=$_POST['floor'];
            mysqli_query($con,"UPDATE `department` SET `name_deprtment`='$Name_deprtment',`nbOfRoom`='$NbOfRoom',`floor`='$Floor' WHERE id_department='$id'");
            echo "<script>window.location.href='home.php#Department';</script>";   
        }
    ?>    
</body>
</html>
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
        $record = mysqli_query($con, "SELECT * FROM `filiere` WHERE id_filiere = $ID");
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
        <h2>Edit Filiere</h2>
        <form action="" method="post" enctype="multipart/form-data" class="form">
            <div class="input-group">
                <input type="text" name="name_filiere" value="<?php echo $data['name_filiere']; ?>" required><label>Name Filiere</label>
            </div>
            <input type="hidden" name="id_filiere" value="<?php echo $data['id_filiere']; ?>">
           <br>
            <input class="btn-submit" type="submit" value="Edit" name="submit">
        </form>
    </div> 
    <?php 
        if (isset($_POST['submit'])) {
            $id = $_POST['id_filiere'];
            $name_filiere = $_POST['name_filiere'];
            mysqli_query($con, "UPDATE `filiere` SET `name_filiere` = '$name_filiere' WHERE id_filiere = '$id'");
            echo "<script>window.location.href='home.php#Filiere';</script>";    
        }
    ?>    
</body>
</html>

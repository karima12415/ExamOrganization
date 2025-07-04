<?php
include("connection.php");
$Id = $_GET['Id'];
mysqli_query($con,"DELETE FROM `filiere` WHERE 	id_filiere=$Id");
echo "<script>window.location.href='home.php#Filiere';</script>";
?>
<?php
include("connection.php");
$Id = $_GET['Id'];
mysqli_query($con,"DELETE FROM `university` WHERE id_univ=$Id");
echo "<script>window.location.href='home.php#University';</script>";    
?>
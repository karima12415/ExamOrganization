<?php
include("connection.php");
$Id = $_GET['Id'];
mysqli_query($con,"DELETE FROM `department` WHERE id_department=$Id");
echo "<script>window.location.href='home.php#Department';</script>"; 
?>
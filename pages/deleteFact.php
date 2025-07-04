<?php
include("connection.php");
$Id = $_GET['Id'];
mysqli_query($con,"DELETE FROM `faculty` WHERE id_faculty=$Id");
echo "<script>window.location.href='home.php#Faculty';</script>"; 
?>
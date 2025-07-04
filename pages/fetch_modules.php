<?php
session_start();
include("connection.php");
if (isset($_GET['level_id'])) {
    $level_id = (int)$_GET['level_id'];
    $query = "SELECT id, name FROM modules WHERE  level_id = $level_id";
    $result = mysqli_query($con, $query);
    $modules = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $modules[] = $row;
    }
    echo json_encode($modules);
}
?>

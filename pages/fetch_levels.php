<?php
include('connection.php');
if (isset($_GET['specialty_id'])) {
    $specialty_id = (int)$_GET['specialty_id'];
    $level_query = "SELECT id, name FROM levels WHERE specialty_id = $specialty_id";
    $result = mysqli_query($con, $level_query);
    $levels = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $levels[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($levels);
}
?>


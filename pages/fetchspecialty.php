<?php
function databaseconnection(){
    $mysqli = new mysqli('localhost','root','','exam-org');
    if($mysqli->connect_errno != 0){
        die("Error connecting database".$mysqli->connect_error);
    }
    return $mysqli;
} 
function fetchSpecialties($con) {
    $query = "SELECT * FROM specialties";
    $result = mysqli_query($con, $query);
    $specialties = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $specialties[] = $row;
    }
    return $specialties;
}
function fetchLevels() {
    global $con;
    $levels = [];
    $query = "
        SELECT levels.*, specialties.name AS specialty_name 
        FROM levels 
        INNER JOIN specialties ON levels.specialty_id = specialties.id 
        ORDER BY levels.id DESC
    ";
    $result = mysqli_query($con, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $levels[] = $row;
        }
    }
    return $levels;
}
function fetchModules(){
    global $con;
    $modules = [];
    $query = "
        SELECT m.id, m.name, l.name AS level_name, s.name AS specialty_name
        FROM modules m
        INNER JOIN levels l ON m.level_id = l.id
        INNER JOIN specialties s ON l.specialty_id = s.id
    ";
    $result = mysqli_query($con, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $modules[] = $row;
        }
    }
    return $modules;
}
function fetchRooms(){
    global $con;
    $rooms = [];
    $query = "SELECT * FROM rooms";
    $result = mysqli_query($con, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)){
            $rooms[] = $row;
        }
    }
    return $rooms;
}
function fetchdepartment($id_faculty){
    $mysqli = databaseconnection();
    $data = [];
    if(!$mysqli){
        return false;
    }
    $id_faculty = $mysqli->real_escape_string($id_faculty);
    $res = $mysqli->query("SELECT * FROM `department` WHERE id_faculty = '$id_faculty'");
    while($row = $res->fetch_assoc()){
        $data[] = $row;
    }
    $mysqli->close();
    return json_encode($data);
}
$departments = [];
if (isset($id_faculty)) {
    $departments = json_decode(fetchdepartment($id_faculty), true);
}

?>
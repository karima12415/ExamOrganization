<?php
function databaseconnection(){
    $mysqli = new mysqli('localhost','root','','exam-org');
    if($mysqli->connect_errno != 0){
        die("Error connecting database".$mysqli->connect_error);
    }
    return $mysqli;
}

// Fetch universities
function getuniversity(){
    $mysqli = databaseconnection();
    if(!$mysqli){
        return false;
    }
    $res = $mysqli->query("SELECT * FROM `university`");
    while($row = $res->fetch_assoc()){
        $data[] = $row;
    }
    return $data;
}
// Fetch faculties based on university ID
if (isset($_GET["id_univ"])) {
   echo getfaculty($_GET["id_univ"]);
   exit;
}
function getfaculty($id_univ){
    $mysqli = databaseconnection();
    $data = [];
    if(!$mysqli){
        return false;
    }
    $id_univ = $mysqli->real_escape_string($id_univ);
    $res = $mysqli->query("SELECT * FROM `faculty` WHERE id_univ = '$id_univ'");
    while($row = $res->fetch_assoc()){
        $data[] = $row;
    }
    $mysqli->close();
    return json_encode($data);
}

// Fetch departments based on faculty ID
if (isset($_GET["id_faculty"])) {
    echo getdepartment($_GET["id_faculty"]);
    exit;
}
function getdepartment($id_faculty){
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

// Fetch filières based on department ID
if (isset($_GET["id_department"])) {
    echo getfiliere($_GET["id_department"]);
    exit;
}
function getfiliere($id_department){
    $mysqli = databaseconnection();
    $data = [];

    if(!$mysqli){
        return false;
    }

    $id_department = $mysqli->real_escape_string($id_department);
    $res = $mysqli->query("SELECT * FROM `filiere` WHERE id_department = '$id_department'");

    while($row = $res->fetch_assoc()){
        $data[] = $row;
    }

    $mysqli->close();
    return json_encode($data);
    
}
//fetch filieres
function getFilieress($id_organizer) {
    global $con;
    $facultyQuery = "SELECT id_faculty FROM organizer WHERE id = '$id_organizer'";
    $facultyResult = mysqli_query($con, $facultyQuery);
    if (!$facultyResult || mysqli_num_rows($facultyResult) === 0) {
        return [];
    }
    $facultyRow = mysqli_fetch_assoc($facultyResult);
    $id_faculty = $facultyRow['id_faculty'];
    $query = "
        SELECT f.id_filiere, f.name_filiere
        FROM filiere f
        JOIN department d ON f.id_department = d.id_department
        WHERE d.id_faculty = $id_faculty
    ";
    $result = mysqli_query($con, $query);
    $filieres = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $filieres[] = $row;
    }

    return $filieres;

}
// Fetch specialties based on filière Id
if (isset($_GET["id_filiere"])) {
    echo getspecialties($_GET["id_filiere"]);
    exit;
}

function getspecialties($id_filiere){
    $mysqli = databaseconnection();
    $data = [];

    if(!$mysqli){
        return false;
    }

    $id_filiere = $mysqli->real_escape_string($id_filiere);
    $res = $mysqli->query("SELECT * FROM specialties WHERE id_filiere = '$id_filiere'");

    while($row = $res->fetch_assoc()){
        $data[] = $row;
    }

    $mysqli->close();
    return json_encode($data);
}

// Fetch levels based on specialty Id
if (isset($_GET["id_specialty"])) {
    echo getLevels($_GET["id_specialty"]);
    exit;
}

function getLevels($id_specialty) {
    $mysqli = databaseconnection();
    $data = [];

     if(!$mysqli){
        return false;
    }

    $id_specialty = $mysqli->real_escape_string($id_specialty);
    $res = $mysqli->query("SELECT * FROM levels WHERE specialty_id = '$id_specialty'");
    while($row = $res->fetch_assoc()){
        $data[] = $row;
    }
    
    $mysqli->close();
    return json_encode($data);
}

// Fetch modules based on level ID
if (isset($_GET["id_level"])) {
    echo getModules($_GET["id_level"]);
    exit;
}

function getModules($id_level) {
    $mysqli = databaseconnection();
    $data = [];

    if (!$mysqli) {
        return false;
    }

    $id_level = $mysqli->real_escape_string($id_level);
    $res = $mysqli->query("SELECT * FROM modules WHERE level_id = '$id_level' AND is_prog=0 ");

    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }

    $mysqli->close();
    return json_encode($data);
}
// Fetch teachers based on faculty ID
function getTeachers($id_faculty) {
    $mysqli = databaseconnection();
    $data = [];
    if (!$mysqli) {
        return false;
    }
    $id_faculty = $mysqli->real_escape_string($id_faculty);
    $res = $mysqli->query("SELECT teachers.*, faculty.name AS faculty_name FROM teachers LEFT JOIN faculty ON teachers.id_faculty = faculty.id_faculty WHERE teachers.id_faculty = '$id_faculty'");
    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }
    $mysqli->close();
    return $data;
}
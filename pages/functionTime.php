
<?php
function databaseconnection(){
    $mysqli = new mysqli('localhost','root','','exam-org');
    if($mysqli->connect_errno != 0){
        die("Error connecting database".$mysqli->connect_error);
    }
    return $mysqli;
}

// Fetch filieres
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

// Fetch rooms for a faculty
if (isset($_GET["id_faculty"])) {
    echo getFacultyRooms($_GET["id_faculty"]);
    exit;
}

function getFacultyRooms($id_faculty) {
    $mysqli = databaseconnection();
    $data = [];
    if (!$mysqli) {
        return false;
    }
    $id_faculty = $mysqli->real_escape_string($id_faculty);
    
    // Get departments for the faculty
    $departments = [];
    $resDept = $mysqli->query("SELECT id_department FROM department WHERE id_faculty = $id_faculty");
    while ($row = $resDept->fetch_assoc()) {
        $departments[] = $row['id_department'];
    }
    
    if (empty($departments)) {
        return json_encode([]);
    }
    
    $deptList = implode(',', $departments);
    $res = $mysqli->query("SELECT * FROM rooms WHERE id_department IN ($deptList)");
    
    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }
    
    $mysqli->close();
    return json_encode($data);
}

// Fetch teachers for a faculty
if (isset($_GET["faculty_teachers"])) {
    echo getFacultyTeachers($_GET["faculty_teachers"]);
    exit;
}

function getFacultyTeachers($id_faculty) {
    $mysqli = databaseconnection();
    $data = [];
    if (!$mysqli) {
        return false;
    }
    $id_faculty = $mysqli->real_escape_string($id_faculty);
    $res = $mysqli->query("SELECT * FROM teachers WHERE id_faculty = '$id_faculty'");
    
    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }
    
    $mysqli->close();
    return json_encode($data);
}
?> 
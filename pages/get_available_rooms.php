<?php
include 'connection.php';
session_start(); // أضف هذا السطر لأنك تستخدم $_SESSION

if (!isset($_GET['date'], $_GET['slot'])) {
  echo json_encode([]);
  exit;
}

$date = $con->real_escape_string($_GET['date']);
$slot = $con->real_escape_string($_GET['slot']);

$scheduling_data = $_SESSION['scheduling_data'];
$exam_type = $con->real_escape_string($scheduling_data['exam_type']);

if (!$date || !$slot || !$exam_type) {
  echo json_encode([]);
  exit;
}


$rooms = [];
$id_organizer = $_SESSION['id_organizer'];
$id_faca = mysqli_query($con, "SELECT id_faculty FROM organizer WHERE id = '$id_organizer'");
$id_fac = mysqli_fetch_assoc($id_faca);
$fac_id = $id_fac['id_faculty'];
$sql = "SELECT rooms.* 
        FROM rooms
        JOIN department ON rooms.id_department = department.id_department
        WHERE department.id_faculty = $fac_id";
$result = $con->query($sql);

while ($room = $result->fetch_assoc()) {
  $room_id = (int)$room['id'];

  $check_sql = "SELECT COUNT(*) as cnt 
                FROM exam_sessions es
                JOIN session_rooms sr ON sr.session_id = es.id
                JOIN exam_schedules sch ON sch.id = es.schedule_id
                WHERE sr.room_id = $room_id 
                  AND es.exam_date = '$date' 
                  AND es.slot = '$slot'
                  AND sch.exam_type = '$exam_type'";

  $check_result = $con->query($check_sql);
  $check = $check_result->fetch_assoc();

  if ($check['cnt'] == 0) {
    $rooms[] = $room;
  }
}

echo json_encode($rooms);
?>

<?php
session_start();
include("connection.php");


$id_organizer = $_SESSION['id_organizer'];
$id_faca = mysqli_query($con, "SELECT id_faculty FROM organizer WHERE id = '$id_organizer'");
$id_fac = mysqli_fetch_assoc($id_faca);
$fac_id = $id_fac['id_faculty'];
// Fetch teachers list
$teachers = [];
$res_teachers = mysqli_query($con, "SELECT id, name FROM teachers WHERE id_faculty='$fac_id' ORDER BY name");
while ($row = mysqli_fetch_assoc($res_teachers)) {
  $teachers[] = $row;
}

$selected_teacher_id = $_POST['teacher_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="/css/all.min.css" />
  <link rel="stylesheet" href="/css/styletime.css" />
  <link rel="website icon" type="png" href="/images/logo.png" />
  <title>Exam Organization</title>
</head>

<body>
  <div class="navigationS">
    <div class="logoS">
      <img src="/images/logo.png" alt="Logo">
      <h1>Exam<span class="danger">Org</span></h1>
    </div>
    <li><a href="/pages/manager.php"><i class="fa-solid fa-circle-left"></i></a></li>
  </div>
  <div class="content_all">
    <h2><img src="/images/teachertime.PNG">View Teacher's Sessions</h2>
    <form method="post" action="" class="form_all">
      <div class="input_group_all">
        <label for="teacher_id">Select Teacher:</label>
        <select name="teacher_id" id="teacher_id" required>
          <option value="">-- Choose --</option>
          <?php foreach ($teachers as $t) { ?>
            <option value="<?= $t['id'] ?>" <?= ($selected_teacher_id == $t['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($t['name']) ?>
            </option>
          <?php } ?>
        </select>
      </div>
      <div class="input_group_all">
        <label>Exam Type</label>
        <select name="exam_type" required>
          <option value="" hidden selected>-- Select Exam Type --</option>
          <option value="semester1_Regular">Regular Exam (Semester 1)</option>
          <option value="semester1_Makeup">Make-up Exam (Semester 1)</option>
          <option value="semester1_Remedial">Remedial Exam (Semester 1)</option>
          <option value="semester2_Regular">Regular Exam (Semester 2)</option>
          <option value="semester2_Makeup">Make-up Exam (Semester 2)</option>
          <option value="semester2_Remedial">Remedial Exam (Semester 2)</option>
        </select>
      </div>
      <input type="submit" value="Search" class="search_btn">
    </form>
   <div>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($selected_teacher_id, $_POST['exam_type'])) {
  $exam_type = mysqli_real_escape_string($con, $_POST['exam_type']);

  // As a subject teacher
  $prof_sessions = [];
  $query_prof = "
    SELECT 
      es.exam_date,
      es.slot,
      l.name AS level_name,
      m.name AS module_name
    FROM exam_schedules sch
    JOIN exam_sessions es ON sch.id = es.schedule_id
    JOIN modules m ON es.module_id = m.id
    JOIN levels l ON es.level_id = l.id
    WHERE es.module_teacher_id = $selected_teacher_id
    AND sch.exam_type = '$exam_type'
  ";
  $res_prof = mysqli_query($con, $query_prof);
  while ($row = mysqli_fetch_assoc($res_prof)) {
    $prof_sessions[$row['exam_date']][$row['slot']][] = $row;
  }

  // As a supervisor
  $sup_sessions = [];
  $query_sup = "
    SELECT 
      es.exam_date,
      es.slot,
      l.name AS level_name,
      r.name AS room_name,
      m.name AS module_namee
    FROM exam_schedules sch
    JOIN exam_sessions es ON sch.id = es.schedule_id
    JOIN session_rooms sr ON sr.session_id = es.id
    JOIN rooms r ON sr.room_id = r.id
    JOIN levels l ON es.level_id = l.id
    JOIN modules m ON es.module_id = m.id
    WHERE sr.teacher_id = $selected_teacher_id
    AND sch.exam_type = '$exam_type'
  ";
  $res_sup = mysqli_query($con, $query_sup);
  while ($row = mysqli_fetch_assoc($res_sup)) {
    $sup_sessions[$row['exam_date']][$row['slot']][] = $row;
  }

  if (empty($prof_sessions) && empty($sup_sessions)) {
    echo "<p class='info_search_eror'>This teacher is not assigned to this exam type as a subject teacher or a supervisor.</p>";
  } else {

    // Get all unique days and slots
    $days = array_unique(array_merge(array_keys($prof_sessions), array_keys($sup_sessions)));
    sort($days);
    $slots = [];

    foreach ([$prof_sessions, $sup_sessions] as $sessions) {
      foreach ($sessions as $day => $day_sessions) {
        foreach ($day_sessions as $slot => $data) {
          $slots[$slot] = true;
        }
      }
    }
    $slots = array_keys($slots);
    sort($slots);

    // Display table for subject teacher
    if (!empty($prof_sessions)) {
      echo "<h3 class='h3_teach'>Schedule as Subject Teacher</h3>";
      echo "<table border='1' cellpadding='6' class='custom-table_search'  style='margin-left:122px'>";
      echo "<tr><th>Day / Slot</th>";
      foreach ($slots as $slot) {
        echo "<th>Slot {$slot}</th>";
      }
      echo "</tr>";
      foreach ($days as $day) {
        echo "<tr><td><strong>$day</strong></td>";
        foreach ($slots as $slot) {
          echo "<td>";
          if (!empty($prof_sessions[$day][$slot])) {
            foreach ($prof_sessions[$day][$slot] as $s) {
              echo "<span style='color:#092a5f ; font-weight: 500;'>Level:</span> {$s['level_name']} - <span style='color:#092a5f ; font-weight: 500;'>Module:</span> {$s['module_name']}<br>";
            }
          } else {
            echo "-";
          }
          echo "</td>";
        }
        echo "</tr>";
      }
      echo "</table>";
    }

    // Display table for supervisor
    if (!empty($sup_sessions)) {
      echo "<h3 class='h3_teach'>Schedule as Supervisor</h3>";
      echo "<table border='1' cellpadding='6' class='custom-table_search' style='margin-left:122px'>";
      echo "<tr><th>Day / Slot</th>";
      foreach ($slots as $slot) {
        echo "<th>Slot {$slot}</th>";
      }
      echo "</tr>";
      foreach ($days as $day) {
        echo "<tr><td><strong>$day</strong></td>";
        foreach ($slots as $slot) {
          echo "<td>";
          if (!empty($sup_sessions[$day][$slot])) {
            foreach ($sup_sessions[$day][$slot] as $s) {
              echo "<span style='color:#092a5f ; font-weight: 500;'>Level:</span> {$s['level_name']} - <span style='color:#092a5f ;font-weight: 500;'>Module:</span>  {$s['module_namee']} <br> <span style='color:#092a5f ;font-weight: 500;'>Room:</span>  {$s['room_name']}<br>";
            }
          } else {
            echo "-";
          }
          echo "</td>";
        }
        echo "</tr>";
      }
      echo "</table>";
    }
  }
}
?>
</div>

</body>

</html>
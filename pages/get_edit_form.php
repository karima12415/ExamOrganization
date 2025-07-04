<?php
session_start();
include("connection.php");
$id_organizer = $_SESSION['id_organizer'];
$id_faca = mysqli_query($con, "SELECT id_faculty FROM organizer WHERE id = '$id_organizer'");
$id_fac = mysqli_fetch_assoc($id_faca);
$fac_id = $id_fac['id_faculty'];
$session_id = (int)$_GET['session_id'];
$room_name = mysqli_real_escape_string($con, $_GET['room_name']);

// Get room id
$res_room = mysqli_query($con, "SELECT id FROM rooms WHERE name = '$room_name'");
$room = mysqli_fetch_assoc($res_room);
$room_id = $room['id'];

// Get supervisors in this room and session
$supervisors = [];
$res_supervisors = mysqli_query($con, "
  SELECT t.id, t.name 
  FROM session_rooms sr 
  JOIN teachers t ON sr.teacher_id = t.id 
  WHERE sr.session_id = $session_id AND sr.room_id = $room_id
");
while ($s = mysqli_fetch_assoc($res_supervisors)) {
  $supervisors[] = $s;
}

// Get teachers who are NOT in any session_rooms (completely free)
$free_teachers = mysqli_query($con, "
  SELECT id, name FROM teachers
  WHERE id_faculty = '$fac_id' AND used_hours < hourly_size
    AND id NOT IN (SELECT DISTINCT teacher_id FROM session_rooms)
    AND id NOT IN (SELECT DISTINCT module_teacher_id FROM exam_sessions)
  ORDER BY name
");
?>

<form method="post" action="update_supervisors.php" class="form_edit_teacher">
  <input type="hidden" name="session_id" value="<?= $session_id ?>">
  <input type="hidden" name="room_id" value="<?= $room_id ?>">

  <h3 class="h3_teach">Edit Supervisors for Room: <span style="color: #007bff;"><?= htmlspecialchars($room_name) ?></span></h3>
  <hr>

  <?php foreach ($supervisors as $index => $sup) { ?>
    <label><strong>Supervisor <?= $index + 1 ?>:</strong></label>
    <select name="supervisors[]" class="teacher-select">
      <!-- Current supervisor -->
      <option value="<?= $sup['id'] ?>" selected><?= htmlspecialchars($sup['name']) ?> (current)</option>

      <!-- Other available teachers -->
      <?php
      mysqli_data_seek($free_teachers, 0); // Reset result pointer
      while ($t = mysqli_fetch_assoc($free_teachers)) {
        echo "<option value='{$t['id']}'>" . htmlspecialchars($t['name']) . "</option>";
      }
      ?>
    </select>
  <?php } ?>

  <input type="submit" value="Save Changes" class="btn_save_change">

  <style>
    .teacher-select {
      width: 100%;
      padding: 10px;
      font-size: 15px;
      border-radius: 6px;
      border: 1px solid #ccc;
      background-color: #f9f9f9;
      margin-bottom: 10px;
    }

    .btn_save_change {
      background-color: #0252d1;
      color: white;
      border: none;
      width: 200px;
      padding: 10px 20px;
      font-size: 15px;
      border-radius: 6px;
      cursor: pointer;
      align-self: center;
      transition: background-color 0.3s ease;
    }

    .btn_save_change:hover {
      background-color: #c9d5e8;
    }

    .form_edit_teacher {
      display: flex;
      flex-direction: column;
    }

    .h3_teach {
      margin-bottom: 10px;
      color: #222;
    }
  </style>
</form>

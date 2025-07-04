<?php
session_start();
include("connection.php");
$id_organizer = $_SESSION['id_organizer'];
function getFilieress($organizer_id)
{
  global $con;
  $res_faculty = mysqli_query($con, "SELECT id_faculty FROM organizer WHERE id = $organizer_id");
  if (!$res_faculty || mysqli_num_rows($res_faculty) === 0) return [];

  $row = mysqli_fetch_assoc($res_faculty);
  $id_faculty = (int)$row['id_faculty'];

  $res_departments = mysqli_query($con, "SELECT id_department FROM department WHERE id_faculty = $id_faculty");
  $filieres = [];

  while ($dept = mysqli_fetch_assoc($res_departments)) {
    $dept_id = (int)$dept['id_department'];
    $res_filiere = mysqli_query($con, "SELECT * FROM filiere WHERE id_department = $dept_id");
    while ($filiere = mysqli_fetch_assoc($res_filiere)) {
      $filieres[] = $filiere;
    }
  }

  return $filieres;
}
$filieres = getFilieress($id_organizer);
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
    <h2><img src="/images/alltime.PNG">View ALL Exam schedule</h2>
    <form method="post" action="" class="form_all">
      <div class="input_group_all">
        <label>Filiere</label>
        <select name="filiere_input" id="filiere_input" required onchange="handleFiliereSelect(this)">
          <option value="">-- Select Filiere --</option>
          <?php foreach ($filieres as $f) {
            echo "<option value='{$f['id_filiere']}'>{$f['name_filiere']}</option>";
          } ?>
        </select>
      </div>
      <div class="input_group_all">
        <label>Specialty</label>
        <select name="specialty_id" id="specialty_select" required disabled>
          <option value="">-- Select Specialty --</option>
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
  </div>
  <div>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filiere_input'], $_POST['specialty_id'], $_POST['exam_type'])) {
      $filiere_id = (int)$_POST['filiere_input'];
      $specialty_id = (int)$_POST['specialty_id'];
      $exam_type = mysqli_real_escape_string($con, $_POST['exam_type']);

      $res_schedule = mysqli_query($con, "SELECT * FROM exam_schedules WHERE filiere_id = $filiere_id AND specialty_id = $specialty_id AND exam_type = '$exam_type'");

      if (mysqli_num_rows($res_schedule) === 0) {
        echo "<p class='info_search_eror'>No schedule found for this specialty and exam type.</p>";
      } else {
        $schedule = mysqli_fetch_assoc($res_schedule);
        $schedule_id = $schedule['id'];

        echo "<h3 class='info_search'>Start Date: <strong>{$schedule['start_date']}</strong> | End Date: <strong>{$schedule['end_date']}</strong></h3>";
        $sessions = [];
        $dates = [];
        $slots = [];

        $res_sessions = mysqli_query($con, "SELECT * FROM exam_sessions WHERE schedule_id = $schedule_id ORDER BY exam_date, slot");
        while ($row = mysqli_fetch_assoc($res_sessions)) {
          $sessions[$row['exam_date']][$row['slot']][] = $row;
          $dates[] = $row['exam_date'];
          $slots[] = $row['slot'];
        }

        $dates = array_unique($dates);
        sort($dates);
        $slots = array_unique($slots);
        sort($slots);

        echo "<table  class='custom-table_search' style='margin-left:122px'>";
        echo "<tr><th>Day / Time</th>";
        foreach ($slots as $slot) {
          echo "<th>$slot</th>";
        }
        echo "</tr>";
        foreach ($dates as $date) {
          echo "<tr>";
          echo "<td><strong>$date</strong></td>";
          foreach ($slots as $slot) {
            echo "<td>";
            if (!empty($sessions[$date][$slot])) {
              foreach ($sessions[$date][$slot] as $s) {
                $module = mysqli_fetch_assoc(mysqli_query($con, "SELECT name FROM modules WHERE id = {$s['module_id']}"));
                $level = mysqli_fetch_assoc(mysqli_query($con, "SELECT name FROM levels WHERE id = {$s['level_id']}"));
                $main_teacher = mysqli_fetch_assoc(mysqli_query($con, "SELECT name FROM teachers WHERE id = {$s['module_teacher_id']}"));

                echo "<div style='margin-bottom:10px'>";
                echo "<strong>{$level['name']} ({$module['name']})</strong><br>";
                echo "<strong>Module Teacher:</strong> {$main_teacher['name']}<br>";

                // Get rooms and teachers
                $rooms_teachers = [];
                $res_rooms = mysqli_query($con, "
                SELECT sr.room_id, r.name AS room_name, t.name AS teacher_name 
                FROM session_rooms sr
                JOIN rooms r ON sr.room_id = r.id
                JOIN teachers t ON sr.teacher_id = t.id
                WHERE sr.session_id = {$s['id']}
                ORDER BY sr.room_id
                ");
                while ($r = mysqli_fetch_assoc($res_rooms)) {
                  $room_name = $r['room_name'];
                  $teacher_name = $r['teacher_name'];
                  $rooms_teachers[$room_name][] = $teacher_name;
                }

                foreach ($rooms_teachers as $room => $teachers) {
                  echo "
                  <div class='supervisor-card'>
                  <div class='supervisor-room'>Room: <span class='room-name'>$room</span></div>
                  <div class='supervisor-label'>Supervisor:</div>
                  ";

                  foreach ($teachers as $teacher) {
                    echo "<div class='supervisor-name'>$teacher</div>";
                  }
                  echo "<button class='edit-btn' onclick=\"editSupervisors({$s['id']}, '$room')\">Edit</button>";
                  echo "</div>";
                }


                echo "</div><hr>";
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
    ?>
    <div id="editModal" class="modal-overlay" style="display:none;">
      <div class="modal-content" id="modalFormContainer">
        <!-- The form will be loaded here by js -->
      </div>
    </div>
  </div>


  <script>
    function handleFiliereSelect(select) {
      const filiereId = select.value;
      const specialtySelect = document.getElementById('specialty_select');
      specialtySelect.innerHTML = '<option value="">-- Select Specialty --</option>';
      specialtySelect.disabled = true;

      if (filiereId) {
        fetch(`get_specialties.php?filiere_id=${filiereId}`)
          .then(res => res.json())
          .then(data => {
            data.forEach(item => {
              const opt = document.createElement('option');
              opt.value = item.id;
              opt.textContent = item.name;
              specialtySelect.appendChild(opt);
            });
            specialtySelect.disabled = false;
          });
      }
    }
  </script>
  <script>
    function editSupervisors(sessionId, roomName) {
      fetch(`get_edit_form.php?session_id=${sessionId}&room_name=${encodeURIComponent(roomName)}`)
        .then(response => response.text())
        .then(html => {
          document.getElementById('modalFormContainer').innerHTML = `
        <button class="close-modal" onclick="closeModal()">
          <i class="fa-solid fa-xmark" style='color:red;'></i>
        </button>
        ${html}
      `;
          document.getElementById('editModal').style.display = 'flex';
        });
    }

    function closeModal() {
      document.getElementById('editModal').style.display = 'none';
      document.getElementById('modalFormContainer').innerHTML = '';
    }
  </script>
</body>

</html>
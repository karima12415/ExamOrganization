<?php
//save data from step2
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['go_step3'])) {
  $_SESSION['schedule_step2'] = $_POST['schedule'];
  header("Location: step3.php");
  exit;
}
?>
<?php
include 'connection.php';
if (!isset($_SESSION['schedule_step2'])) {
  die("No scheduled data found!");
}
/*
function isTeacherOccupied($con, $teacher_id, $exam_date, $slot, $exam_type) {
  $teacher_id = (int)$teacher_id;
  $exam_date = $con->real_escape_string($exam_date);
  $slot = $con->real_escape_string($slot);
  $exam_type = $con->real_escape_string($exam_type);

  $query = "
    SELECT COUNT(*) AS cnt
    FROM exam_sessions es
    JOIN exam_schedules sch ON es.schedule_id = sch.id
    LEFT JOIN session_rooms sr ON sr.session_id = es.id
    WHERE es.exam_date = '$exam_date'
      AND es.slot = '$slot'
      AND sch.exam_type = '$exam_type'
      AND (
        es.module_teacher_id = $teacher_id OR
        sr.teacher_id = $teacher_id
      )
  ";

  $result = $con->query($query);
  if (!$result) {
    die("Query failed: " . $con->error);
  }

  $row = $result->fetch_assoc();
  return $row['cnt'] > 0; // true if teacher is already assigned in that slot
}
*/

$schedule = $_SESSION['schedule_step2'];
$scheduling_data = $_SESSION['scheduling_data'];
$filiere_id = $scheduling_data['filiere_id'];
$specialty_id = $scheduling_data['specialty_id'];
$exam_type = $scheduling_data['exam_type'];
$start_date = $scheduling_data['start_date'];
$end_date = $scheduling_data['end_date'];
$slot_start = $scheduling_data['slot_start'];
$slots = $scheduling_data['slots'];

$id_organizer = $_SESSION['id_organizer'];
$id_faca = mysqli_query($con, "SELECT id_faculty FROM organizer WHERE id = '$id_organizer'");
$id_fac = mysqli_fetch_assoc($id_faca);
$fac_id = $id_fac['id_faculty'];

$filiernamee = mysqli_query($con, "SELECT name_filiere FROM filiere WHERE id_filiere = '$filiere_id'");
$filiername = mysqli_fetch_array($filiernamee);
$name_filiere = $filiername['name_filiere'];

$spcailtynamee = mysqli_query($con, "SELECT name FROM specialties WHERE 	id ='$specialty_id'");
$spcailtyname = mysqli_fetch_array($spcailtynamee);
$name_spcailty = $spcailtyname['name'];


$levels_result = $con->query("SELECT id, name FROM levels");
$level_names = [];
while ($lvl = $levels_result->fetch_assoc()) {
  $level_names[$lvl['id']] = $lvl['name'];
}

// تحميل أسماء المقاييس
$modules_result = $con->query("SELECT id, name FROM modules");
$module_names = [];
while ($mod = $modules_result->fetch_assoc()) {
  $module_names[$mod['id']] = $mod['name'];
}

$teachers_result = $con->query("SELECT id, name,hourly_size,used_hours FROM teachers WHERE id_faculty ='$fac_id'");
$teachers = [];
$teacher_hours = [];
$teacher_used_hours = [];
while ($t = $teachers_result->fetch_assoc()) {
  $teachers[$t['id']] = $t['name'];
  $teacher_hours[$t['id']] = $t['hourly_size'];
  $teacher_used_hours[$t['id']] = $t['used_hours'];
}

// تحميل بيانات القاعات مع عدد المراقبين المطلوب
$rooms_result = $con->query("SELECT id, name, supervisor_count FROM rooms");
$rooms_info = [];
while ($r = $rooms_result->fetch_assoc()) {
  $rooms_info[$r['id']] = [
    'name' => $r['name'],
    'supervisor_count' => $r['supervisor_count']
  ];
}

// بيانات الأساتذة المرتبطين بالمقاييس (لمنع أستاذ المادة من مراقبتها)
$module_teachers_result = $con->query("SELECT module_id, teacher_id FROM teacher_modules");
$module_teachers = [];
while ($mt = $module_teachers_result->fetch_assoc()) {
  $module_teachers[$mt['module_id']][] = $mt['teacher_id'];
}

$teacher_occupations = []; // [teacher_id][date][slot][exam_type] = true
$query = "
  SELECT es.module_teacher_id AS teacher_id, es.exam_date, es.slot, sch.exam_type
  FROM exam_sessions es
  JOIN exam_schedules sch ON es.schedule_id = sch.id
  UNION ALL
  SELECT sr.teacher_id, es.exam_date, es.slot, sch.exam_type
  FROM session_rooms sr
  JOIN exam_sessions es ON sr.session_id = es.id
  JOIN exam_schedules sch ON es.schedule_id = sch.id

";
$res = $con->query($query);
while ($row = $res->fetch_assoc()) {
  $tid = $row['teacher_id'];
  $date = $row['exam_date'];
  $slot = $row['slot'];
  $type = $row['exam_type'];
  $teacher_occupations[$tid][$date][$slot][$type] = true;
}
function isTeacherOccupied($teacher_occupations, $teacher_id, $exam_date, $slot, $exam_type)
{
  return isset($teacher_occupations[$teacher_id][$exam_date][$slot][$exam_type]);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/css/all.min.css">
  <link rel="stylesheet" href="/css/styletime.css">
  <link rel="website icon" type="png" href="/images/logo.png">
  <title>Exam Organization</title>
</head>

<body>
  <div class="navigationS">
    <div class="logoS">
      <img src="/images/logo.png" alt="Logo">
      <h1>Exam<span class="danger">Org</span></h1>
    </div>
    <li><a href="/pages/step2.php"><i class="fa-solid fa-circle-left"></i></a></li>
  </div>
  <div class="container">
    <div class="header-info">
      <h2><img src="/images/step2icon.png">Step3: Assign Supervisors </h2>
      <div class="info-grid">
        <div class="info-item">
          <h3>filiere:</h3>
          <p><?php echo $name_filiere ?></p>
        </div>
        <div class="info-item">
          <h3>Exam Type:</h3>
          <p><?php echo $exam_type ?></p>
        </div>
        <div class="info-item">
          <h3>Start Date:</h3>
          <p><?php echo $start_date ?></p>
        </div>
        <div class="info-item">
          <h3>End Date:</h3>
          <p><?php echo $end_date ?></p>
        </div>
      </div>
    </div>
    <form action="save_schode.php" method="POST" class="form2">
      <h2 class="specialty-nav">Specialty: <?= $name_spcailty ?></h2>
      <div class="custom-table-container">
        <table class="custom-table">
          <thead>
            <tr>
              <th>Date</th>
              <?php
              $all_slots = [];
              foreach ($schedule as $date => $slots_on_day) {
                foreach ($slots_on_day as $slot => $entries) {
                  if (!in_array($slot, $all_slots)) {
                    $all_slots[] = $slot;
                  }
                }
              }
              sort($all_slots);
              foreach ($all_slots as $slot) {
                echo "<th>$slot</th>";
              }
              ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($schedule as $date => $slots_on_day) { ?>
              <tr>
                <td><strong><?= htmlspecialchars($date) ?></strong></td>
                <?php foreach ($all_slots as $slot) { ?>
                  <td>
                    <?php
                    if (isset($slots_on_day[$slot])) {
                      foreach ($slots_on_day[$slot] as $index => $entry) {
                        $level_id = $entry['level_id'];
                        $module_id = $entry['module_id'];
                        $room_ids = $entry['room_ids'];
                    ?>
                        <div style="margin-bottom: 10px;">
                          <div style="color: grey;">
                            Level : <?= htmlspecialchars($level_names[$level_id] ?? "??") ?> |
                            Module : <?= htmlspecialchars($module_names[$module_id] ?? "??") ?>
                          </div>
                          <?php
                          $module_teacher_list = $module_teachers[$module_id] ?? [];
                          ?>
                          <div class="selct_STEP3">
                            <strong>Module Teacher :</strong>
                            <select name="module_teacher[<?= $date ?>][<?= $slot ?>][<?= $index ?>]">
                              <option value="">-- Choose a Module Teacher --</option>
                              <?php foreach ($module_teacher_list as $teacher_id) {
                                if (isTeacherOccupied($teacher_occupations, $teacher_id, $date, $slot, $exam_type)) continue;
                                if (isset($teachers[$teacher_id])) { ?>

                                  <option value="<?= $teacher_id ?>"><?= htmlspecialchars($teachers[$teacher_id]) ?></option>
                              <?php }
                              } ?>
                            </select>
                          </div>
                          <?php foreach ($room_ids as $room_id) {
                            $room = $rooms_info[$room_id];
                            $supervisor_count = $room['supervisor_count'];
                          ?>
                            <div class="selct_STEP3">
                              <strong>Room : <?= $room['name'] ?> (<?= $supervisor_count ?> Supervisor)</strong><br>
                              <?php for ($i = 0; $i < $supervisor_count; $i++) { ?>
                                <select name="supervisors[<?= $date ?>][<?= $slot ?>][<?= $index ?>][<?= $room_id ?>][]">
                                  <option value="">-- Choose a Teacher --</option>
                                  <?php foreach ($teachers as $teacher_id => $teacher_name) {
                                    if (in_array($teacher_id, $module_teachers[$module_id] ?? [])) continue;
                                    if (isTeacherOccupied($teacher_occupations, $teacher_id, $date, $slot, $exam_type)) continue;
                                  ?>
                                    <option value="<?= $teacher_id ?>"
                                      data-teacher-id="<?= $teacher_id ?>"
                                      data-used-hours="<?= $teacher_used_hours[$teacher_id] ?>"
                                      data-hourly-size="<?= $teacher_hours[$teacher_id] ?>"><?= htmlspecialchars($teacher_name) ?></option>
                                  <?php } ?>
                                </select><br><hr>
                              <?php } ?>
                            </div>
                          <?php } ?>
                        </div>
                    <?php }
                    } else {
                      echo '<i> No class scheduled</i>';
                    }
                    ?>
                  </td>
                <?php } ?>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      <button style="margin-left: 53rem;" type="submit" onclick="return confirm('Do you want to save the schedule now? if there are incomplete slots, they will not be saved.')">Save</button>
    </form>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const allSelects = document.querySelectorAll('select'); // جميع الحقول
      const teacherHours = {}; // {id: {used, max}}
      const selectedInSlot = {}; // {slotKey: Set(teacher_ids)}

      // تجهيز الساعات من كل option
      allSelects.forEach(select => {
        const options = select.querySelectorAll('option[data-teacher-id]');
        options.forEach(option => {
          const id = option.dataset.teacherId;
          const used = parseInt(option.dataset.usedHours || "0");
          const max = parseInt(option.dataset.hourlySize || "99");
          if (!teacherHours[id]) {
            teacherHours[id] = {
              used,
              max
            };
          }
        });
      });

      // دالة استخراج مفتاح الحصة (تاريخ + ساعة)
      function getSlotKey(select) {
        const match = select.name.match(/\[(\d{4}-\d{2}-\d{2})]\[(.*?)\]/);
        return match ? `${match[1]}|${match[2]}` : '';
      }

      // تحديث النصوص وتعطيل الخيارات
      function updateAllSelects() {
        Object.keys(selectedInSlot).forEach(key => selectedInSlot[key] = new Set());

        allSelects.forEach(select => {
          const slotKey = getSlotKey(select);
          const selectedId = select.value;
          if (!selectedInSlot[slotKey]) selectedInSlot[slotKey] = new Set();
          if (selectedId) selectedInSlot[slotKey].add(selectedId);
        });

        allSelects.forEach(select => {
          const slotKey = getSlotKey(select);
          const options = select.querySelectorAll('option[data-teacher-id]');
          const currentValue = select.value;

          options.forEach(option => {
            const id = option.dataset.teacherId;
            const label = option.dataset.label || option.textContent.split(' (')[0];
            option.dataset.label = label;

            const used = teacherHours[id]?.used || 0;
            const max = teacherHours[id]?.max || 99;
            const status = used >= max ? ' (max)' : ` (${used}/${max})`;

            option.textContent = label + status;

            // ✅ فقط إذا وصل للحد الأقصى، عطل الاختيار
            option.disabled = used >= max && currentValue !== id;
          });
        });
      }


      // التعامل مع التغيير
      allSelects.forEach(select => {
        select.addEventListener('change', function() {
          const slotKey = getSlotKey(this);
          const oldVal = this.dataset.prev || '';
          const newVal = this.value;
          const isSupervisor = this.name.startsWith("supervisors");

          if (!selectedInSlot[slotKey]) selectedInSlot[slotKey] = new Set();

          // إذا كان مكرر في نفس الحصة
          if (selectedInSlot[slotKey].has(newVal) && newVal !== oldVal) {
            alert("The same teacher cannot be assigned to more than one role (as a supervisor or module teacher) in the same slot.");
            this.value = '';
            return;
          }

          // إنقاص القديم إن كان مراقب
          if (oldVal && isSupervisor && teacherHours[oldVal]) {
            teacherHours[oldVal].used--;
          }

          // زيادة الجديد إن كان مراقب
          if (newVal && isSupervisor && teacherHours[newVal]) {
            if (teacherHours[newVal].used >= teacherHours[newVal].max) {
              alert("This teacher has reached the maximum number of sessions!");
              this.value = '';
              return;
            }
            teacherHours[newVal].used++;
          }

          this.dataset.prev = newVal;
          updateAllSelects();
        });
      });

      updateAllSelects();
    });
  </script>
</body>

</html>
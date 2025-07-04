<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['scheduling_data'])) {
  header('Location: step1.php');
  exit();
}
/*function isRoomOccupied($con, $room_id, $exam_date, $slot, $exam_type)
{
  // تأمين المتغيرات
  $room_id = (int)$room_id;
  $exam_date = $con->real_escape_string($exam_date);
  $slot = $con->real_escape_string($slot);
  $exam_type = $con->real_escape_string($exam_type);
  // الربط الصحيح بين الجداول
  $sql = "SELECT COUNT(*) as cnt 
          FROM exam_sessions es
          JOIN session_rooms sr ON sr.session_id = es.id
          JOIN exam_schedules sch ON sch.id = es.schedule_id
          WHERE sr.room_id = $room_id 
            AND es.exam_date = '$exam_date' 
            AND es.slot = '$slot'
            AND sch.exam_type = '$exam_type'";
  $result = $con->query($sql);
  if (!$result) {
    die("Query failed: " . $con->error);
  }
  $row = $result->fetch_assoc();
  return $row['cnt'] > 0;
}*/
$scheduling_data = $_SESSION['scheduling_data'];
$filiere_id = $scheduling_data['filiere_id'];
$specialty_id = $scheduling_data['specialty_id'];
$exam_type = $scheduling_data['exam_type'];
$start_date = $scheduling_data['start_date'];
$end_date = $scheduling_data['end_date'];
$slot_start = $scheduling_data['slot_start'];
$slots = $scheduling_data['slots'];

$filiernamee = mysqli_query($con, "SELECT name_filiere FROM filiere WHERE id_filiere = '$filiere_id'");
$filiername = mysqli_fetch_array($filiernamee);
$name_filiere = $filiername['name_filiere'];

$specialty_data = [];
$ids_string = (int)$specialty_id;
$query = "SELECT s.id AS specialty_id, s.name AS specialty_name, l.id AS level_id, l.name AS level_name, l.student_count 
          FROM specialties s 
          LEFT JOIN levels l ON s.id = l.specialty_id 
          WHERE s.id = $ids_string";
$result = mysqli_query($con, $query);
if ($result) {
  while ($row = mysqli_fetch_assoc($result)) {
    $specialty_data[$row['specialty_id']]['name'] = $row['specialty_name'];
    if ($row['level_id']) {
      $specialty_data[$row['specialty_id']]['levels'][] = [
        'id' => $row['level_id'],
        'name' => $row['level_name'],
        'student_count' => $row['student_count']
      ];
    }
  }
}

$modules_by_level = [];
$module_query = "SELECT m.id, m.name, m.level_id 
                 FROM modules m 
                 INNER JOIN levels l ON m.level_id = l.id 
                 WHERE l.specialty_id = $ids_string";
$module_result = mysqli_query($con, $module_query);
while ($row = mysqli_fetch_assoc($module_result)) {
  $modules_by_level[$row['level_id']][] = [
    'id' => $row['id'],
    'name' => $row['name']
  ];
}

function generateSlotTimes($start_time, $count)
{
  $slots = [];
  $current = DateTime::createFromFormat('H:i', $start_time);
  for ($i = 1; $i <= $count; $i++) {
    $start = clone $current;
    $end = clone $current;
    $end->modify('+90 minutes');
    $slots[] = $start->format('H:i') . ' - ' . $end->format('H:i');
    $current->modify('+90 minutes');
  }
  return $slots;
}

function generateDateRange($start, $end)
{
  $dates = [];
  $current = new DateTime($start);
  $end = new DateTime($end);
  while ($current <= $end) {
    $dates[] = $current->format('Y-m-d');
    $current->modify('+1 day');
  }
  return $dates;
}

$slotTimes = generateSlotTimes($slot_start, $slots);
$examDates = generateDateRange($start_date, $end_date);

$id_organizer = $_SESSION['id_organizer'];
$id_faca = mysqli_query($con, "SELECT id_faculty FROM organizer WHERE id = '$id_organizer'");
$id_fac = mysqli_fetch_assoc($id_faca);
$fac_id = $id_fac['id_faculty'];

$rooms = $con->query("SELECT rooms.* FROM rooms
    JOIN department ON rooms.id_department = department.id_department
    WHERE department.id_faculty = $fac_id
");
$rooms_arr = [];
while ($r = $rooms->fetch_assoc()) {
  $rooms_arr[] = $r;
}

$room_occupations = []; // [room_id][date][slot][exam_type] = true
$query = "
  SELECT sr.room_id, es.exam_date, es.slot, sch.exam_type
  FROM session_rooms sr
  JOIN exam_sessions es ON sr.session_id = es.id
  JOIN exam_schedules sch ON sch.id = es.schedule_id
";
$res = $con->query($query);
while ($row = $res->fetch_assoc()) {
  $rid = $row['room_id'];
  $date = $row['exam_date'];
  $slot = $row['slot'];
  $type = $row['exam_type'];
  $room_occupations[$rid][$date][$slot][$type] = true;
}
function isRoomOccupied($room_occupations, $room_id, $exam_date, $slot, $exam_type)
{
  return isset($room_occupations[$room_id][$exam_date][$slot][$exam_type]);
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
    <li><a href="/pages/step1.php"><i class="fa-solid fa-circle-left"></i></a></li>
  </div>
  <div class="container">
    <div class="header-info">
      <h2><img src="/images/step3icon.PNG">Step2: Assign Levels, Modules, Rooms</h2>
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
    <form action="step3.php" method="POST" class="form2" id="step2-form">
      <?php
      $spec_id = array_key_first($specialty_data);
      $data = $specialty_data[$spec_id] ?? [];
      ?>
      <h2 class="specialty-nav">Specialty: <?= htmlspecialchars($data['name'] ?? '') ?></h2>
      <div class="custom-table-container">
        <table class="custom-table">
          <thead>
            <tr>
              <th>Date</th>
              <?php foreach ($slotTimes as $slotTime) { ?>
                <th><?= $slotTime ?></th>
              <?php } ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($examDates as $date) { ?>
              <tr>
                <td><?= $date ?></td>
                <?php foreach ($slotTimes as $slot) { ?>
                  <td data-slot="<?= $slot ?>" data-date="<?= $date ?>">
                    <div class="slot-content">
                      <div class="level-container">
                        <div class="level-select">
                          <?php $index = 0; ?>
                          <input type="hidden" name="schedule[<?= $date ?>][<?= $slot ?>][<?= $index ?>][specialty_id]" value="<?= $spec_id ?>">

                          <select name="schedule[<?= $date ?>][<?= $slot ?>][<?= $index ?>][level_id]" class="level-dropdown" onchange="updateModules(this)">
                            <option value="">-- Select Level --</option>
                            <?php foreach ($data['levels'] as $level) { ?>
                              <option value="<?= $level['id'] ?>"
                                data-modules='<?= json_encode($modules_by_level[$level['id']] ?? []) ?>'
                                data-student-count="<?= $level['student_count'] ?>">
                                <?= htmlspecialchars($level['name']) ?> (<?= $level['student_count'] ?>)
                              </option>
                            <?php } ?>
                          </select>

                          <select name="schedule[<?= $date ?>][<?= $slot ?>][<?= $index ?>][module_id]" class="module-dropdown">
                            <option value="">-- Select Module --</option>
                          </select>

                          <select name="schedule[<?= $date ?>][<?= $slot ?>][<?= $index ?>][room_ids][]" multiple>
                            <?php foreach ($rooms_arr as $room) {
                              if (isRoomOccupied($room_occupations, $room['id'], $date, $slot, $exam_type)) {
                                continue;
                              }
                              $label = htmlspecialchars($room['name']) . ' (' . $room['student_count'] . ')';
                            ?>
                              <option value="<?= $room['id'] ?>" data-capacity="<?= $room['student_count'] ?>">
                                <?= $label ?>
                              </option>
                            <?php } ?>
                          </select>

                          <div class="room-msg"></div>
                        </div>
                      </div>
                      <button
                        type="button"
                        class="add-level-btn"
                        onclick='addLevel(this, <?= json_encode(array_map(function ($lvl) use ($modules_by_level) {
                                                  $lvl["modules"] = $modules_by_level[$lvl["id"]] ?? [];
                                                  return $lvl;
                                                }, $data["levels"])) ?>, <?= $spec_id ?>)'>
                        + Add Exam
                      </button>
                    </div>
                  </td>
                <?php } ?>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      <div style="margin-top: 10px; text-align: right;">
        <button type="submit" class="go-step3-btn" name="go_step3">Next</button>
      </div>
    </form>
  </div>
  <script>
    function addLevel(button, levels, spec_id) {
      const container = button.previousElementSibling;
      const td = button.closest("td");
      const date = td.dataset.date;
      const slot = td.dataset.slot;
      const index = container.querySelectorAll(".level-select").length;

      const div = document.createElement("div");
      div.className = "level-select";

      // Specialty hidden input
      const specialtyInput = document.createElement("input");
      specialtyInput.type = "hidden";
      specialtyInput.name = `schedule[${date}][${slot}][${index}][specialty_id]`;
      specialtyInput.value = spec_id;
      div.appendChild(specialtyInput);

      // Level select
      const levelSelect = document.createElement("select");
      levelSelect.name = `schedule[${date}][${slot}][${index}][level_id]`;
      levelSelect.className = "level-dropdown";

      const defaultOption = document.createElement("option");
      defaultOption.value = "";
      defaultOption.textContent = "-- Select Level --";
      levelSelect.appendChild(defaultOption);

      levels.forEach(level => {
        const option = document.createElement("option");
        option.value = level.id;
        option.textContent = `${level.name} (${level.student_count})`;
        option.dataset.modules = JSON.stringify(level.modules || []);
        option.dataset.studentCount = level.student_count;
        levelSelect.appendChild(option);
      });

      // Prevent duplicate levels in the same date/slot
      levelSelect.onchange = function() {
        const selectedValue = this.value;
        const existing = Array.from(td.querySelectorAll(".level-dropdown")).map(sel => sel.value);
        if (selectedValue && existing.filter(v => v === selectedValue).length > 1) {
          alert("This level is already selected in this slot!");
          this.value = "";
          return;
        }
        updateModules(this);
      };

      div.appendChild(levelSelect);

      // Module select
      const moduleSelect = document.createElement("select");
      moduleSelect.name = `schedule[${date}][${slot}][${index}][module_id]`;
      moduleSelect.className = "module-dropdown";
      moduleSelect.innerHTML = '<option value="">-- Select Module --</option>';
      div.appendChild(moduleSelect);

      // Room select (multiple)
      const roomSelect = document.createElement("select");
      roomSelect.name = `schedule[${date}][${slot}][${index}][room_ids][]`;
      roomSelect.multiple = true;
      div.appendChild(roomSelect);

      // Room capacity message
      const msgDiv = document.createElement("div");
      msgDiv.className = "room-msg";
      div.appendChild(msgDiv);

      //room data base 
      fetch(`get_available_rooms.php?date=${encodeURIComponent(date)}&slot=${encodeURIComponent(slot)}`)
        .then(res => res.json())
        .then(rooms => {
          rooms.forEach(room => {
            const option = document.createElement("option");
            option.value = room.id;
            option.textContent = `${room.name} (${room.student_count})`;
            option.dataset.capacity = room.student_count;
            roomSelect.appendChild(option);
          });

          // بعد تحميل القاعات، حدّث الحقول المرتبطة
          updateRoomMessage(td);
          updateReservedRooms();
          hideReservedRoomsInAllSelects();
        });

      // Cancel button
      const cancelBtn = document.createElement("button");
      cancelBtn.type = "button";
      cancelBtn.className = "cancel-btn";
      cancelBtn.textContent = "Cancel";
      cancelBtn.onclick = function() {
        removeLevel(cancelBtn);
      };
      div.appendChild(cancelBtn);

      // Append to container
      container.appendChild(div);

      // UI Updates
      updateRoomMessage(td);
      updateReservedRooms();
      hideReservedRoomsInAllSelects();
      disableUsedModulesInAllDropdowns();
    }

    function removeLevel(btn) {
      const div = btn.parentElement;
      const moduleSelect = div.querySelector(".module-dropdown");
      const selectedModule = moduleSelect?.value;

      if (selectedModule) {
        usedModules.delete(selectedModule);
      }

      div.remove();
      updateReservedRooms();
      hideReservedRoomsInAllSelects();
      disableUsedModulesInAllDropdowns();
    }

    function updateModules(levelSelect) {
      const selectedOption = levelSelect.options[levelSelect.selectedIndex];
      const modules = JSON.parse(selectedOption.dataset.modules || '[]');

      const container = levelSelect.parentElement;
      const moduleSelect = container.querySelector('.module-dropdown');
      moduleSelect.innerHTML = '<option value="">-- Select Module --</option>';

      modules.forEach(mod => {
        const option = document.createElement('option');
        option.value = mod.id;
        option.textContent = mod.name;
        moduleSelect.appendChild(option);
      });
    }

    const usedModules = new Set();

    function updateModules(levelSelect) {
      const levelId = levelSelect.value;
      const moduleSelect = levelSelect.parentElement.querySelector(".module-dropdown");

      moduleSelect.innerHTML = '<option value="">-- Select Module --</option>';

      if (!levelId) return;

      const modules = JSON.parse(levelSelect.options[levelSelect.selectedIndex].dataset.modules || '[]');
      modules.forEach(module => {
        if (!usedModules.has(module.id.toString())) {
          const option = document.createElement("option");
          option.value = module.id;
          option.textContent = module.name;
          moduleSelect.appendChild(option);
        }
      });

      moduleSelect.onchange = function() {
        const selectedModule = moduleSelect.value;

        // 1. إزالة القيمة السابقة إذا وُجدت
        const previous = moduleSelect.dataset.selected;
        if (previous && usedModules.has(previous)) {
          usedModules.delete(previous);
          enableModuleInAllDropdowns(previous);
        }

        // 2. إذا تم اختيار مادة جديدة، أضفها
        if (selectedModule) {
          if (usedModules.has(selectedModule)) {
            alert("This module has already been selected!");
            moduleSelect.value = "";
          } else {
            usedModules.add(selectedModule);
            disableUsedModulesInAllDropdowns();
            moduleSelect.dataset.selected = selectedModule;
          }
        } else {
          // 3. إذا تم اختيار الخيار الفارغ
          moduleSelect.dataset.selected = "";
        }
      };

    }

    function enableModuleInAllDropdowns(moduleId) {
      document.querySelectorAll('.module-dropdown').forEach(dropdown => {
        Array.from(dropdown.options).forEach(option => {
          if (option.value === moduleId) {
            option.style.display = '';
          }
        });
      });
    }

    function disableUsedModulesInAllDropdowns() {
      document.querySelectorAll('.module-dropdown').forEach(dropdown => {
        const currentValue = dropdown.value;
        const options = Array.from(dropdown.options);

        options.forEach(option => {
          if (option.value && usedModules.has(option.value) && option.value !== currentValue) {
            option.style.display = 'none';
          } else {
            option.style.display = '';
          }
        });
      });
    }

    document.querySelectorAll('.level-dropdown').forEach(sel => {
      sel.addEventListener('change', function() {
        updateModules(this);
      });
    });

    const reservedRooms = {};

    document.addEventListener('change', function(e) {
      if (e.target.matches('select[name*="[room_ids]"]')) {
        updateReservedRooms();
        hideReservedRoomsInAllSelects();
      }
    });

    function updateReservedRooms() {
      Object.keys(reservedRooms).forEach(key => reservedRooms[key].clear());

      document.querySelectorAll('select[name*="[room_ids]"]').forEach(select => {
        const cell = select.closest('td');
        const date = cell?.dataset.date;
        const slot = cell?.dataset.slot;
        if (!date || !slot) return;

        const key = `${date}|${slot}`;
        if (!reservedRooms[key]) reservedRooms[key] = new Set();

        Array.from(select.selectedOptions).forEach(option => {
          reservedRooms[key].add(option.value);
        });
      });
    }

    function hideReservedRoomsInAllSelects() {
      document.querySelectorAll('select[name*="[room_ids]"]').forEach(select => {
        const cell = select.closest('td');
        const date = cell.dataset.date;
        const slot = cell.dataset.slot;
        const key = `${date}|${slot}`;
        const reserved = reservedRooms[key] || new Set();

        Array.from(select.options).forEach(option => {
          const value = option.value;
          const isSelected = option.selected;
          const originalText = option.textContent.replace(' [Reserved]', '');

          if (reserved.has(value) && !isSelected) {
            option.disabled = true;
            option.textContent = originalText + ' [Reserved]';
            option.style.color = 'red';
          } else {
            option.disabled = false;
            option.textContent = originalText;
            option.style.color = isSelected ? 'green' : '';
          }
        });
      });
    }

    document.addEventListener("change", function(e) {
      if (e.target.closest("td")) {
        const td = e.target.closest("td");
        updateRoomMessage(td);
      }
    });

    function updateRoomMessage(td) {
      const levelDivs = td.querySelectorAll(".level-select");

      levelDivs.forEach(levelDiv => {
        const levelSelect = levelDiv.querySelector(".level-dropdown");
        const roomSelect = levelDiv.querySelector("select[name*='[room_ids]']");
        const msgDiv = levelDiv.querySelector(".room-msg");

        if (!levelSelect || !roomSelect || !msgDiv) return;

        let studentCount = 0;
        let roomCapacity = 0;

        const selected = levelSelect.selectedOptions[0];
        if (selected && selected.dataset.studentCount) {
          studentCount = parseInt(selected.dataset.studentCount);
        }

        Array.from(roomSelect.selectedOptions).forEach(opt => {
          if (opt.dataset.capacity) {
            roomCapacity += parseInt(opt.dataset.capacity);
          }
        });

        msgDiv.innerHTML = "";
        if (roomCapacity < studentCount) {
          msgDiv.style.color = "red";
          msgDiv.textContent = "Room capacity is not sufficient for the number of students!";
        } else if (roomCapacity > studentCount * 1.5) {
          msgDiv.style.color = "orange";
          msgDiv.textContent = "The room capacity is much larger than the number of students.";
        } else {
          msgDiv.textContent = "";
        }
      });
    }
  </script>
  <script>
    document.getElementById("step2-form").addEventListener("submit", function(e) {
      let valid = true;
      let allFieldsFilled = true;
      const levelDivs = document.querySelectorAll(".level-select");

      levelDivs.forEach(levelDiv => {
        const levelSelect = levelDiv.querySelector(".level-dropdown");
        const moduleSelect = levelDiv.querySelector(".module-dropdown");
        const roomSelect = levelDiv.querySelector("select[name*='[room_ids]']");
        const selectedLevel = levelSelect?.selectedOptions[0];
        const msgDiv = levelDiv.querySelector(".room-msg");

        if (!levelSelect || !moduleSelect || !roomSelect || !msgDiv) return;

        // تحقق من أن كل الحقول ممتلئة
        if (
          !levelSelect.value ||
          !moduleSelect.value ||
          roomSelect.selectedOptions.length === 0
        ) {
          allFieldsFilled = false;
          return;
        }

        // تحقق من كفاية السعة
        const studentCount = parseInt(selectedLevel.dataset.studentCount || "0");
        let totalCapacity = 0;

        Array.from(roomSelect.selectedOptions).forEach(opt => {
          const capacity = parseInt(opt.dataset.capacity || "0");
          totalCapacity += capacity;
        });

        if (totalCapacity < studentCount) {
          msgDiv.style.color = "red";
          msgDiv.textContent = "Room capacity is not sufficient!";
          valid = false;
        } else {
          msgDiv.textContent = ""; // مسح الرسالة إن كانت موجودة
        }
      });

      // منع الإرسال إذا لم تُملأ كل الخانات
      if (!allFieldsFilled) {
        alert("Please fill in all required fields (level, module, and rooms) before proceeding.");
        e.preventDefault();
        return;
      }

      // منع الإرسال إذا كانت سعة القاعات غير كافية
      if (!valid) {
        alert("Please make sure all rooms are sufficient before proceeding.");
        e.preventDefault();
        return;
      }
    });
  </script>

</body>

</html>
<?php mysqli_close($con); ?>
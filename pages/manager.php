<?php
session_start();
include("connection.php");
require "fetchspecialty.php";
$message = '';
$message_type = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_type']) && $_POST['delete_type'] === 'specialty') {
  $delete_id = (int)$_POST['delete_id'];
  $delete_query = "DELETE FROM specialties WHERE id = $delete_id";
  if (mysqli_query($con, $delete_query)) {
    $_SESSION['message'] = "Specialty deleted successfully.";
    $_SESSION['message_type'] = "success";
  } else {
    $_SESSION['message'] = "Error deleting specialty: " . mysqli_error($con);
    $_SESSION['message_type'] = "error";
  }
  header("Location: " . $_SERVER['PHP_SELF']);
  exit();
}
// After redirect: retrieve and clear flash message
if (isset($_SESSION['message'])) {
  $message = $_SESSION['message'];
  $message_type = $_SESSION['message_type'] ?? '';
  unset($_SESSION['message'], $_SESSION['message_type']);
}
// Fetch faculty and filiere list (your existing code)
$id_organizer = $_SESSION['id_organizer'];
$get_faculty = mysqli_query($con, "SELECT id_faculty FROM organizer WHERE id = '$id_organizer'");
$result_faculty = mysqli_fetch_assoc($get_faculty);
$id_faculty = (int)$result_faculty['id_faculty'];
$filiere_query = mysqli_query($con, "
    SELECT filiere.id_filiere, filiere.name_filiere 
    FROM filiere
    INNER JOIN department ON filiere.id_department = department.id_department
    WHERE department.id_faculty = $id_faculty
");
$filiere_list = [];
while ($row = mysqli_fetch_assoc($filiere_query)) {
  $filiere_list[] = $row;
}
$departments = [];
if (isset($id_faculty)) {
  $departments = json_decode(fetchdepartment($id_faculty), true);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="/css/all.min.css" />
  <link rel="stylesheet" href="/css/managstyle.css" />
  <link rel="website icon" type="png" href="/images/logo.png" />
  <title>Exam Organization</title>
</head>
<body>
  <div class="dashboard" id="dashboard">
    <div class="sidebar">
      <div class="logo">
        <img src="/images/logo.png" alt="Logo" />
        <h1>Exam<span class="danger">Org</span></h1>
      </div>
      <ul>
        <li><a href="#dashboard-page" class="nav-link active"><i class="fa-solid fa-chart-bar"></i><span>Dashboard</span></a></li>
        <li><a href="#level" class="nav-link"><i class="fa-solid fa-l"></i><span>Level</span></a></li>
        <li><a href="#module" class="nav-link"><i class="fa-solid fa-superscript"></i><span>Module</span></a></li>
        <li style="cursor: pointer;"><a onclick="window.location.href='/pages/step1.php'; return false;"><i class="fa-solid fa-calendar-plus"></i><span>Time table</span></a></li>
        <li style="cursor: pointer;"><a onclick="window.location.href='/pages/all_exam_schedules.php'; return false;"><i class="fa-solid fa-calendar-check"></i><span>All Exam Schedules</span></a></li>
        <li style="cursor: pointer;"><a onclick="window.location.href='/pages/view_teacher_sch.php'; return false;"><i class="fa-solid fa-chalkboard-user"></i><span>View Teacher Slots</span></a></li>
        <li style="cursor: pointer;"><a onclick="window.location.href='/pages/view_room-sch.php'; return false;"><i class="fa-solid fa-person-booth"></i><span>Room Schedule Viewer</span></a></li>
        <li style="cursor: pointer;"><a onclick="window.location.href='/pages/exam_day_sign.php'; return false;"><i class="fa-solid fa-user-check"></i><span>Daily Attendance Sheet</span></a></li>
        <li style="cursor: pointer;"><a onclick="window.location.href='/pages/homeorgan.php'; return false;"><i class="fa-solid fa-circle-left"></i><span>Go back</span></a></li>
        <li>
          <form action="logout.php" method="POST" style="display: inline;">
            <button type="submit" class="btn-logout" style="background:none; border:none; cursor:pointer; color: #0252d1; font: inherit;padding-left:20px; display: flex; align-items: center;">
              <i class="fa-solid fa-sign-out-alt" style="margin-right: 8px;"></i><span>Logout</span>
            </button>
          </form>
        </li>
      </ul>
    </div>
    <div class="body-dash">
      <div class="navigation">
        <button class="btn" data-target="#dashboard" data-btn><i class="fa-solid fa-bars"></i></button>
        <div class="nav-settings">
          <button class="btn1" id="dark-mode-toggle"><i class="fa-solid fa-lightbulb"></i></button>
          <button class="btn1"><i class="fa-solid fa-gear"></i></button>
        </div>
      </div>
      <section id="dashboard-page" class="page active">
        <div class="tabs">
          <div class="tab active" data-target="Specialty">
            <img src="/images/spese.png" alt="imageSpecialty" />
            <h3>Specialty</h3>
          </div>
          <div class="tab" data-target="teachers">
            <img src="/images/teachers.png" alt="imageteacher" />
            <h3>Teachers</h3>
          </div>
          <div class="tab" data-target="classes">
            <img src="/images/clases.png" alt="imageclasses" />
            <h3>Classes</h3>
          </div>
        </div>
        <div class="content">
          <!--___________________________________spcailty____________________________________________________________-->
          <div class="content-section active" id="Specialty">
            <h2>Specialties Management</h2>
            <div class="form-container">
              <?php if ($message) { ?>
                <div id="specialty-message" class="<?= htmlspecialchars($message_type) ?>">
                  <?= htmlspecialchars($message) ?>
                </div>
              <?php } ?>
              <form id="specialtyForm" method="POST" action="insert_spcie.php">
                <div class="input-group">
                  <select name="id_filiere" id="id_filiere" required>
                    <option value="" disabled selected>Select Filiere</option>
                    <?php foreach ($filiere_list as $row) { ?>
                      <option value="<?= htmlspecialchars($row['id_filiere']) ?>">
                        <?= htmlspecialchars($row['name_filiere']) ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>
                <div class="input-group">
                  <input type="text" id="specialty_name" name="specialty_name" required>
                  <label>Enter specialty name</label>
                </div>
                <input type="hidden" name="specialty_id" id="specialty_id">
                <button type="submit" name="save_specialty" class="btn btn-add"><i class="fas fa-save"></i> Save</button>
              </form>
              <!-- Edit Specialty Form -->
              <form id="editSpecialtyForm" method="POST" action="editspcie.php">
                <h3>Edit Specialty</h3>
                <input type="hidden" id="edit_specialty_id" name="specialty_id" required>
                <div class="input-group">
                  <select name="id_filiere" id="edit_id_filiere" required>
                    <option value="" disabled>Select Filiere</option>
                    <?php foreach ($filiere_list as $row) { ?>
                      <option value="<?= htmlspecialchars($row['id_filiere']) ?>">
                        <?= htmlspecialchars($row['name_filiere']) ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>
                <div class="input-group">
                  <input type="text" id="edit_specialty_name" name="specialty_name" required>
                  <label>Enter specialty name</label>
                </div>
                <button type="submit" name="edit_specialty" class="btn btn-edit"><i class="fa-solid fa-pen-to-square"></i>Update</button>
                <button type="button" id="cancelEdit" class="btn">Cancel</button>
              </form>
            </div>
            <div class="specialties-container">
              <?php
              $specialties = $con->query("
                SELECT specialties.*, filiere.name_filiere 
                FROM specialties 
                INNER JOIN filiere ON specialties.id_filiere = filiere.id_filiere 
                ORDER BY specialties.id DESC
              ");
              if ($specialties && $specialties->num_rows > 0) {
                while ($specialty = $specialties->fetch_assoc()) {
              ?>
                  <div class="specialty-card">
                    <div class="specialty-header">
                      <h3><?= htmlspecialchars($specialty['name']) ?>
                        <small style="font-size: 0.8em; color: #777;">(<?= htmlspecialchars($specialty['name_filiere']) ?>)</small>
                      </h3>
                      <div class="action-buttons">
                        <button type="button" class="btn btn-edit btn-small"
                          onclick="editSpecialty(<?= htmlspecialchars(json_encode($specialty)) ?>)">
                          <i class="fas fa-pen"></i>
                        </button>
                        <form method="POST" action="" style="display:inline;">
                          <input type="hidden" name="delete_type" value="specialty">
                          <input type="hidden" name="delete_id" value="<?= htmlspecialchars($specialty['id']) ?>">
                          <button type="submit" class="btn btn-danger btn-small" style="padding-bottom: 26px;" onclick="return confirm('Are you sure you want to delete this specialty?');">
                            <i class="fas fa-trash"></i>
                          </button>
                        </form>
                      </div>
                    </div>
                  </div>
              <?php
                }
              } else {
                echo "<p>No specialties found.</p>";
              }
              ?>
            </div>
          </div>
          <!--___________________________________level__________________________________________________________________-->
          <div class="content-section" id="level">
            <h2>Levels Management</h2>
            <form method="POST" action="insert_level.php">
              <?php $specialties = fetchSpecialties($con); ?>
              <div class="input-group">
                <select name="specialty_id" id="specialty_id" required>
                  <option value="" disabled selected>Select Specialty</option>
                  <?php foreach ($specialties as $specialty) { ?>
                    <option value="<?= htmlspecialchars($specialty['id']) ?>">
                      <?= htmlspecialchars($specialty['name']) ?>
                    </option>
                  <?php } ?>
                </select>
              </div>
              <div class="input-group">
                <input type="text" id="level_name" name="level_name" required>
                <label for="level_name">Level Name</label>
              </div>
              <div class="input-group">
                <input type="number" id="student_count" name="student_count" required>
                <label for="student_count">Number of Students</label>
              </div>
              <button type="submit" name="add_level" class="btn btn-add"><i class="fas fa-save"></i> Add Level</button>
            </form>
            <!-- Edit Level Form -->
            <form method="POST" action="edit_level.php" id="editLevelForm" style="display: none;">
              <input type="hidden" id="edit_level_id" name="level_id" required>
              <div class="input-group">
                <select name="specialty_id" id="edit_specialty_id" required>
                  <option value="" disabled>Select Specialty</option>
                  <?php foreach ($specialties as $specialty) { ?>
                    <option value="<?= htmlspecialchars($specialty['id']) ?>">
                      <?= htmlspecialchars($specialty['name']) ?>
                    </option>
                  <?php } ?>
                </select>
              </div>
              <div class="input-group">
                <input type="text" id="edit_level_name" name="level_name" required>
                <label for="edit_level_name">Level Name</label>
              </div>
              <div class="input-group">
                <input type="number" id="edit_student_count" name="student_count" required>
                <label for="edit_student_count">Number of Students</label>
              </div>
              <button type="submit" name="edit_level" class="btn btn-save"><i class="fas fa-save"></i> Save Changes</button>
              <button type="button" id="cancelLevelEdit" class="btn btn-cancel">Cancel</button>
            </form>
            <div class="levels-container">
              <?php $levels = fetchLevels(); ?>
              <?php if (!empty($levels)) {
                foreach ($levels as $level) { ?>
                  <div class="level-card">
                    <div class="level-header">
                      <h3><?= htmlspecialchars($level['name']) ?> <small style="font-size: 0.8em; color: #777;">(<?= htmlspecialchars($level['specialty_name']) ?>)</small></h3>
                      <p>Students:<?= (int)$level['student_count'] ?></p>
                      <div class="action-buttons">
                        <button type="button" class="btn btn-edit btn-small" onclick='editLevel(<?= json_encode($level) ?>)'><i class="fas fa-pen"></i></button>
                        <form method="POST" action="delete_level.php" style="display:inline;">
                          <input type="hidden" name="delete_type" value="level">
                          <input type="hidden" name="delete_id" value="<?= htmlspecialchars($level['id']) ?>">
                          <button type="submit" class="btn btn-danger btn-small" style="padding-bottom: 26px;" onclick="return confirm('Are you sure you want to delete this level?');"><i class="fas fa-trash"></i></button>
                        </form>
                      </div>
                    </div>
                  </div>
              <?php }
              } else {
                echo "<p>No levels found.</p>";
              } ?>
            </div>
          </div>
          <!--___________________________________module______________________________________________________________________-->
          <div class="content-section" id="module">
            <h2>Modules Management</h2>
            <form method="POST" action="insert_module.php"> <!-- Action to save the module -->
              <div class="input-group">
                <select name="specialty_id" id="module_specialty_id" required onchange="fetchLevels(this.value)">
                  <option value="" disabled selected>Select Specialty</option>
                  <?php foreach ($specialties as $specialty) { ?>
                    <option value="<?= htmlspecialchars($specialty['id']) ?>">
                      <?= htmlspecialchars($specialty['name']) ?>
                    </option>
                  <?php } ?>
                </select>
              </div>
              <div class="input-group">
                <select name="level_id" id="module_level_id" required>
                  <option value="" disabled selected>Select Level</option>
                  <!-- Levels will be populated based on the selected specialty -->
                </select>
              </div>
              <div class="input-group">
                <input type="text" id="module_name" name="module_name" required>
                <label for="module_name">Module Name</label>
              </div>
              <button type="submit" name="add_module" class="btn btn-add"><i class="fas fa-save"></i> Add Module</button>
            </form>

            <!-- Edit module -->
            <form id="editModuleForm" method="POST" action="edit_module.php" style="display: none;">
              <input type="hidden" id="edit_module_id" name="module_id" required>
              <div class="input-group">
                <select name="specialty_id" id="edit_module_specialty_id" required onchange="fetchLevelsForEdit(this.value)">
                  <option value="" disabled>Select Specialty</option>
                  <?php foreach ($specialties as $specialty) { ?>
                    <option value="<?= htmlspecialchars($specialty['id']) ?>">
                      <?= htmlspecialchars($specialty['name']) ?>
                    </option>
                  <?php } ?>
                </select>
              </div>
              <div class="input-group">
                <select name="level_id" id="edit_module_level_id" required>
                  <option value="" disabled selected>Select Level</option>
                  <!-- Levels will be populated dynamically -->
                </select>
              </div>
              <div class="input-group">
                <input type="text" id="edit_module_name" name="module_name" required>
                <label for="edit_module_name">Module Name</label>
              </div>
              <button type="submit" name="edit_module" class="btn btn-save"><i class="fas fa-save"></i> Save Changes</button>
              <button type="button" id="cancelModuleEdit" class="btn btn-cancel">Cancel</button>
            </form>
            <div class="modules-container">
              <?php $modules = fetchModules(); ?>
              <?php if (!empty($modules)) {
                foreach ($modules as $module) { ?>
                  <div class="module-card">
                    <div class="module-header">
                      <h3><?= htmlspecialchars($module['name']) ?></h3>
                      <p>Specialty:<?= htmlspecialchars($module['specialty_name']) ?> |
                        Level:<?= htmlspecialchars($module['level_name']) ?></p>
                      <div class="action-buttons">
                        <button type="button" class="btn btn-edit btn-small" onclick='editModule(<?= json_encode($module) ?>)'><i class="fas fa-pen"></i></button>
                        <form method="POST" action="delete_module.php" style="display:inline;">
                          <input type="hidden" name="delete_type" value="module">
                          <input type="hidden" name="delete_id" value="<?= htmlspecialchars($module['id']) ?>">
                          <button type="submit" class="btn btn-danger btn-small" style="padding-bottom: 26px;" onclick="return confirm('Are you sure you want to delete this module?');"><i class="fas fa-trash"></i></button>
                        </form>
                      </div>
                    </div>
                  </div>
              <?php }
              } else {
                echo "<p>No modules found.</p>";
              } ?>
            </div>
          </div>
          <!--___________________________________room-classes________________________________________________________________-->
          <div class="content-section" id="classes">
            <h2>Classes Management</h2>
            <!-- Form to Add New Class -->
            <form method="POST" action="insert_class.php">
              <div class="input-group">
                <select name="id_deprtment" id="id_deprtment" required>
                  <option value="" disabled selected>Select Department</option>
                  <?php foreach ($departments as $department) { ?>
                    <option value="<?= htmlspecialchars($department['id_department']) ?>">
                      <?= htmlspecialchars($department['name_deprtment']) ?>
                    </option>
                  <?php } ?>
                </select>
              </div>
              <div class="input-group">
                <input type="text" id="class_name" name="class_name" required>
                <label for="class_name">Class Name</label>
              </div>
              <div class="input-group">
                <input type="number" id="student_count" name="student_count" required>
                <label for="student_count">Number of Students</label>
              </div>
              <div class="input-group">
                <input type="number" id="supervisor_count" name="supervisor_count" required>
                <label for="supervisor_count">supervisor_count</label>
              </div>
              <button type="submit" name="add_class" class="btn btn-add"><i class="fas fa-save"></i> Add Class</button>
            </form>
            <!-- Edit Class Form -->
            <form method="POST" action="edit_class.php" id="editClassForm" style="display: none;">
              <input type="hidden" id="edit_class_id" name="class_id" required>
              <div class="input-group">
                <select name="id_deprtment" id="edit_id_deprtment" required>
                  <option value="" disabled>Select Department</option>
                  <?php foreach ($departments as $department) { ?>
                    <option value="<?= htmlspecialchars($department['id_department']) ?>">
                      <?= htmlspecialchars($department['name_deprtment']) ?>
                    </option>
                  <?php } ?>
                </select>
              </div>
              <div class="input-group">
                <input type="text" id="edit_class_name" name="class_name" required>
                <label for="edit_class_name">Class Name</label>
              </div>
              <div class="input-group">
                <input type="number" id="edit_count" name="student_count" required>
                <label for="edit_student_count">Number of Students</label>
              </div>
              <div class="input-group">
                <input type="number" id="edit_supervisor_count" name="supervisor_count" required>
                <label for="edit_supervisor_count">Number of Supervisors</label>
              </div>
              <button type="submit" name="edit_class" class="btn btn-save"><i class="fas fa-save"></i> Save Changes</button>
              <button type="button" id="cancelClassEdit" class="btn btn-cancel">Cancel</button>
            </form>
            <div class="classes-container">
              <?php $rooms = fetchRooms(); ?>
              <?php if (!empty($rooms)) {
                foreach ($rooms as $room) { ?>
                  <div class="room-card">
                    <h4>room: <?= htmlspecialchars($room['name']) ?></h4>
                    <p>Students: <?= (int)$room['student_count'] ?></p>
                    <p>Supervisors: <?= (int)$room['supervisor_count'] ?></p>
                    <div class="action-buttons">
                      <button type="button" class="btn btn-edit btn-small" onclick='editRoom(<?= json_encode($room) ?>)'><i class="fas fa-pen"></i></button>
                      <form method="POST" action="delete_room.php" style="display:inline;">
                        <input type="hidden" name="delete_type" value="room">
                        <input type="hidden" name="delete_id" value="<?= htmlspecialchars($room['id']) ?>">
                        <button type="submit" class="btn btn-danger btn-small" style="padding-bottom: 26px;" onclick="return confirm('Are you sure you want to delete this room?');"><i class="fas fa-trash"></i></button>
                      </form>
                    </div>
                  </div>
              <?php }
              } else {
                echo "<p>No rooms found.</p>";
              } ?>
            </div>
          </div>
          <!--___________________________________teacher_____________________________________________________________________-->
          <div class="content-section" id="teachers">
            <h2>Teachers Management</h2>
            <form id="form_teacher" method="POST" action="add_teacher.php" class="form_teacher">
              <input type="hidden" name="id_faculty" value="<?= htmlspecialchars($id_faculty) ?>"> <!-- Hidden field for faculty ID -->
              <div class="input-group">
                <input type="text" id="name" name="name" required><label for="name">Name</label>
              </div>
              <div class="input-group">
                <input type="email" id="email" name="email" required> <label for="email">Email</label>
              </div>
              <div class="input-group">
                <input type="number" id="hourly_size" name="hourly_size" required> <label for="hourly_size">Hourly Size</label>
              </div>
              <div class="input-group" select-container>
                <select name="modules[]" id="modules" multiple>
                  <!-- Options will be populated from the database -->
                  <?php
                  // Fetch modules with their associated levels and specialties
                  $modules_query = "
                  SELECT 
                  m.id AS module_id, 
                  m.name AS module_name, 
                  l.name AS level_name, 
                  s.name AS specialty_name
                  FROM modules m
                  LEFT JOIN levels l ON m.level_id = l.id
                  LEFT JOIN specialties s ON l.specialty_id = s.id
                  ";
                  $modules_result = mysqli_query($con, $modules_query);
                  while ($module = mysqli_fetch_assoc($modules_result)) {
                    $module_id = htmlspecialchars($module['module_id']);
                    $module_name = htmlspecialchars($module['module_name']);
                    $level_name = htmlspecialchars($module['level_name']);
                    $specialty_name = htmlspecialchars($module['specialty_name']);
                    echo '<option value="' . $module_id . '">' . $module_name . ' | ' . $level_name . ' | ' . $specialty_name . '</option>';
                  }
                  ?>
                </select>
                <p style="color: gray; margin-left:12px">Select Modules (hold Ctrl or Cmd to select multiple)</p>
              </div>
              <button type="submit" name="add_teacher" class="btn btn-add"><i class="fas fa-save"></i> Add Teacher</button>
            </form>
            <!-- Edit Teacher Form -->
            <form id="editTeacherForm" method="POST" action="edit_teacher.php" style="display: none;">
              <h3>Edit Teacher</h3>
              <input type="hidden" id="edit_teacher_id" name="teacher_id" required>
              <div class="input-group">
                <input type="text" id="edit_name" name="name" required>
                <label for="edit_name">Name</label>
              </div>
              <div class="input-group">
                <input type="email" id="edit_email" name="email" required>
                <label for="edit_email">Email</label>
              </div>
              <div class="input-group">
                <input type="number" id="edit_hourly_size" name="hourly_size" required>
                <label for="edit_hourly_size">Hourly Size</label>
              </div>
              <div class="input-group" select-container>
                <select name="modules[]" id="edit_modules" multiple>
                  <!-- Options will be populated from the database -->
                  <?php
                  // Fetch modules with their associated levels and specialties
                  $modules_query = "
                  SELECT 
                  m.id AS module_id, 
                  m.name AS module_name, 
                  l.name AS level_name, 
                  s.name AS specialty_name
                  FROM modules m
                  LEFT JOIN levels l ON m.level_id = l.id
                  LEFT JOIN specialties s ON l.specialty_id = s.id
                  ";
                  $modules_result = mysqli_query($con, $modules_query);
                  while ($module = mysqli_fetch_assoc($modules_result)) {
                    $module_id = htmlspecialchars($module['module_id']);
                    $module_name = htmlspecialchars($module['module_name']);
                    $level_name = htmlspecialchars($module['level_name']);
                    $specialty_name = htmlspecialchars($module['specialty_name']);
                    echo '<option value="' . $module_id . '">' . $module_name . ' | ' . $level_name . ' | ' . $specialty_name . '</option>';
                  }
                  ?>
                </select>
                <p style="color: gray; margin-left:12px">Select Modules (hold Ctrl or Cmd to select multiple)</p>
              </div>
              <button type="submit" name="edit_teacher" class="btn btn-save"><i class="fas fa-save"></i> Save Changes</button>
              <button type="button" id="cancelEdit" class="btn btn-cancel">Cancel</button>
            </form>
            <div class="teachers-container">
              <?php
              // Fetch teachers from the database
              $teachers_query = "
              SELECT t.*, GROUP_CONCAT(m.name SEPARATOR ', ') AS modules
              FROM teachers t
              LEFT JOIN teacher_modules tm ON t.id = tm.teacher_id
              LEFT JOIN modules m ON tm.module_id = m.id
              WHERE t.id_faculty = '$id_faculty'
              GROUP BY t.id
              ";
              $teachers_result = mysqli_query($con, $teachers_query);
              if ($teachers_result && mysqli_num_rows($teachers_result) > 0) {
                while ($teacher = mysqli_fetch_assoc($teachers_result)) {
              ?>
                  <div class="teacher-card">
                    <div class="teacher-header">
                      <h3><?= htmlspecialchars($teacher['name']) ?> <small style="font-size: 0.8em; color: #777;"><br>(<?= htmlspecialchars($teacher['email']) ?>)</small></h3>
                      <p>Hourly Size: <?= (int)$teacher['hourly_size'] ?></p>
                      <p>Modules: <?= htmlspecialchars($teacher['modules']) ?></p>
                      <div class="action-buttons">
                        <button type="button" class="btn btn-edit btn-small" onclick='editTeacher(<?= json_encode($teacher) ?>)'><i class="fas fa-pen"></i></button>
                        <form method="POST" action="delete_teacher.php" style="display:inline;">
                          <input type="hidden" name="delete_type" value="teacher">
                          <input type="hidden" name="delete_id" value="<?= htmlspecialchars($teacher['id']) ?>">
                          <button type="submit" class="btn btn-danger btn-small" style="padding-bottom: 26px;" onclick="return confirm('Are you sure you want to delete this teacher?');"><i class="fas fa-trash"></i></button>
                        </form>
                      </div>
                    </div>
                  </div>
              <?php
                }
              } else {
                echo "<p>No teachers found.</p>";
              }
              ?>
            </div>
          </div>
        </div>
        <script src="/scripts/scriptmanager.js"></script>
</body>
</html>
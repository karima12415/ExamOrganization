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
$selected_filiere = $_POST['filiere_input'] ?? '';
$selected_exam_type = $_POST['exam_type'] ?? '';
$selected_date = $_POST['selected_date'] ?? '';
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
        <h2><img src="/images/sheetteacher.PNG">Daily Teacher Attendance Sheet</h2>
        <form method="post" action="" class="form_all">
            <div class="input_group_all">
                <label>Filiere</label>
                <select name="filiere_input" id="filiere_input" required>
                    <option value="">-- Select Filiere --</option>
                    <?php foreach ($filieres as $f) {
                        $selected = ($f['id_filiere'] == $selected_filiere) ? "selected" : "";
                        echo "<option value='{$f['id_filiere']}' $selected>{$f['name_filiere']}</option>";
                    } ?>
                </select>

            </div>
            <div class="input_group_all">
                <label>Exam Type</label>
                <select name="exam_type" required>
                    <option value="" hidden>-- Select Exam Type --</option>
                    <option value="semester1_Regular" <?= ($selected_exam_type === "semester1_Regular") ? "selected" : "" ?>>Regular Exam (Semester 1)</option>
                    <option value="semester1_Makeup" <?= ($selected_exam_type === "semester1_Makeup") ? "selected" : "" ?>>Make-up Exam (Semester 1)</option>
                    <option value="semester1_Remedial" <?= ($selected_exam_type === "semester1_Remedial") ? "selected" : "" ?>>Remedial Exam (Semester 1)</option>
                    <option value="semester2_Regular" <?= ($selected_exam_type === "semester2_Regular") ? "selected" : "" ?>>Regular Exam (Semester 2)</option>
                    <option value="semester2_Makeup" <?= ($selected_exam_type === "semester2_Makeup") ? "selected" : "" ?>>Make-up Exam (Semester 2)</option>
                    <option value="semester2_Remedial" <?= ($selected_exam_type === "semester2_Remedial") ? "selected" : "" ?>>Remedial Exam (Semester 2)</option>
                </select>

            </div>
            <div class="input_group_all">
                <label>Select Date</label>
                <input type="date" name="selected_date" required value="<?= $selected_date ?>">
            </div>
            <input type="submit" value="Search" class="search_btn">
        </form>
    </div>
    <div>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filiere_input'], $_POST['exam_type'], $_POST['selected_date'])) {
            $filiere_id = (int)$_POST['filiere_input'];
            $exam_type = mysqli_real_escape_string($con, $_POST['exam_type']);
            $selected_date = mysqli_real_escape_string($con, $_POST['selected_date']);



            // Check if exam schedule exists
            $check_query = "SELECT sch.id FROM exam_schedules sch JOIN exam_sessions es ON sch.id = es.schedule_id
            WHERE sch.filiere_id = $filiere_id
            AND sch.exam_type = '$exam_type'
            AND es.exam_date = '$selected_date' LIMIT 1 ";
            $check_result = mysqli_query($con, $check_query);

            if (mysqli_num_rows($check_result) === 0) {
                echo "<p class='info_search_eror'>No scheduled exams on this day.</p>";
            } else {
                // Get all teachers supervising on that day from session_rooms
                $query = "
                SELECT DISTINCT t.name AS teacher_name
                FROM teachers t
                JOIN session_rooms sr ON t.id = sr.teacher_id
                JOIN exam_sessions es ON sr.session_id = es.id
                JOIN exam_schedules sch ON es.schedule_id = sch.id
                WHERE sch.filiere_id = $filiere_id
                AND sch.exam_type = '$exam_type'
                AND es.exam_date = '$selected_date'
                ";
                $result = mysqli_query($con, $query);

                if (mysqli_num_rows($result) > 0) {
                    echo "<table style='width: 60rem;margin-left: 90px;' class='custom-table_search'>
                    <tr style='background-color: #f2f2f2;'>
                        <th style='border: 1px solid #ccc; padding: 8px;'>#</th>
                        <th style='border: 1px solid #ccc; padding: 8px;'>Teacher Name</th>
                        <th style='border: 1px solid #ccc; padding: 8px;'>Signature</th>
                    </tr>";
                    $i = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                        <td style='border: 1px solid #ccc; padding: 8px;'>$i</td>
                        <td style='border: 1px solid #ccc; padding: 8px;'>{$row['teacher_name']}</td>
                        <td style='border: 1px solid #ccc; height: 50px;'></td>
                      </tr>";
                        $i++;
                    }
                    echo "</table>";

                    echo "<button class='print_btn' onclick='window.print()'><i class='fa-solid fa-print'></i>Print</button>";
                } else {
                    echo "<p>No supervisors found for this day.</p>";
                }
            }
        }

        ?>
    </div>
</body>


</html>
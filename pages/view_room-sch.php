<?php
session_start();
include("connection.php");
$id_organizer = $_SESSION['id_organizer'];
$id_faca = mysqli_query($con, "SELECT id_faculty FROM organizer WHERE id = '$id_organizer'");
$id_fac = mysqli_fetch_assoc($id_faca);
$fac_id = $id_fac['id_faculty'];

$rooms = $con->query("SELECT rooms.* FROM rooms
    JOIN department ON rooms.id_department = department.id_department
    WHERE department.id_faculty = $fac_id
");
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
        <h2><img src="/images/roomicon.PNG">Room Schedule Viewer</h2>
        <form method="POST" class="form_all">
            <div class="input_group_all">
                <label for="room_id">Select Room:</label>
                <select name="room_id" id="room_id" required>
                    <option value="">-- Select Room --</option>
                    <?php while ($room = $rooms->fetch_assoc()) : ?>
                        <option value="<?= $room['id'] ?>"><?= htmlspecialchars($room['name']) ?></option>
                    <?php endwhile; ?>
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
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['room_id'], $_POST['exam_type'])) {
                $room_id = intval($_POST['room_id']);
                $exam_type = mysqli_real_escape_string($con, $_POST['exam_type']);

                $query = "
                SELECT 
                es.exam_date,
                es.slot,
                m.name AS module_name,
                l.name AS level_name,
                GROUP_CONCAT(t.name SEPARATOR ', ') AS teachers
                FROM session_rooms sr
                JOIN exam_sessions es ON sr.session_id = es.id
                JOIN exam_schedules sch ON es.schedule_id = sch.id
                JOIN levels l ON es.level_id = l.id
                JOIN modules m ON es.module_id = m.id
                JOIN teachers t ON sr.teacher_id = t.id
                WHERE sr.room_id = $room_id
                    AND sch.exam_type = '$exam_type'
                GROUP BY es.exam_date, es.slot, es.module_id
                ORDER BY es.exam_date, es.slot
                ";

                $result = mysqli_query($con, $query);

                $dates = [];
                $slots = [];
                $data = [];

                while ($row = mysqli_fetch_assoc($result)) {
                    $date = $row['exam_date'];
                    $slot = $row['slot'];
                    $text = "<span style='color:#092a5f ; font-weight: 500;'>Level: </span>".$row['level_name']."<span style='color:#092a5f ; font-weight: 500;'> _ Module: </span>". $row['module_name']   . "<br>(" . $row['teachers'] . ")";
                    $data[$date][$slot][] = $text;
                    $dates[$date] = true;
                    $slots[$slot] = true;
                }

                if (!empty($data)) {
                    echo "<table class='custom-table_search' style='margin-top:30px;margin-left:122px'>";
                    echo "<thead><tr><th>Day</th>";
                    foreach (array_keys($slots) as $slot) {
                        echo "<th>$slot</th>";
                    }
                    echo "</tr></thead><tbody>";

                    foreach (array_keys($dates) as $date) {
                        echo "<tr><td>$date</td>";
                        foreach (array_keys($slots) as $slot) {
                            echo "<td>";
                            if (isset($data[$date][$slot])) {
                                echo implode("<hr>", $data[$date][$slot]);
                            } else {
                                echo "-";
                            }
                            echo "</td>";
                        }
                        echo "</tr>";
                    }

                    echo "</tbody></table>";
                } else {
                    echo "<p class='info_search_eror'>No sessions found for this room and exam type.</p>";
                }
            }
            ?>


        </div>
    </div>

</body>

</html>
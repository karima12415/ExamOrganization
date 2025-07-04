<?php
session_start();
include 'connection.php';

// التحقق من وجود البيانات المطلوبة
if (!isset($_SESSION['schedule_step2']) || !isset($_SESSION['scheduling_data'])) {
    die("Missing required session data!");
}

// استرجاع البيانات من الجلسة
$schedule = $_SESSION['schedule_step2'];
$scheduling_data = $_SESSION['scheduling_data'];
$id_organizer = $_SESSION['id_organizer'];

// استخراج بيانات الجدول الزمني
$filiere_id = (int)$scheduling_data['filiere_id'];
$specialty_id = (int)$scheduling_data['specialty_id'];
$exam_type = mysqli_real_escape_string($con, $scheduling_data['exam_type']);
$start_date = mysqli_real_escape_string($con, $scheduling_data['start_date']);
$end_date = mysqli_real_escape_string($con, $scheduling_data['end_date']);
$slot_start = mysqli_real_escape_string($con, $scheduling_data['slot_start']);
$slots = (int)$scheduling_data['slots'];

// 1. حفظ جدول الامتحانات الرئيسي
$sql = "INSERT INTO exam_schedules 
        (filiere_id, specialty_id, exam_type, start_date, end_date, slot_start, slots) 
        VALUES ($filiere_id, $specialty_id, '$exam_type', '$start_date', '$end_date', '$slot_start', $slots)";

if (!mysqli_query($con, $sql)) {
    $_SESSION['error_message'] = "Error saving the exam schedule: " . mysqli_error($con);
    header("Location: step3.php");
    exit();
}

$schedule_id = mysqli_insert_id($con);

// 2. حفظ جلسات الامتحانات
$module_teachers = $_POST['module_teacher'] ?? [];
$supervisors = $_POST['supervisors'] ?? [];

$teacher_Used_hour = []; // لحساب عدد مرات استخدام كل أستاذ كمراقب

foreach ($schedule as $date => $slots_on_day) {
    $escaped_date = mysqli_real_escape_string($con, $date);

    foreach ($slots_on_day as $slot => $entries) {
        $escaped_slot = mysqli_real_escape_string($con, $slot);

        foreach ($entries as $index => $entry) {
            $level_id = (int)$entry['level_id'];
            $module_id = (int)$entry['module_id'];
            $room_ids = $entry['room_ids'];

            // استخراج أستاذ المادة
            $module_teacher_id = 'NULL';
            if (isset($module_teachers[$date][$slot][$index]) && is_numeric($module_teachers[$date][$slot][$index])) {
                $module_teacher_id = (int)$module_teachers[$date][$slot][$index];
            }

            // حفظ جلسة الامتحان
            $sql = "INSERT INTO exam_sessions 
                    (schedule_id, exam_date, slot, level_id, module_id, module_teacher_id) 
                    VALUES ($schedule_id, '$escaped_date', '$escaped_slot', $level_id, $module_id, $module_teacher_id)";

            if (!mysqli_query($con, $sql)) {
                $_SESSION['error_message'] = "Error saving the exam session: " . mysqli_error($con);
                header("Location: step3.php");
                exit();
            }

            $session_id = mysqli_insert_id($con);

            // 3. حفظ القاعات والمراقبين في session_rooms
            foreach ($room_ids as $room_id) {
                $room_id = (int)$room_id;

                if (isset($supervisors[$date][$slot][$index][$room_id])) {
                    foreach ($supervisors[$date][$slot][$index][$room_id] as $teacher_id) {
                        if (!empty($teacher_id) && is_numeric($teacher_id)) {
                            $teacher_id = (int)$teacher_id;

                            $sql = "INSERT INTO session_rooms (session_id, room_id, teacher_id)
                                    VALUES ($session_id, $room_id, $teacher_id)";

                            if (!mysqli_query($con, $sql)) {
                                $_SESSION['error_message'] = "Error saving session_rooms: " . mysqli_error($con);
                                header("Location: step3.php");
                                exit();
                            }

                            // حساب عدد مرات اختيار كل أستاذ
                            if (!isset($teacher_Used_hour[$teacher_id])) {
                                $teacher_Used_hour[$teacher_id] = 0;
                            }
                            $teacher_Used_hour[$teacher_id]++;
                        }
                    }
                }
            }
        }
    }
}
foreach ($teacher_Used_hour as $teacher_id => $count) {
    $updateSql = "UPDATE teachers SET used_hours = used_hours + $count WHERE id = $teacher_id";
    if (!mysqli_query($con, $updateSql)) {
        $_SESSION['error_message'] = "Error updating teacher hours: " . mysqli_error($con);
        header("Location: step3.php");
        exit();
    }
}
// رسالة نجاح
header("Location: step1.php?success=1");
exit();
?>

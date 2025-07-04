<?php
if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo "<div class='success-message'>The exam schedule has been saved successfully!</div>";
}
?>
<?php
session_start();
include 'connection.php'; // Use your existing connection file
$id_organizer = $_SESSION['id_organizer'];
// Get filieres
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup'])) {
    $filiere_id = (int)$_POST['filiere_input'];
    $specialty_id = (int)$_POST['specialty_id'];
    $exam_type = mysqli_real_escape_string($con, $_POST['exam_type']);

    // تحقق مما إذا كان التوقيت موجود مسبقًا
    $check_query = "
        SELECT id FROM exam_schedules
        WHERE filiere_id = $filiere_id AND specialty_id = $specialty_id AND exam_type = '$exam_type'
    ";
    $check_result = mysqli_query($con, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        echo "<script>
        alert('You have already created a schedule for this specialty and exam type.');
        window.location.href = 'step1.php';
        </script>";
        exit();
    } else {
        // إذا لم يكن موجودًا، احفظ البيانات وأرسل إلى الصفحة التالية
        $_SESSION['scheduling_data'] = [
            'filiere_id' => (int)$_POST['filiere_input'],
            'specialty_id' => (int)$_POST['specialty_id'],
            'exam_type' => $_POST['exam_type'],
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'slot_start' => $_POST['slot_start'],
            'slots' => (int)$_POST['slots']
        ];
        header('Location: step2.php');
        exit();
    }
}

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
    <div class="content">
        <h2><img src="/images/step1img.png">Step1: Schedule Setup</h2>
        <form method="post" action="step1.php" onsubmit="return validateForm();">
            <div class="input_group">
                <label>Filiere</label>
                <select name="filiere_input" id="filiere_input" required onchange="handleFiliereSelect(this)">
                    <option value="">-- Select Filiere --</option>
                    <?php foreach ($filieres as $f) {
                        echo "<option value='{$f['id_filiere']}'>{$f['name_filiere']}</option>";
                    } ?>
                </select>
            </div>

            <div class="input_group">
                <label>Specialty</label>
                <select name="specialty_id" id="specialty_select" required disabled>
                    <option value="">-- Select Specialty --</option>
                </select>
            </div>

            <div class="input_group">
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

            <div class="input_group">
                <label>Start Date</label><input type="date" name="start_date" required>
            </div>
            <div class="input_group">
                <label>End Date</label><input type="date" name="end_date" required>
            </div>
            <div class="input_group">
                <label>Start Time of First Slot</label><input type="time" name="slot_start" required>
            </div>
            <div class="input_group">
                <label>Number of Slots per Day</label><input type="number" name="slots" min="1" max="4" required>
            </div>

            <button type="submit" name="setup">Next →</button>
        </form>
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

        function validateForm() {
            const startDate = new Date(document.querySelector('[name="start_date"]').value);
            const endDate = new Date(document.querySelector('[name="end_date"]').value);
            const slots = parseInt(document.querySelector('[name="slots"]').value);
            const specialty = document.getElementById("specialty_select").value;

            if (endDate < startDate) {
                alert('End date must be after or equal to the start date!');
                return false;
            }

            if (isNaN(slots) || slots < 1 || slots > 5) {
                alert('Number of slots must be between 1 and 5!');
                return false;
            }

            if (!specialty) {
                alert('Please select a specialty.');
                return false;
            }

            return true;
        }
    </script>
</body>

</html>
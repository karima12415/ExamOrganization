<?php
include('connection.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_module'])) {
    $level_id = (int)$_POST['level_id'];
    $module_name = mysqli_real_escape_string($con, $_POST['module_name']);
    $insert_query = "INSERT INTO modules (name,is_prog,level_id) VALUES ('$module_name',0,$level_id)";
    $check_query = "SELECT id FROM modules WHERE name = '$module_name' AND level_id = $level_id";
    $check_result = mysqli_query($con, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
        echo "<script>alert('This level already has a module assigned.'); window.location.href='manager.php#module';</script>";
        exit();
    }
    if (mysqli_query($con, $insert_query)) {
        echo "<script>alert('Module added successfully.'); window.location.href='manager.php#module';</script>";
        exit();
    } else {
        echo "<script>alert('Error adding module!'); window.location.href='manager.php#module';</script>";
        exit();
    }
}
?>

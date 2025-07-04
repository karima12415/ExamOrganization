<?php 
session_start();
include("connection.php");
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_module'])) {
    $module_id = (int)$_POST['module_id'];
    $module_name = mysqli_real_escape_string($con, $_POST['module_name']);
    $level_id = (int)$_POST['level_id'];
    $check_query = "SELECT id FROM modules WHERE name = '$module_name' AND level_id = $level_id AND id != $module_id";
    $check_result = mysqli_query($con, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
        echo "<script>alert('This level already has a module with this name.'); window.location.href='manager.php#module';</script>";
        exit();
    }
    $update_query = "UPDATE modules SET name = '$module_name', level_id = $level_id WHERE id = $module_id";
    if (mysqli_query($con, $update_query)) {
        echo "<script>alert('Module updated successfully.'); window.location.href='manager.php#module';</script>";
        exit();
    } else {
        echo "<script>alert('Error updating module.'); window.location.href='manager.php#module';</script>";
        exit();
    }
}
?>

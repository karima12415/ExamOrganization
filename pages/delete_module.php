<?php
session_start();
include("connection.php");
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_type']) && $_POST['delete_type'] === 'module') {
    $delete_id = (int)$_POST['delete_id'];
    $delete_query = "DELETE FROM modules WHERE id = $delete_id";
    if (mysqli_query($con, $delete_query)) {
        echo "<script>alert('Module deleted successfully.'); window.location.href='manager.php#level';</script>";
        exit();
    } else {
        echo "<script>alert('Error deleting module!'); window.location.href='manager.php#level';</script>";
        exit();
    }
}
?>

<?php
session_start();
include("connection.php");
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_type']) && $_POST['delete_type'] === 'room') {
    $delete_id = (int)$_POST['delete_id'];
    $delete_query = "DELETE FROM rooms WHERE id = $delete_id";
    if (mysqli_query($con, $delete_query)) {
        echo "<script>alert('Room deleted successfully.'); window.location.href='manager.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error deleting room!'); window.location.href='manager.php';</script>";
        exit();
    }
}
?>

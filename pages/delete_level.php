<?php 
session_start();
include("connection.php");
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_type']) && $_POST['delete_type'] === 'level') {
    $delete_id = (int)$_POST['delete_id'];
    $delete_query = "DELETE FROM levels WHERE id = $delete_id";
    if (mysqli_query($con, $delete_query)) {
        echo "<script>alert('Level deleted successfully.'); window.location.href='manager.php#level';</script>";
        exit();
    } else {
        echo "<script>alert('Error deleting level.'); window.location.href='manager.php#level';</script>";
        exit();
    }
}
?>
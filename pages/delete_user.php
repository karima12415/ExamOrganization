<?php
header('Content-Type: application/json');
include("connection.php");
$id = $_POST['id'] ?? '';
if (empty($id)) {
    echo json_encode(['success' => false, 'message' => 'Missing user ID']);
    exit;
}
$stmt = $con->prepare("DELETE FROM admin2 WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Delete failed']);
}
$stmt->close();
$con->close();
?>
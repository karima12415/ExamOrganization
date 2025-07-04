<?php
$conn = new mysqli("localhost", "root", "", "exam-org");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if (isset($_GET['filiere_id'])) {
    $filiere_id = intval($_GET['filiere_id']);
    $stmt = $conn->prepare("SELECT id, name FROM specialties WHERE id_filiere = ?");
    $stmt->bind_param("i", $filiere_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $specialties = [];
    while ($row = $result->fetch_assoc()) {
        $specialties[] = $row;
    }
    echo json_encode($specialties);
}
?>

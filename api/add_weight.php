<?php
session_start();
require_once '../includes/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

$user_id = $_SESSION['user_id'];
$weight = $_POST['weight'] ?? '';

if (empty($weight)) {
    echo json_encode(['success' => false, 'message' => 'Peso no proporcionado']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("INSERT INTO user_weight_tracking (user_id, weight, date) VALUES (:user_id, :weight, CURDATE())");
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':weight', $weight);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Peso registrado correctamente', 'weight' => $weight, 'date' => date('Y-m-d')]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al registrar el peso']);
}

header("Location: index.php?page=home");

?>

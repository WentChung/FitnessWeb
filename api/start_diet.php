<?php
session_start();
require_once '../includes/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

$user_id = $_SESSION['user_id'];
$diet_id = $_POST['diet_id'] ?? '';

if (empty($diet_id)) {
    echo json_encode(['success' => false, 'message' => 'ID de dieta no proporcionado']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

// Verificar si la dieta ya está en la lista de pendientes
$stmt = $conn->prepare("SELECT * FROM user_pending_items WHERE user_id = :user_id AND item_id = :diet_id AND item_type = 'diet' AND completed = FALSE");
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':diet_id', $diet_id);
$stmt->execute();

if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'La dieta ya está en la lista de pendientes']);
    exit;
}

// Agregar la dieta a la lista de pendientes
$stmt = $conn->prepare("INSERT INTO user_pending_items (user_id, item_type, item_id, start_date) VALUES (:user_id, 'diet', :diet_id, CURDATE())");
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':diet_id', $diet_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Dieta agregada a pendientes']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al agregar la dieta a pendientes']);
}
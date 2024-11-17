<?php
session_start();
require_once '../includes/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

$user_id = $_SESSION['user_id'];
$routine_id = $_POST['routine_id'] ?? '';

if (empty($routine_id)) {
    echo json_encode(['success' => false, 'message' => 'ID de rutina no proporcionado']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

// Verificar si la rutina ya está en la lista de pendientes
$stmt = $conn->prepare("SELECT * FROM user_pending_items WHERE user_id = :user_id AND item_id = :routine_id AND item_type = 'routine' AND completed = FALSE");
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':routine_id', $routine_id);
$stmt->execute();

if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'La rutina ya está en la lista de pendientes']);
    exit;
}

// Agregar la rutina a la lista de pendientes
$stmt = $conn->prepare("INSERT INTO user_pending_items (user_id, item_type, item_id, start_date) VALUES (:user_id, 'routine', :routine_id, CURDATE())");
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':routine_id', $routine_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Rutina agregada a pendientes']);
    header("Location: index.php?page=login");
} else {
    echo json_encode(['success' => false, 'message' => 'Error al agregar la rutina a pendientes']);
}
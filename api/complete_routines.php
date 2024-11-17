<?php
session_start();
require_once '../includes/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

$user_id = $_SESSION['user_id'];
$routine_ids = $_POST['routines'] ?? [];

if (empty($routine_ids)) {
    echo json_encode(['success' => false, 'message' => 'No se seleccionaron rutinas para completar']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

// Iniciar transacción
$conn->beginTransaction();

try {
    $completed_ids = [];
    foreach ($routine_ids as $routine_id) {
        // Marcar la rutina como completada en user_pending_items
        $stmt = $conn->prepare("UPDATE user_pending_items SET completed = TRUE WHERE user_id = :user_id AND item_id = :routine_id AND item_type = 'routine'");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':routine_id', $routine_id);
        $stmt->execute();

        // Agregar la rutina a user_completed_routines
        $stmt = $conn->prepare("INSERT INTO user_completed_routines (user_id, routine_id, completion_date) VALUES (:user_id, :routine_id, CURDATE())");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':routine_id', $routine_id);
        $stmt->execute();

        $completed_ids[] = $routine_id;
    }

    // Confirmar la transacción
    $conn->commit();

    echo json_encode([
        'success' => true, 
        'message' => 'Rutinas marcadas como completadas',
        'completed_ids' => $completed_ids
    ]);
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error al marcar las rutinas como completadas: ' . $e->getMessage()]);
}
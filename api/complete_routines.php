<?php
session_start();
require_once '../includes/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

$user_id = $_SESSION['user_id'];
$routine_ids = $_POST['routine_ids'] ?? [];

if (empty($routine_ids)) {
    echo json_encode(['success' => false, 'message' => 'No se seleccionaron rutinas para completar']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

try {
    $conn->beginTransaction();

    $stmt = $conn->prepare("SELECT item_id FROM user_pending_items WHERE id = ? AND user_id = ? AND item_type = 'routine'");
    $delete_stmt = $conn->prepare("DELETE FROM user_pending_items WHERE id = ? AND user_id = ? AND item_type = 'routine'");
    $insert_stmt = $conn->prepare("INSERT INTO user_completed_routines (user_id, routine_id, completion_date) VALUES (?, ?, NOW())");

    foreach ($routine_ids as $pending_id) {
        $stmt->execute([$pending_id, $user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            $routine_id = $result['item_id'];
            $delete_stmt->execute([$pending_id, $user_id]);
            $insert_stmt->execute([$user_id, $routine_id]);
        }
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Rutinas completadas con éxito']);
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error al completar las rutinas: ' . $e->getMessage()]);
}
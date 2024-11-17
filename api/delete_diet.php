<?php
require_once '../includes/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isAdmin($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$data = json_decode(file_get_contents('php://input'), true);
$ids = $data['ids'] ?? [];

if (empty($ids)) {
    echo json_encode(['success' => false, 'message' => 'No se proporcionaron IDs para eliminar']);
    exit;
}

try {
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $conn->prepare("DELETE FROM diets WHERE id IN ($placeholders)");
    $stmt->execute($ids);

    $deletedCount = $stmt->rowCount();
    echo json_encode(['success' => true, 'message' => "Se eliminaron $deletedCount dieta(s) correctamente"]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al eliminar las dietas: ' . $e->getMessage()]);
}
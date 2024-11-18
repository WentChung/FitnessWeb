<?php
require_once '../includes/database.php';
require_once '../includes/functions.php';
redirectIfNotAdmin();

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
    $conn->beginTransaction();

    // Crear placeholders para la consulta IN
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    // Primero eliminar registros relacionados en diet_views
    $stmt = $conn->prepare("DELETE FROM diet_views WHERE diet_id IN ($placeholders)");
    $stmt->execute($ids);

    // Eliminar registros relacionados en user_pending_items
    $stmt = $conn->prepare("DELETE FROM user_pending_items WHERE item_id IN ($placeholders) AND item_type = 'diet'");
    $stmt->execute($ids);

    // Eliminar registros relacionados en user_complete_diets
    $stmt = $conn->prepare("DELETE FROM user_completed_diets WHERE diet_id IN ($placeholders)");
    $stmt->execute($ids);

    // Finalmente, eliminar las dietas
    $stmt = $conn->prepare("DELETE FROM diets WHERE id IN ($placeholders)");
    $stmt->execute($ids);

    $deletedCount = $stmt->rowCount();
    
    $conn->commit();
    echo json_encode(['success' => true, 'message' => "Se eliminaron $deletedCount dieta(s) correctamente"]);
} catch (PDOException $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error al eliminar las dietas: ' . $e->getMessage()]);
}
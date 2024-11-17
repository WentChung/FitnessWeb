<?php
session_start();
require_once '../includes/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

$user_id = $_SESSION['user_id'];
$diet_ids = $_POST['diets'] ?? [];

if (empty($diet_ids)) {
    echo json_encode(['success' => false, 'message' => 'No se seleccionaron dietas para completar']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

// Iniciar transacción
$conn->beginTransaction();

try {
    $completed_ids = [];
    $total_calories = 0;
    foreach ($diet_ids as $diet_id) {
        // Marcar la dieta como completada en user_pending_items
        $stmt = $conn->prepare("UPDATE user_pending_items SET completed = TRUE WHERE user_id = :user_id AND item_id = :diet_id AND item_type = 'diet'");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':diet_id', $diet_id);
        $stmt->execute();

        // Obtener las calorías de la dieta
        $stmt = $conn->prepare("SELECT calorie_target FROM diets WHERE id = :diet_id");
        $stmt->bindParam(':diet_id', $diet_id);
        $stmt->execute();
        $calories = $stmt->fetch(PDO::FETCH_ASSOC)['calorie_target'];

        // Agregar las calorías consumidas a user_calorie_intake
        $stmt = $conn->prepare("INSERT INTO user_calorie_intake (user_id, diet_id, calories, date) VALUES (:user_id, :diet_id, :calories, CURDATE())");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':diet_id', $diet_id);
        $stmt->bindParam(':calories', $calories);
        $stmt->execute();

        $completed_ids[] = $diet_id;
        $total_calories += $calories;
    }

    // Confirmar la transacción
    $conn->commit();

    echo json_encode([
        'success' => true, 
        'message' => 'Dietas marcadas como completadas',
        'completed_ids' => $completed_ids,
        'calories_added' => $total_calories
    ]);
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error al marcar las dietas como completadas: ' . $e->getMessage()]);
}
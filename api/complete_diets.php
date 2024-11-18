<?php
session_start();
require_once '../includes/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

$user_id = $_SESSION['user_id'];
$diet_ids = $_POST['diet_ids'] ?? [];

if (empty($diet_ids)) {
    echo json_encode(['success' => false, 'message' => 'No se seleccionaron dietas para completar']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

try {
    $conn->beginTransaction();

    $stmt = $conn->prepare("SELECT item_id FROM user_pending_items WHERE id = ? AND user_id = ? AND item_type = 'diet'");
    $delete_stmt = $conn->prepare("DELETE FROM user_pending_items WHERE id = ? AND user_id = ? AND item_type = 'diet'");
    $insert_stmt = $conn->prepare("INSERT INTO user_completed_diets (user_id, diet_id, completion_date) VALUES (?, ?, NOW())");
    $calorie_stmt = $conn->prepare("INSERT INTO user_calorie_intake (user_id, diet_id, calories, date) VALUES (?, ?, ?, NOW())");
    $get_calories_stmt = $conn->prepare("SELECT calorie_target FROM diets WHERE id = ?");

    $total_calories_added = 0;

    foreach ($diet_ids as $pending_id) {
        $stmt->execute([$pending_id, $user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            $diet_id = $result['item_id'];

            $delete_stmt->execute([$pending_id, $user_id]);
            $insert_stmt->execute([$user_id, $diet_id]);

            $get_calories_stmt->execute([$diet_id]);
            $calories = $get_calories_stmt->fetchColumn();

            $calorie_stmt->execute([$user_id, $diet_id, $calories]);
            $total_calories_added += $calories;
        }
    }

    $total_calories_stmt = $conn->prepare("SELECT SUM(calories) as total_calories FROM user_calorie_intake WHERE user_id = ? AND date >= DATE_SUB(NOW(), INTERVAL 1 DAY)");
    $total_calories_stmt->execute([$user_id]);
    $total_calories = $total_calories_stmt->fetchColumn();

    $conn->commit();
    echo json_encode([
        'success' => true, 
        'message' => 'Dietas completadas con éxito',
        'calories_added' => $total_calories_added,
        'total_calories' => $total_calories
    ]);
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error al completar las dietas: ' . $e->getMessage()]);
}
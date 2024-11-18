<?php
    require_once '../includes/database.php';
    require_once '../includes/functions.php';

    header('Content-Type: application/json');

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $type = $_GET['type'] ?? '';
    $id = $_GET['id'] ?? '';

    if (empty($type) || empty($id)) {
        echo json_encode(['success' => false, 'message' => 'Parámetros inválidos']);
        exit;
    }

    $db = new Database();
    $conn = $db->getConnection();

    // Verificar si ya existe en favoritos
    $stmt = $conn->prepare("SELECT * FROM user_favorites WHERE user_id = :user_id AND favorite_type = :type AND favorite_id = :id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':id', $id);   
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $stmt = $conn->prepare("DELETE FROM user_favorites WHERE user_id = :user_id AND favorite_type = :type AND favorite_id = :id");
        $action = 'removed';
    } else {
        // Si no existe, lo agregamos
        $stmt = $conn->prepare("INSERT INTO user_favorites (user_id, favorite_type, favorite_id) VALUES (:user_id, :type, :id)");
        $action = 'added';
    }

    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => "$action succesfully", 'action' => $action]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al procesar la solicitud']);
    }
    
?>
<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../includes/database.php';
require_once '../includes/functions.php';

session_start();

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(array("message" => "Unauthorized"));
    exit();
}

$db = new Database();
$conn = $db->getConnection();

$type = isset($_GET['type']) ? sanitizeInput($_GET['type']) : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (empty($type) || $id === 0) {
    http_response_code(400);
    echo json_encode(array("message" => "Missing required parameters"));
    exit();
}

$column = $type . '_id';

// Eliminar de favoritos
$stmt = $conn->prepare("DELETE FROM user_favorites WHERE user_id = :user_id AND $column = :item_id");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->bindParam(':item_id', $id);

if ($stmt->execute()) {
    echo json_encode(array("success" => true, "message" => "Removed from favorites successfully"));
} else {
    http_response_code(500);
    echo json_encode(array("message" => "Error removing from favorites"));
}
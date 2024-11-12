<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once './includes/database.php';
require_once './includes/functions.php';

session_start();

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(array("message" => "Unauthorized"));
    exit();
}

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT d.* FROM diets d 
                        JOIN user_favorites uf ON d.id = uf.diet_id 
                        WHERE uf.user_id = :user_id 
                        ORDER BY uf.id DESC LIMIT 5");
$stmt->bindParam(":user_id", $_SESSION['user_id']);
$stmt->execute();
$recent_diets = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($recent_diets);
?>
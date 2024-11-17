<?php
require_once './includes/database.php';
require_once './includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

$db = new Database();
$conn = $db->getConnection();

$name = $_POST['name'] ?? '';
$description = $_POST['description'] ?? '';
$objective = $_POST['objective'] ?? '';
$diet_type = $_POST['diet_type'] ?? '';
$calorie_target = $_POST['calorie_target'] ?? '';
$image_url = $_POST['image_url'] ?? '';

if (empty($name) || empty($description) || empty($objective) || empty($diet_type) || empty($calorie_target)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
    exit;
}

try {
    $stmt = $conn->prepare("INSERT INTO diets (name, description, objective, diet_type, calorie_target, image_url) VALUES (:name, :description, :objective, :diet_type, :calorie_target, :image_url)");
    
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':objective', $objective);
    $stmt->bindParam(':diet_type', $diet_type);
    $stmt->bindParam(':calorie_target', $calorie_target);
    $stmt->bindParam(':image_url', $image_url);
    
    $stmt->execute();
    
    $diet_id = $conn->lastInsertId();
    
    echo json_encode(['success' => true, 'message' => 'Dieta agregada correctamente', 'diet_id' => $diet_id]);
    header("Location: index.php?page=add_diet");
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al agregar la dieta: ' . $e->getMessage()]);
}

} else {
    header("Location: index.php");
    exit();
}

?>
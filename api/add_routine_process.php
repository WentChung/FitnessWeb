<?php
require_once './includes/database.php';
require_once './includes/functions.php';

header('Content-Type: application/json');
//validacion de admin

redirectIfNotAdmin();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

$db = new Database();
$conn = $db->getConnection();

$name = $_POST['name'];
$description = $_POST['description'];
$level = $_POST['level'];
$target_muscle = $_POST['target_muscle'];
$duration = $_POST['duration'];
$calories_burned = $_POST['calories_burned'];
$image_url = $_POST['image_url'];

if (empty($name) || empty($description) || empty($level) || empty($target_muscle) || empty($duration) || empty($calories_burned)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
    exit;
}

try {
    $stmt = $conn->prepare("INSERT INTO routines (name, description, level, target_muscle, duration, calories_burned, image_url) VALUES (:name, :description, :level, :target_muscle, :duration, :calories_burned, :image_url)");
    
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':level', $level);
    $stmt->bindParam(':target_muscle', $target_muscle);
    $stmt->bindParam(':duration', $duration);
    $stmt->bindParam(':calories_burned', $calories_burned);
    $stmt->bindParam(':image_url', $image_url);
    
    $stmt->execute();
    
    $routine_id = $conn->lastInsertId();
    
    echo json_encode(['success' => true, 'message' => 'Rutina agregada correctamente', 'routine_id' => $routine_id]);
    header("Location: index.php?page=add_routine");
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al agregar la rutina: ' . $e->getMessage()]);
}

} else {
    header("Location: index.php");
    exit();
}
?>
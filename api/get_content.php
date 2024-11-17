<?php
require_once '../includes/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$db = new Database();
$conn = $db->getConnection();

try {
    $stmt = $conn->query("SELECT id, name FROM diets");
    $diets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->query("SELECT id, name FROM routines");
    $routines = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'diets' => $diets,
        'routines' => $routines
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al obtener el contenido: ' . $e->getMessage()]);
}
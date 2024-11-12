<?php
require_once './includes/functions.php';
require_once './includes/database.php';

redirectIfNotAdmin();

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $description = sanitizeInput($_POST['description']);
    $level = sanitizeInput($_POST['level']);
    $target_muscle = sanitizeInput($_POST['target_muscle']);

    $stmt = $conn->prepare("INSERT INTO routines (name, description, level, target_muscle) VALUES (:name, :description, :level, :target_muscle)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':level', $level);
    $stmt->bindParam(':target_muscle', $target_muscle);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Rutina agregada exitosamente.";
        header("Location: index.php?page=rutinas");
        exit();
    } else {
        $error = "Error al agregar la rutina.";
    }
}
?>

<div class="container">
    <h2>Agregar Nueva Rutina</h2>
    <?php if (isset($error)): ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>
    <form action="index.php?page=add_routine" method="POST" class="form-add">
        <div class="form-group">
            <label for="name">Nombre de la Rutina:</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="description">Descripción:</label>
            <textarea id="description" name="description" required></textarea>
        </div>
        <div class="form-group">
            <label for="level">Nivel:</label>
            <select id="level" name="level" required>
                <option value="Principiante">Principiante</option>
                <option value="Intermedio">Intermedio</option>
                <option value="Avanzado">Avanzado</option>
            </select>
        </div>
        <div class="form-group">
            <label for="target_muscle">Músculo Objetivo:</label>
            <input type="text" id="target_muscle" name="target_muscle" required>
        </div>
        <button type="submit" class="btn btn-primary">Agregar Rutina</button>
    </form>
</div>
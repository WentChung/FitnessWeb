<?php
require_once './includes/functions.php';
require_once './includes/database.php';

redirectIfNotAdmin();

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $description = sanitizeInput($_POST['description']);
    $calorie_target = intval($_POST['calorie_target']);

    $stmt = $conn->prepare("INSERT INTO diets (name, description, calorie_target) VALUES (:name, :description, :calorie_target)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':calorie_target', $calorie_target);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Dieta agregada exitosamente.";
        header("Location: index.php?page=dietas");
        exit();
    } else {
        $error = "Error al agregar la dieta.";
    }
}
?>

<div class="container">
    <h2>Agregar Nueva Dieta</h2>
    <?php if (isset($error)): ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>
    <form action="index.php?page=add_dieta" method="POST" class="form-add">
        <div class="form-group">
            <label for="name">Nombre de la Dieta:</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="description">Descripción:</label>
            <textarea id="description" name="description" required></textarea>
        </div>
        <div class="form-group">
            <label for="calorie_target">Objetivo Calórico:</label>
            <input type="number" id="calorie_target" name="calorie_target" required>
        </div>
        <button type="submit" class="btn btn-primary">Agregar Dieta</button>
    </form>
</div>
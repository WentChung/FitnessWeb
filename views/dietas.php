<?php
require_once './includes/functions.php';
require_once './includes/database.php';

redirectIfNotLoggedIn();

$db = new Database();
$conn = $db->getConnection();

// Obtener filtros y ordenamiento
$objective = isset($_GET['objective']) ? sanitizeInput($_GET['objective']) : '';
$diet_type = isset($_GET['diet_type']) ? sanitizeInput($_GET['diet_type']) : '';
$sort = isset($_GET['sort']) ? sanitizeInput($_GET['sort']) : 'name';
$order = isset($_GET['order']) ? sanitizeInput($_GET['order']) : 'ASC';

// Construir la consulta SQL
$sql = "SELECT * FROM diets WHERE 1=1";
if (!empty($objective)) {
    $sql .= " AND objective = :objective";
}
if (!empty($diet_type)) {
    $sql .= " AND diet_type = :diet_type";
}
$sql .= " ORDER BY $sort $order";

$stmt = $conn->prepare($sql);
if (!empty($objective)) {
    $stmt->bindParam(':objective', $objective);
}
if (!empty($diet_type)) {
    $stmt->bindParam(':diet_type', $diet_type);
}
$stmt->execute();
$diets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container2">
    <h2>Planes de alimentación</h2>
    
    <form action="" method="GET" class="filters">
        <div class="form-group">
            <label for="objective">Objetivo:</label>
            <select name="objective" id="objective">
                <option value="">Todos</option>
                <option value="Ganar masa muscular" <?php echo $objective == 'Ganar masa muscular' ? 'selected' : ''; ?>>Ganar masa muscular</option>
                <option value="Perder peso" <?php echo $objective == 'Perder peso' ? 'selected' : ''; ?>>Perder peso</option>
                <option value="Mantenimiento" <?php echo $objective == 'Mantenimiento' ? 'selected' : ''; ?>>Mantenimiento</option>
            </select>
        </div>
        <div class="form-group">
            <label for="diet_type">Tipo de dieta:</label>
            <select name="diet_type" id="diet_type">
                <option value="">Todos</option>
                <option value="Normal" <?php echo $diet_type == 'Normal' ? 'selected' : ''; ?>>Normal</option>
                <option value="Vegana" <?php echo $diet_type == 'Vegana' ? 'selected' : ''; ?>>Vegana</option>
                <option value="Vegetariana" <?php echo $diet_type == 'Vegetariana' ? 'selected' : ''; ?>>Vegetariana</option>
                <option value="Sin gluten" <?php echo $diet_type == 'Sin gluten' ? 'selected' : ''; ?>>Sin gluten</option>
            </select>
        </div>
        <div class="form-group">
            <label for="sort">Ordenar por:</label>
            <select name="sort" id="sort">
                <option value="name" <?php echo $sort == 'name' ? 'selected' : ''; ?>>Nombre</option>
                <option value="calorie_target" <?php echo $sort == 'calorie_target' ? 'selected' : ''; ?>>Calorías</option>
            </select>
        </div>
        <div class="form-group">
            <label for="order">Orden:</label>
            <select name="order" id="order">
                <option value="ASC" <?php echo $order == 'ASC' ? 'selected' : ''; ?>>Ascendente</option>
                <option value="DESC" <?php echo $order == 'DESC' ? 'selected' : ''; ?>>Descendente</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filtrar</button>
    </form>

    <div class="diets-grid">
        <?php foreach ($diets as $diet): ?>
            <div class="diet-card">
                <img src="<?php echo htmlspecialchars($diet['image_url']); ?>" alt="<?php echo htmlspecialchars($diet['name']); ?>" class="diet-image">
                <h3><?php echo htmlspecialchars($diet['name']); ?></h3>
                <p>Objetivo: <?php echo htmlspecialchars($diet['objective']); ?></p>
                <p>Tipo: <?php echo htmlspecialchars($diet['diet_type']); ?></p>
                <p>Calorías: <?php echo htmlspecialchars($diet['calorie_target']); ?></p>
                <a href="diet_details?id=<?php echo $diet['id']; ?>" class="btn btn-secondary">Ver detalles</a>
                <button class="btn btn-favorite" data-id="<?php echo $diet['id']; ?>" data-type="diet">Agregar a favoritos</button>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const favoriteButtons = document.querySelectorAll('.btn-favorite');
    favoriteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const type = this.getAttribute('data-type');
            addToFavorites(type, id);
        });
    });
});

function addToFavorites(type, id) {
    fetch(`/api/add_favorite.php?type=${type}&id=${id}`, { method: 'POST' })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Agregado a favoritos exitosamente');
            } else {
                alert('Error al agregar a favoritos');
            }
        })
        .catch(error => console.error('Error:', error));
}
</script>
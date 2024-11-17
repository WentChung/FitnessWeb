<?php
require_once './includes/functions.php';
require_once './includes/database.php';

redirectIfNotLoggedIn();

$db = new Database();
$conn = $db->getConnection();

// Obtener filtros y ordenamiento
$level = isset($_GET['level']) ? sanitizeInput($_GET['level']) : '';
$target_muscle = isset($_GET['target_muscle']) ? sanitizeInput($_GET['target_muscle']) : '';
$sort = isset($_GET['sort']) ? sanitizeInput($_GET['sort']) : 'name';
$order = isset($_GET['order']) ? sanitizeInput($_GET['order']) : 'ASC';

// Construir la consulta SQL
$sql = "SELECT * FROM routines WHERE 1=1";
if (!empty($level)) {
    $sql .= " AND level = :level";
}
if (!empty($target_muscle)) {
    $sql .= " AND target_muscle = :target_muscle";
}
$sql .= " ORDER BY $sort $order";

$stmt = $conn->prepare($sql);
if (!empty($level)) {
    $stmt->bindParam(':level', $level);
}
if (!empty($target_muscle)) {
    $stmt->bindParam(':target_muscle', $target_muscle);
}
$stmt->execute();
$routines = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container2">
    <h2>Rutinas de entrenamiento</h2>

    <div class="search-container">
        <input type="text" id="searchRoutines" placeholder="Buscar rutinas..." class="search-input">
    </div>
    
    <form action="" method="GET" class="filters">
        <div class="form-group">
            <label for="level">Nivel:</label>
            <select name="level" id="level">
                <option value="">Todos</option>
                <option value="Principiante" <?php echo $level == 'Principiante' ? 'selected' : ''; ?>>Principiante</option>
                <option value="Intermedio" <?php echo $level == 'Intermedio' ? 'selected' : ''; ?>>Intermedio</option>
                <option value="Avanzado" <?php echo $level == 'Avanzado' ? 'selected' : ''; ?>>Avanzado</option>
            </select>
        </div>
        <div class="form-group">
            <label for="target_muscle">Músculo objetivo:</label>
            <input type="text" name="target_muscle" id="target_muscle" value="<?php echo htmlspecialchars($target_muscle); ?>">
        </div>
        <div class="form-group">
            <label for="sort">Ordenar por:</label>
            <select name="sort" id="sort">
                <option value="name" <?php echo $sort == 'name' ? 'selected' : ''; ?>>Nombre</option>
                <option value="level" <?php echo $sort == 'level' ? 'selected' : ''; ?>>Nivel</option>
                <option value="duration" <?php echo $sort == 'duration' ? 'selected' : ''; ?>>Duración</option>
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

    <div id="routinesGrid" class="routines-grid">
        <?php foreach ($routines as $routine): ?>
            <div class="routine-card" data-name="<?php echo htmlspecialchars(strtolower($routine['name'])); ?>">
                <img src="<?php echo htmlspecialchars($routine['image_url']); ?>" alt="<?php echo htmlspecialchars($routine['name']); ?>" class="routine-image">
                <h3><?php echo htmlspecialchars($routine['name']); ?></h3>
                <p>Nivel: <?php echo htmlspecialchars($routine['level']); ?></p>
                <p>Músculo objetivo: <?php echo htmlspecialchars($routine['target_muscle']); ?></p>
                <p>Duración: <?php echo htmlspecialchars($routine['duration']); ?> minutos</p>
                <p>Calorías quemadas: <?php echo htmlspecialchars($routine['calories_burned']); ?></p>
                <a href="routine_details?id=<?php echo $routine['id']; ?>" class="btn btn-secondary">Ver detalles</a>
                <button class="btn btn-favorite" data-id="<?php echo $routine['id']; ?>" data-type="routine">
                    <span class="favorite-icon">&#9733;</span>
                </button>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="js/notifications.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchRoutines');
    const routinesGrid = document.getElementById('routinesGrid');
    const routineCards = routinesGrid.querySelectorAll('.routine-card');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();

        routineCards.forEach(card => {
            const routineName = card.getAttribute('data-name');
            if (routineName.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });

    routinesGrid.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-favorite') || e.target.closest('.btn-favorite')) {
            const button = e.target.classList.contains('btn-favorite') ? e.target : e.target.closest('.btn-favorite');
            const routineId = button.getAttribute('data-id');
            addToFavorites('routine', routineId, button);
        }
    });

    function addToFavorites(type, id, button) {
        fetch(`api/add_favorite.php?type=${type}&id=${id}`, { method: 'POST' })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    updateFavoriteButton(button, data.action);
                } else {
                    showNotification('Error al procesar favorito', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al procesar favorito', 'error');
            });
    }

    function updateFavoriteButton(button, action) {
        if (action === 'added') {
            button.classList.add('active');
        } else {
            button.classList.remove('active');
        }
    }
});
</script>

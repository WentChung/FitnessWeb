<?php
require_once './includes/functions.php';
require_once './includes/database.php';

redirectIfNotLoggedIn();

$db = new Database();
$conn = $db->getConnection();

$routine_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($routine_id === 0) {
    header("Location: index.php?page=rutinas");
    exit();
}

// Obtener detalles de la rutina
$stmt = $conn->prepare("SELECT * FROM routines WHERE id = :id");
$stmt->bindParam(':id', $routine_id);
$stmt->execute();
$routine = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$routine) {
    header("Location: index.php?page=rutinas");
    exit();
}

// Registrar la vista de la rutina
$stmt = $conn->prepare("INSERT INTO routine_views (user_id, routine_id) VALUES (:user_id, :routine_id)");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->bindParam(':routine_id', $routine_id);
$stmt->execute();

// Verificar si la rutina está en favoritos
$stmt = $conn->prepare("SELECT id FROM user_favorites WHERE user_id = :user_id AND routine_id = :routine_id");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->bindParam(':routine_id', $routine_id);
$stmt->execute();
$is_favorite = $stmt->rowCount() > 0;
?>

<div class="container">
    <div class="routine-details">
        <h1><?php echo htmlspecialchars($routine['name']); ?></h1>
        <img src="<?php echo htmlspecialchars($routine['image_url']); ?>" alt="<?php echo htmlspecialchars($routine['name']); ?>" class="routine-image">
        <p><strong>Nivel:</strong> <?php echo htmlspecialchars($routine['level']); ?></p>
        <p><strong>Músculo objetivo:</strong> <?php echo htmlspecialchars($routine['target_muscle']); ?></p>
        <p><strong>Duración:</strong> <?php echo htmlspecialchars($routine['duration']); ?> minutos</p>
        <p><strong>Calorías quemadas:</strong> <?php echo htmlspecialchars($routine['calories_burned']); ?></p>
        <p><strong>Descripción:</strong> <?php echo nl2br(htmlspecialchars($routine['description'])); ?></p>
        
        <button id="favoriteBtn" class="btn <?php echo $is_favorite ? 'btn-secondary' : 'btn-primary'; ?>" data-id="<?php echo $routine_id; ?>" data-type="routine">
            <?php echo $is_favorite ? 'Quitar de favoritos' : 'Agregar a favoritos'; ?>
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const favoriteBtn = document.getElementById('favoriteBtn');
    favoriteBtn.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const type = this.getAttribute('data-type');
        const action = this.textContent.trim() === 'Agregar a favoritos' ? 'add' : 'remove';
        
        fetch(`/api/${action}_favorite.php?type=${type}&id=${id}`, { method: 'POST' })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (action === 'add') {
                        this.textContent = 'Quitar de favoritos';
                        this.classList.remove('btn-primary');
                        this.classList.add('btn-secondary');
                    } else {
                        this.textContent = 'Agregar a favoritos';
                        this.classList.remove('btn-secondary');
                        this.classList.add('btn-primary');
                    }
                } else {
                    alert('Error al actualizar favoritos');
                }
            })
            .catch(error => console.error('Error:', error));
    });
});
</script>
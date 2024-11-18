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

$stmt = $conn->prepare("SELECT id FROM user_favorites WHERE user_id = :user_id AND favorite_id = :routine_id AND favorite_type = 'routine'");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->bindParam(':routine_id', $routine_id);
$stmt->execute();
$is_favorite = $stmt->rowCount() > 0;

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM user_pending_items WHERE user_id = :user_id AND item_id = :routine_id AND item_type = 'routine' AND completed = FALSE");
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':routine_id', $routine_id);
$stmt->execute();
$is_pending = $stmt->fetch(PDO::FETCH_ASSOC);
?>



<div class="container">
    <div class="routine-details">
        <a href="index.php?page=rutinas" class="btn-back" aria-label="Volver a la lista de rutinas">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1><?php echo htmlspecialchars($routine['name']); ?></h1>
        <img src="<?php echo htmlspecialchars($routine['image_url']); ?>" alt="<?php echo htmlspecialchars($routine['name']); ?>" class="routine-image">
        <p><strong>Nivel:</strong> <?php echo htmlspecialchars($routine['level']); ?></p>
        <p><strong>Músculo objetivo:</strong> <?php echo htmlspecialchars($routine['target_muscle']); ?></p>
        <p><strong>Duración:</strong> <?php echo htmlspecialchars($routine['duration']); ?> minutos</p>
        <p><strong>Calorías quemadas:</strong> <?php echo htmlspecialchars($routine['calories_burned']); ?></p>
        <p><strong>Descripción:</strong> <?php echo nl2br(htmlspecialchars($routine['description'])); ?></p>

        <?php if (!$is_pending): ?>
            <button id="startRoutineBtn" class="btn btn-primary" data-id="<?php echo $routine['id']; ?>">Empezar Rutina</button>
        <?php else: ?>
            <p>Esta rutina ya está en tu lista de pendientes.</p>
        <?php endif; ?>
        
        <button id="favoriteBtn" class="btn btn-favorite <?php echo $is_favorite ? 'active' : ''; ?>" data-id="<?php echo $routine['id']; ?>" data-type="routine">
            <?php echo $is_favorite ? '<span class="favorite-icon">&#9733;</span> Quitar de favoritos' : '<span class="favorite-icon">&#9733;</span> Agregar a favoritos'; ?>
        </button>
    </div>
</div>

<script src="js/notifications.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    const startRoutineBtn = document.getElementById('startRoutineBtn');
    if (startRoutineBtn) {
        startRoutineBtn.addEventListener('click', function() {
            const routineId = this.getAttribute('data-id');
            
            fetch('api/start_routine.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `routine_id=${routineId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Rutina agregada a pendientes', 'success');
                    this.style.display = 'none';
                    const pendingMessage = document.createElement('p');
                    pendingMessage.textContent = 'Esta rutina ya está en tu lista de pendientes.';
                    this.parentNode.appendChild(pendingMessage);
                } else {
                    showNotification('Error al agregar la rutina a pendientes', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al agregar la rutina a pendientes', 'error');
            });
        });
    }

    const favoriteButton = document.querySelector('.btn-favorite'); 

    favoriteButton.addEventListener('click', function() {
        const routineId = this.dataset.id;
        addToFavorites('routine', routineId, this);
    });

    function addToFavorites(type, id, button) {
    const isFavorite = button.classList.contains('active'); 

    fetch(`api/add_favorite.php?type=${type}&id=${id}`, { 
        method: 'POST',
        headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `is_favorite=${isFavorite}` 
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
        if (data.action === 'added') {
            showNotification('Rutina agregada a favoritos', 'success');
            button.classList.add('active'); 
            button.innerHTML = '<span class="favorite-icon">&#9733;</span> Quitar de favoritos';
        } else if (data.action === 'removed') {
            showNotification('Rutina removida de favoritos', 'success');
            button.classList.remove('active'); 
            button.innerHTML = '<span class="favorite-icon">&#9733;</span> Agregar a favoritos';
        }
        } else {
        showNotification('Error al procesar favorito', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al procesar favorito', 'error');
    });
    }
});
</script>
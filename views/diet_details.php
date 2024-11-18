<?php
require_once './includes/functions.php';
require_once './includes/database.php';

redirectIfNotLoggedIn();

$db = new Database();
$conn = $db->getConnection();

$diet_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($diet_id === 0) {
    header("Location: index.php?page=dietas");
    exit();
}

// Obtener detalles de la dieta
$stmt = $conn->prepare("SELECT * FROM diets WHERE id = :id");
$stmt->bindParam(':id', $diet_id);
$stmt->execute();
$diet = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$diet) {
    header("Location: index.php?page=dietas");
    exit();
}

// Registrar la vista de la dieta
$stmt = $conn->prepare("INSERT INTO diet_views (user_id, diet_id) VALUES (:user_id, :diet_id)");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->bindParam(':diet_id', $diet_id);
$stmt->execute();


$stmt = $conn->prepare("SELECT id FROM user_favorites WHERE user_id = :user_id AND favorite_id = :diet_id AND favorite_type = 'diet'");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->bindParam(':diet_id', $diet_id);
$stmt->execute();
$is_favorite = $stmt->rowCount() > 0;


$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM user_pending_items WHERE user_id = :user_id AND item_id = :diet_id AND item_type = 'diet' AND completed = FALSE");
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':diet_id', $diet_id);
$stmt->execute();
$is_pending = $stmt->fetch(PDO::FETCH_ASSOC);

?>



<div class="container">
    <div class="diet-details">
        <a href="index.php?page=dietas" class="btn-back" aria-label="Volver a la lista de dietas">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1><?php echo htmlspecialchars($diet['name']); ?></h1>
        <img src="<?php echo htmlspecialchars($diet['image_url']); ?>" alt="<?php echo htmlspecialchars($diet['name']); ?>" class="diet-image">
        <p><strong>Objetivo:</strong> <?php echo htmlspecialchars($diet['objective']); ?></p>
        <p><strong>Tipo de dieta:</strong> <?php echo htmlspecialchars($diet['diet_type']); ?></p>
        <p><strong>Calorías objetivo:</strong> <?php echo htmlspecialchars($diet['calorie_target']); ?></p>
        <p><strong>Descripción:</strong> <?php echo nl2br(htmlspecialchars($diet['description'])); ?></p>
        
        <?php if (!$is_pending): ?>
            <button id="startDietBtn" class="btn btn-primary" data-id="<?php echo $diet['id']; ?>">Empezar Dieta</button>
        <?php else: ?>
            <p><br><b>Esta dieta ya está en tu lista de pendientes. <b></br></p>
        <?php endif; ?>

        
        <button id="favoriteBtn" class="btn btn-favorite <?php echo $is_favorite ? 'active' : ''; ?>" data-id="<?php echo $diet_id; ?>" data-type="diet">
            <?php echo $is_favorite ? '☆ Quitar de favoritos' : '☆ Agregar a favoritos'; ?>
        </button>
    </div>
</div>

<script src="js/notifications.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {


    const startDietBtn = document.getElementById('startDietBtn'); 
    if (startDietBtn) {
        startDietBtn.addEventListener('click', function() {
            const dietId = this.getAttribute('data-id');
            
            fetch('api/start_diet.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `diet_id=${dietId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Dieta agregada a pendientes', 'success');
                    this.style.display = 'none';
                    const pendingMessage = document.createElement('p');
                    pendingMessage.textContent = 'Esta dieta ya está en tu lista de pendientes.';
                    this.parentNode.appendChild(pendingMessage);
                } else {
                    showNotification('Error al agregar la dieta a pendientes', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al agregar la dieta a pendientes', 'error');
            });
        });
    }

const favoriteButton = document.querySelector('.btn-favorite'); 

favoriteButton.addEventListener('click', function() {
    const dietId = this.getAttribute('data-id');
    addToFavorites('diet', dietId, this); 
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
        button.textContent = '☆ Quitar de favoritos'; 
    } else {
        button.classList.remove('active');
        button.textContent = '☆ Agregar a favoritos'; 
    }
}
});


</script>
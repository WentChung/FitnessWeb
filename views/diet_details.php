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

// Verificar si la dieta está en favoritos
$stmt = $conn->prepare("SELECT id FROM user_favorites WHERE user_id = :user_id AND diet_id = :diet_id");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->bindParam(':diet_id', $diet_id);
$stmt->execute();
$is_favorite = $stmt->rowCount() > 0;
?>

<div class="container">
    <div class="diet-details">
        <h1><?php echo htmlspecialchars($diet['name']); ?></h1>
        <img src="<?php echo htmlspecialchars($diet['image_url']); ?>" alt="<?php echo htmlspecialchars($diet['name']); ?>" class="diet-image">
        <p><strong>Objetivo:</strong> <?php echo htmlspecialchars($diet['objective']); ?></p>
        <p><strong>Tipo de dieta:</strong> <?php echo htmlspecialchars($diet['diet_type']); ?></p>
        <p><strong>Calorías objetivo:</strong> <?php echo htmlspecialchars($diet['calorie_target']); ?></p>
        <p><strong>Descripción:</strong> <?php echo nl2br(htmlspecialchars($diet['description'])); ?></p>
        
        <button id="favoriteBtn" class="btn <?php echo $is_favorite ? 'btn-secondary' : 'btn-primary'; ?>" data-id="<?php echo $diet_id; ?>" data-type="diet">
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
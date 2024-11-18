<?php
require_once './includes/functions.php';
require_once './includes/database.php';

redirectIfNotLoggedIn();

$db = new Database();
$conn = $db->getConnection();

$user_id = $_SESSION['user_id'];


$stmt = $conn->prepare("
    SELECT r.* 
    FROM routines r
    JOIN user_favorites uf ON r.id = uf.favorite_id
    WHERE uf.user_id = :user_id AND uf.favorite_type = 'routine'
");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$favorite_routines = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("
    SELECT d.* 
    FROM diets d
    JOIN user_favorites uf ON d.id = uf.favorite_id
    WHERE uf.user_id = :user_id AND uf.favorite_type = 'diet'
");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$favorite_diets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container2">
    <h1>Mis Favoritos</h1>
    <br>
    <h2>Rutinas Favoritas</h2>
    <div class="routines-grid">
        <?php foreach ($favorite_routines as $routine): ?>
            <div class="routine-card">
                <img src="<?php echo htmlspecialchars($routine['image_url']); ?>" alt="<?php echo htmlspecialchars($routine['name']); ?>" class="routine-image">
                <h3><?php echo htmlspecialchars($routine['name']); ?></h3>
                <p>Nivel: <?php echo htmlspecialchars($routine['level']); ?></p>
                <p>Músculo objetivo: <?php echo htmlspecialchars($routine['target_muscle']); ?></p>
                <a href="index.php?page=routine_details&id=<?php echo $routine['id']; ?>" class="btn btn-secondary">Ver detalles</a>
                <button class="btn btn-favorite active" data-id="<?php echo $routine['id']; ?>" data-type="routine">
                    <span class="favorite-icon">&#9733;</span>
                </button>
            </div>
        <?php endforeach; ?>
    </div>
    <br>
    <h2>Dietas Favoritas</h2>
    <div class="diets-grid">
        <?php foreach ($favorite_diets as $diet): ?>
            <div class="diet-card">
                <img src="<?php echo htmlspecialchars($diet['image_url']); ?>" alt="<?php echo htmlspecialchars($diet['name']); ?>" class="diet-image">
                <h3><?php echo htmlspecialchars($diet['name']); ?></h3>
                <p>Objetivo: <?php echo htmlspecialchars($diet['objective']); ?></p>
                <p>Tipo: <?php echo htmlspecialchars($diet['diet_type']); ?></p>
                <a href="index.php?page=diet_details&id=<?php echo $diet['id']; ?>" class="btn btn-secondary">Ver detalles</a>
                <button class="btn btn-favorite active" data-id="<?php echo $diet['id']; ?>" data-type="diet">
                    <span class="favorite-icon">&#9733;</span>
                </button>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="js/notifications.js"></script>
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
    fetch(`api/add_favorite.php?type=${type}&id=${id}`, { method: 'POST' })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                if (data.action === 'removed') {
                }
                setTimeout(function() {
                window.location.reload();
                }, 1000);
            } else {
                showNotification('Error al agregar a favoritos', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al agregar a favoritos', 'error');
        });
}
</script>
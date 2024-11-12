<?php
require_once './includes/functions.php';
require_once './includes/database.php';

redirectIfNotLoggedIn();

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->bindParam(":user_id", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="container2">
    <h1>Perfil de Usuario</h1>
    <div class="profile-info">
        <div class="profile-picture">
            <img src="https://images.imagenmia.com/model_version/bbfea91410ef7994cfefde4a33e032f3aebf7b90dda683f7fa32ea2685d2e7bb/1723819204347-output.jpg" alt="Foto de perfil">
        </div>
        <p><strong>Nombre de usuario:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        <p><strong>Correo electrónico:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Fecha de registro:</strong> <?php echo htmlspecialchars($user['created_at']); ?></p>
    </div>
    <h3>Actualizar información</h3>
    <form action="update_profile" method="POST" enctype="multipart/form-data" class="form-profile">
        <div class="form-group2">
            <label for="email">Nuevo correo electrónico:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
        </div>
        <div class="form-group2">
            <label for="password">Nueva contraseña:</label>
            <input type="password" id="password" name="password">
        </div>
        <div class="form-group2 ">
            <label for="confirm_password">Confirmar nueva contraseña:</label>
            <input type="password" id="confirm_password" name="confirm_password">
        </div>
        <button type="submit" class="btn btn-primary">Actualizar perfil</button>
    </form>
    <h3>Mis Rutinas Favoritas</h3>
    <div id="favorite-routines">
        <!-- Aquí se cargarán las rutinas favoritas con JavaScript -->
    </div>
    <h3>Mis Dietas Favoritas</h3>
    <div id="favorite-diets">
        <!-- Aquí se cargarán las dietas favoritas con JavaScript -->
    </div>
    <button class="btn btn-tertiary">
        <a href="logout">Cerrar sesión</a>
    </button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    fetchFavoriteRoutines();
    fetchFavoriteDiets();
});

function fetchFavoriteRoutines() {
    fetch('../api/favorite_routines.php')
        .then(response => response.json())
        .then(data => {
            const favoriteRoutinesContainer = document.getElementById('favorite-routines');
            favoriteRoutinesContainer.innerHTML = '';
            data.forEach(routine => {
                const routineElement = document.createElement('div');
                routineElement.classList.add('routine-card');
                routineElement.innerHTML = `
                    <h3>${routine.name}</h3>
                    <p>Nivel: ${routine.level}</p>
                    <a href="routine_details.php?id=${routine.id}" class="btn btn-secondary">Ver detalles</a>
                `;
                favoriteRoutinesContainer.appendChild(routineElement);
            });
        })
        .catch(error => console.error('Error:', error));
}

function fetchFavoriteDiets() {
    fetch('../api/favorite_diets.php')
        .then(response => response.json())
        .then(data => {
            const favoriteDietsContainer = document.getElementById('favorite-diets');
            favoriteDietsContainer.innerHTML = '';
            data.forEach(diet => {
                const dietElement = document.createElement('div');
                dietElement.classList.add('diet-card');
                dietElement.innerHTML = `
                    <h3>${diet.name}</h3>
                    <p>Calorías: ${diet.calorie_target}</p>
                    <a href="diet_details.php?id=${diet.id}" class="btn btn-secondary">Ver detalles</a>
                `;
                favoriteDietsContainer.appendChild(dietElement);
            });
        })
        .catch(error => console.error('Error:', error));
}
</script>
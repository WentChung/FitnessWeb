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
        <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Foto de perfil">
        </div>
        <p><strong>Nombre de usuario:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        <p><strong>Correo electrónico:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Fecha de registro:</strong> <?php echo htmlspecialchars($user['created_at']); ?></p>
    </div>
    <br />
    <h2>Actualizar información</h2>
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
        <div class="form-group">
            <label for="profile_picture_url">URL de la foto de perfil:</label>
            <input type="text" id="profile_picture_url" name="profile_picture_url">
        </div>
        <button type="submit" class="btn btn-secondary">Actualizar perfil</button>
    </form>
    <br />
    <h2>Mis Favoritos</h2>
    <div class="profile-favorites">
        <button class="btn btn-primary">
            <a href="favorites">Ver Mis Favoritos</a>
        </button>
    </div>
    <h3>Cerrar Sesión</h3>
    <button class="btn btn-tertiary">
        <a href="logout">Cerrar sesión</a>
    </button>
</div>

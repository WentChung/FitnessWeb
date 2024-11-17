<?php 
require_once './includes/database.php'; 

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;


if ($user_id) {
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("SELECT profile_picture FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $profile_picture_url = $stmt->fetchColumn();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitnessApp</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="../js/notifications.js"></script>
    <script src="/js/script.js"></script> 
</head>
<body>
    <header class="site-header">
        <div class="container2">
            <nav class="main-nav">
                <a href="home" class="logo">
                    <img src="./src/images/FitLife_Logo.png" alt="FitnessApp Logo"> 
                </a> 
                <ul class="nav-links">
                    <li><a href="home">Inicio</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="rutinas">Rutinas</a></li> 
                        <li><a href="dietas">Dietas</a></li> 
                        <li>
                            <a href="perfil"> 
                            <img src="<?php echo htmlspecialchars($profile_picture_url); ?>" alt="Foto de perfil" class="profile-picture-header"> 
                            </a>
                        </li> 
                    <?php else: ?>
                        <li><a href="login">Iniciar sesión</a></li> 
                        <li><a href="registro">Registrarse</a></li> 
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="site-content">
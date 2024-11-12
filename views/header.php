<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitnessApp</title>
    <link rel="stylesheet" href="css/style.css"> 
</head>
<body>
    <header class="site-header">
        <div class="container2">
            <nav class="main-nav">
                <a href="home" class="logo">FitnessApp</a> 
                <ul class="nav-links">
                    <li><a href="home">Inicio</a></li> 
                    <li><a href="rutinas">Rutinas</a></li> 
                    <li><a href="dietas">Dietas</a></li> 
                    <?php if (isLoggedIn()): ?>
                        <li>
                            <a href="perfil"> 
                                <img src="https://images.imagenmia.com/model_version/bbfea91410ef7994cfefde4a33e032f3aebf7b90dda683f7fa32ea2685d2e7bb/1723819204347-output.jpg" alt="Foto de Perfil" class="profile-picture-header"> 
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
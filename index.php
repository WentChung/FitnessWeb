<?php
require_once 'includes/functions.php';
require_once 'includes/database.php';

$db = new Database();
$conn = $db->getConnection();

include 'views/header.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'home'; 

if (isLoggedIn()) {
    switch ($page) {
        case 'home':
            include 'views/home.php';
            break;
        case 'rutinas':
            include 'views/rutinas.php';
            break;
        case 'dietas':
            include 'views/dietas.php';
            break;
        case 'perfil':
            include 'views/perfil.php';
            break;
        case 'update_profile':
            include 'views/update_profile.php';
            break;
        case 'logout':
            include 'views/logout.php';
        case 'routine_details':
            include 'views/routine_details.php';
            break;
        case 'diet_details':
            include 'views/diet_details.php';
        case 'add_routine':
            include 'views/add_routine.php';
            break;
        case 'add_dieta':
            include 'views/add_dieta.php';
        case 'add_favorite':
            include 'api/add_favorite.php';
        default:
            include 'views/home.php';
        
    }
} else {
    switch ($page) {
        case 'login':
            include 'views/login.php';
            break;
        case 'login_process': 
            include 'views/login_process.php';
            break;
        case 'registro':
            include 'views/registro.php';
            break;
        case 'registro_process':
            include 'views/registro_process.php';
            break;
        default:
            include 'views/login.php'; 
    }
}

include 'views/footer.php';
?>
<?php
require_once './includes/functions.php';
require_once './includes/database.php';

redirectIfNotLoggedIn();

$db = new Database();
$conn = $db->getConnection();

// Obtener rutinas recientes
$stmt = $conn->prepare("SELECT r.* FROM routines r 
                        JOIN routine_views rv ON r.id = rv.routine_id 
                        WHERE rv.user_id = :user_id 
                        ORDER BY rv.viewed_at DESC LIMIT 5");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$recent_routines = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener dietas recientes
$stmt = $conn->prepare("SELECT d.* FROM diets d 
                        JOIN diet_views dv ON d.id = dv.diet_id 
                        WHERE dv.user_id = :user_id 
                        ORDER BY dv.viewed_at DESC LIMIT 5");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$recent_diets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container2">
    <h1>Bienvenido a FitnessApp</h1>
    
    <section class="recent-section">
        <h2>Rutinas recientes</h2>
        <div class="recent-grid">
            <?php foreach ($recent_routines as $routine): ?>
                <div class="recent-card">
                    <img src="<?php echo htmlspecialchars($routine['image_url']); ?>" alt="<?php echo htmlspecialchars($routine['name']); ?>" class="recent-image">
                    <h3><?php echo htmlspecialchars($routine['name']); ?></h3>
                    <p>Nivel: <?php echo htmlspecialchars($routine['level']); ?></p>
                    <a href="routine_details.php?id=<?php echo $routine['id']; ?>" class="btn btn-secondary">Ver detalles</a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="recent-section">
        <h2>Dietas recientes</h2>
        <div class="recent-grid">
            <?php foreach ($recent_diets as $diet): ?>
                <div class="recent-card">
                    <img src="<?php echo htmlspecialchars($diet['image_url']); ?>" alt="<?php echo htmlspecialchars($diet['name']); ?>" class="recent-image">
                    <h3><?php echo htmlspecialchars($diet['name']); ?></h3>
                    <p>Objetivo: <?php echo htmlspecialchars($diet['objective']); ?></p>
                    <a href="diet_details.php?id=<?php echo $diet['id']; ?>" class="btn btn-secondary">Ver detalles</a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>
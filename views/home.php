<?php
require_once './includes/functions.php';
require_once './includes/database.php';

redirectIfNotLoggedIn();


$db = new Database();
$conn = $db->getConnection();

if (isset($_SESSION['notification'])) {
    $notification = $_SESSION['notification'];
    echo "<script>showNotification2('{$notification['message']}', '{$notification['type']}');</script>";
    unset($_SESSION['notification']);
}

// Obtener información del usuario
$stmt = $conn->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener rutinas recientes
$stmt = $conn->prepare("SELECT r.* FROM routines r 
                        JOIN routine_views rv ON r.id = rv.routine_id 
                        WHERE rv.user_id = :user_id
                        GROUP BY r.id 
                        ORDER BY rv.viewed_at DESC LIMIT 5");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$recent_routines = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener dietas recientes
$stmt = $conn->prepare("SELECT d.* FROM diets d 
                        JOIN diet_views dv ON d.id = dv.diet_id 
                        WHERE dv.user_id = :user_id
                        GROUP BY d.id  
                        ORDER BY dv.viewed_at DESC LIMIT 5");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$recent_diets = $stmt->fetchAll(PDO::FETCH_ASSOC);

$user_id = $_SESSION['user_id'];

// Obtener el último peso registrado
$stmt = $conn->prepare("SELECT weight, date FROM user_weight_tracking WHERE user_id = :user_id ORDER BY date DESC LIMIT 1");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$last_weight = $stmt->fetch(PDO::FETCH_ASSOC);


$stmt = $conn->prepare("SELECT weight, DATE(date) AS date FROM user_weight_tracking WHERE user_id = :user_id ORDER BY date DESC, id DESC LIMIT 7");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$weight_history = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT SUM(calories) as total_calories FROM user_calorie_intake WHERE user_id = :user_id AND date >= DATE_SUB(CURDATE(), INTERVAL 1 DAY)");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$calories_consumed = $stmt->fetch(PDO::FETCH_ASSOC)['total_calories'] ?? 0;

$stmt = $conn->prepare("SELECT COUNT(*) as completed_routines FROM user_completed_routines WHERE user_id = :user_id AND completion_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$completed_routines = $stmt->fetch(PDO::FETCH_ASSOC)['completed_routines'];

$stmt = $conn->prepare("SELECT upi.id, r.name FROM user_pending_items upi 
                        JOIN routines r ON upi.item_id = r.id 
                        WHERE upi.user_id = ? AND upi.item_type = 'routine'");
$stmt->execute([$user_id]);
$pending_routines = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener dietas pendientes
$stmt = $conn->prepare("SELECT upi.id, d.name FROM user_pending_items upi 
                        JOIN diets d ON upi.item_id = d.id 
                        WHERE upi.user_id = ? AND upi.item_type = 'diet'");
$stmt->execute([$user_id]);
$pending_diets = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container2">
    <h1>Bienvenido, <?php echo htmlspecialchars($user['username']); ?>!</h1>
    
    <div class="dashboard">
    <div class="dashboard-section">
            <h2>Resumen</h2>
            <p><?php 
                if ($last_weight) {
                    echo "Último peso registrado: " . $last_weight['weight'] . ' kg ';
                } else {
                    echo "No registrado";
                }?>
            </p>
            <p>Calorías consumidas en las últimas 24h: <?php echo $calories_consumed; ?> kcal</p>
            <p>Rutinas completadas en los ultimos 7 días: <?php echo $completed_routines; ?></p>
            
            <h3>Registrar nuevo peso</h3>
            <form id="weightForm" action="api/add_weight.php" method="POST">
                <label for="weight">Kg </label>
                <input type="number" id="weight" name="weight" step="0.1" class="weight-input" required>
                <button type="submit" class="btn btn-primary">Registrar</button>
            </form>
        </div>

        <div class="dashboard-section">
            <h2>Seguimiento de Peso</h2>
            <canvas id="weightChart" width="400" height="200"></canvas>
        </div>

        <div class="dashboard-section">
            <h2>Rutinas Pendientes</h2>
            <?php if (empty($pending_routines)): ?>
                <p>No tienes rutinas pendientes.</p>
            <?php else: ?>
                <form id="complete-routines-form">
                    <ul>
                    <?php foreach ($pending_routines as $routine): ?>
                        <div>
                            <input type="checkbox" name="routine_ids[]" value="<?php echo $routine['id']; ?>" id="routine-<?php echo $routine['id']; ?>">
                            <label for="routine-<?php echo $routine['id']; ?>"><?php echo htmlspecialchars($routine['name']); ?></label>
                        </div>
                    <?php endforeach; ?>
                    </ul>
                    <button type="submit" class="btn btn-primary">Completar Rutinas</button>
                </form>
            <?php endif; ?>
        </div>

        <div class="dashboard-section">
            <h2>Dietas Pendientes</h2>
            <?php if (empty($pending_diets)): ?>
                <p>No tienes dietas pendientes.</p>
            <?php else: ?>
                <form id="complete-diets-form">
                    <ul>
                    <?php foreach ($pending_diets as $diet): ?>
                        <div>
                            <input type="checkbox" name="diet_ids[]" value="<?php echo $diet['id']; ?>" id="diet-<?php echo $diet['id']; ?>">
                            <label for="diet-<?php echo $diet['id']; ?>"><?php echo htmlspecialchars($diet['name']); ?></label>
                        </div>
                    <?php endforeach; ?>
                    </ul>
                    <button type="submit" class="btn btn-primary">Completar Dietas</button>
                </form>
            <?php endif; ?>
        </div>
        
        <div class="dashboard-section">
        <?php
            if (isAdmin()) { 
                echo '<div class="dashboard-section">';
                echo '<h2>Funciones de Administrador</h2>';
                echo '<div class="admin-options">';
                echo '<a href="add_routine" class="btn btn-admin">☆ Añadir nueva rutina</a>';
                echo '<a href="add_diet" class="btn btn-admin">☆ Añadir nueva dieta</a>';
                echo '<a href="manage_content" class="btn btn-admin">☆ Gestionar Rutinas y Dietas</a>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        
        </div>
    </div>
    <br />
    <section class="recent-section">
        <h2>Rutinas recientes</h2>
        <div class="recent-grid">
            <?php foreach ($recent_routines as $routine): ?>
                <div class="recent-card">
                    <img src="<?php echo htmlspecialchars($routine['image_url']); ?>" alt="<?php echo htmlspecialchars($routine['name']); ?>" class="recent-image">
                    <h3><?php echo htmlspecialchars($routine['name']); ?></h3>
                    <p>Nivel: <?php echo htmlspecialchars($routine['level']); ?></p>
                    <a href="index.php?page=routine_details&id=<?php echo $routine['id']; ?>" class="btn btn-secondary">Ver detalles</a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    
    <br />
    <section class="recent-section">
        <h2>Dietas recientes</h2>
        <div class="recent-grid">
            <?php foreach ($recent_diets as $diet): ?>
                <div class="recent-card">
                    <img src="<?php echo htmlspecialchars($diet['image_url']); ?>" alt="<?php echo htmlspecialchars($diet['name']); ?>" class="recent-image">
                    <h3><?php echo htmlspecialchars($diet['name']); ?></h3>
                    <p>Objetivo: <?php echo htmlspecialchars($diet['objective']); ?></p>
                    <a href="index.php?page=diet_details&id=<?php echo $diet['id']; ?>" class="btn btn-secondary">Ver detalles</a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<script src="js/notifications.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const weightForm = document.getElementById('weightForm');
    const completeRoutinesForm = document.getElementById('complete-routines-form');
    const completeDietsForm = document.getElementById('complete-diets-form');

    weightForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('api/add_weight.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Peso registrado correctamente', 'success');
                const lastWeightElement = document.querySelector('.dashboard-section p:first-of-type');
                lastWeightElement.textContent = `Último peso registrado: ${data.weight} kg (${data.date})`;
                weightChart.data.labels.push(data.date);
                weightChart.data.datasets[0].data.push(data.weight);
                weightChart.update();
            } else {
                showNotification('Error al registrar el peso', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al registrar el peso', 'error');
            window.location.reload();
        });
    });

    const weightData = {
    labels: <?php echo json_encode(array_column($weight_history, 'date')); ?>,
    datasets: [{
        label: 'Peso (kg)',
        data: <?php echo json_encode(array_column($weight_history, 'weight')); ?>,
        }]
    };
    const weightChartConfig = {
    type: 'line',
    data: weightData,
    };
    const weightChart = new Chart(document.getElementById('weightChart'), weightChartConfig);

    if (completeRoutinesForm) {
        completeRoutinesForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('api/complete_routines.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');

                    // Eliminar las rutinas completadas de la lista
                    formData.getAll('routine_ids[]').forEach(id => {
                        const element = document.getElementById(`routine-${id}`);
                        if (element) element.parentNode.remove();
                    });

                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al procesar la solicitud', 'error');
            });
        });
    }

    if (completeDietsForm) {
        completeDietsForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('api/complete_diets.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');

                    // Eliminar las dietas completadas de la lista
                    formData.getAll('diet_ids[]').forEach(id => {
                        const element = document.getElementById(`diet-${id}`);
                        if (element) element.parentNode.remove();
                    });
                    
                    // Actualizar las calorías consumidas en las últimas 24h
                    const caloriesElement = document.querySelector('.dashboard-section p:nth-of-type(2)');
                    caloriesElement.textContent = `Calorías consumidas en las últimas 24h: ${data.total_calories} kcal`;
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al procesar la solicitud', 'error');
            });
        });
    }

});
</script>
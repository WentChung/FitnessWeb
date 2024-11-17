<?php
require_once 'includes/functions.php';
redirectIfNotAdmin()
?>

<body>

    <div class="container">
        <a href="index.php?page=home" class="btn-back" aria-label="Volver a la lista de dietas">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1>Agregar Nueva Dieta</h1>
        <form action="add_diet_process" method="POST">
            <div class="form-group">
                <label for="name">Nombre de la Dieta:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="options">
                <label for="description">Descripción:</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <div class="form-group">
                <label for="objective">Objetivo:</label>
                <input type="text" id="objective" name="objective" required>
            </div>
            <div class="options">
                <label for="diet_type">Tipo de Dieta:</label>
                <select id="diet_type" name="diet_type" required>
                    <option value="">Seleccione un tipo</option>
                    <option value="Normal">Normal</option>
                    <option value="Vegetariana">Vegetariana</option>
                    <option value="Vegana">Vegana</option>
                    <option value="Sin gluten">Sin gluten</option>
                </select>
            </div>
            <div class="form-group">
                <label for="calorie_target">Objetivo de Calorías:</label>
                <input type="number" id="calorie_target" name="calorie_target" required min="0">
            </div>
            <div class="form-group">
                <label for="image_url">URL de la Imagen:</label>
                <input type="url" id="image_url" name="image_url" required>
            </div>
            <button type="submit" class="btn btn-primary">Agregar Dieta</button>
        </form>
    </div>

    <script src="js/notifications.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('addDietForm');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('api/add_diet_process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    form.reset();
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al agregar la dieta', 'error');
            });
        });
    });
    </script>

</body>
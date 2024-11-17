<?php
require_once 'includes/functions.php';
redirectIfNotAdmin()
?>


<body>

    <div class="container3">
        <h1>Gestionar Contenido</h1>
        
        <div class="content-section">
            <h2>Dietas</h2>
            <ul id="dietList" class="content-list">
            </ul>
            <button id="deleteDietsBtn" class="btn btn-tertiary">Eliminar Dietas Seleccionadas</button>
        </div>

        <div class="content-section">
            <h2>Rutinas</h2>
            <ul id="routineList" class="content-list">
            </ul>
            <button id="deleteRoutinesBtn" class="btn btn-tertiary">Eliminar Rutinas Seleccionadas</button>
        </div>
    </div>

    <script src="js/notifications.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const dietList = document.getElementById('dietList');
        const routineList = document.getElementById('routineList');
        const deleteDietsBtn = document.getElementById('deleteDietsBtn');
        const deleteRoutinesBtn = document.getElementById('deleteRoutinesBtn');

        function loadContent() {
            fetch('api/get_content.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        dietList.innerHTML = data.diets.map(diet => `
                            <li>
                                <input type="checkbox" id="diet_${diet.id}" value="${diet.id}">
                                <label for="diet_${diet.id}">${diet.name}</label>
                            </li>
                        `).join('');

                        routineList.innerHTML = data.routines.map(routine => `
                            <li>
                                <input type="checkbox" id="routine_${routine.id}" value="${routine.id}">
                                <label for="routine_${routine.id}">${routine.name}</label>
                            </li>
                        `).join('');
                    } else {
                        showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error al cargar el contenido', 'error');
                });
        }

        function deleteContent(type) {
            const selectedItems = Array.from(document.querySelectorAll(`#${type}List input:checked`)).map(input => input.value);
            
            if (selectedItems.length === 0) {
                showNotification(`No se han seleccionado ${type}s para eliminar`, 'error');
                return;
            }

            fetch(`api/delete_${type}.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ ids: selectedItems })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    loadContent();
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification(`Error al eliminar ${type}s`, 'error');
            });
        }

        deleteDietsBtn.addEventListener('click', () => deleteContent('diet'));
        deleteRoutinesBtn.addEventListener('click', () => deleteContent('routine'));

        loadContent();
    });
    </script>

</body>

function fetchRoutines() {
    fetch('/api/rutinas.php')
        .then(response => response.json())
        .then(data => {
            const routinesContainer = document.getElementById('routines-container');
            routinesContainer.innerHTML = '';
            data.forEach(routine => {
                const routineElement = document.createElement('div');
                routineElement.classList.add('routine-card');
                routineElement.innerHTML = `
                    <h3>${routine.name}</h3>
                    <p>Nivel: ${routine.level}</p>
                    <p>Músculo objetivo: ${routine.target_muscle}</p>
                    <a href="routine_details.php?id=${routine.id}" class="btn btn-secondary">Ver detalles</a>
                `;
                routinesContainer.appendChild(routineElement);
            });
        })
        .catch(error => console.error('Error:', error));
}

document.addEventListener('DOMContentLoaded', fetchRoutines);

function fetchRecentRoutines() {
    fetch('/api/recent_routines.php')
        .then(response => response.json())
        .then(data => {
            const recentRoutinesContainer = document.getElementById('recent-routines');
            recentRoutinesContainer.innerHTML = '';
            data.forEach(routine => {
                const routineElement = document.createElement('div');
                routineElement.classList.add('routine-card');
                routineElement.innerHTML = `
                    <h3>${routine.name}</h3>
                    <p>Nivel: ${routine.level}</p>
                    <a href="routine_details.php?id=${routine.id}" class="btn btn-secondary">Ver detalles</a>
                `;
                recentRoutinesContainer.appendChild(routineElement);
            });
        })
        .catch(error => console.error('Error:', error));
}

function fetchRecentDiets() {
    fetch('/api/recent_diets.php')
        .then(response => response.json())
        .then(data => {
            const recentDietsContainer = document.getElementById('recent-diets');
            recentDietsContainer.innerHTML = '';
            data.forEach(diet => {
                const dietElement = document.createElement('div');
                dietElement.classList.add('diet-card');
                dietElement.innerHTML = `
                    <h3>${diet.name}</h3>
                    <p>Calorías: ${diet.calorie_target}</p>
                    <a href="diet_details.php?id=${diet.id}" class="btn btn-secondary">Ver detalles</a>
                `;
                recentDietsContainer.appendChild(dietElement);
            });
        })
        .catch(error => console.error('Error:', error));
}

function fetchFavoriteRoutines() {
    fetch('/api/favorite_routines.php')
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
    fetch('/api/favorite_diets.php')
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
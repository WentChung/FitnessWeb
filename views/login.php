
<div class="container">
    <h2>Iniciar sesión</h2>
    <form   action="login_process" method="POST">
        <div class="form-group">
            <label for="username">Usuario:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Iniciar sesión</button>
    </form>
    <p>¿No tienes una cuenta? <a href="registro">Regístrate aquí</a></p>
</div>

<script src="js/notifications.js"></script>
<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault(); 

    const formData = new FormData(this);

    fetch(this.action, 
 { 
        method: this.method, 
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 
            'success');
            setTimeout(function() {
            }, 4000);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => { 
        console.error('Error:', error);
        showNotification('Error al iniciar sesión', 'error'); 
    });
});
</script>
<?php
require_once './includes/functions.php';
require_once './includes/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validación básica
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
        header("Location: index.php?page=registro");
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Las contraseñas no coinciden.";
        header("Location: index.php?page=registro");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "El correo electrónico no es válido.";
        header("Location: index.php?page=registro");
        exit();
    }

    $db = new Database();
    $conn = $db->getConnection();

    // Verificar si el usuario ya existe
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":email", $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "El nombre de usuario o correo electrónico ya está en uso.";
        header("Location: index.php?page=registro");
        exit();
    }

    // Hashear la contraseña
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insertar el nuevo usuario
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":password", $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Registro exitoso. Por favor, inicia sesión.";
        header("Location: index.php?page=login");
    } else {
        $_SESSION['error'] = "Ocurrió un error durante el registro. Por favor, intenta de nuevo.";
        header("Location: index.php?page=registro");
    }
    exit();
} else {
    header("Location: index.php");
    exit();
}
?>
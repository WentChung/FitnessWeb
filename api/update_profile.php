<?php
require_once './includes/functions.php';
require_once './includes/database.php';

redirectIfNotLoggedIn();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = new Database();
    $conn = $db->getConnection();

    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $profile_picture_url = sanitizeInput($_POST['profile_picture_url']);

    $update_fields = [];
    $params = [":user_id" => $_SESSION['user_id']];

    if (!empty($email)) {
        $update_fields[] = "email = :email";
        $params[":email"] = $email;
    }

    if (!empty($password) && $password === $confirm_password) {
        $update_fields[] = "password = :password";
        $params[":password"] = password_hash($password, PASSWORD_DEFAULT);
    }

    if (!empty($profile_picture_url)) {

        $update_fields[] = "profile_picture = :profile_picture";
        $params[":profile_picture"] = $profile_picture_url;

        $_SESSION['profile_picture'] = $profile_picture_url; 
    }   

    if (!empty($update_fields)) {
        $sql = "UPDATE users SET " . implode(", ", $update_fields) . " WHERE id = :user_id";
        $stmt = $conn->prepare($sql);
        if ($stmt->execute($params)) {
            $_SESSION['success'] = "Perfil actualizado correctamente.";
        } else {
            $_SESSION['error'] = "Hubo un error al actualizar el perfil.";
        }
    } else {
        $_SESSION['error'] = "No se realizaron cambios.";
    }

    header("Location: index.php?page=perfil");
    exit();
}
?>
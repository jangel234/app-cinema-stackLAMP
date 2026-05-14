<?php
session_start();
require_once 'db.php'; // conexión PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $errores = [];

    if (strlen($nombre) < 2) $errores[] = "Nombre muy corto.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = "Email no válido.";
    if (strlen($password) < 6) $errores[] = "La contraseña debe tener al menos 6 caracteres.";

    if (empty($errores)) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password_hash) VALUES (?, ?, ?)");
        try {
            $stmt->execute([$nombre, $email, $hash]);
            $_SESSION['mensaje'] = "Registro exitoso. Inicia sesión.";
            header('Location: login.php');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errores[] = "El email ya está registrado.";
            } else {
                $errores[] = "Error en el servidor.";
            }
        }
    }
}
?>
<form method="post">
    <!-- campos nombre, email, password -->
    <?php if (!empty($errores)): ?>
        <div class="error"><?= implode('<br>', $errores) ?></div>
    <?php endif; ?>
    <button type="submit">Registrarse</button>
</form>
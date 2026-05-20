<?php
require_once 'db.php';
include 'includes/header.php';

$errores = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (strlen($nombre) < 2) $errores[] = "El nombre es muy corto.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = "Ingresa un correo electrónico válido.";
    if (strlen($password) < 6) $errores[] = "La contraseña debe contener al menos 6 caracteres.";

    if (empty($errores)) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password_hash) VALUES (?, ?, ?)");
        try {
            $stmt->execute([$nombre, $email, $hash]);
            $_SESSION['mensaje_registro'] = "¡Registro exitoso! Inicia sesión para continuar.";
            header('Location: login.php');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errores[] = "Este correo electrónico ya está registrado.";
            } else {
                $errores[] = "Ocurrió un error inesperado en el servidor.";
            }
        }
    }
}
?>

<div class="container my-5 py-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card card-auth p-4">
                <div class="text-center mb-4">
                    <h3 class="fw-bold" style="color: var(--onyx);">Registrarse</h3>
                    <p class="text-muted small">Únete para obtener beneficios y promociones</p>
                </div>

                <?php if (!empty($errores)): ?>
                    <div class="alert alert-danger py-2 small" role="alert">
                        <ul class="mb-0 ps-3">
                            <?php foreach($errores as $err): ?> <li><?= $err ?></li> <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="registro.php">
                    <div class="mb-3">
                        <label for="nombre" class="form-label small fw-bold">Nombre Completo</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($nombre ?? '') ?>" placeholder="Juan Pérez" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label small fw-bold">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" placeholder="juan@correo.com" required>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label small fw-bold">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Mínimo 6 caracteres" required>
                    </div>
                    <button type="submit" class="btn btn-cherry w-100 py-2">Crear Cuenta</button>
                </form>

                <div class="text-center mt-4">
                    <span class="small text-muted">¿Ya tienes una cuenta?</span>
                    <a href="login.php" class="small fw-bold ms-1" style="color: var(--baltic-blue); text-decoration: none;">Inicia sesión aquí</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
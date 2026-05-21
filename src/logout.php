<?php
// 1. DESTRUIR LA SESIÓN PRIMERO
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vaciamos las variables de sesión
$_SESSION = array();

// Si se desea destruir la sesión completamente, borramos también la cookie de sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruimos la sesión
session_destroy();

// 2. CARGAMOS EL FRONTEND PARA MOSTRAR EL MODAL
include 'includes/header.php';
?>

<div class="container my-5 py-5 text-center">
    <div class="spinner-border text-secondary" role="status">
        <span class="visually-hidden">Cerrando sesión...</span>
    </div>
</div>

<div class="modal fade" id="logoutModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-body p-4 text-center">
        
        <h2 class="display-1 text-info mb-3" style="color: var(--baltic-blue) !important;">👋</h2>
        <h4 class="fw-bold" style="color: var(--onyx);">Sesión cerrada</h4>
        <p class="text-muted mt-2 mb-4">Has cerrado sesión correctamente. ¡Vuelve pronto a disfrutar de la mejor cartelera!</p>
        
        <a href="index.php" class="btn btn-baltic w-100 py-2">Volver al inicio</a>

      </div>
    </div>
  </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var myModal = new bootstrap.Modal(document.getElementById('logoutModal'));
        myModal.show();
    });
</script>

<?php include 'includes/footer.php'; ?>
<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'db.php';
$stmt = $pdo->query("SELECT id, titulo, imagen, boletos_disponibles FROM peliculas");
$peliculas = $stmt->fetchAll();
?>
<h1>Cartelera</h1>
<?php foreach ($peliculas as $p): ?>
    <div class="pelicula">
        <img src="<?= htmlspecialchars($p['imagen']) ?>" alt="<?= htmlspecialchars($p['titulo']) ?>">
        <h3><?= htmlspecialchars($p['titulo']) ?></h3>
        <p>Asientos disponibles: <strong><?= $p['boletos_disponibles'] ?></strong></p>
        <?php if ($p['boletos_disponibles'] > 0): ?>
            <a href="comprar.php?pelicula_id=<?= $p['id'] ?>">Comprar boletos</a>
        <?php else: ?>
            <span>Agotado</span>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
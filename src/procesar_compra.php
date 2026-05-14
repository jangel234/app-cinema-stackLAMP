<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'db.php';

$pelicula_id = $_POST['pelicula_id'] ?? 0;
$cantidad = (int)($_POST['cantidad'] ?? 0);

if ($cantidad <= 0) {
    die("Cantidad no válida.");
}

$pdo->beginTransaction();
try {
    // Bloquear fila para evitar condiciones de carrera
    $stmt = $pdo->prepare("SELECT id, titulo, boletos_disponibles FROM peliculas WHERE id = ? FOR UPDATE");
    $stmt->execute([$pelicula_id]);
    $pelicula = $stmt->fetch();

    if (!$pelicula) {
        throw new Exception("Película no encontrada.");
    }
    if ($pelicula['boletos_disponibles'] < $cantidad) {
        throw new Exception("No hay suficientes boletos. Disponibles: " . $pelicula['boletos_disponibles']);
    }

    // Actualizar inventario
    $nuevo_stock = $pelicula['boletos_disponibles'] - $cantidad;
    $stmt = $pdo->prepare("UPDATE peliculas SET boletos_disponibles = ? WHERE id = ?");
    $stmt->execute([$nuevo_stock, $pelicula_id]);

    // Registrar compra (precio fijo de ejemplo: 8.50 por boleto)
    $precio_unitario = 8.50;
    $total = $cantidad * $precio_unitario;
    $stmt = $pdo->prepare("INSERT INTO compras (usuario_id, pelicula_id, cantidad, total) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_SESSION['usuario_id'], $pelicula_id, $cantidad, $total]);

    $pdo->commit();

    // Mostrar Ticket Digital
    echo "<h1>Ticket Digital</h1>";
    echo "<p>Película: " . htmlspecialchars($pelicula['titulo']) . "</p>";
    echo "<p>Cantidad de boletos: $cantidad</p>";
    echo "<p>Total a pagar: $" . number_format($total, 2) . "</p>";
    echo "<p>Gracias por tu compra, " . htmlspecialchars($_SESSION['usuario_nombre']) . ".</p>";

} catch (Exception $e) {
    $pdo->rollBack();
    die("Error: " . $e->getMessage());
}
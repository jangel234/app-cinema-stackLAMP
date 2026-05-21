<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'db.php';

// Se espera recibir el ID de la función y un array de IDs de asientos
$funcion_id = $_POST['funcion_id'] ?? 0;
$asientos_seleccionados = $_POST['asientos'] ?? []; // ej: [101, 102, 103]

if (empty($funcion_id) || empty($asientos_seleccionados) || !is_array($asientos_seleccionados)) {
    die("Datos de compra inválidos. Por favor, selecciona una función y al menos un asiento.");
}

$cantidad = count($asientos_seleccionados);

$pdo->beginTransaction();
try {
    // 1. Obtener el precio de la función para calcular el total
    $stmt = $pdo->prepare("SELECT precio, pelicula_id FROM funciones WHERE id = ?");
    $stmt->execute([$funcion_id]);
    $funcion = $stmt->fetch();

    if (!$funcion) {
        throw new Exception("La función seleccionada ya no existe.");
    }
    
    $precio_unitario = $funcion['precio'];
    $total = $cantidad * $precio_unitario;

    // 2. Registrar la compra principal para obtener un ID de compra
    $stmt = $pdo->prepare("INSERT INTO compras (usuario_id, total) VALUES (?, ?)");
    $stmt->execute([$_SESSION['usuario_id'], $total]);
    $compra_id = $pdo->lastInsertId();

    // 3. Intentar reservar cada asiento seleccionado
    // La clave está en el UNIQUE KEY (funcion_id, asiento_id) en la tabla 'boletos'
    $stmtBoletos = $pdo->prepare(
        "INSERT INTO boletos (compra_id, funcion_id, asiento_id, precio_pagado) VALUES (?, ?, ?, ?)"
    );

    foreach ($asientos_seleccionados as $asiento_id) {
        // El execute fallará si el par (funcion_id, asiento_id) ya existe,
        // lo que activará el catch block y revertirá toda la transacción.
        $stmtBoletos->execute([$compra_id, $funcion_id, (int)$asiento_id, $precio_unitario]);
    }

    // 4. Si todo salió bien, confirmar la transacción
    $pdo->commit();

    // 5. Obtener datos para mostrar el ticket digital
    $stmtPeli = $pdo->prepare("SELECT titulo FROM peliculas WHERE id = ?");
    $stmtPeli->execute([$funcion['pelicula_id']]);
    $pelicula_titulo = $stmtPeli->fetchColumn();

    // Mostrar Ticket Digital (esto debería ser una página más elaborada)
    echo "<h1>Ticket Digital</h1>";
    echo "<h3>Compra #$compra_id</h3>";
    echo "<p>Película: " . htmlspecialchars($pelicula_titulo) . "</p>";
    echo "<p>Cantidad de boletos: $cantidad</p>";
    echo "<p>Total pagado: $" . number_format($total, 2) . "</p>";
    echo "<p>Gracias por tu compra, " . htmlspecialchars($_SESSION['usuario_nombre']) . ".</p>";

} catch (Exception $e) {
    $pdo->rollBack();
    
    // El código de error 23000 de PDO/MySQL indica una violación de integridad (como una UNIQUE KEY)
    if ($e->getCode() == 23000) {
        die("Error: Uno o más de los asientos que seleccionaste ya no están disponibles. Por favor, intenta de nuevo.");
    } else {
        die("Error al procesar la compra: " . $e->getMessage());
    }
}
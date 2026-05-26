<?php
/**
 * CLI script para generar usuarios de prueba y un usuario administrador.
 * Uso: php seed_usuarios.php [cantidad_clientes] [admin_email] [admin_password]
 * Ejemplo: php seed_usuarios.php 10 admin@cine.local Admin1234
 */

if (PHP_SAPI !== 'cli') {
    echo "Este script debe ejecutarse desde la línea de comandos.\n";
    exit(1);
}

$cantidad = isset($argv[1]) && is_numeric($argv[1]) && (int)$argv[1] > 0 ? (int)$argv[1] : 10;
$adminEmail = $argv[2] ?? 'admin@cine.local';
$adminPassword = $argv[3] ?? 'Admin1234';

$dbPathCandidates = [
    __DIR__ . '/../src/db.php',
    __DIR__ . '/../html/db.php',
    __DIR__ . '/../../src/db.php',
    '/var/www/html/db.php',
];
$dbFound = false;
foreach ($dbPathCandidates as $path) {
    if (file_exists($path)) {
        require_once $path;
        $dbFound = true;
        break;
    }
}

if (!$dbFound) {
    echo "No se encontró el archivo de conexión a la base de datos (db.php).\n";
    exit(1);
}

function crearUsuario(PDO $pdo, string $nombre, string $email, string $password, string $rol): bool {
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password_hash, rol) VALUES (?, ?, ?, ?)");
    try {
        return $stmt->execute([$nombre, $email, $hash, $rol]);
    } catch (PDOException $e) {
        return false;
    }
}

echo "Conectando a la base de datos...\n";

function ensureUserRoleEnum(PDO $pdo): void {
    $stmt = $pdo->query("SHOW COLUMNS FROM usuarios LIKE 'rol'");
    $column = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$column) {
        return;
    }

    if (strpos($column['Type'], "'superadmin'") === false) {
        $pdo->exec("ALTER TABLE usuarios MODIFY rol ENUM('cliente','admin','superadmin') NOT NULL DEFAULT 'cliente'");
        echo "Actualizado el esquema de usuarios para soportar el rol superadmin.\n";
    }
}

ensureUserRoleEnum($pdo);

$adminCreado = false;

// Crear o actualizar admin principal
$stmt = $pdo->prepare("SELECT id, rol FROM usuarios WHERE email = ? LIMIT 1");
$stmt->execute([$adminEmail]);
$usuarioExistente = $stmt->fetch(PDO::FETCH_ASSOC);
if ($usuarioExistente) {
    if ($usuarioExistente['rol'] !== 'superadmin') {
        $stmtUpdate = $pdo->prepare("UPDATE usuarios SET rol = 'superadmin' WHERE id = ?");
        if ($stmtUpdate->execute([$usuarioExistente['id']])) {
            echo "Usuario existente actualizado a superadmin: $adminEmail\n";
            $adminCreado = true;
        } else {
            echo "El usuario existe pero no se pudo actualizar a superadmin.\n";
        }
    } else {
        echo "Ya existe un superadmin con el email $adminEmail.\n";
    }
} else {
    if (crearUsuario($pdo, 'Administrador Principal', $adminEmail, $adminPassword, 'superadmin')) {
        echo "Administrador principal creado: $adminEmail / $adminPassword\n";
        $adminCreado = true;
    } else {
        echo "Error al crear el administrador principal. Verifica la conexión y los datos.\n";
    }
}

$creados = 0;
for ($i = 1; $i <= $cantidad; $i++) {
    $nombre = "Cliente $i";
    $email = "cliente{$i}@cine.local";
    $password = "Cliente{$i}#2026";

    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo "Ya existe el usuario $email. Omitiendo.\n";
        continue;
    }

    if (crearUsuario($pdo, $nombre, $email, $password, 'cliente')) {
        echo "Usuario creado: $nombre < $email > / $password\n";
        $creados++;
    } else {
        echo "Error al crear usuario $email.\n";
    }
}

echo "\nResumen:\n";
echo "  Admin creado: " . ($adminCreado ? 'Sí' : 'No / ya existente') . "\n";
echo "  Clientes generados: $creados de $cantidad solicitados.\n";

echo "\nSi necesitas un admin distinto, ejecuta: php seed_usuarios.php {$cantidad} otroadmin@cine.local MiPass123\n";

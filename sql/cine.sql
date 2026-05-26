DROP DATABASE IF EXISTS cine;
CREATE DATABASE cine CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cine;

-- 1. Usuarios (Añadido el rol para diferenciar clientes de administradores)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM('cliente', 'admin', 'superadmin') DEFAULT 'cliente',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Películas (Añadidos detalles para el Frontend y banners para el carrusel)
CREATE TABLE peliculas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    sinopsis TEXT,
    duracion INT COMMENT 'Duración en minutos',
    clasificacion VARCHAR(10),
    genero VARCHAR(50),
    poster_url VARCHAR(255) COMMENT 'Imagen vertical para la cuadrícula',
    banner_url VARCHAR(255) COMMENT 'Imagen horizontal para el carrusel',
    estado ENUM('cartelera', 'proximamente') DEFAULT 'cartelera'
);

-- 3. Salas del Cine
CREATE TABLE salas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    capacidad INT NOT NULL,
    tipo ENUM('Tradicional', 'VIP', '3D', 'IMAX') DEFAULT 'Tradicional'
);

-- 4. Funciones (Horarios: Aquí es donde se controlan los boletos disponibles)
CREATE TABLE funciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pelicula_id INT NOT NULL,
    sala_id INT NOT NULL,
    fecha_hora DATETIME NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pelicula_id) REFERENCES peliculas(id) ON DELETE CASCADE,
    FOREIGN KEY (sala_id) REFERENCES salas(id) ON DELETE CASCADE
);

-- 5. Promociones
CREATE TABLE promociones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT,
    imagen_url VARCHAR(255),
    codigo_descuento VARCHAR(20),
    fecha_inicio DATE,
    fecha_fin DATE,
    tipo ENUM('monto','porcentaje','2x1') NOT NULL DEFAULT 'monto',
    stock INT NOT NULL DEFAULT 0,
    descuento DECIMAL(10,2) NOT NULL DEFAULT 0.00
);

-- 6. Compras (Representa la transacción general)
CREATE TABLE compras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    promocion_id INT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (promocion_id) REFERENCES promociones(id)
);

-- 7. Asientos (NUEVO: Define la disposición física de los asientos en una sala)
CREATE TABLE asientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sala_id INT NOT NULL,
    fila VARCHAR(5) NOT NULL,
    numero INT NOT NULL,
    tipo ENUM('normal', 'preferencial', 'discapacitado') DEFAULT 'normal',
    UNIQUE KEY (sala_id, fila, numero),
    FOREIGN KEY (sala_id) REFERENCES salas(id) ON DELETE CASCADE
);

-- 8. Boletos (NUEVO: Representa cada boleto individual de una compra)
CREATE TABLE boletos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    compra_id INT NOT NULL,
    funcion_id INT NOT NULL,
    asiento_id INT NOT NULL,
    precio_pagado DECIMAL(10,2) NOT NULL,
    UNIQUE KEY (funcion_id, asiento_id) COMMENT 'Evita que un asiento se venda dos veces para la misma función',
    FOREIGN KEY (compra_id) REFERENCES compras(id) ON DELETE CASCADE,
    FOREIGN KEY (funcion_id) REFERENCES funciones(id) ON DELETE CASCADE,
    FOREIGN KEY (asiento_id) REFERENCES asientos(id) ON DELETE CASCADE
);

-- ==========================================
-- TRIGGERS
-- ==========================================

-- ==========================================
-- INSERCIÓN DE DATOS DE PRUEBA
-- ==========================================

-- Insertar Salas
INSERT INTO salas (id, nombre, capacidad, tipo) VALUES
(1, 'Sala 1', 100, 'Tradicional'),
(2, 'Sala 2', 100, 'Tradicional'),
(3, 'Sala 3', 100, 'Tradicional'),
(4, 'Sala 4', 100, '3D'),
(5, 'Sala 5 VIP', 40, 'VIP'),
(6, 'Sala 6 VIP', 40, 'VIP'),
(7, 'Sala IMAX', 150, 'IMAX'),
(8, 'Sala IMAX', 150, 'IMAX');


INSERT INTO asientos (sala_id, fila, numero)
WITH RECURSIVE Filas AS (
    SELECT 'A' AS fila
    UNION ALL SELECT CHAR(ASCII(fila) + 1) FROM Filas WHERE ASCII(fila) < ASCII('I')
),
Numeros AS (
    SELECT 1 AS numero
    UNION ALL SELECT numero + 1 FROM Numeros WHERE numero < 9
)
SELECT s.id, F.fila, N.numero 
FROM salas s
CROSS JOIN Filas F
CROSS JOIN Numeros N;

-- Insertar 15 Películas Populares
INSERT INTO peliculas (titulo, sinopsis, duracion, clasificacion, genero, poster_url, banner_url, estado) VALUES
('Interestelar', 'Un grupo de exploradores hace uso de un agujero de gusano para superar las limitaciones de los viajes espaciales humanos.', 169, 'B', 'Ciencia Ficción', 'img/posters/interestelar.jpg', 'img/banners/interestelar_banner.jpg', 'cartelera'),
('The Avengers', 'Los héroes más poderosos de la Tierra deben unirse para detener a Loki.', 143, 'B', 'Acción', 'img/posters/avengers.jpg', 'img/banners/avengers_banner.jpg', 'cartelera'),
('Super Mario Bros. La Película', 'Mario y Luigi viajan por un laberinto subterráneo para rescatar a la Princesa Peach.', 92, 'A', 'Animación', 'img/posters/mario.jpg', 'img/banners/mario_banner.jpg', 'cartelera'),
('Oppenheimer', 'La historia del científico estadounidense J. Robert Oppenheimer y su papel en el desarrollo de la bomba atómica.', 180, 'B15', 'Drama', 'img/posters/oppenheimer.jpg', 'img/banners/oppenheimer_banner.jpg', 'cartelera'),
('Barbie', 'Barbie sufre una crisis que la lleva a cuestionar su mundo y su existencia.', 114, 'A', 'Comedia', 'img/posters/barbie.jpg', 'img/banners/barbie_banner.jpg', 'cartelera'),
('Spider-Man: No Way Home', 'La identidad de Spider-Man es revelada, trayendo consecuencias multiversales.', 148, 'B', 'Acción', 'img/posters/spiderman.jpg', 'img/banners/spiderman_banner.jpg', 'cartelera'),
('El Caballero de la Noche', 'Batman se enfrenta a su mayor reto físico y psicológico: El Guasón.', 152, 'B15', 'Acción', 'img/posters/batman.jpg', 'img/banners/batman_banner.jpg', 'cartelera'),
('Toy Story', 'Un muñeco vaquero se siente amenazado cuando un nuevo juguete espacial llega al cuarto de Andy.', 81, 'A', 'Animación', 'img/posters/toystory.jpg', 'img/banners/toystory_banner.jpg', 'cartelera'),
('Jurassic Park', 'Un parque temático de dinosaurios clonados se sale de control.', 127, 'B', 'Ciencia Ficción', 'img/posters/jurassic.jpg', 'img/banners/jurassic_banner.jpg', 'cartelera'),
('Avatar', 'Un marine parapléjico es enviado a la luna Pandora en una misión única.', 162, 'B', 'Ciencia Ficción', 'img/posters/avatar.jpg', 'img/banners/avatar_banner.jpg', 'cartelera'),
('El Rey León', 'El joven león Simba debe enfrentar su destino para convertirse en rey.', 88, 'A', 'Animación', 'img/posters/reyleon.jpg', 'img/banners/reyleon_banner.jpg', 'cartelera'),
('Matrix', 'Un hacker descubre la verdadera naturaleza de su realidad.', 136, 'B15', 'Ciencia Ficción', 'img/posters/matrix.jpg', 'img/banners/matrix_banner.jpg', 'cartelera'),
('Deadpool & Wolverine', 'Deadpool y Wolverine se unen en una aventura a través del multiverso.', 127, 'C', 'Acción', 'img/posters/deadpool.jpg', 'img/banners/deadpool_banner.jpg', 'cartelera'),
('Shrek', 'Un ogro gruñón emprende un viaje para rescatar a una princesa.', 90, 'A', 'Animación', 'img/posters/shrek.jpg', 'img/banners/shrek_banner.jpg', 'cartelera'),
('Volver al Futuro', 'Un joven es enviado accidentalmente 30 años en el pasado.', 116, 'A', 'Ciencia Ficción', 'img/posters/bttf.jpg', 'img/banners/bttf_banner.jpg', 'cartelera');

-- ==========================================
-- FUNCIONES 26 MAY – 2 JUN 2026
-- (3 funciones por película, total 45)
-- ==========================================

-- Martes 26 de mayo (5 funciones)
INSERT INTO funciones (pelicula_id, sala_id, fecha_hora, precio) VALUES
(1, 7, '2026-05-26 16:00:00', 120.00),   -- Interestelar IMAX
(2, 5, '2026-05-26 18:00:00', 150.00),   -- Avengers VIP
(5, 2, '2026-05-26 18:00:00', 80.00),    -- Barbie Tradicional
(3, 1, '2026-05-26 20:00:00', 80.00),    -- Mario Bros Tradicional
(4, 4, '2026-05-26 22:00:00', 95.00);    -- Oppenheimer 3D

-- Miércoles 27 de mayo (5 funciones)
INSERT INTO funciones (pelicula_id, sala_id, fecha_hora, precio) VALUES
(6, 8, '2026-05-27 16:00:00', 120.00),   -- Spider-Man IMAX
(9, 2, '2026-05-27 16:00:00', 80.00),    -- Jurassic Park Tradicional
(8, 3, '2026-05-27 18:00:00', 80.00),    -- Toy Story Tradicional
(10, 4, '2026-05-27 18:00:00', 95.00),   -- Avatar 3D
(7, 6, '2026-05-27 20:00:00', 150.00);   -- El Caballero de la Noche VIP

-- Jueves 28 de mayo (5 funciones)
INSERT INTO funciones (pelicula_id, sala_id, fecha_hora, precio) VALUES
(13, 1, '2026-05-28 18:00:00', 80.00),   -- Deadpool Tradicional
(15, 8, '2026-05-28 18:00:00', 120.00),  -- Volver al Futuro IMAX
(11, 7, '2026-05-28 20:00:00', 120.00),  -- El Rey León IMAX
(12, 5, '2026-05-28 22:00:00', 150.00),  -- Matrix VIP
(14, 2, '2026-05-28 22:00:00', 80.00);   -- Shrek Tradicional

-- Viernes 29 de mayo (6 funciones)
INSERT INTO funciones (pelicula_id, sala_id, fecha_hora, precio) VALUES
(3, 6, '2026-05-29 16:00:00', 150.00),   -- Mario Bros VIP
(7, 1, '2026-05-29 16:00:00', 80.00),    -- El Caballero Tradicional
(11, 2, '2026-05-29 18:00:00', 80.00),   -- El Rey León Tradicional
(1, 3, '2026-05-29 20:00:00', 80.00),    -- Interestelar Tradicional
(9, 4, '2026-05-29 20:00:00', 95.00),    -- Jurassic Park 3D
(5, 8, '2026-05-29 22:00:00', 120.00);   -- Barbie IMAX

-- Sábado 30 de mayo (6 funciones)
INSERT INTO funciones (pelicula_id, sala_id, fecha_hora, precio) VALUES
(2, 7, '2026-05-30 16:00:00', 120.00),   -- Avengers IMAX
(12, 4, '2026-05-30 16:00:00', 95.00),   -- Matrix 3D
(6, 3, '2026-05-30 18:00:00', 80.00),    -- Spider-Man Tradicional
(4, 5, '2026-05-30 20:00:00', 150.00),   -- Oppenheimer VIP
(8, 8, '2026-05-30 22:00:00', 120.00),   -- Toy Story IMAX
(10, 1, '2026-05-30 22:00:00', 80.00);   -- Avatar Tradicional

-- Domingo 31 de mayo (6 funciones)
INSERT INTO funciones (pelicula_id, sala_id, fecha_hora, precio) VALUES
(5, 1, '2026-05-31 16:00:00', 80.00),    -- Barbie Tradicional
(15, 2, '2026-05-31 16:00:00', 80.00),   -- Volver al Futuro Tradicional
(14, 4, '2026-05-31 18:00:00', 95.00),   -- Shrek 3D
(13, 6, '2026-05-31 20:00:00', 150.00),  -- Deadpool VIP
(2, 3, '2026-05-31 22:00:00', 80.00),    -- Avengers Tradicional
(7, 7, '2026-05-31 22:00:00', 120.00);   -- El Caballero IMAX

-- Lunes 1 de junio (6 funciones)
INSERT INTO funciones (pelicula_id, sala_id, fecha_hora, precio) VALUES
(8, 5, '2026-06-01 16:00:00', 150.00),   -- Toy Story VIP
(4, 1, '2026-06-01 18:00:00', 80.00),    -- Oppenheimer Tradicional
(9, 7, '2026-06-01 18:00:00', 120.00),   -- Jurassic Park IMAX
(3, 8, '2026-06-01 20:00:00', 120.00),   -- Mario Bros IMAX
(12, 2, '2026-06-01 20:00:00', 80.00),   -- Matrix Tradicional
(10, 6, '2026-06-01 22:00:00', 150.00);  -- Avatar VIP

-- Martes 2 de junio (6 funciones)
INSERT INTO funciones (pelicula_id, sala_id, fecha_hora, precio) VALUES
(13, 7, '2026-06-02 16:00:00', 120.00),  -- Deadpool IMAX
(1, 6, '2026-06-02 18:00:00', 150.00),   -- Interestelar VIP
(14, 3, '2026-06-02 20:00:00', 80.00),   -- Shrek Tradicional
(6, 5, '2026-06-02 22:00:00', 150.00),   -- Spider-Man VIP
(11, 4, '2026-06-02 22:00:00', 95.00),   -- El Rey León 3D
(15, 8, '2026-06-02 22:00:00', 120.00);  -- Volver al Futuro IMAX

-- Insertar Promociones
INSERT INTO promociones (titulo, descripcion, imagen_url, codigo_descuento, fecha_inicio, fecha_fin, tipo, stock, descuento) VALUES
('CineStack: el mejor 2x1', 'Disfruta de todas las películas al 2x1 todos los martes.', 'img/promos/martes2x1.jpg', 'MARTES2X1', '2026-01-01', '2026-12-31', '2x1', 120, 0.00),
('Combo Nachos', 'Compra un boleto IMAX y llévate unos nachos a mitad de precio.', 'img/promos/nachos.jpg', NULL, '2026-05-01', '2026-12-31', 'monto', 80, 25.00);
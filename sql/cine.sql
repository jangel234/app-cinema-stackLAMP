DROP DATABASE IF EXISTS cine;
CREATE DATABASE cine CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cine;

-- 1. Usuarios (Añadido el rol para diferenciar clientes de administradores)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM('cliente', 'admin') DEFAULT 'cliente',
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
    asientos_disponibles INT NOT NULL,
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
    fecha_fin DATE
);

-- 6. Compras (Ahora enlazadas a la función, no a la película en general)
CREATE TABLE compras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    funcion_id INT NOT NULL,
    cantidad INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (funcion_id) REFERENCES funciones(id)
);

-- ==========================================
-- INSERCIÓN DE DATOS DE PRUEBA
-- ==========================================

-- Insertar Salas
INSERT INTO salas (nombre, capacidad, tipo) VALUES
('Sala 1', 100, 'Tradicional'),
('Sala 2', 100, '3D'),
('Sala 3 VIP', 40, 'VIP'),
('Sala IMAX', 150, 'IMAX');

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

-- Insertar Funciones de prueba (Asumiendo que hoy es una fecha cercana)
INSERT INTO funciones (pelicula_id, sala_id, fecha_hora, precio, asientos_disponibles) VALUES
(1, 4, DATE_ADD(NOW(), INTERVAL 1 DAY), 120.00, 150), -- Interestelar IMAX
(3, 1, DATE_ADD(NOW(), INTERVAL 2 HOUR), 80.00, 100), -- Mario Bros
(6, 2, DATE_ADD(NOW(), INTERVAL 5 HOUR), 95.00, 100), -- Spiderman 3D
(4, 3, DATE_ADD(NOW(), INTERVAL 1 DAY), 150.00, 40);  -- Oppenheimer VIP

-- Insertar Promociones
INSERT INTO promociones (titulo, descripcion, imagen_url, codigo_descuento, fecha_inicio, fecha_fin) VALUES
('Martes 2x1', 'Disfruta de todas las películas al 2x1 todos los martes.', 'img/promos/martes2x1.jpg', 'MARTES2X1', '2024-01-01', '2024-12-31'),
('Combo Nachos', 'Compra un boleto IMAX y llévate unos nachos a mitad de precio.', 'img/promos/nachos.jpg', NULL, '2024-06-01', '2024-08-31');
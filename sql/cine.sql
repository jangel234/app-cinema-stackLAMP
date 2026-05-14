CREATE DATABASE IF NOT EXISTS cine;
USE cine;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE peliculas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    imagen VARCHAR(100),
    boletos_disponibles INT NOT NULL DEFAULT 60
);

CREATE TABLE compras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    pelicula_id INT NOT NULL,
    cantidad INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (pelicula_id) REFERENCES peliculas(id)
);

-- Insertar 3 películas de ejemplo
INSERT INTO peliculas (titulo, imagen, boletos_disponibles) VALUES
('El Gran Escape', 'img/escape.jpg', 60),
('Mundo Virtual', 'img/virtual.jpg', 60),
('La Última Frontera', 'img/frontera.jpg', 60);
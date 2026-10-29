
CREATE DATABASE IF NOT EXISTS parqueadero_comunal
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE parqueadero_comunal;

CREATE TABLE administradores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE residentes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(150) NOT NULL,
    cedula VARCHAR(30) NOT NULL UNIQUE,
    torre VARCHAR(20) NOT NULL,
    apartamento VARCHAR(20) NOT NULL,
    placa VARCHAR(10) NOT NULL UNIQUE,
    color VARCHAR(40) NOT NULL,
    estado ENUM('activo','inactivo') DEFAULT 'activo',
    autorizado ENUM('autorizado','no autorizado')
      DEFAULT 'no autorizado',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE puestos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero INT NOT NULL UNIQUE,
    estado ENUM('disponible','ocupado')
      DEFAULT 'disponible',
    residente_id INT NULL,
    FOREIGN KEY (residente_id)
      REFERENCES residentes(id)
      ON DELETE SET NULL
);

CREATE TABLE movimientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    residente_id INT NOT NULL,
    puesto_id INT NULL,
    tipo ENUM('entrada','salida') NOT NULL,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    observacion VARCHAR(255) NULL,
    administrador_id INT NULL,
    FOREIGN KEY (residente_id) REFERENCES residentes(id),
    FOREIGN KEY (puesto_id) REFERENCES puestos(id),
    FOREIGN KEY (administrador_id)
      REFERENCES administradores(id)
      ON DELETE SET NULL
);

-- Crear exactamente 50 puestos.
INSERT INTO puestos (numero)
WITH RECURSIVE numeros AS (
    SELECT 1 AS n
    UNION ALL
    SELECT n + 1 FROM numeros WHERE n < 50
)
SELECT n FROM numeros;

-- Índice para búsquedas de vehículos.
CREATE INDEX idx_movimientos_fecha
ON movimientos(fecha_hora);

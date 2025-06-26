DROP DATABASE IF EXISTS cashAnt;

CREATE DATABASE cashAnt CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;

USE cashAnt;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
    apellido VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
    user VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
    email VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
    contrasenia VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
    salario_base DECIMAL(10,2) NULL,
    porcentaje_gastos FLOAT NULL,
    UNIQUE INDEX (email)
);

CREATE TABLE gastos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    fecha DATE NOT NULL,
    descripcion TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
    monto DECIMAL(10,2) NOT NULL,
    categoria ENUM('Alimentación', 'Entretenimiento', 'Ocio', 'Educación', 'Transporte', 'Otros') 
        CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE balance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    mes DATE NOT NULL,
    total_ingresos DECIMAL(10,2) NULL,
    total_gastos DECIMAL(10,2) NULL,
    ahorros DECIMAL(10,2) NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

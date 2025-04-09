-- Creación de la base de datos
CREATE DATABASE IF NOT EXISTS DS6;
USE DS6;



-- Tabla nacionalidad
CREATE TABLE nacionalidad (
    codigo VARCHAR(3) PRIMARY KEY,
    pais VARCHAR(40)
);

-- Tabla provincia
CREATE TABLE provincia (
    codigo_provincia VARCHAR(2) PRIMARY KEY,
    nombre_provincia VARCHAR(50)
);

-- Tabla distrito
CREATE TABLE distrito (
    codigo_provincia VARCHAR(2),
    codigo_distrito VARCHAR(4) PRIMARY KEY,
    codigo VARCHAR(2),
    nombre_distrito VARCHAR(150),
    FOREIGN KEY (codigo_provincia) REFERENCES provincia(codigo_provincia)
);

-- Tabla corregimiento
CREATE TABLE corregimiento (
    codigo_provincia VARCHAR(2),
    codigo_distrito VARCHAR(4),
    codigo VARCHAR(2),
    codigo_corregimiento VARCHAR(6) PRIMARY KEY,
    nombre_corregimiento VARCHAR(150),
    FOREIGN KEY (codigo_provincia) REFERENCES provincia(codigo_provincia),
    FOREIGN KEY (codigo_distrito) REFERENCES distrito(codigo_distrito)
);

-- Tabla departamento
CREATE TABLE departamento (
    codigo VARCHAR(2) PRIMARY KEY,
    nombre VARCHAR(40)
);



-- Tabla e_eliminados
CREATE TABLE e_eliminados (
    cedula VARCHAR(13),
    prefijo VARCHAR(6),
    tomo VARCHAR(6),
    asiento VARCHAR(6),
    nombre1 VARCHAR(25),
    nombre2 VARCHAR(25),
    apellido1 VARCHAR(25),
    apellido2 VARCHAR(25),
    apellidoc VARCHAR(25),
    genero INT(1),
    estado_civil INT(1),
    tipo_sangre VARCHAR(3),
    usa_ac INT(1),
    f_nacimiento DATE,
    celular INT(8),
    telefono INT(7),
    correo VARCHAR(40),
    provincia VARCHAR(2),
    distrito VARCHAR(4),
    corregimiento VARCHAR(6),
    calle VARCHAR(30),
    casa VARCHAR(10),
    comunidad VARCHAR(25),
    nacionalidad VARCHAR(3),
    f_contra DATE,
    cargo VARCHAR(2),
    departamento VARCHAR(2),
    estado INT(1),
    f_eliminacion DATE
);

-- Tabla u_eliminados
CREATE TABLE u_eliminados (
    id INT,
    cedula VARCHAR(13),
    contraseña VARCHAR(18),
    correo_institucional VARCHAR(40),
    f_eliminacion DATE
);


-- Tabla empleados
CREATE TABLE empleados (
    cedula VARCHAR(13) PRIMARY KEY,
    prefijo VARCHAR(6),
    tomo VARCHAR(6),
    asiento VARCHAR(6),
    nombre1 VARCHAR(25),
    nombre2 VARCHAR(25),
    apellido1 VARCHAR(25),
    apellido2 VARCHAR(25),
    apellidoc VARCHAR(25),
    genero INT(1),
    estado_civil INT(1),
    tipo_sangre VARCHAR(3),
    usa_ac INT(1),
    f_nacimiento DATE,
    celular INT(8),
    telefono INT(7),
    correo VARCHAR(40),
    provincia VARCHAR(2),
    distrito VARCHAR(4),
    corregimiento VARCHAR(6),
    calle VARCHAR(30),
    casa VARCHAR(10),
    comunidad VARCHAR(25),
    nacionalidad VARCHAR(3),
    f_contra DATE,
    cargo VARCHAR(2),
    departamento VARCHAR(2),
    estado INT(1),
    FOREIGN KEY (provincia) REFERENCES provincia(codigo_provincia),
    FOREIGN KEY (distrito) REFERENCES distrito(codigo_distrito),
    FOREIGN KEY (corregimiento) REFERENCES corregimiento(codigo_corregimiento),
    FOREIGN KEY (nacionalidad) REFERENCES nacionalidad(codigo),
    
    FOREIGN KEY (departamento) REFERENCES departamento(codigo)
);

-- Tabla usuarios
CREATE TABLE usuarios (
    id INT PRIMARY KEY,
    cedula VARCHAR(13),
    contraseña VARCHAR(18),
    correo_institucional VARCHAR(40),
    FOREIGN KEY (cedula) REFERENCES empleados(cedula)
);

-- Tabla cargo
CREATE TABLE cargo (
    dep_codigo VARCHAR(2),
    codigo VARCHAR(2) PRIMARY KEY,
    nombre VARCHAR(40),
    FOREIGN KEY (dep_codigo) REFERENCES departamento(codigo)
);

ALTER TABLE empleados
ADD FOREIGN KEY (cargo) REFERENCES cargo(codigo);
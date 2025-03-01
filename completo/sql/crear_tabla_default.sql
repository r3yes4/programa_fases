USE bleet;

CREATE TABLE departamentos (
    id_departamento INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL  
);

CREATE TABLE usuarios (
    usuario VARCHAR(50) NOT NULL PRIMARY KEY,
    password VARCHAR(255) NOT NULL,
    admin TINYINT(1) NOT NULL DEFAULT 0 CHECK (admin IN (0, 1)),
    id_departamento INT,
    FOREIGN KEY (id_departamento) REFERENCES departamentos(id_departamento)
);

CREATE TABLE archivos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ruta_archivo VARCHAR(255) NOT NULL,
    analizado TINYINT(1) NOT NULL DEFAULT 0 CHECK (analizado IN (0, 1)),
    virus TINYINT(1) NOT NULL DEFAULT 0 CHECK (virus IN (0, 1)),
    fecha_subida DATETIME DEFAULT CURRENT_TIMESTAMP,
    ruta_carpeta VARCHAR(255),
    id_usuario VARCHAR(50) NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(usuario)
);

CREATE TABLE archivos_compartidos (
    id_compartido INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario VARCHAR(50),                   -- Referencia al usuario que comparte
    id_archivo INT,                            -- Referencia al archivo que se comparte
    id_usuario_compartido VARCHAR(50),        -- Referencia al usuario que recibe el archivo compartido
    fecha_compartido DATETIME DEFAULT CURRENT_TIMESTAMP, -- Fecha en que se compartió el archivo
    permisos VARCHAR(50) DEFAULT 'lectura',  -- Puede ser "lectura", "escritura", etc.
    FOREIGN KEY (id_usuario) REFERENCES usuarios(usuario),
    FOREIGN KEY (id_archivo) REFERENCES archivos(id),
    FOREIGN KEY (id_usuario_compartido) REFERENCES usuarios(usuario)
);


INSERT INTO usuarios (usuario, password, admin) 
VALUES 
    ('admin', '$2y$10$myVvD0glXldf89ruwuDQmuktcVJ0hCr6ENf9uLTfM7cHnC5Thnowy', 1);


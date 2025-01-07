USE bleet;

CREATE TABLE users (
    user VARCHAR(50) NOT NULL PRIMARY KEY,
    password VARCHAR(255) NOT NULL,
    admin TINYINT(1) NOT NULL DEFAULT 0 CHECK (admin IN (0, 1))
);

INSERT INTO users (user, password, admin) 
VALUES 
    ('admin', '$2y$10$myVvD0glXldf89ruwuDQmuktcVJ0hCr6ENf9uLTfM7cHnC5Thnowy', 1);
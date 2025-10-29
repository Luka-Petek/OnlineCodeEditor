DROP DATABASE IF EXISTS onlinecodeeditor;
CREATE DATABASE onlinecodeeditor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE onlinecodeeditor;

DROP USER IF EXISTS 'guest'@'%';
CREATE USER 'guest'@'%' IDENTIFIED BY 'guest';
GRANT SELECT, INSERT, UPDATE, DELETE ON onlinecodeeditor.* TO 'guest'@'%';
FLUSH PRIVILEGES;

DROP TABLE IF EXISTS datoteka;
DROP TABLE IF EXISTS projekt;
DROP TABLE IF EXISTS nastavitve;
DROP TABLE IF EXISTS uporabnik;

CREATE TABLE uporabnik(
    id INT PRIMARY KEY AUTO_INCREMENT,
    ime VARCHAR(50) NOT NULL,
    priimek VARCHAR(50) NOT NULL,
    gmail VARCHAR(255) NOT NULL UNIQUE,
    geslo VARCHAR(255) NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    datumNastanka DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE nastavitve(
    FKuporabnik INT PRIMARY KEY,
    tema VARCHAR(50) DEFAULT 'dark',
    fontSize INT DEFAULT 14,
    autoSave INT DEFAULT 30,

    FOREIGN KEY (FKuporabnik) REFERENCES uporabnik(id) ON DELETE CASCADE
);

CREATE TABLE projekt(
    id INT PRIMARY KEY AUTO_INCREMENT,
    imeProjekta VARCHAR(255) NOT NULL,
    opis TEXT,
    datumNastanka DATETIME DEFAULT CURRENT_TIMESTAMP,
    FKuporabnik INT NOT NULL,

    FOREIGN KEY (FKuporabnik) REFERENCES uporabnik(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_project (FKuporabnik, imeProjekta)
);

CREATE TABLE datoteka(
    id INT PRIMARY KEY AUTO_INCREMENT,
    FKprojekt INT NOT NULL,
    
    imeDatoteke VARCHAR(255) NOT NULL,
    potDatoteke VARCHAR(512) NOT NULL UNIQUE,
    jezik VARCHAR(50) NOT NULL,
    jeMain BOOLEAN DEFAULT FALSE,
    datumNastanka DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (FKprojekt) REFERENCES projekt(id) ON DELETE CASCADE,
    UNIQUE KEY unique_project_file (FKprojekt, imeDatoteke)
);

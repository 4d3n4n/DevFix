CREATE DATABASE IF NOT EXISTS `devfix`;

use `devfix`;

DROP TABLE IF EXISTS type_panne;

DROP TABLE IF EXISTS type_appareil;

DROP TABLE IF EXISTS marque;

DROP TABLE IF EXISTS contenir;

DROP TABLE IF EXISTS modele;

DROP TABLE IF EXISTS pieces;

DROP TABLE IF EXISTS role;

DROP TABLE IF EXISTS notes;

DROP TABLE IF EXISTS ville;

DROP TABLE IF EXISTS utilisateur;

DROP TABLE IF EXISTS appareil;

DROP TABLE IF EXISTS reparation;

DROP TABLE IF EXISTS tarif;

DROP TABLE IF EXISTS devis;

DROP TABLE IF EXISTS alerte;

DROP TABLE IF EXISTS parametre;

DROP TABLE IF EXISTS composer;

CREATE TABLE
    type_panne (
        id_panne INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
        lib_panne VARCHAR(150) NOT NULL,
        img_panne VARCHAR(40)
    ) ENGINE = InnoDB;

CREATE TABLE
    type_appareil (
        id_type_appareil INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
        lib_type_appareil VARCHAR(50) NOT NULL,
        img_type_appareil VARCHAR(100)
    ) ENGINE = InnoDB;

CREATE TABLE
    marque (
        id_marque INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
        lib_marque VARCHAR(150) NOT NULL,
        logo_marque VARCHAR(150)
    ) ENGINE = InnoDB;

CREATE TABLE
    contenir (
        id_type_appareil INT NOT NULL,
        id_marque INT NOT NULL
    ) ENGINE = InnoDB;

CREATE TABLE
    modele (
        id_modele INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
        lib_modele VARCHAR(150) NOT NULL,
        img_modele VARCHAR(40),
        id_marque INT NOT NULL
    ) ENGINE = InnoDB;

CREATE TABLE
    pieces (
        id_pieces INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
        lib_pieces VARCHAR(150) NOT NULL,
        ref_fabricant VARCHAR(150) NOT NULL,
        stock INT NOT NULL,
        img_piece VARCHAR(50) NOT NULL,
        delai_livraison_pieces INT NOT NULL,
        prix_pieces_ttc DECIMAL(15, 3) NOT NULL,
        date_creation_pieces DATETIME NOT NULL,
        date_maj_pieces DATETIME NOT NULL,
        id_modele INT NOT NULL
    ) ENGINE = InnoDB;

CREATE TABLE
    role (
        id_role INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
        code_role VARCHAR(50) NOT NULL,
        libelle_role VARCHAR(50) NOT NULL
    ) ENGINE = InnoDB;

CREATE TABLE
    notes (
        id_notes INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
        titre_notes VARCHAR(100) NOT NULL,
        commentaires TEXT NOT NULL,
        date_creation_note DATETIME NOT NULL,
        date_maj_note DATETIME NOT NULL,
        id_modele INT NOT NULL
    ) ENGINE = InnoDB;

CREATE TABLE
    ville (
        id_ville INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
        nom_ville VARCHAR(50) NOT NULL,
        code_insee CHAR(5) NOT NULL,
        code_postal CHAR(5) NOT NULL
    ) ENGINE = InnoDB;

CREATE TABLE
    utilisateur (
        id_utilisateur INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
        nom VARCHAR(100) NOT NULL,
        prenom VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL,
        mdp VARCHAR(150) NOT NULL,
        numero VARCHAR(20) NOT NULL,
        adresse VARCHAR(255),
        complement_adresse VARCHAR(50),
        date_creation_utilisateur DATETIME NOT NULL,
        date_maj_utilisateur DATETIME NOT NULL,
        id_role INT NOT NULL,
        id_ville INT NOT NULL
    ) ENGINE = InnoDB;

CREATE TABLE
    appareil (
        id_appareil INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
        code_imei VARCHAR(100),
        num_serie VARCHAR(100) NOT NULL,
        date_creation_appareil DATETIME NOT NULL,
        id_utilisateur INT NOT NULL,
        id_modele INT NOT NULL,
        id_type_appareil INT NOT NULL
    ) ENGINE = InnoDB;

CREATE TABLE
    reparation (
        id_reparation INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
        observation TEXT NOT NULL,
        date_demande DATETIME NOT NULL,
        date_maj_demande DATETIME NOT NULL,
        id_panne INT NOT NULL,
        id_appareil INT NOT NULL,
        id_utilisateur INT NOT NULL
    ) ENGINE = InnoDB;

CREATE TABLE
    tarif (
        id_tarif INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
        montant DECIMAL(15, 3) NOT NULL,
        date_creation DATETIME NOT NULL,
        date_maj_tarif DATETIME NOT NULL,
        id_panne INT NOT NULL,
        id_modele INT NOT NULL
    ) ENGINE = InnoDB;

CREATE TABLE
    devis (
        id_devis INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
        num_devis VARCHAR(50) NOT NULL,
        date_devis DATETIME NOT NULL,
        prix_ttc DECIMAL(15, 3) NOT NULL,
        commentaire_devis TEXT,
        date_restitution DATE NOT NULL,
        statut BOOL NOT NULL,
        date_maj_devis DATETIME NOT NULL,
        id_reparation INT NOT NULL,
        id_utilisateur INT NOT NULL
    ) ENGINE = InnoDB;

CREATE TABLE
    alerte (
        id_alerte INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
        titre_alerte VARCHAR(100) NOT NULL,
        contenu_alerte TEXT NOT NULL,
        date_alerte DATETIME NOT NULL,
        id_utilisateur INT NOT NULL,
        id_devis INT NOT NULL
    ) ENGINE = InnoDB;

CREATE TABLE
    parametre (
        id_parametre INT AUTO_INCREMENT PRIMARY KEY,
        nom_entreprise VARCHAR(50) NOT NULL,
        siren VARCHAR(50) NOT NULL,
        code_tva VARCHAR(50) NOT NULL,
        adresse_entreprise VARCHAR(50) NOT NULL,
        email_entreprise VARCHAR(50) NOT NULL,
        telephone_entreprise VARCHAR(50) NOT NULL,
        couleur_primaire_entreprise VARCHAR(30) NOT NULL,
        logo_entreprise VARCHAR(30) NOT NULL
    ) ENGINE = InnoDB;

CREATE TABLE
    composer (
        id_devis Int NOT NULL,
        id_pieces Int NOT NULL,
        PRIMARY KEY (id_devis, id_pieces)
    ) ENGINE = InnoDB;

-- ALTER TABLE tbl_name DROP FOREIGN KEY fk_symbol;
ALTER TABLE composer ADD CONSTRAINT composer_devis_FK FOREIGN KEY (id_devis) REFERENCES devis (id_devis);
ALTER TABLE composer ADD CONSTRAINT composer_pieces_FK FOREIGN KEY (id_pieces) REFERENCES pieces (id_pieces);

ALTER TABLE alerte ADD CONSTRAINT fk_alerte_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur);
ALTER TABLE alerte ADD CONSTRAINT fk_alerte_devis FOREIGN KEY (id_devis) REFERENCES devis (id_devis);

ALTER TABLE devis ADD CONSTRAINT fk_devis_reparation FOREIGN KEY (id_reparation) REFERENCES reparation (id_reparation);
ALTER TABLE devis ADD CONSTRAINT fk_devis_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur);

ALTER TABLE tarif ADD CONSTRAINT fk_tarif_type_panne FOREIGN KEY (id_panne) REFERENCES type_panne (id_panne) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE tarif ADD CONSTRAINT fk_tarif_modele FOREIGN KEY (id_modele) REFERENCES modele (id_modele) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE reparation ADD CONSTRAINT fk_reparation_type_panne FOREIGN KEY (id_panne) REFERENCES type_panne (id_panne) ON UPDATE CASCADE;
ALTER TABLE reparation ADD CONSTRAINT fk_reparation_appareil FOREIGN KEY (id_appareil) REFERENCES appareil (id_appareil) ON UPDATE CASCADE;
ALTER TABLE reparation ADD CONSTRAINT fk_reparation_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur);

ALTER TABLE appareil ADD CONSTRAINT fk_appareil_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur) ON DELETE CASCADE;
ALTER TABLE appareil ADD CONSTRAINT fk_appareil_modele FOREIGN KEY (id_modele) REFERENCES modele (id_modele);
ALTER TABLE appareil ADD CONSTRAINT fk_appareil_type_appareil FOREIGN KEY (id_type_appareil) REFERENCES type_appareil (id_type_appareil) ON UPDATE CASCADE;

ALTER TABLE utilisateur ADD CONSTRAINT fk_utilisateur_role FOREIGN KEY (id_role) REFERENCES role (id_role) ON UPDATE CASCADE;
ALTER TABLE utilisateur ADD CONSTRAINT fk_utilisateur_ville FOREIGN KEY (id_ville) REFERENCES ville (id_ville) ON UPDATE CASCADE;


ALTER TABLE notes ADD CONSTRAINT fk_notes_modele FOREIGN KEY (id_modele) REFERENCES modele (id_modele);


ALTER TABLE pieces ADD CONSTRAINT fk_pieces_modele FOREIGN KEY (id_modele) REFERENCES modele (id_modele);

ALTER TABLE modele ADD CONSTRAINT fk_modele_marque FOREIGN KEY (id_marque) REFERENCES marque (id_marque) ON DELETE CASCADE;

ALTER TABLE contenir ADD CONSTRAINT fk_contenir_type_appareil FOREIGN KEY (id_type_appareil) REFERENCES type_appareil (id_type_appareil) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE contenir ADD CONSTRAINT fk_contenir_marque FOREIGN KEY (id_marque) REFERENCES marque (id_marque) ON DELETE CASCADE;
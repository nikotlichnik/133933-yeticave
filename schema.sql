CREATE DATABASE yeticave
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

USE yeticave;

CREATE TABLE users (
  id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  registration_date TIMESTAMP     NOT NULL,
  email             CHAR(255)     NOT NULL,
  name              CHAR(128)     NOT NULL,
  password          CHAR(255)     NOT NULL,
  avatar_path       CHAR(255),
  contacts          VARCHAR(1000) NOT NULL
);

CREATE UNIQUE INDEX email
  ON users (email);

CREATE TABLE categories (
  id   INT AUTO_INCREMENT PRIMARY KEY,
  name CHAR(128) NOT NULL
);

CREATE TABLE lots (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name            CHAR(255)     NOT NULL,
  description     VARCHAR(1000) NOT NULL,
  img_path        CHAR(255)     NOT NULL,
  start_price     INT UNSIGNED  NOT NULL,
  bet_step        INT UNSIGNED  NOT NULL,
  creation_date   TIMESTAMP     NOT NULL,
  expiration_date TIMESTAMP     NOT NULL,
  author          INT UNSIGNED  NOT NULL,
  winner          INT UNSIGNED,
  category        INT UNSIGNED  NOT NULL
);

CREATE INDEX lot_name
  ON lots (name);
CREATE INDEX lot_description
  ON lots (description);

CREATE TABLE bets (
  id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  date   TIMESTAMP    NOT NULL,
  bet    INT UNSIGNED NOT NULL,
  author INT UNSIGNED NOT NULL,
  lot    INT UNSIGNED NOT NULL
);

CREATE FULLTEXT INDEX ft_lot_search ON lots(name, description);

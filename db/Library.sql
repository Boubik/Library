-- MySQL Script generated by MySQL Workbench
-- Sat Dec 14 16:57:07 2019
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema Library
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema Library
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `Library` DEFAULT CHARACTER SET utf8 ;
USE `Library` ;

-- -----------------------------------------------------
-- Table `Library`.`room`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Library`.`room` (
  `name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`name`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Library`.`book`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Library`.`book` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `relase` YEAR NOT NULL,
  `language` CHAR(2) NOT NULL,
  `ISBN` VARCHAR(20) NULL,
  `pages` SMALLINT NOT NULL,
  `img` VARCHAR(200) NULL,
  `room_name` VARCHAR(45) NOT NULL,
  `show` TINYINT NULL,
  `price` SMALLINT UNSIGNED NULL,
  `level` VARCHAR(5) NULL,
  `accesories` VARCHAR(250) NULL,
  `for` VARCHAR(250) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_book_room1_idx` (`room_name` ASC),
  CONSTRAINT `fk_book_room1`
    FOREIGN KEY (`room_name`)
    REFERENCES `Library`.`room` (`name`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Library`.`author`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Library`.`author` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `f_name` VARCHAR(45) NOT NULL,
  `l_name` VARCHAR(45) NOT NULL,
  `bday` DATE NULL DEFAULT NULL,
  `country` CHAR(2) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Library`.`user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Library`.`user` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `f_name` VARCHAR(45) NOT NULL,
  `l_name` VARCHAR(45) NOT NULL,
  `username` VARCHAR(45) NOT NULL,
  `password` VARCHAR(512) NOT NULL,
  `last_login` DATETIME NOT NULL,
  `created` DATETIME NOT NULL,
  `role` VARCHAR(45) NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Library`.`reservation`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Library`.`reservation` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `s-reservation` DATE NOT NULL,
  `e-reservation` DATE NOT NULL,
  `taken` TINYINT NOT NULL DEFAULT 0,
  `user_id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_reservation_user1_idx` (`user_id` ASC),
  CONSTRAINT `fk_reservation_user1`
    FOREIGN KEY (`user_id`)
    REFERENCES `Library`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Library`.`book_has_author`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Library`.`book_has_author` (
  `book_id` BIGINT UNSIGNED NOT NULL,
  `author_id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`book_id`, `author_id`),
  INDEX `fk_book_has_author_author1_idx` (`author_id` ASC),
  INDEX `fk_book_has_author_book1_idx` (`book_id` ASC),
  CONSTRAINT `fk_book_has_author_book1`
    FOREIGN KEY (`book_id`)
    REFERENCES `Library`.`book` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_book_has_author_author1`
    FOREIGN KEY (`author_id`)
    REFERENCES `Library`.`author` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Library`.`genre`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Library`.`genre` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Library`.`book_has_genre`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Library`.`book_has_genre` (
  `book_id` BIGINT UNSIGNED NOT NULL,
  `genre_id` INT NOT NULL,
  PRIMARY KEY (`book_id`, `genre_id`),
  INDEX `fk_book_has_genres_genres1_idx` (`genre_id` ASC),
  INDEX `fk_book_has_genres_book1_idx` (`book_id` ASC),
  CONSTRAINT `fk_book_has_genres_book1`
    FOREIGN KEY (`book_id`)
    REFERENCES `Library`.`book` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_book_has_genres_genres1`
    FOREIGN KEY (`genre_id`)
    REFERENCES `Library`.`genre` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Library`.`book_has_reservation`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Library`.`book_has_reservation` (
  `book_id` BIGINT UNSIGNED NOT NULL,
  `reservation_id` BIGINT NOT NULL,
  PRIMARY KEY (`book_id`, `reservation_id`),
  INDEX `fk_book_has_reservation_reservation1_idx` (`reservation_id` ASC),
  INDEX `fk_book_has_reservation_book1_idx` (`book_id` ASC),
  CONSTRAINT `fk_book_has_reservation_book1`
    FOREIGN KEY (`book_id`)
    REFERENCES `Library`.`book` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_book_has_reservation_reservation1`
    FOREIGN KEY (`reservation_id`)
    REFERENCES `Library`.`reservation` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

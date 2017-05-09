-- MySQL Script generated by MySQL Workbench
-- Tue May  9 17:21:15 2017
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema u693453499_turno
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema u693453499_turno
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `u693453499_turno` DEFAULT CHARACTER SET utf8 ;
-- -----------------------------------------------------
-- Schema esmiturno
-- -----------------------------------------------------
USE `u693453499_turno` ;

-- -----------------------------------------------------
-- Table `u693453499_turno`.`emt_address`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `u693453499_turno`.`emt_address` (
  `emt_addres_id` SMALLINT(10) NOT NULL AUTO_INCREMENT,
  `country_code` SMALLINT(3) NULL,
  `country` VARCHAR(45) NULL,
  `region` VARCHAR(45) NULL,
  `area` VARCHAR(45) NULL,
  `locality` VARCHAR(45) NULL,
  `custom` VARCHAR(45) NULL,
  `postal_code` SMALLINT(8) NULL,
  `street_name` VARCHAR(45) NULL,
  `street_number` VARCHAR(45) NULL,
  `tower` VARCHAR(45) NULL,
  `floor` VARCHAR(45) NULL,
  `dpto` VARCHAR(45) NULL,
  `type` VARCHAR(45) NULL,
  `status` VARCHAR(7) NOT NULL DEFAULT 'OK',
  `comments` VARCHAR(255) NULL,
  `create_time` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `modify_time` TIMESTAMP NULL,
  `create_user` VARCHAR(45) NULL,
  `modify_user` VARCHAR(45) NULL,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`emt_addres_id`));


-- -----------------------------------------------------
-- Table `u693453499_turno`.`emt_meetplace`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `u693453499_turno`.`emt_meetplace` (
  `emt_meetplace_id` SMALLINT(10) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NULL,
  `fantasy_name` VARCHAR(255) NULL,
  `status` VARCHAR(7) NOT NULL DEFAULT 'OK',
  `comments` VARCHAR(255) NULL,
  `create_time` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `modify_time` TIMESTAMP NULL,
  `create_user` VARCHAR(45) NULL,
  `modify_user` VARCHAR(45) NULL,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `emt_address_emt_addres_id` SMALLINT(10) NOT NULL,
  PRIMARY KEY (`emt_meetplace_id`, `emt_address_emt_addres_id`),
  INDEX `fk_emt_meetplace_emt_address1_idx` (`emt_address_emt_addres_id` ASC),
  CONSTRAINT `fk_emt_meetplace_emt_address1`
    FOREIGN KEY (`emt_address_emt_addres_id`)
    REFERENCES `u693453499_turno`.`emt_address` (`emt_addres_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Table `u693453499_turno`.`emt_contacts`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `u693453499_turno`.`emt_contacts` (
  `emt_contact_id` SMALLINT(10) NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(45) NOT NULL,
  `email2` VARCHAR(45) NULL,
  `telephone` VARCHAR(45) NULL,
  `intern` VARCHAR(45) NULL,
  `telephone2` VARCHAR(45) NULL,
  `cellphone` VARCHAR(45) NULL,
  `cellphone2` VARCHAR(45) NULL,
  `facebook` VARCHAR(100) NULL,
  `url_site` VARCHAR(45) NULL,
  `status` VARCHAR(7) NOT NULL DEFAULT 'OK',
  `comments` VARCHAR(255) NULL,
  `create_time` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `modify_time` TIMESTAMP NULL,
  `create_user` VARCHAR(45) NULL,
  `modify_user` VARCHAR(45) NULL,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `emt_meetplace_emt_meetplace_id` SMALLINT(10) NULL,
  PRIMARY KEY (`emt_contact_id`),
  UNIQUE INDEX `telephone_UNIQUE` (`telephone` ASC),
  INDEX `fk_emt_contacts_emt_meetplace1_idx` (`emt_meetplace_emt_meetplace_id` ASC),
  CONSTRAINT `fk_emt_contacts_emt_meetplace1`
    FOREIGN KEY (`emt_meetplace_emt_meetplace_id`)
    REFERENCES `u693453499_turno`.`emt_meetplace` (`emt_meetplace_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Table `u693453499_turno`.`emt_customers`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `u693453499_turno`.`emt_customers` (
  `emt_customer_id` SMALLINT(10) NOT NULL AUTO_INCREMENT,
  `dots` INT NOT NULL,
  `status` VARCHAR(7) NOT NULL DEFAULT 'OK',
  `comments` VARCHAR(255) NULL,
  `create_time` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `modify_time` TIMESTAMP NULL,
  `create_user` VARCHAR(45) NULL,
  `modify_user` VARCHAR(45) NULL,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `emt_users_emt_user_id` SMALLINT(10) NOT NULL,
  PRIMARY KEY (`emt_customer_id`, `emt_users_emt_user_id`));


-- -----------------------------------------------------
-- Table `u693453499_turno`.`emt_providers`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `u693453499_turno`.`emt_providers` (
  `emt_provider_id` SMALLINT(10) NOT NULL,
  `dots` VARCHAR(45) NULL,
  `status` VARCHAR(7) NOT NULL DEFAULT 'OK',
  `comments` VARCHAR(255) NULL,
  `create_time` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `modify_time` TIMESTAMP NULL,
  `create_user` VARCHAR(45) NULL,
  `modify_user` VARCHAR(45) NULL,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`emt_provider_id`));


-- -----------------------------------------------------
-- Table `u693453499_turno`.`emt_users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `u693453499_turno`.`emt_users` (
  `emt_user_id` SMALLINT(10) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(45) NULL,
  `password` VARCHAR(45) NULL,
  `status` VARCHAR(7) NOT NULL DEFAULT 'OK',
  `comments` VARCHAR(255) NULL,
  `create_time` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `modify_time` TIMESTAMP NULL,
  `create_user` VARCHAR(45) NULL,
  `modify_user` VARCHAR(45) NULL,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `emt_customers_emt_customer_id` SMALLINT(10) NULL,
  `emt_providers_emt_provider_id` SMALLINT(10) NULL,
  PRIMARY KEY (`emt_user_id`, `emt_customers_emt_customer_id`, `emt_providers_emt_provider_id`),
  INDEX `fk_emt_users_emt_customers1_idx` (`emt_customers_emt_customer_id` ASC),
  INDEX `fk_emt_users_emt_providers1_idx` (`emt_providers_emt_provider_id` ASC),
  CONSTRAINT `fk_emt_users_emt_customers1`
    FOREIGN KEY (`emt_customers_emt_customer_id`)
    REFERENCES `u693453499_turno`.`emt_customers` (`emt_customer_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_emt_users_emt_providers1`
    FOREIGN KEY (`emt_providers_emt_provider_id`)
    REFERENCES `u693453499_turno`.`emt_providers` (`emt_provider_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Table `u693453499_turno`.`emt_persons`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `u693453499_turno`.`emt_persons` (
  `emt_person_id` SMALLINT(10) NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR(45) NULL,
  `last_name` VARCHAR(45) NULL,
  `document_number` DECIMAL(8) NOT NULL,
  `document_text` VARCHAR(45) NULL,
  `document_type` VARCHAR(10) NOT NULL,
  `nacionality` VARCHAR(45) NULL,
  `gender` VARCHAR(45) NULL,
  `birthday` DATE NULL,
  `status` VARCHAR(7) NOT NULL DEFAULT 'OK',
  `comments` VARCHAR(255) NULL,
  `create_time` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `modify_time` TIMESTAMP NULL,
  `create_user` VARCHAR(45) NULL,
  `modify_user` VARCHAR(45) NULL,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `emt_address_emt_addres_id` SMALLINT(10) NOT NULL,
  `emt_contacts_emt_contact_id` SMALLINT(10) NOT NULL,
  `emt_users_emt_user_id` SMALLINT(10) NOT NULL,
  PRIMARY KEY (`emt_person_id`, `emt_address_emt_addres_id`, `emt_contacts_emt_contact_id`, `emt_users_emt_user_id`),
  UNIQUE INDEX `document_UNIQUE` (`document_number` ASC),
  INDEX `fk_emt_persons_emt_address_idx` (`emt_address_emt_addres_id` ASC),
  INDEX `fk_emt_persons_emt_contacts1_idx` (`emt_contacts_emt_contact_id` ASC),
  INDEX `fk_emt_persons_emt_users1_idx` (`emt_users_emt_user_id` ASC),
  CONSTRAINT `fk_emt_persons_emt_address`
    FOREIGN KEY (`emt_address_emt_addres_id`)
    REFERENCES `u693453499_turno`.`emt_address` (`emt_addres_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_emt_persons_emt_contacts1`
    FOREIGN KEY (`emt_contacts_emt_contact_id`)
    REFERENCES `u693453499_turno`.`emt_contacts` (`emt_contact_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_emt_persons_emt_users1`
    FOREIGN KEY (`emt_users_emt_user_id`)
    REFERENCES `u693453499_turno`.`emt_users` (`emt_user_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Table `u693453499_turno`.`emt_meet`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `u693453499_turno`.`emt_meet` (
  `emt_meet_id` SMALLINT(10) NOT NULL,
  `date` TIMESTAMP NULL,
  `status` VARCHAR(7) NOT NULL DEFAULT 'OK',
  `comments` VARCHAR(255) NULL,
  `create_time` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `modify_time` TIMESTAMP NULL,
  `create_user` VARCHAR(45) NULL,
  `modify_user` VARCHAR(45) NULL,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `emt_customers_emt_customer_id` SMALLINT(10) NOT NULL,
  `emt_providers_emt_provider_id` SMALLINT(10) NOT NULL,
  `emt_meetplace_emt_meetplace_id` SMALLINT(10) NOT NULL,
  PRIMARY KEY (`emt_meet_id`, `emt_customers_emt_customer_id`, `emt_providers_emt_provider_id`, `emt_meetplace_emt_meetplace_id`),
  INDEX `fk_emt_turnos_emt_customers1_idx` (`emt_customers_emt_customer_id` ASC),
  INDEX `fk_emt_turnos_emt_providers1_idx` (`emt_providers_emt_provider_id` ASC),
  INDEX `fk_emt_meet_emt_meetplace1_idx` (`emt_meetplace_emt_meetplace_id` ASC),
  CONSTRAINT `fk_emt_turnos_emt_customers1`
    FOREIGN KEY (`emt_customers_emt_customer_id`)
    REFERENCES `u693453499_turno`.`emt_customers` (`emt_customer_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_emt_turnos_emt_providers1`
    FOREIGN KEY (`emt_providers_emt_provider_id`)
    REFERENCES `u693453499_turno`.`emt_providers` (`emt_provider_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_emt_meet_emt_meetplace1`
    FOREIGN KEY (`emt_meetplace_emt_meetplace_id`)
    REFERENCES `u693453499_turno`.`emt_meetplace` (`emt_meetplace_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

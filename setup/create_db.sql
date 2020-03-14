-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema security
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema security
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `security` DEFAULT CHARACTER SET utf8 ;
USE `security` ;

-- -----------------------------------------------------
-- Table `security`.`User`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `security`.`User` ;

CREATE TABLE IF NOT EXISTS `security`.`User` (
  `username` VARCHAR(50) NOT NULL,
  `password` VARCHAR(80) NULL,
  `firstname` VARCHAR(50) NOT NULL,
  `email` NVARCHAR(254) NULL,
  `disabled` TINYINT NULL,
  PRIMARY KEY (`username`),
  UNIQUE INDEX `userid_UNIQUE` (`username` ASC) VISIBLE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `security`.`Permission`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `security`.`Permission` ;

CREATE TABLE IF NOT EXISTS `security`.`Permission` (
  `permission` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`permission`),
  UNIQUE INDEX `name_UNIQUE` (`permission` ASC) VISIBLE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `security`.`Role`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `security`.`Role` ;

CREATE TABLE IF NOT EXISTS `security`.`Role` (
  `role` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`role`),
  UNIQUE INDEX `role_UNIQUE` (`role` ASC) VISIBLE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `security`.`RolePermission`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `security`.`RolePermission` ;

CREATE TABLE IF NOT EXISTS `security`.`RolePermission` (
  `role` VARCHAR(50) NOT NULL,
  `permission` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`role`, `permission`),
  INDEX `FK_PERMISSION_idx` (`permission` ASC) VISIBLE,
  CONSTRAINT `FK_ROLEPERMISSION_ROLE`
    FOREIGN KEY (`role`)
    REFERENCES `security`.`Role` (`role`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `FK_ROLEPERMISSION_PERMISSION`
    FOREIGN KEY (`permission`)
    REFERENCES `security`.`Permission` (`permission`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `security`.`UserRole`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `security`.`UserRole` ;

CREATE TABLE IF NOT EXISTS `security`.`UserRole` (
  `username` VARCHAR(50) NOT NULL,
  `role` VARCHAR(50) NOT NULL,
  INDEX `FK_ROLE_idx` (`role` ASC) VISIBLE,
  PRIMARY KEY (`role`, `username`),
  CONSTRAINT `FK_USERROLE_USER`
    FOREIGN KEY (`username`)
    REFERENCES `security`.`User` (`username`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `FK_USERROLE_ROLE`
    FOREIGN KEY (`role`)
    REFERENCES `security`.`Role` (`role`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `security`.`User`
-- -----------------------------------------------------
START TRANSACTION;
USE `security`;
INSERT INTO `security`.`User` (`username`, `password`, `firstname`, `email`, `disabled`) VALUES ('admin', 'WelcomeAdmin01', 'Administrator', 'admin@voorbeeld.local', 0);
INSERT INTO `security`.`User` (`username`, `password`, `firstname`, `email`, `disabled`) VALUES ('helpdesk', 'WelcomeHelpdesk01', 'Helpdesk', 'helpdesk@voorbeeld.local', 0);
INSERT INTO `security`.`User` (`username`, `password`, `firstname`, `email`, `disabled`) VALUES ('user', 'WelcomeUser01', 'User', 'user@voorbeeld.local', 0);
INSERT INTO `security`.`User` (`username`, `password`, `firstname`, `email`, `disabled`) VALUES ('disableduser', 'WelcomeDisabledUser01', 'Disabled User', 'disableduser@voorbeeld.local', 1);

COMMIT;


-- -----------------------------------------------------
-- Data for table `security`.`Permission`
-- -----------------------------------------------------
START TRANSACTION;
USE `security`;
INSERT INTO `security`.`Permission` (`permission`) VALUES ('PERMISSION_CREATE_ACCOUNT');
INSERT INTO `security`.`Permission` (`permission`) VALUES ('PERMISSION_READ_ACCOUNT');
INSERT INTO `security`.`Permission` (`permission`) VALUES ('PERMISSION_UPDATE_ACCOUNT');
INSERT INTO `security`.`Permission` (`permission`) VALUES ('PERMISSION_DELETE_ACCOUNT');
INSERT INTO `security`.`Permission` (`permission`) VALUES ('PERMISSION_ARCHIVE_ACCOUNT');
INSERT INTO `security`.`Permission` (`permission`) VALUES ('PERMISSION_RESET_PASSWORD');
INSERT INTO `security`.`Permission` (`permission`) VALUES ('PERMISSION_RESET_TOTP');

COMMIT;


-- -----------------------------------------------------
-- Data for table `security`.`Role`
-- -----------------------------------------------------
START TRANSACTION;
USE `security`;
INSERT INTO `security`.`Role` (`role`) VALUES ('Helpdesk');
INSERT INTO `security`.`Role` (`role`) VALUES ('Administrator');

COMMIT;


-- -----------------------------------------------------
-- Data for table `security`.`RolePermission`
-- -----------------------------------------------------
START TRANSACTION;
USE `security`;
INSERT INTO `security`.`RolePermission` (`role`, `permission`) VALUES ('Administrator', 'PERMISSION_CREATE_ACCOUNT');
INSERT INTO `security`.`RolePermission` (`role`, `permission`) VALUES ('Administrator', 'PERMISSION_READ_ACCOUNT');
INSERT INTO `security`.`RolePermission` (`role`, `permission`) VALUES ('Administrator', 'PERMISSION_UPDATE_ACCOUNT');
INSERT INTO `security`.`RolePermission` (`role`, `permission`) VALUES ('Administrator', 'PERMISSION_DELETE_ACCOUNT');
INSERT INTO `security`.`RolePermission` (`role`, `permission`) VALUES ('Administrator', 'PERMISSION_ARCHIVE_ACCOUNT');
INSERT INTO `security`.`RolePermission` (`role`, `permission`) VALUES ('Helpdesk', 'PERMISSION_RESET_PASSWORD');
INSERT INTO `security`.`RolePermission` (`role`, `permission`) VALUES ('Helpdesk', 'PERMISSION_RESET_TOTP');

COMMIT;


-- -----------------------------------------------------
-- Data for table `security`.`UserRole`
-- -----------------------------------------------------
START TRANSACTION;
USE `security`;
INSERT INTO `security`.`UserRole` (`username`, `role`) VALUES ('admin', 'Administrator');
INSERT INTO `security`.`UserRole` (`username`, `role`) VALUES ('helpdesk', 'Helpdesk');

COMMIT;


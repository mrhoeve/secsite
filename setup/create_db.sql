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
-- Table `security`.`Role`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `security`.`Role` ;

CREATE TABLE IF NOT EXISTS `security`.`Role` (
  `role` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`role`),
  UNIQUE INDEX `role_UNIQUE` (`role` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `security`.`User`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `security`.`User` ;

CREATE TABLE IF NOT EXISTS `security`.`User` (
  `username` VARCHAR(50) NOT NULL,
  `password` VARCHAR(80) NULL,
  `fasecret` VARCHAR(32) NULL,
  `firstname` VARCHAR(50) NOT NULL,
  `email` NVARCHAR(254) NULL,
  `role` VARCHAR(50) NULL,
  `changepwonl` TINYINT NULL,
  `disabled` TINYINT NULL,
  `timestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`username`),
  UNIQUE INDEX `userid_UNIQUE` (`username` ASC),
  INDEX `FK_USER_ROLE_idx` (`role` ASC),
  CONSTRAINT `FK_USER_ROLE`
    FOREIGN KEY (`role`)
    REFERENCES `security`.`Role` (`role`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `security`.`Permission`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `security`.`Permission` ;

CREATE TABLE IF NOT EXISTS `security`.`Permission` (
  `permission` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`permission`),
  UNIQUE INDEX `name_UNIQUE` (`permission` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `security`.`RolePermission`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `security`.`RolePermission` ;

CREATE TABLE IF NOT EXISTS `security`.`RolePermission` (
  `role` VARCHAR(50) NOT NULL,
  `permission` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`role`, `permission`),
  INDEX `FK_PERMISSION_idx` (`permission` ASC),
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


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `security`.`Role`
-- -----------------------------------------------------
START TRANSACTION;
USE `security`;
INSERT INTO `security`.`Role` (`role`) VALUES ('Helpdesk');
INSERT INTO `security`.`Role` (`role`) VALUES ('Administrator');
INSERT INTO `security`.`Role` (`role`) VALUES ('Root');

COMMIT;


-- -----------------------------------------------------
-- Data for table `security`.`User`
-- -----------------------------------------------------
START TRANSACTION;
USE `security`;
INSERT INTO `security`.`User` (`username`, `password`, `fasecret`, `firstname`, `email`, `role`, `changepwonl`, `disabled`, `timestamp`) VALUES ('admin', 'WelcomeAdmin01', NULL, 'Administrator', 'admin@voorbeeld.local', 'Administrator', 1, 0, NULL);
INSERT INTO `security`.`User` (`username`, `password`, `fasecret`, `firstname`, `email`, `role`, `changepwonl`, `disabled`, `timestamp`) VALUES ('helpdesk', 'WelcomeHelpdesk01', NULL, 'Helpdesk', 'helpdesk@voorbeeld.local', 'Helpdesk', 1, 0, NULL);
INSERT INTO `security`.`User` (`username`, `password`, `fasecret`, `firstname`, `email`, `role`, `changepwonl`, `disabled`, `timestamp`) VALUES ('user', 'WelcomeUser01', NULL, 'User', 'user@voorbeeld.local', NULL, 1, 0, NULL);
INSERT INTO `security`.`User` (`username`, `password`, `fasecret`, `firstname`, `email`, `role`, `changepwonl`, `disabled`, `timestamp`) VALUES ('disableduser', 'WelcomeDisabledUser01', NULL, 'Disabled User', 'disableduser@voorbeeld.local', NULL, 1, 1, NULL);
INSERT INTO `security`.`User` (`username`, `password`, `fasecret`, `firstname`, `email`, `role`, `changepwonl`, `disabled`, `timestamp`) VALUES ('root', 'WelcomeRoot01', NULL, 'Root User', 'root@voorbeeld.local', 'Root', 0, 0, NULL);

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
INSERT INTO `security`.`RolePermission` (`role`, `permission`) VALUES ('Root', 'PERMISSION_CREATE_ACCOUNT');
INSERT INTO `security`.`RolePermission` (`role`, `permission`) VALUES ('Root', 'PERMISSION_READ_ACCOUNT');
INSERT INTO `security`.`RolePermission` (`role`, `permission`) VALUES ('Root', 'PERMISSION_UPDATE_ACCOUNT');
INSERT INTO `security`.`RolePermission` (`role`, `permission`) VALUES ('Root', 'PERMISSION_DELETE_ACCOUNT');
INSERT INTO `security`.`RolePermission` (`role`, `permission`) VALUES ('Root', 'PERMISSION_ARCHIVE_ACCOUNT');
INSERT INTO `security`.`RolePermission` (`role`, `permission`) VALUES ('Root', 'PERMISSION_RESET_PASSWORD');
INSERT INTO `security`.`RolePermission` (`role`, `permission`) VALUES ('Root', 'PERMISSION_RESET_TOTP');

COMMIT;


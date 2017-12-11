-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema mtd
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema mtd
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `mtd` DEFAULT CHARACTER SET latin1 ;
USE `mtd` ;

-- -----------------------------------------------------
-- Table `mtd`.`attackhistory`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mtd`.`attackhistory` ;

CREATE TABLE IF NOT EXISTS `mtd`.`attackhistory` (
  `attacker_id` VARCHAR(100) NOT NULL,
  `source_IP` VARCHAR(20) NULL DEFAULT NULL,
  `destination_IP` VARCHAR(20) NULL DEFAULT NULL,
  `attackStartTime` DATETIME NULL DEFAULT NULL,
  `attackStopTime` DATETIME NULL DEFAULT NULL,
  `numberOfPackets` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`attacker_id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `mtd`.`blacklist`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mtd`.`blacklist` ;

CREATE TABLE IF NOT EXISTS `mtd`.`blacklist` (
  `ipAddress` VARCHAR(20) NULL DEFAULT NULL,
  `macAddress` VARCHAR(20) NULL DEFAULT NULL,
  `blacklistedOn` DATETIME NULL DEFAULT NULL)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `mtd`.`logs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mtd`.`logs` ;

CREATE TABLE IF NOT EXISTS `mtd`.`logs` (
  `switch_id` BIGINT(20) UNSIGNED NOT NULL,
  `port_id` INT(10) UNSIGNED NOT NULL,
  `timestamp` DATETIME NOT NULL,
  `rx_packets` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
  `delta_rx_packets` INT(10) UNSIGNED NULL DEFAULT '0',
  `tx_packets` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
  `delta_tx_packets` INT(10) UNSIGNED NULL DEFAULT '0',
  `rx_bytes` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
  `delta_rx_bytes` INT(10) UNSIGNED NULL DEFAULT '0',
  `tx_bytes` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
  `delta_tx_bytes` INT(10) UNSIGNED NULL DEFAULT '0',
  `rx_dropped` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
  `tx_dropped` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
  `rx_errors` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
  `tx_errors` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
  `rx_fram_err` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
  `rx_over_err` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
  `rx_crc_err` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
  `collisions` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`switch_id`, `port_id`, `timestamp`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `mtd`.`packet_logs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mtd`.`packet_logs` ;

CREATE TABLE IF NOT EXISTS `mtd`.`packet_logs` (
  `switch_id` BIGINT(20) UNSIGNED NOT NULL,
  `port_id` INT(10) UNSIGNED NOT NULL,
  `timestamp` DATETIME NOT NULL,
  `ethType` SMALLINT(5) UNSIGNED NULL DEFAULT NULL,
  `vlan` VARCHAR(45) NULL DEFAULT NULL,
  `vlanPcp` VARCHAR(45) NULL DEFAULT NULL,
  `hw_src` VARCHAR(17) NULL DEFAULT NULL,
  `hw_dst` VARCHAR(17) NULL DEFAULT NULL,
  `ip4Src` VARCHAR(15) NULL DEFAULT NULL,
  `ip4Dst` VARCHAR(15) NULL DEFAULT NULL,
  `ipProto` TINYINT(3) UNSIGNED NULL DEFAULT NULL,
  `tcpSrcPort` SMALLINT(5) UNSIGNED NULL DEFAULT NULL,
  `tcpDstPort` SMALLINT(5) UNSIGNED NULL DEFAULT NULL)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `mtd`.`qvm`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mtd`.`qvm` ;

CREATE TABLE IF NOT EXISTS `mtd`.`qvm` (
  `qvmUID` VARCHAR(100) NOT NULL,
  `qvmName` VARCHAR(255) NULL DEFAULT NULL,
  `qvmIP` VARCHAR(20) NULL DEFAULT NULL,
  `qvmStartTime` DATETIME NULL DEFAULT NULL,
  `numberOfAttackers` INT(11) NULL DEFAULT NULL,
  `currentlyActive` TINYINT(4) NULL DEFAULT NULL,
  PRIMARY KEY (`qvmUID`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `mtd`.`devices`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mtd`.`devices` ;

CREATE TABLE IF NOT EXISTS `mtd`.`devices` (
  `deviceID` INT(11) UNSIGNED NOT NULL,
  `name` VARCHAR(45) NOT NULL,
  `ipv4` VARCHAR(15) NOT NULL,
  `ipv6` VARCHAR(45) NULL,
  `MAC` VARCHAR(45) NULL,
  PRIMARY KEY (`deviceID`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mtd`.`policies`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mtd`.`policies` ;

CREATE TABLE IF NOT EXISTS `mtd`.`policies` (
  `deviceID` INT(11) UNSIGNED NOT NULL,
  `policyID` INT(11) UNSIGNED NOT NULL,
  `policy` MEDIUMTEXT NOT NULL,
  `loaded` TINYINT(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`deviceID`, `policyID`),
  CONSTRAINT `policies_TO_devices`
    FOREIGN KEY (`deviceID`)
    REFERENCES `mtd`.`devices` (`deviceID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `mtd`.`servers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mtd`.`servers` ;

CREATE TABLE IF NOT EXISTS `mtd`.`servers` (
  `serverUID` VARCHAR(100) NOT NULL,
  `serverName` VARCHAR(255) NOT NULL,
  `serverIP` VARCHAR(20) NOT NULL,
  `serverCreatedOn` DATETIME NULL DEFAULT NULL,
  `reputationValue` DOUBLE NULL DEFAULT NULL,
  `bidValue` DOUBLE NULL DEFAULT NULL,
  PRIMARY KEY (`serverUID`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `mtd`.`switches`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mtd`.`switches` ;

CREATE TABLE IF NOT EXISTS `mtd`.`switches` (
  `switchID` BIGINT(20) UNSIGNED NOT NULL,
  `name` VARCHAR(45) NOT NULL,
  `totalPorts` SMALLINT(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`switchID`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `mtd`.`switch_devices`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mtd`.`switch_devices` ;

CREATE TABLE IF NOT EXISTS `mtd`.`switch_devices` (
  `switchID` BIGINT(20) UNSIGNED NOT NULL,
  `deviceID` INT(11) UNSIGNED NOT NULL,
  `port` INT(11) UNSIGNED NULL DEFAULT 0,
  PRIMARY KEY (`switchID`, `deviceID`),
  INDEX `switch_devices_TO_devices_idx` (`deviceID` ASC),
  CONSTRAINT `switch_devices_TO_switches`
    FOREIGN KEY (`switchID`)
    REFERENCES `mtd`.`switches` (`switchID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `switch_devices_TO_devices`
    FOREIGN KEY (`deviceID`)
    REFERENCES `mtd`.`devices` (`deviceID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `mtd`.`usermigration`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mtd`.`usermigration` ;

CREATE TABLE IF NOT EXISTS `mtd`.`usermigration` (
  `userMigrationUID` VARCHAR(100) NOT NULL,
  `userIP` VARCHAR(20) NULL DEFAULT NULL,
  `originalServerIP` VARCHAR(20) NULL DEFAULT NULL,
  `migratedServerIP` VARCHAR(20) NULL DEFAULT NULL,
  `migrationStartTime` DATETIME NULL DEFAULT NULL,
  `migrationStopTime` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`userMigrationUID`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `mtd`.`users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mtd`.`users` ;

CREATE TABLE IF NOT EXISTS `mtd`.`users` (
  `userUID` VARCHAR(100) NOT NULL,
  `username` VARCHAR(100) NULL DEFAULT NULL,
  `ipAddress` VARCHAR(20) NULL DEFAULT NULL,
  `connectionStartTime` DATETIME NULL DEFAULT NULL,
  `connectionStopTime` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`userUID`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `mtd`.`devices`
-- -----------------------------------------------------
START TRANSACTION;
USE `mtd`;
INSERT INTO `mtd`.`devices` (`deviceID`, `name`, `ipv4`, `ipv6`, `MAC`) VALUES (1, 'user1', '10.0.0.5', NULL, NULL);
INSERT INTO `mtd`.`devices` (`deviceID`, `name`, `ipv4`, `ipv6`, `MAC`) VALUES (2, 'user2', '10.0.0.6', NULL, NULL);
INSERT INTO `mtd`.`devices` (`deviceID`, `name`, `ipv4`, `ipv6`, `MAC`) VALUES (3, 'attacker1', '10.0.0.7', NULL, NULL);
INSERT INTO `mtd`.`devices` (`deviceID`, `name`, `ipv4`, `ipv6`, `MAC`) VALUES (4, 'attacker2', '10.0.0.8', NULL, NULL);
INSERT INTO `mtd`.`devices` (`deviceID`, `name`, `ipv4`, `ipv6`, `MAC`) VALUES (5, 'attacker3', '10.0.0.9', NULL, NULL);
INSERT INTO `mtd`.`devices` (`deviceID`, `name`, `ipv4`, `ipv6`, `MAC`) VALUES (6, 'qvm', '10.0.0.4', NULL, NULL);
INSERT INTO `mtd`.`devices` (`deviceID`, `name`, `ipv4`, `ipv6`, `MAC`) VALUES (7, 'server1', '10.0.0.1', NULL, NULL);
INSERT INTO `mtd`.`devices` (`deviceID`, `name`, `ipv4`, `ipv6`, `MAC`) VALUES (8, 'server2', '10.0.0.2', NULL, NULL);
INSERT INTO `mtd`.`devices` (`deviceID`, `name`, `ipv4`, `ipv6`, `MAC`) VALUES (9, 'server3', '10.0.0.3', NULL, NULL);

COMMIT;


-- -----------------------------------------------------
-- Data for table `mtd`.`policies`
-- -----------------------------------------------------
START TRANSACTION;
USE `mtd`;
INSERT INTO `mtd`.`policies` (`deviceID`, `policyID`, `policy`, `loaded`) VALUES (1, 1, 'Filter(SwitchEq(51570677359425) & IP4DstEq(\"10.0.0.5\")) >> SetPort(6)', 0);
INSERT INTO `mtd`.`policies` (`deviceID`, `policyID`, `policy`, `loaded`) VALUES (2, 1, 'Filter(SwitchEq(51570677359425) & IP4DstEq(\"10.0.0.7\")) >> SetPort(2)', 0);

COMMIT;


-- -----------------------------------------------------
-- Data for table `mtd`.`switches`
-- -----------------------------------------------------
START TRANSACTION;
USE `mtd`;
INSERT INTO `mtd`.`switches` (`switchID`, `name`, `totalPorts`) VALUES (99947915534402, 'root-switch', 4);
INSERT INTO `mtd`.`switches` (`switchID`, `name`, `totalPorts`) VALUES (51570677359425, 'slave-switch', 7);

COMMIT;


-- -----------------------------------------------------
-- Data for table `mtd`.`switch_devices`
-- -----------------------------------------------------
START TRANSACTION;
USE `mtd`;
INSERT INTO `mtd`.`switch_devices` (`switchID`, `deviceID`, `port`) VALUES (99947915534402, 7, 2);
INSERT INTO `mtd`.`switch_devices` (`switchID`, `deviceID`, `port`) VALUES (99947915534402, 8, 1);
INSERT INTO `mtd`.`switch_devices` (`switchID`, `deviceID`, `port`) VALUES (99947915534402, 9, 4);
INSERT INTO `mtd`.`switch_devices` (`switchID`, `deviceID`, `port`) VALUES (51570677359425, 1, 6);
INSERT INTO `mtd`.`switch_devices` (`switchID`, `deviceID`, `port`) VALUES (51570677359425, 2, 5);
INSERT INTO `mtd`.`switch_devices` (`switchID`, `deviceID`, `port`) VALUES (51570677359425, 3, 2);
INSERT INTO `mtd`.`switch_devices` (`switchID`, `deviceID`, `port`) VALUES (51570677359425, 4, 1);
INSERT INTO `mtd`.`switch_devices` (`switchID`, `deviceID`, `port`) VALUES (51570677359425, 5, 7);
INSERT INTO `mtd`.`switch_devices` (`switchID`, `deviceID`, `port`) VALUES (51570677359425, 6, 3);

COMMIT;


-- MySQL dump 10.13  Distrib 5.7.9, for Win64 (x86_64)
--
-- Host: localhost    Database: mtd
-- ------------------------------------------------------
-- Server version	5.7.20-0ubuntu0.16.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `attackhistory`
--

DROP TABLE IF EXISTS `attackhistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attackhistory` (
  `attacker_id` varchar(100) NOT NULL,
  `source_IP` varchar(20) DEFAULT NULL,
  `destination_IP` varchar(20) DEFAULT NULL,
  `attackStartTime` datetime DEFAULT NULL,
  `attackStopTime` datetime DEFAULT NULL,
  `numberOfPackets` int(11) DEFAULT NULL,
  PRIMARY KEY (`attacker_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `blacklist`
--

DROP TABLE IF EXISTS `blacklist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blacklist` (
  `ipAddress` varchar(20) DEFAULT NULL,
  `macAddress` varchar(20) DEFAULT NULL,
  `blacklistedOn` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deviceType`
--

DROP TABLE IF EXISTS `deviceType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deviceType` (
  `type` tinyint(4) NOT NULL,
  `name` varchar(45) NOT NULL DEFAULT 'defaultType',
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `devices`
--

DROP TABLE IF EXISTS `devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `devices` (
  `deviceID` int(11) unsigned NOT NULL,
  `name` varchar(45) NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ipv4` varchar(15) DEFAULT NULL,
  `ipv6` varchar(45) DEFAULT NULL,
  `mac` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`deviceID`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `login`
--

DROP TABLE IF EXISTS `login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login` (
  `adminUID` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `passwd` varchar(255) DEFAULT NULL,
  `salt` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`adminUID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logs` (
  `switch_id` bigint(20) unsigned NOT NULL,
  `port_id` int(10) unsigned NOT NULL,
  `timestamp` datetime NOT NULL,
  `rx_packets` bigint(20) unsigned NOT NULL DEFAULT '0',
  `delta_rx_packets` int(10) unsigned DEFAULT '0',
  `tx_packets` bigint(20) unsigned NOT NULL DEFAULT '0',
  `delta_tx_packets` int(10) unsigned DEFAULT '0',
  `rx_bytes` bigint(20) unsigned NOT NULL DEFAULT '0',
  `delta_rx_bytes` int(10) unsigned DEFAULT '0',
  `tx_bytes` bigint(20) unsigned NOT NULL DEFAULT '0',
  `delta_tx_bytes` int(10) unsigned DEFAULT '0',
  `rx_dropped` bigint(20) unsigned NOT NULL DEFAULT '0',
  `tx_dropped` bigint(20) unsigned NOT NULL DEFAULT '0',
  `rx_errors` bigint(20) unsigned NOT NULL DEFAULT '0',
  `tx_errors` bigint(20) unsigned NOT NULL DEFAULT '0',
  `rx_fram_err` bigint(20) unsigned NOT NULL DEFAULT '0',
  `rx_over_err` bigint(20) unsigned NOT NULL DEFAULT '0',
  `rx_crc_err` bigint(20) unsigned NOT NULL DEFAULT '0',
  `collisions` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`switch_id`,`port_id`,`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `packet_logs`
--

DROP TABLE IF EXISTS `packet_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `packet_logs` (
  `switch_id` bigint(20) unsigned NOT NULL,
  `trace_id` int(10) unsigned NOT NULL,
  `frame_number` bigint(20) unsigned NOT NULL,
  `frame_time` bigint(20) unsigned NOT NULL,
  `frame_time_relative` float NOT NULL DEFAULT '0',
  `frame_protocols` mediumtext NOT NULL,
  `frame_len` int(11) unsigned NOT NULL,
  `eth_src` varchar(17) DEFAULT NULL,
  `eth_dst` varchar(17) DEFAULT NULL,
  `eth_type` varchar(10) DEFAULT NULL,
  `ip_proto` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ip_src` varchar(15) DEFAULT NULL,
  `ip_dst` varchar(15) DEFAULT NULL,
  `tcp_srcport` smallint(5) unsigned NOT NULL,
  `tcp_dstport` smallint(5) unsigned NOT NULL DEFAULT '0',
  `udp_srcport` smallint(5) unsigned NOT NULL DEFAULT '0',
  `udp_dstport` smallint(5) unsigned NOT NULL DEFAULT '0',
  `vlan` varchar(45) DEFAULT NULL,
  `vlanPcp` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`switch_id`,`trace_id`,`frame_number`),
  KEY `index_ip_src` (`ip_src`),
  KEY `index_ip_dst` (`ip_dst`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `policies`
--

DROP TABLE IF EXISTS `policies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `policies` (
  `policyID` varchar(36) NOT NULL,
  `deviceID` int(11) unsigned NOT NULL,
  `policy` mediumtext NOT NULL,
  `loaded` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`policyID`),
  KEY `policies_TO_devices` (`deviceID`),
  CONSTRAINT `policies_TO_devices` FOREIGN KEY (`deviceID`) REFERENCES `devices` (`deviceID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `qvm`
--

DROP TABLE IF EXISTS `qvm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `qvm` (
  `qvmUID` varchar(100) NOT NULL,
  `qvmName` varchar(255) DEFAULT NULL,
  `qvmIP` varchar(20) DEFAULT NULL,
  `qvmStartTime` datetime DEFAULT NULL,
  `numberOfAttackers` int(11) DEFAULT NULL,
  `currentlyActive` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`qvmUID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rules`
--

DROP TABLE IF EXISTS `rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rules` (
  `rule` mediumtext NOT NULL,
  `loaded` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `servers`
--

DROP TABLE IF EXISTS `servers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `servers` (
  `serverUID` varchar(100) NOT NULL,
  `serverName` varchar(255) NOT NULL,
  `serverIP` varchar(20) NOT NULL,
  `serverCreatedOn` datetime DEFAULT NULL,
  `reputationValue` double DEFAULT NULL,
  `bidValue` double DEFAULT NULL,
  PRIMARY KEY (`serverUID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `simple_policies`
--

DROP TABLE IF EXISTS `simple_policies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `simple_policies` (
  `policyID` varchar(36) NOT NULL,
  `deviceSrcID` int(11) DEFAULT NULL,
  `deviceDstID` int(11) DEFAULT NULL,
  `loaded` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`policyID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `suspiciousness_scores`
--

DROP TABLE IF EXISTS `suspiciousness_scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suspiciousness_scores` (
  `name` varchar(45) NOT NULL,
  `traceID` int(11) NOT NULL,
  `score` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`name`,`traceID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `suspiciousness_scores_by_time`
--

DROP TABLE IF EXISTS `suspiciousness_scores_by_time`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suspiciousness_scores_by_time` (
  `frame_time` bigint(20) NOT NULL,
  `score` double DEFAULT '0',
  PRIMARY KEY (`frame_time`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `switch_devices`
--

DROP TABLE IF EXISTS `switch_devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `switch_devices` (
  `switchID` bigint(20) unsigned NOT NULL,
  `deviceID` int(11) unsigned NOT NULL,
  `port` int(11) unsigned NOT NULL,
  PRIMARY KEY (`switchID`,`deviceID`,`port`),
  KEY `switch_devices_TO_devices_idx` (`deviceID`),
  CONSTRAINT `switch_devices_TO_devices` FOREIGN KEY (`deviceID`) REFERENCES `devices` (`deviceID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `switch_devices_TO_switches` FOREIGN KEY (`switchID`) REFERENCES `switches` (`switchID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `switches`
--

DROP TABLE IF EXISTS `switches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `switches` (
  `switchID` bigint(20) unsigned NOT NULL,
  `name` varchar(45) NOT NULL,
  `totalPorts` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`switchID`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usermigration`
--

DROP TABLE IF EXISTS `usermigration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usermigration` (
  `userMigrationUID` varchar(100) NOT NULL,
  `userIP` varchar(20) DEFAULT NULL,
  `originalServerIP` varchar(20) DEFAULT NULL,
  `migratedServerIP` varchar(20) DEFAULT NULL,
  `migrationStartTime` datetime DEFAULT NULL,
  `migrationStopTime` datetime DEFAULT NULL,
  PRIMARY KEY (`userMigrationUID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `userUID` varchar(100) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `ipAddress` varchar(20) DEFAULT NULL,
  `connectionStartTime` datetime DEFAULT NULL,
  `connectionStopTime` datetime DEFAULT NULL,
  PRIMARY KEY (`userUID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `whitelist`
--

DROP TABLE IF EXISTS `whitelist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `whitelist` (
  `ipv4` varchar(15) NOT NULL,
  PRIMARY KEY (`ipv4`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-01-31 23:22:05

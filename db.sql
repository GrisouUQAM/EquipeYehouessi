-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Mar 17 Décembre 2013 à 02:55
-- Version du serveur: 5.6.12-log
-- Version de PHP: 5.4.12


SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";




/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


--
-- Base de données: `grisou`
--
CREATE DATABASE IF NOT EXISTS `grisou` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `grisou`;


-- --------------------------------------------------------


--
-- Structure de la table `comments`
--


CREATE TABLE IF NOT EXISTS `comments` (
  `pageId` int(11) NOT NULL,
  `comment` varchar(200) NOT NULL,
  PRIMARY KEY (`pageId`,`comment`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- --------------------------------------------------------


--
-- Structure de la table `page`
--


CREATE TABLE IF NOT EXISTS `page` (
  `pageId` int(11) NOT NULL,
  `titre` varchar(100) NOT NULL,
  PRIMARY KEY (`pageId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- --------------------------------------------------------


--
-- Structure de la table `talk`
--


CREATE TABLE IF NOT EXISTS `talk` (
  `pageId` int(11) NOT NULL,
  `comment` varchar(100) NOT NULL,
  `ordre` int(11) NOT NULL DEFAULT '0',
  `niveau` int(11) NOT NULL DEFAULT '0',
  `user` varchar(30) NOT NULL,
  PRIMARY KEY (`pageId`,`comment`,`ordre`,`user`),
  KEY `ordre` (`ordre`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- --------------------------------------------------------


--
-- Structure de la table `user`
--


CREATE TABLE IF NOT EXISTS `user` (
  `userId` int(11) NOT NULL,
  `userName` varchar(30) NOT NULL,
  PRIMARY KEY (`userId`),
  UNIQUE KEY `userName` (`userName`),
  KEY `userName_2` (`userName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

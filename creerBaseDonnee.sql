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
--CREATE DATABASE IF NOT EXISTS `grisou` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
--USE `grisou`;



--
-- Structure de la table `discussion`
--


CREATE TABLE IF NOT EXISTS `discussion` (
  `discussionId` int(11) NOT NULL,
  `titre` varchar(100) NOT NULL,
  PRIMARY KEY (`discussionId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



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


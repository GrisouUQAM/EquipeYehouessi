
    
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+00:00';




/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


/*--
-- Base de données: `grisou`
--*/
CREATE DATABASE IF NOT EXISTS `grisou` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `grisou`;



/*--
-- Structure de la table `discussion`
--*/


CREATE TABLE IF NOT EXISTS `discussion` (
  `discussionId` int(11) NOT NULL,
  `titre` varchar(100) NOT NULL,
  PRIMARY KEY (`discussionId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



/*--
-- Structure de la table `user`
--*/


CREATE TABLE IF NOT EXISTS `user` (
  `userId` int(11) NOT NULL,
  `userName` varchar(30) NOT NULL,
  PRIMARY KEY (`userId`),
  UNIQUE KEY `userName` (`userName`),
  KEY `userName_2` (`userName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;    
    


DROP TABLE IF EXISTS `intervenants`;
CREATE TABLE `intervenants` (
`intervenantId` varchar(100) NOT NULL,
`intervenantName` varchar(100) NOT NULL,
`intervenantAuteurArticle` int(11) not null,
PRIMARY KEY (`intervenantId`)
)ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `liens`;

CREATE TABLE `liens`(
`discussionId` int(11) NOT NULL,
`debutLienId` varchar(100) NOT NULL,
`debutLien` varchar(100) NOT NULL,
`finLienId` varchar(100) NOT NULL,
`finLien` varchar(100) NOT NULL,
`poids` int(11) NOT NULL,
`noSection` int(11) NOT NULL,
`noArchive` int(11) NOT NULL,
PRIMARY KEY (`discussionId`,`debutLienId`,`debutLien`,`finLienId`,`finLien`,`noSection`,`noArchive`),
CHECK (debutLien != finLien),
check (debutLien IN (SELECT intervenantId from intervenants)) ,
check (finLien IN (SELECT intervenantId from intervenants))
)ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `centralites`;

CREATE TABLE `centralites`(
`userId` int(11) NOT NULL,
`centraliteDegre` double(20,5) NOT NULL,
`centraliteInter` double(20,5) NOT NULL,
`centraliteProxi` double(20,5) NOT NULL,
PRIMARY KEY (`userId`)
)ENGINE=InnoDB DEFAULT CHARSET=latin1;


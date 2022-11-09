DROP DATABASE IF EXISTS `comprovendo`;
CREATE DATABASE `comprovendo` DEFAULT CHARACTER SET latin1 COLLATE latin1_general_ci;
USE `comprovendo`;




#
# table structure for table 'utenti'
#
DROP TABLE IF EXISTS `utenti`;
CREATE TABLE `utenti` (
  `uid` INT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
  `nick` VARCHAR(16) NOT NULL,
  `pwd` VARCHAR(16) NOT NULL,
  `saldo` INT DEFAULT 0,
  PRIMARY KEY (`uid`)
) ENGINE=INNODB AUTO_INCREMENT=5;
#
# data for table 'utenti'
#
INSERT INTO `utenti` (`uid`, `nick`, `pwd`, `saldo`) VALUES
(1,'marco96', '123456', 9999900),
(2,'gino60', '123456', 100),
(3,'0rt0p3p3', '123456', 200000),
(4,'pwr2019', '123456', 10000);




#
# table structure for table 'prodotti'
#
DROP TABLE IF EXISTS `prodotti`;
CREATE TABLE `prodotti` (
  `pid` INT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
  `vid` INT UNSIGNED NOT NULL,
  `vend` VARCHAR(16) NOT NULL,
  `nome` VARCHAR(64) NOT NULL,
  `prezzo` INT NOT NULL,
  `qty` INT DEFAULT 1,
  PRIMARY KEY (`pid`)
  KEY `fk_prodotti_utenti` (`vid`),
  CONSTRAINT `fk_prodotti_utenti` FOREIGN KEY (`vid`) REFERENCES `utenti` (`uid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
) ENGINE=InnoDB AUTO_INCREMENT=6;
#
# data for table 'prodotti'
#
INSERT INTO `prodotti` (`pid`, `vid`, `vend`, `nome`, `prezzo`, `qty`) VALUES
(1, 1, 'marco96', 'La Arte della Guerra - Sun Tzu', 100, 1),
(2, 2, 'gino60', 'I Fratelli Karamazov - Dostoevsky', 150, 1),
(3, 3, '0rt0p3p3', 'Principles of Heredity - Mendel', 300, 20),
(4, 3, '0rt0p3p3', 'Pride and Prejudice - Jane Austen', 150, 20),
(5, 3, '0rt0p3p3', 'Frankenstain - Mary Shelley', 100, 20);




#
# table structure for table 'transazioni'
#
DROP TABLE IF EXISTS `transazioni`;
CREATE TABLE `transazioni` (
  `tid` INT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
  `sid` INT UNSIGNED NOT NULL,
  `did` INT UNSIGNED NOT NULL,
  `src` VARCHAR(16) NOT NULL,
  `dst` VARCHAR(16) NOT NULL,
  `pid` INT UNSIGNED NOT NULL,
  `qty` INT UNSIGNED NOT NULL,
  `importo` INT UNSIGNED NOT NULL,
  `tstamp` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tid`),
  KEY `fk_transazioni_utenti_1` (`sid`),
  KEY `fk_transazioni_utenti_2` (`did`),
  KEY `fk_transazioni_prodotti` (`pid`),
  CONSTRAINT `fk_transazioni_utenti_1` FOREIGN KEY (`sid`) REFERENCES `utenti` (`uid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transazioni_utenti_2` FOREIGN KEY (`did`) REFERENCES `utenti` (`uid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transazioni_prodotti` FOREIGN KEY (`pid`) REFERENCES `prodotti` (`pid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=6;
#
# data for table 'transazioni'
#
INSERT INTO `transazioni` (`tid`, `sid`, `did`, `src`, `dst`, `pid`, `qty`, `importo`) VALUES
(1, 1, 2, 'marco96', 'gino60', 3, 1, 300),
(2, 2, 3, 'gino60', '0rt0p3p3', 3, 1, 300),
(3, 1, 3, 'marco96', '0rt0p3p3', 3, 1, 300),
(4, 2, 3, 'gino60', '0rt0p3p3', 3, 1, 300),
(5, 1, 4, 'marco96', 'pwr2019', 3, 1, 300);




#
# Permessi user: uWeak; pwd: posso_solo_leggere (solo SELECT)
#
GRANT USAGE ON `comprovendo`.* TO 'uWeak'@'%' IDENTIFIED BY PASSWORD '*0FBF5C395B1E6B971E9CBB18F95041B49D0B0947';

GRANT SELECT ON `comprovendo`.* TO 'uWeak'@'%';

#
# Permessi user: uStrong; pwd: SuperPippo!!! (solo SELECT, INSERT, UPDATE)
#
GRANT USAGE ON `comprovendo`.* TO 'uStrong'@'%' IDENTIFIED BY PASSWORD '*400BF58DFE90766AF20296B3D89A670FC66BEAEC';

GRANT SELECT, INSERT, UPDATE ON `comprovendo`.* TO 'uStrong'@'%';

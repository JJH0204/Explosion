DROP DATABASE IF EXISTS `flameDB`;
CREATE DATABASE `flameDB`;

USE flameDB;
DROP TABLE IF EXISTS `ID_info`;
CREATE TABLE `ID_info` (
	`ID` VARCHAR(20) NOT NULL ,
	`PW` VARCHAR(255) NOT NULL ,
	`NICKNAME` VARCHAR(20) NOT NULL,
	`RECOREDE_DATE` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `USER_info`;
CREATE TABLE `USER_info` (
	`ID` VARCHAR(20) NOT NULL,
	`NICKNAME` VARCHAR(20) NOT NULL,
	`SCORE` INT NOT NULL DEFAULT 0,
	`STAGE` INT NOT NULL DEFAULT 0,
	`RECOREDE_DATE` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `CLEARED_STAGE`;
CREATE TABLE `CLEARED_STAGE` (
    `NICKNAME` VARCHAR(20) NOT NULL,
    `ANSWER` INT NOT NULL    
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `LAB_SCORE`;
CREATE TABLE `LAB_SCORE` (
	`ID` INT NOT NULL,
	`SCORE` INT NOT NULL 
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO ID_info (ID, PW, NICKNAME) VALUES ('flame', '$2y$10$iAyr/B348/gdixD4sYSnc.6mkXwo/yQ4/Jf1yG6x2JGZ96w3JVUCi', 'flame');
INSERT INTO USER_info (ID, NICKNAME, SCORE, STAGE) VALUES ('flame', 'flame', 9999, 40);

INSERT INTO ID_info (ID, PW, NICKNAME) VALUES ('flame_admin', '$2y$10$uitUiH8DZ3ekbt16pd5miOZIOWeUeg4vV5fMSsRkk/khsB82mV.J2', 'admin');
INSERT INTO USER_info (ID, NICKNAME, SCORE, STAGE) VALUES ('flame_admin', 'admin', 9999, 40);

INSERT INTO `LAB_SCORE` (ID, SCORE) VALUES (1,50), (2,50), (3,50), (4,50), (5,100), (6,50), (7,50), 
(8,50), (9,50), (10,50), (11,300), (12,200), (13,100), (14,100), (15,300), (16,100), (17,100), (18,100), 
(19,100), (20,100), (21,100), (22,200), (23,100), (24,200), (25,200), (26,100), (27,300), (28,200), (29,200), 
(30,300), (31,200), (32,100), (33,300), (34,100), (35,50), (36,50), (37,300), (38,200), (39,300), (40,200);

INSERT INTO `CLEARED_STAGE` (NICKNAME, ANSWER) VALUES 
    ('flame', 1), ('admin', 1), ('flame', 2), ('admin', 2), ('flame', 3), ('admin', 3), ('flame', 4), ('admin', 4), ('flame', 5), ('admin', 5), 
	('flame', 6), ('admin', 6), ('flame', 7), ('admin', 7), ('flame', 8), ('admin', 8), ('flame', 9), ('admin', 9), ('flame', 10), ('admin', 10), 
	('flame', 11), ('admin', 11), ('flame', 12), ('admin', 12), ('flame', 13), ('admin', 13), ('flame', 14), ('admin', 14), ('flame', 15), ('admin', 15), 
	('flame', 16), ('admin', 16), ('flame', 17), ('admin', 17), ('flame', 18), ('admin', 18), ('flame', 19), ('admin', 19), ('flame', 20), ('admin', 20), 
	('flame', 21), ('admin', 21), ('flame', 22), ('admin', 22), ('flame', 23), ('admin', 23), ('flame', 24), ('admin', 24), ('flame', 25), ('admin', 25), 
	('flame', 26), ('admin', 26), ('flame', 27), ('admin', 27), ('flame', 28), ('admin', 28), ('flame', 29), ('admin', 29), ('flame', 30), ('admin', 30), 
	('flame', 31), ('admin', 31), ('flame', 32), ('admin', 32), ('flame', 33), ('admin', 33), ('flame', 34), ('admin', 34), ('flame', 35), ('admin', 35), 
	('flame', 36), ('admin', 36), ('flame', 37), ('admin', 37), ('flame', 38), ('admin', 38), ('flame', 39), ('admin', 39), ('flame', 40), ('admin', 40);

DROP DATABASE IF EXISTS `userDB`;
CREATE DATABASE `userDB`;
use userDB;

DROP TABLE IF EXISTS `flame`;
CREATE TABLE `flame` (
	`NICKNAME` VARCHAR(50) NOT NULL,
	`FLAG` VARCHAR(100) NOT NULL 
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
	`NICKNAME` VARCHAR(50) NOT NULL,
	`FLAG` VARCHAR(100) NOT NULL 
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `flame` (NICKNAME, FLAG) VALUES ('flame', 'flag{flame_is_real_admin}');
INSERT INTO `admin` (NICKNAME, FLAG) VALUES ('admin', 'flag{admin_is_fake_admin}');

CREATE USER `admin`@`localhost` identified by 'flamerootpassword';
GRANT ALL PRIVILEGES ON *.* TO 'admin'@'localhost';
GRANT CREATE USER ON *.* TO 'admin'@'localhost';
GRANT GRANT OPTION ON *.* TO 'admin'@'localhost';

CREATE USER `flame`@`localhost` identified by 'firewalld';
GRANT SELECT, UPDATE ON `userDB`.`flame` TO 'flame'@'localhost';
CREATE USER `flame_admin`@`localhost` identified by 'firewalld';
GRANT SELECT, UPDATE ON `userDB`.`admin` TO 'flame_admin'@'localhost';

FLUSH PRIVILEGES;

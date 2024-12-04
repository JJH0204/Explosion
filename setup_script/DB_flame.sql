DROP DATABASE IF EXISTS `DB_flame`;
CREATE DATABASE `DB_flame`;
USE DB_flame;

-- Users ID information table
DROP TABLE IF EXISTS `ID_INFO`;
CREATE TABLE `ID_INFO` (
	`id` VARCHAR(20) NOT NULL ,
	`pw` VARCHAR(255) NOT NULL ,
	`nickname` VARCHAR(20) NOT NULL,
	`recorede_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Users game information table
DROP TABLE IF EXISTS `USER_INFO`;
CREATE TABLE `USER_INFO` (
	`id` VARCHAR(20) NOT NULL,
	`score` INT NOT NULL DEFAULT 0,
	`total_cleared_stage` INT NOT NULL DEFAULT 0,
	`recorede_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Users cleared stages tables
DROP TABLE IF EXISTS `CLEARED_STAGE`;
CREATE TABLE `CLEARED_STAGE` (
    `id` VARCHAR(20) NOT NULL,
    `challenge_id` INT NOT NULL,
	`recorede_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP    
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Admin Users ID info (flame, admin)
INSERT INTO `ID_INFO` (id, pw, nickname) VALUES ('flame', '$2y$10$iAyr/B348/gdixD4sYSnc.6mkXwo/yQ4/Jf1yG6x2JGZ96w3JVUCi', 'flame');
INSERT INTO `USER_INFO` (id, score, total_cleared_stage) VALUES ('flame', 9999, 40);

INSERT INTO `ID_INFO` (id, pw, nickname) VALUES ('admin', '$2y$10$uitUiH8DZ3ekbt16pd5miOZIOWeUeg4vV5fMSsRkk/khsB82mV.J2', 'admin');
INSERT INTO `USER_INFO` (id, score, total_cleared_stage) VALUES ('admin', 9999, 40);

-- Admin Users have already cleared all challenges
INSERT INTO `CLEARED_STAGE` (id, challenge_id) VALUES 
    ('flame', 1), ('admin', 1), ('flame', 2), ('admin', 2), ('flame', 3), ('admin', 3), ('flame', 4), ('admin', 4), ('flame', 5), ('admin', 5), 
	('flame', 6), ('admin', 6), ('flame', 7), ('admin', 7), ('flame', 8), ('admin', 8), ('flame', 9), ('admin', 9), ('flame', 10), ('admin', 10), 
	('flame', 11), ('admin', 11), ('flame', 12), ('admin', 12), ('flame', 13), ('admin', 13), ('flame', 14), ('admin', 14), ('flame', 15), ('admin', 15), 
	('flame', 16), ('admin', 16), ('flame', 17), ('admin', 17), ('flame', 18), ('admin', 18), ('flame', 19), ('admin', 19), ('flame', 20), ('admin', 20), 
	('flame', 21), ('admin', 21), ('flame', 22), ('admin', 22), ('flame', 23), ('admin', 23), ('flame', 24), ('admin', 24), ('flame', 25), ('admin', 25), 
	('flame', 26), ('admin', 26), ('flame', 27), ('admin', 27), ('flame', 28), ('admin', 28), ('flame', 29), ('admin', 29), ('flame', 30), ('admin', 30), 
	('flame', 31), ('admin', 31), ('flame', 32), ('admin', 32), ('flame', 33), ('admin', 33), ('flame', 34), ('admin', 34), ('flame', 35), ('admin', 35), 
	('flame', 36), ('admin', 36), ('flame', 37), ('admin', 37), ('flame', 38), ('admin', 38), ('flame', 39), ('admin', 39), ('flame', 40), ('admin', 40);


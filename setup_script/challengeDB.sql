DROP DATABASE IF EXISTS `challengeDB`;
CREATE DATABASE `challengeDB`;
USE challengeDB;

-- Lab Information table
DROP TABLE IF EXISTS `CHALLENGE_info`;
CREATE TABLE `CHALLENGE_info` (
	`ID` INT NOT NULL,
	`SCORE` INT NOT NULL,
    `TITLE` VARCHAR(50) NOT NULL,
    `CATEGORY` VARCHAR(100) NOT NULL,
    `DIFFICULTY` VARCHAR(20) NOT NULL,
    `ANSWER` VARCHAR(50)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `CHALLENGE_info` (ID, SCORE, TITLE, CATEGORY, DIFFICULTY, ANSWER) VALUES 
(1,50,"Basic Wargame","cryptology","basic","flame1answer"), 
(2,50,"HTML source code analysis","cryptology,reversing","basic","FLAG{source_code_analysis}"), 
(3,50,"404 Not Found","cryptology,reversing","basic","FLAG{base64_decode_success}"), 
(4,50,"Admin Portal","CSRF","basic","FLAG{proxy_role_manipulation_success}"), 
(5,100,"FileHub","CSRF","basic","FLAG{proxy_role_manipulation_success}"), 
(6,50,"Reaction Speed Test","CSRF,ETC","basic","FLAG{F4st_R3fl3x_M4st3r}"), 
(7,50,"O Finder Game","reversing","basic","FLAG{find_the_O_success}"), 
(8,50,"Image Change", "reversing", "basic", "FLag{image_change}"), 
(9,50,"Local Storage Modification", "reversing", "basic", "Flag{LocalStorage_Modification}"), 
(10,50,"Server IP Finder", "network", "basic", "flag{find_flame_real_ip}"), 
(11,300,"Lotto Number Guessing", "network,pwnable", "advanced", "FLAG{L0770_PR3D1C71ON_M4573R}"), 
(12,200,"Call Correct Phone Number", "xss", "intermediate", "FLAG{C4ll_M3_M4yB3_B4by}"), 
(13,100,"Buffer Overflow", "pwnable,reversing,cryptology", "beginner", "FLAG{advanced_buffer_overflow}"), 
(14,100,"Number Baseball Game", "ETC", "beginner", "FLAG{number_baseball_success}"), 
(15,300,"Steganography", "pwnable", "advanced", "FLAG{hidden_with_lsb}"), 
(16,100,"SQL Grant", "sql", "beginner", "FLAG{sql_grant_correct}"), 
(17,100,"Snake Game", "ETC", "beginner", "FLAG{snake_master}"), 
(18,100,"Basic Subnetting", "network", "beginner", "FLAG{SUBNETTING_MASTER}"), 
(19,100,"Find Flag", "linux", "beginner", "FLAG{this_is_easy}"), 
(20,100,"Binary Code", "linux", "beginner", "FLAG{ASCIIcode}"), 
(21,100,"Cryptanalysis 1", "cryptology", "beginner", "Flag{Le_flag}"), 
(22,200,"Cryptanalysis 2", "cryptology,pwnable", "intermediate", "Flag{hashing_practice}"), 
(23,100,"Cryptanalysis 3", "windows", "beginner", "Flag{insecure_storage}"), 
(24,200,"Decoding", "cryptology", "intermediate", "FLAG{UU24encode_challenge}"), 
(25,200,"Personal Flag", "ETC", "intermediate", NULL), 
(26,100,"Minesweeper", "ETC", "beginner", "Flag{BOMB_game}"), 
(27,300,"Privilege Escalation", "ETC", "advanced", "flag{questionChallenges27flag}"), 
(28,200,"Reverse Engineering", "ETC", "intermediate", "flag{6763634368616c6c656e6765733238666c6167}"), 
(29,200,"Linux Typing Game", "command,ETC", "intermediate", "flag{4368616c6c656e6765666c6167}"), 
(30,300,"2D JumpKing", "ETC", "advanced", "flag{a8b42ddb120d7a9c291323857118bff4}"), 
(31,200,"Cronjob", "ETC", "intermediate", "FLAG{cron_job_solved}"), 
(32,100,"Malware Analysis", "ETC", "beginner", "FLAG{COMPILE_DATE_CORRECT}"), 
(33,300,"ACL Configuration", "ETC", "advanced", "FLAG{ACL_RULES_CORRECT}"), 
(34,100,"Practice Malware", "ETC", "beginner", "FLAG{M4lw4r3_4n4lys1s_Pr0}"), 
(35,50,"Linux Master", "linux", "basic", "FLAG{linux_master}"), 
(36,50,"Network Admin Pro", "network", "basic", "FLAG{network_admin_pro}"), 
(37,300,"ACL Configuration", "network", "advanced", "flag{ACL_RULES_CORRECT}"), 
(38,200,"Admin Flag", "sql,CSRF", "intermediate", NULL), 
(39,200,"Number Clicking", "pwnable,network,xss", "intermediate", "FLAG{XxXx_h4rd_t0_gu3ss_XxXx_39th_Ch4ll3ng3}"), 
(40,200,"Today's Word Guessing", "ETC", "intermediate", "FLAG{W0rd_V3ct0r_S3m4nt1c_M4st3r}");
DROP TABLE IF EXISTS `Scoreboard`;
CREATE TABLE IF NOT EXISTS Scoreboard (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,          
    game_id INT NOT NULL,                   
    score INT NOT NULL DEFAULT 0,            
    stage INT NOT NULL DEFAULT 1,            
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
    UNIQUE KEY unique_user_game (username, game_id) -- username과 game_id 
);

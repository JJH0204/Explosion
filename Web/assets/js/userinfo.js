function updateUserInfo() {
    fetch('assets/php/user_info.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('player-nickname').textContent = data.data.nickname;
                document.getElementById('current-level').textContent = data.data.rank;
                document.getElementById('player-score').textContent = data.data.score;
                
                const characterImage = document.querySelector('.character-image');
                if (characterImage) {
                    characterImage.classList.remove('rank-1', 'rank-2', 'rank-3');
                    
                    const rank = parseInt(data.data.rank);
                    if (rank === 1) {
                        characterImage.style.cssText = `
                            border-radius: 50%;
                            border: 4px solid;
                            border-color: #ffd700 #fff6a9 #ffd700 #ffd700;
                            box-shadow: 
                                0 0 15px #ffd700,
                                inset 0 0 8px #ffd700,
                                0 0 2px 2px rgba(255, 215, 0, 0.3),
                                inset 0 0 2px 2px rgba(255, 215, 0, 0.3);
                            background-image: linear-gradient(145deg, 
                                rgba(255, 215, 0, 0.4) 0%,
                                rgba(255, 246, 169, 0.1) 47%, 
                                rgba(255, 215, 0, 0.4) 100%);
                        `;
                    } else if (rank === 2) {
                        characterImage.style.cssText = `
                            border-radius: 50%;
                            border: 4px solid;
                            border-color: #C0C0C0 #E8E8E8 #C0C0C0 #C0C0C0;
                            box-shadow: 
                                0 0 15px #C0C0C0,
                                inset 0 0 8px #C0C0C0,
                                0 0 2px 2px rgba(192, 192, 192, 0.3),
                                inset 0 0 2px 2px rgba(192, 192, 192, 0.3);
                            background-image: linear-gradient(145deg, 
                                rgba(192, 192, 192, 0.4) 0%,
                                rgba(232, 232, 232, 0.1) 47%, 
                                rgba(192, 192, 192, 0.4) 100%);
                        `;
                    } else if (rank === 3) {
                        characterImage.style.cssText = `
                            border-radius: 50%;
                            border: 4px solid;
                            border-color: #CD7F32 #FFA54F #CD7F32 #CD7F32;
                            box-shadow: 
                                0 0 15px #CD7F32,
                                inset 0 0 8px #CD7F32,
                                0 0 2px 2px rgba(205, 127, 50, 0.3),
                                inset 0 0 2px 2px rgba(205, 127, 50, 0.3);
                            background-image: linear-gradient(145deg, 
                                rgba(205, 127, 50, 0.4) 0%,
                                rgba(255, 165, 79, 0.1) 47%, 
                                rgba(205, 127, 50, 0.4) 100%);
                        `;
                    }
                }
                
                return fetch('assets/php/Scoreboard2.php');
            }
        })
        .catch(error => console.error('Error:', error));
}

async function updateGameProgress() {
    try {
        const requestResponse = await fetch('assets/php/set_game_request.php');
        const requestData = await requestResponse.json();
        
        if (requestData.error) {
            console.error(requestData.error);
            return;
        }

        const scoreResponse = await fetch('assets/php/Scoreboard2.php');
        const scoreData = await scoreResponse.json();

        if (scoreData.error) {
            console.error(scoreData.error);
            return;
        }

        document.getElementById("score").textContent = scoreData.score;
        document.getElementById("level").textContent = scoreData.stage;

        updateUserInfo();
        updateRanking();
        
        console.log('Stage cleared! Score and progress updated');
    } catch (error) {
        console.error('Error updating game progress:', error);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    updateUserInfo();
    setInterval(updateUserInfo, 30000);
});
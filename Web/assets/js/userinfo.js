function updateUserInfo() {
    fetch('assets/php/user_info.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('player-nickname').textContent = data.data.nickname;
                document.getElementById('current-level').textContent = data.data.rank;
                document.getElementById('player-score').textContent = data.data.score;
                
                return fetch('assets/php/Scoreboard2.php');
            }
            throw new Error('Failed to get user info');
        })
        .then(response => response.json())
        .then(scoreData => {
            if (scoreData.success) {
                const completedChallenges = scoreData.completed_challenges || 0;
                document.getElementById('completed-challenges').textContent = completedChallenges;
            } else {
                document.getElementById('completed-challenges').textContent = '0';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('player-nickname').textContent = 'Guest';
            document.getElementById('current-level').textContent = '-';
            document.getElementById('player-score').textContent = '-';
            document.getElementById('completed-challenges').textContent = '0';
        });
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
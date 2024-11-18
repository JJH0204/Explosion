function updateUserInfo() {
    fetch('assets/php/user_info.php')
        .then(response => response.json())
        .then(data => {
            console.log('User info response:', data); // 디버깅용 로그
            if (data.success) {
                document.getElementById('player-nickname').textContent = data.data.nickname;
                document.getElementById('current-level').textContent = data.data.rank;
                document.getElementById('completed-challenges').textContent = data.data.stage;
                document.getElementById('player-score').textContent = data.data.score;
            } else {
                console.error('Failed to get user info:', data.error);
                document.getElementById('player-nickname').textContent = 'Guest';
                document.getElementById('current-level').textContent = '-';
                document.getElementById('player-score').textContent = '-';
            }
        })
        .catch(error => {
            console.error('Error fetching user info:', error);
            document.getElementById('player-nickname').textContent = 'Guest';
            document.getElementById('current-level').textContent = '-';
            document.getElementById('player-score').textContent = '-';
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
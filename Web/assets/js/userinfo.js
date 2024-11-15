function updateUserInfo() {
    fetch('assets/php/user_info.php')
        .then(response => response.json())
        .then(data => {
            console.log('User info response:', data); // 디버깅용 로그
            if (data.success) {
                document.getElementById('player-nickname').textContent = data.data.nickname;
                document.getElementById('current-level').textContent = data.data.rank;
                document.getElementById('completed-challenges').textContent = data.data.stage;
            } else {
                console.error('Failed to get user info:', data.error);
                document.getElementById('player-nickname').textContent = '손님';
                document.getElementById('current-level').textContent = '-';
            }
        })
        .catch(error => {
            console.error('Error fetching user info:', error);
            document.getElementById('player-nickname').textContent = '손님';
            document.getElementById('current-level').textContent = '-';
        });
}

// 페이지 로드 시 사용자 정보 업데이트
document.addEventListener('DOMContentLoaded', function() {
    updateUserInfo();
    // 30초마다 정보 갱신
    setInterval(updateUserInfo, 30000);
});
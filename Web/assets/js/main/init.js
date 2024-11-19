import MainContent from './Manager/MainContent.js';
import { ImageManager } from './Manager/ImageManager.js';

// 세션 체크 함수 추가
function checkLoginStatus() {
    const isLoggedIn = sessionStorage.getItem('loggedIn') === 'true';
    if (!isLoggedIn) {
        window.location.href = 'login.html';
        return false;
    }
    return true;
}

document.addEventListener('DOMContentLoaded', async () => {
    if (!checkLoginStatus()) return;
    // 메인 컨텐츠 초기화
    const mainContent = new MainContent();
    await mainContent.init();

    // 이미지 매니저 초기화
    const imageManager = new ImageManager({
        defaultImagePath: 'assets/images/custom/character.png',
        customImagePath: 'assets/images/custom/',
        imageExtensions: ['jpg', 'jpeg', 'png', 'gif']
    });
    imageManager.init();

    // 진행 상황 업데이트
    const updateProgress = () => {
        const completedChallenges = document.getElementById('completed-challenges');
        const totalChallenges = document.getElementById('total-challenges');
        
        if (mainContent.config) {
            const solved = mainContent.challenges.filter(c => c.solved).length;
            const total = mainContent.config.game.totalCards;
            
            completedChallenges.textContent = solved;
            totalChallenges.textContent = total;
        }
    };

    // 초기 진행 상황 업데이트
    updateProgress();

    // 화살표 버튼 이벤트 리스너 제거 (MainContent에서 처리)
    const leftArrow = document.querySelector('.arrow-button.left');
    const rightArrow = document.querySelector('.arrow-button.right');
    
    leftArrow.removeAttribute('onclick');
    rightArrow.removeAttribute('onclick');
}); 
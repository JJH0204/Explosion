<?php 
require_once 'assets/php/checkSession.php';
?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flame</title>
    <!-- <link rel="stylesheet" href="assets/styles/style.css"> -->
    <link rel="stylesheet" href="assets/styles/main.css">
    <link rel="stylesheet" href="assets/styles/sidebar.css">
    <link rel="stylesheet" href="assets/styles/cards.css">
    <link rel="stylesheet" href="assets/styles/popup.css">
    <link rel="stylesheet" href="assets/styles/markdown.css">
</head>

<body>
    <div class="background-container">
        <img src="assets/images/background.jpg" alt="Background">
    </div>

    <header class="header">
        <div class="logo-container">
            <img src="assets/images/icons/Flame_logo2.png" alt="Logo" class="logo">
            <span class="logo-text">Flame War Game</span>
        </div>
        <div class="header-menu">
            <button class="menu-button" id="logoutBtn">로그아웃</button>
        </div>
    </header>

    <button class="sidebar-toggle">☰</button>
    <div class="main-container">
        <aside class="sidebar">
            <div class="character-profile">
                <div class="character-image-container">
                    <img src="assets/images/character.png" alt="Character" class="character-image">
                </div>
                <div class="character-info">
                    <div class="level-info">
                        <p>랭킹: <span id="current-level">-</span></p>
                    </div>
                    <div class="character-name">
                        <p id="player-nickname">로딩중...</p>
                    </div>
                </div>
            </div>

            <div class="progress-info">
                <h3>진행 상황</h3>
                <div id="progress">
                    <span id="completed-challenges">0</span> /
                    <span id="total-challenges">40</span>
                </div>
            </div>
            <div class="scoreboard">
            </div>
            <div id="rankingBoard" class="ranking-board">
                <h3>실시간 랭킹</h3>
                <ul id="rankingList"></ul>
            </div>
        </aside>
       
    
        <main class="main-content">
            <div class="cards-container">
                <div id="challengeGrid">
                    <!-- 카드 페이지들 -->
                </div>
            </div>
        </main>
    </div>

    <button class="arrow-button left" onclick="cardManager.prevPage()">◀</button>
    <button class="arrow-button right" onclick="cardManager.nextPage()">▶</button>

    <!-- marked.js 추가 -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <!-- 게임 팝업 수정 -->
    <div id="gamePopup" class="popup">
        <div class="popup-content">
            <div id="gameContent"></div>
        </div>
    </div>

    <script src="assets/js/config.js"></script>
    <script src="assets/js/ui-manager.js"></script>
    <script src="assets/js/game-manager.js"></script>
    <script src="assets/js/card-manager.js"></script>
    <script src="assets/js/ranking.js"></script>
    <script src="assets/js/userinfo.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            fetch('assets/php/fetchClearedCards.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        gameManager.initializeClearedCards(data.clearedCards);
                    } else {
                        console.error('Failed to fetch cleared cards:', data.error);
                    }
                })
                .catch(error => {
                    console.error('Error fetching cleared cards:', error);
                });
        });
    </script>
</body>

</html>
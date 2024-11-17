function checkFlag() {
    const userFlag = document.getElementById('userInput').value.trim();
    const correctFlag = "F1rstP4sSw0rD";
    const resultElement = document.getElementById('result');
    const submitButton = document.getElementById('submitButton');

    if (userFlag === correctFlag) {
        resultElement.style.color = '#00ff7f';
        resultElement.textContent = '정답입니다!';
        submitButton.style.display = 'inline-block';
    } else {
        resultElement.style.color = '#ff4d4d';
        resultElement.textContent = '틀렸습니다. 다시 시도하세요.';
        submitButton.style.display = 'none';
    }
}

function toggleSSHInfo() {
    const sshInfo = document.getElementById('sshInfo');
    if (sshInfo.style.display === 'none') {
        sshInfo.style.display = 'block';
    } else {
        sshInfo.style.display = 'none';
    }
}

function login() {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const loginForm = document.getElementById('loginForm');
    const downloadSection = document.getElementById('downloadSection');
    
    if (username === 'admin' && password === 'adminpass') {
        loginForm.style.display = 'none';
        downloadSection.style.display = 'block';
    } else {
        const loginMessage = document.getElementById('loginMessage');
        loginMessage.style.backgroundColor = '#4d0000';
        loginMessage.style.color = '#ff4d4d';
        loginMessage.textContent = '잘못된 사용자 이름 또는 비밀번호입니다.';
        loginMessage.style.display = 'block';
    }
}
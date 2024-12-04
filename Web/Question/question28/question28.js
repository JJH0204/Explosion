async function checkFlag() {
    const userFlag = document.getElementById('userInput').value.trim();
    const resultElement = document.getElementById('result');
    const submitButton = document.getElementById('submitButton');

    try {
        const formData = new FormData();
        formData.append('action', 'checkFlag');

        const response = await fetch('index.php', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });

        const data = await response.json();
        
        if (!data.success) {
            resultElement.style.color = '#ff4d4d';
            resultElement.textContent = '오류가 발생했습니다: ' + data.message;
            return;
        }

        if (userFlag === data.flag) {
            resultElement.style.color = '#00ff7f';
            resultElement.textContent = '정답입니다!';
            submitButton.style.display = 'inline-block';
        } else {
            resultElement.style.color = '#ff4d4d';
            resultElement.textContent = '틀렸습니다. 다시 시도하세요.';
            submitButton.style.display = 'none';
        }
    } catch (error) {
        resultElement.style.color = '#ff4d4d';
        resultElement.textContent = '서버 오류가 발생했습니다.';
        console.error('Error:', error);
    }
} 
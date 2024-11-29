async function getCorrectFlag(stage) {
    try {
        const response = await fetch('./json/flags.json');
        const data = await response.json();
        return data.flags[stage - 1];
    } catch (error) {
        console.error('Error loading flags:', error);
        return null;
    }
}

async function checkFlag(stage) {
    const userFlag = document.getElementById('userInput').value.trim();
    const resultElement = document.getElementById('result');
    const currentButton = document.getElementById(`submit${stage}`);
    const nextButton = document.getElementById(`submit${stage + 1}`);
    const pcapButton = document.getElementById('pcapButton');
    const imageButton = document.getElementById('imageButton');
    const story1 = document.getElementById('story1');
    const story2 = document.getElementById('story2');
    const story3 = document.getElementById('story3');
    const question = document.getElementById('question');

    const correctFlag = await getCorrectFlag(stage);
    
    if (!correctFlag) {
        resultElement.style.color = '#ff4d4d';
        resultElement.textContent = '설정을 불러오는데 실패했습니다.';
        return;
    }

    if (userFlag === correctFlag) {
        resultElement.style.color = '#00ff7f';
        resultElement.textContent = '정답입니다!';
        currentButton.style.display = 'none';
        if (nextButton) {
            nextButton.style.display = 'inline-block';
            if (stage === 1) {
                pcapButton.style.display = 'inline-block';
                imageButton.style.display = 'none';
                story1.style.display = 'none';
                story2.style.display = 'block';
            } else if (stage === 2) {
                pcapButton.style.display = 'none';
                question.style.display = 'block';
                story2.style.display = 'none';
                story3.style.display = 'block';
            }
        }
        document.getElementById('userInput').value = '';
    } else {
        resultElement.style.color = '#ff4d4d';
        resultElement.textContent = '틀렸습니다. 다시 시도하세요.';
    }
}

function downloadImage() {
    const link = document.createElement('a');
    link.href = './ChallengeFile/question30.png';
    link.download = './ChallengeFile/question30.png';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function pcap() {
    const link = document.createElement('a');
    link.href = './ChallengeFile/xmas2011.pcap';
    link.download = './ChallengeFile/xmas2011.pcap';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function toggleHint(hintId) {
    const hint = document.getElementById(hintId);
    if (hint.style.display === 'none') {
        hint.style.display = 'block';
    } else {
        hint.style.display = 'none';
    }
}

async function submitComment() {
    const commentInput = document.getElementById('commentInput');
    const flagResult = document.getElementById('flag-result');
    const flagText = document.getElementById('flag-text');
    const comment = commentInput.value.trim().toLowerCase();
    
    if (comment === 'grandma' || comment === '할머니') {
        try {
            const response = await fetch('./json/flags.json');
            const data = await response.json();
            const flag = data.flags[2]; // 세 번째 플래그 값
            flagText.textContent = `플래그: flag{${flag}}`;
            flagResult.style.display = 'block';
            commentInput.value = '';
        } catch (error) {
            console.error('Error loading flag:', error);
        }
    } else {
        const resultElement = document.getElementById('result');
        resultElement.style.color = '#ff4d4d';
        resultElement.textContent = '틀린 답변입니다. 다시 시도하세요.';
        commentInput.value = '';
    }
} 
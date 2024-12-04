function uploadFile() {
    const fileInput = document.getElementById('file-upload');
    const resultDiv = document.getElementById('upload-result');

    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        const fileName = file.name.toLowerCase();
        const allowedExtensions = ['.txt', '.png', '.jpg', '.jpeg'];

        const isAllowed = allowedExtensions.some(ext => fileName.endsWith(ext));

        if (isAllowed) {
            resultDiv.innerHTML = `파일 "${file.name}" 업로드 성공!`;
            checkFileContent(file);
        } else {
            resultDiv.innerHTML = '허용되지 않는 파일 형식입니다.';
        }
    }
}

function checkFileContent(file) {
    const reader = new FileReader();
    reader.onload = function (e) {
        const content = e.target.result;
        if (content.includes('ADMIN_KEY')) {
            fetchAdminFlag();
        }
    };
    reader.readAsText(file);
}

function fetchAdminFlag() {
    fetch('index.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ action: 'getFlag' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const adminPanel = document.getElementById('admin-panel');
            adminPanel.classList.remove('hidden');
            document.getElementById('admin-content').innerHTML = 
                `축하합니다! 플래그를 찾았습니다: ${data.flag}`;
        }
    })
    .catch(error => console.error('Error:', error));
}
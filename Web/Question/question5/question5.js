function uploadFile() {
    const fileInput = document.getElementById('file-upload');
    const resultDiv = document.getElementById('upload-result');

    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        const fileName = file.name.toLowerCase();
        const allowedExtensions = ['.txt', '.png', '.jpg', '.jpeg'];

        // 파일 확장자가 블랙리스트에 포함되어 있지 않은 경우
        const isAllowed = allowedExtensions.some(ext => fileName.endsWith(ext));

        if (isAllowed) {
            resultDiv.innerHTML = `파일 "${file.name}" 업로드 성공!`;
            checkFileContent(file); // 파일 내용 확인 함수 호출
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
            showAdminPanel();
        }
    };
    reader.readAsText(file);
}

function showAdminPanel() {
    const adminPanel = document.getElementById('admin-panel');
    adminPanel.classList.remove('hidden');

    const flag = decrypt('U2FsdGVkX19+MjAyNCtzZWNyZXQra2V5K3RvK2RlY3J5cHQrZmxhZw==');
    document.getElementById('admin-content').innerHTML = `축하합니다! 플래그를 찾았습니다: ${flag}`;
}

// 암호화된 플래그 복호화
function decrypt(encoded) {
    return atob(encoded);
}
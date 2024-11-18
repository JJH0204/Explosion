
---

# 문제 5 - 파일 업로드 취약점

## 문제 설명
파일 업로드 기능의 취약점을 이용하여 관리자 권한을 활성화하고, Base64로 인코딩된 플래그를 복호화하여 찾는 문제입니다.

---

## 풀이 과정

### 1. 소스 코드 분석

**파일 업로드 처리 코드**
- 파일 업로드와 확장자 검증은 `uploadFile` 함수에서 수행됩니다.
- 허용된 파일 확장자: `.txt`, `.png`, `.jpg`, `.jpeg`
```javascript
const allowedExtensions = ['.txt', '.png', '.jpg', '.jpeg'];
const isAllowed = allowedExtensions.some(ext => fileName.endsWith(ext));
```

**파일 내용 확인 코드**
- 파일 내용에 `'ADMIN_KEY'` 문자열이 포함되어 있으면 관리자 패널이 활성화됩니다.
```javascript
if (content.includes('ADMIN_KEY')) {
    showAdminPanel();
}
```

---

### 2. 관리자 패널 활성화

**관리자 패널 활성화 코드**
- `showAdminPanel` 함수가 호출되면 관리자 패널이 활성화되며, 암호화된 플래그가 복호화되어 표시됩니다.
```javascript
function showAdminPanel() {
    const adminPanel = document.getElementById('admin-panel');
    adminPanel.classList.remove('hidden');

    const flag = decrypt('U2FsdGVkX19+MjAyNCtzZWNyZXQra2V5K3RvK2RlY3J5cHQrZmxhZw==');
    document.getElementById('admin-content').innerHTML = `축하합니다! 플래그: ${flag}`;
}
```

---

### 3. 파일 업로드 우회

1. **텍스트 파일 생성**
   - 이름: `admin.txt`
   - 내용: `ADMIN_KEY` 문자열 포함

2. **파일 업로드**
   - 웹 페이지에서 파일을 업로드하여 검증 과정을 통과합니다.

3. **관리자 패널 확인**
   - 업로드 후 관리자 패널이 활성화됩니다.

---

### 4. 플래그 디코딩

**암호화된 플래그**
- 암호화된 플래그는 Base64 형식입니다:
```plaintext
U2FsdGVkX19+MjAyNCtzZWNyZXQra2V5K3RvK2RlY3J5cHQrZmxhZw==
```

**복호화 함수**
- `atob` 함수를 사용하여 Base64를 복호화합니다:
```javascript
function decrypt(encoded) {
    return atob(encoded);
}
```

**복호화 결과**
```plaintext
flag{SaltedX_2024+secret+key+to+decrypt+flag}
```

---

## 힌트

**콘솔 로그 확인**
- 개발자 도구에서 힌트를 확인할 수 있습니다.
```plaintext
startLine: 179
endLine: 185
```

---

## 사용된 기술

1. **파일 업로드 취약점 분석**
2. **파일 내용 기반 조건 검증 우회**
3. **Base64 디코딩**
4. **문자열 조작**
5. **개발자 도구 활용**

---

## 최종 플래그
```
flag{SaltedX_2024+secret+key+to+decrypt+flag}
```
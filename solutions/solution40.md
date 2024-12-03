# Question 40

## 취약점 분석 및 공격 방법

### 1. 웹 소스 확인
- 웹 페이지의 소스 코드를 확인하여 게임의 동작 방식을 파악
- JavaScript 코드에서 정답 체크 로직이 클라이언트 측에서 이루어지는 것을 확인

### 2. 클라이언트 스크립트 (전역 변수) 취약점 확인
```javascript
if (input === gameData.key) {
    if (gameData.flag) {
        alert(gameData.flag);
    }
    document.getElementById('result').innerHTML = '정답입니다!';
    result = { rank: 0, word: input, similarity: 1.0 };
}
```
- gameData.key 변수가 전역 변수로 저장되어 있다.

### 3. 브라우저 개발자 모드 활성화
- F12 또는 브라우저 개발자 도구를 실행
- Console 탭으로 이동

### 4. gameData.key 값 변경
- Console에서 다음 명령어 실행:
```javascript
gameData.key;  // 값 확인
```

### 5. 단어 입력
- 입력창에 확인한 단어 입력
- 제출 버튼 클릭

### 6. 플래그 획득
획득한 플래그: `FLAG{W0rd_V3ct0r_S3m4nt1c_M4st3r}`

### 7. 보완점
1. 클라이언트 측에서 정답 검증을 하지 않고, 서버 측에서 처리해야 함
2. gameData 객체를 전역 변수로 저장하지 않고, 클로저나 모듈 패턴을 사용하여 보호
3. 입력값 검증을 서버 측에서 수행
4. 플래그 값을 서버 측에서만 저장하고 필요할 때만 전송
5. API 응답에 대한 무결성 검사 추가
6. 디버거 감지 및 개발자 도구 사용 제한 고려

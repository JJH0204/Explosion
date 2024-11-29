# LocalStorage 조작 취약점 풀이

## 1. 문제 분석
- 웹 페이지는 클리어한 스테이지 정보를 LocalStorage에 저장
- DB에는 실제 클리어한 스테이지 정보가 저장됨
- LocalStorage와 DB의 정보 불일치를 통해 취약점 발견 가능

## 2. 공격 방법

### 개발자 도구를 이용한 LocalStorage 조작
1. F12를 눌러 개발자 도구 실행
2. Application 탭 선택
3. Storage > Local Storage > 해당 도메인 선택
4. solvedCards 값을 수정하여 스테이지 9 추가
   예: `["1","2","9"]`

### LocalStorage 조작 예시
```javascript
// 기존 solvedCards 가져오기
let solvedCards = JSON.parse(localStorage.getItem('solvedCards') || '[]');
// 스테이지 9 추가
solvedCards.push('9');
// 수정된 값 저장
localStorage.setItem('solvedCards', JSON.stringify(solvedCards));
```

## 3. 플래그 획득
1. LocalStorage 조작 시 alert으로 다음 값이 출력됨:
   `Flag{LocalStorage_Modification}`

## 4. 취약점 설명
- 클라이언트 측 저장소인 LocalStorage는 사용자가 쉽게 조작 가능
- 중요한 데이터 검증이 클라이언트 측에서만 이루어짐
- 서버 측 DB와 클라이언트 측 저장소의 데이터 불일치 발생 가능

## 5. 보안 대책
1. 중요한 데이터는 서버 DB에서만 관리
2. LocalStorage는 임시 데이터 저장용으로만 사용
3. 클라이언트 데이터 사용 시 항상 서버 측 검증 수행
4. 주기적인 데이터 동기화 및 무결성 검사 구현

## 최종 플래그
```
Flag{LocalStorage_Modification}
```
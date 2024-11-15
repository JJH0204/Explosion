# 문제 4 - JWT & 암호화 문제

## 문제 설명
JWT 토큰 조작과 AES 암호화가 적용된 고급 난이도의 문제입니다. JWT 토큰을 분석하고 조작하여 관리자 권한을 획득한 후, 암호화된 플래그를 복호화해야 합니다.

## 풀이 과정

### 1. JWT 토큰 분석
관리자 패널 접근을 위한 JWT 토큰 검증 코드: 
```
html:Web/Question/question4/question4.html
startLine: 105
endLine: 111
```

### 2. 관리자 권한 획득
1. JWT 토큰의 페이로드에서 role 값을 'admin'으로 수정
2. 수정된 토큰으로 인증 우회
3. 관리자 패널 접근 권한 획득

### 3. 플래그 복호화
암호화된 플래그와 복호화 코드:
```
html:Web/Question/question4/question4.html
startLine: 119
endLine: 124
```

복호화 과정:
1. 암호화된 플래그: `U2FsdGVkX19KV1QjhX7/8q9WQj+zX5tX/YwF+A8L1Jw=`
2. 키 생성: `CryptoJS.SHA256('admin_key_2024')`
3. AES 복호화 수행

## 힌트
1. JWT 토큰의 구조를 이해하세요
2. 페이로드의 role 값을 확인하세요
3. CryptoJS 라이브러리의 AES 복호화 방식을 분석하세요

## 사용된 기술
- JWT 토큰 분석 및 조작
- CryptoJS 라이브러리
- AES 암호화/복호화
- SHA256 해시 함수

## 최종 플래그
```
flag{decrypted_admin_jwt_2024}
```
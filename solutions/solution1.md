# 문제 1 - Basic Wargame Challenge

## 문제 설명
기본적인 웹 해킹 입문 문제입니다. 페이지 소스 코드를 분석하여 관리자 계정으로 로그인하고, Base64로 인코딩된 플래그를 찾아내야 합니다.

## 풀이 과정

### 1. 소스 코드 분석
JavaScript 코드에서 관리자 계정 정보를 확인할 수 있습니다:
```
html:Web/Question/question1/question1.html
startLine: 70
endLine: 74
```
여기서 관리자 ID가 "admin"임을 알 수 있습니다.

### 2. 관리자 로그인
1. 입력창에 "admin" 입력
2. 로그인 버튼 클릭
3. 성공 시 Base64로 인코딩된 플래그 출력: `ZmxhbWUxYW5zd2Vy`

### 3. 플래그 디코딩
- Base64 디코딩 도구 사용 (예: CyberChef, 개발자 콘솔)
- `ZmxhbWUxYW5zd2Vy` → `flame1answer`

## 힌트
1. 페이지 소스 코드를 확인하세요
2. 개발자 도구의 콘솔 메시지를 확인하세요
3. Base64 디코딩이 필요합니다

## 사용된 기술
- 소스 코드 분석
- Base64 인코딩/디코딩
- 개발자 도구 활용

## 최종 플래그
```
flag{flame1answer}
```
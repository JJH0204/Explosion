# 문제 1 - JavaScript Analysis Challenge

## 문제 설명
이 문제는 JavaScript 코드를 분석하여 숨겨진 플래그를 획득하는 웹 해킹 문제입니다. 콘솔 메시지를 확인하고 암호를 입력하여 플래그를 출력해야 합니다.

## 풀이 과정

### 1. 소스 코드 분석
JavaScript 코드에서 암호(secretKey)와 플래그 복호화 로직을 확인할 수 있습니다:
```
html:Web/Question/question7/question7.html
startLine: 35
endLine: 50
```
여기서 암호가 "secret2024"임을 알 수 있습니다.

### 2. 관리자 로그인
1. 입력창에 "secret2024"를 입력
2. 확인 버튼 클릭
3. 성공 시 플래그가 출력됩니다.

### 3. 플래그 복호화
- const encodedFlag = [0x46, 0x4C, 0x41, 0x47, 0x7B, 0x6A, 0x73, 0x5F, 0x63, 0x6F, 0x64, 0x65, 0x5F, 0x61, 0x6E, 0x61, 0x6C, 0x79, 0x73, 0x69, 0x73, 0x7D];
return encodedFlag.map(byte => String.fromCharCode(byte)).join('');

### 4. 플래그 획득
"secret2024"를 입력하여 다음 플래그를 획득합니다:
- FLAG{js_code_analysis}

## 힌트
1. JavaScript 코드를 분석하세요
2. 콘솔 메시지에서 힌트를 찾으세요
3. 암호를 입력하여 플래그를 출력하세요

## 사용된 기술
- JavaScript 코드 분석
- 개발자 도구(F12)
- 문자열 복호화

## 최종 플래그
```
FLAG{js_code_analysis}
```
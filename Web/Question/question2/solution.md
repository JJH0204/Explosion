# 문제 2 - 워게임 챌린지

## 문제 설명
HTML 소스 코드에 숨겨진 플래그를 찾는 기초적인 문제입니다. 페이지 소스의 주석을 확인하여 플래그를 찾아내야 합니다.

## 풀이 과정

### 1. HTML 주석 확인
페이지 소스 코드의 주석에서 플래그를 발견할 수 있습니다:
```
html:Web/Question/question2/question2.html
startLine: 49
endLine: 49
```

### 2. JavaScript 검증 코드 확인
소스 코드에서 플래그 검증 로직을 확인할 수 있습니다:
```
html:Web/Question/question2/question2.html
startLine: 56
endLine: 57
```

### 3. 플래그 제출
1. 입력창에 `hidden_flag_123` 입력
2. 제출 버튼 클릭
3. 성공 메시지 확인:
```
html:Web/Question/question2/question2.html
startLine: 63
endLine: 65
```

## 힌트
1. 페이지 소스 코드의 HTML 주석을 찾아보세요
2. 개발자 도구로 소스 코드를 확인하세요
3. JavaScript 변수를 확인해보세요

## 사용된 기술
- HTML 소스 코드 분석
- 주석 확인
- JavaScript 코드 이해

## 최종 플래그
```
flag{hidden_flag_123}
```
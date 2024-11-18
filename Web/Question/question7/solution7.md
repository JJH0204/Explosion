# 문제 7 - 메모리 해킹 문제

## 문제 설명
메모리 스캐닝과 XOR 암호화가 적용된 고급 난이도의 문제입니다. 안티 디버깅을 우회하고 메모리 무결성 검사를 통과하여 플래그를 찾아내야 합니다.

## 풀이 과정

### 1. 안티 디버깅 분석
안티 디버깅 코드: 
```
html:Web/Question/question7/question7.html
startLine: 223
endLine: 230
```

### 2. 메모리 무결성 검사
메모리 검사 코드:
```
html:Web/Question/question7/question7.html
startLine: 233
endLine: 235
```

### 3. 플래그 복호화 분석
XOR 복호화 코드:
```
html:Web/Question/question7/question7.html
startLine: 211
endLine: 216
```

복호화 과정:
1. XOR 키: `0xFF`
2. 인코딩된 데이터: `[0x7F, 0x6E, 0x61, 0x67, 0x7B, 0x6D, 0x65, 0x6D, 0x5F, 0x68, 0x61, 0x63, 0x6B, 0x7D]`
3. XOR 연산 후 문자열 변환

### 4. 메모리 스캐너 우회
실행 검증 코드:
```
html:Web/Question/question7/question7.html
startLine: 202
endLine: 208
```

## 힌트
1. 디버거 탐지를 우회하세요
2. 메모리 체크섬을 분석하세요
3. XOR 연산의 특성을 이용하세요
4. 메모리 스캐너의 검증 로직을 이해하세요

## 사용된 기술
- 메모리 스캐닝
- XOR 암호화/복호화
- 안티 디버깅 우회
- 메모리 무결성 검사

## 최종 플래그
```
FLAG_mem{hack}
```
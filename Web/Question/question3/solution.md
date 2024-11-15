# 문제 3 - JavaScript 난독화 문제

## 문제 설명
JavaScript 난독화와 Base64 인코딩이 적용된 중급 난이도의 문제입니다. 난독화된 코드를 분석하고 디버거를 우회하여 플래그를 찾아내야 합니다.

## 풀이 과정

### 1. 난독화된 코드 분석
소스 코드에서 난독화된 변수와 Base64로 인코딩된 secretKey를 확인할 수 있습니다: 
```
html:Web/Question/question3/question3.html
startLine: 33
endLine: 42
```

### 2. Base64 인코딩된 키 확인
secretKey 값을 확인하고 디코딩:
```
html:Web/Question/question3/question3.html
startLine: 45
endLine: 45
```
- secretKey: `VGhpc0lzTm90VGhlUmVhbEtleQ==`
- 디코딩 결과: `ThisIsNotTheRealKey`

### 3. 키 검증 함수 분석
키 검증 로직을 확인:
```html:Web/Question/question3/question3.html
startLine: 89
endLine: 91
```
필요한 입력값: `ThisIsNotTheRealKey_admin`을 Base64로 인코딩한 값

### 4. 디버거 우회
안티 디버깅 코드를 확인:
```html:Web/Question/question3/question3.html
startLine: 48
endLine: 55
```

## 힌트
1. 콘솔의 힌트 메시지를 확인하세요
2. Base64 인코딩/디코딩이 필요합니다
3. 키 검증 함수의 로직을 분석하세요
4. 디버거 탐지를 우회해야 합니다

## 사용된 기술
- JavaScript 난독화 분석
- Base64 인코딩/디코딩
- 디버거 우회 기법
- 문자열 조작

## 최종 플래그
```
FLAG{Base64_encoded_ThisIsNotTheRealKey_admin_reversed}
```
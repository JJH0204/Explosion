# Question 3 - Base64 디코딩 문제 풀이

## 문제 개요
이 문제는 Base64로 인코딩된 문자열을 디코딩하여 올바른 인증 키를 찾는 문제입니다.

## 풀이 과정

### 1. 초기 분석
- 웹 페이지에 접속하면 404 에러 페이지처럼 보이지만 인증 키를 입력하는 폼이 존재합니다.
- 페이지 소스 코드를 확인하면 난독화된 JavaScript 코드를 발견할 수 있습니다.
- 콘솔에 두 개의 힌트 메시지가 출력됩니다:
  ```
  힌트2: 비밀 키는 Base64로 인코딩되어 있습니다.
  힌트3: 개발자 도구의 콘솔에서 atob() 함수를 사용해보세요.
  ```

### 2. 코드 분석
- 난독화된 코드를 자세히 보면 Base64로 인코딩된 문자열 `aGFja2VyMTIz`를 발견할 수 있습니다.
- 이 문자열이 인증 키와 관련이 있을 것으로 추정됩니다.

### 3. Base64 디코딩
- 브라우저의 개발자 도구 콘솔을 열고 다음 명령어를 실행합니다:
  ```javascript
  atob('aGFja2VyMTIz')
  ```
- 실행 결과로 `'hacker123'`이 출력됩니다.

### 4. 인증 키 입력
- 얻은 문자열을 그대로 입력하는 것이 아니라, Base64로 인코딩된 형태로 입력해야 합니다.
- 따라서 `aGFja2VyMTIz`를 인증 키 입력 필드에 입력합니다.

### 5. 플래그 획득
- 정확한 인증 키를 입력하면 다음과 같은 플래그가 출력됩니다:
  ```
  FLAG{base64_decode_success}
  ```

## 학습 포인트
1. Base64 인코딩/디코딩의 개념 이해
2. 브라우저 개발자 도구의 활용
3. JavaScript의 atob() 함수 사용법
4. 기본적인 코드 난독화 분석 방법

## 주요 도구
- 브라우저 개발자 도구
- JavaScript Console
- Base64 디코딩 함수 (atob)
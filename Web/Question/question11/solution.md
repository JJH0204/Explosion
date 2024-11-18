# 문제 11 - XSS 댓글 시스템

## 문제 설명
XSS 취약점을 활용하여 악성 스크립트를 실행시키세요. 댓글 입력 창에 XSS 공격을 성공적으로 수행하면 플래그가 출력됩니다.  
XSS 공격의 기본 원리를 이해하고 시도해보세요.

## 풀이 과정

### 1. 소스 코드 분석
댓글 입력 값이 그대로 HTML에 렌더링되는 취약점:
```
html:Web/Question/question2/question2.html 
startLine: 40 
endLine: 50
```
입력된 값이 `innerHTML` 속성으로 추가되므로 HTML 및 JavaScript 코드가 그대로 실행될 수 있습니다:
```javascript
commentElement.innerHTML = comment;

### 2. XSS 페이로드 작성
XSS 공격을 성공시키기 위한 기본적인 페이로드:
<script>alert('XSS 취약점 발견!')</script>
위 코드를 댓글 입력란에 입력하면, 브라우저에서 alert 창이 실행됩니다.

### 3. 플래그 획득 방법
1. 댓글 입력란에 다음 페이로드를 입력:
<script>document.body.innerHTML += '<p>플래그: flag_xss_vulnerability</p>';</script>
2. 게시 버튼을 클릭하여 스크립트 실행.
3. 플래그가 페이지에 추가로 표시됩니다:
플래그: flag_xss_vulnerability

## 힌트
1. <script> 태그를 사용하여 악성 코드를 삽입해보세요.
2. 개발자 도구의 콘솔에 추가적인 힌트를 확인하세요.
3. XSS 페이로드를 통해 플래그를 직접 출력하거나 페이지를 조작하세요.

## 사용된 기술
- XSS 공격
- DOM 조작
- JavaScript 실행
- HTML 렌더링 취약점 분석

## 최종 플래그
```
flag{flag_xss_vulnerability}
```
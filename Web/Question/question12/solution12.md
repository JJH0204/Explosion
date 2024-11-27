# 문제 12 - Phone Number Challenge Solution

## 문제 설명
이 문제는 웹 페이지에서 제시된 전화번호(070-8272-9218)를 입력하는 웹 해킹 문제입니다. 전화번호는 슬라이더를 통해 입력해야 하며, XSS 취약점을 이용하여 우회할 수 있습니다.

## 풀이 과정

### 1. 정상적인 풀이 방법
1. 웹 페이지에 접속하면 슬라이더가 표시됩니다.
2. 슬라이더를 조작하여 목표 전화번호인 070-8272-9218을 입력합니다.
3. "전화 걸기" 버튼을 클릭하여 정답을 제출합니다.

### 2. XSS 취약점을 이용한 풀이
1. URL 파라미터 `shared`를 통해 XSS 공격이 가능합니다.
2. 취약점이 발생하는 소스코드는 다음과 같습니다:
```javascript
function getSharedNumber() {
    const urlParams = new URLSearchParams(window.location.search);
    const shared = urlParams.get('shared');
    if (shared) {
        // XSS 취약점: shared 파라미터 값을 직접 innerHTML로 삽입
        document.getElementById('selectedNumber').innerHTML = '선택된 번호: ' + shared;
        // 슬라이더 값도 업데이트
        if (!isNaN(shared.replace(/-/g, ''))) {
            slider.value = shared.replace(/-/g, '');
        }
    }
}
```
위 코드에서 `innerHTML`을 사용하여 검증 없이 `shared` 파라미터 값을 직접 삽입하는 부분이 XSS 취약점을 발생시킵니다.

3. 다음과 같은 URL을 사용하여 전화번호를 자동으로 입력할 수 있습니다:
```
?shared=<img src=x onerror="document.getElementById('phoneSlider').value=7082729218;checkNumber();">
```
4. XSS 공격이 성공하면 자동으로 정답이 입력되고 플래그가 표시됩니다.

## 힌트
1. 전화번호는 하이픈(-)을 제외한 11자리 숫자입니다.
2. URL 파라미터를 통한 입력 검증 우회가 가능합니다.
3. JavaScript 이벤트 핸들러를 활용하여 자동화된 입력이 가능합니다.

## 사용된 기술
- XSS(Cross-Site Scripting): URL 파라미터를 통한 스크립트 주입
- DOM 조작: JavaScript를 통한 슬라이더 값 변경
- 입력 검증 우회: URL 파라미터를 이용한 입력 메커니즘 우회

## 최종 플래그
```
FLAG{C4ll_M3_M4yB3_B4by}
```
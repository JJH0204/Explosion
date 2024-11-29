# 이미지 조작 취약점 풀이

## 1. 문제 분석
- 웹 페이지에는 기본 이미지(character.png)가 표시됨
- 이미지 변경 시 checkImage() 함수가 동작하여 플래그를 확인
- 플래그는 Base64로 인코딩되어 있음

## 2. 공격 방법

### 개발자 도구를 이용한 이미지 소스 변경
1. F12를 눌러 개발자 도구 실행
2. Elements 탭에서 img 태그 찾기
3. img 태그의 src 속성을 다른 이미지 경로로 변경
   예: `src="assets/images/custom/another.png"`

### 유효한 이미지 경로 예시
```html
<img src="assets/images/custom/another.png" onclick="checkImage(this)">
```

## 3. 플래그 획득
1. 이미지 변경 시 alert으로 다음 값이 출력됨:
   `FLag{image_change}`

## 4. 취약점 설명
- 클라이언트 사이드에서 이미지 검증이 이루어짐
- 사용자가 개발자 도구를 통해 쉽게 이미지 소스를 변경할 수 있음
- 중요한 검증은 서버 사이드에서 이루어져야 함

## 5. 보안 대책
1. 중요한 검증은 서버 측에서 수행
2. 클라이언트 사이드 검증은 보조 수단으로만 사용
3. 이미지 변경이 필요한 경우 적절한 인증 절차 추가

## 6. 최종 플래그
```
FLag{image_change}
```
# Question 4 - 프록시 도구를 이용한 권한 상승 문제 풀이

## 문제 개요
이 문제는 프록시 도구를 사용하여 로그인 요청의 role 파라미터를 조작하는 웹 취약점 문제입니다.

## 풀이 과정

### 1. 초기 분석
- 웹 페이지에 접속하면 기본적인 로그인 폼이 표시됩니다.
- 페이지 소스 코드를 확인하면 hidden 필드로 role=user가 설정되어 있습니다.
- 콘솔에 두 개의 힌트 메시지가 출력됩니다:
  ```
  힌트1: 로그인 요청을 프록시 툴로 확인해보세요
  힌트2: role 파라미터의 값을 확인해보세요
  ```

### 2. 프록시 도구 설정
1. Burp Suite 실행
2. 프록시 설정 확인 (기본값: 127.0.0.1:8080)
3. 브라우저 프록시 설정
   - 설정 → 네트워크 설정
   - 프록시 수동 설정
   - HTTP 프록시: 127.0.0.1, 포트: 8080

### 3. 로그인 요청 분석
- 임의의 사용자명과 비밀번호로 로그인 시도
- Burp Suite에서 요청을 가로채서 확인
- POST 요청에서 role=user 파라미터 확인

### 4. 파라미터 조작
- 가로챈 요청에서 role 파라미터 값을 수정:
  ```
  변경 전: role=user
  변경 후: role=admin
  ```
- Forward 버튼을 눌러 수정된 요청 전송

### 5. 플래그 획득
- 성공적으로 role 파라미터를 admin으로 변경하면 다음 응답을 받습니다:
  ```
  관리자로 로그인 성공!
  FLAG{proxy_role_manipulation_success}
  ```

## 대체 풀이 방법
브라우저 개발자 도구를 사용한 방법:
1. 개발자 도구 열기 (F12)
2. Elements 탭에서 hidden input 필드 찾기
3. role의 value 속성을 'admin'으로 직접 수정
4. 로그인 버튼 클릭

## 학습 포인트
1. 웹 프록시 도구의 기본 사용법
2. HTTP 요청/응답 구조 이해
3. 파라미터 조작을 통한 권한 우회 취약점
4. Hidden 필드의 보안 한계

## 주요 도구
- 웹 브라우저 (Chrome, Firefox 등)
- Burp Suite Community Edition
- 브라우저 개발자 도구

## 보안 시사점
1. 클라이언트 측 검증만으로는 보안을 보장할 수 없음
2. 중요한 권한 설정은 서버 측에서 철저히 검증해야 함
3. Hidden 필드는 보안 통제 수단으로 적절하지 않음
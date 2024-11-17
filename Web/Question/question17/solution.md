# 문제 16 - SQL Grant Challenge

## 문제 설명
이 문제는 사용자가 SQL 권한 부여 명령어(GRANT)를 이해하고, 정확히 실행하여 플래그를 획득하는 도전 과제입니다. 제공된 조건에 맞는 SQL 명령어를 작성하여 입력란에 제출하면 서버가 이를 검증하고 플래그를 반환합니다.

## 풀이 과정

### 1. 문제 조건 분석
입력해야 하는 SQL 명령어는 다음 조건을 충족해야 합니다:
- 유저는 'test'@'localhost'입니다.
- 대상 테이블은 USER_db.test입니다.
- 부여할 권한은 SELECT 및 DELETE입니다.

### 2. 정답 SQL 명령어 작성
이다음 SQL 명령어를 작성해야 합니다: 스크립트는 다음과 같은 작업을 수행합니다:
```
GRANT SELECT, DELETE ON USER_db.test TO 'test'@'localhost';
```

### 3. 제출 및 검증
1. HTML 페이지에서 SQL 명령어를 작성하여 제출합니다.
2. 서버(PHP)가 입력된 SQL 명령어를 검증합니다.
3. 올바른 명령어를 제출하면 플래그가 출력됩니다.

## 사용된 기술
- SQL 명령어 검증: PHP로 서버 측에서 SQL 명령어를 검증.
- HTML 폼과 JavaScript: 클라이언트에서 SQL 명령어를 작성하고 서버에 제출.
- 정답 비교: 서버는 SQL 명령어를 사전에 정의된 정답과 비교.

## 최종 플래그
```
FLAG{sql_grant_correct}
```
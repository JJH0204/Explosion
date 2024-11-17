
# 1. 문제 분석
## 1.1 문제 설명
로그인 폼이 있는 웹 페이지  
SQL Injection 취약점이 존재  
admin 계정으로 로그인하면 플래그 획득 가능  

# 2. 공격 방법
## 2.1 SQL 구문 오류 확인
아이디 입력창에 작은따옴표(') 입력  
SQL 구문 오류 메시지 확인  
SQL Injection 취약점 존재 확인됨  

## 2.2 테이블 정보 조회
information_schema 또는 show tables 구문 사용  
데이터베이스 테이블 구조 확인  
users 테이블 존재 확인  

##  2.3 사용자 정보 조회
SELECT * FROM users 구문으로 사용자 정보 조회  
admin 계정 정보 확인: 
``` 
username: admin  
password: admin123
isAdmin: true
```

## 2.4 SQL Injection 공격
' OR '1'='1 구문으로 로그인 우회 시도  
쿼리 실행 결과가 TRUE로 반환됨  
전체 사용자 데이터 접근 가능  

## 3. 플래그 획득
admin/admin123 계정으로 로그인  
관리자 권한 확인  
플래그 획득: flag{sql_injection_success}  

## 4. 취약점 설명
사용자 입력값이 SQL 쿼리에 직접 삽입됨  
입력값 검증이 불충분  
SQL Injection 공격에 취약  

## 5. 보안 대책
입력값 검증 및 이스케이프 처리  
준비된 구문(Prepared Statement) 사용  
서버 측에서의 보안 검증 강화  
최소 권한 원칙 적용  
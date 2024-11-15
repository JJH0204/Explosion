# 문제 5 - 파일 업로드 취약점

## 문제 설명
파일 업로드 기능의 취약점을 이용하여 관리자 권한을 획득하고, Base64로 인코딩된 플래그를 찾아내는 문제입니다.

## 풀이 과정

### 1. 소스 코드 분석
파일 업로드 처리 및 검증 코드: 
```
startLine: 153
endLine: 160
```
파일 내용에 'ADMIN_KEY'가 포함되어 있으면 관리자 패널이 활성화됩니다.
### 2. 관리자 패널 접근
관리자 패널 표시 및 플래그 복호화 코드:
```
startLine: 163
endLine: 170
```

파일 내용에 'ADMIN_KEY'가 포함되어 있으면 관리자 패널이 활성화됩니다.

### 3. 파일 업로드 우회
1. 텍스트 파일 생성 (`admin.txt`)
2. 파일 내용에 'ADMIN_KEY' 문자열 포함
3. 파일 업로드
4. Base64로 인코딩된 플래그 획득: `U2FsdGVkX19+MjAyNCtzZWNyZXQra2V5K3RvK2RlY3J5cHQrZmxhZw==`

### 4. 플래그 디코딩
복호화 함수:
```
startLine: 173
endLine: 175
```

Base64 디코딩을 통해 최종 플래그 획득

## 힌트
콘솔에 출력된 힌트들:
```
startLine: 179
endLine: 185
```

## 사용된 기술
- 파일 업로드 취약점 분석
- Base64 디코딩
- 문자열 조작
- 개발자 도구 활용

## 최종 플래그
```
flag{SaltedX_2024+secret+key+to+decrypt+flag}
```
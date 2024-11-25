# 문제 31 - Cronjob Challenge

## 문제 설명
이 문제는 사용자가 SSH를 통해 서버에 접속하여 특정 cron 작업이 수행되는 과정을 분석하고, 주어진 환경에서 적절한 스크립트를 작성하여 플래그를 획득하는 Linux 해킹 문제입니다. cron 작업의 이해와 쉘 스크립트를 활용한 문제 해결 능력을 테스트합니다.

## 풀이 과정

### 1. 접속 정보 확인
문제에서 제공된 SSH 접속 정보는 다음과 같습니다:
- 사용자: `flame31`
- 서버: `192.168.1.54`
- 비밀번호: `1234`

이를 사용해 SSH 클라이언트로 서버에 접속합니다:
```
ssh flame31@192.168.1.54
```

### 2. Cron 작업 분석
Cron 작업은 /usr/bin/cronjob_flame31.sh 스크립트를 실행하며, 해당 스크립트는 매 분마다 실행됩니다. Cron 설정 파일과 스크립트의 내용을 확인합니다:
```
cat /etc/cron.d/cronjob_flame31
cat /usr/bin/cronjob_flame31.sh
```

Cron 설정 내용:
```
@reboot flame31 /usr/bin/cronjob_flame31.sh &> /dev/null
* * * * * flame31 /usr/bin/cronjob_flame31.sh &> /dev/null
```

스크립트 내용:
```
#!/bin/bash
chmod 644 /tmp/t7O6lds9S0RqQh9aMcz6ShpAoZKF7fgv
cat /etc/flame31_pass/flame31 > /tmp/t7O6lds9S0RqQh9aMcz6ShpAoZKF7fgv
```

### 3. 플래그 파일 경로 확인
스크립트는 /etc/flame31_pass/flame31 파일에 저장된 내용을 /tmp/t7O6lds9S0RqQh9aMcz6ShpAoZKF7fgv 파일로 복사합니다. /tmp 디렉터리의 해당 파일을 확인합니다:
```
ls -la /tmp
cat /tmp/t7O6lds9S0RqQh9aMcz6ShpAoZKF7fgv
```

출력 결과:
```
FLAG{ASCIIcode}
```

### 5. 사용된 기술
- Cron 작업 이해: @reboot와 * * * * *와 같은 cron 표현식을 분석.
- 쉘 스크립트 분석: 파일 권한 변경(chmod) 및 내용 복사(cat)와 같은 작업을 분석.
- 파일 시스템 탐색: /tmp 디렉터리에서 스크립트의 결과를 확인.

### 최종 플래그
```
FLAG{cron_job_solved}
```
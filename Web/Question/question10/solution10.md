# 서버 IP 찾기 챌린지 풀이

## 1. 문제 분석
- 웹 서버의 실제 IP 주소를 찾아야 하는 문제
- DNS 조회와 네트워크 도구를 활용해야 함

## 2. 해결 방법

### 네트워크 도구 활용
1. Windows CMD 또는 PowerShell 실행
2. 다음 명령어들을 사용하여 서버 정보 수집:
   ```bash
   nslookup ip.firewall-flame.kro.kr/
   ping ip.firewall-flame.kro.kr/
   tracert ip.firewall-flame.kro.kr/
   ```

### DNS 조회 결과 분석
1. A 레코드 확인
2. 실제 서버 IP 주소 식별
3. CDN이나 프록시 서버가 아닌 실제 원본 서버 IP 찾기

## 3. 플래그 획득
1. 찾아낸 IP 주소(221.166.254.49)를 입력
2. 정답 확인 시 플래그 출력: `flag{answer10}`

## 4. 학습 포인트
1. DNS 조회 방법
2. 네트워크 도구 사용법
3. IP 주소 체계 이해
4. CDN과 실제 서버 IP의 차이점

## 5. 보안 고려사항
- 실제 서비스에서는 서버 IP 주소 노출 최소화
- CDN 활용으로 원본 서버 보호
- DNS 보안 설정 강화

## 최종 플래그
```
flag{answer10}
```
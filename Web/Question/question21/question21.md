# 문제 21 - Decoder Challenge Solution

## 문제 설명
이 문제는 Hash - DCC로 암호화된 메시지를 해독하고 규칙에 따라 플래그를 추출하는 웹 해킹 문제입니다. 제공된 메시지를 디코딩하고 지정된 규칙을 적용하여 플래그를 확인해야 합니다.

## 풀이 과정

### 1. 소스 코드 분석
begin 644 worg_challenge_uudeview
B5F5R>2!S:6UP;&4@.RD*4$%34R`](%5,5%)!4TE-4$Q%"@``
`
end

begin 644 worg_challenge_uudeview: 파일 이름과 권한(644)이 제공됨.
본문: UUencode로 인코딩된 바이너리 데이터.
end: 인코딩 종료를 나타냄.

### 2. UUencode 디코딩
import uu
from io import BytesIO

# UUencoded 데이터
uucode = """
begin 644 example.txt
B5F5R>2!S:6UP;&4@.RD*4$%34R`](%5,5%)!4TE-4$Q%"@``
`
end
"""

# 디코딩 결과를 저장할 버퍼
decoded_output = BytesIO()

# UUdecode 실행
uu.decode(BytesIO(uucode.encode('utf-8')), decoded_output)

# 디코딩 결과 출력
decoded_text = decoded_output.getvalue().decode('utf-8')
print(decoded_text)

### 2-1. 리눅스 UUdecode
uudecode encoded_file.uue


### 3. 플래그 추출
Very simple ;)
PASS = ULTRASIMPLE

## 힌트
1.데이터가 UUencode 형식임을 이해하고 begin과 end를 활용하여 디코딩을 수행합니다.
2.Python, 리눅스 명령어(uudecode), 또는 온라인 도구를 사용하여 UUdecode를 실행할 수 있습니다.
3.디코딩 결과에서 PASS 값이 문제의 플래그로 사용됩니다.

## 사용된 기술
UUencode/UUdecode: 이메일 및 데이터 전송에 사용되는 오래된 바이너리 텍스트 변환 기술.
Python 또는 리눅스 명령어: 디코딩 작업에 필수적.
텍스트 분석: 디코딩된 문자열에서 플래그 추출
apt install sharutils 설치후 > uudecode


## 최종 플래그
```
ULTRASIMPLE
```
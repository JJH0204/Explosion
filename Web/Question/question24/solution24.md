# 문제 24 - Decoder Challenge Solution

## 문제 설명
웹 페이지에서 관리자 계정으로 로그인하여 암호화된 파일을 다운로드 받고, 이를 해독하여 플래그를 찾는 문제입니다.

## 풀이 과정
sudo apt install sharutils  # Debian/Ubuntu
sudo yum install sharutils  # RHEL/CentOS
명령어 없을시 설치

1. 웹 페이지의 소스 코드를 분석하여 관리자 계정 정보를 찾습니다.
   - 난독화된 JavaScript 파일(script24.min.js)을 분석
   - 관리자 계정: admin
   - 비밀번호: adminpass

2. 관리자 계정으로 로그인하여 암호화된 파일을 다운로드 받습니다.
   - UU24encode.txt 파일 다운로드

3. Kali Linux의 uudecode 도구를 사용하여 파일을 디코딩합니다.
   ```bash
   uudecode UU24encode.txt
   cat encoded_flag

   flag{24Challengesflag}
   ```

4. 디코딩된 파일에서 플래그를 찾아 입력합니다.
   - 플래그: flag{24Challengesflag}

### 플래그 추출
디코딩된 파일에서 발견한 플래그: flag{24Challengesflag}

## 힌트
1. 사용자의 비밀번호 찾기
2. script파일 찾기
3. kali linux 사용

## 사용된 기술
- JavaScript 난독화/역난독화
- UUEncoding/UUDecoding
- 웹 소스코드 분석
- Kali Linux 도구 활용

## 최종 플래그
```
flag{24Challengesflag}
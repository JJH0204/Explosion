# 문제 22 - Decoder Challenge Solution

## 문제 설명
File - Insecure storage 1
이 문제는 tgz 파일을 다운로드 받아 압축을 풀고 사용자의 패스워드를 찾는 문제입니다.

## 풀이 과정
주어진 파일을 다운로드 받아 압축을 풀고 파일을 열어보면 사용자의 패스워드를 찾을 수 있습니다.
https://download.sqlitebrowser.org/DB.Browser.for.SQLite-v3.13.1-win64.msi
프로그렘 실행후 signons.sqlite 파일을 열어 moz_logins 테이블을 찾아보면 암호화된 사용자의 패스워드를 찾을 수 있습니다.
wget https://github.com/unode/firefox_decrypt 에서 firefox_decrypt.py 를 실행하여 signons.sqlite를 보면 바로 패스워드 찾을수 있음



### 플래그 추출

Password:F1rstP4sSw0rD

## 힌트
1.사용자의 비밀번호 찾기
2.https://github.com/unode/firefox_decrypt
3.https://download.sqlitebrowser.org/

## 사용된 기술



## 최종 플래그
```
F1rstP4sSw0rD
```
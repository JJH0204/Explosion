# 문제 22 - Decoder Challenge Solution

## 문제 설명
Hash - Message Digest 
이 문제는 해시로 암호화된 문자열을 해독하고 규칙에 따라 플래그를 추출하는 웹 해킹 문제입니다. 제공된 해시를 디코딩하여 원래 문자열을 확인하고, 이를 플래그로 제출해야 합니다.

## 풀이 과정
주어진 데이터의 길이는 32자입니다. MD5 해시는 항상 32자리의 16진수 문자열을 생성하므로, 주어진 데이터는 MD5 해시로 추정됩니다.
```
1번
echo "8f6b1ecc7d7c0377c707ec6913cbbc3d" > hash.txt
hashcat -m 0 hash.txt /usr/share/wordlists/rockyou.txt
```

```
2번
import hashlib

hash_to_crack = "8f6b1ecc7d7c0377c707ec6913cbbc3d"
with open('/usr/share/wordlists/rockyou.txt', 'r') as wordlist:
    for password in wordlist:
        password = password.strip()
        hashed_password = hashlib.md5(password.encode()).hexdigest()
        if hashed_password == hash_to_crack:
            print(f"비밀번호를 찾았습니다: {password}")
            break
```



### 플래그 추출
weak

## 힌트
주어진 문자열 8f6b1ecc7d7c0377c707ec6913cbbc3d는 해시값입니다. 이 값이 어떤 해시 알고리즘(MD5, SHA-1, SHA-256 등)으로 생성된 것인지 식별하고, 이를 크래킹하여 원래의 값을 알아내야 합니다.
해시값의 길이를 기준으로 어떤 알고리즘인지 추정할 수 있습니다:

MD5: 32자 (16바이트, 128비트)
SHA-1: 40자 (20바이트, 160비트)
SHA-256: 64자 (32바이트, 256비트)
주어진 해시는 32자 길이를 가지므로, 이는 MD5로 생성된 해시일 가능성이 높습니다.

## 사용된 기술
john --format=Raw-md5
Hashcat
텍스트 분석: 디코딩된 문자열에서 플래그를 추출.
MD5 해싱 및 크래킹: 해시값을 복원하기 위한 핵심 기술.
Python 및 리눅스 명령어: 해시 크래킹과 분석 작업을 수행하는 데 사용.
사전 공격 및 텍스트 분석: 해시와 원래 데이터를 매핑하기 위한 방법.


## 최종 플래그
```
Flag{hashing_practice}
```
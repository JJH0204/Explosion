
# 문제 34 - Practice Malware

## 문제 설명
이 문제는 주어진 악성코드 샘플을 분석하여 특정 정보를 찾아내는 문제입니다.
두 개의 악성코드 샘플(Lab05-01.dll과 Lab03-01.exe)을 분석하여 각각의 특징과 동작을 파악해야 합니다.

## 풀이 과정

### 1. Lab05-01.dll 분석
#### 1) DLLMain 주소 찾기
- IDA Pro나 다른 디스어셈블러를 사용하여 DLL을 분석
- DLLMain의 주소는 0x1000D02E임을 확인

#### 2) DNS 요청 분석
- 0x10001757 주소의 코드를 분석
- gethostbyname 함수 호출 확인
- DNS 요청 주소: pics.practicalmalwareanalysis.com

### 2. Lab03-01.exe 분석
#### 1) 호스트 기반 표시자
다음 두 가지 중요한 호스트 기반 표시자를 발견:
- 실행 파일명: vmx32to64.exe
- 레지스트리 키: SOFTWARE\Microsoft\Windows\CurrentVersion\Run

#### 2) 네트워크 기반 표시자
- 악성코드가 통신하는 도메인: www.practicalmalwareanalysis.com

### 사용된 도구
- IDA Pro/Ghidra: 정적 분석
- Process Monitor: 호스트 기반 활동 모니터링
- Wireshark: 네트워크 트래픽 분석

### 최종 플래그
FLAG{M4lw4r3_4n4lys1s_Pr0}
# 문제 32 - Malware Challenge

## 문제 설명
이 문제는 FLARE 환경에서 제공된 실행 파일인 Lab16-01.exe의 컴파일 날짜를 찾는 문제입니다. PE 파일 구조에 대한 이해와 적절한 도구를 활용하여 파일의 컴파일 타임스탬프를 확인해야 합니다

## 풀이 과정

### 1. FLARE 환경 설정
문제는 FLARE 가상 환경에서 해결해야 합니다. FLARE VM은 다양한 디지털 포렌식 도구를 포함한 가상 머신 환경으로, PE 파일 분석에 유용합니다.

### 2. 파일 로드
Lab16-01.exe 파일을 FLARE VM 환경으로 가져옵니다.

### 3. 적절한 도구 탐색
PE 파일 구조를 분석할 수 있는 도구를 사용해야 합니다. 다음 도구 중 하나를 사용할 수 있습니다:
- PEview: PE 파일 구조를 시각적으로 분석할 수 있는 도구입니다.
- Detect It Easy (DIE): 파일의 헤더 정보를 쉽게 확인할 수 있는 도구입니다.
- CFF Explorer: PE 파일의 세부 정보를 제공하는 도구입니다.

### 4. Time Date Stamp 확인
PE 파일 헤더에서 Time Date Stamp 값을 찾습니다. PEview를 사용하는 경우 다음 단계를 따릅니다:
1. PEview에서 Lab16-01.exe를 열기.
2. IMAGE_NT_HEADERS 섹션으로 이동.
3. Time Date Stamp 필드 확인.

### 5. Time Date Stamp 확인
Time Date Stamp는 16진수 값으로 저장됩니다. 이를 사람이 읽을 수 있는 날짜로 변환합니다:
- 예: 4EA004F7 → 2011/10/20.

### 6. 결과 제출
도구를 통해 확인된 날짜(2011/10/20)를 입력란에 작성하여 제출합니다.

### 7. 사용된 기술
- PE 파일 구조 이해
- Time Date Stamp 확인 및 해석
- PE 파일 분석 도구 활용

### 최종 플래그
```
FLAG{COMPILE_DATE_CORRECT}
```
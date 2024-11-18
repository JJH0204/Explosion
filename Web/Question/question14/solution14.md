# 문제 14 - 숫자 야구 게임

## 문제 설명
이 문제는 숫자 야구 게임의 규칙을 이해하고, 정답을 맞춰 플래그를 획득하는 도전 과제입니다. 3자리 숫자를 입력하여 스트라이크와 볼의 개수를 확인하며 정답을 찾아야 합니다.

## 풀이 과정

### 1. 게임 규칙 이해
- 입력 값은 정확히 3자리 숫자여야 합니다.
- "스트라이크": 숫자와 위치가 모두 일치하는 경우
- "볼": 숫자는 있지만 위치가 다른 경우

### 2. PHP 코드 분석
question14.php 파일을 확인해보면:
```php
$secretNumber = '680';  // 정답이 680으로 설정되어 있음
```

### 3. 정답 도출
1. 정답은 '680'
2. 입력 예시와 결과:
   - 123 입력 → "0 스트라이크, 0 볼"
   - 601 입력 → "1 스트라이크, 1 볼"
   - 680 입력 → "3 스트라이크, 0 볼" (정답)

### 4. 플래그 획득
정답인 "680"을 입력하면 다음과 같은 플래그를 획득할 수 있습니다:
```
FLAG{number_baseball_success}
```

## 사용된 기술
- 숫자 야구 게임 로직
- PHP를 이용한 서버 사이드 검증
- JSON을 통한 클라이언트-서버 통신
- 입력값 유효성 검사

## 보안 고려사항
1. 입력값 검증: 3자리 숫자만 허용
2. 서버 사이드에서 정답 검증
3. JSON 응답 형식 사용

## 최종 플래그
```
FLAG{number_baseball_success}
```
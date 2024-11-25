# 문제 14 - 숫자 야구 게임

## 문제 설명
이 문제는 숫자 야구 게임의 규칙을 이해하고, 정답을 맞춰 플래그를 획득하는 도전 과제입니다. 3자리 숫자를 입력하여 스트라이크와 볼의 개수를 확인하며 정답을 찾아야 합니다. 시도 횟수가 10번을 초과하면 정답을 공개하고 새로운 게임이 시작됩니다.

## 풀이 과정

### 1. 게임 규칙 이해
- 입력 값은 정확히 3자리 숫자여야 합니다.
- "스트라이크": 숫자와 위치가 모두 일치하는 경우
- "볼": 숫자는 있지만 위치가 다른 경우
- 10번 시도 이후에도 정답을 맞히지 못하면 OUT 메시지와 함께 정답이 공개되며 새 게임이 시작됩니다.

### 2. PHP 코드 분석
question14.php 파일을 확인해보면:
```php
session_start();
if (!isset($_SESSION['secretNumber'])) {
    $_SESSION['secretNumber'] = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
    $_SESSION['attempts'] = 0;
}
```
- 서버가 시작될 때 랜덤으로 생성된 3자리 숫자가 $_SESSION['secretNumber']에 저장됩니다.
- 시도 횟수는 $_SESSION['attempts']를 통해 관리됩니다.

숫자 야구 로직:
- 스트라이크 계산: 같은 위치에서 숫자가 일치하는 경우
- 볼 계산: 다른 위치에서 숫자가 일치하는 경우

시도 횟수 초과 시:
```php
if ($_SESSION['attempts'] > 10) {
    $correctAnswer = $_SESSION['secretNumber'];
    $_SESSION['secretNumber'] = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
    $_SESSION['attempts'] = 0;
    echo json_encode([
        'status' => 'out',
        'message' => "OUT!! 정답은 {$correctAnswer}입니다. 새 게임을 시작하세요!"
    ]);
}
```
10번 시도 후 정답이 공개되고 새 게임이 시작됩니다.

### 3. 정답 도출
1. 서버에서 생성된 정답은 매번 다릅니다. 예를 들어, 정답이 680일 때:
   - 123 입력 → "0 스트라이크, 0 볼"
   - 601 입력 → "1 스트라이크, 1 볼"
   - 680 입력 → "3 스트라이크, 0 볼" (정답)
2. 정답을 맞히면 플래그를 반환합니다:
```php
if ($result['strikes'] === 3) {
    echo json_encode([
        'status' => 'success',
        'flag' => $flag,
        'message' => "축하합니다! 정답({$correctAnswer})을 맞췄습니다!"
    ]);
}
```

### 4. 플래그 획득
정답을 입력하면 다음과 같은 플래그를 획득할 수 있습니다:
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
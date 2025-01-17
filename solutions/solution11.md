# 문제 11 - 로또 번호 예측 챌린지

## 문제 설명
이 문제는 로또 번호 생성 시스템의 취약한 난수 생성 메커니즘을 발견하고 이용하는 웹 해킹 문제입니다.  
서버는 3분마다 새로운 로또 번호를 생성하며, 이 번호를 정확히 맞추면 플래그를 획득할 수 있습니다.  

## 풀이 과정

### 1. (비)정상적인 풀이 방법
1. 웹 페이지에서 1부터 45까지의 숫자 중 6개를 선택합니다.
2. 제한 시간 3분 후 로또번호를 추첨합니다.
3. 3분마다 새로운 기회가 주어지며, 1/8,145,060의 확률로 당첨될 수 있습니다.

### 2. 취약점을 이용한 풀이
1. 프록시 툴(예: Burp Suite)을 사용하여 서버 응답을 관찰합니다.
2. 응답 데이터에 `debug_time` 값이 포함된 것을 발견할 수 있습니다:
```json
{
    "drawnNumbers": [1, 15, 23, 35, 41, 44],
    "correct": false,
    "debug_time": 24012345
}
```

3. 2~3 회 수집한 debug_time 값을 통해 3분마다 1씩 증가한 다는 규칙을 알 수 있습니다.

4. n번째 요청에 사용될 값을 이용해 번호 예측:
```php
<?php
$timeSlot = 예측한 값;  // 서버에서 받은 debug_time 값
mt_srand($timeSlot);

$numbers = [];
while (count($numbers) < 6) {
    $num = mt_rand(1, 45);
    if (!in_array($num, $numbers)) {
        $numbers[] = $num;
    }
}
sort($numbers);
print_r($numbers);
?>
```

5. 예측된 번호를 웹 페이지에서 선택하여 제출합니다.

## 정보
1. 개발자는 가끔 중요한 정보를 외부에 노출하는 실수를 합니다.
2. 대다수의 개발자는 시간값을 활용해 의사난수를 생성합니다.
3. 난수 생성 메커니즘은 시드값이 같으면 항상 동일한 순서로 난수를 생성합니다.
4. 시드 값을 알고 있다면 생성되는 값을 예측 가능할 수 있습니다.

## 사용된 기술
- 취약한 난수 생성: 시간 기반 시드값 사용
- HTTP 통신 분석: 서버 응답 데이터 확인
- PHP mt_rand() 함수의 특성 이해
- 시간 기반 취약점 공격

## 최종 플래그
```
FLAG{L0770_PR3D1C71ON_M4573R}
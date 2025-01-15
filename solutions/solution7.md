# 문제 7 - O 찾기 게임

## 문제 설명
이 문제는 사용자가 0과 O로 구성된 그리드에서 O를 찾아내는 도전 과제입니다. 제한된 시간 안에 올바른 O를 클릭하면 플래그를 획득합니다. 정답을 맞추지 못하거나 제한 시간을 초과하면 게임이 종료되고 다시 시작해야 합니다.

## 풀이 과정

### 1. 문제 조건 분석
- 사용자에게 400칸의 0과 O로 구성된 그리드가 제공됩니다.
- 그리드에서 정확한 O를 찾아 클릭해야 합니다.
- 제한 시간은 20초로 설정되어 있으며, 시간 초과 시 게임이 종료됩니다.
- 잘못된 칸을 클릭하면 정답이 하이라이트되며 게임이 종료됩니다.
- 정답을 맞추면 서버에서 플래그를 반환합니다.

### 2. 게임 조작 방법
1. 게임 시작 버튼을 클릭하여 시작합니다.
2. 그리드에서 O를 찾아 클릭합니다.
3. 정답 클릭 시 플래그를 획득하며 성공 메시지가 표시됩니다.
4. 잘못된 칸을 클릭하거나 제한 시간을 초과하면 게임이 종료되며 다시 시작해야 합니다.

### 3. 힌트 기능 사용
1. 페이지소스를 확인하여 힌트의 위치를 확인합니다.
1. 브라우저의 개발자 도구를 엽니다. (F12 또는 Ctrl+Shift+I)
2. Console 탭에서 hint를 입력하고 엔터를 누릅니다.
3. 정답 위치가 강조 표시됩니다.
4. 힌트를 확인한 후, 정답을 클릭하여 문제를 해결합니다.

### 4. 제출 및 검증
1. 정답을 클릭하면 서버에서 플래그를 반환합니다.
2. 반환된 플래그를 확인하여 문제를 해결합니다.
3. 플래그는 페이지 소스 코드에 나타나지 않으며 서버 통신을 통해만 얻을 수 있습니다.

### 5. 사용된 기술
- 게임 구현: HTML, CSS, JavaScript를 사용하여 0과 O로 구성된 그리드를 생성.
- 타이머 및 이벤트 처리: JavaScript로 제한 시간과 클릭 이벤트를 관리.
- 힌트 기능: 콘솔에서 hint 명령을 입력하면 정답 위치를 강조 표시.
- 서버와 통신: PHP를 사용하여 정답을 맞췄을 때만 플래그를 반환.
- 보안: 플래그는 서버에서 동적으로 생성되며 클라이언트 측에서는 숨겨집니다.

### 최종 플래그
```
FLAG{o_finder_challenge}
```
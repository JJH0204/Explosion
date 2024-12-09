# Flame WarGame

Flame WarGame은 웹 보안 학습을 위한 실전형 워게임 플랫폼입니다.  
40개 이상의 단계별 문제를 통해 다양한 웹 취약점을 학습하고 실습할 수 있습니다.

## 주요 기능 ✨

### 1. 단계별 학습 시스템 📚

| 특징 | 설명 |
|:---|:---|
| 🎯 **다양한 문제** | 40개 이상의 웹 해킹 문제 제공 |
| 📈 **체계적인 커리큘럼** | 난이도별로 구성된 학습 경로 |
| 🔬 **실전형 환경** | 실무와 유사한 취약점 실습 환경 |

### 2. 사용자 시스템 👥

| 특징 | 설명 |
|:---|:---|
| 🔐 **계정 관리** | 회원가입 및 로그인 시스템 |
| 📊 **진행 현황** | 개인별 학습 진도 추적 기능 |
| ⚙️ **관리자 기능** | 시스템 모니터링 및 관리 도구 |

### 3. 기술 스택 🛠️

| 분야 | 기술 | 설명 |
|:---:|:---|:---|
| **Frontend** | ![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=flat-square&logo=html5&logoColor=white) ![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=flat-square&logo=css3&logoColor=white) ![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat-square&logo=javascript&logoColor=black) | 웹 기반 사용자 인터페이스 구현 |
| **Backend** | ![Apache](https://img.shields.io/badge/Apache-D22128?style=flat-square&logo=apache&logoColor=white) | 웹 서버 및 서비스 호스팅 |
| **Security** | 🔒 `.htaccess` <br> 🛡️ 보안 기능 <br> 📊 취약점 테스트 | - 접근 제어 관리<br>- 보안 기능 구현<br>- 웹 취약점 실습 환경 |

## 프로젝트 구조 📂

```
Flame_WarGame/
├── 📁 bak/                  # 백업 파일 보관
├── 🛠️ setup_script/         # 서버 초기 설정 스크립트
├── 📝 solutions/           # 문제 풀이 및 해설
└── 🌐 Web/                 # 메인 웹 애플리케이션
    ├── 📦 assets/          # 웹 리소스
    │   ├── 🖼️ images/      # 이미지 리소스
    │   ├── 📜 js/          # JavaScript 파일
    │   ├── ⚙️ php/         # PHP 서버 코드
    │   └── 🎨 styles/      # CSS 스타일시트
    ├── 💾 data/            # 설정 및 데이터 파일
    └── ❓ Question/        # 워게임 문제 파일
```

각 디렉터리 설명:
- `bak`: 시스템 백업 및 복구 파일
- `setup_script`: 서버 환경 구성 및 초기 설정 스크립트
- `solutions`: 각 문제별 풀이 방법과 상세 해설
- `Web`: 웹 애플리케이션 루트 디렉터리
  - `assets`: 웹 사이트 구성 요소
  - `data`: 시스템 설정 및 사용자 데이터
  - `Question`: 단계별 워게임 문제 모음

## 설치 및 실행 🚀

### Docker 배포 방식 🐳

| 단계 | 설명 |
|:---|:---|
| 📦 **컨테이너 환경** | Linux 기반 컨테이너 환경 제공 |
| 🔄 **자동화된 설치** | 소스 코드 자동 다운로드 및 웹 서비스 구성 |
| 🌐 **즉시 실행** | 사전 구성된 환경으로 즉시 서비스 실행 가능 |

### 배포 계획 📋

```bash
# Docker Hub에서 이미지 다운로드
$ docker pull [organization]/flame-wargame:latest

# 컨테이너 실행
$ docker run -d -p 80:80 [organization]/flame-wargame:latest
```

> 💡 **참고사항**
> - Docker Hub를 통해 컨테이너 이미지 배포 예정
> - 환경 설정과 의존성이 모두 포함된 이미지 제공
> - 간단한 명령어로 즉시 서비스 구동 가능

### 소스 빌드 방법

```bash
$ git clone https://github.com/JJH0204/Flame_WarGame.git
$ cd ./Flame_WarGame
$ docker build -t flame-wargame .
$ docker run -d -p 80:80 -p 3307:3307 flame-wargame
```

## 개발 및 유지보수

### 프로젝트 관리 

- **개발팀**: Team Firewall
- **작업 관리**: [TASK](./TASK.md) 를 통해 진행 상황 추적
- **이슈 관리**: 버그 리포트 및 기능 요청 관리

### 기여하기 

1. **버그 리포트**
   - 보안 취약점이나 버그 발견 시 관리자에게 보고
   - [TASK](./TASK.md)에서 현재 알려진 이슈 확인 가능

2. **개발 참여**
   - [TASK](./TASK.md)에서 진행 중인 작업 확인
   - 새로운 기능 제안 및 개발 참여 가능

> **참고**: 프로젝트의 진행 상황과 예정된 업데이트는 [TASK](./TASK.md)에서 확인하실 수 있습니다.

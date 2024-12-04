# Flame WarGame 작업 목록 📋

## 진행 상태 표시
- 🔄 진행 중
- ✅ 완료됨 (~~취소선~~으로 표시)
- ⏳ 대기 중
- ❌ 보류/취소

## 핵심 작업 영역

### 1. 보안 강화 🛡️
- ⏳ WAF 구현 및 디렉터리 보호
  - [ ] assets 폴더 보호
  - [ ] data 폴더 보호 (팝업창 문제 호환성 검토 필요)
  - [ ] Question 폴더 보호 (php 제외)
- 🔄 계정 보안
  - [ ] flame, admin 패스워드 변경
  - [ ] 관련 PHP 코드 수정

### 2. 시스템 최적화 ⚡
- 🔄 UI/UX 개선
  - [ ] 화면 레이아웃 최적화
  - [ ] 모바일 환경 지원
- ⏳ 코드 정리
  - [ ] CSS 중복 코드 제거
  - [ ] HTML 인라인 스타일 제거

### 3. 인프라 구축 🌐
- 🔄 Docker 구현
  - [ ] 컨테이너화 작업
  - [ ] 배포 자동화
- ⏳ 클라우드 서버
  - [ ] 유료 도메인 적용
  - [ ] 수익화 전략 수립

### 4. 콘텐츠 개선 📚
- 🔄 문제 업데이트
  - [ ] 낮은 퀄리티 문제 제거
  - [ ] 중급 이상 문제 추가
  - [ ] 전체 문제 수 확장
- ⏳ 다국어 지원
  - [ ] 영어 버전 추가

### 5. 자동화 시스템 ⚙️
- ⏳ 문제 관리
  - [ ] 문제 출제 자동화
  - [ ] 빌드 자동화 시스템

## 긴급 수정 사항 🚨
1. 🔄 사용자 계정 조회 시스템 (ID 기준) - #25
2. 🔄 플래그 검증 버그 수정 - #13
3. 🔄 29번 문제 버그 수정
4. 🔄 Admin 페이지 25번 문제 SQL 오류
5. ⏳ solution 파일 격리 및 접근 제한
6. ~~⏳ config.json DB 마이그레이션~~

## 파일 정리 작업 📁

### 사용 중인 파일 목록
#### Web 루트
- `flame.html`, `flameAdmin.html`, `index.html`

#### assets/php
```php
checkAdmin.php      # 관리자 인증
checkSession.php    # 세션 관리
login.php           # 로그인 처리
logout.php          # 로그아웃 처리
ranking.php         # 랭킹 시스템
saveImage.php       # 이미지 업로드
signup.php          # 회원가입
signupFlameDB.php   # 회원가입(DB_flame)
signupSqlDB.php     # 회원가입(DB_sql)
userInfo.php        # 사용자 정보
```

#### assets/js
- `admin.js` - 관리자 기능
- `main.js` - 메인 로직

#### assets/styles
- `flameAdmin.css` - 관리자 페이지 스타일
- `flame.css` - 메인 페이지 스타일
- `login.css` - 로그인 페이지 스타일

> 💡 **참고**: 불필요한 파일은 `bak` 디렉토리로 이동 후 주석 추가
> 파일의 역할을 잘 모르겠으면 물어보기(무작정 삭제 x)

## 작명 규칙 📝

### 1. 파일 명명 규칙

#### 웹 파일
- **HTML 파일**: 소문자, 페이지 중심 (예: `flame.html`, `index.html`)
- **CSS 파일**: 소문자, 연관된 HTML 파일명과 동일 (예: `flame.css`)
- **JS 파일**: 소문자, 기능 중심 (예: `flame.js`)
- **PHP 파일**: 소문자, camelCase (예: `lowerCamelCase.php`)

#### 디렉토리
- 소문자 사용
- 명확한 용도 표현
- 언더스코어(_) 사용 가능
예: `assets`, `question`, `setup_script`

### 2. 데이터베이스 명명 규칙

#### 데이터베이스
- 접두어 `DB_` 사용
- 소문자 사용
예: `DB_flame`, `DB_challenge`

#### 테이블
- 대문자 스네이크 케이스
- 명확한 용도 표현
예: `USER_INFO`, `CHALLENGE_INFO`, `CLEARED_STAGE`

#### 필드(컬럼)
- 소문자 스네이크 케이스
- 테이블명과 연관성 있게 명명
예:
- `id`: 기본 키
- `challenge_id`: 외래 키
- `challenge_score`: 일반 필드
- `total_cleared_stage`: 통계 필드

> 💡 **참고**: 
> - 모든 식별자는 영문 사용
> - 약어 사용은 최소화
> - 일관성 있는 명명 규칙 준수

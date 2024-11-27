[] !!!!!!!!!!![[[ 주석 ]]]!!!!!!!!!!!! 
    모든 사용하는 파일에 무슨 파일인지 주석 달기!

[] 보안 강화
    [] WAF 및 디렉터리 보호
    [] flame, flame_admin 패스워드 변경 + 관련 php 코드 수정

[] 최적화
    [] 화면 최적화

    [] 코드 최적화
        css 파일 내 중복된 코드(css에 정의된 스타일이 html에서 다시 정의되는 것도 포함)

    [] 파일 최적화
        [] root(Web)
            [flame.html, flameadmin.html, index.html]

            [] test 디렉토리 (테스트 폴더에서 작업 후 오류 없을 시 -> 실제 파일에 업데이트)
            
            [] Question (실제 문제 로직 폴더)

            [] data (문제 md, 정보, 플래그 폴더)

        [] assets
            [] php
                [check_admin.php, check_storage.php, checkSession.php, get_cleared_stages.php, login.php, logout.php, ranking.php,
                save-image.php, saveClearedCard.php, signup_testDB.php, signup_userDB.php, signup.php, user_info.php]

            [] js
                [assets/js/admin/admin.js, assets/js/admin/main.js]

            [] styles
                [assets/styles/admin/admin1.css, assets/styles/admin/admin2.css, assets/styles/admin/main1.css, assets/styles/admin/main2.css,
                assets/styles/etc/login_styles.css]

            [] images (모두 사용)

[] 언어 확장
    [] 영어

[] 모바일 최적화
    - 아이디어 종합(모바일 어플리케이션 구현 / ...)

[] 프로젝트 빌드 안정성 강화
    [] docker 기반 프로젝트로 전환

[] 문제 업데이트
    [] 낮은 퀄리티 문제 제거

    [] 중급 이상 문제 추가

    [] 전체 문제 수 확장

[] 클라우드 서버 구축
    [] 유료 도메인 적용

    [] 수익화 전략 수립 밎 적용

[] 문제 관리 시스템 구축
    [] 문제 출제 자동화??

    [] 빌드 자동화 시스템



--------------------------------------------------------------------------------------------------------------------------------------
파일 확인해주세요.(필요없다 싶은건 일단 bak 디렉토리로 옮겨주세요.)
bak 디렉토리로 옮긴 파일은 추후 삭제 예정 + bak 주석 추가해 주세요.
사용하는 파일은 삭제 후 위쪽 List에 추가해 주세요. (파일 안에 무슨 용도의 파일인지 주석 추가 <-- 매우 중요)

[] root 디렉토리
    [pepe.png,  -> assets/images/etc 폴더로 옮긴후 관련 파일에서 이미지 경로 수정
    ch20.tgz,   -> 관련 폴더로 옮긴후 관련 파일에서 파일 경로 수정
    package-lock.json, package.json,    -> assets/js/etc 폴더로 옮긴후 관련 파일에서 js 경로 수정
    ]

[] php 파일
    [challenges.php, create_flag.php, fetchClearedCards.php(bak), set_game_request.php, verifyAnswer.php]

[] 디렉토리
    [assets/js/etc, assets/js/main,
    assets/php/main,
    assets/styles/etc, assets/styles/main,
    node_modules,
    Qustion/test,
    node_modules(이거 진짜 뭐임?)]


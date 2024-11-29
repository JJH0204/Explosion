#!/bin/bash
set -e

# 사용자 shell 제한 설정
/setup_script/usershell.sh

# 컨테이너를 계속 실행
exec "$@"
tail -f /dev/null

#!/bin/bash


# 1. 제한된 셸(rbash)을 활성화합니다.
# 사용자에게 rbash를 기본 셸로 설정
USERNAME="Challenge21"  # 제한할 사용자 이름
sudo usermod --shell /bin/rbash $USERNAME

# 2. 사용자 홈 디렉토리를 제한된 환경으로 구성합니다.
USER_HOME="/home/$USERNAME"
if [ ! -d "$USER_HOME" ]; then
    echo "사용자 $USERNAME의 홈 디렉토리가 없습니다. 새로 생성합니다."
    sudo mkdir -p "$USER_HOME"
    sudo chown $USERNAME:$USERNAME "$USER_HOME"
fi

# 3. 제한된 환경에 필요한 명령어만 심볼릭 링크로 허용합니다.
BIN_DIR="$USER_HOME/bin"
sudo mkdir -p "$BIN_DIR"

# 제한된 환경에서 사용할 명령어
ALLOWED_COMMANDS=("vi" "uudecode" "ls")

# 기존 링크 제거 및 새로운 링크 생성
sudo rm -f $BIN_DIR/*
for cmd in "${ALLOWED_COMMANDS[@]}"; do
    if command -v "$cmd" > /dev/null; then
        sudo ln -s "$(command -v "$cmd")" "$BIN_DIR/$cmd"
    else
        echo "경고: $cmd 명령어를 찾을 수 없습니다. 스크립트를 확인하세요."
    fi
done

# 4. 사용자 환경 파일을 제한된 경로로 설정합니다.
PROFILE_FILE="$USER_HOME/.bash_profile"
sudo bash -c "cat > $PROFILE_FILE" <<EOF
export PATH=$BIN_DIR
export SHELL=/bin/rbash
alias ls='ls --color=auto'
alias ll='ls -la'
EOF

# 권한 설정
sudo chown $USERNAME:$USERNAME "$PROFILE_FILE"
sudo chmod 644 "$PROFILE_FILE"

# 5. 필요하지 않은 파일 제거 및 홈 디렉토리 권한 제한
sudo rm -f "$USER_HOME/.bashrc"
sudo chmod 750 "$USER_HOME"

echo "제한된 셸 환경이 사용자 $USERNAME에 대해 설정되었습니다."
echo "사용 가능한 명령어: ${ALLOWED_COMMANDS[*]}"


class Pokemon {
    constructor(name, hp) {
        this.name = name;
        this.hp = hp;
        this.currentHP = hp;
    }

    takeDamage(damage) {
        this.currentHP -= damage;
        if (this.currentHP < 0) this.currentHP = 0;
    }

    isFainted() {
        return this.currentHP === 0;
    }
}

// 플레이어와 적 포켓몬 생성
const pikachu = new Pokemon("Pikachu", 100);
const charmander = new Pokemon("Charmander", 100);

// HP 바 업데이트 함수
function updateHPBar(pokemon, elementId) {
    const hpBar = document.getElementById(elementId);
    const hpPercentage = (pokemon.currentHP / pokemon.hp) * 100;
    hpBar.style.width = hpPercentage + "%";
}

// 공격 버튼 클릭 시 실행되는 함수
function playerAttack() {
    const damage = Math.floor(Math.random() * 20) + 5; // 5~25 사이의 랜덤한 데미지
    charmander.takeDamage(damage);
    updateHPBar(charmander, "enemyHP");
    logMessage(`${pikachu.name} attacked ${charmander.name} for ${damage} damage!`);
    
    // 적 포켓몬 기절 체크
    if (charmander.isFainted()) {
        logMessage(`${charmander.name} has fainted! You won!`);
    } else {
        enemyAttack();
    }
}

// 적의 공격 함수
function enemyAttack() {
    const damage = Math.floor(Math.random() * 20) + 5;
    pikachu.takeDamage(damage);
    updateHPBar(pikachu, "playerHP");
    logMessage(`${charmander.name} attacked ${pikachu.name} for ${damage} damage!`);

    // 플레이어 포켓몬 기절 체크
    if (pikachu.isFainted()) {
        logMessage(`${pikachu.name} has fainted! You lost!`);
    }
}

// 메시지 로그 출력 함수
function logMessage(message) {
    console.log(message);
}

// 초기 상태 업데이트
window.onload = function() {
    updateHPBar(pikachu, "playerHP");
    updateHPBar(charmander, "enemyHP");
};
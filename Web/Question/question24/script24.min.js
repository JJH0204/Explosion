var _0x4e8a=['value','getElementById','username','password','loginForm','downloadSection','admin','adminpass','style','display','none','block','loginMessage','backgroundColor','#4d0000','color','#ff4d4d','textContent','잘못된 사용자 이름 또는 비밀번호입니다.','trim','userInput'];

function login(){
    var a=document[_0x4e8a[1]](_0x4e8a[2])[_0x4e8a[0]];
    var b=document[_0x4e8a[1]](_0x4e8a[3])[_0x4e8a[0]];
    var c=document[_0x4e8a[1]](_0x4e8a[4]);
    var d=document[_0x4e8a[1]](_0x4e8a[5]);
    var e=document[_0x4e8a[1]](_0x4e8a[12]);
    
    if(a===_0x4e8a[6]&&b===_0x4e8a[7]){
        c[_0x4e8a[8]][_0x4e8a[9]]=_0x4e8a[10];
        d[_0x4e8a[8]][_0x4e8a[9]]=_0x4e8a[11];
    } else {
        e[_0x4e8a[8]][_0x4e8a[13]]=_0x4e8a[14];
        e[_0x4e8a[8]][_0x4e8a[15]]=_0x4e8a[16];
        e[_0x4e8a[17]]=_0x4e8a[18];
        e[_0x4e8a[8]][_0x4e8a[9]]=_0x4e8a[11];
    }
}   

async function checkFlag(){
    const userFlag = document.getElementById('userInput').value.trim();
    const resultElement = document.getElementById('result');
    
    try {
        const response = await fetch('question24.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'flag=' + encodeURIComponent(userFlag)
        });

        const data = await response.json();

        if (data.success) {
            resultElement.style.color = '#00ff7f';
            resultElement.textContent = `정답입니다! 플래그: ${data.flag}`;
        } else {
            resultElement.style.color = '#ff4d4d';
            resultElement.textContent = '틀렸습니다. 다시 시도하세요.';
        }
    } catch (error) {
        resultElement.style.color = '#ff4d4d';
        resultElement.textContent = '오류가 발생했습니다. 다시 시도해주세요.';
    }
}
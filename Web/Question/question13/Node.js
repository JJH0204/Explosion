const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors'); // CORS 허용
const app = express();

app.use(cors()); // 모든 출처에서 요청 허용
app.use(bodyParser.json());

const FLAG = "FLAG{secure_flag_generation}";

app.post('/validate', (req, res) => {
    const input = req.body.input;
    console.log(`Received Input: ${input}`); // 디버깅용 입력값 출력

    // 조건: 입력 길이가 32 초과 + 특정 패턴 포함
    if (input.length > 32 && input.includes("BOFB")) {
        res.send(FLAG); // 조건 만족 시 플래그 반환
    } else {
        res.status(403).send('조건 불충분');
    }
});

const PORT = 3000;
app.listen(PORT, () => {
    console.log(`서버가 ${PORT}번 포트에서 실행 중입니다.`);
});

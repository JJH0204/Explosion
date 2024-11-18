
const users = [
    { id: 1, username: 'admin', password: 'admin123', isAdmin: true },
    { id: 2, username: 'user1', password: 'password123', isAdmin: false },
    { id: 3, username: 'user2', password: 'password456', isAdmin: false }
];

const tables = [
    { name: 'users', columns: ['id', 'username', 'password', 'isAdmin'] },
    { name: 'products', columns: ['id', 'name', 'price'] },
    { name: 'orders', columns: ['id', 'user_id', 'product_id', 'date'] }
];

const FLAG = "flag{sql_injection_success}";

function simulateSQL(username, password) {
    const query = `SELECT * FROM users WHERE username='${username}' AND password='${password}'`;
    // console.log('실행된 쿼리:', query);

    // 단순 SQL 문법 에러 체크
    if (username.includes("'") && !username.toLowerCase().includes("or") && !username.toLowerCase().includes("select")) {
        return {
            success: false,
            query: query,
            error: "SQL 구문 오류"
        };
    }

    // 특정 테이블 데이터 조회 시도
    if ((username.toLowerCase().includes("select") && username.toLowerCase().includes("from users")) ||
        (username.toLowerCase().includes("select") && username.toLowerCase().includes("from `users`")) ||
        (username.toLowerCase().includes("select") && username.toLowerCase().includes("from [users]")) ||
        (username.toLowerCase().includes("select") && username.toLowerCase().includes("from \"users\"")) ||
        username.toLowerCase().includes("union select") ||
        username.toLowerCase().includes("union all select") ||
        (username.toLowerCase().includes("select") && username.toLowerCase().includes("join users")) ||
        (username.toLowerCase().includes("select") && username.toLowerCase().includes("from (select")) ||
        username.toLowerCase().includes("users;") ||
        username.toLowerCase().includes("users--") ||
        username.toLowerCase().includes("users/*") ||
        (username.toLowerCase().includes("select") && username.toLowerCase().includes("subselect")) ||
        username.toLowerCase().includes("users where") ||
        username.toLowerCase().includes("users group") ||
        username.toLowerCase().includes("users order") ||
        username.toLowerCase().includes("users limit")) {
        return {
            success: true,
            query: query,
            message: "쿼리 실행 결과:",
            result: users
        };
    }

    // 테이블 정보 조회 시도
    if (username.toLowerCase().includes("information_schema") ||
        username.toLowerCase().includes("show tables") ||
        username.toLowerCase().includes("select table_name") ||
        username.toLowerCase().includes("select * from sqlite_master") ||
        username.toLowerCase().includes("select name from sqlite_master") ||
        username.toLowerCase().includes("select * from sys.tables") ||
        username.toLowerCase().includes("select * from sysobjects") ||
        username.toLowerCase().includes("select * from pg_tables") ||
        username.toLowerCase().includes("desc ") ||
        username.toLowerCase().includes("describe ") ||
        username.toLowerCase().includes("show columns") ||
        username.toLowerCase().includes("show schema") ||
        username.toLowerCase().includes("select catalog_name") ||
        username.toLowerCase().includes("select table_schema")) {
        return {
            success: true,
            query: query,
            message: "쿼리 실행 결과:",
            result: tables
        };
    }

    // SQL Injection 공격 패턴 체크
    if (username.toLowerCase().includes("' or") || 
        username.toLowerCase().includes("' and") ||
        username.toLowerCase().includes("' union") ||
        username.toLowerCase().includes("' having") ||
        username.toLowerCase().includes("' group by") ||
        username.toLowerCase().includes("' order by") ||
        username.toLowerCase().includes("' limit") ||
        username.toLowerCase().includes("' like") ||
        username.toLowerCase().includes("' in") ||
        username.toLowerCase().includes("' exists") ||
        username.toLowerCase().includes("' not") ||
        username.toLowerCase().includes("' between") ||
        username.toLowerCase().includes("/*") ||
        username.toLowerCase().includes("--") ||
        username.toLowerCase().includes("#") ||
        username.toLowerCase().includes(";") ||
        username.toLowerCase().includes("\\") ||
        username.toLowerCase().includes("1=1") ||
        username.toLowerCase().includes("1 = 1") ||
        username.toLowerCase().includes("true") ||
        username.toLowerCase().includes("false") ||
        username.toLowerCase().includes("sleep") ||
        username.toLowerCase().includes("waitfor") ||
        username.toLowerCase().includes("delay") ||
        username.toLowerCase().includes("benchmark") ||
        username.toLowerCase().includes("pg_sleep") ||
        username.toLowerCase().includes("xp_cmdshell") ||
        username.toLowerCase().includes("exec") ||
        username.toLowerCase().includes("' +") ||
        username.toLowerCase().includes("' -") ||
        username.toLowerCase().includes("@@version") ||
        username.toLowerCase().includes("database()") ||
        username.toLowerCase().includes("schema()") ||
        username.toLowerCase().includes("load_file")) {
        return {
            success: true,
            query: query,
            message: "쿼리 실행 결과: TRUE",
            result: { affected_rows: users.length }
        };
    }

    // 일반 로그인 시도
    const user = users.find(u => u.username === username && u.password === password);
    return {
        success: !!user,
        query: query,
        result: user,
        isAdmin: user?.isAdmin || false
    };
}

document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const resultDiv = document.getElementById('result');

    const queryResult = simulateSQL(username, password);

    if (queryResult.success) {
        if (queryResult.result === users || Array.isArray(queryResult.result)) {
            resultDiv.innerHTML = `
                <div class="success">
                    ${queryResult.message}
                    <pre>${JSON.stringify(queryResult.result, null, 2)}</pre>
                </div>
            `;
        } else if (queryResult.result.affected_rows) {
            resultDiv.innerHTML = `
                <div class="success">
                    ${queryResult.message}
                </div>
            `;
        } else if (queryResult.result.isAdmin) {
            resultDiv.innerHTML = `
                <div class="success">
                    로그인 성공
                    ${FLAG}
                </div>
            `;
        } else {
            resultDiv.innerHTML = `
                <div class="success">
                    로그인 성공
                </div>
            `;
        }
    } else {
        resultDiv.innerHTML = `
            <div class="error">
                ${queryResult.error || '로그인 실패'}
            </div>
        `;
    }
});
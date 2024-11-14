function checkAdmin() {
    const input = document.getElementById("inputString").value;
    const result = document.getElementById("result");

    if (input === "admin") {
        result.textContent = "ZmxhbWUxYW5zd2Vy";
    } else {
        result.textContent = "error";
    }
}
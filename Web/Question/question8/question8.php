<script>
    // 개발자 도구 열기
    setTimeout(function() {
        // Chrome, Firefox 등에서 작동
        window.open('about:blank', '', 'width=800,height=600').close();
        window.dispatchEvent(new KeyboardEvent('keydown', {
            key: 'F12',
            keyCode: 123,
            bubbles: true
        }));
    }, 100);

    // index.html로 리다이렉트
    window.location.href = "/index.html";
</script>

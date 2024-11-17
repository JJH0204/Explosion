# 문제 16 - Steganography Challenge

## 문제 설명
이 문제는 사용자가 스테가노그래피(Steganography) 기술을 이해하고, 이미지에 숨겨진 플래그를 추출하여 문제를 해결하는 도전 과제입니다. LSB(Least Significant Bit) 기법으로 이미지에 숨겨진 메시지를 복원하여 플래그를 획득하는 것이 목표입니다.

## 풀이 과정

### 1. 이미지 파일 다운로드
제공된 이미지를 다운로드합니다:
- 이미지 파일: stego_challenge_with_flag.png
이 이미지는 LSB 기법으로 플래그가 숨겨져 있습니다.

### 2. LSB 복호화 스크립트 작성
이미지에서 숨겨진 메시지를 복호화하기 위해 Python 스크립트를 작성합니다. 스크립트는 다음과 같은 작업을 수행합니다:
1. 이미지 파일을 읽어 픽셀 데이터를 가져옵니다.
2. 픽셀의 R 채널의 LSB를 추출하여 이진 문자열로 변환합니다.
3. 종료 마커(11111111)를 만나면 데이터를 ASCII 문자열로 변환하여 출력합니다.

Python 스크립트 예제:
```py
from PIL import Image

# LSB를 사용한 이미지에서 숨겨진 메시지 추출 함수
def decode_lsb(image_path):
    img = Image.open(image_path)
    img_data = img.getdata()
    binary_message = ""

    # 픽셀의 R 채널에서 LSB 추출
    for pixel in img_data:
        r = pixel[0]
        binary_message += str(r & 1)

    # 종료 마커 확인
    end_marker = "11111111"
    if end_marker in binary_message:
        binary_message = binary_message[:binary_message.index(end_marker)]  # 종료 마커 이후 제거

    # 8비트씩 나누어 ASCII로 변환
    bytes_data = [binary_message[i:i+8] for i in range(0, len(binary_message), 8)]
    decoded_text = "".join([chr(int(byte, 2)) for byte in bytes_data if int(byte, 2) != 0])
    
    return decoded_text

# 실행
if __name__ == "__main__":
    image_path = "stego_challenge_with_flag.png"  # 분석할 이미지 파일
    hidden_message = decode_lsb(image_path)
    print(f"숨겨진 메시지: {hidden_message}")
```

### 3. 플래그 추출
위 스크립트를 실행하여 플래그를 추출합니다:
- 스크립트 실행:
```
python3 decode_lsb.py
```
- 출력 결과:
```
숨겨진 메시지: FLAG{hidden_with_lsb}
```

## 사용된 기술
- 스테가노그래피 기법: LSB(Least Significant Bit)를 사용하여 이미지의 픽셀 데이터에 플래그를 숨기고 복원.
- Python 스크립트: Pillow 라이브러리를 사용하여 이미지 데이터를 처리, 이진 데이터를 ASCII로 변환하여 숨겨진 메시지를 추출.
- 종료 마커 사용: 데이터의 끝을 구분하기 위해 11111111 종료 마커를 삽입.

## 최종 플래그
```
FLAG{hidden_with_lsb}
```
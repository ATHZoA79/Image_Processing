<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @vite('resources/css/app.css')
</head>

<body>
    <header></header>
    <main class="w-full flex justify-center">
        <div class="m-2 p-5">
            <label for="">1. </label>
            <input type="file" name="" accept="image/*">
            {{-- <img src="" alt="" id="preview_1" width="200" height="200"> --}}
        </div>
        <div>
            <h2>Original Image</h2>
            <img src="" alt="" id="preview_1" style="border: 1px solid #000;">
        </div>
        <div>
            <label>Rezise</label><input min="1" max="100" value="80" type="range" name="resize" id="resize">
            <label>Quality</label><input min="1" max="100" value="80" type="range" name="quality" id="quality">
        </div>
        <div>
            <label>CropLeft</label><input type="range" step="1" name="cropleft" id="cropleft">
            <label>CropTop</label><input type="range" step="1" name="croptop" id="croptop">
        </div>
        <div>
            <label>CropWidth</label><input type="range" step="1" name="cropwidth" id="cropwidth">
            <label>CropHeight</label><input type="range" step="1" name="cropheight" id="cropheight">
        </div>
        <h2>Compressed Image</h2>
        <div><b>Size:</b> <span id="size"></span></div>
        <img id="compressedImage" />
    </main>

    <script>
        const originalImage = document.querySelector("img");
        const input = document.querySelector("input[type=file]");
        console.log(originalImage, input);
        // 取得DOM物件
        const resizingElement = document.querySelector("#resize");
        const qualityElement = document.querySelector("#quality");
        const cropLeftElement = document.querySelector("#cropleft");
        const cropTopElement = document.querySelector("#croptop");
        const cropWidthElement = document.querySelector("#cropwidth");
        const cropHeightElement = document.querySelector("#cropheight");
        // 設定初始條件
        var resizingFactor = 0.8;
        var quality = 0.8;
        var cropLeft = 0;
        var cropTop = 0;
        var cropWidth = originalImage.width;
        var cropHeight = originalImage.height;

        // initializing the compressed image
        compressImage(originalImage, resizingFactor, quality);


        input.addEventListener('change', (e) => {
            // 1.建立FileReader準備讀取資料
            const reader = new FileReader();
            console.log(input.files[0]);

            // 2.確認有資料載入，將資料轉換成dataURL形式
            if (input.files) {
                reader.readAsDataURL(input.files[0]);
                console.log(reader);
            }

            reader.addEventListener("load", () => {
                // convert image file to base64 string
                // 3.將轉換過後的資料賦予給img標籤的src
                originalImage.src = reader.result;
                // 4.將資料包裝成blob物件儲存
                let testBlob = dataURItoBlob(reader.result);
                console.log(testBlob);
                compressImage(originalImage, resizingFactor, quality);
            }, false);
        });

        function dataURItoBlob(dataURI) {
            // **dataURL形式 : data:image/png;base64,iVBORw0KGgoAAAANSUh...
            // convert base64/URLEncoded data component to raw binary data held in a string
            var byteString;
            if (dataURI.split(',')[0].indexOf('base64') >= 0)
            // 陣列:["data:image/png;base64", "iVBORw0KGgoAAAANSUh..."]
                byteString = atob(dataURI.split(',')[1]);
            else
            // 解碼dataURI，建議用decodeURI() 
            // 1.取陣列:["iVBORw0KGgoAAAANSUh..."]並解碼
                byteString = unescape(dataURI.split(',')[1]);

            // separate out the mime component
            // 2.取得字元陣列：["image/png"]
            var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];

            // write the bytes of the string to a typed array
            var ia = new Uint8Array(byteString.length);
            for (var i = 0; i < byteString.length; i++) {
                // 3.將所有字元轉換成ASCII號碼
                ia[i] = byteString.charCodeAt(i);
            }

            // 4.建立blob物件儲存資料
            let blob = new Blob([ia], {
                type: mimeString
            });

            // 5.建立file物件方便傳送
            let file = new File([blob], "name", { type: "image/jpeg", });
            return file;
        }

        resizingElement.oninput = (e) => {
            resizingFactor = parseInt(e.target.value) / 100;
            compressImage(originalImage, resizingFactor, quality);
        };
        qualityElement.oninput = (e) => {
            quality = parseInt(e.target.value) / 100;
            compressImage(originalImage, resizingFactor, quality);
        };
        cropLeftElement.oninput = (e) => {
            quality = parseInt(e.target.value);
            compressImage(originalImage, resizingFactor, quality);
        };
        cropTopElement.oninput = (e) => {
            quality = parseInt(e.target.value);
            compressImage(originalImage, resizingFactor, quality);
        };
        cropWidthElement.oninput = (e) => {
            quality = parseInt(e.target.value);
            compressImage(originalImage, resizingFactor, quality);
        };
        cropHeightElement.oninput = (e) => {
            quality = parseInt(e.target.value);
            compressImage(originalImage, resizingFactor, quality);
        };
        function compressImage(imgToCompress, resizingFactor, quality) {
            // showing the compressed image
            // 1. 建立Canvas畫布暫存修改後的圖片
            const canvas = document.createElement("canvas");
            const context = canvas.getContext("2d");

            // 2.儲存原圖尺寸及畫布尺寸
            const originalWidth = imgToCompress.width;
            const originalHeight = imgToCompress.height;
            const canvasWidth = originalWidth * resizingFactor;
            const canvasHeight = originalHeight * resizingFactor;

            // 3.賦予畫布尺寸
            canvas.width = canvasWidth;
            canvas.height = canvasHeight;

            // 4.將修改後的圖片放上畫布
            context.drawImage(
                imgToCompress,
                0,
                0,
                originalWidth * resizingFactor,
                originalHeight * resizingFactor
            );

            // reducing the quality of the image
            // 5.將圖片存為blob物件，並壓縮畫質
            canvas.toBlob(
                (blob) => {
                if (blob) {
                    compressedImageBlob = blob;
                    compressedImage.src = URL.createObjectURL(compressedImageBlob);
                    document.querySelector("#size").innerHTML = bytesToSize(blob.size);
                }
                },
                "image/jpeg",
                quality
            );
        }
        // source: https://stackoverflow.com/a/18650828
        function bytesToSize(bytes) {
            const sizes = ["Bytes", "KB", "MB", "GB", "TB"];

            if (bytes === 0) {
                return "0 Byte";
            }

            const i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));

            return Math.round(bytes / Math.pow(1024, i), 2) + " " + sizes[i];
        }
    </script>
</body>

</html>
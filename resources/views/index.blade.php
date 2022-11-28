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
    <main class="flex flex-col justify-center">
        <div class="m-2 p-5">
            <label for="">1. </label>
            <input type="file" name="" accept="image/*">
            {{-- <img src="" alt="" id="preview_1" width="200" height="200"> --}}
        </div>
        <div>
            <h2>Original Image</h2>
            <img src="" alt="" id="preview_1" style="outline: 1px solid #000;">
        </div>
        <div>
            <label>Rezise</label><input min="1" max="100" value="80" type="range" name="resize"
                id="resize">
            <label>Quality</label><input min="1" max="100" value="80" type="range" name="quality"
                id="quality">
            <label>Rotate</label><input min="-180" max="180" value="0" type="range" name="rotate"
                id="rotate">
        </div>
        <div>
            <label>CropLeft</label><input type="range" step="1" name="cropleft" id="cropleft" min="0"
                max="100" value="0">
            <label>CropTop</label><input type="range" step="1" name="croptop" id="croptop" min="0"
                max="100" value="0">
        </div>
        <div>
            <label>CropWidth</label><input type="range" step="1" name="cropwidth" id="cropwidth" min="0"
                max="100" value="50">
            <label>CropHeight</label><input type="range" step="1" name="cropheight" id="cropheight"
                min="0" max="100" value="50">
        </div>
        <h2>Compressed Image</h2>
        <button class="w-1/12 p-2 bg-slate-700 text-center text-slate-300 rounded-md" onclick="Upload()">Upload</button>
        <div><b>Size:</b> <span id="size"></span></div>
        <img id="compressedImage" style="outline:1px solid #555;" />
    </main>

    <script>
        const originalImage = document.querySelector("#preview_1");
        const compressedImage = document.querySelector("#compressedImage");
        const input = document.querySelector("input[type=file]");
        console.log(originalImage, input);
        // 取得DOM物件
        const resizingElement = document.querySelector("#resize");
        const qualityElement = document.querySelector("#quality");
        const rotateElement = document.querySelector("#rotate");
        const cropLeftElement = document.querySelector("#cropleft");
        const cropTopElement = document.querySelector("#croptop");
        const cropWidthElement = document.querySelector("#cropwidth");
        const cropHeightElement = document.querySelector("#cropheight");

        // 設定初始條件
        var resizingFactor = 0.8;
        var quality = 0.8;
        var rotate = 0;
        var cropLeft = 0;
        var cropTop = 0;
        var cropWidth = 0.5;
        var cropHeight = 0.5;
        var canvasWidth = 0;
        var canvasHeight = 0;
        var sx = 0;
        var sy = 0;
        var sWidth = 0.8;
        var sHeight = 0.8;

        // 用來存放圖片檔案
        var imgFile;

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

                // 顯示修正後圖片
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
            let file = new File([blob], "name", {
                type: "image/jpeg",
            });
            return file;
        }

        resizingElement.oninput = (e) => {
            resizingFactor = parseInt(e.target.value) / 100;
            // console.log(resizingFactor);
            // 任何滑桿的改變都會呼叫一次函式
            compressImage(originalImage, resizingFactor, quality);
        };
        qualityElement.oninput = (e) => {
            quality = parseInt(e.target.value) / 100;
            compressImage(originalImage, resizingFactor, quality);
        };
        rotateElement.oninput = (e) => {
            rotate = parseInt(e.target.value) / 180 * Math.PI;
            console.log(rotate);
            compressImage(originalImage, resizingFactor, quality);
        };
        cropLeftElement.oninput = (e) => {
            cropLeft = parseInt(e.target.value) / 100;
            compressImage(originalImage, resizingFactor, quality);
        };
        cropTopElement.oninput = (e) => {
            cropTop = parseInt(e.target.value) / 100;
            compressImage(originalImage, resizingFactor, quality);
        };
        cropWidthElement.oninput = (e) => {
            cropWidth = parseInt(e.target.value) / 100;
            compressImage(originalImage, resizingFactor, quality);
        };
        cropHeightElement.oninput = (e) => {
            cropHeight = parseInt(e.target.value) / 100;
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

            compressedImage.width = originalWidth * resizingFactor;
            compressedImage.height = originalHeight * resizingFactor;
            // console.log(canvasWidth, canvasHeight);

            // 3.賦予畫布尺寸
            // canvas.width = canvasWidth;
            // canvas.height = canvasHeight;

            // console.log(`canvas (width, height) = ${canvasWidth}, ${canvasHeight}`);
            // console.log(Number(sx), Number(sy), Number(sWidth), Number(sHeight));

            // 旋轉畫布
            console.log(`sin, cos : ${Math.sin(rotate)}, ${Math.cos(rotate)}`);

            // 4.將修改後的圖片放上畫布
            if (rotate <= (Math.PI / 2) && rotate >= 0) {
                canvasWidth = Math.sin(rotate) * originalHeight + Math.cos(rotate) * originalWidth;
                canvasHeight = Math.cos(rotate) * originalHeight + Math.sin(rotate) * originalWidth;
                setParams(canvas);
                
                context.translate(Math.sin(rotate) * originalHeight, 0)
                context.rotate(rotate);
                context.fillStyle = "red";
                context.fillRect(0, 0, 10, 10);
                context.drawImage(
                    imgToCompress,
                    Number(sx),
                    Number(sy),
                    Number(sWidth),
                    Number(sHeight),
                    0,
                    0,
                    sWidth,
                    sHeight
                );
            } else if (rotate > (Math.PI / 2)) {
                canvasWidth = Math.abs(Math.cos(rotate) * originalWidth) + Math.sin(rotate) * originalHeight;
                canvasHeight = Math.abs(Math.cos(rotate) * originalHeight) + Math.sin(rotate) * originalWidth;
                setParams(canvas);
                context.translate(
                    Math.abs(Math.cos(rotate) * originalWidth) + Math.sin(rotate) * originalHeight,
                    Math.abs(Math.cos(rotate) * originalHeight)
                );
                context.rotate(rotate);
                context.fillStyle = "red";
                context.fillRect(0, 0, 10, 10);
                context.drawImage(
                    imgToCompress,
                    Number(sx),
                    Number(sy),
                    Number(sWidth),
                    Number(sHeight),
                    0,
                    0,
                    sWidth,
                    sHeight
                );
            } else if (rotate >= (-Math.PI / 2) && rotate <= 0) {
                canvasWidth = Math.cos(rotate) * originalWidth + Math.abs(Math.sin(rotate) * originalHeight);
                canvasHeight = Math.abs(Math.sin(rotate) * originalWidth) + Math.cos(rotate) * originalHeight;
                setParams(canvas);
                context.translate(
                    0,
                    Math.abs(Math.sin(rotate) * originalWidth)
                );
                context.rotate(rotate);
                context.fillStyle = "red";
                context.fillRect(0, 0, 10, 10);
                context.drawImage(
                    imgToCompress,
                    Number(sx),
                    Number(sy),
                    Number(sWidth),
                    Number(sHeight),
                    0,
                    0,
                    sWidth,
                    sHeight
                );
            } else if (rotate < (-Math.PI / 2)) {
                canvasWidth = Math.abs(Math.cos(rotate) * originalWidth + Math.sin(rotate) * originalHeight);
                canvasHeight = Math.abs(Math.cos(rotate) * originalHeight + Math.sin(rotate) * originalWidth);
                setParams(canvas);
                context.translate(
                    Math.abs(Math.cos(rotate) * originalWidth),
                    Math.abs(Math.cos(rotate) * originalHeight + Math.sin(rotate) * originalWidth)
                );
                context.rotate(rotate);
                context.fillStyle = "red";
                context.fillRect(0, 0, 10, 10);
                context.drawImage(
                    imgToCompress,
                    Number(sx),
                    Number(sy),
                    Number(sWidth),
                    Number(sHeight),
                    0,
                    0,
                    sWidth,
                    sHeight
                );
            }

            // reducing the quality of the image
            // 5.將圖片存為blob物件，並壓縮畫質
            canvas.toBlob(
                (blob) => {
                    if (blob) {
                        compressedImageBlob = blob;
                        compressedImage.src = URL.createObjectURL(compressedImageBlob);
                        document.querySelector("#size").innerHTML = bytesToSize(blob.size);
                        imgFile = new File([blob], "modifiedImage");
                    }
                },
                "image/jpeg",
                quality
            );
        }

        // source: https://stackoverflow.com/a/18650828
        function bytesToSize(bytes) {
            // 計算圖片容量
            const sizes = ["Bytes", "KB", "MB", "GB", "TB"];

            if (bytes === 0) {
                return "0 Byte";
            }

            const i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));

            return Math.round(bytes / Math.pow(1024, i), 2) + " " + sizes[i];
        }

        // 設定畫布大小及裁切參數
        function setParams(canvas) {
            compressedImage.width = canvasWidth;
            compressedImage.height = canvasHeight;
            canvas.width = canvasWidth;
            canvas.height = canvasHeight;
            sx = Number(canvasWidth * cropLeft).toFixed(0);
            sy = Number(canvasHeight * cropTop).toFixed(0);
            sWidth = Number(canvasWidth * cropWidth).toFixed(0);
            sHeight = Number(canvasHeight * cropHeight).toFixed(0);
        }
        // 上傳檔案
        function Upload() {
            if (imgFile) {
                let fd = new FormData();
                fd.append('_token', '{{ csrf_token() }}');
                fd.append("uploadImage", imgFile);
                fetch("{{ route('store_img') }}", {
                        method: "POST",
                        body: fd
                    })
                    .then();
            }
        }
    </script>
</body>

</html>

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
            <img src="" alt="" id="preview_1">
        </div>
        {{-- <div class="m-2 p-5">
      <label for="">1. </label>
      <input type="file">
      <img src="" alt="" id="preview_2">
    </div> --}}
    </main>

    <script>
        const img = document.querySelector("img");
        const input = document.querySelector("input[type=file]");
        console.log(img, input);

        input.addEventListener('change', (e) => {
            const reader = new FileReader();
            console.log(input.files[0]);

            reader.addEventListener("load", () => {
                // convert image file to base64 string
                img.src = reader.result;
                let testBlob = dataURItoBlob(reader.result);
                console.log(testBlob);
            }, false);

            if (input.files) {
                reader.readAsDataURL(input.files[0]);
                console.log(reader);
            }
        });

        function dataURItoBlob(dataURI) {
            // convert base64/URLEncoded data component to raw binary data held in a string
            var byteString;
            if (dataURI.split(',')[0].indexOf('base64') >= 0)
                byteString = atob(dataURI.split(',')[1]);
            else
                byteString = unescape(dataURI.split(',')[1]);

            // separate out the mime component
            var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];

            // write the bytes of the string to a typed array
            var ia = new Uint8Array(byteString.length);
            for (var i = 0; i < byteString.length; i++) {
                ia[i] = byteString.charCodeAt(i);
            }

            return new Blob([ia], {
                type: mimeString
            });
        }
    </script>
</body>

</html>

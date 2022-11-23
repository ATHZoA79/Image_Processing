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
            <img src="" alt="" id="preview_1" width="200" height="200">
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
            }, false);
            
            if (input.files) {
              reader.readAsDataURL(input.files[0]);
              console.log(reader);
            }
        });
    </script>
</body>

</html>

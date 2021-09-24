@for ($i = 0; $i < 3; $i++)
<?php

header("Content-type: application/vnd.ms-word");
header("Content-Disposition: attachment;Filename=dewan-word.doc");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }
        body {
            width: 1200px;
            margin-top: 37.44px;
            margin-bottom: 548.16px;
            margin-left: 28.8px;
            margin-right: 19.2;
        }
    </style>
</head>
<body>
    tes
</body>
</html>
@endfor


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Agreement</title>
    <style>
        .privacy-policy{
            padding: 12px;
            line-height: 25px;
        }
    </style>
</head>
<body>
    <div class="privacy-policy">
        {!! isset($privacyPolicy->description) ? $privacyPolicy->description : '' !!}
    </div>
</body>
</html>
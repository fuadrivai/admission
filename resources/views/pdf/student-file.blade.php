<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            size: A4;
            margin: 30px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }

        .row {
            display: flex;
            gap: 10px;
        }

        .img-box {
            width: 100%;
            text-align: center;
        }

        img {
            display: block;
            width: 100%;
            height: auto;
        }

        .label {
            font-weight: bold;
            margin-bottom: 6px;
        }
    </style>
</head>

<body>

    @foreach ($groups as $group)
        <div class="page">
            <div class="row">
                @foreach ($group as $file)
                    <div class="img-box">
                        <div class="label">{{ $file['label'] }}</div>
                        <img src="{{ $file['path'] }}">
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

</body>

</html>

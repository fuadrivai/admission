@php
    $primaryColor = $data['primary_color'] ?? '#174A83';
    $secondaryColor = $data['secondary_color'] ?? '#D9B66F';
@endphp
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $data['event_title'] }}</title>
</head>

<body style="margin:0;padding:24px;background:#f5f7fa;font-family:Arial,sans-serif;color:#172033;">
    <main style="max-width:620px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;">
        <header style="padding:28px 24px;text-align:center;background:{{ $primaryColor }};color:#fff;">
            <h1 style="margin:0;font-size:26px;">{{ $data['event_title'] }}</h1>
        </header>
        <section style="padding:28px 24px;line-height:1.7;">
            {!! $data['content'] !!}
        </section>
        <footer style="height:8px;background:{{ $secondaryColor }};"></footer>
    </main>
</body>

</html>

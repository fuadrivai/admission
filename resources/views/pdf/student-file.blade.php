<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Files</title>
    <style>
        @page {
            margin: 30px;
            size: A4;
        }

        body {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
        }

        .header {
            width: 100%;
            border-bottom: 2px solid #eae0e0;
            padding: 12px 0 22px 0;
            margin-bottom: 18px;
        }

        .header-table {
            width: 100%;
        }

        .logo-cell {
            width: 40%;
        }

        .title-cell {
            width: 60%;
            text-align: right;
            vertical-align: top;
        }

        .title {
            margin: 0;
            font-size: 20px;
            color: #800000;
        }

        .subtitle {
            margin-top: 6px;
            font-style: italic;
            color: #800000;
        }

        .meta {
            margin: 8px 0 18px 0;
        }

        .meta td {
            padding: 6px 8px;
            vertical-align: top;
        }

        .meta td.label {
            width: 28%;
            font-weight: bold;
            color: #6b2b2b;
        }

        .images-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .img-box {
            background: #fff;
            border: 1px solid #efe3e3;
            padding: 8px;
            border-radius: 6px;
            text-align: center;
        }

        .img-box-full {
            grid-column: span 2;
        }

        .img-box img {
            width: 100%;
            height: auto;
            display: block;
            border-radius: 4px;
        }

        .img-label {
            font-weight: 700;
            margin: 8px 0 4px 0;
            color: #442222;
            font-size: 12px;
        }

        .page-break {
            page-break-before: always;
            break-before: page;
            margin-top: 20px;
        }

        @media print {
            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>

<body>

    <div class="header">
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    <img src="{{ $logo ?? '' }}" alt="Logo" style="max-width:150px;">
                </td>
                <td class="title-cell">
                    <h1 class="title">Student & Parent Identification Data</h1>
                    <p class="subtitle">Form pengumpulan data orang tua dan siswa</p>
                </td>
            </tr>
        </table>
    </div>

    <table class="meta">
        <tr>
            <td>
                <h2 class="section-title">Student Information</h2>
            </td>
            <td></td>
        </tr>
        <tr>
            <td class="label">Enrolment Code</td>
            <td>: {{ $data->code ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Full name</td>
            <td>: <strong>{{ $data->applicant->fullname ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td class="label">Level / Grade</td>
            <td>: {{ optional($data->level)->name ?? '-' }} / {{ optional($data->grade)->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Academic Year</td>
            <td>: {{ $data->accademic_year ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Branch</td>
            <td>: {{ optional($data->branch)->name ?? '-' }}</td>
        </tr>
    </table>
    <div class="images-section">
        <div class="images-grid">
            @foreach ($groups as $file)
                <div class="img-box @if ($loop->index > 1) img-box-full @endif">
                    <div class="img-label">{{ $file['label'] ?? 'Document' }}</div>
                    <img src="{{ $file['path'] }}" alt="{{ $file['label'] ?? 'document' }}">
                </div>
                @if (!$loop->last)
                    <div class="page-break"></div>
                @endif
            @endforeach
        </div>
    </div>
</body>

</html>

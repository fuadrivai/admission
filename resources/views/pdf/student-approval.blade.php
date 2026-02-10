<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent's Statement Form</title>
    <style>
        /* CSS yang kompatibel dengan Dompdf */
        @page {
            margin: 50px 40px 10px 40px;
        }

        body {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Warna maroon - dominan */
        :root {
            --maroon-primary: #800000;
            --maroon-secondary: #9a3333;
            --maroon-light: #f5eaea;
            --maroon-dark: #660000;
        }

        .agreement-confirmation {
            margin: 15px 0;
            padding: 12px 15px;
            background-color: #f0f8f0;
            border: 1px solid #c8e6c8;
            border-radius: 4px;
            font-weight: bold;
            color: #2e7d32;
        }

        /* Content */
        .content {
            margin-top: 10px;
        }

        .section {
            margin-bottom: 2px;
            orphans: 3;
            widows: 3;
        }

        .section-title {
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 12px;
            padding: 8px 10px;
            background-color: var(--maroon-light);
            border-left: 4px solid var(--maroon-primary);
            color: var(--maroon-dark);
            text-transform: uppercase;
        }

        .subsection-title {
            font-size: 13px;
            font-weight: bold;
            margin: 18px 0 10px 0;
            color: var(--maroon-secondary);
            padding-bottom: 4px;
            border-bottom: 1px solid #e0c0c0;
        }

        /* Data tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
        }

        .data-table td {
            padding: 7px 8px;
            vertical-align: top;
            border-bottom: 1px solid #eee;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table td:first-child {
            width: 40%;
            font-weight: bold;
            color: var(--maroon-secondary);
        }

        /* Declaration box */
        .declaration {
            border: 1px solid #d8b8b8;
            padding: 14px;
            margin: 7px 0;
            0 background-color: #fefafa;
            border-radius: 4px;
            border-left: 3px solid var(--maroon-primary);
        }

        /* Checkbox styling */
        .checkbox-container {
            margin: 12px 0;
            display: flex;
            align-items: center;
        }

        .checkbox-label {
            margin-left: 8px;
            font-weight: normal;
        }

        .checkbox-checked {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid var(--maroon-primary);
            background-color: var(--maroon-primary);
            margin-right: 5px;
            position: relative;
        }

        .checkbox-checked:after {
            content: "✓";
            color: white;
            font-size: 10px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .checkbox-unchecked {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid #999;
            margin-right: 5px;
        }

        /* Fee table */
        .fee-table {
            width: 100%;
            border-collapse: collapse;
            margin: 18px 0;
            border: 1px solid #d8b8b8;
        }

        .fee-table th,
        .fee-table td {
            border: 1px solid #d8b8b8;
            padding: 10px;
            text-align: left;
        }

        .fee-table th {
            background-color: var(--maroon-light);
            font-weight: bold;
            color: var(--maroon-dark);
        }

        .fee-table tr:nth-child(even) {
            background-color: #fcf8f8;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 2px solid var(--maroon-light);
            font-size: 10px;
            color: #777;
            text-align: center;
        }

        .footer p {
            margin: 3px 0;
        }

        .footer strong {
            color: var(--maroon-secondary);
        }

        /* Utilities */
        .text-bold {
            font-weight: bold;
        }

        .text-italic {
            font-style: italic;
        }

        .mt-10 {
            margin-top: 10px;
        }

        .mb-10 {
            margin-bottom: 10px;
        }

        .text-maroon {
            color: var(--maroon-primary);
        }

        /* Untuk tanda tangan */
        .signature-area {
            margin-top: 40px;
            padding: 20px;
            border: 1px dashed #d8b8b8;
            border-radius: 4px;
            background-color: #fefcfc;
        }

        .signature-line {
            width: 70%;
            border-top: 2px solid var(--maroon-primary);
            margin: 50px auto 0 auto;
            text-align: center;
        }

        .signature-label {
            margin-top: 8px;
            font-size: 11px;
            color: var(--maroon-secondary);
            text-align: center;
        }

        /* Page break control */
        .page-break {
            page-break-before: always;
            break-before: page;
        }

        /* Maroon accents */
        .maroon-accent {
            color: var(--maroon-primary);
            font-weight: bold;
        }

        /* Important note box */
        .important-note {
            background-color: var(--maroon-light);
            border: 1px solid var(--maroon-secondary);
            padding: 12px;
            margin: 15px 0;
            border-radius: 4px;
            font-size: 10.5px;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <table width="100%" cellpadding="0" cellspacing="0" style="border-bottom:2px solid #914949; margin-bottom:30px;">
        <tr>
            <!-- LEFT : LOGO -->
            <td width="30%" valign="top" style="padding-bottom:25px;">
                <img src="{{ $logo }}" alt="School Logo" style="max-width:180px;">
            </td>

            <!-- RIGHT : TITLE -->
            <td width="70%" valign="top" align="right" style="padding-bottom:25px;">
                <h1 style="margin:0; font-size:24px; color:#800000;">
                    Parent’s Statement Form
                </h1>
                <p style="margin-top:6px; font-style:italic; color:#800000;">
                    Formulir persetujuan orang tua
                </p>
            </td>
        </tr>
    </table>

    <!-- Content -->
    <div class="content">
        <!-- Section 1: Authorized Signatory Confirmation -->
        <div class="section">
            <div class="section-title">{{ config('student_approval.step1.title') }}</div>

            <div class="subsection-title">Relationship to Student : <span
                    class="maroon-accent">{{ \Illuminate\Support\Str::title($data->statement->actor) }}</span></div>

            <div class="subsection-title">{{ config('student_approval.step1.labels.text1.english') }} :</div>
            <table class="data-table">
                <tr>
                    <td>{{ config('student_approval.step1.labels.text2.english') }}:</td>
                    <td><span class="maroon-accent">{{ $parent->fullname }}</span></td>
                </tr>
                <tr>
                    <td>{{ config('student_approval.step1.labels.text3.english') }}:</td>
                    <td><span class="maroon-accent">{{ $parent->email }}</span></td>
                </tr>
                <tr>
                    <td>{{ config('student_approval.step1.labels.text4.english') }}:</td>
                    <td><span class="maroon-accent">{{ $parent->phone }}</span></td>
                </tr>
                <tr>
                    <td>D.O.B:</td>
                    <td><span class="maroon-accent">{{ $parent->birth_place }},
                            {{ date('d F Y', strtotime($parent->birth_date)) }}</span></td>
                </tr>
                <tr>
                    <td>{{ config('student_approval.step1.labels.text7.english') }}:</td>
                    <td><span class="maroon-accent">{{ $parent->identity_number }}</span></td>
                </tr>
            </table>

            <div class="declaration">
                <p class="text-bold">{{ config('student_approval.step1.labels.text8.english') }}</p>
                <p class="text-italic">{{ config('student_approval.step1.labels.text8.indonesian') }}</p>
            </div>

            <div class="subsection-title">{{ config('student_approval.step1.labels.text13.english') }}</div>
            <table class="data-table">
                <tr>
                    <td>{{ config('student_approval.step1.labels.text2.english') }}:</td>
                    <td><span class="maroon-accent">{{ $data->applicant->fullname }}</span></td>
                </tr>
                <tr>
                    <td>{{ config('student_approval.step1.labels.text9.english') }}:</td>
                    <td><span class="maroon-accent">{{ $data->applicant->age }}</span></td>
                </tr>
                <tr>
                    <td>{{ config('student_approval.step1.labels.text10.english') }}:</td>
                    <td><span class="maroon-accent">{{ $data->level->name }} / {{ $data->grade->name }}</span></td>
                </tr>
                <tr>
                    <td>{{ config('student_approval.step1.labels.text12.english') }}:</td>
                    <td><span class="maroon-accent">{{ $data->accademic_year }} </span></td>
                </tr>
            </table>
        </div>
        <div class="page-break"></div>
        <!-- Section 2: School Fee Payment Consent Form -->
        <div class="section">
            <div class="section-title">{{ config('student_approval.step2.title') }}</div>

            <div class="declaration">
                <p class="text-bold">{{ config('student_approval.step2.labels.text1.english') }}</p>
                <p class="text-italic">{{ config('student_approval.step2.labels.text1.indonesian') }}</p>
            </div>

            <div class="subsection-title">Payment Details</div>
            <table class="fee-table">
                <thead>
                    <tr>
                        <th>Fee Type</th>
                        <th>Amount (IDR)</th>
                        <th>In Words</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ config('student_approval.step2.labels.text2.english') }}</td>
                        <td class="maroon-accent">Rp. <span>
                                {{ number_format($data->statement->financial->development_fee, 0, '.', ',') }}</span>
                        </td>
                        <td><span
                                class="maroon-accent">{{ $data->statement->financial->development_fee_terbilang }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ config('student_approval.step2.labels.text3.english') }}</td>
                        <td class="maroon-accent">Rp. <span>
                                {{ number_format($data->statement->financial->annual_fee, 0, '.', ',') }}</span></td>
                        <td><span class="maroon-accent">{{ $data->statement->financial->annual_fee_terbilang }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ config('student_approval.step2.labels.text4.english') }}</td>
                        <td class="maroon-accent">Rp. <span>
                                {{ number_format($data->statement->financial->school_fee, 0, '.', ',') }}</span></td>
                        <td><span class="maroon-accent">{{ $data->statement->financial->school_fee_terbilang }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ config('student_approval.step2.labels.text22.english') }}</td>
                        <td class="maroon-accent">Rp. <span>
                                {{ number_format($data->statement->financial->uniform_fee, 0, '.', ',') }}</span></td>
                        <td><span class="maroon-accent">{{ $data->statement->financial->uniform_fee_terbilang }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ config('student_approval.step2.labels.text23.english') }}</td>
                        <td class="maroon-accent">Rp. <span>
                                {{ number_format($data->statement->financial->ittihada_fee, 0, '.', ',') }}</span></td>
                        <td><span
                                class="maroon-accent">{{ $data->statement->financial->ittihada_fee_terbilang }}</span>
                        </td>
                    </tr>
                    @if ($data->level->division->name == 'Secondary')
                        <tr>
                            <td>{{ config('student_approval.step2.labels.text24.english') }}</td>
                            <td class="maroon-accent">Rp. <span>
                                    {{ number_format($data->statement->financial->mhsu_fee, 0, '.', ',') }}</span>
                            </td>
                            <td><span
                                    class="maroon-accent">{{ $data->statement->financial->mhsu_fee_terbilang }}</span>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
            <div class="subsection-title">Payment Terms and Conditions</div>
            <p class="text-bold">{{ config('student_approval.step2.labels.text5.english') }}:
            </p>

            <div class="declaration">
                <ol>
                    <li>
                        <p><strong>{{ config('student_approval.step2.labels.text6.english') }}</strong><br>
                            <span
                                class="text-italic">{{ config('student_approval.step2.labels.text6.indonesian') }}</span>
                        </p>
                    </li>
                    <li>
                        <p><strong>{{ config('student_approval.step2.labels.text7.english') }}</strong><br>
                            <span
                                class="text-italic">{{ config('student_approval.step2.labels.text7.indonesian') }}</span>
                        </p>
                    </li>
                    <li>
                        <p><strong>{{ config('student_approval.step2.labels.text8.english') }}</strong> <br><span
                                class="text-italic">{{ config('student_approval.step2.labels.text8.indonesian') }}</span>
                        </p>
                    </li>
                    <li>
                        <p><strong>{{ config('student_approval.step2.labels.text9.english') }}</strong> <br>
                            <span
                                class="text-italic">{{ config('student_approval.step2.labels.text9.indonesian') }}</span>
                        </p>

                    </li>
                    <li>
                        <p><strong>{{ config('student_approval.step2.labels.text10.english') }}</strong> <br>
                            <span
                                class="text-italic">{{ config('student_approval.step2.labels.text10.indonesian') }}</span>
                        </p>

                    </li>
                    <li>
                        <p><strong>{{ config('student_approval.step2.labels.text11.english') }}</strong> <br>
                            <span
                                class="text-italic">{{ config('student_approval.step2.labels.text11.indonesian') }}</span>
                        </p>

                    </li>
                    <li>
                        <p><strong>{{ config('student_approval.step2.labels.text12.english') }}</strong> <br>
                            <span
                                class="text-italic">{{ config('student_approval.step2.labels.text12.indonesian') }}</span>
                        </p>

                    </li>
                    <li>
                        <p><strong>{{ config('student_approval.step2.labels.text13.english') }}</strong> <br>
                            <span
                                class="text-italic">{{ config('student_approval.step2.labels.text13.indonesian') }}</span>
                        </p>

                    </li>
                    <li>
                        <p><strong>{{ config('student_approval.step2.labels.text14.english') }}</strong><br>
                            <span
                                class="text-italic">{{ config('student_approval.step2.labels.text14.indonesian') }}</span>
                        </p>

                    </li>
                    <li>
                        <p><strong>{{ config('student_approval.step2.labels.text15.english') }}</strong><br>
                            <span
                                class="text-italic">{{ config('student_approval.step2.labels.text15.indonesian') }}</span>
                        </p>

                        <ol type="A">
                            <li>
                                <p><strong>{{ config('student_approval.step2.labels.text16.english') }}</strong> <br>
                                    <span
                                        class="text-italic">{{ config('student_approval.step2.labels.text16.indonesian') }}</span>
                                </p>

                            </li>
                            <li>
                                <p><strong>{{ config('student_approval.step2.labels.text17.english') }}</strong> <br>
                                    <span
                                        class="text-italic">{{ config('student_approval.step2.labels.text17.indonesian') }}
                                    </span>
                                </p>

                            </li>
                            <li>
                                <p><strong>{{ config('student_approval.step2.labels.text18.english') }}</strong><br>
                                    <span
                                        class="text-italic">{{ config('student_approval.step2.labels.text18.indonesian') }}
                                    </span>
                                </p>

                            </li>
                            <li>
                                <p><strong>{{ config('student_approval.step2.labels.text19.english') }}</strong><br>
                                    <span
                                        class="text-italic">{{ config('student_approval.step2.labels.text19.indonesian') }}
                                    </span>
                                </p>

                            </li>
                            <li>
                                <p><strong>{{ config('student_approval.step2.labels.text20.english') }}</strong><br>
                                    <span
                                        class="text-italic">{{ config('student_approval.step2.labels.text20.indonesian') }}
                                    </span>
                                </p>

                            </li>
                        </ol>
                    </li>
                    <li>
                        <p><strong>{{ config('student_approval.step2.labels.text21.english') }}</strong><br>
                            <span
                                class="text-italic">{{ config('student_approval.step2.labels.text21.indonesian') }}</span>
                        </p>

                    </li>
                </ol>
            </div>
        </div>

        <!-- Section 3: FORM PERNYATAAN ORANG TUA / WALI MURID -->
        <div class="section">
            <div class="section-title">{{ config('student_approval.step3.title') }}</div>
            <div class="declaration">
                <ol>
                    <li>
                        <p><strong>{{ config('student_approval.step3.labels.text1.english') }}</strong><br>
                            <span
                                class="text-italic">{{ config('student_approval.step3.labels.text1.indonesian') }}</span>
                        </p>
                    </li>
                    <li>
                        <p><strong>{{ config('student_approval.step3.labels.text2.english') }}</strong><br>
                            <span
                                class="text-italic">{{ config('student_approval.step3.labels.text2.indonesian') }}</span>
                        </p>
                    </li>
                    <li>
                        <p><strong>{{ config('student_approval.step3.labels.text3.english') }}</strong><br>
                            <span
                                class="text-italic">{{ config('student_approval.step3.labels.text3.indonesian') }}</span>
                        </p>
                    </li>
                    <li>
                        <p><strong>{{ config('student_approval.step3.labels.text4.english') }}</strong><br>
                            <span
                                class="text-italic">{{ config('student_approval.step3.labels.text4.indonesian') }}</span>
                        </p>
                    </li>
                    <li>
                        <p><strong>{{ config('student_approval.step3.labels.text5.english') }}</strong><br>
                            <span
                                class="text-italic">{{ config('student_approval.step3.labels.text5.indonesian') }}</span>
                        </p>
                    </li>
                    <li>
                        <p><strong>{{ config('student_approval.step3.labels.text6.english') }}</strong><br>
                            <span
                                class="text-italic">{{ config('student_approval.step3.labels.text6.indonesian') }}</span>
                        </p>
                    </li>
                    <li>
                        <p><strong>{{ config('student_approval.step3.labels.text7.english') }}</strong><br>
                            <span
                                class="text-italic">{{ config('student_approval.step3.labels.text7.indonesian') }}</span>
                        </p>
                    </li>
                    <li>
                        <p><strong>{{ config('student_approval.step3.labels.text8.english') }}</strong><br>
                            <span
                                class="text-italic">{{ config('student_approval.step3.labels.text8.indonesian') }}</span>
                        </p>
                    </li>
                    <li>
                        <p><strong>{{ config('student_approval.step3.labels.text9.english') }}</strong><br>
                            <span
                                class="text-italic">{{ config('student_approval.step3.labels.text9.indonesian') }}</span>
                        </p>
                    </li>
                    <li>
                        <p><strong>{{ config('student_approval.step3.labels.text10.english') }}</strong><br>
                            <span
                                class="text-italic">{{ config('student_approval.step3.labels.text10.indonesian') }}</span>
                        </p>
                    </li>
                    <li>
                        <p><strong>{{ config('student_approval.step3.labels.text11.english') }}</strong><br>
                            <span
                                class="text-italic">{{ config('student_approval.step3.labels.text11.indonesian') }}</span>
                        </p>
                    </li>
                    <li>
                        <p><strong>{{ config('student_approval.step3.labels.text12.english') }}</strong><br>
                            <span
                                class="text-italic">{{ config('student_approval.step3.labels.text12.indonesian') }}</span>
                        </p>
                    </li>
                    <li>
                        <p><strong>{{ config('student_approval.step3.labels.text13.english') }}</strong><br>
                            <span
                                class="text-italic">{{ config('student_approval.step3.labels.text13.indonesian') }}</span>
                        </p>
                    </li>
                    <li>
                        <p><strong>{{ config('student_approval.step3.labels.text14.english') }}</strong><br>
                            <span
                                class="text-italic">{{ config('student_approval.step3.labels.text14.indonesian') }}</span>
                        </p>
                    </li>
                    <li>
                        <p><strong>{{ config('student_approval.step3.labels.text15.english') }}</strong><br>
                            <span
                                class="text-italic">{{ config('student_approval.step3.labels.text15.indonesian') }}</span>
                        </p>
                    </li>
                    <li>
                        <p><strong>{{ config('student_approval.step3.labels.text16.english') }}</strong><br>
                            <span
                                class="text-italic">{{ config('student_approval.step3.labels.text16.indonesian') }}</span>
                        </p>
                    </li>
                    <li>
                        <p><strong>{{ config('student_approval.step3.labels.text17.english') }}</strong><br>
                            <span
                                class="text-italic">{{ config('student_approval.step3.labels.text17.indonesian') }}</span>
                        </p>
                    </li>
                </ol>
            </div>
        </div>


        @if ($data->level->name == 'Upper Secondary')
            <div class="page-break"></div>
            <!-- Section 4: SURAT PERNYATAAN ORANG TUA KESEDIAAN MENJALANI TES NARKOTIKA DAN OBAT TERLARANG -->
            <div class="section">
                <div class="section-title">{{ config('student_approval.step5.title') }}</div>
                <div class="subsection-title">{{ config('student_approval.step5.labels.text0.indonesian') }} :</div>
                <table class="data-table">
                    <tr>
                        <td>{{ config('student_approval.step5.labels.text1.english') }}:</td>
                        <td><span class="maroon-accent">{{ $parent->fullname }}</span></td>
                    </tr>
                    <tr>
                        <td>{{ config('student_approval.step5.labels.text2.english') }}:</td>
                        <td><span class="maroon-accent">{{ $parent->email }}</span></td>
                    </tr>
                    <tr>
                        <td>{{ config('student_approval.step5.labels.text3.english') }}:</td>
                        <td><span class="maroon-accent">{{ $parent->phone }}</span></td>
                    </tr>
                    <tr>
                        <td>D.O.B:</td>
                        <td><span class="maroon-accent">{{ $parent->birth_place }},
                                {{ date('d F Y', strtotime($parent->birth_date)) }}</span></td>
                    </tr>
                    <tr>
                        <td>{{ config('student_approval.step5.labels.text3.english') }}:</td>
                        <td><span class="maroon-accent">{{ $parent->identity_number }}</span></td>
                    </tr>
                </table>
                <div class="declaration">
                    <p class="text-bold">{{ config('student_approval.step5.labels.text7.english') }}</p>
                    {{-- <p class="text-italic">{{ config('student_approval.step1.labels.text8.indonesian') }}</p> --}}
                </div>

                <div class="subsection-title">{{ config('student_approval.step5.labels.text8.indonesia') }}</div>
                <table class="data-table">
                    <tr>
                        <td>{{ config('student_approval.step5.labels.text1.english') }}:</td>
                        <td><span class="maroon-accent">{{ $data->applicant->fullname }}</span></td>
                    </tr>
                    <tr>
                        <td>{{ config('student_approval.step5.labels.text10.english') }}:</td>
                        <td><span class="maroon-accent">{{ $data->applicant->age }}</span></td>
                    </tr>
                    <tr>
                        <td>{{ config('student_approval.step5.labels.text11.english') }}:</td>
                        <td><span class="maroon-accent">{{ $data->level->name }} / {{ $data->grade->name }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ config('student_approval.step5.labels.text13.english') }}:</td>
                        <td><span class="maroon-accent">{{ $data->accademic_year }} </span></td>
                    </tr>
                </table>
                <div class="declaration">
                    <p class="text-bold">{!! config('student_approval.step5.labels.text14.english') !!}</p>
                    {{-- <p class="text-italic">{{ config('student_approval.step1.labels.text8.indonesian') }}</p> --}}
                </div>
            </div>
            <div class="page-break"></div>
            <!-- Section 5: SURAT PERNYATAAN SISWA -->
            <div class="section">
                <div class="section-title">{{ config('student_approval.step6.title') }}</div>
                <div class="subsection-title">{{ config('student_approval.step6.labels.text0.indonesia') }}</div>
                <table class="data-table">
                    <tr>
                        <td>{{ config('student_approval.step6.labels.text1.english') }}:</td>
                        <td><span class="maroon-accent">{{ $data->applicant->fullname }}</span></td>
                    </tr>
                    <tr>
                        <td>{{ config('student_approval.step6.labels.text2.english') }}:</td>
                        <td><span class="maroon-accent">{{ $data->applicant->age }}</span></td>
                    </tr>
                    <tr>
                        <td>{{ config('student_approval.step6.labels.text3.english') }}:</td>
                        <td><span class="maroon-accent">{{ $data->level->name }} / {{ $data->grade->name }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ config('student_approval.step6.labels.text5.english') }}:</td>
                        <td><span class="maroon-accent">{{ $data->accademic_year }} </span></td>
                    </tr>
                </table>
                <div class="declaration">
                    <p class="text-bold">{!! config('student_approval.step6.labels.text6.english') !!}</p>
                    {{-- <p class="text-italic">{{ config('student_approval.step1.labels.text8.indonesian') }}</p> --}}
                </div>
            </div>
        @endif

        <div class="page-break"></div>
        <!-- Signature Area -->
        <div class="signature-area">
            <p><strong>Declared and agreed in good faith,</strong></p>

            <div style="margin-top: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Agreed at</label>
                <input type="text" value="{{ date('d F Y H:i', strtotime($data->statement->completed_at)) }}"
                    readonly style="width: 200px;">
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>MUTIARA HARAPAN ISLAMIC SCHOOL</strong></p>
        <p>Parent's Statement Form • Academic Year 2025/2026</p>
        <p>Generated on: <span id="current-date"></span> • This document is valid only with official school stamp
        </p>
    </div>

    <script>
        const now = new Date();
        const options = {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        };
        document.getElementById('current-date').textContent = now.toLocaleDateString('en-GB', options);

        // Untuk Dompdf, script JavaScript mungkin tidak dieksekusi
        // Alternatif: Tambahkan tanggal secara manual atau gunakan PHP
    </script>
</body>

</html>

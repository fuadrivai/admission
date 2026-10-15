@php
    $openingSection = $financialDocument->sections->firstWhere('sort_order', 0);
@endphp

<div class="section">
    @if ($openingSection)
        <div class="section-title">{{ $openingSection->title_en }}</div>
        @if ($openingSection->title_id)
            <div class="text-italic" style="margin-bottom: 12px;">{{ $openingSection->title_id }}</div>
        @endif

        <div class="declaration">
            <p class="text-bold">{{ config('student_approval.step3.labels.text0.english') }}</p>
            <p class="text-italic">{{ config('student_approval.step3.labels.text0.indonesian') }}</p>
        </div>

        @foreach ($openingSection->items->where('number', 0) as $openingText)
            <div style="margin: 12px 0; text-align: justify; white-space: pre-line;">
                <div class="text-bold">{{ $openingText->text_en }}</div>
                <div class="text-italic">{{ $openingText->text_id }}</div>
            </div>
        @endforeach
    @endif

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
                <td class="maroon-accent">Rp.
                    {{ number_format($data->statement->financial->development_fee, 0, '.', ',') }}</td>
                <td class="maroon-accent">{{ $data->statement->financial->development_fee_terbilang }}</td>
            </tr>
            <tr>
                <td>{{ config('student_approval.step2.labels.text3.english') }}</td>
                <td class="maroon-accent">Rp. {{ number_format($data->statement->financial->annual_fee, 0, '.', ',') }}
                </td>
                <td class="maroon-accent">{{ $data->statement->financial->annual_fee_terbilang }}</td>
            </tr>
            <tr>
                <td>{{ config('student_approval.step2.labels.text4.english') }}</td>
                <td class="maroon-accent">Rp. {{ number_format($data->statement->financial->school_fee, 0, '.', ',') }}
                </td>
                <td class="maroon-accent">{{ $data->statement->financial->school_fee_terbilang }}</td>
            </tr>
            <tr>
                <td>{{ config('student_approval.step2.labels.text22.english') }}</td>
                <td class="maroon-accent">Rp.
                    {{ number_format($data->statement->financial->uniform_fee, 0, '.', ',') }}</td>
                <td class="maroon-accent">{{ $data->statement->financial->uniform_fee_terbilang }}</td>
            </tr>
            <tr>
                <td>{{ config('student_approval.step2.labels.text23.english') }}</td>
                <td class="maroon-accent">Rp.
                    {{ number_format($data->statement->financial->ittihada_fee, 0, '.', ',') }}</td>
                <td class="maroon-accent">{{ $data->statement->financial->ittihada_fee_terbilang }}</td>
            </tr>
            @if ($data->level->division->name == 'Secondary')
                <tr>
                    <td>{{ config('student_approval.step2.labels.text24.english') }}</td>
                    <td class="maroon-accent">Rp.
                        {{ number_format($data->statement->financial->mhsu_fee, 0, '.', ',') }}</td>
                    <td class="maroon-accent">{{ $data->statement->financial->mhsu_fee_terbilang }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    @foreach ($financialDocument->sections->where('sort_order', '>', 0) as $section)
        <div style="margin: 18px 0 10px; color: #9a3333; font-size: 13px; font-weight: bold;">
            {{ $section->title_en }}
        </div>
        @if ($section->title_id)
            <div class="text-italic" style="margin: 0 0 10px;">{{ $section->title_id }}</div>
        @endif

        @foreach ($section->items->where('number', 0) as $preamble)
            <div style="margin: 10px 0; text-align: justify; white-space: pre-line;">
                <div class="text-bold">{{ $preamble->text_en }}</div>
                <div class="text-italic">{{ $preamble->text_id }}</div>
            </div>
        @endforeach

        @php($numberedItems = $section->items->where('number', '>', 0))
        @if ($numberedItems->isNotEmpty())
            <ol style="padding-left: 22px;">
                @foreach ($numberedItems as $item)
                    <li value="{{ $item->number }}" style="margin-bottom: 12px; page-break-inside: avoid;">
                        <div class="text-bold" style="text-align: justify; white-space: pre-line;">{{ $item->text_en }}</div>
                        <div class="text-italic" style="text-align: justify; white-space: pre-line;">{{ $item->text_id }}</div>
                    </li>
                @endforeach
            </ol>
        @endif
    @endforeach
</div>

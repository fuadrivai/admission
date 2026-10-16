<div class="section">
    <div class="section-title">{{ config('student_approval.step3.title') }}</div>

    @foreach ($parentStatementDocument->sections as $section)
        <div style="margin: 18px 0 10px; color: #9a3333; font-size: 13px; font-weight: bold;">
            {{ $section->title_en }}
        </div>
        @if ($section->title_id)
            <div class="text-italic" style="margin: 0 0 10px;">{{ $section->title_id }}</div>
        @endif

        <ol style="padding-left: 22px;">
            @foreach ($section->items as $item)
                <li style="margin-bottom: 12px;">
                    <div class="text-bold" style="text-align: justify; white-space: pre-line;">{{ $item->text_en }}</div>
                    <div class="text-italic" style="text-align: justify; white-space: normal;">{{ $item->text_id }}
                    </div>
                </li>
            @endforeach
        </ol>
    @endforeach

    @if (
        $parentStatementDocument->description_en ||
            $parentStatementDocument->description_id ||
            $parentStatementDocument->description)
        @if ($parentStatementDocument->description_en || $parentStatementDocument->description)
            <div class="text-bold" style="margin-top: 16px; text-align: justify; white-space: pre-line;">
                {{ $parentStatementDocument->description_en ?? $parentStatementDocument->description }}
            </div>
        @endif
        @if ($parentStatementDocument->description_id)
            <div class="text-italic" style="text-align: justify; white-space: normal;">
                {{ $parentStatementDocument->description_id }}
            </div>
        @endif
    @endif
</div>

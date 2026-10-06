<div>
    <h2 class="section-title">{{ config('student_approval.step3.title') }}</h2>

    <div class="info-box">
        <div><i class="bi bi-info-circle"></i> {{ config('student_approval.step3.labels.text0.english') }}</div>
        <div><i><small>{{ config('student_approval.step3.labels.text0.indonesian') }}</small></i></div>
    </div>

    @if ($parentStatementDocument)
        @foreach ($parentStatementDocument->sections as $section)
            <div class="checkbox-declaration mb-4 statement-section-block">
                <h5 class="fw-bold mb-2">{{ $section->title_en }}</h5>
                <div class="text-muted mb-3"><i>{{ $section->title_id }}</i></div>
                <ol class="ps-3">
                    @foreach ($section->items as $item)
                        <li class="mb-3">
                            <div class="fw-semibold text-muted" style="text-align: justify;">{{ $item->text_en }}</div>
                            <div class="text-muted small" style="text-align: justify;"><i>{{ $item->text_id }}</i></div>
                        </li>
                    @endforeach
                </ol>

                @if ($section->is_required)
                    <div class="form-check mt-3">
                        <input class="form-check-input parent-statement-item" type="checkbox"
                            id="section-agree-{{ $section->id }}" name="statement_item_id[]"
                            value="{{ $section->items->first()->id ?? $section->id }}"
                            data-item-id="{{ $section->id }}" required>
                        <label class="form-check-label" for="section-agree-{{ $section->id }}">
                            I agree
                        </label>
                    </div>
                @endif
            </div>
        @endforeach

        @if ($parentStatementDocument->description_en || $parentStatementDocument->description_id || $parentStatementDocument->description)
            <div class="mt-4">
                @if ($parentStatementDocument->description_en || $parentStatementDocument->description)
                    <div class="fw-bold" style="text-align: justify; white-space: pre-line;">{{ $parentStatementDocument->description_en ?? $parentStatementDocument->description }}</div>
                @endif
                @if ($parentStatementDocument->description_id)
                    <div class="mt-3" style="text-align: justify; white-space: pre-line;">{{ $parentStatementDocument->description_id }}</div>
                @endif
            </div>
        @endif

        <div id="parent-statement-required-error" class="alert alert-danger mt-3 d-none">
            Please check all required parent statement items before continuing.
        </div>
    @else
        <div class="alert alert-warning">No published parent statement is available.</div>
    @endif
</div>

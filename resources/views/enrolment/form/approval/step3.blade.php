<div>
    @php
        $openingSection = $financialDocument ? $financialDocument->sections->firstWhere('sort_order', 0) : null;
    @endphp

    @if ($openingSection)
        <h2 class="section-title">{{ $openingSection->title_en }}</h2>
        <div class="text-muted mb-3"><i>{{ $openingSection->title_id }}</i></div>
        <div class="info-box">
            <div><i class="bi bi-info-circle"></i> {{ config('student_approval.step3.labels.text0.english') }}</div>
            <div><i><small>{{ config('student_approval.step3.labels.text0.indonesian') }}</small></i></div>
        </div>
        @foreach ($openingSection->items->where('number', 0) as $openingText)
            <div class="checkbox-declaration mb-4">
                <p class="text-muted" style="text-align: justify;">{{ $openingText->text_en }}</p>
                <p class="text-muted" style="text-align: justify;"><i>{{ $openingText->text_id }}</i></p>
            </div>
        @endforeach
    @else
        <div class="alert alert-warning">No published Financial Agreement is available.</div>
    @endif

    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <label for="developmentFee"
                class="form-label required">{{ config('student_approval.step2.labels.text2.english') }}</label>
            <div class="money-input-group">
                <span class="input-group-text">Rp</span>
                <input type="text" class="form-control number2" id="developmentFee" placeholder="0" required>
            </div>
            <div class="error-message" id="developmentFee-error">Please enter development fee</div>
            <div class="terbilang-display" id="developmentFeeTerbilang">-</div>
        </div>

        <div class="col-md-6 mb-3">
            <label for="annualFee"
                class="form-label required">{{ config('student_approval.step2.labels.text3.english') }}</label>
            <div class="money-input-group">
                <span class="input-group-text">Rp</span>
                <input type="text" class="form-control number2" id="annualFee" placeholder="0" required>
            </div>
            <div class="error-message" id="annualFee-error">Please enter annual fee</div>
            <div class="terbilang-display" id="annualFeeTerbilang">-</div>
        </div>

        <div class="col-md-6 mb-3 secondary">
            <label for="schoolFee"
                class="form-label required">{{ config('student_approval.step2.labels.text4.english') }}</label>
            <div class="money-input-group">
                <span class="input-group-text">Rp</span>
                <input type="text" class="form-control number2" id="schoolFee" placeholder="0" required>
            </div>
            <div class="error-message" id="schoolFee-error">Please enter school fee</div>
            <div class="terbilang-display" id="schoolFeeTerbilang">-</div>
        </div>
        <div class="col-md-6 mb-3 secondary">
            <label for="uniform"
                class="form-label required">{{ config('student_approval.step2.labels.text22.english') }}</label>
            <div class="money-input-group">
                <span class="input-group-text">Rp</span>
                <input type="text" class="form-control number2" id="uniform" placeholder="0" required>
            </div>
            <div class="error-message" id="uniform-error">Please enter school fee</div>
            <div class="terbilang-display" id="uniformTerbilang">-</div>
        </div>
        <div class="col-md-6 mb-3 secondary">
            <label for="ittihada"
                class="form-label required">{{ config('student_approval.step2.labels.text23.english') }}</label>
            <div class="money-input-group">
                <span class="input-group-text">Rp</span>
                <input type="text" class="form-control number2" id="ittihada" placeholder="0" required>
            </div>
            <div class="error-message" id="ittihada-error">Please enter school fee</div>
            <div class="terbilang-display" id="ittihadaTerbilang">-</div>
        </div>
        <div class="col-md-6 mb-3 div-mhsu">
            <label for="mhsu"
                class="form-label required">{{ config('student_approval.step2.labels.text24.english') }}</label>
            <div class="money-input-group">
                <span class="input-group-text">Rp</span>
                <input type="text" class="form-control number2" id="mhsu" placeholder="0" required>
            </div>
            <div class="error-message" id="mhsu-error">Please enter school fee</div>
            <div class="terbilang-display" id="mhsuTerbilang">-</div>
        </div>
    </div>

    @if ($financialDocument)
        @foreach ($financialDocument->sections->where('sort_order', '>', 0) as $section)
            <section class="checkbox-declaration mb-4">
                <h5 class="fw-bold">{{ $section->title_en }}</h5>
                <div class="text-muted mb-3"><i>{{ $section->title_id }}</i></div>

                @foreach ($section->items->where('number', 0) as $preamble)
                    <div class="mb-3">
                        <p class="text-muted mb-1" style="text-align: justify;">{{ $preamble->text_en }}</p>
                        <p class="text-muted mb-0" style="text-align: justify;"><i>{{ $preamble->text_id }}</i></p>
                    </div>
                @endforeach

                @php($numberedItems = $section->items->where('number', '>', 0))
                @if ($numberedItems->isNotEmpty())
                    <ol class="ps-3">
                        @foreach ($numberedItems as $item)
                            <li value="{{ $item->number }}" class="mb-3">
                                <div class="text-muted" style="text-align: justify;">{{ $item->text_en }}</div>
                                <div class="text-muted" style="text-align: justify;"><i>{{ $item->text_id }}</i>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </section>
        @endforeach

        <h5 class="fw-bold">Final Agreement | <i>Persetujuan Akhir</i></h5>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="agreeFinancialDocument"
                data-document-id="{{ $financialDocument->id }}" required>
            <label class="form-check-label required" for="agreeFinancialDocument">
                I have read, understood, and agree to all provisions of this Financial Agreement.
            </label>
            <div class="error-message" id="agreeFinancialDocument-error">Please agree to the complete Financial
                Agreement.</div>
        </div>
    @endif
</div>

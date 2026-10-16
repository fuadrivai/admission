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
            <div class="checkbox-declaration mb-4 statement-section-block">
                <div class="fw-semibold text-muted" style="text-align: justify; white-space: pre-line;">{{ $openingText->text_en }}</div>
                <div class="text-muted small mt-2" style="text-align: justify; white-space: pre-line;"><i>{{ $openingText->text_id }}</i></div>
            </div>
        @endforeach
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
            <section class="checkbox-declaration mb-4 statement-section-block">
                <h5 class="fw-bold mb-2">{{ $section->title_en }}</h5>
                <div class="text-muted mb-3"><i>{{ $section->title_id }}</i></div>

                @foreach ($section->items->where('number', 0) as $preamble)
                    <div class="mb-3">
                        <div class="fw-semibold text-muted mb-1" style="text-align: justify; white-space: pre-line;">{{ $preamble->text_en }}</div>
                        <div class="text-muted small" style="text-align: justify; white-space: pre-line;"><i>{{ $preamble->text_id }}</i></div>
                    </div>
                @endforeach

                @php($numberedItems = $section->items->where('number', '>', 0))
                @if ($numberedItems->isNotEmpty())
                    @foreach ($numberedItems as $item)
                        <div class="d-flex align-items-start mb-3">
                            <span class="flex-shrink-0 me-2" style="min-width: 1.5rem;">{{ $item->number }}.</span>
                            <div class="flex-grow-1">
                                <div class="fw-semibold text-muted" style="text-align: justify; white-space: pre-line;">{{ $item->text_en }}</div>
                                <div class="text-muted small" style="text-align: justify; white-space: pre-line;"><i>{{ $item->text_id }}</i></div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </section>
        @endforeach

        <div class="checkbox-declaration mb-4 statement-section-block">
            <h5 class="fw-bold mb-2">Final Agreement</h5>
            <div class="text-muted mb-3"><i>Persetujuan Akhir</i></div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="agreeFinancialDocument"
                    data-document-id="{{ $financialDocument->id }}" required>
                <label class="form-check-label required" for="agreeFinancialDocument">
                    I have read, understood, and agree to all provisions of this Financial Agreement.
                </label>
                <div class="error-message" id="agreeFinancialDocument-error">Please agree to the complete Financial
                    Agreement.</div>
            </div>
        </div>
    @endif

    <div class="final-submit-section d-none" id="btn-under-upper-secondary">
        <div class="alert alert-success">
            <h5 class="alert-heading"><i class="bi bi-check-circle-fill"></i>
                {{ config('student_approval.step4.labels.text3.indonesian') }}</h5>
            <p class="mb-3">{{ config('student_approval.step4.labels.text4.indonesian') }}</p>
            <div class="text-start mb-4">
                <label class="form-label required" for="parentSignatureStep3">Parent/Guardian signature image</label>
                <div class="signature-upload">
                    <input
                        type="file"
                        class="signature-upload-input"
                        id="parentSignatureStep3"
                        name="signature"
                        accept="image/jpeg,image/jpg,image/png"
                        required>
                    <label class="signature-upload-prompt" for="parentSignatureStep3">
                        <i class="bi bi-cloud-arrow-up"></i>
                        <span><strong>Choose a signature image</strong><small>JPG or PNG, up to 2 MB</small></span>
                        <span class="btn btn-outline-success btn-sm">Browse</span>
                    </label>
                    <div class="signature-preview d-none">
                        <img src="" alt="Parent/guardian signature preview" class="signature-preview-image">
                        <div class="signature-preview-details">
                            <strong>Signature preview</strong>
                            <span class="signature-file-name"></span>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm remove-signature">
                            <i class="bi bi-x-lg"></i> Remove
                        </button>
                    </div>
                </div>
                <div class="form-text mt-2">Upload one image of the parent/guardian's signature, not the student's.</div>
                <div class="error-message" id="parentSignatureStep3-error">Please upload one parent/guardian signature image.</div>
            </div>
            <button type="button" class="btn btn-success btn-lg final-submit-btn" id="final-submit-btn-1">
                <i class="bi bi-send-check"></i> {{ config('student_approval.step4.labels.text5.indonesian') }}
            </button>
        </div>
    </div>
</div>

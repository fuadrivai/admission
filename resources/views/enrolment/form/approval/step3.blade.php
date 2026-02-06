<div>
    <h2 class="section-title">{{ config('student_approval.step3.title') }}</h2>

    <div class="alert alert-info mb-4">
        <i class="bi bi-info-circle"></i> {{ config('student_approval.step3.labels.text0.indonesian') }}
    </div>

    <div class="checkbox-declaration">
        <div class="statement-item">
            <p>{!! config('student_approval.step3.labels.text1.indonesian') !!}</p>
        </div>

        <div class="statement-item">
            <p>{{ config('student_approval.step3.labels.text2.indonesian') }}</p>
        </div>

        <div class="statement-item">
            <p>{{ config('student_approval.step3.labels.text3.indonesian') }}</p>
        </div>

        <div class="statement-item">
            <p>{{ config('student_approval.step3.labels.text4.indonesian') }}</p>
        </div>

        <div class="statement-item">
            <p>{{ config('student_approval.step3.labels.text5.indonesian') }}</p>
        </div>

        <div class="statement-item">
            <p>{{ config('student_approval.step3.labels.text6.indonesian') }}</p>
        </div>

        <div class="statement-item">
            <p>{{ config('student_approval.step3.labels.text7.indonesian') }}</p>
        </div>

        <div class="statement-item">
            <p>{{ config('student_approval.step3.labels.text8.indonesian') }}</p>
        </div>

        <div class="statement-item">
            <p>{{ config('student_approval.step3.labels.text9.indonesian') }}</p>
        </div>

        <div class="statement-item">
            <p>{{ config('student_approval.step3.labels.text10.indonesian') }}</p>
        </div>

        <div class="statement-item">
            <p>{{ config('student_approval.step3.labels.text11.indonesian') }}</p>
        </div>

        <div class="statement-item">
            <p>{{ config('student_approval.step3.labels.text12.indonesian') }}</p>
        </div>

        <div class="statement-item">
            <p>{{ config('student_approval.step3.labels.text13.indonesian') }}</p>
        </div>

        <div class="statement-item">
            <p>{{ config('student_approval.step3.labels.text14.indonesian') }}</p>
        </div>

        <div class="statement-item">
            <p>{{ config('student_approval.step3.labels.text15.indonesian') }}</p>
        </div>

        <div class="statement-item">
            <p>{{ config('student_approval.step3.labels.text16.indonesian') }}</p>
        </div>

        <div class="statement-item">
            <p>{{ config('student_approval.step3.labels.text17.indonesian') }}</p>

            <div class="current-date-display" id="currentDate2"></div>

            <div class="form-check">
                <input class="form-check-input" type="hidden" id="parentAgreeStatementId">
                <input class="form-check-input" type="checkbox" id="parentAgreeStatement" required>
                <label class="form-check-label required" for="parentAgreeStatement">Yes i agree</label>
                <div class="error-message" id="parentAgreeStatement-error">Please agree to this statement.</div>
            </div>
        </div>
    </div>

    <!-- Final Submit Section  if leve is under Upper Secondary-->
    <div class="final-submit-section d-none" id="btn-under-upper-secondary">
        <div class="alert alert-success">
            <h5 class="alert-heading"><i class="bi bi-check-circle-fill"></i>
                {{ config('student_approval.step4.labels.text3.indonesian') }}</h5>
            <p class="mb-3">{{ config('student_approval.step4.labels.text4.indonesian') }}</p>
            <button type="button" class="btn btn-success btn-lg final-submit-btn" id="final-submit-btn-1">
                <i class="bi bi-send-check"></i> {{ config('student_approval.step4.labels.text5.indonesian') }}
            </button>
        </div>
    </div>
</div>

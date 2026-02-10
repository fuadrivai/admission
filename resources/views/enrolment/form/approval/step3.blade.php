<div>
    <h2 class="section-title">{{ config('student_approval.step3.title') }}</h2>

    <div class="alert alert-info mb-4">
        <label for="">
            <i class="bi bi-info-circle"></i> {{ config('student_approval.step3.labels.text0.english') }}
        </label>
        <br> <i><small>{{ config('student_approval.step3.labels.text0.indonesian') }}</small></i>
    </div>

    <div class="checkbox-declaration">
        <div class="statement-item">
            <label for="">
                {{ config('student_approval.step3.labels.text01.english') }} :
            </label>
            <br> <i><small>{{ config('student_approval.step3.labels.text01.indonesian') }} :</small></i>
        </div>
        <div class="statement-item">
            <label for="">
                {{ config('student_approval.step3.labels.text1.english') }}
            </label>
            <br> <i><small>{{ config('student_approval.step3.labels.text1.indonesian') }}</small></i>
        </div>
        <div class="statement-item">
            <label for="">
                {{ config('student_approval.step3.labels.text2.english') }}
            </label>
            <br> <i><small>{{ config('student_approval.step3.labels.text2.indonesian') }}</small></i>
        </div>
        <div class="statement-item">
            <label for="">
                {{ config('student_approval.step3.labels.text3.english') }}
            </label>
            <br> <i><small>{{ config('student_approval.step3.labels.text3.indonesian') }}</small></i>
        </div>
        <div class="statement-item">
            <label for="">
                {{ config('student_approval.step3.labels.text4.english') }}
            </label>
            <br> <i><small>{{ config('student_approval.step3.labels.text4.indonesian') }}</small></i>
        </div>
        <div class="statement-item">
            <label for="">
                {{ config('student_approval.step3.labels.text5.english') }}
            </label>
            <br> <i><small>{{ config('student_approval.step3.labels.text5.indonesian') }}</small></i>
        </div>
        <div class="statement-item">
            <label for="">
                {{ config('student_approval.step3.labels.text6.english') }}
            </label>
            <br> <i><small>{{ config('student_approval.step3.labels.text6.indonesian') }}</small></i>
        </div>
        <div class="statement-item">
            <label for="">
                {{ config('student_approval.step3.labels.text7.english') }}
            </label>
            <br> <i><small>{{ config('student_approval.step3.labels.text7.indonesian') }}</small></i>
        </div>
        <div class="statement-item">
            <label for="">
                {{ config('student_approval.step3.labels.text8.english') }}
            </label>
            <br> <i><small>{{ config('student_approval.step3.labels.text8.indonesian') }}</small></i>
        </div>
        <div class="statement-item">
            <label for="">
                {{ config('student_approval.step3.labels.text9.english') }}
            </label>
            <br> <i><small>{{ config('student_approval.step3.labels.text9.indonesian') }}</small></i>
        </div>
        <div class="statement-item">
            <label for="">
                {{ config('student_approval.step3.labels.text10.english') }}
            </label>
            <br> <i><small>{{ config('student_approval.step3.labels.text10.indonesian') }}</small></i>
        </div>
        <div class="statement-item">
            <label for="">
                {{ config('student_approval.step3.labels.text11.english') }}
            </label>
            <br> <i><small>{{ config('student_approval.step3.labels.text11.indonesian') }}</small></i>
        </div>
        <div class="statement-item">
            <label for="">
                {{ config('student_approval.step3.labels.text12.english') }}
            </label>
            <br> <i><small>{{ config('student_approval.step3.labels.text12.indonesian') }}</small></i>
        </div>
        <div class="statement-item">
            <label for="">
                {{ config('student_approval.step3.labels.text13.english') }}
            </label>
            <br> <i><small>{{ config('student_approval.step3.labels.text13.indonesian') }}</small></i>
        </div>
        <div class="statement-item">
            <label for="">
                {{ config('student_approval.step3.labels.text14.english') }}
            </label>
            <br> <i><small>{{ config('student_approval.step3.labels.text14.indonesian') }}</small></i>
        </div>
        <div class="statement-item">
            <label for="">
                {{ config('student_approval.step3.labels.text15.english') }}
            </label>
            <br> <i><small>{{ config('student_approval.step3.labels.text15.indonesian') }}</small></i>
        </div>
        <div class="statement-item">
            <label for="">
                {{ config('student_approval.step3.labels.text16.english') }}
            </label>
            <br> <i><small>{{ config('student_approval.step3.labels.text16.indonesian') }}</small></i>
        </div>

        <div class="statement-item">
            <label for="">
                {{ config('student_approval.step3.labels.text17.english') }}
                <br> <i><small>{{ config('student_approval.step3.labels.text17.indonesian') }}</small></i>
            </label>

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

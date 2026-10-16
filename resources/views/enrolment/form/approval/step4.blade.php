<div>
    <h2 class="section-title">{{ config('student_approval.step4.title') }}</h2>

    <div class="checkbox-declaration">
        <div class="statement-item">
            <p>{{ config('student_approval.step4.labels.text1.indonesian') }}</p>
        </div>

        <div class="statement-item">
            <p>{{ config('student_approval.step4.labels.text2.indonesian') }}</p>

            <div class="current-date-display" id="currentDate3"></div>

            <div class="form-check">
                <input class="form-check-input" type="hidden" id="guardianAgreeStatementId">
                <input class="form-check-input" type="checkbox" id="guardianAgreeStatement" required>
                <label class="form-check-label required" for="guardianAgreeStatement">Yes, i agree</label>
                <div class="error-message" id="guardianAgreeStatement-error">Please agree to this statement.</div>
            </div>
        </div>
    </div>
</div>

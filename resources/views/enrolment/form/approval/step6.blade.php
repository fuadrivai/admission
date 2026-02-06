<div>
    <h2 class="section-title">{{ config('student_approval.step6.title') }}</h2>

    <div class="student-info-card">
        <h5 class="mb-3">{{ config('student_approval.step6.labels.text0.indonesian') }}</h5>
        <div class="student-info-row">
            <div class="student-label">{{ config('student_approval.step6.labels.text1.indonesian') }}</div>
            <div class="student-value studentFullName" id="studentFullName3"></div>
        </div>
        <div class="student-info-row">
            <div class="student-label">{{ config('student_approval.step6.labels.text2.indonesian') }}</div>
            <div class="student-value studentAge" id="studentAge3"></div>
        </div>
        <div class="student-info-row">
            <div class="student-label">{{ config('student_approval.step6.labels.text3.indonesian') }}</div>
            <div class="student-value studentLevel" id="studentLevel3"></div>
        </div>
        <div class="student-info-row">
            <div class="student-label">{{ config('student_approval.step6.labels.text4.indonesian') }}</div>
            <div class="student-value studentGrade" id="studentGrade3"></div>
        </div>
        <div class="student-info-row">
            <div class="student-label">{{ config('student_approval.step6.labels.text5.indonesian') }}</div>
            <div class="student-value academicYear" id="academicYear3"></div>
        </div>

        <div class="checkbox-declaration">
            <div class="statement-item">
                <p>{!! config('student_approval.step6.labels.text6.indonesian') !!}</p>

                <div class="current-date-display" id="currentDate5"></div>

                <div class="form-check">
                    <input class="form-check-input" type="hidden" id="studentAgreeStatementId">
                    <input class="form-check-input" type="checkbox" id="studentAgreeStatement" required>
                    <label class="form-check-label required" for="studentAgreeStatement">Yes, i agree</label>
                    <div class="error-message" id="studentAgreeStatement-error">
                        Please agree to this statement.</div>
                </div>
            </div>
        </div>

        <!-- Final Submit Section -->
        <div class="final-submit-section">
            <div class="alert alert-success">
                <h5 class="alert-heading"><i class="bi bi-check-circle-fill"></i>
                    {{ config('student_approval.step4.labels.text3.indonesian') }}</h5>
                <p class="mb-3">{{ config('student_approval.step4.labels.text4.indonesian') }}</p>
                <button type="button" class="btn btn-success btn-lg final-submit-btn" id="final-submit-btn-2">
                    <i class="bi bi-send-check"></i> {{ config('student_approval.step4.labels.text5.indonesian') }}
                </button>
            </div>
        </div>
    </div>
</div>

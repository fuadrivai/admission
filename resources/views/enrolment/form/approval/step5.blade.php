<div>
    <h2 class="section-title">{{ config('student_approval.step5.title') }}</h2>

    <div class="parent-info-card">
        <h5 class="mb-3">{{ config('student_approval.step5.labels.text0.indonesian') }}</h5>
        <div class="parent-info-row">
            <div class="parent-label">{{ config('student_approval.step5.labels.text1.indonesian') }}</div>
            <div class="parent-value parentFullName" id="parentFullName2">-</div>
        </div>
        <div class="parent-info-row">
            <div class="parent-label">{{ config('student_approval.step5.labels.text2.indonesian') }}</div>
            <div class="parent-value parentEmail" id="parentEmail2">-</div>
        </div>
        <div class="parent-info-row">
            <div class="parent-label">{{ config('student_approval.step5.labels.text3.indonesian') }}</div>
            <div class="parent-value parentPhone" id="parentPhone2">-</div>
        </div>
        <div class="parent-info-row">
            <div class="parent-label">{{ config('student_approval.step5.labels.text4.indonesian') }}</div>
            <div class="parent-value parentBirthPlace" id="parentBirthPlace2">-</div>
        </div>
        <div class="parent-info-row">
            <div class="parent-label">{{ config('student_approval.step5.labels.text5.indonesian') }}</div>
            <div class="parent-value parentBirthDate" id="parentBirthDate2">-</div>
        </div>
        <div class="parent-info-row">
            <div class="parent-label">{{ config('student_approval.step5.labels.text6.indonesian') }}</div>
            <div class="parent-value parentIdCard" id="parentIdCard2">-</div>
        </div>
    </div>

    <div class="statement-item">
        <p>{{ config('student_approval.step5.labels.text7.indonesian') }}</p>
    </div>

    <div class="student-info-card">
        <h5 class="mb-3">{{ config('student_approval.step5.labels.text8.indonesian') }}</h5>
        <div class="student-info-row">
            <div class="student-label">{{ config('student_approval.step5.labels.text9.indonesian') }}</div>
            <div class="student-value studentFullName" id="studentFullName2">-</div>
        </div>
        <div class="student-info-row">
            <div class="student-label">{{ config('student_approval.step5.labels.text10.indonesian') }}</div>
            <div class="student-value studentAge" id="studentAge2">-</div>
        </div>
        <div class="student-info-row">
            <div class="student-label">{{ config('student_approval.step5.labels.text11.indonesian') }}</div>
            <div class="student-value studentLevel" id="studentLevel2">-</div>
        </div>
        <div class="student-info-row">
            <div class="student-label">{{ config('student_approval.step5.labels.text12.indonesian') }}</div>
            <div class="student-value studentGrade" id="studentGrade2">-</div>
        </div>
        <div class="student-info-row">
            <div class="student-label">{{ config('student_approval.step5.labels.text13.indonesian') }}</div>
            <div class="student-value academicYear" id="academicYear2">-</div>
        </div>
    </div>

    <div class="checkbox-declaration">
        <div class="statement-item">
            <p>{!! config('student_approval.step5.labels.text14.indonesian') !!}</p>
            <div class="current-date-display" id="currentDate4"></div>
            <div class="form-check">
                <input class="form-check-input" type="hidden" id="narcoticaAgreeStatementId">
                <input class="form-check-input" type="checkbox" id="narcoticaAgreeStatement" required>
                <label class="form-check-label required" for="narcoticaAgreeStatement">Yes, i agree</label>
                <div class="error-message" id="narcoticaAgreeStatement-error">
                    Please agree to this statement.</div>
            </div>
        </div>
    </div>
</div>

<div>
    <h3 class="subsection-title">{{ config('student_approval.step1.title') }}</h3>

    <div class="mb-4">
        <input type="hidden" id="admission-code" value="{{ $code }}">
        <label for="parentSelector"
            class="form-label required">{{ config('student_approval.step1.labels.text0.english') }}</label>
        <select class="form-select" id="parentSelector" required>
            <option value="" selected disabled>Select an option</option>
            <option value="father">Father</option>
            <option value="mother">Mother</option>
            <option value="guardian">Guardian</option>
        </select>
        <div class="error-message" id="parentSelector-error">Please select a relationship</div>
    </div>

    <!-- Parent Information Display -->
    <div class="parent-info-card" id="parentInfoCard" style="display: none;">
        <h5 class="mb-3">{{ config('student_approval.step1.labels.text1.english') }}</h5>
        <div class="parent-info-row">
            <div class="parent-label">{{ config('student_approval.step1.labels.text2.english') }}:</div>
            <div class="parent-value parentFullName" id="parentFullName">-</div>
        </div>
        <div class="parent-info-row">
            <div class="parent-label">{{ config('student_approval.step1.labels.text3.english') }}</div>
            <div class="parent-value parentEmail" id="parentEmail">-</div>
        </div>
        <div class="parent-info-row">
            <div class="parent-label">{{ config('student_approval.step1.labels.text4.english') }}</div>
            <div class="parent-value parentPhone" id="parentPhone">-</div>
        </div>
        <div class="parent-info-row">
            <div class="parent-label">{{ config('student_approval.step1.labels.text5.english') }}</div>
            <div class="parent-value parentBirthPlace" id="parentBirthPlace">-</div>
        </div>
        {{-- <div class="parent-info-row">
            <div class="parent-label">{{ config('student_approval.step1.labels.text6.english') }}</div>
            <div class="parent-value parentBirthDate" id="parentBirthDate">-</div>
        </div> --}}
        <div class="parent-info-row">
            <div class="parent-label">{{ config('student_approval.step1.labels.text7.english') }}</div>
            <div class="parent-value parentIdCard" id="parentIdCard">-</div>
        </div>
    </div>

    <div class="statement-item">
        <p>{{ config('student_approval.step1.labels.text8.english') }}
            <br><i><small>{{ config('student_approval.step1.labels.text8.indonesian') }}</small></i>
        </p>
    </div>

    <!-- Student Information Display -->
    <div class="student-info-card">
        <h5 class="mb-3">{{ config('student_approval.step1.labels.text13.english') }}</h5>
        <div class="student-info-row">
            <div class="student-label">{{ config('student_approval.step1.labels.text2.english') }}</div>
            <div class="student-value studentFullName" id="studentFullName">--</div>
        </div>
        <div class="student-info-row">
            <div class="student-label">{{ config('student_approval.step1.labels.text9.english') }}</div>
            <div class="student-value studentAge" id="studentAge">--</div>
        </div>
        <div class="student-info-row">
            <div class="student-label">{{ config('student_approval.step1.labels.text10.english') }}</div>
            <div class="student-value studentLevel" id="studentLevel">-</div>
        </div>
        {{-- <div class="student-info-row">
            <div class="student-label">{{ config('student_approval.step1.labels.text11.english') }}</div>
            <div class="student-value studentGrade" id="studentGrade">--</div>
        </div> --}}
        <div class="student-info-row">
            <div class="student-label">{{ config('student_approval.step1.labels.text12.english') }}</div>
            <div class="student-value academicYear" id="academicYear">--</div>
        </div>
    </div>
</div>

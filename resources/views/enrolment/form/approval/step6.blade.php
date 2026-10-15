<div>
    <h2 class="section-title">{{ config('student_approval.step6.title') }}</h2>

    <div class="student-info-card">
        <h5 class="mb-3">{{ config('student_approval.step6.labels.text0.english') }}</h5>
        <div class="student-info-row">
            <div class="student-label">{{ config('student_approval.step6.labels.text1.english') }}</div>
            <div class="student-value studentFullName" id="studentFullName3"></div>
        </div>
        <div class="student-info-row">
            <div class="student-label">{{ config('student_approval.step6.labels.text2.english') }}</div>
            <div class="student-value studentAge" id="studentAge3"></div>
        </div>
        <div class="student-info-row">
            <div class="student-label">{{ config('student_approval.step6.labels.text3.english') }}</div>
            <div class="student-value studentLevel" id="studentLevel3"></div>
        </div>
        {{-- <div class="student-info-row">
            <div class="student-label">{{ config('student_approval.step6.labels.text4.english') }}</div>
            <div class="student-value studentGrade" id="studentGrade3"></div>
        </div> --}}
        <div class="student-info-row">
            <div class="student-label">{{ config('student_approval.step6.labels.text5.english') }}</div>
            <div class="student-value academicYear" id="academicYear3"></div>
        </div>

        <div class="checkbox-declaration">
            <div class="statement-item">
                <p>{{ config('student_approval.step6.labels.text6.english') }}
                    <br><i><small>{{ config('student_approval.step6.labels.text6.indonesian') }}</small></i>
                </p>
                <p>{{ config('student_approval.step6.labels.text7.english') }}
                    <br><i><small>{{ config('student_approval.step6.labels.text7.indonesian') }}</small></i>
                </p>

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
                    {{ config('student_approval.step4.labels.text3.english') }}</h5>
                <p class="mb-3">{{ config('student_approval.step4.labels.text4.english') }}</p>
                <div class="text-start mb-4">
                    <label class="form-label required" for="parentSignature">Parent/Guardian signature image</label>
                    <div class="signature-upload">
                        <input
                            type="file"
                            class="signature-upload-input"
                            id="parentSignature"
                            name="signature"
                            accept="image/jpeg,image/jpg,image/png"
                            required>
                        <label class="signature-upload-prompt" for="parentSignature">
                            <i class="bi bi-cloud-arrow-up"></i>
                            <span><strong>Choose a signature image</strong><small>JPG or PNG, up to 2 MB</small></span>
                            <span class="btn btn-outline-success btn-sm">Browse</span>
                        </label>
                        <div class="signature-preview d-none" id="parentSignaturePreview">
                            <img src="" alt="Parent/guardian signature preview" id="parentSignaturePreviewImage">
                            <div class="signature-preview-details">
                                <strong>Signature preview</strong>
                                <span id="parentSignatureFileName"></span>
                            </div>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="removeParentSignature">
                                <i class="bi bi-x-lg"></i> Remove
                            </button>
                        </div>
                    </div>
                    <div class="form-text mt-2">Upload one image of the parent/guardian's signature, not the student's.</div>
                    <div class="error-message" id="parentSignature-error">Please upload one parent/guardian signature image.</div>
                </div>
                <button type="button" class="btn btn-success btn-lg final-submit-btn" id="final-submit-btn-2">
                    <i class="bi bi-send-check"></i> {{ config('student_approval.step4.labels.text5.english') }}
                </button>
            </div>
        </div>
    </div>
</div>

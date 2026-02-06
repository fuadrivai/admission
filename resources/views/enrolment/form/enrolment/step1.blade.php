<div>
    <div class="arabic-text">
        {{-- ٱلسَّلَامُ عَلَيْكُمْ وَرَحْمَةُ ٱللَّٰهِ وَبَرَكَاتُهُ --}}
        {{ config('student_enrolment.step1.labels.text0.english') }}
    </div>

    <p class="mb-3" style="text-align: justify">{{ config('student_enrolment.step1.labels.text1.english') }}</p>
    <p><small><i>{{ config('student_enrolment.step1.labels.text1.indonesian') }}</i></small></p>
    <div class="info-box">
        <h5 class="mb-2">{{ config('student_enrolment.step1.labels.text2.english') }}</h5>
        <ol class="mb-0">
            <li>{{ config('student_enrolment.step1.labels.text3.english') }}</li>
            <li>{{ config('student_enrolment.step1.labels.text4.english') }}</li>
            <li>{{ config('student_enrolment.step1.labels.text5.english') }}</li>
        </ol>
    </div>

    <p class="mb-4">{!! config('student_enrolment.step1.labels.text6.english') !!}</p>
    <p class="mb-4">{!! config('student_enrolment.step1.labels.text7.english') !!}</p>
    <p class="mb-4"><em>{{ config('student_enrolment.step1.labels.text8.english') }}</em></p>

    <div class="card border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">{{ config('student_enrolment.step1.labels.text9.english') }}</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <input type="text" class="form-control" value="{{ $data['code'] ?? '' }}" id="enrollmentCode"
                    placeholder="kode pendaftaran Ananda" required>
                <input type="text" class="form-control" id="admissionID" hidden>
                <input type="text" class="form-control" id="applicantId" hidden>
                <div class="error-message" id="enrollmentCode-error">Harap masukkan kode pendaftaran yang valid
                </div>
            </div>
        </div>
    </div>
</div>

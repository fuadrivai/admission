<div>
    <h2 class="section-title">{{ config('student_approval.step2.title') }}</h2>

    <div class="statement-item">
        <p>{{ config('student_approval.step2.labels.text1.english') }}
            <br> <i><small>{{ config('student_approval.step2.labels.text1.indonesian') }}</small></i>
        </p>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="agreePayment1" required>
            <label class="form-check-label required" for="agreePayment1">Yes, i Agree</label>
            <div class="error-message" id="agreePayment1-error">Please agree to this statement.</div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <label for="developmentFee"
                class="form-label required">{{ config('student_approval.step2.labels.text2.english') }}</label>
            <div class="money-input-group">
                <span class="input-group-text">Rp</span>
                <input type="text" class="form-control number2" id="developmentFee" placeholder="12,500,000"
                    required>
            </div>
            <div class="error-message" id="developmentFee-error">Please enter development fee</div>
            <div class="terbilang-display" id="developmentFeeTerbilang">-</div>
        </div>

        <div class="col-md-6 mb-3">
            <label for="annualFee"
                class="form-label required">{{ config('student_approval.step2.labels.text3.english') }}</label>
            <div class="money-input-group">
                <span class="input-group-text">Rp</span>
                <input type="text" class="form-control number2" id="annualFee" placeholder="4,000,000" required>
            </div>
            <div class="error-message" id="annualFee-error">Please enter annual fee</div>
            <div class="terbilang-display" id="annualFeeTerbilang">-</div>
        </div>

        <div class="col-md-6 mb-3 secondary">
            <label for="schoolFee"
                class="form-label required">{{ config('student_approval.step2.labels.text4.english') }}</label>
            <div class="money-input-group">
                <span class="input-group-text">Rp</span>
                <input type="text" class="form-control number2" id="schoolFee" placeholder="1,750,000" required>
            </div>
            <div class="error-message" id="schoolFee-error">Please enter school fee</div>
            <div class="terbilang-display" id="schoolFeeTerbilang">-</div>
        </div>
        <div class="col-md-6 mb-3 secondary">
            <label for="uniform"
                class="form-label required">{{ config('student_approval.step2.labels.text22.english') }}</label>
            <div class="money-input-group">
                <span class="input-group-text">Rp</span>
                <input type="text" class="form-control number2" id="uniform" placeholder="1,750,000" required>
            </div>
            <div class="error-message" id="uniform-error">Please enter school fee</div>
            <div class="terbilang-display" id="uniformTerbilang">-</div>
        </div>
        <div class="col-md-6 mb-3 secondary">
            <label for="ittihada"
                class="form-label required">{{ config('student_approval.step2.labels.text23.english') }}</label>
            <div class="money-input-group">
                <span class="input-group-text">Rp</span>
                <input type="text" class="form-control number2" id="ittihada" placeholder="1,750,000" required>
            </div>
            <div class="error-message" id="ittihada-error">Please enter school fee</div>
            <div class="terbilang-display" id="ittihadaTerbilang">-</div>
        </div>
        <div class="col-md-6 mb-3 div-mhsu">
            <label for="mhsu"
                class="form-label required">{{ config('student_approval.step2.labels.text24.english') }}</label>
            <div class="money-input-group">
                <span class="input-group-text">Rp</span>
                <input type="text" class="form-control number2" id="mhsu" placeholder="1,750,000" required>
            </div>
            <div class="error-message" id="mhsu-error">Please enter school fee</div>
            <div class="terbilang-display" id="mhsuTerbilang">-</div>
        </div>
    </div>

    <h6>{{ config('student_approval.step2.labels.text5.english') }}</h6>

    <div class="checkbox-declaration">
        <div class="statement-item">
            <p>{{ config('student_approval.step2.labels.text6.english') }}
                <br><i><small>{{ config('student_approval.step2.labels.text6.indonesian') }}</small></i>
            </p>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="agreePayment2" required>
                <label class="form-check-label required" for="agreePayment2">Yes, i agree</label>
                <div class="error-message" id="agreePayment2-error">Please agree to this statement.</div>
            </div>
        </div>

        <div class="statement-item">
            <p>{{ config('student_approval.step2.labels.text7.english') }}<br>
                <i><small>{{ config('student_approval.step2.labels.text7.indonesian') }}</small></i>
            </p>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="agreePayment3" required>
                <label class="form-check-label required" for="agreePayment3">Yes, i agree</label>
                <div class="error-message" id="agreePayment3-error">Please agree to this statement.</div>
            </div>
        </div>

        <div class="statement-item">
            <p>{{ config('student_approval.step2.labels.text8.english') }}<br><i><small>{{ config('student_approval.step2.labels.text8.indonesian') }}</small></i>
            </p>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="agreePayment4" required>
                <label class="form-check-label required" for="agreePayment4">Yes, i agree</label>
                <div class="error-message" id="agreePayment4-error">Please agree to this statement.</div>
            </div>
        </div>

        <div class="statement-item">
            <p>{{ config('student_approval.step2.labels.text9.english') }} <br>
                <i><small>{{ config('student_approval.step2.labels.text9.indonesian') }}</small></i>
            </p>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="agreePayment5" required>
                <label class="form-check-label required" for="agreePayment5">Yes, i agree</label>
                <div class="error-message" id="agreePayment5-error">Please agree to this statement.</div>
            </div>
        </div>

        <div class="statement-item">
            <p>{{ config('student_approval.step2.labels.text10.english') }} <br>
                <i><small>{{ config('student_approval.step2.labels.text10.indonesian') }}</small></i>
            </p>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="agreePayment6" required>
                <label class="form-check-label required" for="agreePayment6">Yes, i agree</label>
                <div class="error-message" id="agreePayment6-error">Please agree to this statement.</div>
            </div>
        </div>

        <div class="statement-item">
            <p>{{ config('student_approval.step2.labels.text11.english') }}
                <br>
                <i><small>{{ config('student_approval.step2.labels.text11.indonesian') }}</small></i>
            </p>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="agreePayment7" required>
                <label class="form-check-label required" for="agreePayment7">Yes, i agree</label>
                <div class="error-message" id="agreePayment7-error">Please agree to this statement.</div>
            </div>
        </div>

        <div class="statement-item">
            <p>{{ config('student_approval.step2.labels.text12.english') }} <br>
                <i><small>{{ config('student_approval.step2.labels.text12.indonesian') }}</small></i>
            </p>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="agreePayment8" required>
                <label class="form-check-label required" for="agreePayment8">Yes, i agree</label>
                <div class="error-message" id="agreePayment8-error">Please agree to this statement.</div>
            </div>
        </div>

        <div class="statement-item">
            <p>{{ config('student_approval.step2.labels.text13.english') }}<br>
                <i><small>{{ config('student_approval.step2.labels.text13.indonesian') }}</small></i>
            </p>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="agreePayment9" required>
                <label class="form-check-label required" for="agreePayment9">Yes, i agree</label>
                <div class="error-message" id="agreePayment9-error">Please agree to this statement.</div>
            </div>
        </div>

        <div class="statement-item">
            <p>{{ config('student_approval.step2.labels.text14.english') }}<br>
                <i><small>{{ config('student_approval.step2.labels.text14.indonesian') }}</small></i>
            </p>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="agreePayment10" required>
                <label class="form-check-label required" for="agreePayment10">Yes, i agree</label>
                <div class="error-message" id="agreePayment10-error">Please agree to this statement.</div>
            </div>
        </div>

        <div class="statement-item">
            <p>{{ config('student_approval.step2.labels.text15.english') }} <br>
                <i><small>{{ config('student_approval.step2.labels.text15.indonesian') }}</small></i>
            </p>
            <ol type="A" class="mb-3">
                <li>{{ config('student_approval.step2.labels.text16.english') }}
                    <br><i><small>{{ config('student_approval.step2.labels.text16.indonesian') }}</small></i>
                </li>
                <li>{{ config('student_approval.step2.labels.text17.english') }}
                    <br><i><small>{{ config('student_approval.step2.labels.text17.indonesian') }}</small></i>
                </li>
                <li>{{ config('student_approval.step2.labels.text18.english') }}
                    <br><i><small>{{ config('student_approval.step2.labels.text18.indonesian') }}</small></i>
                </li>
                <li>{{ config('student_approval.step2.labels.text19.english') }}
                    <br><i><small>{{ config('student_approval.step2.labels.text19.indonesian') }}</small></i>
                </li>
                <li>{{ config('student_approval.step2.labels.text20.english') }}
                    <br><i><small>{{ config('student_approval.step2.labels.text20.indonesian') }}</small></i>
                </li>
            </ol>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="agreePayment11" required>
                <label class="form-check-label required" for="agreePayment11">Yes, i agree</label>
                <div class="error-message" id="agreePayment11-error">Please agree to this statement.</div>
            </div>
        </div>

        <div class="statement-item">
            <p>{{ config('student_approval.step2.labels.text21.english') }}
                <br><i><small>{{ config('student_approval.step2.labels.text21.indonesian') }}</small></i>
            </p>

            <div class="current-date-display" id="currentDate1"></div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="agreePayment12" required>
                <label class="form-check-label required" for="agreePayment12">Yes, i agree</label>
                <div class="error-message" id="agreePayment12-error">Please agree to this statement.</div>
            </div>
        </div>
    </div>
</div>

@extends('main-layout.index')

@section('content-style')
    <link rel="stylesheet" href="/assets/static/css/document-detail.css?v=1.1.0">
    <style>
        .text-italic {
            font-style: italic;
        }
    </style>
@endsection

@section('content-child')
    <section class="section">
        <div class="detail-container">
            <!-- Left Sidebar (30%) -->
            <div class="detail-sidebar">
                <div class="sidebar-card">
                    <!-- Header with Avatar -->
                    <div class="sidebar-profile">
                        <div class="student-avatar-large">{{ $admission->avatarName() }}</div>
                        <div class="student-fullname">{{ $admission->applicantName() ?? 'N/A' }}</div>
                        <div class="student-meta">
                            Code: {{ $admission->code ?? 'N/A' }}
                        </div>

                        <div class="student-source" style="margin-top:6px;">
                            <span class="label">Source:</span>
                            <span class="value">
                                @if (isset($admission->enrolment) && $admission->enrolment->source_data)
                                    <span
                                        class="badge text-bg-info">{{ ucfirst($admission->enrolment->source_data) }}</span>
                                @else
                                    <span class="badge text-bg-secondary">-</span>
                                @endif
                            </span>
                        </div>

                        <div class="student-doc-status" style="margin-top:6px;">
                            <span class="label">Document Status:</span>
                            <span class="value">
                                @if (isset($admission) && $admission->status == 1)
                                    <span class="badge text-bg-success">All Complete</span>
                                @else
                                    <span class="badge text-bg-warning">In Progress</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Student Info -->
                    <div class="sidebar-info">
                        <div class="info-item">
                            <div class="info-label">Branch:</div>
                            <div class="info-value">{{ $admission->branch->name ?? '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Level:</div>
                            <div class="info-value">{{ $admission->level->name ?? '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Grade:</div>
                            <div class="info-value">{{ $admission->grade->name ?? '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">A.Y:</div>
                            <div class="info-value">{{ $admission->accademic_year ?? '-' }}</div>
                        </div>
                    </div>

                    <!-- Menu Tabs -->
                    <ul class="menu-tabs">
                        <li>
                            <a class="menu-link active" data-tab="student-tab" href="javascript:void(0)">
                                <i class="fa fa-user"></i>
                                <span>Student</span>
                            </a>
                        </li>
                        <li>
                            <a class="menu-link" data-tab="document-tab" href="javascript:void(0)">
                                <i class="fa fa-file"></i>
                                <span>Documents</span>
                            </a>
                        </li>
                        <li>
                            <a class="menu-link" data-tab="statement-tab" href="javascript:void(0)">
                                <i class="fa fa-check"></i>
                                <span>Statement</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Right Content (70%) -->
            <div class="detail-content">
                <!-- Student Tab -->
                <div id="student-tab" class="tab-content active">
                    <div class="content-card student-wizard-card">
                        <!-- Wizard Steps -->
                        <div class="wizard-steps">
                            <div class="wizard-step active" data-step="1">
                                <div class="wizard-step-icon"><i class="fa fa-user"></i></div>
                                <span class="wizard-step-label">Student</span>
                            </div>
                            <div class="wizard-step-line"></div>
                            <div class="wizard-step" data-step="2">
                                <div class="wizard-step-icon"><i class="fa fa-users"></i></div>
                                <span class="wizard-step-label">Parent</span>
                            </div>
                            <div class="wizard-step-line"></div>
                            <div class="wizard-step" data-step="3">
                                <div class="wizard-step-icon"><i class="fa fa-heartbeat"></i></div>
                                <span class="wizard-step-label">Health</span>
                            </div>
                            <div class="wizard-step-line"></div>
                            <div class="wizard-step" data-step="4">
                                <div class="wizard-step-icon"><i class="fa fa-check"></i></div>
                                <span class="wizard-step-label">Agreement</span>
                            </div>
                        </div>

                        <!-- Wizard Panels -->
                        <div class="wizard-panels">
                            <!-- Step 1: Student -->
                            @php
                                $a = $admission->applicant;
                                $formatYesNo = fn($v) => $v ? 'Yes' : 'No';
                            @endphp
                            <div class="wizard-panel active" data-panel="1">
                                <div class="content-header">
                                    <h3><i class="fa fa-id-card"></i> Student Information</h3>
                                </div>
                                <div class="content-body content-body-stack">
                                    <div class="info-cards-vertical">
                                        <div class="info-card">
                                            <div class="info-card-title"><i class="fa fa-user"></i> Personal Information
                                            </div>
                                            <ul class="info-list info-list-stack">
                                                <li><span class="label">Full Name</span><span
                                                        class="value">{{ $a->fullname ?? '-' }}</span></li>
                                                <li><span class="label">Nickname</span><span
                                                        class="value">{{ $a->nickname ?? '-' }}</span></li>
                                                <li><span class="label">Gender</span><span
                                                        class="value">{{ ucfirst($a->gender ?? '-') }}</span></li>
                                                <li><span class="label">Date of Birth</span><span
                                                        class="value">{{ $a->dateBirth() ?? '-' }}</span></li>
                                                <li><span class="label">Place of Birth</span><span
                                                        class="value">{{ ucfirst($a->place_of_birth ?? '-') }}</span></li>
                                                <li><span class="label">Age</span><span
                                                        class="value">{{ $a->age() ?? '-' }}</span></li>
                                                <li><span class="label">Height (cm)</span><span
                                                        class="value">{{ $a->height ? number_format($a->height, 0) : '-' }}</span>
                                                </li>
                                                <li><span class="label">Weight (kg)</span><span
                                                        class="value">{{ $a->weight ? number_format($a->weight, 0) : '-' }}</span>
                                                </li>
                                                <li><span class="label">Religion</span><span
                                                        class="value">{{ ucfirst($a->religion ?? '-') }}</span></li>
                                                <li><span class="label">Ethnicity</span><span
                                                        class="value">{{ $a->ethnicity ?? '-' }}</span></li>
                                                <li><span class="label">Citizenship</span><span
                                                        class="value">{{ $a->citizenship ?? '-' }}</span></li>
                                                <li><span class="label">Siblings Count</span><span
                                                        class="value">{{ $a->siblings_count ?? '-' }}</span></li>
                                                <li><span class="label">Birth Order</span><span
                                                        class="value">{{ $a->birth_order ?? '-' }}</span></li>
                                                <li><span class="label">Home Language</span><span
                                                        class="value">{{ $a->home_language ?? '-' }}</span></li>
                                                <li><span class="label">Other Languages</span><span
                                                        class="value">{{ $a->other_languages ?? '-' }}</span></li>
                                            </ul>
                                        </div>

                                        <div class="info-card">
                                            <div class="info-card-title"><i class="fa fa-home"></i> Address & Contact
                                            </div>
                                            <ul class="info-list info-list-stack">
                                                <li><span class="label">Address</span><span
                                                        class="value">{{ $a->address ?? '-' }}</span></li>
                                                <li><span class="label">Zipcode</span><span
                                                        class="value">{{ $a->zipcode ?? '-' }}</span></li>
                                                <li><span class="label">Parent Phone</span><span
                                                        class="value">{{ $a->parent_phone ?? '-' }}</span></li>
                                                <li><span class="label">Home Phone</span><span
                                                        class="value">{{ $a->home_phone ?? '-' }}</span></li>
                                                <li><span class="label">Living With</span><span
                                                        class="value">{{ $a->living_with ?? '-' }}</span></li>
                                                <li><span class="label">Living With Other</span><span
                                                        class="value">{{ $a->living_with_other ?? '-' }}</span></li>
                                                <li><span class="label">Distance to School (km)</span><span
                                                        class="value">{{ $a->distance_km ? number_format($a->distance_km, 1) : '-' }}</span>
                                                </li>
                                                <li><span class="label">Emergency Contact Priority</span><span
                                                        class="value">{{ $a->emergency_contact_priority ?? '-' }}</span>
                                                </li>
                                                <li><span class="label">Emergency Contact Phone</span><span
                                                        class="value">{{ $a->emergency_contact_phone ?? '-' }}</span>
                                                </li>
                                            </ul>
                                        </div>

                                        <div class="info-card">
                                            <div class="info-card-title"><i class="fa fa-graduation-cap"></i> School
                                                History</div>
                                            <ul class="info-list info-list-stack">
                                                <li><span class="label">Previous School</span><span
                                                        class="value">{{ $a->previous_school ?? '-' }}</span></li>
                                                <li><span class="label">Previous School Address</span><span
                                                        class="value">{{ $a->previous_school_address ?? '-' }}</span>
                                                </li>
                                                <li><span class="label">Graduation Year</span><span
                                                        class="value">{{ $a->graduation_year ?? '-' }}</span></li>
                                                <li><span class="label">Ever Not School</span><span
                                                        class="value">{{ isset($a->ever_not_school) ? $formatYesNo($a->ever_not_school) : '-' }}</span>
                                                </li>
                                                <li><span class="label">Not School Duration</span><span
                                                        class="value">{{ $a->not_school_duration ?? '-' }}</span></li>
                                                <li><span class="label">Not School Reason</span><span
                                                        class="value">{{ $a->not_school_reason ?? '-' }}</span></li>
                                                <li><span class="label">Dev Assessment</span><span
                                                        class="value">{{ isset($a->dev_check) ? $formatYesNo($a->dev_check) : '-' }}</span>
                                                </li>
                                                <li><span class="label">Dev Diagnosis</span><span
                                                        class="value">{{ $a->dev_diagnosis ?? '-' }}</span></li>
                                                <li><span class="label">Therapy History</span><span
                                                        class="value">{{ isset($a->therapy_history) ? $formatYesNo($a->therapy_history) : '-' }}</span>
                                                </li>
                                                <li><span class="label">Therapy Detail</span><span
                                                        class="value">{{ $a->therapy_detail ?? '-' }}</span></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2: Parent -->
                            <div class="wizard-panel" data-panel="2">
                                <div class="content-header">
                                    <h3><i class="fa fa-users"></i> Parent / Guardian Information</h3>
                                </div>
                                <div class="content-body content-body-stack">
                                    @php
                                        $parents = $admission->applicant->parents->keyBy('role');
                                        $father = $parents->get('father');
                                        $mother = $parents->get('mother');
                                        $guardian = $parents->get('guardian');
                                    @endphp

                                    <div class="parent-block" style="margin-bottom: 30px;">
                                        <div class="parent-block-title parent-block-father">
                                            <i class="fa fa-male"></i> Father
                                        </div>
                                        <div class="info-card">
                                            <ul class="info-list info-list-stack">
                                                <li><span class="label">Full Name</span><span
                                                        class="value">{{ $father->fullname ?? '-' }}</span></li>
                                                <li><span class="label">Phone</span><span
                                                        class="value">{{ $father->phone ?? '-' }}</span></li>
                                                <li><span class="label">Home Phone</span><span
                                                        class="value">{{ $father->home_phone ?? '-' }}</span></li>
                                                <li><span class="label">Email</span><span
                                                        class="value">{{ $father->email ?? '-' }}</span></li>
                                                <li><span class="label">Birth Place</span><span
                                                        class="value">{{ $father ? $father->birth_place ?? '-' : '-' }}</span>
                                                </li>
                                                <li><span class="label">Birth Date</span><span
                                                        class="value">{{ $father && $father->birth_date ? $father->dateBirth() : '-' }}</span>
                                                </li>
                                                <li><span class="label">Identity Number</span><span
                                                        class="value">{{ $father->identity_number ?? '-' }}</span></li>
                                                <li><span class="label">Religion</span><span
                                                        class="value">{{ $father->religion ?? '-' }}</span></li>
                                                <li><span class="label">Ethnicity</span><span
                                                        class="value">{{ $father->ethnicity ?? '-' }}</span></li>
                                                <li><span class="label">Address</span><span
                                                        class="value">{{ $father->address ?? '-' }}</span></li>
                                                <li><span class="label">Zipcode</span><span
                                                        class="value">{{ $father->zipcode ?? '-' }}</span></li>
                                                <li><span class="label">Education</span><span
                                                        class="value">{{ $father->education ?? '-' }}</span></li>
                                                <li><span class="label">Occupation</span><span
                                                        class="value">{{ $father->occupation ?? '-' }}</span></li>
                                                <li><span class="label">Company Name</span><span
                                                        class="value">{{ $father->company_name ?? '-' }}</span></li>
                                                <li><span class="label">Company Address</span><span
                                                        class="value">{{ $father->company_address ?? '-' }}</span></li>
                                                <li><span class="label">Marital Status</span><span
                                                        class="value">{{ $father->marital_status ?? '-' }}</span></li>
                                                <li><span class="label">Monthly Income</span><span
                                                        class="value">{{ $father && $father->monthly_income !== null ? 'Rp ' . $father->monthlyIncome() : '-' }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="parent-block" style="margin-bottom: 30px;">
                                        <div class="parent-block-title parent-block-mother">
                                            <i class="fa fa-female"></i> Mother
                                        </div>
                                        <div class="info-card">
                                            <ul class="info-list info-list-stack">
                                                <li><span class="label">Full Name</span><span
                                                        class="value">{{ $mother->fullname ?? '-' }}</span></li>
                                                <li><span class="label">Phone</span><span
                                                        class="value">{{ $mother->phone ?? '-' }}</span></li>
                                                <li><span class="label">Home Phone</span><span
                                                        class="value">{{ $mother->home_phone ?? '-' }}</span></li>
                                                <li><span class="label">Email</span><span
                                                        class="value">{{ $mother->email ?? '-' }}</span></li>
                                                <li><span class="label">Birth Place</span><span
                                                        class="value">{{ $mother->birth_place ?? '-' }}</span></li>
                                                <li><span class="label">Birth Date</span><span
                                                        class="value">{{ $mother && $mother->birth_date ? $mother->dateBirth() : '-' }}</span>
                                                </li>
                                                <li><span class="label">Identity Number</span><span
                                                        class="value">{{ $mother->identity_number ?? '-' }}</span></li>
                                                <li><span class="label">Religion</span><span
                                                        class="value">{{ $mother->religion ?? '-' }}</span></li>
                                                <li><span class="label">Ethnicity</span><span
                                                        class="value">{{ $mother->ethnicity ?? '-' }}</span></li>
                                                <li><span class="label">Address</span><span
                                                        class="value">{{ $mother->address ?? '-' }}</span></li>
                                                <li><span class="label">Zipcode</span><span
                                                        class="value">{{ $mother->zipcode ?? '-' }}</span></li>
                                                <li><span class="label">Education</span><span
                                                        class="value">{{ $mother->education ?? '-' }}</span></li>
                                                <li><span class="label">Occupation</span><span
                                                        class="value">{{ $mother->occupation ?? '-' }}</span></li>
                                                <li><span class="label">Company Name</span><span
                                                        class="value">{{ $mother->company_name ?? '-' }}</span></li>
                                                <li><span class="label">Company Address</span><span
                                                        class="value">{{ $mother->company_address ?? '-' }}</span></li>
                                                <li><span class="label">Marital Status</span><span
                                                        class="value">{{ $mother->marital_status ?? '-' }}</span></li>
                                                <li><span class="label">Monthly Income</span><span
                                                        class="value">{{ $mother && $mother->monthly_income !== null ? 'Rp ' . $mother->monthlyIncome() : '-' }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="parent-block" style="margin-bottom: 30px;">
                                        <div class="parent-block-title parent-block-guardian">
                                            <i class="fa fa-shield"></i> Guardian
                                        </div>
                                        <div class="info-card">
                                            <ul class="info-list info-list-stack">
                                                <li><span class="label">Full Name</span><span
                                                        class="value">{{ $guardian->fullname ?? '-' }}</span></li>
                                                <li><span class="label">Phone</span><span
                                                        class="value">{{ $guardian->phone ?? '-' }}</span></li>
                                                <li><span class="label">Home Phone</span><span
                                                        class="value">{{ $guardian->home_phone ?? '-' }}</span></li>
                                                <li><span class="label">Email</span><span
                                                        class="value">{{ $guardian->email ?? '-' }}</span></li>
                                                <li><span class="label">Birth Place</span><span
                                                        class="value">{{ $guardian->birth_place ?? '-' }}</span></li>
                                                <li><span class="label">Birth Date</span><span
                                                        class="value">{{ $guardian && $guardian->birth_date ? $guardian->dateBirth() : '-' }}</span>
                                                </li>
                                                <li><span class="label">Identity Number</span><span
                                                        class="value">{{ $guardian->identity_number ?? '-' }}</span></li>
                                                <li><span class="label">Religion</span><span
                                                        class="value">{{ $guardian->religion ?? '-' }}</span></li>
                                                <li><span class="label">Ethnicity</span><span
                                                        class="value">{{ $guardian->ethnicity ?? '-' }}</span></li>
                                                <li><span class="label">Address</span><span
                                                        class="value">{{ $guardian->address ?? '-' }}</span></li>
                                                <li><span class="label">Zipcode</span><span
                                                        class="value">{{ $guardian->zipcode ?? '-' }}</span></li>
                                                <li><span class="label">Education</span><span
                                                        class="value">{{ $guardian->education ?? '-' }}</span></li>
                                                <li><span class="label">Occupation</span><span
                                                        class="value">{{ $guardian->occupation ?? '-' }}</span></li>
                                                <li><span class="label">Company Name</span><span
                                                        class="value">{{ $guardian->company_name ?? '-' }}</span></li>
                                                <li><span class="label">Company Address</span><span
                                                        class="value">{{ $guardian->company_address ?? '-' }}</span></li>
                                                <li><span class="label">Marital Status</span><span
                                                        class="value">{{ $guardian->marital_status ?? '-' }}</span></li>
                                                <li><span class="label">Monthly Income</span><span
                                                        class="value">{{ $guardian && $guardian->monthly_income !== null ? 'Rp ' . $guardian->monthlyIncome() : '-' }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    @if (!$father && !$mother && !$guardian)
                                        <div class="empty-state">
                                            <i class="fa fa-users"></i>
                                            <p>No parent/guardian information available</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Step 3: Health -->
                            <div class="wizard-panel" data-panel="3">
                                <div class="content-header">
                                    <h3><i class="fa fa-heartbeat"></i> Health & Medical History</h3>
                                </div>
                                <div class="content-body content-body-stack">
                                    @php
                                        $immunization = $admission->applicant->immunization;
                                        $health = $admission->applicant->health;
                                        $pregnancyHistory = $admission->applicant->pregnancyHistory;
                                        $medicalHistory = $admission->applicant->medicalHistory;
                                    @endphp

                                    <!-- 1. BASIC IMMUNISATION -->
                                    <div class="parent-block" style="margin-bottom: 30px;">
                                        <div class="parent-block-title parent-block-father">
                                            <i class="fa fa-syringe"></i> Basic Immunisation
                                        </div>
                                        <div class="info-card">
                                            @if ($immunization)
                                                <div class="health-badges">
                                                    <span
                                                        class="health-badge {{ $immunization->bcg ? 'true' : 'false' }}"><i
                                                            class="fa {{ $immunization->bcg ? 'fa-check' : 'fa-times' }}"></i>
                                                        BCG</span>
                                                    <span
                                                        class="health-badge {{ $immunization->hepatitis ? 'true' : 'false' }}"><i
                                                            class="fa {{ $immunization->hepatitis ? 'fa-check' : 'fa-times' }}"></i>
                                                        Hepatitis</span>
                                                    <span
                                                        class="health-badge {{ $immunization->dtp ? 'true' : 'false' }}"><i
                                                            class="fa {{ $immunization->dtp ? 'fa-check' : 'fa-times' }}"></i>
                                                        DTP</span>
                                                    <span
                                                        class="health-badge {{ $immunization->polio ? 'true' : 'false' }}"><i
                                                            class="fa {{ $immunization->polio ? 'fa-check' : 'fa-times' }}"></i>
                                                        Polio</span>
                                                    <span
                                                        class="health-badge {{ $immunization->measles ? 'true' : 'false' }}"><i
                                                            class="fa {{ $immunization->measles ? 'fa-check' : 'fa-times' }}"></i>
                                                        Measles</span>
                                                    <span
                                                        class="health-badge {{ $immunization->hepatitis_a ? 'true' : 'false' }}"><i
                                                            class="fa {{ $immunization->hepatitis_a ? 'fa-check' : 'fa-times' }}"></i>
                                                        Hepatitis A</span>
                                                    <span
                                                        class="health-badge {{ $immunization->mmr ? 'true' : 'false' }}"><i
                                                            class="fa {{ $immunization->mmr ? 'fa-check' : 'fa-times' }}"></i>
                                                        MMR</span>
                                                    <span
                                                        class="health-badge {{ $immunization->influenza ? 'true' : 'false' }}"><i
                                                            class="fa {{ $immunization->influenza ? 'fa-check' : 'fa-times' }}"></i>
                                                        Influenza</span>
                                                </div>
                                            @else
                                                <div class="empty-state">
                                                    <i class="fa fa-inbox"></i>
                                                    <p>No immunization records available</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- 2. MEDICAL RECORDS -->
                                    <div class="parent-block" style="margin-bottom: 30px;">
                                        <div class="parent-block-title parent-block-mother">
                                            <i class="fa fa-heart"></i> Medical Records
                                        </div>
                                        <div class="info-card">
                                            @if ($health)
                                                <ul class="info-list info-list-stack">
                                                    <li><span class="label">Blood Type</span><span
                                                            class="value">{{ $health->blood_type ?? '-' }}</span></li>
                                                    <li><span class="label">Food Allergy</span><span class="value"><span
                                                                class="health-badge {{ $health->food_allergy ? 'true' : 'false' }}">{{ $health->food_allergy ? 'Yes' : 'No' }}</span></span>
                                                    </li>
                                                    <li><span class="label">Food Allergy Note</span><span
                                                            class="value">{{ $health->food_allergy_note ?? '-' }}</span>
                                                    </li>
                                                    <li><span class="label">Drug Allergy</span><span class="value"><span
                                                                class="health-badge {{ $health->drug_allergy ? 'true' : 'false' }}">{{ $health->drug_allergy ? 'Yes' : 'No' }}</span></span>
                                                    </li>
                                                    <li><span class="label">Drug Allergy Note</span><span
                                                            class="value">{{ $health->drug_allergy_note ?? '-' }}</span>
                                                    </li>
                                                    <li><span class="label">Uses Glasses</span><span class="value"><span
                                                                class="health-badge {{ $health->uses_glasses ? 'true' : 'false' }}">{{ $health->uses_glasses ? 'Yes' : 'No' }}</span></span>
                                                    </li>
                                                    <li><span class="label">Uses Hearing Aid</span><span
                                                            class="value"><span
                                                                class="health-badge {{ $health->uses_hearing_aid ? 'true' : 'false' }}">{{ $health->uses_hearing_aid ? 'Yes' : 'No' }}</span></span>
                                                    </li>
                                                    <li><span class="label">Routine Medication</span><span
                                                            class="value"><span
                                                                class="health-badge {{ $health->routine_medication ? 'true' : 'false' }}">{{ $health->routine_medication ? 'Yes' : 'No' }}</span></span>
                                                    </li>
                                                    <li><span class="label">Routine Medication Note</span><span
                                                            class="value">{{ $health->routine_medication_note ?? '-' }}</span>
                                                    </li>
                                                    <li><span class="label">Family Disease History</span><span
                                                            class="value">{{ $health->family_disease_history ?? '-' }}</span>
                                                    </li>
                                                    <li><span class="label">Referral Health Facility</span><span
                                                            class="value">{{ $health->referral_health_facility ?? '-' }}</span>
                                                    </li>
                                                    <li><span class="label">Emergency Contact</span><span
                                                            class="value">{{ $health->emergency_contact ?? '-' }}</span>
                                                    </li>
                                                </ul>
                                            @else
                                                <div class="empty-state">
                                                    <i class="fa fa-inbox"></i>
                                                    <p>No medical records available</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- 3. Pregnancy History -->
                                    <div class="parent-block" style="margin-bottom: 30px;">
                                        <div class="parent-block-title parent-block-guardian">
                                            <i class="fa fa-baby"></i> Pregnancy History
                                        </div>
                                        <div class="info-card">
                                            @if ($pregnancyHistory)
                                                <ul class="info-list info-list-stack">
                                                    <li><span class="label">Medication During Pregnancy</span><span
                                                            class="value"><span
                                                                class="health-badge {{ $pregnancyHistory->medication_during_pregnancy ? 'true' : 'false' }}">{{ $pregnancyHistory->medication_during_pregnancy ? 'Yes' : 'No' }}</span></span>
                                                    </li>
                                                    <li><span class="label">Medication Note</span><span
                                                            class="value">{{ $pregnancyHistory->medication_note ?? '-' }}</span>
                                                    </li>
                                                    <li><span class="label">Illness During Pregnancy</span><span
                                                            class="value"><span
                                                                class="health-badge {{ $pregnancyHistory->illness_during_pregnancy ? 'true' : 'false' }}">{{ $pregnancyHistory->illness_during_pregnancy ? 'Yes' : 'No' }}</span></span>
                                                    </li>
                                                    <li><span class="label">Illness Note</span><span
                                                            class="value">{{ $pregnancyHistory->illness_note ?? '-' }}</span>
                                                    </li>
                                                </ul>
                                            @else
                                                <div class="empty-state">
                                                    <i class="fa fa-inbox"></i>
                                                    <p>No pregnancy history available</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- 4. Condition at Birth -->
                                    <div class="parent-block" style="margin-bottom: 30px;">
                                        <div class="parent-block-title" style="border-bottom-color: #28a745;">
                                            <i class="fa fa-child"></i> Condition at Birth
                                        </div>
                                        <div class="info-card">
                                            @if ($pregnancyHistory)
                                                <ul class="info-list info-list-stack">
                                                    <li><span class="label">Gestational Age</span><span
                                                            class="value">{{ $pregnancyHistory->gestational_age ?? '-' }}</span>
                                                    </li>
                                                    <li><span class="label">Delivery Method</span><span
                                                            class="value">{{ ucfirst($pregnancyHistory->delivery_method ?? '-') }}</span>
                                                    </li>
                                                    <li><span class="label">Birth Weight (kg)</span><span
                                                            class="value">{{ $pregnancyHistory->birth_weight ? number_format($pregnancyHistory->birth_weight, 0) : '-' }}</span>
                                                    </li>
                                                    <li><span class="label">Birth Height (cm)</span><span
                                                            class="value">{{ $pregnancyHistory->birth_height ? number_format($pregnancyHistory->birth_height, 0) : '-' }}</span>
                                                    </li>
                                                    <li><span class="label">Walk Age (months)</span><span
                                                            class="value">{{ $pregnancyHistory->walk_age_month ?? '-' }}</span>
                                                    </li>
                                                    <li><span class="label">Talk Age (months)</span><span
                                                            class="value">{{ $pregnancyHistory->talk_age_month ?? '-' }}</span>
                                                    </li>
                                                </ul>
                                            @else
                                                <div class="empty-state">
                                                    <i class="fa fa-inbox"></i>
                                                    <p>No condition at birth information available</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- 5. Previous Medical History -->
                                    <div class="parent-block" style="margin-bottom: 30px;">
                                        <div class="parent-block-title" style="border-bottom-color: #6f42c1;">
                                            <i class="fa fa-file-medical"></i> Previous Medical History
                                        </div>
                                        <div class="info-card">
                                            @if ($medicalHistory)
                                                <ul class="info-list info-list-stack">
                                                    <li><span class="label">Surgery History</span><span
                                                            class="value"><span
                                                                class="health-badge {{ $medicalHistory->surgery_history ? 'true' : 'false' }}">{{ $medicalHistory->surgery_history ? 'Yes' : 'No' }}</span></span>
                                                    </li>
                                                    <li><span class="label">Surgery Note</span><span
                                                            class="value">{{ $medicalHistory->surgery_note ?? '-' }}</span>
                                                    </li>
                                                    <li><span class="label">Hospitalization History</span><span
                                                            class="value"><span
                                                                class="health-badge {{ $medicalHistory->hospitalization_history ? 'true' : 'false' }}">{{ $medicalHistory->hospitalization_history ? 'Yes' : 'No' }}</span></span>
                                                    </li>
                                                    <li><span class="label">Hospitalization Note</span><span
                                                            class="value">{{ $medicalHistory->hospitalization_note ?? '-' }}</span>
                                                    </li>
                                                    <li><span class="label">Seizure History</span><span
                                                            class="value"><span
                                                                class="health-badge {{ $medicalHistory->seizure_history ? 'true' : 'false' }}">{{ $medicalHistory->seizure_history ? 'Yes' : 'No' }}</span></span>
                                                    </li>
                                                    <li><span class="label">Seizure Note</span><span
                                                            class="value">{{ $medicalHistory->seizure_note ?? '-' }}</span>
                                                    </li>
                                                    <li><span class="label">Accident History</span><span
                                                            class="value"><span
                                                                class="health-badge {{ $medicalHistory->accident_history ? 'true' : 'false' }}">{{ $medicalHistory->accident_history ? 'Yes' : 'No' }}</span></span>
                                                    </li>
                                                    <li><span class="label">Accident Note</span><span
                                                            class="value">{{ $medicalHistory->accident_note ?? '-' }}</span>
                                                    </li>
                                                </ul>
                                            @else
                                                <div class="empty-state">
                                                    <i class="fa fa-inbox"></i>
                                                    <p>No previous medical history available</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 4: Download -->
                            <div class="wizard-panel" data-panel="4">
                                <div class="content-header">
                                    <h3><i class="fa fa-check"></i> Parent's Agreement</h3>
                                </div>
                                <div class="content-body">
                                    @php
                                        $declaration = $admission->applicant->declaration;
                                        $hasAgreed =
                                            $declaration &&
                                            ($declaration->agreed == 1 ||
                                                $declaration->agreed === true ||
                                                strtolower($declaration->agreed ?? '') === 'yes');
                                    @endphp
                                    @if ($hasAgreed)
                                        <div class="declaration-content">
                                            <div class="declaration-agreed-badge">
                                                <i class="fa fa-check-circle"></i> Parent has agreed to the declaration
                                            </div>
                                            <p class="declaration-text">
                                                I hereby declare my commitment to complete and fulfil all financial and
                                                administrative obligations at Mutiara Harapan Islamic School as required. I
                                                fully understand and agree to accept any consequences arising from the
                                                failure to meet these obligations, including but not limited to the
                                                suspension of my child's right to participate in learning activities at
                                                Mutiara Harapan Islamic School until such obligations have been fully
                                                settled.
                                            </p>
                                            <p class="declaration-text">
                                                The development fee, annual fee, school fee, and any other fees that I have
                                                paid are non-refundable and non-transferable under any circumstances, except
                                                in the event that my child is declared not accepted at Mutiara Harapan
                                                Islamic School.
                                            </p>
                                            <p class="declaration-text declaration-text-id">
                                                Development fee, annual fee, school fee, dan biaya lainnya yang telah Saya
                                                bayarkan tidak dapat ditarik kembali maupun dialihkan dengan alasan apa pun,
                                                kecuali apabila Putra/Putri Saya dinyatakan tidak diterima di Mutiara
                                                Harapan Islamic School.
                                            </p>
                                            @if ($declaration->agreed_at)
                                                <p class="declaration-agreed-at">
                                                    <strong>Agreed at:</strong>
                                                    {{ \Carbon\Carbon::parse($declaration->agreed_at)->format('d F Y H:i') }}
                                                </p>
                                            @endif
                                        </div>
                                    @else
                                        <div class="declaration-content declaration-not-agreed">
                                            <div class="empty-state">
                                                <i class="fa fa-clock-o"></i>
                                                <p>Parent has not agreed to the declaration yet</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Wizard Navigation -->
                        <div class="wizard-nav">
                            <button type="button" class="btn btn-outline-secondary wizard-prev" disabled>
                                <i class="fa fa-arrow-left"></i> Previous
                            </button>
                            <div class="wizard-step-indicator">Step <span class="wizard-current-step">1</span> of 4</div>
                            <button type="button" class="btn btn-primary wizard-next">
                                Next <i class="fa fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Documents Tab -->
                <div id="document-tab" class="tab-content">
                    <div class="content-card">
                        <div class="content-header">
                            <h3><i class="fa fa-file-pdf"></i> Admission Documents</h3>
                        </div>
                        <div class="content-body">
                            @if ($admission->documents && $admission->documents->count() > 0)
                                <div class="document-grid">
                                    @foreach ($admission->documents as $document)
                                        @php
                                            $ext = strtolower(
                                                pathinfo(
                                                    $document->file_path ?? $document->original_name,
                                                    PATHINFO_EXTENSION,
                                                ),
                                            );
                                            $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
                                            $isImage = in_array($ext, $imageExts);
                                            $fileUrl = str_starts_with($document->file_path ?? '', 'http')
                                                ? $document->file_path
                                                : asset('storage/' . $document->file_path);
                                        @endphp
                                        <div class="document-card">
                                            <div class="document-card-label">
                                                {{ str_replace('_', ' ', $document->type) }}
                                                @if ($document->verified_at)
                                                    <span class="badge text-bg-success document-verified-badge"><i
                                                            class="fa fa-check"></i> Verified</span>
                                                @else
                                                    <span class="badge text-bg-warning document-verified-badge"><i
                                                            class="fa fa-clock"></i> Pending</span>
                                                @endif
                                            </div>
                                            <div class="document-card-preview">
                                                @if ($isImage)
                                                    <img src="{{ $fileUrl }}"
                                                        alt="{{ $document->original_name }}"
                                                        class="document-preview-img">
                                                @else
                                                    <div class="document-preview-pdf">
                                                        <i class="fa fa-file-pdf-o"></i>
                                                        <span>PDF Document</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <a href="{{ $fileUrl }}" download
                                                class="btn btn-sm btn-primary document-download-btn">
                                                <i class="fa fa-download"></i> Download
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state">
                                    <i class="fa fa-file"></i>
                                    <p>No documents uploaded yet</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Statement Tab -->
                <div id="statement-tab" class="tab-content">
                    <div class="content-card">
                        <div class="content-header">
                            <h3><i class="fa fa-list-check"></i> Parent's Statement Form</h3>
                        </div>
                        <div class="content-body" style="font-size: 13px; line-height: 1.6;">
                            @if ($admission->statement)
                                @php
                                    $data = $admission;
                                    $parent = $admission->applicant->parents->first();
                                    $formatYesNo = fn($v) => $v ? 'Yes' : 'No';
                                @endphp

                                <!-- Statement Details Summary -->
                                <div style="margin-bottom: 30px; padding: 15px; background: #f8f9fa; border-radius: 6px;">
                                    <div style="margin-bottom: 12px;">
                                        <span style="font-weight: bold; color: #333;">Status:</span>
                                        <span style="margin-left: 10px;">
                                            @if ($admission->statement->is_completed)
                                                <span
                                                    style="display: inline-block; background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 4px;">Completed</span>
                                            @else
                                                <span
                                                    style="display: inline-block; background: #fff3cd; color: #856404; padding: 4px 8px; border-radius: 4px;">Incomplete</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div style="margin-bottom: 12px;">
                                        <span style="font-weight: bold; color: #333;">Complete Date:</span>
                                        <span
                                            style="margin-left: 10px;">{{ \Carbon\Carbon::parse($admission->statement->completed_at)->format('d M Y H:i') }}</span>
                                    </div>
                                </div>

                                <!-- Section 1: Authorized Signatory Confirmation -->
                                <div style="margin-bottom: 30px;">
                                    <div
                                        style="font-size: 15px; font-weight: bold; margin-bottom: 12px; padding: 8px 10px; background-color: #f5eaea; border-left: 4px solid #800000; color: #660000; text-transform: uppercase;">
                                        {{ config('student_approval.step1.title') }}
                                    </div>

                                    <div
                                        style="font-size: 13px; font-weight: bold; margin: 18px 0 10px 0; color: #9a3333; padding-bottom: 4px; border-bottom: 1px solid #e0c0c0;">
                                        Relationship to Student : <span
                                            style="color: #800000;">{{ \Illuminate\Support\Str::title($data->statement->actor) }}</span>
                                    </div>

                                    <div
                                        style="font-size: 13px; font-weight: bold; margin: 18px 0 10px 0; color: #9a3333; padding-bottom: 4px; border-bottom: 1px solid #e0c0c0;">
                                        {{ config('student_approval.step1.labels.text1.english') }} :
                                    </div>
                                    <table style="width: 100%; border-collapse: collapse; margin: 12px 0;">
                                        <tr>
                                            <td
                                                style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; width: 40%; font-weight: bold; color: #9a3333;">
                                                {{ config('student_approval.step1.labels.text2.english') }}:</td>
                                            <td
                                                style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; color: #800000;">
                                                {{ $parent->fullname }}</td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; width: 40%; font-weight: bold; color: #9a3333;">
                                                {{ config('student_approval.step1.labels.text3.english') }}:</td>
                                            <td
                                                style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; color: #800000;">
                                                {{ $parent->email }}</td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; width: 40%; font-weight: bold; color: #9a3333;">
                                                {{ config('student_approval.step1.labels.text4.english') }}:</td>
                                            <td
                                                style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; color: #800000;">
                                                {{ $parent->phone }}</td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; width: 40%; font-weight: bold; color: #9a3333;">
                                                D.O.B:</td>
                                            <td
                                                style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; color: #800000;">
                                                {{ $parent->birth_place }},
                                                {{ date('d F Y', strtotime($parent->birth_date)) }}</td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="padding: 7px 8px; vertical-align: top; border-bottom: none; width: 40%; font-weight: bold; color: #9a3333;">
                                                {{ config('student_approval.step1.labels.text7.english') }}:</td>
                                            <td
                                                style="padding: 7px 8px; vertical-align: top; border-bottom: none; color: #800000;">
                                                {{ $parent->identity_number }}</td>
                                        </tr>
                                    </table>

                                    <div
                                        style="border: 1px solid #d8b8b8; padding: 14px; margin: 7px 0; background-color: #fefafa; border-radius: 4px; border-left: 3px solid #800000;">
                                        <p style="font-weight: bold; margin: 0;">
                                            {{ config('student_approval.step1.labels.text8.english') }}</p>
                                        <p style="font-style: italic; margin-top: 8px; margin-bottom: 0;">
                                            {{ config('student_approval.step1.labels.text8.indonesian') }}</p>
                                    </div>

                                    <div
                                        style="font-size: 13px; font-weight: bold; margin: 18px 0 10px 0; color: #9a3333; padding-bottom: 4px; border-bottom: 1px solid #e0c0c0;">
                                        {{ config('student_approval.step1.labels.text13.english') }}
                                    </div>
                                    <table style="width: 100%; border-collapse: collapse; margin: 12px 0;">
                                        <tr>
                                            <td
                                                style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; width: 40%; font-weight: bold; color: #9a3333;">
                                                {{ config('student_approval.step1.labels.text2.english') }}:</td>
                                            <td
                                                style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; color: #800000;">
                                                {{ $data->applicant->fullname }}</td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; width: 40%; font-weight: bold; color: #9a3333;">
                                                {{ config('student_approval.step1.labels.text9.english') }}:</td>
                                            <td
                                                style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; color: #800000;">
                                                {{ $data->applicant->age }}</td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; width: 40%; font-weight: bold; color: #9a3333;">
                                                {{ config('student_approval.step1.labels.text10.english') }}:</td>
                                            <td
                                                style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; color: #800000;">
                                                {{ $data->level->name }} / {{ $data->grade->name }}</td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="padding: 7px 8px; vertical-align: top; border-bottom: none; width: 40%; font-weight: bold; color: #9a3333;">
                                                {{ config('student_approval.step1.labels.text12.english') }}:</td>
                                            <td
                                                style="padding: 7px 8px; vertical-align: top; border-bottom: none; color: #800000;">
                                                {{ $data->accademic_year }}</td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Section 2: School Fee Payment Consent Form -->
                                <div style="margin-bottom: 30px;">
                                    <div
                                        style="font-size: 15px; font-weight: bold; margin-bottom: 12px; padding: 8px 10px; background-color: #f5eaea; border-left: 4px solid #800000; color: #660000; text-transform: uppercase;">
                                        {{ config('student_approval.step2.title') }}
                                    </div>

                                    <div
                                        style="border: 1px solid #d8b8b8; padding: 14px; margin: 7px 0; background-color: #fefafa; border-radius: 4px; border-left: 3px solid #800000;">
                                        <p style="font-weight: bold; margin: 0;">
                                            {{ config('student_approval.step2.labels.text1.english') }}</p>
                                        <p style="font-style: italic; margin-top: 8px; margin-bottom: 0;">
                                            {{ config('student_approval.step2.labels.text1.indonesian') }}</p>
                                    </div>

                                    <div
                                        style="font-size: 13px; font-weight: bold; margin: 18px 0 10px 0; color: #9a3333; padding-bottom: 4px; border-bottom: 1px solid #e0c0c0;">
                                        Payment Details</div>
                                    <table
                                        style="width: 100%; border-collapse: collapse; margin: 18px 0; border: 1px solid #d8b8b8;">
                                        <thead>
                                            <tr style="background-color: #f5eaea;">
                                                <th
                                                    style="border: 1px solid #d8b8b8; padding: 10px; text-align: left; font-weight: bold; color: #660000;">
                                                    Fee Type</th>
                                                <th
                                                    style="border: 1px solid #d8b8b8; padding: 10px; text-align: left; font-weight: bold; color: #660000;">
                                                    Amount (IDR)</th>
                                                <th
                                                    style="border: 1px solid #d8b8b8; padding: 10px; text-align: left; font-weight: bold; color: #660000;">
                                                    In Words</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($data->statement->financial)
                                                <tr style="background-color: #fcf8f8;">
                                                    <td style="border: 1px solid #d8b8b8; padding: 10px;">
                                                        {{ config('student_approval.step2.labels.text2.english') }}</td>
                                                    <td style="border: 1px solid #d8b8b8; padding: 10px; color: #800000;">
                                                        Rp.
                                                        {{ number_format($data->statement->financial->development_fee, 0, '.', ',') }}
                                                    </td>
                                                    <td style="border: 1px solid #d8b8b8; padding: 10px; color: #800000;">
                                                        {{ $data->statement->financial->development_fee_terbilang }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="border: 1px solid #d8b8b8; padding: 10px;">
                                                        {{ config('student_approval.step2.labels.text3.english') }}</td>
                                                    <td style="border: 1px solid #d8b8b8; padding: 10px; color: #800000;">
                                                        Rp.
                                                        {{ number_format($data->statement->financial->annual_fee, 0, '.', ',') }}
                                                    </td>
                                                    <td style="border: 1px solid #d8b8b8; padding: 10px; color: #800000;">
                                                        {{ $data->statement->financial->annual_fee_terbilang }}</td>
                                                </tr>
                                                <tr style="background-color: #fcf8f8;">
                                                    <td style="border: 1px solid #d8b8b8; padding: 10px;">
                                                        {{ config('student_approval.step2.labels.text4.english') }}</td>
                                                    <td style="border: 1px solid #d8b8b8; padding: 10px; color: #800000;">
                                                        Rp.
                                                        {{ number_format($data->statement->financial->school_fee, 0, '.', ',') }}
                                                    </td>
                                                    <td style="border: 1px solid #d8b8b8; padding: 10px; color: #800000;">
                                                        {{ $data->statement->financial->school_fee_terbilang }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="border: 1px solid #d8b8b8; padding: 10px;">
                                                        {{ config('student_approval.step2.labels.text22.english') }}</td>
                                                    <td style="border: 1px solid #d8b8b8; padding: 10px; color: #800000;">
                                                        Rp.
                                                        {{ number_format($data->statement->financial->uniform_fee, 0, '.', ',') }}
                                                    </td>
                                                    <td style="border: 1px solid #d8b8b8; padding: 10px; color: #800000;">
                                                        {{ $data->statement->financial->uniform_fee_terbilang }}</td>
                                                </tr>
                                                <tr style="background-color: #fcf8f8;">
                                                    <td style="border: 1px solid #d8b8b8; padding: 10px;">
                                                        {{ config('student_approval.step2.labels.text23.english') }}</td>
                                                    <td style="border: 1px solid #d8b8b8; padding: 10px; color: #800000;">
                                                        Rp.
                                                        {{ number_format($data->statement->financial->ittihada_fee, 0, '.', ',') }}
                                                    </td>
                                                    <td style="border: 1px solid #d8b8b8; padding: 10px; color: #800000;">
                                                        {{ $data->statement->financial->ittihada_fee_terbilang }}</td>
                                                </tr>
                                                @if ($data->level->division->name == 'Secondary')
                                                    <tr>
                                                        <td style="border: 1px solid #d8b8b8; padding: 10px;">
                                                            {{ config('student_approval.step2.labels.text24.english') }}
                                                        </td>
                                                        <td
                                                            style="border: 1px solid #d8b8b8; padding: 10px; color: #800000;">
                                                            Rp.
                                                            {{ number_format($data->statement->financial->mhsu_fee, 0, '.', ',') }}
                                                        </td>
                                                        <td
                                                            style="border: 1px solid #d8b8b8; padding: 10px; color: #800000;">
                                                            {{ $data->statement->financial->mhsu_fee_terbilang }}</td>
                                                    </tr>
                                                @endif
                                            @endif
                                        </tbody>
                                    </table>

                                    <div
                                        style="font-size: 13px; font-weight: bold; margin: 18px 0 10px 0; color: #9a3333; padding-bottom: 4px; border-bottom: 1px solid #e0c0c0;">
                                        Payment Terms and Conditions</div>
                                    <p style="font-weight: bold; margin: 12px 0;">
                                        {{ config('student_approval.step2.labels.text5.english') }}:</p>

                                    <div
                                        style="border: 1px solid #d8b8b8; padding: 14px; margin: 7px 0; background-color: #fefafa; border-radius: 4px; border-left: 3px solid #800000;">
                                        <ol style="margin: 0; padding-left: 20px;">

                                            <li>
                                                <p><strong>{{ config('student_approval.step2.labels.text6.english') }}</strong><br>
                                                    <span
                                                        class="text-italic">{{ config('student_approval.step2.labels.text6.indonesian') }}</span>
                                                </p>
                                            </li>
                                            <li>
                                                <p><strong>{{ config('student_approval.step2.labels.text7.english') }}</strong><br>
                                                    <span
                                                        class="text-italic">{{ config('student_approval.step2.labels.text7.indonesian') }}</span>
                                                </p>
                                            </li>
                                            <li>
                                                <p><strong>{{ config('student_approval.step2.labels.text8.english') }}</strong>
                                                    <br><span
                                                        class="text-italic">{{ config('student_approval.step2.labels.text8.indonesian') }}</span>
                                                </p>
                                            </li>
                                            <li>
                                                <p><strong>{{ config('student_approval.step2.labels.text9.english') }}</strong>
                                                    <br>
                                                    <span
                                                        class="text-italic">{{ config('student_approval.step2.labels.text9.indonesian') }}</span>
                                                </p>

                                            </li>
                                            <li>
                                                <p><strong>{{ config('student_approval.step2.labels.text10.english') }}</strong>
                                                    <br>
                                                    <span
                                                        class="text-italic">{{ config('student_approval.step2.labels.text10.indonesian') }}</span>
                                                </p>

                                            </li>
                                            <li>
                                                <p><strong>{{ config('student_approval.step2.labels.text11.english') }}</strong>
                                                    <br>
                                                    <span
                                                        class="text-italic">{{ config('student_approval.step2.labels.text11.indonesian') }}</span>
                                                </p>

                                            </li>
                                            <li>
                                                <p><strong>{{ config('student_approval.step2.labels.text12.english') }}</strong>
                                                    <br>
                                                    <span
                                                        class="text-italic">{{ config('student_approval.step2.labels.text12.indonesian') }}</span>
                                                </p>

                                            </li>
                                            <li>
                                                <p><strong>{{ config('student_approval.step2.labels.text13.english') }}</strong>
                                                    <br>
                                                    <span
                                                        class="text-italic">{{ config('student_approval.step2.labels.text13.indonesian') }}</span>
                                                </p>

                                            </li>
                                            <li>
                                                <p><strong>{{ config('student_approval.step2.labels.text14.english') }}</strong><br>
                                                    <span
                                                        class="text-italic">{{ config('student_approval.step2.labels.text14.indonesian') }}</span>
                                                </p>

                                            </li>
                                            <li>
                                                <p><strong>{{ config('student_approval.step2.labels.text15.english') }}</strong><br>
                                                    <span
                                                        class="text-italic">{{ config('student_approval.step2.labels.text15.indonesian') }}</span>
                                                </p>

                                                <ol type="A" style="margin: 8px 0 0 0; padding-left: 20px;">
                                                    <li>
                                                        <p><strong>{{ config('student_approval.step2.labels.text16.english') }}</strong>
                                                            <br>
                                                            <span
                                                                class="text-italic">{{ config('student_approval.step2.labels.text16.indonesian') }}</span>
                                                        </p>

                                                    </li>
                                                    <li>
                                                        <p><strong>{{ config('student_approval.step2.labels.text17.english') }}</strong>
                                                            <br>
                                                            <span
                                                                class="text-italic">{{ config('student_approval.step2.labels.text17.indonesian') }}
                                                            </span>
                                                        </p>

                                                    </li>
                                                    <li>
                                                        <p><strong>{{ config('student_approval.step2.labels.text18.english') }}</strong><br>
                                                            <span
                                                                class="text-italic">{{ config('student_approval.step2.labels.text18.indonesian') }}
                                                            </span>
                                                        </p>

                                                    </li>
                                                    <li>
                                                        <p><strong>{{ config('student_approval.step2.labels.text19.english') }}</strong><br>
                                                            <span
                                                                class="text-italic">{{ config('student_approval.step2.labels.text19.indonesian') }}
                                                            </span>
                                                        </p>

                                                    </li>
                                                    <li>
                                                        <p><strong>{{ config('student_approval.step2.labels.text20.english') }}</strong><br>
                                                            <span
                                                                class="text-italic">{{ config('student_approval.step2.labels.text20.indonesian') }}
                                                            </span>
                                                        </p>

                                                    </li>
                                                </ol>
                                            </li>
                                            <li>
                                                <p><strong>{{ config('student_approval.step2.labels.text21.english') }}</strong><br>
                                                    <span
                                                        class="text-italic">{{ config('student_approval.step2.labels.text21.indonesian') }}</span>
                                                </p>

                                            </li>
                                        </ol>
                                    </div>
                                </div>

                                <!-- Section 3: FORM PERNYATAAN ORANG TUA / WALI MURID -->
                                <div style="margin-bottom: 30px;">
                                    <div
                                        style="font-size: 15px; font-weight: bold; margin-bottom: 12px; padding: 8px 10px; background-color: #f5eaea; border-left: 4px solid #800000; color: #660000; text-transform: uppercase;">
                                        {{ config('student_approval.step3.title') }}
                                    </div>
                                    <div
                                        style="border: 1px solid #d8b8b8; padding: 14px; margin: 7px 0; background-color: #fefafa; border-radius: 4px; border-left: 3px solid #800000;">
                                        <ol style="margin: 0; padding-left: 20px;">
                                            <li>
                                                <p><strong>{{ config('student_approval.step3.labels.text1.english') }}</strong><br>
                                                    <span
                                                        class="text-italic">{{ config('student_approval.step3.labels.text1.indonesian') }}</span>
                                                </p>
                                            </li>
                                            <li>
                                                <p><strong>{{ config('student_approval.step3.labels.text2.english') }}</strong><br>
                                                    <span
                                                        class="text-italic">{{ config('student_approval.step3.labels.text2.indonesian') }}</span>
                                                </p>
                                            </li>
                                            <li>
                                                <p><strong>{{ config('student_approval.step3.labels.text3.english') }}</strong><br>
                                                    <span
                                                        class="text-italic">{{ config('student_approval.step3.labels.text3.indonesian') }}</span>
                                                </p>
                                            </li>
                                            <li>
                                                <p><strong>{{ config('student_approval.step3.labels.text4.english') }}</strong><br>
                                                    <span
                                                        class="text-italic">{{ config('student_approval.step3.labels.text4.indonesian') }}</span>
                                                </p>
                                            </li>
                                            <li>
                                                <p><strong>{{ config('student_approval.step3.labels.text5.english') }}</strong><br>
                                                    <span
                                                        class="text-italic">{{ config('student_approval.step3.labels.text5.indonesian') }}</span>
                                                </p>
                                            </li>
                                            <li>
                                                <p><strong>{{ config('student_approval.step3.labels.text6.english') }}</strong><br>
                                                    <span
                                                        class="text-italic">{{ config('student_approval.step3.labels.text6.indonesian') }}</span>
                                                </p>
                                            </li>
                                            <li>
                                                <p><strong>{{ config('student_approval.step3.labels.text7.english') }}</strong><br>
                                                    <span
                                                        class="text-italic">{{ config('student_approval.step3.labels.text7.indonesian') }}</span>
                                                </p>
                                            </li>
                                            <li>
                                                <p><strong>{{ config('student_approval.step3.labels.text8.english') }}</strong><br>
                                                    <span
                                                        class="text-italic">{{ config('student_approval.step3.labels.text8.indonesian') }}</span>
                                                </p>
                                            </li>
                                            <li>
                                                <p><strong>{{ config('student_approval.step3.labels.text9.english') }}</strong><br>
                                                    <span
                                                        class="text-italic">{{ config('student_approval.step3.labels.text9.indonesian') }}</span>
                                                </p>
                                            </li>
                                            <li>
                                                <p><strong>{{ config('student_approval.step3.labels.text10.english') }}</strong><br>
                                                    <span
                                                        class="text-italic">{{ config('student_approval.step3.labels.text10.indonesian') }}</span>
                                                </p>
                                            </li>
                                            <li>
                                                <p><strong>{{ config('student_approval.step3.labels.text11.english') }}</strong><br>
                                                    <span
                                                        class="text-italic">{{ config('student_approval.step3.labels.text11.indonesian') }}</span>
                                                </p>
                                            </li>
                                            <li>
                                                <p><strong>{{ config('student_approval.step3.labels.text12.english') }}</strong><br>
                                                    <span
                                                        class="text-italic">{{ config('student_approval.step3.labels.text12.indonesian') }}</span>
                                                </p>
                                            </li>
                                            <li>
                                                <p><strong>{{ config('student_approval.step3.labels.text13.english') }}</strong><br>
                                                    <span
                                                        class="text-italic">{{ config('student_approval.step3.labels.text13.indonesian') }}</span>
                                                </p>
                                            </li>
                                            <li>
                                                <p><strong>{{ config('student_approval.step3.labels.text14.english') }}</strong><br>
                                                    <span
                                                        class="text-italic">{{ config('student_approval.step3.labels.text14.indonesian') }}</span>
                                                </p>
                                            </li>
                                            <li>
                                                <p><strong>{{ config('student_approval.step3.labels.text15.english') }}</strong><br>
                                                    <span
                                                        class="text-italic">{{ config('student_approval.step3.labels.text15.indonesian') }}</span>
                                                </p>
                                            </li>
                                            <li>
                                                <p><strong>{{ config('student_approval.step3.labels.text16.english') }}</strong><br>
                                                    <span
                                                        class="text-italic">{{ config('student_approval.step3.labels.text16.indonesian') }}</span>
                                                </p>
                                            </li>
                                            <li>
                                                <p><strong>{{ config('student_approval.step3.labels.text17.english') }}</strong><br>
                                                    <span
                                                        class="text-italic">{{ config('student_approval.step3.labels.text17.indonesian') }}</span>
                                                </p>
                                            </li>
                                        </ol>
                                    </div>
                                </div>

                                @if ($data->level->name == 'Upper Secondary')
                                    <div style="margin-bottom: 30px;">
                                        <div
                                            style="font-size: 15px; font-weight: bold; margin-bottom: 12px; padding: 8px 10px; background-color: #f5eaea; border-left: 4px solid #800000; color: #660000; text-transform: uppercase;">
                                            {{ config('student_approval.step5.title') }}
                                        </div>
                                        <p style="font-weight: bold; margin: 12px 0;">
                                            {{ config('student_approval.step5.labels.text0.english') }}:</p>
                                        <table style="width: 100%; border-collapse: collapse; margin: 12px 0;">
                                            <tr>
                                                <td
                                                    style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; width: 40%; font-weight: bold; color: #9a3333;">
                                                    {{ config('student_approval.step5.labels.text1.english') }}:</td>
                                                <td
                                                    style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; color: #800000;">
                                                    {{ $parent->fullname }}</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; width: 40%; font-weight: bold; color: #9a3333;">
                                                    {{ config('student_approval.step5.labels.text2.english') }}:</td>
                                                <td
                                                    style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; color: #800000;">
                                                    {{ $parent->email }}</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; width: 40%; font-weight: bold; color: #9a3333;">
                                                    {{ config('student_approval.step5.labels.text3.english') }}:</td>
                                                <td
                                                    style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; color: #800000;">
                                                    {{ $parent->phone }}</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; width: 40%; font-weight: bold; color: #9a3333;">
                                                    D.O.B:</td>
                                                <td
                                                    style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; color: #800000;">
                                                    {{ $parent->birth_place }},
                                                    {{ date('d F Y', strtotime($parent->birth_date)) }}</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="padding: 7px 8px; vertical-align: top; border-bottom: none; width: 40%; font-weight: bold; color: #9a3333;">
                                                    {{ config('student_approval.step5.labels.text6.english') }}:</td>
                                                <td
                                                    style="padding: 7px 8px; vertical-align: top; border-bottom: none; color: #800000;">
                                                    {{ $parent->identity_number }}</td>
                                            </tr>
                                        </table>
                                        <div
                                            style="border: 1px solid #d8b8b8; padding: 14px; margin: 7px 0; background-color: #fefafa; border-radius: 4px; border-left: 3px solid #800000;">
                                            <p style="font-weight: bold; margin: 0;">
                                                {{ config('student_approval.step5.labels.text7.english') }}</p>
                                            <p style="font-style: italic; margin-top: 8px; margin-bottom: 0;">
                                                {{ config('student_approval.step5.labels.text7.indonesian') }}</p>
                                        </div>
                                        <div
                                            style="font-size: 13px; font-weight: bold; margin: 18px 0 10px 0; color: #9a3333; padding-bottom: 4px; border-bottom: 1px solid #e0c0c0;">
                                            {{ config('student_approval.step5.labels.text8.english') }}</div>
                                        <table style="width: 100%; border-collapse: collapse; margin: 12px 0;">
                                            <tr>
                                                <td
                                                    style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; width: 40%; font-weight: bold; color: #9a3333;">
                                                    {{ config('student_approval.step5.labels.text1.english') }}:</td>
                                                <td
                                                    style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; color: #800000;">
                                                    {{ $data->applicant->fullname }}</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; width: 40%; font-weight: bold; color: #9a3333;">
                                                    {{ config('student_approval.step5.labels.text10.english') }}:</td>
                                                <td
                                                    style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; color: #800000;">
                                                    {{ $data->applicant->age }}</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; width: 40%; font-weight: bold; color: #9a3333;">
                                                    {{ config('student_approval.step5.labels.text11.english') }}:</td>
                                                <td
                                                    style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; color: #800000;">
                                                    {{ $data->level->name }}/{{ $data->grade->name }}</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="padding: 7px 8px; vertical-align: top; border-bottom: none; width: 40%; font-weight: bold; color: #9a3333;">
                                                    {{ config('student_approval.step5.labels.text13.english') }}:</td>
                                                <td
                                                    style="padding: 7px 8px; vertical-align: top; border-bottom: none; color: #800000;">
                                                    {{ $data->accademic_year }}</td>
                                            </tr>
                                        </table>
                                        <div
                                            style="border: 1px solid #d8b8b8; padding: 14px; margin: 7px 0; background-color: #fefafa; border-radius: 4px; border-left: 3px solid #800000;">
                                            <p style="font-weight: bold; margin: 0;">
                                                {{ config('student_approval.step5.labels.text14.english') }}
                                                <br><i><small>{{ config('student_approval.step5.labels.text14.indonesian') }}</small></i>
                                            </p>
                                            <p style="font-weight: bold; margin-top: 10px;">
                                                {{ config('student_approval.step5.labels.text15.english') }}
                                                <br><i><small>{{ config('student_approval.step5.labels.text15.indonesian') }}</small></i>
                                            </p>
                                        </div>
                                    </div>

                                    <div style="margin-bottom: 30px;">
                                        <div
                                            style="font-size: 15px; font-weight: bold; margin-bottom: 12px; padding: 8px 10px; background-color: #f5eaea; border-left: 4px solid #800000; color: #660000; text-transform: uppercase;">
                                            {{ config('student_approval.step6.title') }}
                                        </div>
                                        <div
                                            style="font-size: 13px; font-weight: bold; margin: 18px 0 10px 0; color: #9a3333; padding-bottom: 4px; border-bottom: 1px solid #e0c0c0;">
                                            {{ config('student_approval.step6.labels.text0.english') }}</div>
                                        <table style="width: 100%; border-collapse: collapse; margin: 12px 0;">
                                            <tr>
                                                <td
                                                    style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; width: 40%; font-weight: bold; color: #9a3333;">
                                                    {{ config('student_approval.step6.labels.text1.english') }}:</td>
                                                <td
                                                    style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; color: #800000;">
                                                    {{ $data->applicant->fullname }}</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; width: 40%; font-weight: bold; color: #9a3333;">
                                                    {{ config('student_approval.step6.labels.text2.english') }}:</td>
                                                <td
                                                    style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; color: #800000;">
                                                    {{ $data->applicant->age }}</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; width: 40%; font-weight: bold; color: #9a3333;">
                                                    {{ config('student_approval.step6.labels.text3.english') }}:</td>
                                                <td
                                                    style="padding: 7px 8px; vertical-align: top; border-bottom: 1px solid #eee; color: #800000;">
                                                    {{ $data->level->name }}/{{ $data->grade->name }}</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="padding: 7px 8px; vertical-align: top; border-bottom: none; width: 40%; font-weight: bold; color: #9a3333;">
                                                    {{ config('student_approval.step6.labels.text5.english') }}:</td>
                                                <td
                                                    style="padding: 7px 8px; vertical-align: top; border-bottom: none; color: #800000;">
                                                    {{ $data->accademic_year }}</td>
                                            </tr>
                                        </table>
                                        <div
                                            style="border: 1px solid #d8b8b8; padding: 14px; margin: 7px 0; background-color: #fefafa; border-radius: 4px; border-left: 3px solid #800000;">
                                            <p style="font-weight: bold; margin: 0;">
                                                {{ config('student_approval.step6.labels.text6.english') }}
                                                <br><i><small>{{ config('student_approval.step6.labels.text6.indonesian') }}</small></i>
                                            </p>
                                            <p style="font-weight: bold; margin-top: 10px;">
                                                {{ config('student_approval.step6.labels.text7.english') }}
                                                <br><i><small>{{ config('student_approval.step6.labels.text7.indonesian') }}</small></i>
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="empty-state">
                                    <i class="fa fa-inbox"></i>
                                    <p>No statement available</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content-script')
    <script>
        $(document).ready(function() {
            // Menu Link Click Handler
            $('.menu-link').on('click', function(e) {
                e.preventDefault();
                const tabId = $(this).data('tab');

                // Remove active class from all menu links and tabs
                $('.menu-link').removeClass('active');
                $('.tab-content').removeClass('active');

                // Add active class to clicked menu link and corresponding tab
                $(this).addClass('active');
                $('#' + tabId).addClass('active');

                // Reset wizard to step 1 when switching back to student tab
                if (tabId === 'student-tab') {
                    goToWizardStep(1);
                }
            });

            // Student Wizard
            const totalSteps = 4;

            function goToWizardStep(step) {
                const $steps = $('#student-tab .wizard-step');
                const $panels = $('#student-tab .wizard-panel');
                const $prevBtn = $('#student-tab .wizard-prev');
                const $nextBtn = $('#student-tab .wizard-next');

                $steps.removeClass('active completed');
                $panels.removeClass('active');

                $steps.each(function() {
                    const s = $(this).data('step');
                    if (s === step) $(this).addClass('active');
                    else if (s < step) $(this).addClass('completed');
                });

                $panels.filter('[data-panel="' + step + '"]').addClass('active');

                $prevBtn.prop('disabled', step <= 1);
                $nextBtn.html(step >= totalSteps ? 'Done <i class="fa fa-check"></i>' :
                    'Next <i class="fa fa-arrow-right"></i>');

                $('#student-tab .wizard-current-step').text(step);
            }

            function getCurrentWizardStep() {
                return parseInt($('#student-tab .wizard-panel.active').data('panel'), 10) || 1;
            }

            $('#student-tab .wizard-prev').on('click', function() {
                const current = getCurrentWizardStep();
                if (current > 1) goToWizardStep(current - 1);
            });

            $('#student-tab .wizard-next').on('click', function() {
                const current = getCurrentWizardStep();
                if (current < totalSteps) goToWizardStep(current + 1);
            });

            $('#student-tab .wizard-step').on('click', function() {
                const step = $(this).data('step');
                if (step) goToWizardStep(step);
            });

            $('#student-tab .wizard-print-summary').on('click', function() {
                window.print();
            });
        });
    </script>
@endsection

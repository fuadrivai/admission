@extends('main-layout.index')

@section('content-style')
    {{-- <link rel="stylesheet" href="/assets/static/css/enrolment.css?v=1.0.0"> --}}
    <style>
        .detail-container {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .detail-sidebar {
            width: 30%;
            position: sticky;
            top: 20px;
            height: fit-content;
        }

        .detail-content {
            width: 70%;
        }

        .sidebar-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
        }

        .sidebar-profile {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }

        .student-avatar-large {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: bold;
            margin: 0 auto 15px;
            border: 3px solid white;
        }

        .student-fullname {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .student-meta {
            font-size: 12px;
            opacity: 0.9;
            line-height: 1.6;
        }

        .sidebar-info {
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
        }

        .info-item {
            display: flex;
            margin-bottom: 12px;
            align-items: flex-start;
        }

        .info-label {
            font-weight: 600;
            color: #666;
            width: 90px;
            flex-shrink: 0;
        }

        .info-value {
            color: #333;
            flex: 1;
            word-break: break-word;
        }

        .menu-tabs {
            list-style: none;
            margin: 0;
            padding: 0;
            border-top: 1px solid #e9ecef;
        }

        .menu-tabs li {
            border-bottom: 1px solid #e9ecef;
        }

        .menu-tabs li:last-child {
            border-bottom: none;
        }

        .menu-tabs a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px 20px;
            color: #666;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            font-weight: 500;
        }

        .menu-tabs a:hover {
            background: #f8f9fa;
            color: #667eea;
        }

        .menu-tabs a.active {
            background: #f0f4ff;
            color: #667eea;
            border-left: 4px solid #667eea;
            padding-left: 16px;
        }

        .menu-tabs i {
            width: 20px;
            text-align: center;
            font-size: 16px;
        }

        /* Content Tabs */
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .content-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
            margin-bottom: 20px;
        }

        .content-header {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
        }

        .content-header h3 {
            margin: 0;
            font-size: 18px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .content-body {
            padding: 20px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .info-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
        }

        .info-section-title {
            font-weight: 600;
            color: #333;
            font-size: 14px;
            text-transform: uppercase;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .info-list li {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
            font-size: 13px;
        }

        .info-list li:last-child {
            border-bottom: none;
        }

        .info-list .label {
            font-weight: 500;
            color: #666;
        }

        .info-list .value {
            color: #333;
            text-align: right;
            font-weight: 500;
        }

        .health-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .health-badge {
            display: inline-block;
            padding: 6px 12px;
            background: #e7f3ff;
            color: #0066cc;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .health-badge.true {
            background: #d4edda;
            color: #155724;
        }

        .health-badge.false {
            background: #f8d7da;
            color: #721c24;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        @media (max-width: 1024px) {
            .detail-container {
                flex-direction: column;
            }

            .detail-sidebar {
                width: 100%;
                position: relative;
                top: 0;
            }

            .detail-content {
                width: 100%;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }
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
                                <i class="fa fa-list-check"></i>
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
                    <!-- Student Information -->
                    <div class="content-card">
                        <div class="content-header">
                            <h3><i class="fa fa-id-card"></i> Student Information</h3>
                        </div>
                        <div class="content-body">
                            <div class="info-grid">
                                <div class="info-section">
                                    <div class="info-section-title"><i class="fa fa-user"></i> Personal Info</div>
                                    <ul class="info-list">
                                        <li>
                                            <span class="label">Full Name</span>
                                            <span class="value">{{ $admission->applicant->fullname ?? '-' }}</span>
                                        </li>
                                        <li>
                                            <span class="label">Gender</span>
                                            <span class="value">{{ ucfirst($admission->applicant->gender ?? '-') }}</span>
                                        </li>
                                        <li>
                                            <span class="label">Place of Birth</span>
                                            <span
                                                class="value">{{ ucfirst($admission->applicant->place_of_birth ?? '-') }}</span>
                                        </li>
                                        <li>
                                            <span class="label">Date of Birth</span>
                                            <span class="value">{{ $admission->applicant->dateBirth() ?? '-' }}</span>
                                        </li>
                                        <li>
                                            <span class="label">Citizenship</span>
                                            <span class="value">{{ $admission->applicant->citizenship ?? '-' }}</span>
                                        </li>
                                        <li>
                                            <span class="label">Religion</span>
                                            <span
                                                class="value">{{ ucfirst($admission->applicant->religion ?? '-') }}</span>
                                        </li>
                                    </ul>
                                </div>

                                <div class="info-section">
                                    <div class="info-section-title"><i class="fa fa-home"></i> Address & Contact</div>
                                    <ul class="info-list">
                                        <li>
                                            <span class="label">Address</span>
                                            <span class="value">{{ $admission->applicant->address ?? '-' }}</span>
                                        </li>
                                        <li>
                                            <span class="label">Phone</span>
                                            <span class="value">{{ $admission->applicant->parent_phone ?? '-' }}</span>
                                        </li>
                                        <li>
                                            <span class="label">Last School</span>
                                            <span class="value">{{ $admission->applicant->last_school ?? '-' }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Parent Information -->
                    <div class="content-card">
                        <div class="content-header">
                            <h3><i class="fa fa-users"></i> Parent / Guardian Information</h3>
                        </div>
                        <div class="content-body">
                            @php
                                $parents = $admission->applicant->parents->keyBy('role');
                                $father = $parents->get('father');
                                $mother = $parents->get('mother');
                                $guardian = $parents->get('guardian');
                            @endphp

                            @if ($father)
                                <div style="margin-bottom: 30px;">
                                    <div
                                        style="font-weight: 600; color: #333; font-size: 14px; margin-bottom: 12px; text-transform: uppercase; border-bottom: 2px solid #667eea; padding-bottom: 10px;">
                                        <i class="fa fa-male"></i> Father
                                    </div>
                                    <div class="info-grid">
                                        <div class="info-section">
                                            <ul class="info-list">
                                                <li>
                                                    <span class="label">Full Name</span>
                                                    <span class="value">{{ $father->fullname ?? '-' }}</span>
                                                </li>
                                                <li>
                                                    <span class="label">Email</span>
                                                    <span class="value">{{ $father->email ?? '-' }}</span>
                                                </li>
                                                <li>
                                                    <span class="label">Phone</span>
                                                    <span class="value">{{ $father->phone ?? '-' }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="info-section">
                                            <ul class="info-list">
                                                <li>
                                                    <span class="label">Date of Birth</span>
                                                    <span class="value">{{ $father->dateBirth() ?? '-' }}</span>
                                                </li>
                                                <li>
                                                    <span class="label">Occupation</span>
                                                    <span class="value">{{ $father->occupation ?? '-' }}</span>
                                                </li>
                                                <li>
                                                    <span class="label">Monthly Income</span>
                                                    <span class="value">Rp {{ $father->monthlyIncome() ?? '-' }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($mother)
                                <div style="margin-bottom: 30px;">
                                    <div
                                        style="font-weight: 600; color: #333; font-size: 14px; margin-bottom: 12px; text-transform: uppercase; border-bottom: 2px solid #ffc107; padding-bottom: 10px;">
                                        <i class="fa fa-female"></i> Mother
                                    </div>
                                    <div class="info-grid">
                                        <div class="info-section">
                                            <ul class="info-list">
                                                <li>
                                                    <span class="label">Full Name</span>
                                                    <span class="value">{{ $mother->fullname ?? '-' }}</span>
                                                </li>
                                                <li>
                                                    <span class="label">Email</span>
                                                    <span class="value">{{ $mother->email ?? '-' }}</span>
                                                </li>
                                                <li>
                                                    <span class="label">Phone</span>
                                                    <span class="value">{{ $mother->phone ?? '-' }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="info-section">
                                            <ul class="info-list">
                                                <li>
                                                    <span class="label">Date of Birth</span>
                                                    <span class="value">{{ $mother->dateBirth() ?? '-' }}</span>
                                                </li>
                                                <li>
                                                    <span class="label">Occupation</span>
                                                    <span class="value">{{ $mother->occupation ?? '-' }}</span>
                                                </li>
                                                <li>
                                                    <span class="label">Monthly Income</span>
                                                    <span class="value">Rp {{ $mother->monthlyIncome() ?? '-' }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($guardian)
                                <div>
                                    <div
                                        style="font-weight: 600; color: #333; font-size: 14px; margin-bottom: 12px; text-transform: uppercase; border-bottom: 2px solid #17a2b8; padding-bottom: 10px;">
                                        <i class="fa fa-shield"></i> Guardian
                                    </div>
                                    <div class="info-grid">
                                        <div class="info-section">
                                            <ul class="info-list">
                                                <li>
                                                    <span class="label">Full Name</span>
                                                    <span class="value">{{ $guardian->fullname ?? '-' }}</span>
                                                </li>
                                                <li>
                                                    <span class="label">Email</span>
                                                    <span class="value">{{ $guardian->email ?? '-' }}</span>
                                                </li>
                                                <li>
                                                    <span class="label">Phone</span>
                                                    <span class="value">{{ $guardian->phone ?? '-' }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="info-section">
                                            <ul class="info-list">
                                                <li>
                                                    <span class="label">Date of Birth</span>
                                                    <span class="value">{{ $guardian->dateBirth() ?? '-' }}</span>
                                                </li>
                                                <li>
                                                    <span class="label">Occupation</span>
                                                    <span class="value">{{ $guardian->occupation ?? '-' }}</span>
                                                </li>
                                                <li>
                                                    <span class="label">Monthly Income</span>
                                                    <span class="value">Rp {{ $guardian->monthlyIncome() ?? '-' }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Health & Medical History -->
                    <div class="content-card">
                        <div class="content-header">
                            <h3><i class="fa fa-heartbeat"></i> Health & Medical History</h3>
                        </div>
                        <div class="content-body">
                            @php
                                $health = $admission->applicant->health;
                                $medical = $admission->applicant->medicalHistory;
                                $immunizations = $admission->applicant->immunization;
                            @endphp

                            <!-- Health Information -->
                            <div style="margin-bottom: 30px;">
                                <div class="info-section">
                                    <div class="info-section-title"><i class="fa fa-heart"></i> Health Status</div>
                                    @if ($health)
                                        <ul class="info-list">
                                            <li>
                                                <span class="label">Height (cm)</span>
                                                <span class="value">{{ $health->height ?? '-' }}</span>
                                            </li>
                                            <li>
                                                <span class="label">Weight (kg)</span>
                                                <span class="value">{{ $health->weight ?? '-' }}</span>
                                            </li>
                                            <li>
                                                <span class="label">Blood Type</span>
                                                <span class="value">{{ $health->blood_type ?? '-' }}</span>
                                            </li>
                                            <li>
                                                <span class="label">Uses Glasses</span>
                                                <span class="value">
                                                    <span
                                                        class="health-badge {{ $health->uses_glasses ? 'true' : 'false' }}">
                                                        {{ $health->uses_glasses ? 'Yes' : 'No' }}
                                                    </span>
                                                </span>
                                            </li>
                                            <li>
                                                <span class="label">Uses Hearing Aid</span>
                                                <span class="value">
                                                    <span
                                                        class="health-badge {{ $health->uses_hearing_aid ? 'true' : 'false' }}">
                                                        {{ $health->uses_hearing_aid ? 'Yes' : 'No' }}
                                                    </span>
                                                </span>
                                            </li>
                                        </ul>
                                        <div style="margin-top: 15px;">
                                            <span style="font-weight: 600; color: #333; font-size: 12px;">Allergies:</span>
                                            <div class="health-badges">
                                                <span class="health-badge {{ $health->food_allergy ? 'true' : 'false' }}">
                                                    <i
                                                        class="fa {{ $health->food_allergy ? 'fa-check' : 'fa-times' }}"></i>
                                                    Food Allergy
                                                </span>
                                                <span class="health-badge {{ $health->drug_allergy ? 'true' : 'false' }}">
                                                    <i
                                                        class="fa {{ $health->drug_allergy ? 'fa-check' : 'fa-times' }}"></i>
                                                    Drug Allergy
                                                </span>
                                                <span
                                                    class="health-badge {{ $health->routine_medication ? 'true' : 'false' }}">
                                                    <i
                                                        class="fa {{ $health->routine_medication ? 'fa-check' : 'fa-times' }}"></i>
                                                    Routine Medication
                                                </span>
                                            </div>
                                            @if ($health->medication_notes)
                                                <p style="margin-top: 10px; font-size: 12px; color: #666;">
                                                    <strong>Notes:</strong> {{ $health->medication_notes }}
                                                </p>
                                            @endif
                                        </div>
                                    @else
                                        <div class="empty-state">
                                            <i class="fa fa-inbox"></i>
                                            <p>No health information available</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Medical History -->
                            <div style="margin-bottom: 30px;">
                                <div class="info-section">
                                    <div class="info-section-title"><i class="fa fa-file-medical"></i> Medical History
                                    </div>
                                    @if ($medical)
                                        <div class="health-badges">
                                            <span class="health-badge {{ $medical->surgery_history ? 'true' : 'false' }}">
                                                <i
                                                    class="fa {{ $medical->surgery_history ? 'fa-check' : 'fa-times' }}"></i>
                                                Surgery History
                                            </span>
                                            <span
                                                class="health-badge {{ $medical->hospitalization_history ? 'true' : 'false' }}">
                                                <i
                                                    class="fa {{ $medical->hospitalization_history ? 'fa-check' : 'fa-times' }}"></i>
                                                Hospitalization
                                            </span>
                                            <span class="health-badge {{ $medical->seizure_history ? 'true' : 'false' }}">
                                                <i
                                                    class="fa {{ $medical->seizure_history ? 'fa-check' : 'fa-times' }}"></i>
                                                Seizure History
                                            </span>
                                            <span
                                                class="health-badge {{ $medical->accident_history ? 'true' : 'false' }}">
                                                <i
                                                    class="fa {{ $medical->accident_history ? 'fa-check' : 'fa-times' }}"></i>
                                                Accident History
                                            </span>
                                        </div>
                                        @if ($medical->notes)
                                            <p style="margin-top: 10px; font-size: 12px; color: #666;">
                                                <strong>Notes:</strong> {{ $medical->notes }}
                                            </p>
                                        @endif
                                    @else
                                        <div class="empty-state">
                                            <i class="fa fa-inbox"></i>
                                            <p>No medical history available</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Immunizations -->
                            <div>
                                <div class="info-section">
                                    <div class="info-section-title"><i class="fa fa-syringe"></i> Immunizations</div>
                                    @if ($immunizations)
                                        <div class="health-badges">
                                            <span class="health-badge {{ $immunizations->bcg ? 'true' : 'false' }}">
                                                <i class="fa {{ $immunizations->bcg ? 'fa-check' : 'fa-times' }}"></i> BCG
                                            </span>
                                            <span class="health-badge {{ $immunizations->hepatitis ? 'true' : 'false' }}">
                                                <i
                                                    class="fa {{ $immunizations->hepatitis ? 'fa-check' : 'fa-times' }}"></i>
                                                Hepatitis
                                            </span>
                                            <span class="health-badge {{ $immunizations->dtp ? 'true' : 'false' }}">
                                                <i class="fa {{ $immunizations->dtp ? 'fa-check' : 'fa-times' }}"></i> DTP
                                            </span>
                                            <span class="health-badge {{ $immunizations->polio ? 'true' : 'false' }}">
                                                <i class="fa {{ $immunizations->polio ? 'fa-check' : 'fa-times' }}"></i>
                                                Polio
                                            </span>
                                            <span class="health-badge {{ $immunizations->measles ? 'true' : 'false' }}">
                                                <i class="fa {{ $immunizations->measles ? 'fa-check' : 'fa-times' }}"></i>
                                                Measles
                                            </span>
                                            <span
                                                class="health-badge {{ $immunizations->hepatitis_a ? 'true' : 'false' }}">
                                                <i
                                                    class="fa {{ $immunizations->hepatitis_a ? 'fa-check' : 'fa-times' }}"></i>
                                                Hepatitis A
                                            </span>
                                            <span class="health-badge {{ $immunizations->mmr ? 'true' : 'false' }}">
                                                <i class="fa {{ $immunizations->mmr ? 'fa-check' : 'fa-times' }}"></i> MMR
                                            </span>
                                            <span class="health-badge {{ $immunizations->influenza ? 'true' : 'false' }}">
                                                <i
                                                    class="fa {{ $immunizations->influenza ? 'fa-check' : 'fa-times' }}"></i>
                                                Influenza
                                            </span>
                                        </div>
                                    @else
                                        <div class="empty-state">
                                            <i class="fa fa-inbox"></i>
                                            <p>No immunization records available</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
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
                                <div
                                    style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
                                    @foreach ($admission->documents as $document)
                                        <div
                                            style="border: 1px solid #e9ecef; border-radius: 6px; padding: 15px; text-align: center;">
                                            <div style="font-size: 32px; margin-bottom: 10px;">
                                                <i class="fa fa-file-pdf" style="color: #dc3545;"></i>
                                            </div>
                                            <div
                                                style="font-weight: 600; font-size: 12px; color: #333; margin-bottom: 5px;">
                                                {{ str_replace('_', ' ', $document->type) }}
                                            </div>
                                            <div style="font-size: 11px; color: #666; margin-bottom: 10px;">
                                                {{ $document->original_name }}
                                            </div>
                                            <div style="font-size: 11px; color: #999; margin-bottom: 10px;">
                                                Size: -
                                            </div>
                                            <div style="display: flex; gap: 8px;">
                                                <a href="{{ asset($document->file_path) }}" target="_blank"
                                                    class="btn btn-sm btn-primary" style="flex: 1;">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                                @if ($document->verified_at)
                                                    <span class="badge text-bg-success" style="align-self: center;">
                                                        <i class="fa fa-check"></i> Verified
                                                    </span>
                                                @else
                                                    <span class="badge text-bg-warning" style="align-self: center;">
                                                        <i class="fa fa-clock"></i> Pending
                                                    </span>
                                                @endif
                                            </div>
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
                            <h3><i class="fa fa-list-check"></i> Admission Statement</h3>
                        </div>
                        <div class="content-body">
                            @if ($admission->statement)
                                <div class="info-grid">
                                    <div class="info-section">
                                        <div class="info-section-title"><i class="fa fa-info-circle"></i> Statement
                                            Details</div>
                                        <ul class="info-list">
                                            <li>
                                                <span class="label">Status</span>
                                                <span class="value">
                                                    @if ($admission->statement->is_completed)
                                                        <span class="badge text-bg-success">Completed</span>
                                                    @else
                                                        <span class="badge text-bg-warning">Incomplete</span>
                                                    @endif
                                                </span>
                                            </li>
                                            <li>
                                                <span class="label">Created Date</span>
                                                <span
                                                    class="value">{{ \Carbon\Carbon::parse($admission->statement->created_at)->format('d M Y H:i') }}</span>
                                            </li>
                                            <li>
                                                <span class="label">Updated Date</span>
                                                <span
                                                    class="value">{{ \Carbon\Carbon::parse($admission->statement->updated_at)->format('d M Y H:i') }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                @if ($admission->statement->notes)
                                    <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 6px;">
                                        <strong style="color: #333;">Notes:</strong>
                                        <p style="margin-top: 10px; color: #666;">{{ $admission->statement->notes }}</p>
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
            });
        });
    </script>
@endsection

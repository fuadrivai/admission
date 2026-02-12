<style>
    .tbl {
        border-collapse: collapse;
        border: none;
    }

    .tbl tr td {
        border: none;
        padding: 2px;
    }

    /* Progress Tracker Styles */
    .completion-tracker {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 8px;
        position: relative;
    }

    .completion-tracker.all-completed {
        background: linear-gradient(135deg, #d4edda 0%, #e8f5e9 100%);
        border: 2px solid #28a745;
    }

    .overall-status {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: white;
        border-radius: 20px;
        font-weight: 600;
        font-size: 12px;
    }

    .overall-status.completed {
        color: #28a745;
        border: 1px solid #28a745;
    }

    .overall-status.pending {
        color: #ffc107;
        border: 1px solid #ffc107;
    }

    .tracker-item {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 500;
        position: relative;
        margin-right: 80px;
    }

    .tracker-item:not(:last-child)::after {
        content: '';
        position: absolute;
        right: -15px;
        top: 50%;
        transform: translateY(-50%);
        width: 20px;
        height: 2px;
        background: #e9ecef;
    }

    .tracker-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        font-size: 12px;
        font-weight: bold;
        color: white;
        flex-shrink: 0;
    }

    .tracker-icon.completed {
        background: #28a745;
    }

    .tracker-icon.in-progress {
        background: #ffc107;
        color: #333;
    }

    .tracker-icon.pending {
        background: #6c757d;
    }

    .tracker-label {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .tracker-title {
        font-size: 12px;
        font-weight: 600;
        color: #333;
    }

    .tracker-status {
        font-size: 11px;
        color: #666;
        text-transform: uppercase;
    }

    .tracker-status.completed {
        color: #28a745;
    }

    .tracker-status.in-progress {
        color: #ffc107;
    }

    .tracker-status.pending {
        color: #6c757d;
    }
</style>

@forelse ($admissions as $admission)
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-1">
                    <div class="student-avatar">{{ $admission->avatarName() }}</div>
                </div>
                <div class="col-md-5">
                    <label for="">Kode : {{ $admission->code ?? 'N/A' }} <span
                            class="badge text-bg-secondary">{{ ucfirst($admission->enrolment->source_data) }}</span>
                    </label></label><br>
                    <div class="student-name">{{ $admission->applicantName() ?? 'N/A' }}</div>
                </div>
                <div class="col-md-6 text-end" style="vertical-align: middle">
                    <a href="#" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-download"></i></a>
                    <a href="/applicant/{{ $admission->id }}/edit" class="btn btn-sm btn-info"><i
                            class="fa fa-eye text-white"></i></a>
                </div>
                <hr>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="academic-info">
                        <div class="info-group">
                            <div class="info-label"><i class="fa fa-user"></i> Student Information</div>
                            <div class="info-value">
                                <table class="tbl">
                                    <tr>
                                        <td>D.O.B</td>
                                        <td>: {{ $admission->applicant->dob() ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Age</td>
                                        <td>: {{ ucfirst($admission->applicant->age() ?? '-') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Gender</td>
                                        <td>: {{ ucfirst($admission->applicant->gender ?? '-') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Citizenship</td>
                                        <td>: {{ $admission->applicant->citizenship ?? '-' }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="academic-info">
                        <div class="info-group">
                            <div class="info-label"><i class="fa fa-building"></i> Academic Information
                            </div>
                            <div class="info-value">
                                <table class="tbl">
                                    <tr>
                                        <td>Branch</td>
                                        <td>: {{ $admission->branch->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Level</td>
                                        <td>:
                                            {{ $admission->level->name ?? '-' }}/{{ $admission->grade->name ?? '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>A.Y</td>
                                        <td>: {{ $admission->accademic_year }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="academic-info">
                        <div class="info-group">
                            <div class="info-label"><i class="fa fa-users"></i> Parent/Guardian</div>
                            <div class="info-value">
                                <table class="tbl">
                                    <tr>
                                        <td>Name</td>
                                        <td>: {{ $admission->parent->fullname ?? '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Email</td>
                                        <td>:
                                            {{ $admission->parent->email ?? '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Phone</td>
                                        <td>:
                                            {{ $admission->parent->phone ?? ($admission->applicant->parent_phone ?? '-') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Relation</td>
                                        <td>: {{ ucfirst($admission->parent->role ?? '-') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
            </div>

            <!-- Completion Tracker -->
            @php
                $statementStatus = 'pending';
                if (!$admission->statement) {
                    $statementStatus = 'pending';
                } elseif (!$admission->statement->is_completed) {
                    $statementStatus = 'in-progress';
                } else {
                    $statementStatus = 'completed';
                }
            @endphp

            <div class="completion-tracker {{ $admission->status == 1 ? 'all-completed' : '' }}">
                <!-- Enrolment Status -->
                <div class="tracker-item">
                    <div class="tracker-icon {{ $admission->is_complete == 1 ? 'completed' : 'in-progress' }}">
                        <i class="fa {{ $admission->is_complete != 1 ? 'fa-circle' : 'fa-check' }} fa-check"></i>
                    </div>
                    <div class="tracker-label">
                        <span class="tracker-title">Enrolment</span>
                        <span
                            class="tracker-status {{ $admission->is_complete == 1 ? 'completed' : 'in-progress' }}">{{ $admission->is_complete == 1 ? 'Completed' : 'In Progress' }}</span>
                    </div>
                </div>

                <!-- Documents Status -->
                <div class="tracker-item">
                    <div class="tracker-icon {{ $admission->documentStatus() == 1 ? 'completed' : 'pending' }}">
                        <i class="fa {{ $admission->documentStatus() == 1 ? 'fa-check' : 'fa-circle' }}"></i>
                    </div>
                    <div class="tracker-label">
                        <span class="tracker-title">Documents</span>
                        <span
                            class="tracker-status {{ $admission->documentStatus() == 1 ? 'completed' : 'pending' }}">{{ $admission->documentStatus() == 1 ? 'Completed' : 'Pending' }}</span>
                    </div>
                </div>

                <!-- Statement Status -->
                <div class="tracker-item">
                    <div class="tracker-icon {{ $statementStatus }}">
                        <i class="fa {{ $statementStatus != 'completed' ? 'fa-circle' : 'fa-check' }}"></i>
                    </div>
                    <div class="tracker-label">
                        <span class="tracker-title">Statement</span>
                        <span class="tracker-status {{ $statementStatus }}">{{ ucfirst($statementStatus) }}</span>
                    </div>
                </div>

                <!-- Overall Status Badge -->
                <div class="overall-status {{ $admission->status == 1 ? 'completed' : 'pending' }}">
                    @if ($admission->status == 1)
                        <i class="fa fa-check-circle"></i>
                        <span>All Complete</span>
                    @else
                        <i class="fa fa-hourglass-half"></i>
                        <span>In Progress</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="col-12 text-center text-muted">
        Data tidak ditemukan
    </div>
@endforelse

<div class="mt-3">
    {{ $admissions->links() }}
</div>

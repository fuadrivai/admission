@forelse ($visits as $visit)
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-1">
                    <div class="student-avatar">
                        {{ $visit->avatarName ? $visit->avatarName() : $visit->child_name[0] ?? '?' }}</div>
                </div>
                @php
                    $bg = 'bg-default';
                    switch ($visit->status) {
                        case 'registered':
                            $bg = 'bg-info';
                            break;
                        case 'present':
                            $bg = 'bg-success';
                            break;
                        case 'absent':
                            $bg = 'bg-danger';
                            break;
                        case 'cancelled':
                            $bg = 'bg-warning';
                            break;
                        default:
                            $bg = 'bg-default';
                            break;
                    }
                @endphp

                <div class="col-md-5">
                    <label>Kode : {{ $visit->code }} <span
                            class="badge {{ $bg }}">{{ ucwords($visit->status) }}</span>
                    </label><br>
                    <div class="student-name">{{ ucwords($visit->child_name) }}</div>
                </div>
                <div class="col-md-6 text-end" style="vertical-align: middle">
                    <a data-id="{{ $visit->id }}" class="btn btn-sm btn-primary view-detail"><i
                            class="fa fa-eye"></i></a>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-info dropdown-toggle" type="button" id="dropdownMenuButtonEmoji"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                class="fa fa-cog"></i>
                        </button>
                        <div class="dropdown-menu bg-danger ob" aria-labelledby="dropdownMenuButtonEmoji">
                            <a class="dropdown-item text-white btn-edit" data-id="{{ $visit->id }}"><i
                                    class="fa fa-pencil"></i>
                                Reschedule</a>
                            <a class="dropdown-item text-white btn-confirm" data-id="{{ $visit->id }}"><i
                                    class="bi bi-star"></i>
                                Confirm</a>
                            <a class="dropdown-item text-white btn-detail" data-id="{{ $visit->id }}"><i
                                    class="fa fa-trash"></i>
                                Cancel</a>
                        </div>
                    </div>
                </div>
                <hr>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="visit-info">
                        <div class="info-group">
                            <div class="info-label">Visit Info</div>
                            <div class="info-value">
                                Date: {{ $visit->dateTime() }}<br>
                                Number of Visitors: {{ $visit->number_visitor }}<br>
                                Already Enroled: <span
                                    class="badge text-bg-secondary">{{ ucfirst($visit->already_enrol) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="academic-info">
                        <div class="info-group">
                            <div class="info-label">Academic Info</div>
                            <div class="info-value">
                                Academic Year: {{ $visit->academic_year }}<br>
                                Branch: {{ $visit->branch_name ?? '-' }}<br>
                                Level: {{ $visit->level_name ?? '-' }} / {{ $visit->grade_name ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="parent-info">
                        <div class="info-group">
                            <div class="info-label">Parent</div>
                            <div class="info-value">
                                {{ $visit->parent_name }}
                            </div>
                            <div class="info-value">
                                {{ $visit->email }}<br>
                                {{ $visit->phone_number }}
                            </div>
                        </div>
                    </div>
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
    {{ $visits->links() }}
</div>

@extends('main-layout.index')

@section('content-child')
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Google Sheet Master</h5>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if ($errors->has('google_sheet'))
                    <div class="alert alert-danger">{{ $errors->first('google_sheet') }}</div>
                @endif
                @if ($errors->has('google_sheet_url'))
                    <div class="alert alert-danger">{{ $errors->first('google_sheet_url') }}</div>
                @endif

                <p class="text-muted">
                    Simpan URL spreadsheet untuk tiap integrasi. School Visit dapat disinkronkan sekarang;
                    integrasi lain akan tersedia berikutnya.
                </p>

                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Integration</th>
                                <th>Google Sheet URL</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($integrations as $integration)
                                <tr>
                                    <td>{{ $integration->name }}</td>
                                    <td>
                                        <form method="POST"
                                            action="{{ route('setting.google-sheet-master.update', $integration->integration_key) }}"
                                            class="d-flex gap-2">
                                            @csrf
                                            @method('PUT')
                                            <input type="url" name="google_sheet_url" class="form-control"
                                                value="{{ old('google_sheet_url', $integration->google_sheet_url) }}"
                                                placeholder="https://docs.google.com/spreadsheets/d/..."
                                                aria-label="{{ $integration->name }} Google Sheet URL">
                                            <button type="submit" class="btn btn-outline-primary">Save URL</button>
                                        </form>
                                    </td>
                                    <td>
                                        @if ($integration->google_sheet_url)
                                            <a href="{{ $integration->google_sheet_url }}" target="_blank"
                                                rel="noopener noreferrer">Open Sheet</a>
                                        @else
                                            <span class="text-muted">Not connected</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($integration->integration_key === 'school_visit')
                                            <form method="POST" action="{{ route('setting.google-sheet-master.school-visit.sync') }}">
                                                @csrf
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fa fa-refresh"></i> Sync now
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge bg-secondary">Coming soon</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <small class="text-muted">
                    Sheet School Visit dibagikan sebagai Viewer untuk siapa pun yang memiliki link.
                </small>
            </div>
        </div>
    </section>
@endsection

@extends('main-layout.index')

@section('content-style')
    <link rel="stylesheet" href="/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="/assets/compiled/css/table-datatable-jquery.css">
@endsection

@section('content-child')
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Parent Statement</h4>
                <a href="{{ route('setting.statement.create') }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus"></i> New Document
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Version</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Effective Date</th>
                                <th>Sections</th>
                                <th>Items</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($documents as $document)
                                @php
                                    $sectionCount = $document->sections->count();
                                    $itemCount = $document->sections->sum(fn($section) => $section->items->count());
                                @endphp
                                <tr>
                                    <td>{{ $document->version }}</td>
                                    <td>{{ $document->name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $document->status === 'PUBLISHED' ? 'success' : ($document->status === 'ARCHIVED' ? 'secondary' : 'warning') }}">
                                            {{ $document->status }}
                                        </span>
                                    </td>
                                    <td>{{ $document->effective_at ? $document->effective_at->format('d M Y') : '-' }}</td>
                                    <td>{{ $sectionCount }}</td>
                                    <td>{{ $itemCount }}</td>
                                    <td>{{ $document->created_at ? $document->created_at->format('d M Y') : '-' }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            @if ($document->status === 'DRAFT')
                                                <a href="{{ route('setting.statement.edit', $document->id) }}" class="btn btn-warning">Edit</a>
                                            @endif
                                            <a href="{{ route('setting.statement.preview', $document->id) }}" class="btn btn-info">Preview</a>
                                            @if ($document->status === 'DRAFT')
                                                <form action="{{ route('setting.statement.publish', $document->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success">Publish</button>
                                                </form>
                                                <form action="{{ route('setting.statement.destroy', $document->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this draft?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
                                            @elseif ($document->status !== 'DRAFT')
                                                <form action="{{ route('setting.statement.duplicate', $document->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary">Duplicate Version</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No parent statement documents found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

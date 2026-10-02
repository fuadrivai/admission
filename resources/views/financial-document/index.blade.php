@extends('main-layout.index')

@section('content-child')
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Financial Agreement Documents</h4>
                <a href="{{ route('setting.financial-document.create') }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus"></i> New Draft
                </a>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
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
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($documents as $document)
                                <tr>
                                    <td>{{ $document->version }}</td>
                                    <td>{{ $document->name }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $document->status === 'PUBLISHED' ? 'success' : ($document->status === 'ARCHIVED' ? 'secondary' : 'warning') }}">
                                            {{ $document->status }}
                                        </span>
                                    </td>
                                    <td>{{ $document->effective_at ? $document->effective_at->format('d M Y') : '-' }}</td>
                                    <td>{{ $document->sections->count() }}</td>
                                    <td>{{ $document->sections->sum(function ($section) {return $section->items->count();}) }}
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            @if ($document->status === 'DRAFT')
                                                <a href="{{ route('setting.financial-document.edit', $document->id) }}"
                                                    class="btn btn-warning">Edit</a>
                                            @endif
                                            <a href="{{ route('setting.financial-document.preview', $document->id) }}"
                                                class="btn btn-info">Preview</a>
                                            @if ($document->status === 'DRAFT')
                                                <form
                                                    action="{{ route('setting.financial-document.publish', $document->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success">Publish</button>
                                                </form>
                                                <form
                                                    action="{{ route('setting.financial-document.destroy', $document->id) }}"
                                                    method="POST" onsubmit="return confirm('Delete this unused draft?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
                                            @else
                                                <form
                                                    action="{{ route('setting.financial-document.duplicate', $document->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary">Duplicate
                                                        Version</button>
                                                </form>
                                                @if ($document->status === 'PUBLISHED')
                                                    <form
                                                        action="{{ route('setting.financial-document.archive', $document->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Archive the published financial agreement?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-secondary">Archive</button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No financial agreement documents
                                        found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

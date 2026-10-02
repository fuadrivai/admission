@extends('main-layout.index')

@section('content-child')
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">{{ $document->name }} - Version {{ $document->version }}</h4>
                <a href="{{ route('setting.statement.index') }}" class="btn btn-secondary btn-sm">Back</a>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Status:</strong> {{ $document->status }}
                    @if ($document->effective_at)
                        <span class="ms-2"><strong>Effective Date:</strong>
                            {{ $document->effective_at->format('d M Y') }}</span>
                    @endif
                </div>

                @if ($document->description)
                    <div class="alert alert-light border mb-3">{{ $document->description }}</div>
                @endif

                @foreach ($document->sections as $section)
                    <div class="mb-4 border rounded p-3">
                        <h5 class="fw-bold mb-3">{{ $section->title_en }}</h5>
                        <p class="text-muted mb-3">{{ $section->title_id }}</p>
                        <ol>
                            @foreach ($section->items as $item)
                                <li class="mb-3">
                                    <div class="fw-semibold">{{ $item->text_en }}</div>
                                    <div class="text-muted">{{ $item->text_id }}</div>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection

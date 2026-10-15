@extends('main-layout.index')

@section('content-child')
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">{{ $document->name }} - Version {{ $document->version }}</h4>
                <a href="{{ route('setting.financial-document.index') }}" class="btn btn-secondary btn-sm">Back</a>
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
                    <div class="alert alert-light border" style="white-space: pre-line;">{{ $document->description }}</div>
                @endif
                @foreach ($document->sections as $section)
                    <section class="mb-4 border rounded p-3">
                        <h5 class="fw-bold mb-3">{{ $section->title_en }}</h5>
                        <div class="text-muted mb-3"><i>{{ $section->title_id }}</i></div>
                        @foreach ($section->items->where('number', 0) as $preamble)
                            <div class="mb-3">
                                <div class="fw-semibold" style="text-align: justify; white-space: pre-line;">{{ $preamble->text_en }}</div>
                                <div class="text-muted" style="text-align: justify; white-space: pre-line;"><i>{{ $preamble->text_id }}</i></div>
                            </div>
                        @endforeach
                        @foreach ($section->items->where('number', '>', 0) as $item)
                            <div class="d-flex align-items-start mb-3">
                                <span class="flex-shrink-0 me-2" style="min-width: 1.5rem;">{{ $item->number }}.</span>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold" style="text-align: justify; white-space: pre-line;">{{ $item->text_en }}</div>
                                    <div class="text-muted" style="text-align: justify; white-space: pre-line;"><i>{{ $item->text_id }}</i></div>
                                </div>
                            </div>
                        @endforeach
                    </section>
                @endforeach
            </div>
        </div>
    </section>
@endsection

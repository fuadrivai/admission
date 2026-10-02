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
                    <div class="alert alert-light border">{{ $document->description }}</div>
                @endif
                @foreach ($document->sections as $section)
                    <section class="mb-4">
                        <h5 class="fw-bold">{{ $section->title_en }}</h5>
                        <div class="text-muted mb-3">{{ $section->title_id }}</div>
                        @php(
    $numberedItems = $section->items->filter(function ($item) {
        return $item->number > 0;
    })
)
                        @foreach ($section->items->where('number', 0) as $preamble)
                            <div class="mb-3">
                                <p class="mb-1" style="text-align: justify;">{{ $preamble->text_en }}</p>
                                <p class="text-muted mb-0" style="text-align: justify;">{{ $preamble->text_id }}</p>
                            </div>
                        @endforeach
                        @if ($numberedItems->isNotEmpty())
                            <ol class="ps-3">
                                @foreach ($numberedItems as $item)
                                    <li value="{{ $item->number }}" class="mb-3">
                                        <div style="text-align: justify;">{{ $item->text_en }}</div>
                                        <div class="text-muted" style="text-align: justify;">{{ $item->text_id }}</div>
                                    </li>
                                @endforeach
                            </ol>
                        @endif
                    </section>
                @endforeach
            </div>
        </div>
    </section>
@endsection

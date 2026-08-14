@extends('main-layout.index')

@section('content-child')
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Public Form Preview</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-light border">
                    <strong>Event:</strong> {{ $event->title }}
                </div>
                <form>
                    <div class="row g-3">
                        @foreach ($event->forms()->orderBy('order_index')->get() as $field)
                            <div class="col-md-12">
                                <label class="form-label">
                                    {{ $field->label }}
                                    @if ($field->is_required)
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                @if ($field->type === 'textarea')
                                    <textarea class="form-control" rows="4"></textarea>
                                @elseif ($field->type === 'select')
                                    <select class="form-select">
                                        @foreach ($field->options_json ?? [] as $option)
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endforeach
                                    </select>
                                @elseif ($field->type === 'checkbox')
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input">
                                        <label class="form-check-label">{{ $field->label }}</label>
                                    </div>
                                @elseif ($field->type === 'radio')
                                    @foreach ($field->options_json ?? [] as $option)
                                        <div class="form-check">
                                            <input type="radio" class="form-check-input" name="{{ $field->field_key }}">
                                            <label class="form-check-label">{{ $option }}</label>
                                        </div>
                                    @endforeach
                                @else
                                    <input
                                        type="{{ $field->type === 'email' ? 'email' : ($field->type === 'number' ? 'number' : ($field->type === 'date' ? 'date' : 'text')) }}"
                                        class="form-control">
                                @endif
                            </div>
                        @endforeach
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@extends('main-layout.index')

@section('content-style')
    <style>
        .event-form-list {
            border: 1px solid #dfe3e8;
            border-radius: 12px;
            overflow: hidden;
            background: #ffffff;
        }

        .event-form-list .table-header {
            display: grid;
            grid-template-columns: 40px 2.2fr 1fr 0.9fr 1.6fr 120px;
            align-items: center;
            padding: 0.8rem 1rem;
            background: #f4f5f7;
            border-bottom: 1px solid #dfe3e8;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #2f3742;
        }

        .event-form-list .table-body {
            background: #fff;
        }

        .event-form-list .table-row {
            display: grid;
            grid-template-columns: 40px 2.2fr 1fr 0.9fr 1.6fr 120px;
            align-items: center;
            min-height: 74px;
            padding: 0.9rem 1rem;
            border-bottom: 1px solid #e5e7eb;
            background: #fff;
            cursor: move;
        }

        .event-form-list .table-row:last-child {
            border-bottom: 0;
        }

        .event-form-list .drag-handle {
            color: #8b93a1;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: grab;
        }

        .event-form-list .drag-handle:active {
            cursor: grabbing;
        }

        .event-form-list .label-cell {
            font-size: 1.05rem;
            font-weight: 600;
            color: #1f2937;
        }

        .event-form-list .type-badge {
            display: inline-block;
            padding: 0.25rem 0.55rem;
            border-radius: 6px;
            border: 1px solid #dfe3e8;
            background: #f7f7f7;
            color: #4b5563;
            font-size: 0.8rem;
            text-transform: lowercase;
            min-width: 68px;
            text-align: center;
        }

        .event-form-list .required-mark {
            color: #1ea672;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .event-form-list .options-text {
            color: #4b5563;
            font-size: 0.92rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .event-form-list .actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
        }

        .event-form-list .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1px solid #7bb7ff;
            background: #eaf3ff;
            color: #1d4ed8;
            text-decoration: none;
        }

        .event-form-list .action-btn.delete {
            border-color: #f1b4b4;
            background: #fff1f1;
            color: #dc2626;
        }

        .event-form-list .empty-state {
            padding: 2rem;
            text-align: center;
            color: #6b7280;
        }

        .event-form-list .table-row.dragging {
            opacity: 0.5;
        }
    </style>
@endsection

@section('content-child')
    <section class="section">
        <div class="card" style="border: 0; box-shadow: none;">
            <div class="card-header d-flex justify-content-between align-items-center"
                style="background: #fff; border-bottom: 1px solid #e5e7eb; padding: 1rem 1.2rem;">
                <h5 class="mb-0" style="font-weight: 600;">Form Builder - {{ $event->title }}</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('event.edit', $event) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                    <a href="{{ route('event.preview', $event) }}" target="_blank"
                        class="btn btn-outline-primary btn-sm">Preview</a>
                    <a href="{{ route('event.forms.create', $event) }}" class="btn btn-success btn-sm">Add Field</a>
                </div>
            </div>

            <div class="card-body" style="padding: 0;">
                <div class="event-form-list">
                    <div class="table-header">
                        <div></div>
                        <div>Label</div>
                        <div>Type</div>
                        <div>Required</div>
                        <div>Options</div>
                        <div style="text-align:right;">Actions</div>
                    </div>

                    <div class="table-body" id="form-order-list">
                        @forelse ($event->forms()->orderBy('order_index')->get() as $field)
                            <div class="table-row" data-id="{{ $field->id }}" draggable="true">
                                <div class="drag-handle" title="Drag to reorder">
                                    <i class="fa fa-grip-lines"></i>
                                </div>
                                <div class="label-cell">{{ $field->label }}</div>
                                <div><span class="type-badge">{{ ucfirst($field->type) }}</span></div>
                                <div>
                                    @if ($field->is_required)
                                        <span class="required-mark">✓</span>
                                    @else
                                        <span style="color:#9ca3af;">-</span>
                                    @endif
                                </div>
                                <div class="options-text">
                                    @php
                                        $options = is_array($field->options_json) ? $field->options_json : [];
                                        $optionValue = function ($option) {
                                            if (is_array($option)) {
                                                return (string) ($option['label'] ?? ($option['value'] ?? ''));
                                            }

                                            return is_scalar($option) ? (string) $option : '';
                                        };

                                        if ($field->depends_on_field_id) {
                                            $optionsText = collect($options)
                                                ->map(function ($values, $parentValue) use ($optionValue) {
                                                    $values = is_array($values) ? array_map($optionValue, $values) : [];
                                                    $values = array_values(array_filter($values));

                                                    return $parentValue . ': ' . implode(', ', $values);
                                                })
                                                ->implode(' | ');
                                        } else {
                                            $optionsText = implode(
                                                ', ',
                                                array_values(array_filter(array_map($optionValue, $options))),
                                            );
                                        }

                                        $optionsText = $optionsText !== '' ? $optionsText : '-';
                                    @endphp
                                    {{ $optionsText }}
                                </div>
                                <div class="actions">
                                    <a href="{{ route('event.forms.edit', [$event, $field]) }}" class="action-btn"
                                        title="Edit"><i class="fa fa-pencil"></i></a>
                                    <form method="POST" action="{{ route('event.forms.destroy', [$event, $field]) }}"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete" title="Delete"
                                            onclick="return confirm('Delete this field?')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">No form fields yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content-script')
    <script>
        $(document).ready(function() {
            const list = $('#form-order-list');
            let draggedId = null;

            list.find('.table-row').on('dragstart', function(e) {
                draggedId = $(this).data('id');
                $(this).addClass('dragging');
                e.originalEvent.dataTransfer.effectAllowed = 'move';
            });

            list.find('.table-row').on('dragend', function() {
                $(this).removeClass('dragging');
            });

            list.find('.table-row').on('dragover', function(e) {
                e.preventDefault();
                e.originalEvent.dataTransfer.dropEffect = 'move';
            });

            list.find('.table-row').on('drop', function(e) {
                e.preventDefault();
                const targetId = $(this).data('id');
                if (!draggedId || draggedId === targetId) {
                    return;
                }

                const rows = list.find('.table-row');
                const draggedRow = rows.filter('[data-id="' + draggedId + '"]');
                const targetRow = $(this);

                if (draggedRow.index() < targetRow.index()) {
                    targetRow.after(draggedRow);
                } else {
                    targetRow.before(draggedRow);
                }

                const order = list.find('.table-row').map(function() {
                    return $(this).data('id');
                }).get();

                $.ajax({
                    url: '{{ route('event.forms.reorder', $event) }}',
                    type: 'POST',
                    data: {
                        order: order,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function() {
                        console.log('Order updated');
                    }
                });
            });
        });
    </script>
@endsection

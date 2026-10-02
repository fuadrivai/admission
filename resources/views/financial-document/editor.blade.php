@extends('main-layout.index')

@section('content-child')
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">{{ $editing ? 'Edit Financial Agreement Draft' : 'Create Financial Agreement Draft' }}
                </h4>
                <a href="{{ route('setting.financial-document.index') }}" class="btn btn-secondary btn-sm">Back</a>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form
                    action="{{ $editing ? route('setting.financial-document.update', $document->id) : route('setting.financial-document.store') }}"
                    method="POST">
                    @csrf
                    @if ($editing)
                        @method('PUT')
                    @endif
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Document Name</label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', $document->name) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Version</label>
                            <input type="text" name="version" class="form-control"
                                value="{{ old('version', $document->version) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Effective Date</label>
                            <input type="date" name="effective_at" class="form-control"
                                value="{{ old('effective_at', optional($document->effective_at)->format('Y-m-d')) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description', $document->description) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0">Sections and Statements</h5>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="add-financial-section">Add
                            Section</button>
                    </div>
                    <div id="financial-sections"></div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Save Draft</button>
                        <a href="{{ route('setting.financial-document.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('content-script')
    <script>
        $(function() {
            let sectionCounter = 0;
            const existingSections = @json(old('sections', $document->sections->load('items')->toArray()));

            function addSection(section) {
                const sectionIndex = sectionCounter++;
                const html = `
                    <div class="financial-section border rounded p-3 mb-4" data-section-index="${sectionIndex}" data-next-item-index="0">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">Section</h6>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-financial-section">Remove Section</button>
                        </div>
                        <input type="hidden" name="sections[${sectionIndex}][id]">
                        <div class="row g-3 mb-3">
                            <div class="col-md-5">
                                <label class="form-label">English Title</label>
                                <input type="text" class="form-control" name="sections[${sectionIndex}][title_en]" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Indonesian Title</label>
                                <input type="text" class="form-control" name="sections[${sectionIndex}][title_id]" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Sort Order</label>
                                <input type="number" class="form-control" name="sections[${sectionIndex}][sort_order]" value="${sectionIndex}" required>
                            </div>
                        </div>
                        <div class="financial-items"></div>
                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-sm btn-outline-primary add-financial-item">Add Statement</button>
                        </div>
                    </div>`;
                const $section = $(html);
                $('#financial-sections').append($section);

                if (section) {
                    $section.find(`[name="sections[${sectionIndex}][id]"]`).val(section.id);
                    $section.find(`[name="sections[${sectionIndex}][title_en]"]`).val(section.title_en);
                    $section.find(`[name="sections[${sectionIndex}][title_id]"]`).val(section.title_id);
                    $section.find(`[name="sections[${sectionIndex}][sort_order]"]`).val(section.sort_order);
                    (section.items || []).forEach(function(item, itemIndex) {
                        addItem($section, sectionIndex, item, itemIndex);
                    });
                } else {
                    addItem($section, sectionIndex, null, 0);
                }
            }

            function addItem($section, sectionIndex, item, itemIndex) {
                if (itemIndex === null) {
                    itemIndex = Number($section.attr('data-next-item-index')) || 0;
                }
                $section.attr('data-next-item-index', Math.max(
                    Number($section.attr('data-next-item-index')) || 0,
                    itemIndex + 1
                ));
                const required = !item || item.is_required ? 'checked' : '';
                const itemHtml = `
                    <div class="financial-item border rounded p-3 mb-3">
                        <input type="hidden" name="sections[${sectionIndex}][items][${itemIndex}][id]">
                        <div class="row g-2 mb-2">
                            <div class="col-md-3">
                                <label class="form-label">Number (0 = preamble)</label>
                                <input type="number" class="form-control" name="sections[${sectionIndex}][items][${itemIndex}][number]" value="${item ? item.number : itemIndex + 1}" min="0" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Sort Order</label>
                                <input type="number" class="form-control" name="sections[${sectionIndex}][items][${itemIndex}][sort_order]" value="${item ? item.sort_order : itemIndex}" required>
                            </div>
                            <div class="col-md-4 d-flex align-items-end pb-2">
                                <div class="form-check">
                                    <input type="hidden" name="sections[${sectionIndex}][items][${itemIndex}][is_required]" value="0">
                                    <input type="checkbox" class="form-check-input" name="sections[${sectionIndex}][items][${itemIndex}][is_required]" value="1" ${required}>
                                    <label class="form-check-label">Required statement</label>
                                </div>
                            </div>
                            <div class="col-md-3 text-end">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-financial-item">Remove Statement</button>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">English Text</label>
                                <textarea class="form-control" rows="5" name="sections[${sectionIndex}][items][${itemIndex}][text_en]" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Indonesian Text</label>
                                <textarea class="form-control" rows="5" name="sections[${sectionIndex}][items][${itemIndex}][text_id]" required></textarea>
                            </div>
                        </div>
                    </div>`;
                const $item = $(itemHtml);
                $section.find('.financial-items').append($item);
                if (item) {
                    $item.find(`[name="sections[${sectionIndex}][items][${itemIndex}][id]"]`).val(item.id);
                    $item.find(`[name="sections[${sectionIndex}][items][${itemIndex}][text_en]"]`).val(item
                        .text_en);
                    $item.find(`[name="sections[${sectionIndex}][items][${itemIndex}][text_id]"]`).val(item
                        .text_id);
                }
            }

            $('#add-financial-section').on('click', function() {
                addSection(null);
            });
            $(document).on('click', '.remove-financial-section', function() {
                $(this).closest('.financial-section').remove();
            });
            $(document).on('click', '.add-financial-item', function() {
                const $section = $(this).closest('.financial-section');
                const sectionIndex = $section.data('section-index');
                addItem($section, sectionIndex, null, null);
            });
            $(document).on('click', '.remove-financial-item', function() {
                $(this).closest('.financial-item').remove();
            });

            existingSections.forEach(function(section) {
                addSection(section);
            });
            if (!existingSections.length) {
                addSection(null);
            }
        });
    </script>
@endsection

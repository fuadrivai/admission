@extends('main-layout.index')

@section('content-child')
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Create Parent Statement</h4>
                <a href="{{ route('setting.statement.index') }}" class="btn btn-secondary btn-sm">Back</a>
            </div>
            <div class="card-body">
                <form id="statement-form" action="{{ route('setting.statement.store') }}" method="POST">
                    @csrf
                    @include('statement.edit-form', ['document' => $document])
                </form>
            </div>
        </div>
    </section>
@endsection

@section('content-script')
    <script>
        $(document).ready(function() {
            let sectionCounter = 0;
            const sectionTemplate = `@include('statement.editor-section')`;

            function addSection(section = null) {
                const sectionIndex = sectionCounter++;
                const markup = sectionTemplate.replace(/__SECTION_INDEX__/g, sectionIndex);
                const $section = $(markup);
                $('#statement-sections').append($section);

                if (section) {
                    $section.find('[name="sections[' + sectionIndex + '][id]"]').val(section.id ?? '');
                    $section.find('[name="sections[' + sectionIndex + '][title_en]"]').val(section.title_en ?? '');
                    $section.find('[name="sections[' + sectionIndex + '][title_id]"]').val(section.title_id ?? '');
                    $section.find('[name="sections[' + sectionIndex + '][sort_order]"]').val(section.sort_order ??
                        1);
                    $section.find('[name="sections[' + sectionIndex + '][is_required]"]').prop('checked', !!(section
                        .is_required ?? false));
                }

                if (section && section.items) {
                    const sectionEl = $('#statement-section-' + sectionIndex);
                    const items = section.items || [];
                    items.forEach(function(item, itemIndex) {
                        const itemHtml = createItemMarkup(sectionIndex, item, itemIndex);
                        sectionEl.find('.items-list').append(itemHtml);
                    });
                }
            }

            function createItemMarkup(sectionIndex, item = null, itemIndex = 0) {
                const itemId = item && item.id ? item.id : '';
                const number = item ? item.number : (itemIndex + 1);
                const textEn = item ? item.text_en : '';
                const textId = item ? item.text_id : '';
                const sortOrder = item ? item.sort_order : (itemIndex + 1);
                const isRequired = item && item.is_required ? 'checked' : '';

                return `
                    <div class="item-block border rounded p-3 mb-3">
                        <input type="hidden" name="sections[${sectionIndex}][items][${itemIndex}][id]" value="${itemId}">
                        <div class="row g-2">
                            <div class="col-md-2">
                                <label class="form-label">Number</label>
                                <input type="number" name="sections[${sectionIndex}][items][${itemIndex}][number]" class="form-control" value="${number}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Sort</label>
                                <input type="number" name="sections[${sectionIndex}][items][${itemIndex}][sort_order]" class="form-control" value="${sortOrder}" required>
                            </div>
                            <div class="col-md-3 d-flex align-items-center pt-4">
                                <div class="form-check">
                                    <input type="checkbox" name="sections[${sectionIndex}][items][${itemIndex}][is_required]" value="1" class="form-check-input" ${isRequired}>
                                    <label class="form-check-label">Required</label>
                                </div>
                            </div>
                            <div class="col-md-4 text-end pt-4">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-item">Remove item</button>
                            </div>
                        </div>
                        <div class="row g-2 mt-2">
                            <div class="col-md-6">
                                <label class="form-label">English text</label>
                                <textarea name="sections[${sectionIndex}][items][${itemIndex}][text_en]" class="form-control" rows="3" required>${textEn}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Indonesian text</label>
                                <textarea name="sections[${sectionIndex}][items][${itemIndex}][text_id]" class="form-control" rows="3" required>${textId}</textarea>
                            </div>
                        </div>
                    </div>
                `;
            }

            $('#add-section').on('click', function() {
                addSection();
            });

            $(document).on('click', '.remove-section', function() {
                $(this).closest('.statement-section').remove();
            });

            $(document).on('click', '.add-item', function() {
                const sectionEl = $(this).closest('.statement-section');
                const sectionIndex = sectionEl.data('section-index');
                const itemCount = sectionEl.find('.item-block').length;
                sectionEl.find('.items-list').append(createItemMarkup(sectionIndex, null, itemCount));
            });

            $(document).on('click', '.remove-item', function() {
                $(this).closest('.item-block').remove();
            });

            addSection();
        });
    </script>
@endsection

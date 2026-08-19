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
                                @includeWhen(View::exists('event.partials.preview-fields.' . $field->type),
                                    'event.partials.preview-fields.' . $field->type,
                                    ['field' => $field]
                                )
                            </div>
                        @endforeach
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('content-script')
    <script>
        $(function() {
            function syncPreviewOther(fieldKey) {
                const $toggle = $('[data-preview-other-toggle]').filter(function() {
                    return $(this).data('preview-other-toggle') === fieldKey && $(this).is(':checked');
                });
                const $select = $('[data-preview-other-select]').filter(function() {
                    return $(this).data('preview-other-select') === fieldKey;
                });
                const $input = $('[data-preview-other-input]').filter(function() {
                    return $(this).data('preview-other-input') === fieldKey;
                });
                const isOtherSelected = $toggle.length > 0 || $select.val() === '__OTHER__';

                $input.prop('disabled', !isOtherSelected);
                if (!isOtherSelected) {
                    $input.val('');
                }
            }

            $(document).on('change', '[data-preview-other-toggle], [data-preview-other-select]', function() {
                syncPreviewOther($(this).data('preview-other-toggle') || $(this).data(
                    'preview-other-select'));
            });

            $('[data-preview-other-toggle], [data-preview-other-select]').each(function() {
                syncPreviewOther($(this).data('preview-other-toggle') || $(this).data(
                    'preview-other-select'));
            });

            function previewFieldValue(fieldKey) {
                const $select = $('[data-preview-field-key="' + fieldKey + '"]').filter('select');
                if ($select.length) {
                    return $select.val() || '';
                }

                return $('input[type="radio"][name="preview_' + fieldKey + '"]:checked').val() || '';
            }

            function refreshPreviewDependents(parentKey) {
                $('[data-preview-dependent-on="' + parentKey + '"]').each(function() {
                    const $dependent = $(this);
                    const mapping = $dependent.data('preview-dependent-options') || {};
                    const parentValue = previewFieldValue(parentKey);
                    const options = parentValue && Array.isArray(mapping[parentValue]) ? mapping[
                        parentValue] : [];

                    $dependent.empty().append($('<option>', {
                        value: '',
                        text: parentValue ? '-- Select --' :
                            'Please select the parent field first'
                    }));
                    options.forEach(function(option) {
                        $dependent.append($('<option>', {
                            value: option,
                            text: option
                        }));
                    });
                    $dependent.prop('disabled', !parentValue || options.length === 0).val('');
                    refreshPreviewDependents($dependent.data('preview-field-key'));
                });
            }

            $(document).on('change', '[data-preview-field-key]', function() {
                refreshPreviewDependents($(this).data('preview-field-key'));
            });
            $('[data-preview-dependent-on]').each(function() {
                refreshPreviewDependents($(this).data('preview-dependent-on'));
            });
        });
    </script>
@endsection

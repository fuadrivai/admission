@extends('main-layout.index')

@section('content-child')
    @php
        $priceItems = old('items', isset($price) ? $price->items->toArray() : [[
            'type' => 'enrolment',
            'name' => 'Enrolment Fee',
            'amount' => '',
            'is_required' => true,
            'is_active' => true,
        ]]);
    @endphp
    <section class="section">
        <div class="card">
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
                <form autocomplete="off" action="{{ isset($price) ? '/price/' . $price->id : '/price' }}" method="POST">
                    @csrf
                    @if (isset($price))
                        @method('PUT')
                        <input type="hidden" name="id" value="{{ $price->id }}">
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label required-label">Price Configuration Name</label>
                            <input type="text" class="form-control" id="name" name="name" required
                                value="{{ old('name', $price->name ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="price-total" class="form-label">Total Required</label>
                            <input type="text" class="form-control" id="price-total" readonly value="0">
                            <small class="text-muted">Calculated from active required price items.</small>
                        </div>
                        <div class="col-md-3">
                            <label for="branch" class="form-label required-label">Branch</label>
                            <select name="branch" id="branch" class="form-select" required>
                                <option value="">Select branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ (string) old('branch', $price->branch_id ?? '') === (string) $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="level" class="form-label required-label">Level</label>
                            <select name="level" id="level" class="form-select" required>
                                <option value="">Select level</option>
                                @foreach ($levels ?? [] as $level)
                                    <option value="{{ $level->id }}"
                                        {{ (string) old('level', $price->level_id ?? '') === (string) $level->id ? 'selected' : '' }}>
                                        {{ $level->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="academic-year" class="form-label">Academic Year (optional)</label>
                            <select name="academic_year_id" id="academic-year" class="form-select">
                                <option value="">All academic years</option>
                                @foreach ($academicYears as $year)
                                    <option value="{{ $year->id }}"
                                        {{ (string) old('academic_year_id', $price->academic_year_id ?? '') === (string) $year->id ? 'selected' : '' }}>
                                        {{ $year->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="grade" class="form-label">Grade (optional)</label>
                            <select name="grade_id" id="grade" class="form-select">
                                <option value="">All grades</option>
                                @foreach ($grades ?? [] as $grade)
                                    <option value="{{ $grade->id }}"
                                        {{ (string) old('grade_id', $price->grade_id ?? '') === (string) $grade->id ? 'selected' : '' }}>
                                        {{ $grade->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="type" class="form-label required-label">Price Group</label>
                            <select name="type" id="type" class="form-select" required>
                                <option value="form" {{ old('type', $price->type ?? 'form') === 'form' ? 'selected' : '' }}>Form</option>
                                <option value="fee" {{ old('type', $price->type ?? '') === 'fee' ? 'selected' : '' }}>Enrolment Fee</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="is-active" class="form-label">Status</label>
                            <select name="is_active" id="is-active" class="form-select">
                                <option value="1" {{ (string) old('is_active', $price->is_active ?? 1) === '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ (string) old('is_active', $price->is_active ?? 1) === '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="mb-1">Required Price Items</h5>
                            <p class="text-muted mb-0">Mandatory active items are added automatically to the initial payment.</p>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="add-price-item">Add item</button>
                    </div>
                    <div id="price-items">
                        @foreach ($priceItems as $index => $item)
                            <div class="row g-2 align-items-end mb-3 price-item-row">
                                <div class="col-md-2">
                                    <label class="form-label">Type key</label>
                                    <input class="form-control item-type" name="items[{{ $index }}][type]"
                                        maxlength="64" pattern="[a-z][a-z0-9_-]*" required
                                        value="{{ $item['type'] ?? '' }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Name</label>
                                    <input class="form-control item-name" name="items[{{ $index }}][name]" required
                                        value="{{ $item['name'] ?? '' }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Amount</label>
                                    <input class="form-control item-amount" name="items[{{ $index }}][amount]" type="number"
                                        min="0.01" step="0.01" required value="{{ $item['amount'] ?? '' }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Required</label>
                                    <select class="form-select item-required" name="items[{{ $index }}][is_required]">
                                        <option value="1" {{ !empty($item['is_required']) ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ empty($item['is_required']) ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Active</label>
                                    <select class="form-select item-active" name="items[{{ $index }}][is_active]">
                                        <option value="1" {{ !array_key_exists('is_active', $item) || !empty($item['is_active']) ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ array_key_exists('is_active', $item) && empty($item['is_active']) ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-outline-danger remove-price-item" aria-label="Remove item">&times;</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 text-center">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Price</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('content-script')
    <script>
        $(function() {
            let itemIndex = {{ count($priceItems) }};

            function updateTotal() {
                const total = $('.price-item-row').toArray().reduce(function(sum, row) {
                    const $row = $(row);
                    if ($row.find('.item-active').val() !== '1' || $row.find('.item-required').val() !== '1') {
                        return sum;
                    }
                    return sum + (Number($row.find('.item-amount').val()) || 0);
                }, 0);
                $('#price-total').val('Rp ' + Math.round(total).toLocaleString('id-ID'));
            }

            function appendItem() {
                const index = itemIndex++;
                $('#price-items').append(
                    '<div class="row g-2 align-items-end mb-3 price-item-row">' +
                    '<div class="col-md-2"><label class="form-label">Type key</label><input class="form-control item-type" name="items[' + index + '][type]" maxlength="64" pattern="[a-z][a-z0-9_-]*" required></div>' +
                    '<div class="col-md-3"><label class="form-label">Name</label><input class="form-control item-name" name="items[' + index + '][name]" required></div>' +
                    '<div class="col-md-2"><label class="form-label">Amount</label><input class="form-control item-amount" name="items[' + index + '][amount]" type="number" min="0.01" step="0.01" required></div>' +
                    '<div class="col-md-2"><label class="form-label">Required</label><select class="form-select item-required" name="items[' + index + '][is_required]"><option value="1">Yes</option><option value="0">No</option></select></div>' +
                    '<div class="col-md-2"><label class="form-label">Active</label><select class="form-select item-active" name="items[' + index + '][is_active]"><option value="1">Yes</option><option value="0">No</option></select></div>' +
                    '<div class="col-md-1"><button type="button" class="btn btn-outline-danger remove-price-item" aria-label="Remove item">&times;</button></div></div>'
                );
            }

            $('#add-price-item').on('click', appendItem);
            $('#price-items').on('click', '.remove-price-item', function() {
                if ($('.price-item-row').length > 1) {
                    $(this).closest('.price-item-row').remove();
                    updateTotal();
                }
            });
            $('#price-items').on('input change', '.item-amount, .item-required, .item-active', updateTotal);
            updateTotal();

            let restoreLevel = $('#level').val();
            if ($('#branch').val() && !restoreLevel) {
                loadLevels($('#branch').val());
            }
            $('#branch').on('change', function() {
                restoreLevel = '';
                loadLevels(this.value);
            });

            async function loadLevels(branchId) {
                $('#level').html('<option value="">Select level</option>');
                $('#grade').html('<option value="">All grades</option>');
                if (!branchId) return;
                const levels = await $.getJSON('/level/branch/' + encodeURIComponent(branchId));
                levels.forEach(function(level) {
                    $('#level').append($('<option>').val(level.id).text(level.name));
                });
                if (restoreLevel) $('#level').val(restoreLevel).trigger('change');
                restoreLevel = '';
            }

            $('#level').on('change', async function() {
                const levelId = this.value;
                $('#grade').html('<option value="">All grades</option>');
                if (!levelId) return;
                const grades = await $.getJSON('/uniform/get-grades/' + encodeURIComponent(levelId));
                grades.forEach(function(grade) {
                    $('#grade').append($('<option>').val(grade.id).text(grade.name));
                });
                $('#grade').val('{{ old('grade_id', $price->grade_id ?? '') }}');
            });
        });
    </script>
@endsection

    <script>
        $(function() {
            const gradesUrl = @json($gradesUrl);
            const $form = $('#dp-form');
            const $existing = $('#existing-flow');
            const $new = $('#new-flow');
            const $payment = $('#payment-flow');
            const $summary = $('#enrolment-summary');
            const $alert = $('#lookup-alert');
            let verifiedEnrolment = null;
            let registrationFee = null;
            let requiredPriceItems = [];
            let autoDiscount = 0;
            let lastDiscountKey = '';

            function rupiah(value) {
                const amount = Number(value) || 0;
                return 'Rp ' + Math.round(amount).toLocaleString('id-ID');
            }

            function formatMoneyInput(value) {
                const sanitized = String(value).replace(/,/g, '').replace(/[^\d.]/g, '');
                const decimalIndex = sanitized.indexOf('.');
                const integerPart = decimalIndex === -1 ? sanitized : sanitized.slice(0, decimalIndex);
                const decimalPart = decimalIndex === -1 ? '' : '.' + sanitized.slice(decimalIndex + 1).replace(/\./g, '');
                const groupedInteger = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                return groupedInteger + decimalPart;
            }

            function moneyValue(selector) {
                return parseFloat(String($(selector).val() || '').replace(/,/g, '')) || 0;
            }

            function selectedMode() {
                return $('input[name="already_enrolment"]:checked').val();
            }

            function setAlert(type, message) {
                $alert.removeClass('d-none alert-success alert-warning alert-danger')
                    .addClass('alert-' + type).text(message);
            }

            function refreshSections() {
                const mode = selectedMode();
                const existingMode = mode === 'yes';
                const newMode = mode === 'no';
                const canShowPayment = existingMode ? Boolean(verifiedEnrolment) : (newMode && newDetailsComplete());

                $existing.toggleClass('d-none', !existingMode);
                $new.toggleClass('d-none', !newMode).attr('aria-hidden', !newMode);
                $payment.toggleClass('d-none', !canShowPayment);
                $existing.find(':input').prop('disabled', !existingMode);
                $new.find(':input').prop('disabled', !newMode);
                $('#level').prop('disabled', !newMode || !$('#branch').val());
                $('#grade').prop('disabled', !newMode || !$('#level').val());
                $payment.find(':input').prop('disabled', !canShowPayment);
                $('#registration-fee-option').toggleClass('d-none', existingMode);
                $('#registration-fee-option input').prop('disabled', existingMode).prop('checked', existingMode ? false : $('#registration-fee-option input').prop('checked'));
                if (newMode && !$('#registration-fee-option input').prop('checked')) {
                    $('#registration-fee-option input').prop('checked', true);
                }
                $('#summary-student').text(existingMode
                    ? (verifiedEnrolment ? verifiedEnrolment.student_name : 'Find a paid enrolment to continue')
                    : (newMode ? ($('#student-name').val() || 'Enter student details') : 'Select a parent status'));
                updateSummary();
                updateSubmitState();
            }

            function isOtherPlace() {
                return $('#registration-place option:selected').data('other') == 1;
            }

            function newDetailsComplete() {
                return ['#parent-name', '#parent-email', '#parent-phone', '#student-name', '#academic-year',
                    '#branch', '#level', '#grade'
                ].every(function(selector) {
                    return Boolean($(selector).val());
                });
            }

            function updateSubmitState() {
                const mode = selectedMode();
                const types = selectedPaymentTypes();
                const place = $('#registration-place').val();
                const validPerson = mode === 'yes' ? Boolean(verifiedEnrolment) : (mode === 'no' && newDetailsComplete());
                const validAmount = types.length > 0 && types.every(function(type) {
                    if (type === 'enrolment') return Number(registrationFee) > 0;
                    return moneyValue('#' + (type === 'dp' ? 'dp-amount' : 'other-amount')) > 0;
                });
                const outstandingRequired = getOutstandingRequiredItems();
                const hasValidAmounts = validAmount || outstandingRequired.length > 0;
                const validPlace = Boolean(place) && (!isOtherPlace() || Boolean($('#registration-place-other').val().trim()));
                const subtotal = selectedSubtotal();
                const validDiscount = autoDiscount <= subtotal;
                const hasRequiredPrice = mode !== 'no' || requiredPriceItems.some(function(item) {
                    return item.type === 'enrolment';
                });
                $('#submit-payment').prop('disabled', !(validPerson && hasValidAmounts && hasRequiredPrice && validPlace && validDiscount));
            }

            function selectedPaymentTypes() {
                return $('input[name="payment_types[]"]:checked').map(function() {
                    return this.value;
                }).get();
            }

            function selectedSubtotal() {
                const mandatoryTotal = getOutstandingRequiredItems().reduce(function(total, item) {
                    return total + (Number(item.amount) || 0);
                }, 0);
                return selectedPaymentTypes().reduce(function(total, type) {
                    if (type === 'enrolment') return total;
                    return total + moneyValue('#' + (type === 'dp' ? 'dp-amount' : 'other-amount'));
                }, mandatoryTotal);
            }

            function getOutstandingRequiredItems() {
                const chargeNew = selectedPaymentTypes().includes('enrolment');
                return requiredPriceItems.filter(function(item) {
                    return selectedMode() === 'yes' ? !item.is_paid : chargeNew;
                });
            }

            function renderRequiredPriceItems(items) {
                requiredPriceItems = Array.isArray(items) ? items : [];
                const rows = requiredPriceItems.map(function(item) {
                    const isPaid = selectedMode() === 'yes' && item.is_paid;
                    return '<div class="d-flex justify-content-between"><span>' +
                        $('<div>').text(item.name).html() +
                        (isPaid ? ' <span class="badge bg-success">PAID</span>' : ' <span class="small text-danger">Required</span>') +
                        '</span><span>' + rupiah(item.amount) + '</span></div>';
                });
                const $requiredItems = $('#required-price-items');
                $requiredItems.toggleClass('d-none', rows.length === 0)
                    .html(rows.length ? '<strong class="d-block mb-2">Required enrolment charges</strong>' + rows.join('') : '');
            }

            function updateSummary() {
                const types = selectedPaymentTypes();
                const amount = selectedSubtotal();
                refreshDiscount(types);
                const discount = autoDiscount;
                const bank = Number($('#summary-bank').data('bank-charge')) || 0;
                const total = Math.max(0, amount - discount + bank);
                const labels = {
                    enrolment: 'Registration Fee',
                    dp: 'Development Fee DP',
                    other: $('#other-description').val() || 'Other payment'
                };
                const items = getOutstandingRequiredItems().map(function(item) {
                    return '<div class="d-flex justify-content-between mb-1"><span>' +
                        $('<div>').text(item.name).html() + '</span><span>' + rupiah(item.amount) + '</span></div>';
                });
                const selectedItems = types.filter(function(type) {
                    return type !== 'enrolment';
                }).map(function(type) {
                    const itemAmount = moneyValue('#' + (type === 'dp' ? 'dp-amount' : 'other-amount'));
                    return '<div class="d-flex justify-content-between mb-1"><span>' +
                        $('<div>').text(labels[type]).html() + '</span><span>' + rupiah(itemAmount) + '</span></div>';
                });
                const summaryItems = items.concat(selectedItems);
                $('#summary-item').html(summaryItems.length ? summaryItems.join('') : 'No payment selected');
                $('#summary-subtotal').text(rupiah(amount));
                $('#summary-discount').text(rupiah(discount));
                $('#summary-bank').text(rupiah(bank));
                $('#summary-total').text(rupiah(total));
                updateSubmitState();
            }

            function refreshDiscount(types) {
                const place = $('#registration-place').val() || '';
                const enrolmentItem = getOutstandingRequiredItems().find(function(item) {
                    return item.type === 'enrolment';
                });
                const enrolmentAmount = enrolmentItem ? Number(enrolmentItem.amount) || 0 : 0;
                const hasDp = types.includes('dp') && moneyValue('#dp-amount') > 0;
                const branchId = $('#branch').val() || '';
                const key = [place, branchId, enrolmentAmount, hasDp ? 1 : 0].join('|');
                if (key === lastDiscountKey) return;
                lastDiscountKey = key;
                autoDiscount = 0;
                $('#summary-discount-note').text('');
                if (!place || enrolmentAmount <= 0 || !hasDp) return;
                $.get('{{ $discountUrl }}', {
                    registration_place: place,
                    branch_id: branchId,
                    enrolment_amount: enrolmentAmount,
                    has_dp: 1
                }).done(function(res) {
                    if (key !== lastDiscountKey) return;
                    autoDiscount = Number(res.discount) || 0;
                    $('#summary-discount-note').text(res.rule ? '(' + res.rule + ')' : '');
                    updateSummary();
                });
            }

            function updatePaymentFields() {
                const types = selectedPaymentTypes();
                $('#dp-amount-wrap').toggleClass('d-none', !types.includes('dp'));
                $('#other-payment-wrap').toggleClass('d-none', !types.includes('other'));
                $('#dp-amount').prop('required', types.includes('dp'));
                $('#other-amount').prop('required', types.includes('other'));
                $('#registration-fee-option input').prop('disabled', selectedMode() === 'yes');
                updateSummary();
            }

            function loadRegistrationFee(branchId, levelId, cachedFee, cachedItems) {
                registrationFee = cachedFee === undefined ? null : Number(cachedFee);
                if (cachedItems) renderRequiredPriceItems(cachedItems);
                if (registrationFee !== null && registrationFee > 0) {
                    $('#registration-fee-label').text(rupiah(registrationFee));
                    updatePaymentFields();
                    return;
                }

                $('#registration-fee-label').text('Loading configured price...');
                if (!branchId || !levelId) {
                    $('#registration-fee-label').text('Select branch and level to load current price.');
                    return;
                }

                $.getJSON('/price/branch/level/' + encodeURIComponent(branchId) + '/' + encodeURIComponent(levelId), {
                    academic_year_id: $('#academic-year').val(),
                    grade_id: $('#grade').val()
                })
                    .done(function(price) {
                        registrationFee = price && Number(price.price) > 0 ? Number(price.price) : null;
                        renderRequiredPriceItems(price && price.items ? price.items : []);
                        $('#registration-fee-label').text(registrationFee === null
                            ? 'No active registration fee configured'
                            : rupiah(registrationFee));
                        if (registrationFee === null && selectedPaymentTypes().includes('enrolment')) {
                            setAlert('warning', 'No active registration fee is configured for this branch and level.');
                        } else {
                            $alert.addClass('d-none');
                        }
                        updatePaymentFields();
                    })
                    .fail(function() {
                        registrationFee = null;
                        $('#registration-fee-label').text('Unable to load registration fee.');
                        setAlert('danger', 'Could not load the configured registration fee. Please try again.');
                        updateSubmitState();
                    });
            }

            function showEnrolment(enrolment) {
                verifiedEnrolment = enrolment;
                $summary.removeClass('d-none').html(
                    '<div class="d-flex justify-content-between align-items-center mb-2"><strong><i class="bi bi-check-circle-fill text-success me-1"></i>Enrolment Found</strong><span class="badge bg-success">' +
                    $('<div>').text(enrolment.payment_status).html() +
                    '</span></div><div class="fw-semibold">' + $('<div>').text(enrolment.student_name || '-').html() +
                    '</div><div class="small text-muted">Parent: ' + $('<div>').text(enrolment.parent_name || '-').html() +
                    '</div><div class="small text-muted">' + [enrolment.academic_year, enrolment.branch, enrolment.level,
                        enrolment.grade
                    ].filter(Boolean).map(function(value) {
                        return $('<div>').text(value).html();
                    }).join(' · ') + '</div><div class="small mt-2">Email: ' +
                    $('<div>').text(enrolment.email || '-').html() + ' &nbsp; Phone: ' +
                    $('<div>').text(enrolment.phone || '-').html() + '</div>'
                );
                setAlert('success', 'Enrolment verified. You can continue creating the payment.');
                $('#summary-student').text(enrolment.student_name || '-');
                loadRegistrationFee(
                    enrolment.branch_id,
                    enrolment.level_id,
                    enrolment.registration_fee,
                    enrolment.required_price_items
                );
                refreshSections();
            }

            async function prepareNewEnrolmentFromExpired(enrolment) {
                $('input[name="already_enrolment"][value="no"]').prop('checked', true).trigger('change');
                $('#expired-enrolment-notice').text(
                    'Enrolment ' + (enrolment.code || '') +
                    ' is EXPIRED. A new enrolment will be created with these copied details. Review them before continuing.'
                ).removeClass('d-none');

                $('#parent-name').val(enrolment.parent_name || '');
                $('#parent-email').val(enrolment.email || '');
                $('#parent-phone').val(enrolment.phone || '');
                $('#student-name').val(enrolment.student_name || '');
                $('#academic-year').val(enrolment.academic_year_id || '');
                $('#branch').val(enrolment.branch_id || '');

                try {
                    await loadSelect(
                        $('#level'),
                        enrolment.branch_id ? '/level/branch/' + encodeURIComponent(enrolment.branch_id) : null,
                        'Select level',
                        enrolment.level_id
                    );
                    await loadSelect(
                        $('#grade'),
                        enrolment.level_id ? gradesUrl + encodeURIComponent(enrolment.level_id) : null,
                        'Select grade',
                        enrolment.grade_id
                    );
                } catch (error) {
                    setAlert('danger', 'Could not load the expired enrolment details. Please complete the new enrolment fields manually.');
                }

                if (enrolment.branch_id && enrolment.level_id) {
                    loadRegistrationFee(enrolment.branch_id, enrolment.level_id);
                    $('#registration-fee-option input').prop('checked', true);
                }

                refreshSections();
            }

            function searchEnrolment() {
                const code = $('#enrolment-code').val().trim();
                verifiedEnrolment = null;
                $summary.addClass('d-none').empty();
                if (!code) {
                    setAlert('warning', 'Enter an enrolment code to search.');
                    refreshSections();
                    return;
                }

                $('#search-enrolment').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Searching...');
                $.getJSON('{{ $searchUrl }}', { code: code })
                    .done(function(response) {
                        if (response.success) {
                            showEnrolment(response.data);
                        } else {
                            setAlert('warning', response.message || 'Enrolment code not found. Please check the code and try again.');
                            refreshSections();
                        }
                    })
                    .fail(function(xhr) {
                        const response = xhr.responseJSON || {};
                        if (response.status === 'EXPIRED' && response.data) {
                            prepareNewEnrolmentFromExpired(response.data);
                            return;
                        }

                        setAlert(xhr.status === 422 ? 'warning' : 'danger',
                            response.message || 'Unable to search enrolment. Please try again.');
                        refreshSections();
                    })
                    .always(function() {
                        $('#search-enrolment').prop('disabled', false).html('<i class="bi bi-search me-1"></i>Search');
                    });
            }

            async function loadSelect($select, url, placeholder, selectedValue) {
                $select.html($('<option>').val('').text(placeholder)).prop('disabled', true);
                if (!url) return;
                const response = await $.getJSON(url);
                response.forEach(function(item) {
                    const option = $('<option>').val(item.id).text(item.name);
                    if (String(item.id) === String(selectedValue || '')) option.prop('selected', true);
                    $select.append(option);
                });
                $select.prop('disabled', false);
            }

            $('input[name="already_enrolment"]').on('change', function() {
                verifiedEnrolment = null;
                registrationFee = null;
                requiredPriceItems = [];
                renderRequiredPriceItems([]);
                $summary.addClass('d-none').empty();
                $alert.addClass('d-none');
                $('#enrolment-code').val('');
                $('#new-flow').find('input').val('');
                $('#academic-year, #branch, #level, #grade').val('');
                $('#level, #grade').prop('disabled', true)
                    .each(function() {
                        $(this).html($('<option>').val('').text(this.id === 'level' ? 'Select level' : 'Select grade'));
                    });
                $('#payment-flow').find('input[type="checkbox"], input[type="radio"]').prop('checked', false);
                $('#expired-enrolment-notice').addClass('d-none').empty();
                $('#payment-flow').find('input[type="number"], input[type="text"]').val('');
                autoDiscount = 0;
                lastDiscountKey = '';
                $('#summary-discount-note').text('');
                $('#registration-place').val('');
                $('#registration-place-other-wrap').addClass('d-none');
                $('#registration-place-other').prop('required', false);
                updatePaymentFields();
                refreshSections();
            });
            $('#search-enrolment').on('click', searchEnrolment);
            $('#enrolment-code').on('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    searchEnrolment();
                }
            });
            $('#branch').on('change', async function() {
                registrationFee = null;
                $('#registration-fee-label').text('Select branch and level to load current price.');
                try {
                    await loadSelect($('#level'), this.value ? '/level/branch/' + this.value : null, 'Select level');
                    await loadSelect($('#grade'), null, 'Select grade');
                } catch (error) {
                    setAlert('danger', 'Unable to load levels. Please try again.');
                }
                refreshSections();
            });
            $('#level').on('change', async function() {
                registrationFee = null;
                try {
                    await loadSelect($('#grade'), this.value ? gradesUrl + this.value : null, 'Select grade');
                    if ($('#branch').val()) loadRegistrationFee($('#branch').val(), this.value);
                } catch (error) {
                    setAlert('danger', 'Unable to load grades. Please try again.');
                }
                refreshSections();
            });
            $('#grade, #academic-year, #parent-name, #parent-email, #parent-phone, #student-name')
                .on('change input', function() {
                    refreshSections();
                    if (selectedMode() === 'no' && $('#branch').val() && $('#level').val()
                        && $('#grade').val() && $('#academic-year').val()) {
                        loadRegistrationFee($('#branch').val(), $('#level').val());
                    }
                });
            $('#registration-place').on('change', function() {
                const isOther = isOtherPlace();
                $('#registration-place-other-wrap').toggleClass('d-none', !isOther);
                $('#registration-place-other').prop('required', isOther);
                updateSummary();
            });
            $('#registration-place-other, .payment-amount, #other-description')
                .on('input change', updateSummary);
            $('.money-input').on('input', function() {
                const formatted = formatMoneyInput(this.value);
                if (this.value !== formatted) {
                    this.value = formatted;
                }
            });
            $('input[name="payment_types[]"]').on('change', function() {
                if (this.checked && this.value === 'dp' && !$('#dp-amount').val()) {
                    $('#dp-amount').val('5000000');
                }
                updatePaymentFields();
            });
            $form.on('submit', function() {
                $('.money-input').each(function() {
                    this.value = this.value.replace(/,/g, '');
                });
                $('#submit-payment').prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm me-2"></span>Creating payment...');
            });

            $('.money-input').each(function() {
                this.value = formatMoneyInput(this.value);
            });

            $('#registration-place-other-wrap').toggleClass('d-none', !isOtherPlace());
            $('#registration-place-other').prop('required', isOtherPlace());
            if (!selectedMode()) {
                $existing.addClass('d-none').find(':input').prop('disabled', true);
                $new.addClass('d-none').find(':input').prop('disabled', true);
                $payment.addClass('d-none').find(':input').prop('disabled', true);
            }
            updatePaymentFields();
            refreshSections();

            if (selectedMode() === 'yes' && $('#enrolment-code').val()) searchEnrolment();
            if (selectedMode() === 'no' && $('#branch').val()) {
                const oldLevel = @json(old('level_id'));
                const oldGrade = @json(old('grade_id'));
                loadSelect($('#level'), '/level/branch/' + $('#branch').val(), 'Select level', oldLevel)
                    .then(function() {
                        if (oldLevel) {
                            return loadSelect($('#grade'), gradesUrl + oldLevel, 'Select grade', oldGrade);
                        }
                    })
                    .then(function() {
                        if ($('#level').val()) loadRegistrationFee($('#branch').val(), $('#level').val());
                        refreshSections();
                    })
                    .catch(function() {
                        setAlert('danger', 'Unable to restore the selected level and grade.');
                    });
            }
        });
    </script>

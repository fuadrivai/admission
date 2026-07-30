let currentStep = 1;
const totalSteps = 4;
let bankCharger = 0;
let levels = [];
let productsData = [];
let uniform = {
    student_name : "",
    parent_name : "",
    parent_phone : "",
    parent_email : "",
    branch : "",
    level : "",
    grade_id : "",
    items : [],
    grand_total : 0,
    total_items : 0
};
$(document).ready(function () {
    getBankCharger();
    $(".required-select2").select2({
        theme: "bootstrap-5",
        width: "100%",
    });

    // Initialize Multiple Item Selector
    $("#item_selector").select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder:
            "Choose/search uniform items (e.g. Badge, Seragam SD, Kaus)...",
        allowClear: true,
    });

    // Step Navigation Line & View updates
    updateStepView();

    // Branch Change Listener
    $("#branch").on("change", function () {
        const branchId = $(this).val();
        getLevelsAndGrades(branchId);
    });

    $("#level").on("change", function () {
        const id = $(this).val();
        const selectedLevel = levels.find((level) => level.id == id);
        $("#grade_id")
            .attr("disabled", false)
            .empty()
            .append('<option value="">Select grade...</option>');
        if (selectedLevel) {
            selectedLevel.grades.forEach((grade) => {
                $("#grade_id").append(
                    `<option value="${grade.id}">${grade.name}</option>`,
                );
            });
        }
    });

    // Multiple Choice Item Selector Change
    $("#item_selector").on("change", function () {
        const selectedIds = $(this).val() || [];

        if (selectedIds.length === 0) {
            $("#no_items_notice").show();
            $(".product-table").hide();
        } else {
            $("#no_items_notice").hide();
            $(".product-table").show();
        }

        $(".product-row").each(function () {
            const productId = $(this).data("product-id").toString();
            const isSelected = selectedIds.includes(productId);

            if (isSelected) {
                if ($(this).is(":hidden")) {
                    $(this).fadeIn(200);
                    // Set default qty to 1 if it's currently 0
                    const qtyInput = $(`#qty_${productId}`);
                    if (parseFloat(qtyInput.val()) === 0) {
                        const step = parseFloat(qtyInput.attr("step")) || 1;
                        qtyInput.val(step);
                    }
                }
            } else {
                $(this).hide();
                $(`#qty_${productId}`).val(0);
            }
            updateProductPrice(productId);
        });

        syncUniformItems();
        calculateGrandTotal();
    });

    // Remove Item Button Click
    $(document).on("click", ".btn-remove-item", function () {
        const productId = $(this).data("product-id").toString();
        let currentSelected = $("#item_selector").val() || [];
        currentSelected = currentSelected.filter((id) => id !== productId);
        $("#item_selector").val(currentSelected).trigger("change");
    });

    // Size Change Listener
    $(document).on("change", ".item-size", function () {
        const productId = $(this).data("product-id");
        updateProductPrice(productId);
    });

    // Qty Stepper (-) Click
    $(document).on("click", ".btn-qty-minus", function () {
        const productId = $(this).data("product-id");
        const input = $(`#qty_${productId}`);
        const step = parseFloat(input.attr("step")) || 1;
        let val = parseFloat(input.val()) || 0;
        val = Math.max(0, val - step);
        input.val(val).trigger("change");
    });

    // Qty Stepper (+) Click
    $(document).on("click", ".btn-qty-plus", function () {
        const productId = $(this).data("product-id");
        const input = $(`#qty_${productId}`);
        const step = parseFloat(input.attr("step")) || 1;
        let val = parseFloat(input.val()) || 0;
        val = val + step;
        input.val(val).trigger("change");
    });

    // Qty Input & Change Listener
    $(document).on("input change", ".item-qty", function () {
        const productId = $(this).data("product-id");
        updateProductSubtotal(productId);
        calculateGrandTotal();
    });

    // Prev Button Click
    $("#prevBtn").on("click", function () {
        if (currentStep > 1) {
            currentStep--;
            updateStepView();
        }
    });

    // Next / Submit Button Click - make async and await validation
    $("#nextBtn").on("click", async function () {
        // Await validation which may include async product fetching
        const valid = await validateCurrentStep();
        if (!valid) return;

        if (currentStep < 3) {
            currentStep++;
            updateStepView();
        } else if (currentStep === 3) {
            submitOrderForm();
        }
    });
});

// Format Currency as Rupiah (e.g., Rp 150.000)
function formatRupiah(amount) {
    return "Rp " + (parseFloat(amount) || 0).toLocaleString("id-ID");
}

// Get Price for Product + Branch + Level + Size
function getUnitPrice(productId, size) {
    const product = productsData.find((p) => p.id == productId);
    if (!product || !product.prices || product.prices.length === 0) return 0;

    const branchId = $("#branch").val();
    const levelId = $("#level").val();

    // 1. Filter active prices
    let activePrices = product.prices.filter(
        (p) => p.is_active == 1 || p.is_active === true,
    );
    if (activePrices.length === 0) {
        activePrices = product.prices;
    }

    // 2. Filter by Branch & Level if selected
    let branchLevelPrices = activePrices;
    if (branchId && levelId) {
        const filtered = activePrices.filter(
            (p) =>
                (!p.branch_id || p.branch_id == branchId) &&
                (!p.level_id || p.level_id == levelId),
        );
        if (filtered.length > 0) {
            branchLevelPrices = filtered;
        }
    }

    // 3. Filter by Size if product has size
    if (product.has_size) {
        if (size) {
            const sizeMatched = branchLevelPrices.find((p) => p.size === size);
            if (sizeMatched) return parseFloat(sizeMatched.price);
        }
        return branchLevelPrices.length > 0
            ? parseFloat(branchLevelPrices[0].price)
            : 0;
    }

    // 4. Product has no size
    return branchLevelPrices.length > 0
        ? parseFloat(branchLevelPrices[0].price)
        : 0;
}

// Update single product price and subtotal
function updateProductPrice(productId) {
    const size = $(`#size_${productId}`).val() || "";
    const unitPrice = getUnitPrice(productId, size);
    $(`#price_display_${productId}`).text(formatRupiah(unitPrice));
    updateProductSubtotal(productId);
}

// Update subtotal for a single product
function updateProductSubtotal(productId) {
    const size = $(`#size_${productId}`).val() || "";
    const unitPrice = getUnitPrice(productId, size);
    const qty = parseFloat($(`#qty_${productId}`).val()) || 0;
    const subtotal = unitPrice * qty;

    $(`#subtotal_display_${productId}`).text(formatRupiah(subtotal));
}

// Update all product prices in table
function updateAllProductPrices() {
    productsData.forEach((product) => {
        const branchId = $("#branch").val();
        const levelId = $("#level").val();

        if (product.has_size && branchId && levelId && product.prices) {
            const availablePrices = product.prices.filter(
                (p) =>
                    p.branch_id == branchId &&
                    p.level_id == levelId &&
                    (p.is_active === 1 || p.is_active === true),
            );
            const sizeSelect = $(`#size_${product.id}`);
            const selectedSize = sizeSelect.val();

            if (availablePrices.length > 0) {
                sizeSelect
                    .empty()
                    .append('<option value="">Select size...</option>');
                availablePrices.forEach((p) => {
                    if (p.size) {
                        sizeSelect.append(
                            `<option value="${p.size}" ${p.size === selectedSize ? "selected" : ""}>${p.size}</option>`,
                        );
                    }
                });
            }
        }
        updateProductPrice(product.id);
    });
    calculateGrandTotal();
}

// Sync uniform.items[] from current DOM state
function syncUniformItems() {
    const selectedIds = $("#item_selector").val() || [];
    uniform.items = [];

    selectedIds.forEach(productId => {
        const product = productsData.find(p => p.id == productId);
        if (!product) return;

        const qty = parseFloat($(`#qty_${productId}`).val()) || 0;
        const size = $(`#size_${productId}`).val() || "";
        const unitPrice = getUnitPrice(productId, size);
        const subtotal = unitPrice * qty;

        if (qty > 0) {
            uniform.items.push({
                product_id: product.id,
                product_name: product.name,
                product_code: product.code,
                unit_type: product.unit_type ?? "",
                size: size,
                qty: qty,
                price: unitPrice,
                subtotal: subtotal
            });
        }
    });
}

// Calculate Grand Total and Total Items
function calculateGrandTotal() {
    syncUniformItems();

    let totalItems = 0;
    let grandTotal = 0;

    uniform.items.forEach(item => {
        totalItems += item.qty;
        grandTotal += item.subtotal;
    });

    grandTotal += bankCharger;

    uniform.total_items = totalItems;
    uniform.grand_total = grandTotal;

    $("#summary_total_items").text(totalItems);
    $("#summary_grand_total").text(formatRupiah(grandTotal));
}

// Validate Step inputs
async function validateCurrentStep() {
    let isValid = true;

    if (currentStep === 1) {
        const studentName = $("#student_name").val().trim();
        const parentName = $("#parent_name").val().trim();
        const parentPhone = $("#parent_phone").val().trim();
        const parentEmail = $("#parent_email").val().trim();

        if (!studentName) { $("#student_name").addClass("is-invalid"); isValid = false; } else { $("#student_name").removeClass("is-invalid"); }
        if (!parentName) { $("#parent_name").addClass("is-invalid"); isValid = false; } else { $("#parent_name").removeClass("is-invalid"); }
        if (!parentPhone) { $("#parent_phone").addClass("is-invalid"); isValid = false; } else { $("#parent_phone").removeClass("is-invalid"); }
        if (!parentEmail || !parentEmail.includes("@")) { $("#parent_email").addClass("is-invalid"); isValid = false; } else { $("#parent_email").removeClass("is-invalid"); }

        if (!isValid) showToast("Please fill in all required student & parent fields correctly.", "error");
    } else if (currentStep === 2) {
        const branch = $("#branch").val();
        const level = $("#level").val();
        const grade = $("#grade_id").val();

        if (!branch) { $("#branch").next(".select2-container").addClass("is-invalid"); isValid = false; } else { $("#branch").next(".select2-container").removeClass("is-invalid"); }
        if (!level) { $("#level").next(".select2-container").addClass("is-invalid"); isValid = false; } else { $("#level").next(".select2-container").removeClass("is-invalid"); }
        if (!grade) { $("#grade_id").next(".select2-container").addClass("is-invalid"); isValid = false; } else { $("#grade_id").next(".select2-container").removeClass("is-invalid"); }

        if (!isValid) {
            showToast("Please select Branch, Level, and Grade.", "error");
            return false;
        }
        const fetched = await getProduct(branch, level);
        productsData = fetched;
        generateUniformSelector();
        if (!fetched) {
            return false;
        }
    } else if (currentStep === 3) {
        const selectedItems = $("#item_selector").val() || [];
        if (selectedItems.length === 0) {
            showToast("Please select at least 1 uniform item to order.", "error");
            $("#item_selector").next(".select2-container").addClass("is-invalid");
            return false;
        } else {
            $("#item_selector").next(".select2-container").removeClass("is-invalid");
        }

        let hasValidQty = false;
        selectedItems.forEach(productId => {
            if (parseFloat($(`#qty_${productId}`).val()) > 0) {
                hasValidQty = true;
            }
        });

        if (!hasValidQty) {
            showToast("Please set quantity > 0 for your selected uniform items.", "error");
            isValid = false;
        }
    }

    return isValid;
}

// Update step UI progress
function updateStepView() {
    $(".section-step").removeClass("active");
    $(`.section-step[data-step="${currentStep}"]`).addClass("active");

    $(".step-item").removeClass("active completed");
    $(".step-item").each(function () {
        const step = parseInt($(this).data("step"));
        if (step < currentStep) {
            $(this).addClass("completed");
        } else if (step === currentStep) {
            $(this).addClass("active");
        }
    });

    // Update Progress Line width
    const progressPercent = ((currentStep - 1) / (totalSteps - 1)) * 100;
    $("#progressLine").css("width", progressPercent + "%");

    // Button visibility
    if (currentStep === 1) {
        $("#prevBtn").hide();
        $("#nextBtn").html('Next <i class="fas fa-arrow-right ms-1"></i>');
    } else if (currentStep === 2) {
        $("#prevBtn").show();
        $("#nextBtn").html('Next <i class="fas fa-arrow-right ms-1"></i>');
    } else if (currentStep === 3) {
        $("#prevBtn").show();
        $("#nextBtn").html(
            '<i class="fas fa-paper-plane me-1"></i> Submit Order',
        );
        updateAllProductPrices();
    } else if (currentStep === 4) {
        $("#buttonGroup").hide();
    }

    // Scroll to top of container on step change for mobile
    window.scrollTo({ top: 0, behavior: "smooth" });
}

// Show Toastify Notification
function showToast(message, type = "info") {
    Toastify({
        text: message,
        duration: 3500,
        gravity: "top",
        position: "right",
        style: {
            background:
                type === "error"
                    ? "linear-gradient(to right, #ff416c, #ff4b2b)"
                    : "linear-gradient(to right, #00b09b, #96c93d)",
        },
    }).showToast();
}

// Render Step 4 Order Summary
function renderOrderCompleteSummary(orderCode) {
    $("#successOrderCode").text(orderCode || "");
    $("#summary_student_name").text($("#student_name").val() || "-");
    $("#summary_parent_name").text($("#parent_name").val() || "-");
    
    const branchName = $("#branch option:selected").text() || "-";
    const levelName = $("#level option:selected").text() || "-";
    const gradeName = $("#grade_id option:selected").text() || "-";
    
    $("#summary_branch_level").text(`${branchName} / ${levelName}`);
    $("#summary_grade").text(gradeName);

    const tbody = $("#summary_items_tbody").empty();
    if (uniform.items && uniform.items.length > 0) {
        uniform.items.forEach(item => {
            tbody.append(`
                <tr>
                    <td>${item.product_name} <small class="text-muted">(${item.product_code})</small></td>
                    <td class="text-center">${item.size || '-'}</td>
                    <td class="text-center">${item.qty}</td>
                    <td class="text-end">${formatRupiah(item.price)}</td>
                    <td class="text-end fw-semibold">${formatRupiah(item.subtotal)}</td>
                </tr>
            `);
        });
    }

    $("#summary_final_total").text(formatRupiah(uniform.grand_total));
}

// Submit Form via AJAX
function submitOrderForm() {
    blockUI();
    // Sync items one final time before submit
    syncUniformItems();
    uniform.student_name = $("#student_name").val();
    uniform.parent_name = $("#parent_name").val();
    uniform.parent_phone = $("#parent_phone").val();
    uniform.parent_email = $("#parent_email").val();
    uniform.branch = $("#branch").val();    
    uniform.level = $("#level").val();
    uniform.grade_id = $("#grade_id").val();
    uniform.bank_charger = bankCharger;
    ajax(
        uniform,
        "/uniform/post",
        "POST",
        function (json) {
            if ($.unblockUI) $.unblockUI();
            if (json.success) {
                renderOrderCompleteSummary(json.order_code);
                currentStep = 4;
                updateStepView();
                showToast(
                    json.message || "Order submitted successfully!",
                    "success",
                );
            } else {
                showToast(
                    json.message || "Failed to submit order.",
                    "error",
                );
            }
        },
        function (err) {
            if ($.unblockUI) $.unblockUI();
            showToast(
                err.responseJSON?.message || "An error occurred while submitting order.",
                "error",
            );
        }
    );
}

function getBankCharger() {
    ajax(
        null,
        "/bank/single",
        "GET",
        function (json) {
            bankCharger = parseFloat(json.price ?? 0);
            $("#bank-form").text(formatNumber(bankCharger));
        },
        function (err) {
            toastify(
                "Error",
                err?.responseJSON?.message ?? "Please try again later",
                "bottom",
            );
        },
    );
}

function getLevelsAndGrades(branchId) {
    blockUI();
    ajax(
        null,
        `/level/branch/${branchId}`,
        "GET",
        function (json) {
            $("#level").attr("disabled", false);
            $("#level")
                .empty()
                .append('<option value="">Select level...</option>');
            levels = json;
            levels.forEach((level) => {
                $("#level").append(
                    `<option value="${level.id}">${level.name}</option>`,
                );
            });

            $("#grade_id").val("").trigger('change').attr('disabled',true);
        },
        function (err) {
            toastify(
                "Error",
                err?.responseJSON?.message ?? "Please try again later",
                "bottom",
            );
        },
    );
}

// Fetch product data for selected branch and level
async function getProduct(branchId, levelId) {
    blockUI();
    let response =await ajaxPromise(
        null,
        `/uniform/products/branch/level?branch=${branchId}&level=${levelId}`,
        "GET",
        function (json) {
            productsData = json;
            console.log(productsData);
        },
        function (err) {
            toastify(
                "Error",
                err?.responseJSON?.message ?? "Please try again later",
                "bottom",
            );
        },
    );
    return response;
}

function generateUniformSelector(){
    $('#item_selector').empty();
    productsData.forEach(val=>{
        $('#item_selector').append(`
            <option value="${val.id}">${val.name} ${val.code}</option>
        `)
    })
    
}

let currentStep = 1;
const totalSteps = 4;
let bankCharger = 0;
let levels = [];
let productsData = [];
let uniform = {
    student_name: "",
    parent_name: "",
    parent_phone: "",
    parent_email: "",
    branch: "",
    level: "",
    grade_id: "",
    items: [],
    grand_total: 0,
    total_items: 0,
};
$(document).ready(function () {
    getBankCharger();
    $(".required-select2").select2({
        theme: "bootstrap-5",
        width: "100%",
    });

    // Product modal opener
    $("#openProductModalBtn").on("click", async function () {
        const branchId = $("#branch").val();
        const levelId = $("#level").val();
        if (!branchId || !levelId) {
            showToast("Please select Branch and Level first.", "error");
            return;
        }
        const fetched = await getProduct(branchId, levelId);
        productsData = fetched || [];
        renderProductModal();
        const modal = new bootstrap.Modal(
            document.getElementById("productModal"),
        );
        modal.show();
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
        // Prefetch products for faster modal
        const branchId = $("#branch").val();
        if (branchId && id) {
            getProduct(branchId, id).then((fetched) => {
                productsData = fetched || [];
            });
        }
    });

    // product rows are added via modal - handled elsewhere

    // Remove Item Button Click (removes row)
    $(document).on("click", ".btn-remove-item", function () {
        const rowId = $(this).data("row-id");
        $(`.product-row[data-row-id="${rowId}"]`).remove();
        if ($(".product-row").length === 0) {
            $("#no_items_notice").show();
            $(".product-table").hide();
        }
        calculateGrandTotal();
    });

    // size no longer edited inline (shown as text)

    // Qty Stepper (-) Click (uses row-id)
    $(document).on("click", ".btn-qty-minus", function () {
        const rowId = $(this).data("row-id");
        const input = $(`#qty_${rowId}`);
        const step = parseFloat(input.attr("step")) || 1;
        let val = parseFloat(input.val()) || 0;
        val = Math.max(0, val - step);
        input.val(val).trigger("change");
    });

    // Qty Stepper (+) Click (uses row-id)
    $(document).on("click", ".btn-qty-plus", function () {
        const rowId = $(this).data("row-id");
        const input = $(`#qty_${rowId}`);
        const step = parseFloat(input.attr("step")) || 1;
        let val = parseFloat(input.val()) || 0;
        val = val + step;
        const type = $(this).data("type");
        let max = type === "pcs" ? 3 : 4;
        if (val > max) {
            alert(`Maximum ${max} ${type} allowed`);
            input.val(max).trigger("change");
            return;
        }
        input.val(val).trigger("change");
    });

    // Qty Input & Change Listener (uses row-id)
    $(document).on("input change", ".item-qty", function () {
        const rowId = $(this).data("row-id");
        let val = parseFloat($(this).val()) || 0;
        const type = $(this).data("type");
        let max = type === "pcs" ? 3 : 4;
        if (val > max) {
            alert(`Maximum ${max} ${type} allowed`);
            $(this).val(max).trigger("change");
            return;
        }
        updateRowSubtotal(rowId);
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

    // 1. Filter active prices
    let activePrices = product.prices.filter(
        (p) => p.is_active == 1 || p.is_active === true,
    );
    if (activePrices.length === 0) {
        activePrices = product.prices;
    }

    // 2. Filter by Branch if selected
    let branchPrices = activePrices;
    if (branchId) {
        const filtered = activePrices.filter(
            (p) => !p.branch_id || p.branch_id == branchId,
        );
        if (filtered.length > 0) {
            branchPrices = filtered;
        }
    }

    // 3. Filter by Size if product has size
    if (product.has_size) {
        if (size) {
            const sizeMatched = branchPrices.find((p) => p.size === size);
            if (sizeMatched) return parseFloat(sizeMatched.price);
        }
        return branchPrices.length > 0 ? parseFloat(branchPrices[0].price) : 0;
    }

    // 4. Product has no size
    return branchPrices.length > 0 ? parseFloat(branchPrices[0].price) : 0;
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

// Update all product prices and populate size select options
function updateAllProductPrices() {
    productsData.forEach((product) => {
        const branchId = $("#branch").val();

        if (product.has_size) {
            const sizeSelect = $(`#size_${product.id}`);
            const selectedSize = sizeSelect.val();

            let sizesToRender = [];

            if (product.prices && product.prices.length > 0) {
                // Filter active prices matching current branch (or general branch)
                let matchingPrices = product.prices.filter(
                    (p) =>
                        (p.is_active == 1 || p.is_active === true) &&
                        (!p.branch_id || p.branch_id == branchId) &&
                        p.size,
                );

                if (matchingPrices.length === 0) {
                    matchingPrices = product.prices.filter(
                        (p) =>
                            (p.is_active == 1 || p.is_active === true) &&
                            p.size,
                    );
                }

                matchingPrices.forEach((p) => {
                    if (p.size && !sizesToRender.includes(p.size)) {
                        sizesToRender.push(p.size);
                    }
                });
            }

            // Fallback default standard sizes if no specific prices exist
            if (sizesToRender.length === 0) {
                sizesToRender = [
                    "XS",
                    "S",
                    "M",
                    "L",
                    "XL",
                    "XXL",
                    "XXXL",
                    "4XL",
                    "5XL",
                    "OTHER",
                ];
            }

            sizeSelect.empty().append('<option value="">Select size</option>');
            sizesToRender.forEach((sz) => {
                sizeSelect.append(
                    `<option value="${sz}" ${sz === selectedSize ? "selected" : ""}>${sz}</option>`,
                );
            });
        }
        updateProductPrice(product.id);
    });
    calculateGrandTotal();
}

// Sync uniform.items[] from current DOM state
function syncUniformItems() {
    uniform.items = [];
    $(".product-row").each(function () {
        const rowId = $(this).data("row-id");
        const productId = $(this).data("product-id");
        const price = parseFloat($(this).data("price")) || 0;
        const size = $(`#size_display_${rowId}`).text() || "";
        const qty = parseFloat($(`#qty_${rowId}`).val()) || 0;
        const subtotal = price * qty;

        if (qty > 0) {
            uniform.items.push({
                product_id: productId,
                product_name: $(this).data("product-name") || "",
                product_code: $(this).data("product-code") || "",
                unit_type: $(this).data("unit-type") || "",
                size: size,
                qty: qty,
                price: price,
                subtotal: subtotal,
            });
        }
    });
}

// Calculate Grand Total and Total Items
function calculateGrandTotal() {
    syncUniformItems();

    let totalItems = 0;
    let grandTotal = 0;

    uniform.items.forEach((item) => {
        totalItems += item.qty;
        grandTotal += item.subtotal;
    });

    grandTotal += bankCharger;

    uniform.total_items = totalItems;
    uniform.grand_total = grandTotal;

    $("#summary_total_items").text(totalItems);
    $("#summary_grand_total").text(formatRupiah(grandTotal));
}

// Update subtotal for a dynamic row
function updateRowSubtotal(rowId) {
    const tr = $(`.product-row[data-row-id="${rowId}"]`);
    if (!tr || tr.length === 0) return;
    const unitPrice = parseFloat(tr.data("price")) || 0;
    const qty = parseFloat($(`#qty_${rowId}`).val()) || 0;
    const subtotal = unitPrice * qty;
    $(`#subtotal_display_${rowId}`).text(formatRupiah(subtotal));
}

// Render products into the modal with price buttons
function renderProductModal() {
    const tbody = $("#product_modal_tbody").empty();
    if (!productsData || productsData.length === 0) {
        tbody.append(
            `<tr><td colspan="3" class="text-center text-muted">No products available</td></tr>`,
        );
        return;
    }

    const branchId = $("#branch").val();
    productsData.forEach((product) => {
        const unitBadge = `<span class="badge ${product.unit_type == "pcs" ? "badge-unit-pcs" : "badge-unit-meter"}">${(product.unit_type || "").toUpperCase()}</span>`;
        let pricesHtml = "";
        if (product.prices && product.prices.length > 0) {
            // Prefer branch-specific prices; if none exist, fallback to general (no branch_id)
            let matchingPrices = product.prices.filter(
                (p) => p.branch_id == branchId,
            );
            if (matchingPrices.length === 0) {
                matchingPrices = product.prices.filter(
                    (p) =>
                        !p.branch_id ||
                        p.branch_id === null ||
                        p.branch_id === "",
                );
            }
            // Final fallback: all prices
            if (matchingPrices.length === 0) matchingPrices = product.prices;

            matchingPrices.forEach((p) => {
                const sizeText = p.size ? `${p.size} ` : "";
                pricesHtml += ` <button type="button" class="btn btn-sm btn-outline-primary btn-choose-price" data-product-id="${product.id}" data-price-id="${p.id}" data-size="${p.size || ""}" data-price="${p.price}" data-unit-type="${product.unit_type}" data-product-name="${escapeHtml(product.name)}" data-product-code="${escapeHtml(product.code)}">${sizeText}${formatRupiah(p.price)}</button>`;
            });
        } else {
            pricesHtml = '<span class="text-muted">No price rules</span>';
        }

        tbody.append(`
            <tr>
                <td>
                    <div class="fw-semibold">${escapeHtml(product.name)}</div>
                    <div class="small text-muted">${escapeHtml(product.code || "")}</div>
                </td>
                <td class="text-center">${unitBadge}</td>
                <td class="text-center">${pricesHtml}</td>
            </tr>
        `);
    });
}

// When a price button is clicked, add product row to order table
$(document).on("click", ".btn-choose-price", function () {
    const productId = $(this).data("product-id");
    const priceId = $(this).data("price-id");
    const size = $(this).data("size") || "";
    const price = parseFloat($(this).data("price")) || 0;
    const unitType = $(this).data("unit-type") || "pcs";
    const productName = $(this).data("product-name") || "";
    const productCode = $(this).data("product-code") || "";

    addProductToTable(
        productId,
        priceId,
        size,
        price,
        productName,
        productCode,
        unitType,
    );
    const modalEl = document.getElementById("productModal");
    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) modal.hide();
});

function addProductToTable(
    productId,
    priceId,
    size,
    unitPrice,
    productName,
    productCode,
    unitType,
) {
    const rowId = `${productId}_${priceId}`;
    // If already exists, increment qty
    const existing = $(`.product-row[data-row-id="${rowId}"]`);
    if (existing && existing.length > 0) {
        const input = $(`#qty_${rowId}`);
        const step = parseFloat(input.attr("step")) || 1;
        input
            .val(Math.max(0, (parseFloat(input.val()) || 0) + step))
            .trigger("change");
        return;
    }

    const unitBadge = unitType == "pcs" ? "badge-unit-pcs" : "badge-unit-meter";
    const step = unitType == "pcs" ? 1 : 0.5;
    const qtyDefault = 1;
    const tr = $(
        `<tr class="product-row" data-row-id="${rowId}" data-product-id="${productId}" data-price-id="${priceId}" data-price="${unitPrice}" data-unit-type="${unitType}" data-product-name="${escapeHtml(productName)}" data-product-code="${escapeHtml(productCode)}">
            <td data-label="Product Details">
                <div class="fw-bold text-dark">${escapeHtml(productName)}</div>
                <div class="small text-muted">${escapeHtml(productCode)}</div>
                <div class="small mt-1">
                    <span class="${unitBadge}">${(unitType || "").toUpperCase()}</span>
                    <span id="size_display_${rowId}" class="ms-2">${escapeHtml(size) || "-"}</span>
                </div>
                <div class="small mt-1 text-primary">Price: <span class="fw-semibold">${formatRupiah(unitPrice)}</span></div>
            </td>
            <td data-label="Qty" class="text-center">
                <div class="input-group input-group-sm qty-stepper">
                    <button class="btn btn-outline-secondary btn-qty-minus" type="button" data-row-id="${rowId}" data-type="${unitType}">-</button>
                    <input type="number" class="form-control text-center item-qty" name="items[][qty]" id="qty_${rowId}" data-row-id="${rowId}" data-type="${unitType}" min="0" value="${qtyDefault}" step="${step}">
                    <button class="btn btn-outline-secondary btn-qty-plus" type="button" data-row-id="${rowId}" data-type="${unitType}">+</button>
                </div>
            </td>
            <td data-label="Subtotal" class="text-end"><span id="subtotal_display_${rowId}" class="fw-bold text-primary">${formatRupiah(unitPrice * qtyDefault)}</span></td>
            <td data-label="Action" class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-item" data-row-id="${rowId}"><i class="fas fa-trash-alt"></i></button></td>
        </tr>`,
    );

    $("#product_table_tbody").append(tr);
    $("#no_items_notice").hide();
    $(".product-table").show();
    calculateGrandTotal();
}

// small helper to escape html in product strings
function escapeHtml(str) {
    if (!str && str !== 0) return "";
    return String(str)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#39;");
}

// Validate Step inputs
async function validateCurrentStep() {
    let isValid = true;

    if (currentStep === 1) {
        const studentName = $("#student_name").val().trim();
        const parentName = $("#parent_name").val().trim();
        const parentPhone = $("#parent_phone").val().trim();
        const parentEmail = $("#parent_email").val().trim();

        if (!studentName) {
            $("#student_name").addClass("is-invalid");
            isValid = false;
        } else {
            $("#student_name").removeClass("is-invalid");
        }
        if (!parentName) {
            $("#parent_name").addClass("is-invalid");
            isValid = false;
        } else {
            $("#parent_name").removeClass("is-invalid");
        }
        if (!parentPhone) {
            $("#parent_phone").addClass("is-invalid");
            isValid = false;
        } else {
            $("#parent_phone").removeClass("is-invalid");
        }
        if (!parentEmail || !parentEmail.includes("@")) {
            $("#parent_email").addClass("is-invalid");
            isValid = false;
        } else {
            $("#parent_email").removeClass("is-invalid");
        }

        if (!isValid)
            showToast(
                "Please fill in all required student & parent fields correctly.",
                "error",
            );
    } else if (currentStep === 2) {
        const branch = $("#branch").val();
        const level = $("#level").val();
        const grade = $("#grade_id").val();

        if (!branch) {
            $("#branch").next(".select2-container").addClass("is-invalid");
            isValid = false;
        } else {
            $("#branch").next(".select2-container").removeClass("is-invalid");
        }
        if (!level) {
            $("#level").next(".select2-container").addClass("is-invalid");
            isValid = false;
        } else {
            $("#level").next(".select2-container").removeClass("is-invalid");
        }
        if (!grade) {
            $("#grade_id").next(".select2-container").addClass("is-invalid");
            isValid = false;
        } else {
            $("#grade_id").next(".select2-container").removeClass("is-invalid");
        }

        if (!isValid) {
            showToast("Please select Branch, Level, and Grade.", "error");
            return false;
        }
        const fetched = await getProduct(branch, level);
        productsData = fetched || [];
        // products will be chosen from the modal when user opens it
        if (!fetched) {
            return false;
        }
    } else if (currentStep === 3) {
        // Ensure user has added at least one product row and qty > 0
        const rows = $(".product-row");
        if (!rows || rows.length === 0) {
            showToast(
                "Please add at least 1 uniform item from the product list.",
                "error",
            );
            return false;
        }

        let hasValidQty = false;
        rows.each(function () {
            const rowId = $(this).data("row-id");
            const qty = parseFloat($(`#qty_${rowId}`).val()) || 0;
            if (qty > 0) hasValidQty = true;
        });

        if (!hasValidQty) {
            showToast(
                "Please set quantity > 0 for your selected uniform items.",
                "error",
            );
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
function renderOrderCompleteSummary(orderCode, orderLink) {
    $("#successOrderCode").text(orderCode || "");
    if (orderLink) {
        $("#successPaymentLink").attr("href", orderLink).show();
    } else {
        $("#successPaymentLink").hide();
    }
    $("#summary_student_name").text($("#student_name").val() || "-");
    $("#summary_parent_name").text($("#parent_name").val() || "-");

    const branchName = $("#branch option:selected").text() || "-";
    const levelName = $("#level option:selected").text() || "-";
    const gradeName = $("#grade_id option:selected").text() || "-";

    $("#summary_branch_level").text(`${branchName} / ${levelName}`);
    $("#summary_grade").text(gradeName);

    const tbody = $("#summary_items_tbody").empty();
    if (uniform.items && uniform.items.length > 0) {
        uniform.items.forEach((item) => {
            tbody.append(`
                <tr>
                    <td>${item.product_name} <small class="text-muted">(${item.product_code})</small></td>
                    <td class="text-center">${item.size || "-"}</td>
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
                renderOrderCompleteSummary(
                    json.order_code,
                    json.data?.order_link || json.order_link,
                );
                currentStep = 4;
                updateStepView();
                showToast(
                    json.message || "Order submitted successfully!",
                    "success",
                );
            } else {
                showToast(json.message || "Failed to submit order.", "error");
            }
        },
        function (err) {
            if ($.unblockUI) $.unblockUI();
            showToast(
                err.responseJSON?.message ||
                    "An error occurred while submitting order.",
                "error",
            );
        },
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

            $("#grade_id").val("").trigger("change").attr("disabled", true);
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
    let response = await ajaxPromise(
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

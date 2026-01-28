const uploadedFiles = {
    ktp_father: null,
    ktp_mother: null,
    birth_certificate: null,
    family_card: null,
};
const uploadStatuses = {
    ktp_father: false,
    ktp_mother: false,
    birth_certificate: false,
    family_card: false,
};
$(document).ready(function () {
    getDocumentByCode();
    $("#ktp_fatherInput").on("change", function (e) {
        handleFileUpload(e, "ktp_father");
    });

    $("#ktp_motherInput").on("change", function (e) {
        handleFileUpload(e, "ktp_mother");
    });

    $("#birth_certificateInput").on("change", function (e) {
        handleFileUpload(e, "birth_certificate");
    });

    $("#family_cardInput").on("change", function (e) {
        handleFileUpload(e, "family_card");
    });

    $("#submitBtn").on("click", submitDocuments);

    window.removeFile = function (documentType) {
        uploadedFiles[documentType] = null;
        uploadStatuses[documentType] = false;
        $(`#${documentType}Preview`).removeClass("show").html("");
        updateCardStatus(documentType, "removed");
        checkSubmitButton();
    };
    checkSubmitButton();

    $("html, body").animate(
        {
            scrollTop: 0,
        },
        300,
    );
});

function handleFileUpload(event, documentType) {
    const file = event.target.files[0];
    if (!file) return;

    const maxSize = 5 * 1024 * 1024;
    if (file.size > maxSize) {
        showError(documentType, "Ukuran file terlalu besar. Maksimal 5MB.");
        return;
    }

    const validTypes = [
        "image/jpeg",
        "image/jpg",
        "image/png",
        "application/pdf",
    ];
    if (!validTypes.includes(file.type)) {
        showError(
            documentType,
            "Format file tidak didukung. Gunakan JPG, PNG, atau PDF.",
        );
        return;
    }

    uploadedFiles[documentType] = file;
    showFilePreviewFromFile(file, documentType);
    updateCardStatus(documentType, "uploaded");
    checkSubmitButton();

    event.target.value = "";
}

function showError(documentType, message) {
    const card = $(`#${documentType}Card`);
    card.addClass("error");

    setTimeout(() => {
        card.removeClass("error");
    }, 3000);

    alert(message);
}

function updateCardStatus(documentType, status) {
    const card = $(`#${documentType}Card`);
    card.removeClass("uploaded error");

    if (status === "uploaded") {
        card.addClass("uploaded");
    } else if (status === "removed") {
    }
}

function checkSubmitButton() {
    const allUploaded = Object.values(uploadStatuses).every(
        (status) => status === true,
    );
    $("#submitBtn").prop("disabled", !allUploaded);
}

function formatFileSize(bytes) {
    if (bytes === 0) return "0 Bytes";
    const k = 1024;
    const sizes = ["Bytes", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
}

function showSuccessMessage(message) {
    const alertDiv = $("<div>")
        .addClass(
            "alert alert-success alert-dismissible fade show position-fixed",
        )
        .css({
            top: "20px",
            right: "20px",
            "z-index": "9999",
            "min-width": "300px",
        }).html(`
                        <i class="bi bi-check-circle"></i> ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `);

    $("body").append(alertDiv);

    setTimeout(() => {
        alertDiv.alert("close");
    }, 3000);
}

function submitDocuments() {
    const formData = new FormData();
    const admissionCode = $("#code").val();

    formData.append("admission_id", $("#id").val());
    const files = [
        { key: "ktp_father", data: "ktp_father", type: "ktp_father" },
        { key: "ktp_mother", data: "ktp_mother", type: "ktp_mother" },
        {
            key: "birth_certificate",
            data: "birth_certificate",
            type: "birth_certificate",
        },
        { key: "family_card", data: "family_card", type: "family_card" },
    ];

    files.forEach((item) => {
        const file = uploadedFiles[item.data];
        if (file) {
            formData.append(item.key, file);
        }
    });

    blockUI();

    $.ajax({
        url: `/document/file`,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (res) {
            toastify("success", "Data berhasil disimpan", "bottom");
            setTimeout(function () {
                window.location.href = `/document/statement/${admissionCode}`;
            }, 1000);
        },
        error: function (err) {
            toastify(
                "Error",
                err?.responseJSON?.message ?? "Please try again later",
                "bottom",
            );
        },
    });
}

async function getDocumentByCode() {
    blockUI();
    let id = $("#id").val();
    let documents = await ajaxPromise(null, `/document/file/id/${id}`, "GET");

    documents.forEach((doc) => {
        uploadStatuses[doc.type] = true;
        showFilePreviewFromUrl(doc);
    });
}

function getFileUrl(filePath) {
    const baseUrl = "http://192.168.206.121:3000/storage/";
    return baseUrl + filePath;
}

function showFilePreviewFromUrl(fileData) {
    const previewContainer = $(`#${fileData.type}Preview`);
    const fileSize = formatFileSize(fileData.file_size);
    const fileUrl = getFileUrl(fileData.file_path);

    let previewContent = "";

    if (fileData.mime_type.startsWith("image/")) {
        previewContent = `
            <div class="file-info">
                <div class="file-icon">
                    <i class="bi bi-file-image"></i>
                </div>
                <div class="file-details">
                    <div class="file-name">${fileData.original_name}</div>
                    <div class="file-size">${fileSize}</div>
                </div>
                <div class="file-remove" onclick="removeFile('${fileData.type}')">
                    <i class="bi bi-x-circle"></i>
                </div>
            </div>
            <img src="${fileUrl}" class="preview-image" alt="Preview">
        `;
    } else if (fileData.mime_type === "application/pdf") {
        previewContent = `
            <div class="file-info">
                <div class="file-icon">
                    <i class="bi bi-file-pdf"></i>
                </div>
                <div class="file-details">
                    <div class="file-name">${fileData.original_name}</div>
                    <div class="file-size">${fileSize}</div>
                </div>
                <div class="file-remove" onclick="removeFile('${fileData.type}')">
                    <i class="bi bi-x-circle"></i>
                </div>
            </div>
            <div class="alert alert-info mt-2">
                <i class="bi bi-info-circle"></i> File PDF telah diupload.
                <a href="${fileUrl}" target="_blank" class="ms-2">Lihat file</a>
            </div>
        `;
    }

    previewContainer.html(previewContent).addClass("show");
    updateCardStatus(fileData.type, "uploaded");
    checkSubmitButton();
}

function showFilePreviewFromFile(file, documentType) {
    const previewContainer = $(`#${documentType}Preview`);
    const fileSize = formatFileSize(file.size);

    let previewContent = "";

    if (file.type.startsWith("image/")) {
        const reader = new FileReader();
        reader.onload = function (e) {
            previewContent = `
                            <div class="file-info">
                                <div class="file-icon">
                                    <i class="bi bi-file-image"></i>
                                </div>
                                <div class="file-details">
                                    <div class="file-name">${file.name}</div>
                                    <div class="file-size">${fileSize}</div>
                                </div>
                                <div class="file-remove" onclick="removeFile('${documentType}')">
                                    <i class="bi bi-x-circle"></i>
                                </div>
                            </div>
                            <img src="${e.target.result}" class="preview-image" alt="Preview">
                        `;
            previewContainer.html(previewContent).addClass("show");
        };
        reader.readAsDataURL(file);
    } else if (file.type === "application/pdf") {
        previewContent = `
                        <div class="file-info">
                            <div class="file-icon">
                                <i class="bi bi-file-pdf"></i>
                            </div>
                            <div class="file-details">
                                <div class="file-name">${file.name}</div>
                                <div class="file-size">${fileSize}</div>
                            </div>
                            <div class="file-remove" onclick="removeFile('${documentType}')">
                                <i class="bi bi-x-circle"></i>
                            </div>
                        </div>
                        <div class="alert alert-info mt-2">
                            <i class="bi bi-info-circle"></i> File PDF telah diupload. Pastikan isi dokumen sudah benar.
                        </div>
                    `;
        previewContainer.html(previewContent).addClass("show");
    }

    uploadStatuses[documentType] = true;
}

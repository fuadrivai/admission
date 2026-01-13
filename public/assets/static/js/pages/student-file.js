const uploadedFiles = {
    ktpAyah: null,
    ktpIbu: null,
    akte: null,
    kk: null,
};
$(document).ready(function () {
    $("#ktpAyahInput").on("change", function (e) {
        handleFileUpload(e, "ktpAyah");
    });

    $("#ktpIbuInput").on("change", function (e) {
        handleFileUpload(e, "ktpIbu");
    });

    $("#akteInput").on("change", function (e) {
        handleFileUpload(e, "akte");
    });

    $("#kkInput").on("change", function (e) {
        handleFileUpload(e, "kk");
    });

    $("#submitBtn").on("click", submitDocuments);

    window.removeFile = function (documentType) {
        uploadedFiles[documentType] = null;
        $(`#${documentType}Preview`).removeClass("show").html("");
        updateCardStatus(documentType, "removed");
        checkSubmitButton();
    };
    checkSubmitButton();
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
            "Format file tidak didukung. Gunakan JPG, PNG, atau PDF."
        );
        return;
    }

    uploadedFiles[documentType] = file;
    showFilePreview(file, documentType);
    updateCardStatus(documentType, "uploaded");
    checkSubmitButton();

    event.target.value = "";
}

function showFilePreview(file, documentType) {
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
    const allUploaded = Object.values(uploadedFiles).every(
        (file) => file !== null
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
            "alert alert-success alert-dismissible fade show position-fixed"
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
    Object.keys(uploadedFiles).forEach((key) => {
        if (uploadedFiles[key]) {
            formData.append(key, uploadedFiles[key]);
        }
    });

    formData.append("timestamp", new Date().toISOString());

    console.log("Submitting documents:", formData);

    $("#submitBtn").html(
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengupload...'
    );
    $("#submitBtn").prop("disabled", true);

    setTimeout(() => {
        $("#submitBtn").html(
            '<i class="bi bi-send-check"></i> Kirim Semua Berkas'
        );
        $("#submitBtn").prop("disabled", false);

        showSuccessMessage("Semua dokumen berhasil diupload! Terima kasih.");
    }, 2000);
}

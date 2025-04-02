document.addEventListener("DOMContentLoaded", function () {
    // Existing attachment reordering code
    const rows = Array.from(document.querySelectorAll("tr")).filter((row) => {
        return row.querySelector(".position-input");
    });

    // Rest of your existing code...

    // Add new functionality to handle file uploads and button state
    const fileInput = document.getElementById("listingFormAttachmentUpload");
    const addButton = document.querySelector(".form-attachments .btn-primary");

    // Initially disable the add button if no files are selected
    if (addButton) {
        addButton.disabled = true;
        addButton.classList.add("btn-disabled");
    }

    // Enable/disable button based on file selection
    if (fileInput) {
        fileInput.addEventListener("change", function () {
            const hasFiles = this.files && this.files.length > 0;

            if (addButton) {
                if (hasFiles) {
                    // Enable button
                    addButton.disabled = false;
                    addButton.classList.remove("btn-disabled");
                } else {
                    // Disable button
                    addButton.disabled = true;
                    addButton.classList.add("btn-disabled");
                }
            }

            // Display file names (optional)
            const attachmentsList = document.getElementById("attachmentsList");
            if (attachmentsList) {
                attachmentsList.innerHTML = "";

                if (hasFiles) {
                    const fileList = document.createElement("ul");
                    fileList.className = "attachment-files-list";

                    Array.from(this.files).forEach((file) => {
                        const listItem = document.createElement("li");
                        listItem.textContent = `${file.name} (${formatFileSize(
                            file.size
                        )})`;
                        fileList.appendChild(listItem);
                    });

                    attachmentsList.appendChild(fileList);
                }
            }
        });
    }

    // Helper function to format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return "0 Bytes";

        const k = 1024;
        const sizes = ["Bytes", "KB", "MB", "GB"];
        const i = Math.floor(Math.log(bytes) / Math.log(k));

        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
    }
});

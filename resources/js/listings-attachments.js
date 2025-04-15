document.addEventListener("DOMContentLoaded", function () {
    // Existing attachment reordering code
    const rows = Array.from(document.querySelectorAll("tr")).filter((row) => {
        return row.querySelector(".position-input");
    });

    // Sort rows by position
    rows.sort((a, b) => {
        const posA = parseInt(a.querySelector(".position-input").value);
        const posB = parseInt(b.querySelector(".position-input").value);
        return posA - posB;
    });

    // Move Up button click handler
    document.querySelectorAll(".move-up").forEach((button) => {
        button.addEventListener("click", function () {
            const currentRow = this.closest("tr");
            const currentPos = parseInt(
                currentRow.querySelector(".position-input").value
            );
            const currentIndex = rows.indexOf(currentRow);

            // Don't move if already at the top
            if (currentIndex === 0) return;

            // Swap with the row above
            const prevRow = rows[currentIndex - 1];
            const prevPos = parseInt(
                prevRow.querySelector(".position-input").value
            );

            // Update hidden input values only (no display elements to update)
            currentRow.querySelector(".position-input").value = prevPos;
            prevRow.querySelector(".position-input").value = currentPos;

            // Reorder in the array
            rows[currentIndex] = prevRow;
            rows[currentIndex - 1] = currentRow;

            // Reorder in the DOM
            const tbody = currentRow.parentNode;
            tbody.insertBefore(currentRow, prevRow);

            // Update visual states
            updateButtonStates();
        });
    });

    // Move Down button click handler
    document.querySelectorAll(".move-down").forEach((button) => {
        button.addEventListener("click", function () {
            const currentRow = this.closest("tr");
            const currentPos = parseInt(
                currentRow.querySelector(".position-input").value
            );
            const currentIndex = rows.indexOf(currentRow);

            // Don't move if already at the bottom
            if (currentIndex === rows.length - 1) return;

            // Swap with the row below
            const nextRow = rows[currentIndex + 1];
            const nextPos = parseInt(
                nextRow.querySelector(".position-input").value
            );

            // Update hidden input values only (no display elements to update)
            currentRow.querySelector(".position-input").value = nextPos;
            nextRow.querySelector(".position-input").value = currentPos;

            // Reorder in the array
            rows[currentIndex] = nextRow;
            rows[currentIndex + 1] = currentRow;

            // Reorder in the DOM - this is what visually moves the rows
            const tbody = currentRow.parentNode;
            tbody.insertBefore(nextRow, currentRow);

            // Update visual states
            updateButtonStates();
        });
    });

    // Function to update which buttons should be enabled/disabled
    function updateButtonStates() {
        rows.forEach((row, index) => {
            const upButton = row.querySelector(".move-up");
            const downButton = row.querySelector(".move-down");

            // Disable up button for first row
            if (index === 0) {
                upButton.classList.add("disabled");
                upButton.setAttribute("disabled", "disabled");
            } else {
                upButton.classList.remove("disabled");
                upButton.removeAttribute("disabled");
            }

            // Disable down button for last row
            if (index === rows.length - 1) {
                downButton.classList.add("disabled");
                downButton.setAttribute("disabled", "disabled");
            } else {
                downButton.classList.remove("disabled");
                downButton.removeAttribute("disabled");
            }
        });
    }

    // Initialize button states
    updateButtonStates();

    // Add new functionality to handle file uploads and button state
    const fileInput =
        document.getElementById("listingFormAttachmentUpload") ||
        document.getElementById("orderFormAttachmentUpload");
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

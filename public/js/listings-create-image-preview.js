document.addEventListener("DOMContentLoaded", function () {
    const imageUploadInput = document.getElementById("listingFormImageUpload");
    const imagePreviewsContainer = document.getElementById("imagePreviews");
    let allFiles = [];

    imageUploadInput.addEventListener("change", function () {
        const newFiles = Array.from(imageUploadInput.files);
        allFiles = allFiles.concat(newFiles);

        imagePreviewsContainer.innerHTML = ""; // Clear previous previews

        allFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const fileWrapper = document.createElement("div");
                fileWrapper.style.position = "relative";
                fileWrapper.style.display = "inline-block";
                fileWrapper.style.margin = "10px";

                let fileElement;
                if (file.type.startsWith("image/")) {
                    fileElement = document.createElement("img");
                    fileElement.src = e.target.result;
                    fileElement.style.maxWidth = "100px";
                } else if (file.type.startsWith("video/")) {
                    fileElement = document.createElement("video");
                    fileElement.src = e.target.result;
                    fileElement.style.maxWidth = "100px";
                    fileElement.controls = true;
                }

                fileWrapper.appendChild(fileElement);

                const removeButton = document.createElement("button");
                removeButton.textContent = "x";
                removeButton.style.position = "absolute";
                removeButton.style.top = "0";
                removeButton.style.right = "0";
                removeButton.style.backgroundColor = "red";
                removeButton.style.color = "white";
                removeButton.style.border = "none";
                removeButton.style.borderRadius = "50%";
                removeButton.style.width = "20px";
                removeButton.style.height = "20px";
                removeButton.style.cursor = "pointer";
                removeButton.addEventListener("click", function () {
                    allFiles.splice(index, 1);
                    const dataTransfer = new DataTransfer();
                    allFiles.forEach((file) => dataTransfer.items.add(file));
                    imageUploadInput.files = dataTransfer.files;
                    fileWrapper.remove();
                });

                fileWrapper.appendChild(removeButton);
                imagePreviewsContainer.appendChild(fileWrapper);
            };
            reader.readAsDataURL(file);
        });

        // Clear the input value to allow re-uploading the same file if needed
        imageUploadInput.value = "";
    });
});

import axios from "axios";
import "./bootstrap";

// Import your custom JavaScript files
import "./listings-search-dropdown";

document.addEventListener("DOMContentLoaded", function () {
    const initSlider = () => {
        const slides = document.querySelectorAll(".hero-slide");
        let currentIndex = 0; // Track the current slide
        const totalSlides = slides.length;

        function moveToSlide(n) {
            slides.forEach((slide, index) => {
                slide.style.transform = `translateX(${-100 * n}%)`;
                if (n === index) {
                    slide.classList.add("active");
                } else {
                    slide.classList.remove("active");
                }
            });
            currentIndex = n;
        }

        // Function to go to the next slide
        function nextSlide() {
            if (currentIndex === totalSlides - 1) {
                moveToSlide(0); // Go to the first slide if we're at the last
            } else {
                moveToSlide(currentIndex + 1);
            }
        }

        // Function to go to the previous slide
        function prevSlide() {
            if (currentIndex === 0) {
                moveToSlide(totalSlides - 1); // Go to the last slide if we're at the first
            } else {
                moveToSlide(currentIndex - 1);
            }
        }

        // Example usage with buttons
        // Assuming you have buttons with classes `.next` and `.prev` for navigation
        const carouselNextButton = document.querySelector(".hero-slide-next");
        if (carouselNextButton) {
            carouselNextButton.addEventListener("click", nextSlide);
        }
        const carouselPrevButton = document.querySelector(".hero-slide-prev");
        if (carouselPrevButton) {
            carouselPrevButton.addEventListener("click", prevSlide);
        }

        // Initialize the slider
        moveToSlide(0);
    };

    /**
     * Generic attachment handler that can be used for different forms
     * @param {Object} options - Configuration options
     * @param {string} options.fileInputId - ID of the file input element (without #)
     * @param {string} options.previewsContainerId - ID of the container for previews (without #)
     * @param {string} options.listContainerId - ID of the container for the list view (without #)
     * @param {Array} options.allowedTypes - Array of allowed MIME type patterns (e.g. ['image/*', 'application/pdf'])
     * @param {number} options.maxFiles - Maximum number of files allowed (optional)
     * @param {boolean} options.showPreviews - Whether to show previews (default: true)
     * @param {string} options.previewClass - CSS class to apply to preview elements (default: 'attachment-preview')
     */
    const initAttachmentHandler = (options) => {
        // Default options
        const defaults = {
            showPreviews: true,
            previewClass: "attachment-preview",
            maxFiles: 10,
        };

        // Merge defaults with provided options
        const settings = { ...defaults, ...options };

        const fileInput = document.getElementById(settings.fileInputId);
        const previewsContainer = document.getElementById(
            settings.previewsContainerId
        );
        const attachmentsList = document.getElementById(
            settings.listContainerId
        );

        // Exit if the file input doesn't exist on this page
        if (!fileInput) {
            return;
        }

        // Get the PHP size limits - these values will be populated from PHP
        const maxPostSize = parsePhpSizeLimit(
            fileInput.dataset.maxPostSize || "20M"
        );
        const maxFileSize = parsePhpSizeLimit(
            fileInput.dataset.maxFileSize || "5M"
        );

        // Store selected files
        let selectedFiles = [];

        // Handle file selection
        fileInput.onchange = (ev) => {
            const files = ev.target.files;
            let totalSize = 0;

            // Calculate total size of already selected files
            selectedFiles.forEach((file) => {
                totalSize += file.file.size;
            });

            // Check each new file
            let exceedsLimit = false;
            let exceedsIndividualLimit = false;
            let oversizedFileName = "";
            let invalidTypeFound = false;
            let invalidFileName = "";

            for (let file of files) {
                // Check file type if allowed types are specified
                if (settings.allowedTypes && settings.allowedTypes.length > 0) {
                    const isAllowed = settings.allowedTypes.some((type) => {
                        if (type.endsWith("/*")) {
                            // Handle wildcard MIME types like 'image/*'
                            const mainType = type.split("/")[0];
                            return file.type.startsWith(mainType + "/");
                        } else {
                            // Exact MIME type match
                            return file.type === type;
                        }
                    });

                    if (!isAllowed) {
                        invalidTypeFound = true;
                        invalidFileName = file.name;
                        continue; // Skip this file
                    }
                }

                // Check individual file size
                if (file.size > maxFileSize) {
                    exceedsIndividualLimit = true;
                    oversizedFileName = file.name;
                    continue; // Skip this file
                }

                // Check total size
                if (totalSize + file.size > maxPostSize) {
                    exceedsLimit = true;
                    break;
                }

                // Check max number of files
                if (selectedFiles.length >= settings.maxFiles) {
                    alert(
                        `You can only upload a maximum of ${settings.maxFiles} files.`
                    );
                    break;
                }

                totalSize += file.size;

                // Create a unique identifier for the file
                const fileId = `file-${Date.now()}-${file.name.replace(
                    /[^a-zA-Z0-9]/g,
                    ""
                )}`;
                selectedFiles.push({
                    id: fileId,
                    file: file,
                });

                // Create preview if enabled
                if (settings.showPreviews && previewsContainer) {
                    readFile(file).then((url) => {
                        const previewElement = createAttachmentPreview(
                            file,
                            url,
                            fileId,
                            settings.previewClass
                        );
                        previewsContainer.append(previewElement);
                    });
                }
            }

            // Show appropriate warnings
            if (invalidTypeFound) {
                alert(
                    `The file "${invalidFileName}" is not of an allowed type. Please select a valid file type.`
                );
            }

            if (exceedsIndividualLimit) {
                alert(
                    `The file "${oversizedFileName}" exceeds the maximum individual file size limit of ${formatFileSize(
                        maxFileSize
                    )}. Please upload a smaller file.`
                );
            }

            if (exceedsLimit) {
                alert(
                    `The total size of all files (${formatFileSize(
                        totalSize
                    )}) exceeds the maximum allowed size of ${formatFileSize(
                        maxPostSize
                    )}. Some files were not added.`
                );
            }

            // Update the form with the current files
            updateFormFiles();
        };

        // Utility function to parse PHP size limits (like "8M" to bytes)
        function parsePhpSizeLimit(sizeStr) {
            if (!sizeStr) return 8 * 1024 * 1024; // Default to 8MB

            let size = parseInt(sizeStr);
            const unit = sizeStr.replace(/[0-9]/g, "").toUpperCase();

            switch (unit) {
                case "K":
                    return size * 1024;
                case "M":
                    return size * 1024 * 1024;
                case "G":
                    return size * 1024 * 1024 * 1024;
                default:
                    return size;
            }
        }

        // Utility function to format file size in a human-readable way
        function formatFileSize(bytes) {
            if (bytes < 1024) return bytes + " B";
            else if (bytes < 1048576) return (bytes / 1024).toFixed(1) + " KB";
            else if (bytes < 1073741824)
                return (bytes / 1048576).toFixed(1) + " MB";
            else return (bytes / 1073741824).toFixed(1) + " GB";
        }

        // Read file as data URL for previews
        function readFile(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    resolve(ev.target.result);
                };
                reader.onerror = (ev) => {
                    reject(ev);
                };
                reader.readAsDataURL(file);
            });
        }

        // Create a preview element for the file
        function createAttachmentPreview(file, url, fileId, previewClass) {
            const previewWrapper = document.createElement("div");
            previewWrapper.classList.add(previewClass);
            previewWrapper.dataset.fileid = fileId;
            previewWrapper.style.position = "relative";
            previewWrapper.style.marginRight = "10px";
            previewWrapper.style.marginBottom = "10px";
            previewWrapper.style.display = "inline-block";
            previewWrapper.style.maxWidth = "150px";

            // Add remove button
            const removeButton = document.createElement("div");
            removeButton.innerHTML = "×";
            removeButton.classList.add("remove-attachment");
            removeButton.style.position = "absolute";
            removeButton.style.top = "5px";
            removeButton.style.right = "5px";
            removeButton.style.backgroundColor = "rgba(255, 0, 0, 0.7)";
            removeButton.style.color = "white";
            removeButton.style.borderRadius = "50%";
            removeButton.style.width = "20px";
            removeButton.style.height = "20px";
            removeButton.style.textAlign = "center";
            removeButton.style.lineHeight = "20px";
            removeButton.style.cursor = "pointer";
            removeButton.style.zIndex = "10";
            removeButton.style.fontWeight = "bold";

            removeButton.addEventListener("click", function () {
                // Remove the file from selectedFiles
                selectedFiles = selectedFiles.filter(
                    (item) => item.id !== fileId
                );

                // Remove the preview element
                previewWrapper.remove();

                // Update the form with the current files
                updateFormFiles();
            });

            previewWrapper.appendChild(removeButton);

            // Create appropriate preview based on file type
            if (file.type.startsWith("image/")) {
                const img = document.createElement("img");
                img.src = url;
                img.style.maxWidth = "100%";
                img.style.height = "auto";
                img.style.borderRadius = "4px";
                previewWrapper.appendChild(img);
            } else if (file.type.startsWith("video/")) {
                const video = document.createElement("video");
                video.src = url;
                video.controls = true;
                video.style.maxWidth = "100%";
                video.style.height = "auto";
                previewWrapper.appendChild(video);
            } else {
                // For other file types, show an appropriate icon
                const iconContainer = document.createElement("div");
                iconContainer.style.width = "100px";
                iconContainer.style.height = "100px";
                iconContainer.style.display = "flex";
                iconContainer.style.alignItems = "center";
                iconContainer.style.justifyContent = "center";
                iconContainer.style.backgroundColor = "#f8f9fa";
                iconContainer.style.borderRadius = "4px";

                iconContainer.innerHTML = getFileIcon(file);

                previewWrapper.appendChild(iconContainer);

                // Add filename below the icon
                const fileNameElem = document.createElement("div");
                fileNameElem.textContent =
                    file.name.length > 15
                        ? file.name.substring(0, 12) + "..."
                        : file.name;
                fileNameElem.style.fontSize = "12px";
                fileNameElem.style.marginTop = "5px";
                fileNameElem.style.textAlign = "center";
                fileNameElem.style.wordBreak = "break-word";
                fileNameElem.title = file.name;

                previewWrapper.appendChild(fileNameElem);
            }

            return previewWrapper;
        }

        // Get appropriate icon HTML based on file type
        function getFileIcon(file) {
            const mimeType = file.type;

            if (mimeType === "application/pdf") {
                return '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="red" viewBox="0 0 16 16"><path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/><path d="M4.603 14.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68 7.68 0 0 1 1.482-.645 19.697 19.697 0 0 0 1.062-2.227 7.269 7.269 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.188-.012.396-.047.614-.084.51-.27 1.134-.52 1.794a10.954 10.954 0 0 0 .98 1.686 5.753 5.753 0 0 1 1.334.05c.364.066.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.856.856 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.712 5.712 0 0 1-.911-.95 11.651 11.651 0 0 0-1.997.406 11.307 11.307 0 0 1-1.02 1.51c-.292.35-.609.656-.927.787a.793.793 0 0 1-.58.029z"/></svg>';
            } else if (
                mimeType === "application/msword" ||
                mimeType ===
                    "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
            ) {
                return '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="blue" viewBox="0 0 16 16"><path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/><path d="M4.5 12.5A.5.5 0 0 1 5 12h3a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5zm0-2A.5.5 0 0 1 5 10h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5zm0-2A.5.5 0 0 1 5 8h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5zm0-2A.5.5 0 0 1 5 6h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5z"/></svg>';
            } else if (mimeType === "text/plain") {
                return '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="gray" viewBox="0 0 16 16"><path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/><path d="M4.5 8a.5.5 0 0 1 0-1h7a.5.5 0 0 1 0 1h-7zm0 2a.5.5 0 0 1 0-1h7a.5.5 0 0 1 0 1h-7zm0 2a.5.5 0 0 1 0-1h4a.5.5 0 0 1 0 1h-4z"/></svg>';
            } else {
                return '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" viewBox="0 0 16 16"><path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/></svg>';
            }
        }

        // Update the form with the current files
        function updateFormFiles() {
            // Update the file list display if it exists
            if (attachmentsList) {
                // Clear the attachments list
                attachmentsList.innerHTML = "";

                // Add a heading if there are files
                if (selectedFiles.length > 0) {
                    const fileListHeader = document.createElement("h4");
                    fileListHeader.textContent = "Selected Files:";
                    attachmentsList.appendChild(fileListHeader);
                }

                // Create a list of selected files
                const fileList = document.createElement("ul");
                fileList.style.listStyleType = "none";
                fileList.style.padding = "0";

                selectedFiles.forEach((fileInfo, index) => {
                    const listItem = document.createElement("li");
                    listItem.style.margin = "5px 0";
                    listItem.style.display = "flex";
                    listItem.style.alignItems = "center";

                    // Add file icon
                    const fileIconSpan = document.createElement("span");
                    fileIconSpan.innerHTML = getFileIcon(fileInfo.file);
                    fileIconSpan.style.marginRight = "10px";
                    fileIconSpan.querySelector("svg").style.width = "20px";
                    fileIconSpan.querySelector("svg").style.height = "20px";

                    // Add file name and size
                    const fileDetails = document.createElement("span");
                    fileDetails.textContent = `${
                        fileInfo.file.name
                    } (${formatFileSize(fileInfo.file.size)})`;

                    // Add remove button
                    const removeBtn = document.createElement("button");
                    removeBtn.type = "button";
                    removeBtn.innerHTML = "&times;";
                    removeBtn.style.marginLeft = "auto";
                    removeBtn.style.backgroundColor = "#dc3545";
                    removeBtn.style.color = "white";
                    removeBtn.style.border = "none";
                    removeBtn.style.borderRadius = "50%";
                    removeBtn.style.width = "20px";
                    removeBtn.style.height = "20px";
                    removeBtn.style.cursor = "pointer";
                    removeBtn.style.display = "flex";
                    removeBtn.style.alignItems = "center";
                    removeBtn.style.justifyContent = "center";
                    removeBtn.dataset.fileId = fileInfo.id;

                    removeBtn.addEventListener("click", function () {
                        // Remove from selected files
                        selectedFiles = selectedFiles.filter(
                            (item) => item.id !== fileInfo.id
                        );

                        // Remove preview if exists
                        if (previewsContainer) {
                            const preview = previewsContainer.querySelector(
                                `[data-fileid="${fileInfo.id}"]`
                            );
                            if (preview) preview.remove();
                        }

                        // Update form
                        updateFormFiles();
                    });

                    listItem.appendChild(fileIconSpan);
                    listItem.appendChild(fileDetails);
                    listItem.appendChild(removeBtn);
                    fileList.appendChild(listItem);
                });

                attachmentsList.appendChild(fileList);
            }

            // Create a new DataTransfer object and add the files
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach((fileInfo) => {
                dataTransfer.items.add(fileInfo.file);
            });

            // Update the file input with our curated list of files
            fileInput.files = dataTransfer.files;
        }
    };

    const initMobileNavbar = () => {
        const btnToggle = document.querySelector(".btn-navbar-toggle");

        btnToggle.onclick = () => {
            document.body.classList.toggle("navbar-opened");
        };
    };

    const attachmentCarousel = () => {
        const carousel = document.querySelector(
            ".listing-attachments-carousel"
        );
        if (!carousel) {
            return;
        }

        const thumbnails = document.querySelectorAll(
            ".listing-attachment-thumbnails img, .listing-attachment-thumbnails video"
        );
        const activeAttachmentContainer = document.querySelector(
            ".listing-attachment-wrapper"
        );

        const prevButton = document.getElementById("prevButton");
        const nextButton = document.getElementById("nextButton");

        // Exit early if necessary elements don't exist
        if (!activeAttachmentContainer || thumbnails.length === 0) {
            return;
        }

        let currentIndex = 0;

        // Initialize active thumbnail class
        thumbnails.forEach((thumbnail, index) => {
            const activeAttachment = activeAttachmentContainer.querySelector(
                ".listing-active-attachment"
            );
            if (activeAttachment && thumbnail.src === activeAttachment.src) {
                thumbnail.classList.add("active-thumbnail");
                currentIndex = index;
            }
        });

        // Function to update the active attachment and thumbnail
        const updateActiveAttachment = (index) => {
            const thumbnail = thumbnails[index];
            const mimeType = thumbnail.getAttribute("data-mime-type");
            const attachmentUrl = thumbnail.getAttribute("src");

            activeAttachmentContainer.innerHTML = ""; // Clear the current active attachment

            if (mimeType.startsWith("image/")) {
                const img = document.createElement("img");
                img.src = attachmentUrl;
                img.alt = "";
                img.classList.add("listing-active-attachment");
                img.id = "activeAttachment";
                activeAttachmentContainer.appendChild(img);
            } else if (mimeType.startsWith("video/")) {
                const video = document.createElement("video");
                video.src = attachmentUrl;
                video.classList.add("listing-active-attachment");
                video.id = "activeAttachment";
                video.controls = true;
                activeAttachmentContainer.appendChild(video);
            } else {
                const img = document.createElement("img");
                img.src = "/img/no-photo-available.jpg";
                img.alt = "";
                img.classList.add("listing-active-attachment");
                img.id = "activeAttachment";
                activeAttachmentContainer.appendChild(img);
            }

            thumbnails.forEach((thumbnail) =>
                thumbnail.classList.remove("active-thumbnail")
            );
            thumbnails[index].classList.add("active-thumbnail");
        };

        // Add click event listeners to thumbnails
        thumbnails.forEach((thumbnail, index) => {
            thumbnail.addEventListener("click", () => {
                currentIndex = index;
                updateActiveAttachment(currentIndex);
            });
        });

        // Add click event listener to the previous button (only if it exists)
        if (prevButton) {
            prevButton.addEventListener("click", () => {
                currentIndex =
                    (currentIndex - 1 + thumbnails.length) % thumbnails.length;
                updateActiveAttachment(currentIndex);
            });
        }

        // Add click event listener to the next button (only if it exists)
        if (nextButton) {
            nextButton.addEventListener("click", () => {
                currentIndex = (currentIndex + 1) % thumbnails.length;
                updateActiveAttachment(currentIndex);
            });
        }
    };

    const initMobileFilters = () => {
        const filterButton = document.querySelector(".show-filters-button");
        const sidebar = document.querySelector(".search-listings-sidebar");
        const closeButton = document.querySelector(".close-filters-button");

        if (!filterButton) return;

        console.log(filterButton.classList);
        filterButton.addEventListener("click", () => {
            if (sidebar.classList.contains("opened")) {
                sidebar.classList.remove("opened");
            } else {
                sidebar.classList.add("opened");
            }
        });

        if (closeButton) {
            closeButton.addEventListener("click", () => {
                sidebar.classList.remove("opened");
            });
        }
    };

    const initCascadingDropdown = (parentSelector, childSelector) => {
        const parentDropdown = document.querySelector(parentSelector);
        const childDropdown = document.querySelector(childSelector);

        if (!parentDropdown || !childDropdown) return;

        hideModelOptions(parentDropdown.value);

        parentDropdown.addEventListener("change", (ev) => {
            hideModelOptions(ev.target.value);
            childDropdown.value = "";
        });

        function hideModelOptions(parentValue) {
            const models = childDropdown.querySelectorAll("option");
            models.forEach((model) => {
                if (
                    model.dataset.parent === parentValue ||
                    model.value === ""
                ) {
                    model.style.display = "block";
                } else {
                    model.style.display = "none";
                }
            });
        }
    };

    const initSortingDropdown = () => {
        const sortingDropdown = document.querySelector(".sort-dropdown");
        if (!sortingDropdown) return;

        // Init sorting dropdown with the current value
        const url = new URL(window.location.href);
        const sortValue = url.searchParams.get("sort");
        if (sortValue) {
            sortingDropdown.value = sortValue;
        }

        sortingDropdown.addEventListener("change", (ev) => {
            const url = new URL(window.location.href);
            url.searchParams.set("sort", ev.target.value);
            window.location.href = url.toString();
        });
    };

    const initAddToWatchlist = () => {
        const buttons = document.querySelectorAll(".btn-heart");

        if (!buttons) return;

        buttons.forEach((button) => {
            button.addEventListener("click", (ev) => {
                const button = ev.currentTarget;
                const url = button.dataset.url;
                axios
                    .post(url)
                    .then((response) => {
                        const toShow = button.querySelector("svg.hidden");
                        const toHide = button.querySelector("svg:not(.hidden)");

                        toShow.classList.remove("hidden");
                        toHide.classList.add("hidden");
                        alert(response.data.message);
                    })
                    .catch((error) => {
                        alert("Internal Error: " + error.message);
                    });
            });
        });
    };

    const initShowPhoneNumber = () => {
        const viewButton = document.querySelector(
            ".listing-details-phone-view"
        );
        if (!viewButton) {
            console.log("View button not found on page");
            return;
        }

        console.log("Phone view button found:", viewButton);

        viewButton.addEventListener("click", (ev) => {
            console.log("View button clicked!");
            ev.preventDefault();

            // Show a loading state
            const phoneElement = document.getElementById("phone-number");
            if (!phoneElement) {
                console.error("Phone element not found");
                return;
            }

            console.log("Phone element found:", phoneElement);

            const originalText = phoneElement.textContent;
            console.log("Original phone text:", originalText);
            phoneElement.textContent = "Loading...";

            // Get the URL for the Axios request
            const url = viewButton.getAttribute("data-url");
            console.log("Request URL:", url);

            if (!url) {
                console.error("No URL provided for phone number");
                phoneElement.textContent = originalText;
                return;
            }

            console.log("Testing direct API call");
            const testUrl = viewButton.getAttribute("data-url");
            if (testUrl) {
                axios
                    .get(testUrl)
                    .then((response) =>
                        console.log("Test API call succeeded:", response.data)
                    )
                    .catch((error) =>
                        console.error("Test API call failed:", error)
                    );
            }

            // Fetch the phone number via Axios
            console.log("Making Axios request to:", url);
            axios
                .get(url)
                .then((response) => {
                    console.log("Axios response:", response);
                    if (response.data && response.data.phone) {
                        // Update the phone number element
                        console.log(
                            "Phone number received:",
                            response.data.phone
                        );
                        phoneElement.textContent = response.data.phone;

                        // Hide the view button
                        viewButton.style.display = "none";
                    } else {
                        // If no phone number was returned
                        console.error(
                            "Response missing phone data:",
                            response.data
                        );
                        phoneElement.textContent = originalText;
                    }
                })
                .catch((error) => {
                    console.error("Axios error:", error);
                    // Restore original text on error
                    phoneElement.textContent = originalText;
                });
        });
    };

    initSlider();
    // For listing attachments
    initAttachmentHandler({
        fileInputId: "listingFormAttachmentUpload",
        previewsContainerId: "attachmentPreviews",
        listContainerId: "attachmentsList",
        allowedTypes: ["image/*", "video/*"],
        showPreviews: true,
        previewClass: "listing-form-attachment-preview",
    });
    // For email attachments
    initAttachmentHandler({
        fileInputId: "emailFormAttachmentUpload",
        previewsContainerId: "attachmentPreviews",
        listContainerId: "attachmentsList",
        allowedTypes: [
            "image/*",
            "video/*",
            "application/pdf",
            "application/msword",
            "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
            "text/plain",
        ],
        showPreviews: true,
        previewClass: "email-attachment-preview",
    });
    // For quote attachments
    initAttachmentHandler({
        fileInputId: "quoteFormAttachmentUpload",
        previewsContainerId: "attachmentPreviews",
        listContainerId: "attachmentsList",
        allowedTypes: [
            "image/*",
            "video/*",
            "application/pdf",
            "application/msword",
            "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
            "text/plain",
        ],
        showPreviews: true,
        previewClass: "quote-form-attachment-preview",
    });
    // For order attachments
    initAttachmentHandler({
        fileInputId: "orderFormAttachmentUpload",
        previewsContainerId: "attachmentPreviews",
        listContainerId: "attachmentsList",
        allowedTypes: [
            "image/*",
            "video/*",
            "application/pdf",
            "application/msword",
            "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
            "text/plain",
        ],
        showPreviews: true,
        previewClass: "order-form-attachment-preview",
    });
    initMobileNavbar();
    attachmentCarousel();
    initMobileFilters();
    initCascadingDropdown("#makerSelect", "#modelSelect");
    initCascadingDropdown("#stateSelect", "#citySelect");
    initSortingDropdown();
    initAddToWatchlist();
    initShowPhoneNumber();

    ScrollReveal().reveal(".hero-slide.active .hero-slider-title", {
        delay: 200,
        reset: true,
    });
    ScrollReveal().reveal(".hero-slide.active .hero-slider-content", {
        delay: 200,
        origin: "bottom",
        distance: "50%",
    });
});

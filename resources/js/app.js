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

    const initAttachmentPicker = () => {
        const fileInput = document.querySelector(
            "#listingFormAttachmentUpload"
        );
        const attachmentPreview = document.querySelector("#attachmentPreviews");
        const attachmentsList = document.querySelector("#attachmentsList");

        if (!fileInput) {
            return;
        }

        // Get the PHP size limits from the page
        const maxPostSize = parsePhpSizeLimit(
            "<?php echo ini_get('post_max_size'); ?>"
        );
        const maxFileSize = parsePhpSizeLimit(
            "<?php echo ini_get('upload_max_filesize'); ?>"
        );

        // Store selected files
        let selectedFiles = [];

        fileInput.onchange = (ev) => {
            const files = ev.target.files;
            let totalSize = 0;

            // Calculate total size of selected files
            selectedFiles.forEach((file) => {
                totalSize += file.file.size;
            });

            // Check each new file
            let exceedsLimit = false;
            let exceedsIndividualLimit = false;
            let oversizedFileName = "";

            for (let file of files) {
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

                readFile(file).then((url) => {
                    const previewElement = createAttachmentPreview(
                        file,
                        url,
                        fileId
                    );
                    attachmentPreview.append(previewElement);
                });
            }

            // Show warning if exceeds limit
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

        function parsePhpSizeLimit(sizeStr) {
            // Convert PHP size strings like "8M" to bytes
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

        function formatFileSize(bytes) {
            if (bytes < 1024) return bytes + " B";
            else if (bytes < 1048576) return (bytes / 1024).toFixed(1) + " KB";
            else if (bytes < 1073741824)
                return (bytes / 1048576).toFixed(1) + " MB";
            else return (bytes / 1073741824).toFixed(1) + " GB";
        }

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

        function createAttachmentPreview(file, url, fileId) {
            const previewWrapper = document.createElement("div");
            previewWrapper.classList.add("listing-form-attachment-preview");
            previewWrapper.dataset.fileid = fileId;
            previewWrapper.style.position = "relative";

            // Add remove button
            const removeButton = document.createElement("div");
            removeButton.innerHTML = "×";
            removeButton.classList.add("remove-attachment");
            removeButton.style.position = "absolute";
            removeButton.style.top = "5px";
            removeButton.style.right = "5px";
            removeButton.style.backgroundColor = "rgba(255, 255, 255, 0.7)";
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

            if (file.type.startsWith("image/")) {
                const img = document.createElement("img");
                img.src = url;
                img.style.maxWidth = "100%";
                img.style.height = "auto";
                previewWrapper.appendChild(img);
            } else if (file.type.startsWith("video/")) {
                const video = document.createElement("video");
                video.src = url;
                video.controls = true;
                video.style.maxWidth = "100px"; // Adjust the size as needed
                previewWrapper.appendChild(video);
            }

            return previewWrapper;
        }

        function updateFormFiles() {
            if (!attachmentsList) return;

            // Clear the attachments list
            attachmentsList.innerHTML = "";

            // Create a new FileList-like object with our selected files
            const fileListItems = selectedFiles.map(
                (fileInfo) => fileInfo.file
            );

            // Show the current files in the attachments list
            selectedFiles.forEach((fileInfo) => {
                const fileItem = document.createElement("div");
                fileItem.textContent = fileInfo.file.name;
                attachmentsList.appendChild(fileItem);
            });

            // Create a new DataTransfer object and add the files
            const dataTransfer = new DataTransfer();
            fileListItems.forEach((file) => {
                dataTransfer.items.add(file);
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

        let currentIndex = 0;

        // Initialize active thumbnail class
        thumbnails.forEach((thumbnail, index) => {
            if (
                thumbnail.src ===
                activeAttachmentContainer.querySelector(
                    ".listing-active-attachment"
                ).src
            ) {
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

        // Add click event listener to the previous button
        prevButton.addEventListener("click", () => {
            currentIndex =
                (currentIndex - 1 + thumbnails.length) % thumbnails.length;
            updateActiveAttachment(currentIndex);
        });

        // Add click event listener to the next button
        nextButton.addEventListener("click", () => {
            currentIndex = (currentIndex + 1) % thumbnails.length;
            updateActiveAttachment(currentIndex);
        });
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

    initSlider();
    initAttachmentPicker();
    initMobileNavbar();
    attachmentCarousel();
    initMobileFilters();
    initCascadingDropdown("#makerSelect", "#modelSelect");
    initCascadingDropdown("#stateSelect", "#citySelect");
    initSortingDropdown();
    initAddToWatchlist();

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

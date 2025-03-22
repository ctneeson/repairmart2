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
        if (!fileInput) {
            return;
        }
        fileInput.onchange = (ev) => {
            attachmentPreview.innerHTML = "";
            const files = ev.target.files;
            for (let file of files) {
                readFile(file).then((url) => {
                    const previewElement = createAttachmentPreview(file, url);
                    attachmentPreview.append(previewElement);
                });
            }
        };

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

        function createAttachmentPreview(file, url) {
            const previewWrapper = document.createElement("div");
            previewWrapper.classList.add("listing-form-attachment-preview");

            if (file.type.startsWith("image/")) {
                const img = document.createElement("img");
                img.src = url;
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

    initSlider();
    initAttachmentPicker();
    initMobileNavbar();
    attachmentCarousel();
    initMobileFilters();
    initCascadingDropdown("#makerSelect", "#modelSelect");
    initCascadingDropdown("#stateSelect", "#citySelect");
    initSortingDropdown();

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

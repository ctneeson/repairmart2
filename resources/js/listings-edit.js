// Handle attachment previews
document.addEventListener("DOMContentLoaded", function () {
    // Find all attachment previews
    const attachmentPreviews = document.querySelectorAll(
        ".listing-form-attachment-preview"
    );

    attachmentPreviews.forEach((preview) => {
        const video = preview.querySelector("video");

        // If this preview contains a video, add the video-preview class
        if (video) {
            preview.classList.add("video-preview");

            // Add poster if the video hasn't loaded or can't be played
            video.addEventListener("error", function () {
                this.poster = "/images/video-placeholder.png"; // Add a fallback video thumbnail
            });

            // Set poster frame for the video
            video.addEventListener("loadeddata", function () {
                if (video.readyState >= 2) {
                    // Jump to 1 second in to get a representative frame
                    video.currentTime = 1.0;
                    // Try to play briefly to get a good thumbnail frame
                    video
                        .play()
                        .then(() => {
                            setTimeout(() => {
                                video.pause();
                            }, 100);
                        })
                        .catch(() => {
                            // If autoplay is blocked, just use the first frame
                            video.currentTime = 0;
                        });
                }
            });

            // Handle click to play/pause
            preview.addEventListener("click", function (e) {
                e.preventDefault();
                e.stopPropagation();

                if (video.paused) {
                    video.play().catch((err) => {
                        console.log("Error playing video:", err);
                    });
                } else {
                    video.pause();
                }
            });
        }
    });

    // If we need to access the initialSelectedProducts data
    if (window.initialSelectedProducts) {
        // Your product handling code here
        console.log("Selected products:", window.initialSelectedProducts);
    }
});

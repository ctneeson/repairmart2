document.addEventListener("DOMContentLoaded", function () {
    // Function to get today's date in YYYY-MM-DD format
    function getTodayFormatted() {
        const today = new Date();
        const year = today.getFullYear();
        const month = (today.getMonth() + 1).toString().padStart(2, "0");
        const day = today.getDate().toString().padStart(2, "0");
        return `${year}-${month}-${day}`;
    }

    // Set minimum date for published_at input
    function updatePublishedAtMinDate() {
        const publishedAtInput = document.getElementById("published_at");
        if (publishedAtInput) {
            const todayFormatted = getTodayFormatted();
            publishedAtInput.min = todayFormatted;

            // If current value is before today, update it to today
            if (
                publishedAtInput.value &&
                publishedAtInput.value < todayFormatted
            ) {
                publishedAtInput.value = todayFormatted;
            }
        }
    }

    // Initialize date picker min attribute
    updatePublishedAtMinDate();

    // Get the reset button
    const resetButton = document.querySelector(".btn-default:nth-of-type(2)");

    if (resetButton) {
        resetButton.addEventListener("click", function (e) {
            e.preventDefault();

            // Show confirmation dialog
            if (
                !confirm(
                    "Are you sure you want to reset the form? All entered information will be cleared."
                )
            ) {
                return; // Stop if user cancels
            }

            // Reset basic input fields
            document.querySelector('input[name="title"]').value = "";
            document.querySelector('textarea[name="description"]').value = "";
            document.querySelector('input[name="budget"]').value = "";

            // Reset published_at to today
            const publishedAtInput = document.querySelector(
                'input[name="published_at"]'
            );
            if (publishedAtInput) {
                publishedAtInput.value = getTodayFormatted();
            }

            // Reset select elements
            document.querySelector(
                'select[name="expiry_days"]'
            ).selectedIndex = 0;

            // Reset currency selector
            if (document.querySelector('select[name="currency_id"]')) {
                document.querySelector(
                    'select[name="currency_id"]'
                ).selectedIndex = 0;
            }

            // Reset manufacturer
            if (document.querySelector('select[name="manufacturer_id"]')) {
                document.querySelector(
                    'select[name="manufacturer_id"]'
                ).selectedIndex = 0;
            }

            // Reset product selections - call the global function
            if (typeof window.resetProductSelections === "function") {
                window.resetProductSelections();
            }

            // Reset attachments
            const fileInput = document.getElementById(
                "listingFormAttachmentUpload"
            );
            if (fileInput) {
                fileInput.value = "";

                // Clear DataTransfer object
                const dataTransfer = new DataTransfer();
                fileInput.files = dataTransfer.files;

                // Clear previews
                const previewsContainer =
                    document.getElementById("attachmentPreviews");
                if (previewsContainer) {
                    previewsContainer.innerHTML = "";
                }

                // Clear list
                const attachmentsList =
                    document.getElementById("attachmentsList");
                if (attachmentsList) {
                    attachmentsList.innerHTML = "";
                }
            }

            // Handle address fields based on checkbox state
            const useDefaultLocation = document.getElementById(
                "use-default-location"
            );
            if (useDefaultLocation && !useDefaultLocation.checked) {
                // Only clear address fields if default location is not being used
                document.querySelector('input[name="address_line1"]').value =
                    "";
                document.querySelector('input[name="address_line2"]').value =
                    "";
                document.querySelector('input[name="city"]').value = "";
                document.querySelector('input[name="postcode"]').value = "";
                document.querySelector('input[name="phone"]').value = "";

                // Reset country selector
                if (document.querySelector('select[name="country_id"]')) {
                    document.querySelector(
                        'select[name="country_id"]'
                    ).selectedIndex = 0;
                }
            }
        });
    }

    // Add change listener to the published_at input to prevent manual entry of past dates
    const publishedAtInput = document.getElementById("published_at");
    if (publishedAtInput) {
        publishedAtInput.addEventListener("change", function () {
            const todayFormatted = getTodayFormatted();
            if (this.value < todayFormatted) {
                this.value = todayFormatted;
                alert(
                    "You cannot select a date in the past. The date has been reset to today."
                );
            }
        });
    }
});

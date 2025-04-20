import { initAddressHandling } from "./quote-address-handling";

// Form reset functionality only needed on create page
function initQuoteFormReset(userData) {
    const resetButton = document.getElementById("reset-button");
    if (!resetButton) return;

    resetButton.addEventListener("click", function (e) {
        e.preventDefault();

        if (
            !confirm(
                "Are you sure you want to reset the form? All entered information will be cleared."
            )
        ) {
            return;
        }

        // Reset form fields
        const amountField = document.getElementById("amount");
        if (amountField) amountField.value = "";

        const turnaroundField = document.getElementById("turnaround");
        if (turnaroundField) turnaroundField.value = "";

        const deliveryMethodField =
            document.getElementById("deliverymethod_id");
        if (deliveryMethodField) deliveryMethodField.selectedIndex = 0;

        const descriptionField = document.getElementById("description");
        if (descriptionField) descriptionField.value = "";

        // Reset the currency selector
        const currencySelect = document.querySelector(
            'select[name="currency_id"]'
        );
        if (currencySelect) {
            currencySelect.selectedIndex = 0;
        }

        // Reset default location to checked
        const useDefaultLocationCheckbox = document.getElementById(
            "use-default-location"
        );
        if (useDefaultLocationCheckbox) {
            useDefaultLocationCheckbox.checked = true;
            // Trigger the change event to update UI
            const event = new Event("change");
            useDefaultLocationCheckbox.dispatchEvent(event);
        }

        // Only reset address fields if userData is provided
        if (userData) {
            // Reset address to user defaults
            const addressLine1Field = document.getElementById("address_line1");
            if (addressLine1Field)
                addressLine1Field.value = userData.address_line1 || "";

            const addressLine2Field = document.getElementById("address_line2");
            if (addressLine2Field)
                addressLine2Field.value = userData.address_line2 || "";

            const cityField = document.getElementById("city");
            if (cityField) cityField.value = userData.city || "";

            const postcodeField = document.getElementById("postcode");
            if (postcodeField) postcodeField.value = userData.postcode || "";

            const phoneField = document.getElementById("phone");
            if (phoneField) phoneField.value = userData.phone || "";

            // Reset country
            const visibleCountryId =
                document.getElementById("visible_country_id");
            if (visibleCountryId && userData.country_id) {
                const userCountryOption = visibleCountryId.querySelector(
                    `option[value="${userData.country_id}"]`
                );
                if (userCountryOption) {
                    userCountryOption.selected = true;
                }
            }

            const hiddenCountryId =
                document.getElementById("hidden_country_id");
            if (hiddenCountryId && userData.country_id) {
                hiddenCountryId.value = userData.country_id;
            }
        }

        // Reset attachments
        const fileInput = document.getElementById("quoteFormAttachmentUpload");
        if (fileInput) {
            fileInput.value = "";

            // Clear previews
            const previewsContainer =
                document.getElementById("attachmentPreviews");
            if (previewsContainer) {
                previewsContainer.innerHTML = "";
            }

            // Clear list
            const attachmentsList = document.getElementById("attachmentsList");
            if (attachmentsList) {
                attachmentsList.innerHTML = "";
            }
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    // Initialize address handling functionality
    initAddressHandling();

    // Get user data from the page if available
    let userData = null;
    const userDataElement = document.getElementById("user-data");
    if (userDataElement) {
        try {
            userData = JSON.parse(userDataElement.dataset.user);
        } catch (e) {
            console.error("Error parsing user data:", e);
        }
    }

    // Initialize form reset functionality - only for create page
    initQuoteFormReset(userData);
});

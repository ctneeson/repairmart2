document.addEventListener("DOMContentLoaded", function () {
    const useDefaultLocationCheckbox = document.getElementById(
        "use-default-location"
    );
    const addressFieldsContainer = document.getElementById("address-fields");
    const countrySelect = document.getElementById("countrySelect");

    // Exit if required elements don't exist
    if (!useDefaultLocationCheckbox || !addressFieldsContainer) {
        console.error("Required elements not found for address toggle");
        return;
    }

    // Function to toggle address fields
    function toggleAddressFields() {
        const useDefault = useDefaultLocationCheckbox.checked;
        console.log("Toggle address fields - Using default:", useDefault);

        // Toggle visual indication
        if (useDefault) {
            addressFieldsContainer.classList.add("fields-readonly");
        } else {
            addressFieldsContainer.classList.remove("fields-readonly");
        }

        // Get all input fields in the address section (excluding hidden and checkbox inputs)
        const addressInputs = addressFieldsContainer.querySelectorAll(
            "input:not([type=hidden]):not([type=checkbox])"
        );

        // Toggle each field and set appropriate values
        addressInputs.forEach((field) => {
            field.readOnly = useDefault;
            field.style.backgroundColor = useDefault ? "#f5f5f5" : "";
            field.style.color = useDefault ? "#666666" : "";

            // IMPORTANT: When using default address, always reset to user's default values
            // regardless of what was manually entered
            if (useDefault) {
                // Use the user's default value from data-user attribute
                // If data-user is empty, the field should be empty too
                field.value = field.dataset.user || "";
                console.log(
                    `Reset ${field.id} to user default:`,
                    field.dataset.user || "(empty)"
                );
            } else if (!useDefault && field.dataset.original) {
                // When not using default, restore to the original listing value
                field.value = field.dataset.original || "";
            }
        });

        // Handle the country dropdown
        if (countrySelect) {
            // 1. Toggle disabled state
            countrySelect.disabled = useDefault;

            // 2. Apply visual styling (the CSS already handles this, but we'll be explicit)
            countrySelect.style.backgroundColor = useDefault ? "#f5f5f5" : "";
            countrySelect.style.color = useDefault ? "#666666" : "";

            // 3. Set the correct country value
            if (useDefault && countrySelect.dataset.user) {
                // Find and select the user's default country
                const userCountryId = countrySelect.dataset.user;
                for (let i = 0; i < countrySelect.options.length; i++) {
                    if (countrySelect.options[i].value == userCountryId) {
                        countrySelect.selectedIndex = i;
                        break;
                    }
                }
            } else if (!useDefault && countrySelect.dataset.original) {
                // Restore original listing country
                for (let i = 0; i < countrySelect.options.length; i++) {
                    if (
                        countrySelect.options[i].value ==
                        countrySelect.dataset.original
                    ) {
                        countrySelect.selectedIndex = i;
                        break;
                    }
                }
            }
        }

        // Update hidden country field for form submission if necessary
        const hiddenCountryId = document.getElementById("hidden_country_id");
        if (hiddenCountryId && countrySelect) {
            if (useDefault) {
                // When using default location, use the user's default country ID
                hiddenCountryId.value = countrySelect.dataset.user || "";
            } else {
                // When not using default, use whatever is selected in the dropdown
                hiddenCountryId.value = countrySelect.value;
            }
        }
    }

    // Set initial state
    toggleAddressFields();

    // Add event listener for checkbox changes
    useDefaultLocationCheckbox.addEventListener("change", toggleAddressFields);

    // Add logging for debugging
    useDefaultLocationCheckbox.addEventListener("change", function () {
        console.log("Checkbox changed to:", this.checked);
    });
});

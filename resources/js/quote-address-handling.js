/**
 * Quote form address handling
 * Manages the switching between default address and manual address entry
 */
export function initAddressHandling() {
    const useDefaultLocationCheckbox = document.getElementById(
        "use-default-location"
    );
    const addressFields = document.getElementById("address-fields");

    // Exit if required elements don't exist
    if (!useDefaultLocationCheckbox || !addressFields) return;

    const addressInputs = addressFields.querySelectorAll("input, select");
    const hiddenCountryId = document.getElementById("hidden_country_id");
    const visibleCountryId = document.getElementById("visible_country_id");

    // Add event listener for checkbox changes
    useDefaultLocationCheckbox.addEventListener(
        "change",
        updateAddressFieldsState
    );

    function updateAddressFieldsState() {
        const useDefault = useDefaultLocationCheckbox.checked;

        // Update the visual appearance
        if (useDefault) {
            addressFields.classList.add("opacity-50");
        } else {
            addressFields.classList.remove("opacity-50");
        }

        // Handle the country ID fields specifically
        if (useDefault && hiddenCountryId && visibleCountryId) {
            // When using default address, enable the hidden field and disable the visible dropdown
            hiddenCountryId.disabled = false;
            visibleCountryId.name = "_country_id"; // Change the name so it's not submitted
            visibleCountryId.disabled = true;
        } else if (hiddenCountryId && visibleCountryId) {
            // When manually entering address, disable the hidden field and enable the visible dropdown
            hiddenCountryId.disabled = true;
            visibleCountryId.name = "country_id"; // Set the proper name for submission
            visibleCountryId.disabled = false;
        }

        // Update each input's readonly status
        addressInputs.forEach((input) => {
            if (
                input.id !== "hidden_country_id" &&
                input.id !== "visible_country_id"
            ) {
                input.readOnly = useDefault;
            }
        });
    }

    // Run the function once on page load to set initial state
    updateAddressFieldsState();
}

document.addEventListener("DOMContentLoaded", function () {
    // Debug: Check if elements exist
    console.log(
        "Default location checkbox:",
        document.getElementById("use-default-location")
    );
    console.log("Country Select:", document.getElementById("countrySelect"));
    console.log(
        "Hidden Country ID:",
        document.getElementById("hidden_country_id")
    );

    const useDefaultLocationCheckbox = document.getElementById(
        "use-default-location"
    );
    const addressFields = document.querySelectorAll(
        "#address-fields input:not([type=hidden]), #address-fields select"
    );
    const addressFieldsContainer = document.getElementById("address-fields");

    // Look for both possible IDs
    const countrySelect =
        document.getElementById("countrySelect") ||
        document.getElementById("country_id");
    const hiddenCountryId = document.getElementById("hidden_country_id");

    // User data for default address with PHP variables properly processed
    const userData = {
        address_line1: "{{ auth()->user()->address_line1 ?? '' }}",
        address_line2: "{{ auth()->user()->address_line2 ?? '' }}",
        city: "{{ auth()->user()->city ?? '' }}",
        postcode: "{{ auth()->user()->postcode ?? '' }}",
        phone: "{{ auth()->user()->phone ?? '' }}",
        country_id: "{{ auth()->user()->country_id ?? '' }}",
    };

    // Function to toggle address fields
    function toggleAddressFields() {
        // Debug: Log current state
        console.log(
            "Toggle called, checkbox checked:",
            useDefaultLocationCheckbox.checked
        );
        console.log("Country select element:", countrySelect);

        const useDefaultLocation = useDefaultLocationCheckbox.checked;

        // Toggle visual appearance
        if (useDefaultLocation) {
            addressFieldsContainer.classList.add("disabled-fields");
        } else {
            addressFieldsContainer.classList.remove("disabled-fields");
        }

        // Toggle each field's state
        addressFields.forEach((field) => {
            // Skip the hidden country ID field
            if (field.id === "hidden_country_id") return;

            if (field.tagName.toLowerCase() === "select") {
                field.disabled = useDefaultLocation;
                // Make sure select elements stay visible even when disabled
                field.style.display = "block !important";
                field.style.visibility = "visible !important";
                field.style.opacity = useDefaultLocation ? "0.7" : "1";
                field.style.backgroundColor = useDefaultLocation
                    ? "#f5f5f5"
                    : "";
            } else {
                field.readOnly = useDefaultLocation;
            }
        });

        // Handle the country field specifically
        if (countrySelect && hiddenCountryId) {
            console.log("Handling country fields specifically");

            if (useDefaultLocation) {
                // When using default location, use the hidden field for submission
                countrySelect.name = "_country_id"; // Name that won't be submitted
                hiddenCountryId.name = "country_id";
                hiddenCountryId.disabled = false;

                // Make sure the country dropdown stays visible for UX
                countrySelect.style.cssText = `
                    display: block !important;
                    visibility: visible !important;
                    opacity: 0.7 !important;
                    background-color: #f5f5f5 !important;
                `;

                // Manually force selection for the correct country
                if (userData.country_id) {
                    const options = countrySelect.options;
                    for (let i = 0; i < options.length; i++) {
                        if (options[i].value == userData.country_id) {
                            countrySelect.selectedIndex = i;
                            break;
                        }
                    }
                    hiddenCountryId.value = userData.country_id;
                }
            } else {
                // When manually entering, use the visible dropdown for submission
                countrySelect.name = "country_id";
                hiddenCountryId.name = "_country_id";
                hiddenCountryId.disabled = true;

                // Ensure the select is fully visible and enabled
                countrySelect.style.cssText = `
                    display: block !important;
                    visibility: visible !important;
                    opacity: 1 !important;
                    background-color: unset !important;
                `;
            }
        } else {
            console.error("Country select or hidden ID element not found");
            if (!countrySelect) console.error("Country select not found");
            if (!hiddenCountryId) console.error("Hidden country ID not found");
        }

        // Set values from user data if using default address
        if (useDefaultLocation) {
            // Set text field values
            if (document.getElementById("address_line1"))
                document.getElementById("address_line1").value =
                    userData.address_line1;
            if (document.getElementById("address_line2"))
                document.getElementById("address_line2").value =
                    userData.address_line2;
            if (document.getElementById("city"))
                document.getElementById("city").value = userData.city;
            if (document.getElementById("postcode"))
                document.getElementById("postcode").value = userData.postcode;
            if (document.getElementById("phone"))
                document.getElementById("phone").value = userData.phone;
        }
    }

    // Initial setup
    if (useDefaultLocationCheckbox) {
        // Set initial state
        toggleAddressFields();

        // Add event listener for checkbox changes
        useDefaultLocationCheckbox.addEventListener(
            "change",
            toggleAddressFields
        );
    } else {
        console.error("Default location checkbox not found");
    }
});

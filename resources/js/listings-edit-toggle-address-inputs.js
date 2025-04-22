document.addEventListener("DOMContentLoaded", function () {
    const useDefaultLocationCheckbox = document.getElementById(
        "use-default-location"
    );
    const addressFieldsContainer = document.getElementById("address-fields");
    const addressFields = document.querySelectorAll(
        "#address-fields input, #address-fields select"
    );
    const phoneField = document.getElementById("phone");

    // Make sure the checkbox exists before continuing
    if (!useDefaultLocationCheckbox || !addressFieldsContainer) {
        console.error("Required elements not found for address toggle");
        return;
    }

    // Debugging - log what we find
    console.log(
        "Address toggle initialized with checkbox state:",
        useDefaultLocationCheckbox.checked
    );

    // Get the original listing data (to restore when unchecking the box)
    const listingData = {
        address_line1:
            document.getElementById("address_line1")?.dataset.original || "",
        address_line2:
            document.getElementById("address_line2")?.dataset.original || "",
        city: document.getElementById("city")?.dataset.original || "",
        postcode: document.getElementById("postcode")?.dataset.original || "",
        country_id:
            document.getElementById("countrySelect")?.dataset.original || "",
        phone: phoneField?.dataset.original || "",
    };

    // User data for default address
    const userData = {
        address_line1:
            document.getElementById("address_line1")?.dataset.user || "",
        address_line2:
            document.getElementById("address_line2")?.dataset.user || "",
        city: document.getElementById("city")?.dataset.user || "",
        postcode: document.getElementById("postcode")?.dataset.user || "",
        country_id:
            document.getElementById("countrySelect")?.dataset.user || "",
        phone: phoneField?.dataset.user || "",
    };

    console.log("User data:", userData);
    console.log("Listing data:", listingData);

    // Function to toggle address fields
    function toggleAddressFields() {
        const useDefaultLocation = useDefaultLocationCheckbox.checked;
        console.log(
            "Toggle called - Using default address:",
            useDefaultLocation
        );

        // Visual indication for the container
        if (useDefaultLocation) {
            addressFieldsContainer.classList.add("fields-readonly");
        } else {
            addressFieldsContainer.classList.remove("fields-readonly");
        }

        // Toggle editability for address fields
        addressFields.forEach((field) => {
            // Don't disable the fields, just make them read-only
            // This ensures form values are still submitted
            field.readOnly = useDefaultLocation;

            if (field.tagName.toLowerCase() === "select") {
                field.disabled = useDefaultLocation; // Use disabled for selects
                field.style.pointerEvents = useDefaultLocation
                    ? "none"
                    : "auto";
            }

            field.style.backgroundColor = useDefaultLocation ? "#f5f5f5" : "";
            field.style.color = useDefaultLocation ? "#666" : "";
        });

        // Set values based on checkbox state
        if (useDefaultLocation) {
            // Use user's default address
            if (document.getElementById("address_line1")) {
                document.getElementById("address_line1").value =
                    userData.address_line1;
            }
            if (document.getElementById("address_line2")) {
                document.getElementById("address_line2").value =
                    userData.address_line2;
            }
            if (document.getElementById("city")) {
                document.getElementById("city").value = userData.city;
            }
            if (document.getElementById("postcode")) {
                document.getElementById("postcode").value = userData.postcode;
            }
            if (phoneField) {
                phoneField.value = userData.phone;
            }

            // Set country dropdown
            const countrySelect = document.getElementById("countrySelect");
            if (countrySelect && userData.country_id) {
                for (let i = 0; i < countrySelect.options.length; i++) {
                    if (
                        countrySelect.options[i].value === userData.country_id
                    ) {
                        countrySelect.selectedIndex = i;
                        break;
                    }
                }
            }
        } else {
            // Restore the listing's original address values
            if (document.getElementById("address_line1")) {
                document.getElementById("address_line1").value =
                    listingData.address_line1;
            }
            if (document.getElementById("address_line2")) {
                document.getElementById("address_line2").value =
                    listingData.address_line2;
            }
            if (document.getElementById("city")) {
                document.getElementById("city").value = listingData.city;
            }
            if (document.getElementById("postcode")) {
                document.getElementById("postcode").value =
                    listingData.postcode;
            }
            if (phoneField) {
                phoneField.value = listingData.phone;
            }

            // Restore country dropdown
            const countrySelect = document.getElementById("countrySelect");
            if (countrySelect && listingData.country_id) {
                for (let i = 0; i < countrySelect.options.length; i++) {
                    if (
                        countrySelect.options[i].value ===
                        listingData.country_id
                    ) {
                        countrySelect.selectedIndex = i;
                        break;
                    }
                }
            }
        }
    }

    // Add CSS for visual indication
    const style = document.createElement("style");
    style.textContent = `
        .fields-readonly {
            opacity: 0.9;
            position: relative;
        }
        .fields-readonly::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(245, 245, 245, 0.1);
            pointer-events: none;
        }
    `;
    document.head.appendChild(style);

    // Initial setup
    toggleAddressFields();

    // Add event listener for checkbox changes
    useDefaultLocationCheckbox.addEventListener("change", toggleAddressFields);

    // Log when the change event fires
    useDefaultLocationCheckbox.addEventListener("change", function () {
        console.log("Checkbox changed to:", this.checked);
    });
});

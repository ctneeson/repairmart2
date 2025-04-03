document.addEventListener("DOMContentLoaded", function () {
    const useDefaultLocationCheckbox = document.getElementById(
        "use-default-location"
    );
    const addressFields = document.querySelectorAll(
        "#address-fields input, #address-fields select"
    );
    const addressFieldsContainer = document.getElementById("address-fields");

    // User data for default address (populated from blade)
    const userData = {
        address_line1: "{{ auth()->user()->address_line1 ?? '' }}",
        address_line2: "{{ auth()->user()->address_line2 ?? '' }}",
        city: "{{ auth()->user()->city ?? '' }}",
        postcode: "{{ auth()->user()->postcode ?? '' }}",
        country_id: "{{ auth()->user()->country_id ?? '' }}",
    };

    // Function to toggle address fields
    function toggleAddressFields() {
        const useDefaultLocation = useDefaultLocationCheckbox.checked;

        // Toggle editability
        addressFields.forEach((field) => {
            field.readOnly = useDefaultLocation;
            field.disabled = useDefaultLocation;

            if (field.tagName.toLowerCase() === "select") {
                field.style.pointerEvents = useDefaultLocation
                    ? "none"
                    : "auto";
                field.style.backgroundColor = useDefaultLocation
                    ? "#f5f5f5"
                    : "";
            }
        });

        // Apply visual styling to indicate disabled fields
        if (useDefaultLocation) {
            addressFieldsContainer.classList.add("disabled-fields");
        } else {
            addressFieldsContainer.classList.remove("disabled-fields");
        }

        // Set values from user data if using default address
        if (useDefaultLocation) {
            document.getElementById("address_line1").value =
                userData.address_line1;
            document.getElementById("address_line2").value =
                userData.address_line2;
            document.getElementById("city").value = userData.city;
            document.getElementById("postcode").value = userData.postcode;

            const countrySelect = document.getElementById("countrySelect");
            if (countrySelect) {
                // For a standard select element
                for (let i = 0; i < countrySelect.options.length; i++) {
                    if (countrySelect.options[i].value == userData.country_id) {
                        countrySelect.selectedIndex = i;
                        break;
                    }
                }
            }
        } else {
            // Clear values if not using default address
            document.getElementById("address_line1").value = "";
            document.getElementById("address_line2").value = "";
            document.getElementById("city").value = "";
            document.getElementById("postcode").value = "";

            const countrySelect = document.getElementById("countrySelect");
            if (countrySelect) {
                countrySelect.selectedIndex = 0; // Select the first option
            }
        }
    }

    // Initial setup
    if (useDefaultLocationCheckbox) {
        toggleAddressFields();

        // Add event listener for checkbox changes
        useDefaultLocationCheckbox.addEventListener(
            "change",
            toggleAddressFields
        );
    }
});

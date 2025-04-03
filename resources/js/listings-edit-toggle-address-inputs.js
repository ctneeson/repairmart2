document.addEventListener("DOMContentLoaded", function () {
    const useDefaultLocationCheckbox = document.getElementById(
        "use-default-location"
    );
    const addressFields = document.querySelectorAll(
        "#address-fields input, #address-fields select"
    );
    const phoneField = document.getElementById("phone");

    // Get the original listing data (to restore when unchecking the box)
    const listingData = {
        address_line1: document
            .getElementById("address_line1")
            .getAttribute("data-original"),
        address_line2: document
            .getElementById("address_line2")
            .getAttribute("data-original"),
        city: document.getElementById("city").getAttribute("data-original"),
        postcode: document
            .getElementById("postcode")
            .getAttribute("data-original"),
        country_id: document
            .getElementById("countrySelect")
            .getAttribute("data-original"),
        phone: phoneField ? phoneField.getAttribute("data-original") : "",
    };

    // User data for default address
    const userData = {
        address_line1: document
            .getElementById("address_line1")
            .getAttribute("data-user"),
        address_line2: document
            .getElementById("address_line2")
            .getAttribute("data-user"),
        city: document.getElementById("city").getAttribute("data-user"),
        postcode: document.getElementById("postcode").getAttribute("data-user"),
        country_id: document
            .getElementById("countrySelect")
            .getAttribute("data-user"),
        phone: phoneField ? phoneField.getAttribute("data-user") : "",
    };

    // Function to toggle address fields
    function toggleAddressFields() {
        const useDefaultLocation = useDefaultLocationCheckbox.checked;

        // Wrap all address fields in a container to style them as a group
        const addressFieldsContainer =
            document.getElementById("address-fields");
        if (addressFieldsContainer) {
            if (useDefaultLocation) {
                addressFieldsContainer.classList.add("fields-readonly");
            } else {
                addressFieldsContainer.classList.remove("fields-readonly");
            }
        }

        // Toggle editability for address fields
        addressFields.forEach((field) => {
            field.readOnly = useDefaultLocation;

            if (field.tagName.toLowerCase() === "select") {
                field.style.pointerEvents = useDefaultLocation
                    ? "none"
                    : "auto";
            }

            field.style.backgroundColor = useDefaultLocation ? "#f5f5f5" : "";
            field.style.color = useDefaultLocation ? "#666" : "";
        });

        // Also toggle editability for the phone field
        if (phoneField) {
            phoneField.readOnly = useDefaultLocation;
            phoneField.style.backgroundColor = useDefaultLocation
                ? "#f5f5f5"
                : "";
            phoneField.style.color = useDefaultLocation ? "#666" : "";
        }

        // Set values based on checkbox state
        if (useDefaultLocation) {
            // Use user's default address
            document.getElementById("address_line1").value =
                userData.address_line1;
            document.getElementById("address_line2").value =
                userData.address_line2;
            document.getElementById("city").value = userData.city;
            document.getElementById("postcode").value = userData.postcode;

            // Set phone value
            if (phoneField) {
                phoneField.value = userData.phone;
            }

            // Set country dropdown
            const countrySelect = document.getElementById("countrySelect");
            if (countrySelect) {
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
            document.getElementById("address_line1").value =
                listingData.address_line1;
            document.getElementById("address_line2").value =
                listingData.address_line2;
            document.getElementById("city").value = listingData.city;
            document.getElementById("postcode").value = listingData.postcode;

            // Restore phone
            if (phoneField) {
                phoneField.value = listingData.phone;
            }

            // Restore country dropdown
            const countrySelect = document.getElementById("countrySelect");
            if (countrySelect) {
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

export function initAddressHandling() {
    const useDefaultLocationCheckbox = document.getElementById(
        "use-default-location"
    );
    const addressFields = document.getElementById("address-fields");

    // Exit if required elements don't exist
    if (!useDefaultLocationCheckbox || !addressFields) return;

    // Get all form elements in the address fields section
    const addressInputs = addressFields.querySelectorAll("input");
    const addressSelects = addressFields.querySelectorAll("select");
    const hiddenCountryId = document.getElementById("hidden_country_id");

    // IMPORTANT: Use 'country_id' as the ID, not 'visible_country_id'
    const visibleCountryId = document.getElementById("country_id");

    // Get the user data from the hidden element
    let userData = null;
    const userDataElement = document.getElementById("user-data");
    if (userDataElement) {
        try {
            userData = JSON.parse(userDataElement.dataset.user);
        } catch (e) {
            console.error("Error parsing user data:", e);
        }
    }

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

        // Reset fields to user defaults if checkbox is checked
        if (useDefault && userData) {
            // Reset text fields
            if (document.getElementById("address_line1"))
                document.getElementById("address_line1").value =
                    userData.address_line1 || "";
            if (document.getElementById("address_line2"))
                document.getElementById("address_line2").value =
                    userData.address_line2 || "";
            if (document.getElementById("city"))
                document.getElementById("city").value = userData.city || "";
            if (document.getElementById("postcode"))
                document.getElementById("postcode").value =
                    userData.postcode || "";
            if (document.getElementById("phone"))
                document.getElementById("phone").value = userData.phone || "";
        }

        // Handle the country select specifically
        if (visibleCountryId && hiddenCountryId) {
            if (useDefault) {
                // Set the country select to be readonly-like
                visibleCountryId.disabled = true;
                visibleCountryId.name = "_country_id"; // Name that won't be submitted
                hiddenCountryId.disabled = false;
                hiddenCountryId.name = "country_id";

                // Reset the country dropdown to user's default
                if (userData && userData.country_id) {
                    // Set hidden field value
                    hiddenCountryId.value = userData.country_id;

                    // Set dropdown visible value (even though it's disabled)
                    const options = visibleCountryId.options;
                    for (let i = 0; i < options.length; i++) {
                        if (options[i].value == userData.country_id) {
                            // This needs to happen synchronously
                            options[i].selected = true;
                            visibleCountryId.selectedIndex = i;
                            break;
                        }
                    }
                }
            } else {
                // Enable the visible country dropdown
                visibleCountryId.disabled = false;
                visibleCountryId.name = "country_id";
                hiddenCountryId.disabled = true;
                hiddenCountryId.name = "_country_id";
            }
        }

        // Update each input's readonly status
        addressInputs.forEach((input) => {
            if (input.id !== "hidden_country_id") {
                input.readOnly = useDefault;
            }
        });

        // Update other select elements if any
        addressSelects.forEach((select) => {
            if (
                select.id !== "country_id" &&
                select.id !== "hidden_country_id"
            ) {
                select.disabled = useDefault;
            }
        });
    }

    // Run the function once on page load to set initial state
    updateAddressFieldsState();
}

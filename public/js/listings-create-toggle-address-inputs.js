document.addEventListener("DOMContentLoaded", function () {
    const useDefaultAddressCheckbox = document.getElementById(
        "use-default-address"
    );
    const addressInputs = [
        document.getElementById("address_line1"),
        document.getElementById("address_line2"),
        document.getElementById("city"),
        document.getElementById("postcode"),
        document.getElementById("country"),
        document.getElementById("phone"),
    ];

    function toggleAddressInputs() {
        const isDisabled = useDefaultAddressCheckbox.checked;
        addressInputs.forEach((input) => {
            input.disabled = isDisabled;
            input.style.backgroundColor = isDisabled ? "#e9ecef" : "";
        });
    }

    useDefaultAddressCheckbox.addEventListener("change", toggleAddressInputs);

    // Initialize the state on page load
    toggleAddressInputs();
});

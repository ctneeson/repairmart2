document.addEventListener("DOMContentLoaded", function () {
    function updateDropdownButtonText(dropdownToggle, checkboxes, label) {
        const selected = Array.from(checkboxes).filter(
            (checkbox) => checkbox.checked
        );
        if (selected.length > 0) {
            const selectedText = selected
                .map((checkbox) => checkbox.nextElementSibling.textContent)
                .join(", ");
            dropdownToggle.textContent = selectedText;
        } else {
            dropdownToggle.textContent = "Select...";
        }
        // Update the label with the count of selections
        label.textContent = `${label.dataset.label} (${selected.length})`;
    }

    function setupDropdown(dropdownToggleClass, dropdownMenuClass) {
        const dropdownToggle = document.querySelector(
            `.${dropdownToggleClass}`
        );
        const dropdownMenu = document.querySelector(`.${dropdownMenuClass}`);
        const relatedCheckboxes = dropdownMenu.querySelectorAll(
            'input[type="checkbox"]'
        );
        const resetButton = dropdownToggle.nextElementSibling;
        const label = dropdownToggle
            .closest(".dropdown")
            .querySelector("label");

        dropdownToggle.addEventListener("click", function (e) {
            e.stopPropagation();
            document.querySelectorAll(".dropdown-menu").forEach((menu) => {
                if (menu !== dropdownMenu) {
                    menu.classList.remove("show");
                }
            });
            dropdownMenu.classList.toggle("show");
        });

        relatedCheckboxes.forEach((checkbox) => {
            checkbox.addEventListener("change", function (e) {
                e.stopPropagation(); // Prevent the event from bubbling up
                updateDropdownButtonText(
                    dropdownToggle,
                    relatedCheckboxes,
                    label
                );
            });
        });

        resetButton.addEventListener("click", function (e) {
            e.stopPropagation(); // Prevent the event from bubbling up
            relatedCheckboxes.forEach((checkbox) => {
                checkbox.checked = false;
            });
            updateDropdownButtonText(dropdownToggle, relatedCheckboxes, label);
        });

        // Initial update of the button text and label
        updateDropdownButtonText(dropdownToggle, relatedCheckboxes, label);
    }

    document.querySelectorAll(".dropdown-toggle").forEach((toggle) => {
        const toggleClass = toggle.classList[1];
        const menuClass = toggleClass.replace("toggle", "menu");
        setupDropdown(toggleClass, menuClass);
    });

    // Close the dropdown if the user clicks outside of it
    window.addEventListener("click", function () {
        document.querySelectorAll(".dropdown-menu").forEach((menu) => {
            menu.classList.remove("show");
        });
    });

    // Prevent the dropdown from closing when clicking inside the dropdown menu
    document.querySelectorAll(".dropdown-menu").forEach((menu) => {
        menu.addEventListener("click", function (e) {
            e.stopPropagation();
        });
    });
});

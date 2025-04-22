document.addEventListener("DOMContentLoaded", function () {
    const productSelect = document.getElementById("productSelect");
    const addProductLink = document.getElementById("add-product-link");
    const selectedProductsContainer =
        document.getElementById("selected-products");
    const productHiddenInputs = document.getElementById(
        "product-hidden-inputs"
    );
    const resetButton = document.querySelector(".btn-default:nth-of-type(2)");
    const form = document.querySelector("form");

    // Initialize selectedProducts with values from the initialSelectedProducts array if available
    // or from hidden inputs as a fallback
    let selectedProducts = [];

    // If initialSelectedProducts is defined (in edit mode), use it to initialize
    if (
        typeof initialSelectedProducts !== "undefined" &&
        initialSelectedProducts.length > 0
    ) {
        selectedProducts = initialSelectedProducts.map((product) => product.id);
        console.log(
            "Loaded products from initialSelectedProducts:",
            initialSelectedProducts
        );
    } else {
        // Otherwise use the hidden inputs (for create mode or when returning after validation errors)
        selectedProducts = Array.from(
            new Set(
                Array.from(
                    document.querySelectorAll(
                        '#product-hidden-inputs input[name="product_ids[]"]'
                    )
                ).map((input) => input.value)
            )
        ).filter(Boolean); // Remove any empty strings
        console.log("Loaded products from hidden inputs:", selectedProducts);
    }

    console.log("Initial selected products:", selectedProducts);

    // Function to reset product selections
    window.resetProductSelections = function () {
        // Clear the selectedProducts array
        selectedProducts = [];

        // Update UI
        updateSelectedProductsDisplay();
        updateHiddenInputs();
        updateAddProductLinkVisibility();

        // Reset dropdown product selections if they exist
        if (productSelect) {
            productSelect.selectedIndex = 0;
        }

        // Reset product checkboxes from select-product component
        document
            .querySelectorAll('.product-menu input[type="checkbox"]')
            .forEach((checkbox) => {
                checkbox.checked = false;
            });

        // Update dropdown button text
        const productToggle = document.querySelector(".product-toggle");
        if (productToggle) {
            productToggle.textContent = "Select Products";
        }

        console.log("Products reset. Selected products:", selectedProducts);
    };

    function updateAddProductLinkVisibility() {
        const selectedValue = productSelect ? productSelect.value : "";
        if (
            productSelect &&
            selectedValue !== "" &&
            !selectedProducts.includes(selectedValue) &&
            selectedProducts.length < 3
        ) {
            addProductLink.style.display = "inline";
        } else if (addProductLink) {
            addProductLink.style.display = "none";
        }
    }

    function updateSelectedProductsDisplay() {
        if (!selectedProductsContainer) return;

        selectedProductsContainer.innerHTML = "";
        selectedProducts.forEach((productId) => {
            // Get product details either from initialSelectedProducts or from select options
            let productText = "";

            // First try to find in initialSelectedProducts (for edit mode)
            if (typeof initialSelectedProducts !== "undefined") {
                const initialProduct = initialSelectedProducts.find(
                    (p) => p.id === productId
                );
                if (initialProduct) {
                    productText = `${initialProduct.category} > ${initialProduct.subcategory}`;
                }
            }

            // If not found in initialSelectedProducts, try to get from select options
            if (!productText && productSelect) {
                const productOption = productSelect.querySelector(
                    `option[value="${productId}"]`
                );
                if (productOption) {
                    productText = `${productOption.dataset.category} > ${productOption.dataset.subcategory}`;
                } else {
                    // As a fallback, just show the ID
                    productText = `Product #${productId}`;
                }
            }

            const productElement = document.createElement("div");
            productElement.classList.add("listing-item-badge");
            productElement.style.position = "relative";
            productElement.textContent = productText;

            const removeLink = document.createElement("a");
            removeLink.href = "#";
            removeLink.textContent = " Remove";
            removeLink.style.position = "absolute";
            removeLink.style.right = "10px";
            removeLink.style.top = "50%";
            removeLink.style.transform = "translateY(-50%)";
            removeLink.addEventListener("click", function (e) {
                e.preventDefault();
                selectedProducts = selectedProducts.filter(
                    (p) => p !== productId
                );
                console.log("Product removed:", productId);
                console.log(
                    "Selected products after removal:",
                    selectedProducts
                );
                updateSelectedProductsDisplay();
                updateAddProductLinkVisibility();
                updateHiddenInputs();
            });
            productElement.appendChild(removeLink);
            selectedProductsContainer.appendChild(productElement);
        });
        console.log("Selected products displayed:", selectedProducts);
    }

    function updateHiddenInputs() {
        if (!productHiddenInputs) return;

        productHiddenInputs.innerHTML = "";
        selectedProducts.forEach((productId) => {
            const hiddenInput = document.createElement("input");
            hiddenInput.type = "hidden";
            hiddenInput.name = "product_ids[]";
            hiddenInput.value = productId;
            productHiddenInputs.appendChild(hiddenInput);
        });
    }

    // Handle select-product-all component checkbox changes
    document
        .querySelectorAll('.product-menu input[type="checkbox"]')
        .forEach((checkbox) => {
            checkbox.addEventListener("change", function () {
                const productId = this.value;

                if (this.checked) {
                    // Add product if not already in the list and under limit
                    if (
                        !selectedProducts.includes(productId) &&
                        selectedProducts.length < 3
                    ) {
                        selectedProducts.push(productId);
                        updateSelectedProductsDisplay();
                        updateHiddenInputs();
                    } else if (selectedProducts.length >= 3) {
                        // Uncheck if we're at the limit
                        this.checked = false;
                        alert("You can only select up to 3 products.");
                    }
                } else {
                    // Remove product
                    selectedProducts = selectedProducts.filter(
                        (id) => id !== productId
                    );
                    updateSelectedProductsDisplay();
                    updateHiddenInputs();
                }

                // Update toggle button text
                const checkedCount = document.querySelectorAll(
                    '.product-menu input[type="checkbox"]:checked'
                ).length;
                const productToggle = document.querySelector(".product-toggle");
                if (productToggle) {
                    productToggle.textContent =
                        checkedCount > 0
                            ? `${checkedCount} product${
                                  checkedCount > 1 ? "s" : ""
                              } selected`
                            : "Select Products";
                }
            });
        });

    // Add event listeners only if elements exist
    if (productSelect) {
        productSelect.addEventListener("change", function () {
            updateAddProductLinkVisibility();
        });
    }

    if (addProductLink) {
        addProductLink.addEventListener("click", function (e) {
            e.preventDefault();
            const selectedValue = productSelect.value;
            if (
                selectedValue !== "" &&
                !selectedProducts.includes(selectedValue) &&
                selectedProducts.length < 3
            ) {
                selectedProducts.push(selectedValue);
                console.log("Product added:", selectedValue);
                console.log(
                    "Selected products after addition:",
                    selectedProducts
                );
                updateSelectedProductsDisplay();
                updateAddProductLinkVisibility();
                updateHiddenInputs();
            }
        });
    }

    // Reset button functionality
    if (resetButton) {
        resetButton.addEventListener("click", function (e) {
            e.preventDefault();
            window.resetProductSelections();
        });
    }

    // For the product reset button in select-product component
    document.querySelectorAll(".reset-button").forEach((button) => {
        button.addEventListener("click", function (e) {
            e.preventDefault();

            // If this is the product reset button (check parent to confirm)
            if (
                this.closest(".dropdown") &&
                this.closest(".dropdown").querySelector(".product-toggle")
            ) {
                window.resetProductSelections();
            }
        });
    });

    // Initialize the UI
    updateSelectedProductsDisplay();
    updateAddProductLinkVisibility();
    updateHiddenInputs();

    // Add form submit event listener
    if (form) {
        form.addEventListener("submit", function () {
            // Clean up all product_ids[] inputs before form submission
            // This step is important to avoid duplicate entries
            const allProductInputs = form.querySelectorAll(
                'input[name="product_ids[]"]'
            );
            allProductInputs.forEach((input) => {
                if (
                    productHiddenInputs &&
                    !productHiddenInputs.contains(input)
                ) {
                    input.remove();
                }
            });

            // Make sure our hidden inputs are up to date
            updateHiddenInputs();

            console.log("Form submission with product IDs:", selectedProducts);
        });
    }
});

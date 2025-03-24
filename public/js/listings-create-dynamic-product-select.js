document.addEventListener("DOMContentLoaded", function () {
    const productSelect = document.getElementById("productSelect");
    const addProductLink = document.getElementById("add-product-link");
    const selectedProductsContainer =
        document.getElementById("selected-products");
    const productHiddenInputs = document.getElementById(
        "product-hidden-inputs"
    );
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

document.addEventListener("DOMContentLoaded", function () {
    const productSelect = document.getElementById("productSelect");
    const addProductLink = document.getElementById("add-product-link");
    const selectedProductsContainer =
        document.getElementById("selected-products");
    const productHiddenInputs = document.getElementById(
        "product-hidden-inputs"
    );
    const form = document.querySelector("form");

    // Initialize selectedProducts with unique values from hidden inputs
    let selectedProducts = Array.from(
        new Set(
            Array.from(
                document.querySelectorAll(
                    '#product-hidden-inputs input[name="product_ids[]"]'
                )
            ).map((input) => input.value)
        )
    ).filter(Boolean); // Remove any empty values

    console.log("Initial selected products:", selectedProducts);

    function updateAddProductLinkVisibility() {
        const selectedValue = productSelect.value;
        if (
            selectedValue !== "" &&
            !selectedProducts.includes(selectedValue) &&
            selectedProducts.length < 3
        ) {
            addProductLink.style.display = "inline";
        } else {
            addProductLink.style.display = "none";
        }
    }

    function updateSelectedProductsDisplay() {
        selectedProductsContainer.innerHTML = "";
        selectedProducts.forEach((productId) => {
            const productOption = productSelect.querySelector(
                `option[value="${productId}"]`
            );
            const productText = `${productOption.dataset.category} > ${productOption.dataset.subcategory}`;
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
        productHiddenInputs.innerHTML = "";
        selectedProducts.forEach((productId) => {
            const hiddenInput = document.createElement("input");
            hiddenInput.type = "hidden";
            hiddenInput.name = "product_ids[]";
            hiddenInput.value = productId;
            productHiddenInputs.appendChild(hiddenInput);
        });
    }

    productSelect.addEventListener("change", function () {
        updateAddProductLinkVisibility();
    });

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
            console.log("Selected products after addition:", selectedProducts);
            updateSelectedProductsDisplay();
            updateAddProductLinkVisibility();
            updateHiddenInputs();
        }
    });

    // Clear any existing hidden inputs before initializing
    productHiddenInputs.innerHTML = "";
    updateSelectedProductsDisplay();
    updateAddProductLinkVisibility();
    updateHiddenInputs();

    // Add form submit event listener
    form.addEventListener("submit", function (e) {
        // No need to preventDefault() as we want the form to submit

        // Clean up all product_ids[] inputs before form submission
        // This step is important to avoid duplicate entries
        const allProductInputs = form.querySelectorAll(
            'input[name="product_ids[]"]'
        );
        allProductInputs.forEach((input) => {
            if (!productHiddenInputs.contains(input)) {
                input.remove();
            }
        });

        // Make sure our hidden inputs are up to date
        updateHiddenInputs();

        console.log("Form submission with product IDs:", selectedProducts);
    });
});

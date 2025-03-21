document.addEventListener("DOMContentLoaded", function () {
    const productSelect = document.querySelector(
        ".product-select-wrapper select"
    );
    const addProductLink = document.getElementById("add-product-link");
    const selectedProductsContainer =
        document.getElementById("selected-products");
    let selectedProducts = [];

    function updateAddProductLinkVisibility() {
        const selectedValue = productSelect.value;
        console.log("Selected value:", selectedValue); // Debugging line
        console.log("Selected products:", selectedProducts); // Debugging line
        if (
            selectedValue !== "" &&
            !selectedProducts.includes(selectedValue) &&
            selectedProducts.length < 3
        ) {
            addProductLink.style.display = "inline";
        } else {
            addProductLink.style.display = "none";
        }
        console.log("Add product link display:", addProductLink.style.display); // Debugging line
    }

    function updateSelectedProductsDisplay() {
        selectedProductsContainer.innerHTML = "";
        selectedProducts.forEach((productId) => {
            const productOption = productSelect.querySelector(
                `option[value="${productId}"]`
            );
            const productText = `${productOption.dataset.category} > ${productOption.dataset.subcategory}`;
            const productElement = document.createElement("div");
            productElement.classList.add("listing-item-badge"); // Apply the class
            productElement.style.position = "relative"; // Ensure relative positioning
            productElement.textContent = productText;
            const removeLink = document.createElement("a");
            removeLink.href = "#";
            removeLink.textContent = " -";
            removeLink.style.position = "absolute"; // Position absolute
            removeLink.style.right = "10px"; // Align to the right with padding
            removeLink.style.top = "50%"; // Center vertically
            removeLink.style.transform = "translateY(-50%)"; // Adjust vertical alignment
            removeLink.addEventListener("click", function (e) {
                e.preventDefault();
                selectedProducts = selectedProducts.filter(
                    (p) => p !== productId
                );
                updateSelectedProductsDisplay();
                updateAddProductLinkVisibility();
            });
            productElement.appendChild(removeLink);
            selectedProductsContainer.appendChild(productElement);
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
            updateSelectedProductsDisplay();
            updateAddProductLinkVisibility();
        }
    });

    updateAddProductLinkVisibility();
});

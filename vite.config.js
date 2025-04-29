import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/js/dashboard-statistics.js",
                "resources/js/listings-attachments.js",
                "resources/js/listings-create-dynamic-product-select.js",
                "resources/js/listings-create-image-preview.js",
                "resources/js/listings-create-reset-form.js",
                "resources/js/listings-create-toggle-address-inputs.js",
                "resources/js/listings-edit-toggle-address-inputs.js",
                "resources/js/listings-edit.js",
                "resources/js/listings-search-dropdown.js",
                "resources/js/quote-address-handling.js",
                "resources/js/quote-create.js",
                "resources/js/quote-edit.js",
            ],
            refresh: true,
        }),
    ],
});

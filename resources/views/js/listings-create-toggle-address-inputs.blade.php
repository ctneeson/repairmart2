<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Script loaded');
        const useDefaultLocationCheckbox = document.getElementById('use-default-location');
        console.log('Checkbox found:', useDefaultLocationCheckbox);
        const addressFields = document.querySelectorAll('#address-fields input, #address-fields select');
        const phoneField = document.getElementById('phone');
        const countrySelect = document.getElementById('countrySelect');
        const hiddenCountryId = document.getElementById('hidden_country_id');
        
        // User data for default address and phone
        const userData = {
            address_line1: {!! json_encode(auth()->user()->address_line1 ?? '') !!},
            address_line2: {!! json_encode(auth()->user()->address_line2 ?? '') !!},
            city: {!! json_encode(auth()->user()->city ?? '') !!},
            postcode: {!! json_encode(auth()->user()->postcode ?? '') !!},
            country_id: {!! json_encode(auth()->user()->country_id ?? '') !!},
            phone: {!! json_encode(auth()->user()->phone ?? '') !!}
        };
        console.log('User data:', userData);
        
        // Store the original values when the page loads
        const originalValues = {};
        addressFields.forEach(field => {
            if (field.id) {
                originalValues[field.id] = field.value;
            }
        });
        if (phoneField) {
            originalValues['phone'] = phoneField.value;
        }
        console.log('Original values:', originalValues);
        
        // Function to toggle address fields
        function toggleAddressFields() {
            const useDefaultLocation = useDefaultLocationCheckbox.checked;
            
            // Toggle editability for address fields
            addressFields.forEach(field => {
                // Skip the hidden country field
                if (field.id === 'hidden_country_id') return;
                
                if (field.tagName.toLowerCase() === 'select') {
                    // For select elements, we need to use disabled instead of readOnly
                    field.disabled = useDefaultLocation;
                    
                    // Force visibility for select elements even when disabled
                    field.style.cssText = `
                        display: inline-block !important;
                        visibility: visible !important;
                        opacity: ${useDefaultLocation ? '0.7' : '1'} !important;
                        pointer-events: ${useDefaultLocation ? 'none' : 'auto'};
                        background-color: ${useDefaultLocation ? '#f5f5f5' : ''};
                        color: ${useDefaultLocation ? '#666' : ''};
                    `;
                } else {
                    // For regular input fields, use readOnly
                    field.readOnly = useDefaultLocation;
                    field.style.backgroundColor = useDefaultLocation ? '#f5f5f5' : '';
                    field.style.color = useDefaultLocation ? '#666' : '';
                }
            });
            
            // Handle the country select specifically for form submission
            if (countrySelect && hiddenCountryId) {
                if (useDefaultLocation) {
                    // Use the hidden field for submission when using default address
                    countrySelect.name = '_country_id'; // This won't be submitted
                    hiddenCountryId.name = 'country_id';
                    hiddenCountryId.disabled = false;
                } else {
                    // Use the visible dropdown for submission when manually entering
                    countrySelect.name = 'country_id';
                    hiddenCountryId.name = '_country_id';
                    hiddenCountryId.disabled = true;
                }
            }
            
            // Also toggle editability for the phone field
            if (phoneField) {
                phoneField.readOnly = useDefaultLocation;
                phoneField.style.backgroundColor = useDefaultLocation ? '#f5f5f5' : '';
                phoneField.style.color = useDefaultLocation ? '#666' : '';
            }
            
            // Set values based on the checkbox state
            if (useDefaultLocation) {
                // ONLY use the safe field setting method
                safeSetFieldValue('address_line1', userData.address_line1);
                safeSetFieldValue('address_line2', userData.address_line2);
                safeSetFieldValue('city', userData.city);
                safeSetFieldValue('postcode', userData.postcode);
                
                if (phoneField) {
                    phoneField.value = userData.phone;
                }
                
                if (countrySelect) {
                    // For a standard select element
                    for (let i = 0; i < countrySelect.options.length; i++) {
                        if (countrySelect.options[i].value == userData.country_id) {
                            countrySelect.selectedIndex = i;
                            break;
                        }
                    }
                    
                    // Also set the hidden field value
                    if (hiddenCountryId) {
                        hiddenCountryId.value = userData.country_id;
                    }
                }
            } else {
                // Restore original values or clear if toggling for the first time
                addressFields.forEach(field => {
                    if (field.id && field.id !== 'hidden_country_id') {
                        field.value = originalValues[field.id] || '';
                    }
                });
                
                if (phoneField) {
                    phoneField.value = originalValues['phone'] || '';
                }
            }
        }
        
        // Helper function to safely set field values
        function safeSetFieldValue(id, value) {
            const element = document.getElementById(id);
            if (element) {
                element.value = value;
            } else {
                console.warn(`Element with id ${id} not found`);
            }
        }
        
        // Give the page a moment to fully initialize before applying our logic
        setTimeout(function() {
            // Initial setup
            if (useDefaultLocationCheckbox) {
                toggleAddressFields();
                
                // Add event listener for checkbox changes
                useDefaultLocationCheckbox.addEventListener('change', toggleAddressFields);
            }
        }, 50); // Short delay to ensure DOM is fully processed
    });
</script>
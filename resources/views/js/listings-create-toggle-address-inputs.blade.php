<script>
    document.addEventListener('DOMContentLoaded', function() {
        const useDefaultLocationCheckbox = document.getElementById('use-default-location');
        const addressFields = document.querySelectorAll('#address-fields input, #address-fields select');
        const phoneField = document.getElementById('phone'); // Add the phone field
        const countrySelect = document.getElementById('countrySelect');
        const hiddenCountryId = document.getElementById('hidden_country_id');
        
        // User data for default address and phone
        const userData = {
            address_line1: "{{ auth()->user()->address_line1 ?? '' }}",
            address_line2: "{{ auth()->user()->address_line2 ?? '' }}",
            city: "{{ auth()->user()->city ?? '' }}",
            postcode: "{{ auth()->user()->postcode ?? '' }}",
            country_id: "{{ auth()->user()->country_id ?? '' }}",
            phone: "{{ auth()->user()->phone ?? '' }}" // Add phone data
        };
        
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
            
            // Set values from user data if using default address
            if (useDefaultLocation) {
                document.getElementById('address_line1').value = userData.address_line1;
                document.getElementById('address_line2').value = userData.address_line2;
                document.getElementById('city').value = userData.city;
                document.getElementById('postcode').value = userData.postcode;
                
                // Set phone value
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
                // Clear values if not using default address
                document.getElementById('address_line1').value = '';
                document.getElementById('address_line2').value = '';
                document.getElementById('city').value = '';
                document.getElementById('postcode').value = '';
                
                // Clear phone value
                if (phoneField) {
                    phoneField.value = '';
                }
                
                if (countrySelect) {
                    countrySelect.selectedIndex = 0; // Select the first option
                }
            }
        }
        
        // Add an immediate fix for the country dropdown visibility
        if (countrySelect) {
            // Ensure the country dropdown is visible even when disabled
            countrySelect.style.cssText = `
                display: inline-block !important;
                visibility: visible !important;
                opacity: ${useDefaultLocationCheckbox?.checked ? '0.7' : '1'} !important;
            `;
            
            // Debug
            console.log('Applied visibility fix to country dropdown');
        }
        
        // Initial setup
        if (useDefaultLocationCheckbox) {
            toggleAddressFields();
            
            // Add event listener for checkbox changes
            useDefaultLocationCheckbox.addEventListener('change', toggleAddressFields);
        }
    });
</script>
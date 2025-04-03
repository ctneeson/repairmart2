<script>
    document.addEventListener('DOMContentLoaded', function() {
        const useDefaultLocationCheckbox = document.getElementById('use-default-location');
        const addressFields = document.querySelectorAll('#address-fields input, #address-fields select');
        const phoneField = document.getElementById('phone'); // Add the phone field
        
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
                field.readOnly = useDefaultLocation;
                
                if (field.tagName.toLowerCase() === 'select') {
                    field.style.pointerEvents = useDefaultLocation ? 'none' : 'auto';
                }
                
                field.style.backgroundColor = useDefaultLocation ? '#f5f5f5' : '';
                field.style.color = useDefaultLocation ? '#666' : '';
            });
            
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
                
                const countrySelect = document.getElementById('countrySelect');
                if (countrySelect) {
                    // For a standard select element
                    for (let i = 0; i < countrySelect.options.length; i++) {
                        if (countrySelect.options[i].value == userData.country_id) {
                            countrySelect.selectedIndex = i;
                            break;
                        }
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
                
                const countrySelect = document.getElementById('countrySelect');
                if (countrySelect) {
                    countrySelect.selectedIndex = 0; // Select the first option
                }
            }
        }
        
        // Initial setup
        if (useDefaultLocationCheckbox) {
            toggleAddressFields();
            
            // Add event listener for checkbox changes
            useDefaultLocationCheckbox.addEventListener('change', toggleAddressFields);
        }
    });
</script>
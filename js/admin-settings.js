document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('finance-tracker-stock-api-form');
    
    form.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(form);
        const settings = {};

        // Collect form data
        for (let [key, value] of formData.entries()) {
            // Convert checkbox to boolean
            if (key === 'stock_tracking_enabled') {
                settings[key] = value === 'on' ? 'true' : 'false';
            } else {
                settings[key] = value;
            }
        }

        // Send settings to backend
        fetch(OC.generateUrl('/apps/finance_tracker/admin/stock-settings'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'requesttoken': OC.requestToken
            },
            body: JSON.stringify(settings)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Show success message
            OC.Notification.showTemporary(
                t('finance_tracker', 'Stock API settings saved successfully')
            );
        })
        .catch(error => {
            console.error('Error:', error);
            OC.Notification.showTemporary(
                t('finance_tracker', 'Failed to save stock API settings')
            );
        });
    });
});
